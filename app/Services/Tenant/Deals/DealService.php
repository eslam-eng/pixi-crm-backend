<?php

namespace App\Services\Tenant\Deals;

use App\DTO\Tenant\DealDTO;
use App\DTO\Tenant\DealPaymentDTO;
use App\Enums\ApprovalStatusEnum;
use App\Enums\DealType;
use App\Enums\PaymentStatusEnum;
use App\Enums\RolesEnum;
use App\Models\Tenant\Deal;
use App\Models\Tenant\DealAttachment;
use App\Models\Tenant\DealItem;
use App\Models\Tenant\DealItemSubscription;
use App\Models\Tenant\Item;
use App\Models\Tenant\ItemVariant;
use App\QueryFilters\Tenant\DealsFilter;
use App\Services\BaseService;
use App\Services\Tenant\Deals\DealPaymentService;
use App\Settings\DealsSettings;
use App\Notifications\DealPaymentTermsNotification;
use Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;

class DealService extends BaseService
{
    public function __construct(
        public Deal $model,
        public Item $itemModel,
        public ItemVariant $itemVariantModel,
        public DealPaymentService $dealPaymentService,
    ) {}

    public function getModel(): Deal
    {
        return $this->model;
    }

    public function queryGet(array $filters = [], array $withRelations = []): builder
    {
        $deals = $this->getQuery()->with($withRelations);
        return $deals->filter(new DealsFilter($filters));
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    /**
     * Get paginated deals with filters and relationships
     */
    public function paginate(Request $request, array $relationships = [])
    {
        $defaultRelationships = ['items', 'lead', 'created_by'];
        $relationships = array_merge($defaultRelationships, $relationships);

        // Apply filters using Filterable trait
        $query = $this->model->filter(new DealsFilter($request->all()));

        // Load relationships
        $query->with($relationships);

        $query->orderBy('id', 'desc');

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
                'deal_items.item',
                'deal_items.subscription',
                'variants',
                'attachments',
                'payments.payment_method'
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

            $variants = $this->getVariants($items);
            $mergedVariants = $this->mergeVariants($variants);

            $realItem = $this->getItems($items);
            // Merge duplicate items
            $mergedItems = $this->mergeItems($realItem);

            // Validate and prepare items
            $itemsData = $this->prepareItems($mergedItems);

            $variantsData = $this->prepareVariants($mergedVariants);

            $item_total = collect($itemsData['pivot'])->sum('total');
            $variant_total = collect($variantsData['pivot'])->sum('total');

            $total = $item_total + $variant_total;

            $afterDiscount = $this->afterDiscount($total, $dealDTO->discount_value ?? 0, $dealDTO->discount_type ?? 'fixed');

            $totalAmount = $this->afterTax($afterDiscount, $dealDTO->tax_rate ?? 0);

            // Get deals settings to determine approval status
            $settings = app(DealsSettings::class);

            // Set approval status based on user role and settings
            if (!$dealDTO->approval_status) {
                $dealDTO->approval_status = $this->determineApprovalStatus($settings, $totalAmount);
            }

            // Calculate amount_due based on payment status
            $dealDTO->total_amount = $totalAmount;
            $dealDTO->amount_due = $this->calculatePartialAmountDue($dealDTO->payment_status, $totalAmount, $dealDTO->partial_amount_paid ?? 0);

            if ($dealDTO->payment_status == PaymentStatusEnum::PAID->value) {
                $dealDTO->partial_amount_paid = 0;
            }
            // Create deal
            $deal = $this->model->create(Arr::except($dealDTO->toArray(), ['items', 'attachments']));

            // Create items individually to handle subscriptions
            $this->createDealItemsWithSubscriptions($deal, $dealDTO->items);

            $deal->variants()->attach($variantsData['pivot']);

            // Handle attachments if provided
            if ($dealDTO->attachments && count($dealDTO->attachments) > 0) {
                $this->handleAttachments($deal, $dealDTO->attachments);
            }

            // Create payment record if payment_status is partial and partial_amount_paid > 0
            if (
                $dealDTO->payment_status == PaymentStatusEnum::PARTIAL->value &&
                isset($dealDTO->partial_amount_paid) &&
                $dealDTO->partial_amount_paid > 0
            ) {

                $paymentDTO = DealPaymentDTO::fromArray([
                    'amount' => $dealDTO->partial_amount_paid,
                    'pay_date' => $dealDTO->sale_date, // Use sale_date as payment date
                    'payment_method_id' => $dealDTO->payment_method_id,
                ]);

                $this->dealPaymentService->addPaymentToDeal($deal->id, $paymentDTO);
            }

            // Send payment terms email for unpaid or partial payments
            $this->sendPaymentTermsEmailIfNeeded($deal, $settings);

            return $deal->load('items', 'lead', 'attachments', 'payments');
        });
    }

    public function update(DealDTO $dealDTO, int $dealId): Deal
    {
        // Validate deal settings
        $this->validateDealSettings($dealDTO);

        // Check if deal exists
        $deal = $this->model->find($dealId);
        if (!$deal) {
            throw ValidationException::withMessages([
                'deal' => ['The specified deal does not exist.']
            ]);
        }

        return DB::transaction(function () use ($dealDTO, $deal) {

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
            $dealDTO->amount_due = $this->calculatePartialAmountDue($dealDTO->payment_status, $totalAmount, $dealDTO->partial_amount_paid ?? 0);

            // Preserve approval_status if not provided in request
            if (!$dealDTO->approval_status) {
                $dealDTO->approval_status = $deal->approval_status;
            }

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

        // Validate approval requirements
        $this->validateApprovalRequirements($dealDTO, $settings);
    }

    /**
     * Validate approval requirements based on deal settings
     */
    /**
     * Change the approval status of a deal
     */
    public function changeApprovalStatus(int $dealId, string $status): Deal
    {
        $user = Auth::user();

        // Check if user has permission to change approval status
        $this->validateApprovalPermission($user);

        // Deal existence and status validation is handled in the request
        $deal = $this->model->with('created_by')->find($dealId);

        // Check if user is manager and in the same department as deal creator
        $this->validateDepartmentPermission($user, $deal);

        $deal->update(['approval_status' => $status]);

        return $deal->load('items', 'lead', 'attachments', 'created_by');
    }

    /**
     * Validate if user has permission to change approval status
     */
    private function validateApprovalPermission($user): void
    {
        if ($user->hasRole(RolesEnum::AGENT->value)) {
            throw ValidationException::withMessages([
                'permission' => ['You do not have permission to change approval status. Only managers can perform this action.']
            ]);
        }

        if (!$user->hasRole(RolesEnum::MANAGER->value)) {
            throw ValidationException::withMessages([
                'permission' => ['You do not have permission to change approval status. Only managers can perform this action.']
            ]);
        }
    }

    /**
     * Validate if user is in the same department as deal creator
     */
    private function validateDepartmentPermission($user, Deal $deal): void
    {
        if ($deal->created_by && $deal->created_by->department_id !== $user->department_id) {
            throw ValidationException::withMessages([
                'department' => ['You can only change approval status for deals created by users in your department.']
            ]);
        }
    }

    /**
     * Calculate partial amount due based on payment status
     */
    private function calculatePartialAmountDue(string $paymentStatus, float $totalAmount, float $partialAmountPaid = 0): float
    {
        return match ($paymentStatus) {
            PaymentStatusEnum::PARTIAL->value => $totalAmount - $partialAmountPaid,
            PaymentStatusEnum::PAID->value => 0,
            PaymentStatusEnum::UNPAID->value => $totalAmount,
            default => 0,
        };
    }

    /**
     * Determine the approval status based on user role and deals settings
     */
    private function determineApprovalStatus(DealsSettings $settings, float $totalAmount): string
    {
        $user = Auth::user();

        // If all deals require approval
        if ($settings->all_deals_required_approval) {
            // Managers can approve deals directly, agents need approval
            if ($user->hasRole(RolesEnum::MANAGER->value) || $user->hasRole(RolesEnum::ADMIN->value)) {
                return ApprovalStatusEnum::APPROVED->value;
            } elseif ($user->hasRole(RolesEnum::AGENT->value)) {
                return ApprovalStatusEnum::PENDING->value;
            }
        }

        // If high-value deals require approval
        if ($settings->require_approval_high_value_deals && $totalAmount >= $settings->approval_threshold_amount) {
            // High-value deals require approval regardless of role
            if ($user->hasRole(RolesEnum::MANAGER->value) || $user->hasRole(RolesEnum::ADMIN->value)) {
                return ApprovalStatusEnum::APPROVED->value;
            } elseif ($user->hasRole(RolesEnum::AGENT->value)) {
                return ApprovalStatusEnum::PENDING->value;
            }
        }

        // For deals below threshold or when no approval required
        if ($user->hasRole(RolesEnum::MANAGER->value)) {
            return ApprovalStatusEnum::APPROVED->value;
        } elseif ($user->hasRole(RolesEnum::AGENT->value)) {
            return ApprovalStatusEnum::APPROVED->value; // Agents can approve low-value deals
        }

        // Default fallback
        return ApprovalStatusEnum::PENDING->value;
    }

    private function validateApprovalRequirements(DealDTO $dealDTO, DealsSettings $settings): void
    {
        // TODO: Implement approval validation when approval system is fully implemented
        // For now, this method serves as a placeholder for future approval logic

        // Check if all deals require approval
        if ($settings->all_deals_required_approval) {
            // If all deals require approval, ensure the deal has approval status
            if (!$dealDTO->approval_status || $dealDTO->approval_status === 'pending') {
                throw ValidationException::withMessages([
                    'approval_status' => ['All deals require approval. Please ensure the deal is approved before proceeding.']
                ]);
            }
        } else {
            // Check if high-value deals require approval
            if ($settings->require_approval_high_value_deals && $dealDTO->total_amount >= $settings->approval_threshold_amount) {
                if (!$dealDTO->approval_status || $dealDTO->approval_status === 'pending') {
                    throw ValidationException::withMessages([
                        'approval_status' => ["Deals with amount {$dealDTO->total_amount} or higher require approval. Please ensure the deal is approved before proceeding."]
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
            if ($dbItem->itemable->stock !== null && $dbItem->itemable->stock < $quantity) {
                $errors[] = "Not enough stock for {$dbItem->name}. Available: {$dbItem->itemable->stock}";
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

    private function prepareVariants(array $items): array
    {
        $itemIds = collect($items)->pluck('item_id');
        // Lock items and get current data
        $dbItems = $this->itemVariantModel->whereIn('id', $itemIds)
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
            if ($dbItem->stock !== null && $dbItem->stock < $quantity) {
                $errors[] = "Not enough stock for {$dbItem->product->item->name}. Available: {$dbItem->stock}";
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

    private function getVariants(array $items): array
    {
        return collect($items)->filter(fn($item) => isset($item['variant_id']))->values()->toArray();
    }

    private function getItems(array $items): array
    {
        return collect($items)
            ->reject(fn($item) => array_key_exists('variant_id', $item))
            ->values()
            ->toArray();
    }

    private function mergeVariants(array $items): array
    {
        return collect($items)
            ->groupBy('variant_id')
            ->map(fn($group) => [
                'item_id' => $group->first()['variant_id'],
                'quantity' => $group->sum('quantity'),
                'price' => $group->first()['price']
            ])
            ->values()->toArray();
    }
    /**
     * Send payment terms notification when deal payment status is unpaid or partial
     */
    private function sendPaymentTermsEmailIfNeeded(Deal $deal, DealsSettings $settings): void
    {
        // Check if payment status requires email notification
        if (!in_array($deal->payment_status, [PaymentStatusEnum::UNPAID->value, PaymentStatusEnum::PARTIAL->value])) {
            return;
        }

        try {
            // Get the lead and its contact
            $lead = $deal->lead()->with('contact')->first();

            if (!$lead || !$lead->contact || !$lead->contact->email) {
                \Log::warning('Cannot send payment terms notification: Lead or contact email not found', [
                    'deal_id' => $deal->id,
                    'lead_id' => $lead?->id,
                    'contact_id' => $lead?->contact?->id,
                    'contact_email' => $lead?->contact?->email,
                ]);
                return;
            }

            // Send the notification
            $lead->contact->notify(
                new DealPaymentTermsNotification(
                    deal: $deal,
                    paymentTerms: $settings->payment_terms_text
                )
            );

            \Log::info('Payment terms notification sent successfully', [
                'deal_id' => $deal->id,
                'contact_id' => $lead->contact->id,
                'contact_email' => $lead->contact->email,
                'payment_status' => $deal->payment_status,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send payment terms notification', [
                'deal_id' => $deal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Create deal items individually and handle subscriptions
     */
    private function createDealItemsWithSubscriptions(Deal $deal, array $items): void
    {
        foreach ($items as $item) {
            // Create the deal item
            $dealItem = DealItem::create([
                'deal_id' => $deal->id,
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'] ?? 1,
                'price' => $item['price'],
                'total' => $item['total'] ?? ($item['price'] * ($item['quantity'] ?? 1)),
            ]);

            // Create subscription if subscription data is provided
            if (isset($item['start_at']) && isset($item['end_at']) && isset($item['billing_cycle'])) {
                DealItemSubscription::create([
                    'deal_item_id' => $dealItem->id,
                    'start_at' => $item['start_at'],
                    'end_at' => $item['end_at'],
                    'billing_cycle' => $item['billing_cycle'],
                ]);
            }
        }
    }
}
