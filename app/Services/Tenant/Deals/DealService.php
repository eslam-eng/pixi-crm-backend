<?php

namespace App\Services\Tenant\Deals;

use App\DTO\Tenant\DealDTO;
use App\Enums\DealType;
use App\Models\Tenant\Deal;
use App\Models\Tenant\DealAttachment;
use App\Models\Tenant\Item;
use App\QueryFilters\Tenant\DealsFilter;
use App\Services\BaseService;
use App\Settings\DealsSettings;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DealService extends BaseService
{
    public function __construct(
        public Deal $model,
        public Item $itemModel,
    ) {}

    public function getModel(): Deal
    {
        return $this->model;
    }

    /**
     * Get paginated deals with filters and relationships
     */
    public function paginate(Request $request, array $relationships = [])
    {
        $defaultRelationships = ['items', 'lead'];
        $relationships = array_merge($defaultRelationships, $relationships);

        // Apply filters using Filterable trait
        $query = $this->model->filter(new DealsFilter($request->all()));

        // Load relationships
        $query->with($relationships);

        // Paginate results
        return $query->paginate(per_page());
    }

    /**
     * Get a single deal with all relationships
     */
    public function show(int $dealId): Deal
    {
        return $this->model
            ->with([
                'lead',
                'assigned_to',
                'items',
                'attachments'
            ])
            ->findOrFail($dealId);
    }

    /**
     * Get simple statistics for deals
     */
    public function statistics(): array
    {
        $totalDeals = (int) $this->model->count();
        $pipelineValue = (float) $this->model->sum('total_amount');
        $wonDeals = (int) $this->model->where('payment_status', 'paid')->count();
        $avgDealSize = $totalDeals > 0 ? round($pipelineValue / $totalDeals, 2) : 0.0;

        return [
            'total_deals' => $totalDeals,
            'pipeline_value' => $pipelineValue,
            'won_deals' => $wonDeals,
            'avg_deal_size' => $avgDealSize,
        ];
    }

    /**
     * Delete a deal and its related resources (media/attachments via cascades)
     */
    public function destroy(int $dealId): void
    {
        $deal = $this->model->findOrFail($dealId);
        // Deleting the model will cascade delete deal_attachments (FK) and
        // Spatie MediaLibrary will cleanup media for the model automatically.
        $deal->delete();
    }

    public function store(DealDTO $dealDTO): Deal
    {
        // Validate deal settings
        $this->validateDealSettings($dealDTO);


        return DB::transaction(function () use ($dealDTO) {
            $items = $dealDTO->items ?? [];

            if (empty($items)) {
                throw ValidationException::withMessages(['items' => ['At least one item is required.']]);
            }

            // Merge duplicate items
            $mergedItems = $this->mergeItems($items);

            // Validate and prepare items
            $itemsData = $this->prepareItems($mergedItems);

            $total = collect($itemsData['pivot'])->sum('total');

            $afterDiscount = $this->afterDiscount($total, $dealDTO->discount_value ?? 0, $dealDTO->discount_type ?? 'fixed');

            $totalAmount = $this->afterTax($afterDiscount, $dealDTO->tax_rate ?? 0);

            // Create deal
            $dealDTO->total_amount = $totalAmount;
            $deal = $this->model->create(Arr::except($dealDTO->toArray(), ['items', 'attachments']));

            // Attach items
            $deal->items()->attach($itemsData['pivot']);

            // Handle attachments if provided
            if ($dealDTO->attachments && count($dealDTO->attachments) > 0) {
                $this->handleAttachments($deal, $dealDTO->attachments);
            }

            return $deal->load('items', 'lead', 'attachments');
        });
    }

    public function update(DealDTO $dealDTO, int $dealId): Deal
    {
        // Validate deal settings
        $this->validateDealSettings($dealDTO);

        return DB::transaction(function () use ($dealDTO, $dealId) {
            $deal = $this->model->findOrFail($dealId);

            $items = $dealDTO->items ?? [];

            if (empty($items)) {
                throw ValidationException::withMessages(['items' => ['At least one item is required.']]);
            }

            // Merge duplicate items
            $mergedItems = $this->mergeItems($items);

            // Validate and prepare items
            $itemsData = $this->prepareItems($mergedItems);

            $total = collect($itemsData['pivot'])->sum('total');

            $afterDiscount = $this->afterDiscount($total, $dealDTO->discount_value ?? 0, $dealDTO->discount_type ?? 'fixed');

            $totalAmount = $this->afterTax($afterDiscount, $dealDTO->tax_rate ?? 0);

            // Update deal
            $dealDTO->total_amount = $totalAmount;
            $deal->update(Arr::except($dealDTO->toArray(), ['items', 'attachments']));

            // Sync items (remove old, add new)
            $deal->items()->sync($itemsData['pivot']);

            // Handle attachments if provided
            if ($dealDTO->attachments && count($dealDTO->attachments) > 0) {
                $this->handleAttachments($deal, $dealDTO->attachments);
            }

            return $deal->load('items', 'attachments');
        });
    }

    /**
     * Validate deal settings (shared between create and update)
     */
    private function validateDealSettings(DealDTO $dealDTO): void
    {
        $settings = app(DealsSettings::class);

        // Validate attachments feature toggle
        if (!$settings->enable_attachments && !empty($dealDTO->attachments)) {
            throw ValidationException::withMessages([
                'attachments' => ['Attachments are disabled in the system settings.']
            ]);
        }

        // Validate payment status if partial payment
        if ($dealDTO->payment_status === 'partial') {
            if (!$settings->enable_partial_payments) {
                throw ValidationException::withMessages([
                    'payment_status' => ['Partial payments are not enabled in the system settings.']
                ]);
            }
        }

        // Validate discount settings
        if ($dealDTO->discount_value && $dealDTO->discount_value > 0) {
            // Using settings loaded above

            // Check if discounts are enabled
            if (!$settings->enable_discounts) {
                throw ValidationException::withMessages([
                    'discount_value' => ['Discounts are not enabled in the system settings.']
                ]);
            }

            // Check maximum discount percentage if discount type is percentage
            if ($dealDTO->discount_type === 'percentage') {
                if ($dealDTO->discount_value > $settings->maximum_discount_percentage) {
                    throw ValidationException::withMessages([
                        'discount_value' => ["Discount percentage cannot exceed {$settings->maximum_discount_percentage}%."]
                    ]);
                }
            }
        }
    }

    private function mergeItems(array $items): array
    {
        return collect($items)
            ->groupBy('item_id')
            ->map(fn($group) => [
                'item_id' => $group->first()['item_id'],
                'quantity' => $group->sum('quantity'),
                'price' => $group->first()['price']
            ])
            ->values()->toArray();
    }

    private function prepareItems(array $items): array
    {
        $itemIds = collect($items)->pluck('item_id');

        // Lock items and get current data
        $dbItems = $this->itemModel->whereIn('id', $itemIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $pivot = [];
        $errors = [];

        foreach ($items as $item) {
            $itemId = $item['item_id'];
            $quantity = max(1, $item['quantity']);

            $dbItem = $dbItems->get($itemId);

            if (!$dbItem) {
                $errors[] = "Item ID {$itemId} not found.";
                continue;
            }

            // Check stock
            if ($dbItem->quantity !== null && $dbItem->quantity < $quantity && $dbItem->type == DealType::PRODUCT_SALE->value) {
                $errors[] = "Not enough stock for {$dbItem->name}. Available: {$dbItem->quantity}";
                continue;
            }

            $price = (float) $item['price'];

            $pivot[$itemId] = [
                'quantity' => $quantity,
                'price' => $price,
                'total' => $quantity * $price,
            ];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages(['items' => $errors]);
        }

        return compact('pivot');
    }

    private function afterDiscount(float $totalAmount, float $discountValue, string $discountType): float
    {
        if ($discountType == 'percentage') {
            $totalAmount = $totalAmount - ($totalAmount * $discountValue / 100);
        } else {
            $totalAmount = $totalAmount - $discountValue;
        }
        return $totalAmount;
    }

    private function afterTax(float $totalAmount, float $taxRate): float
    {
        $totalAmount = $totalAmount + ($totalAmount * $taxRate / 100);
        return $totalAmount;
    }

    /**
     * Handle deal attachments
     */
    private function handleAttachments(Deal $deal, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if ($attachment && $attachment->isValid()) {
                // Get file information before processing
                $fileName = $attachment->getClientOriginalName();
                $fileType = $attachment->getClientMimeType();
                $fileSize = $attachment->getSize();

                // Store the file using Spatie MediaLibrary on the Deal model
                $media = $deal
                    ->addMedia($attachment)
                    ->toMediaCollection('deal_attachments');

                // Create the DealAttachment record with media_id
                DealAttachment::create([
                    'deal_id' => $deal->id,
                    'media_id' => $media->id,
                    'name' => $fileName,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                ]);
            }
        }
    }
}
