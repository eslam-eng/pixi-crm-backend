<?php

namespace App\Services;

use App\DTO\Tenant\LeadDTO;
use App\QueryFilters\LeadFilters;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\GeneralException;
use App\Models\Tenant\Item;
use App\Models\Tenant\Lead;
use App\Notifications\Tenant\UpdateAssignOpportunityNotification;
use App\Services\Tenant\Users\UserService;
use Auth;

class LeadService extends BaseService
{
    public function __construct(
        public Lead $model,
        public Item $itemModel,
        public StageService $stageService,
        public UserService $userService,
    ) {}

    public function getModel(): Lead
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $leads = $this->model->with($withRelations)->orderBy('id', 'desc');
        return $leads->filter(new LeadFilters($filters));
    }

    public function datatable(array $filters = [], array $withRelations = [])
    {
        $leads = $this->getQuery()->with($withRelations);
        return $leads->filter(new LeadFilters($filters));
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    private function checkItemQuantityThenUpdate(int $itemId, int $quantity)
    {
        $item = $this->itemModel->find($itemId);
        if ($item->quantity < $quantity) {
            throw new GeneralException(__('app.item_quantity_not_enough') . " with id :" . ' ' . $item->id);
        }
        $item->quantity -= $quantity;
        $item->save();
    }

    public function store(LeadDTO $leadDTO)
    {
        $itemsCol = collect($leadDTO->items ?? [])
            ->filter(
                fn($r) =>
                // filter any row with price and quantity
                isset($r['price'], $r['quantity']) &&
                    (int)$r['quantity'] > 0 &&
                    (float)$r['price'] >= 0
            );

        $leadDTO->deal_value = $itemsCol->sum(fn($r) => (float)$r['price'] * (int)$r['quantity']);

        // divide the items by the presence of variant_id
        [$withVariant, $withItemOnly] = $itemsCol->partition(fn($r) => !empty($r['variant_id']));

        $variantsPayload = $withVariant
            ->mapWithKeys(fn($r) => [
                (int)$r['variant_id'] => [
                    'price'    => (float)$r['price'],
                    'quantity' => (int)$r['quantity'],
                ],
            ])
            ->all();

        $itemsPayload = $withItemOnly
            ->filter(fn($r) => !empty($r['item_id']))
            ->mapWithKeys(fn($r) => [
                (int)$r['item_id'] => [
                    'price'    => (float)$r['price'],
                    'quantity' => (int)$r['quantity'],
                ],
            ])
            ->all();

        $lead = $this->model->create($leadDTO->toArray());

        if (!empty($variantsPayload)) {
            $lead->variants()->sync($variantsPayload, true);
        }

        if (!empty($itemsPayload)) {
            $lead->items()->sync($itemsPayload, true);
        }

        // Dispatch OpportunityCreated event
        $creationData = [
            'deal_value' => $lead->deal_value,
            'status' => $lead->status->value,
            'stage_id' => $lead->stage_id,
            'contact_id' => $lead->contact_id,
            'assigned_to_id' => $lead->assigned_to_id,
            'created_at' => now(),
        ];
        
        // Configuration for opportunity_created trigger
        $configuration = [
            'required_fields' => ['amount', 'country'],
            'default_pipeline' => 'Sales',
            'validation_rules' => [
                'amount' => 'required|numeric|min:0',
                'country' => 'required|string|max:255'
            ]
        ];
        

        return $lead->load('variants', 'items');
    }

    public function show(int $id)
    {
        $lead = $this->findById($id);
        return $lead->load('contact', 'city', 'stage', 'user', 'variants', 'items.itemable');
    }


    public function update(int $id, LeadDTO $leadDTO)
    {
        $lead = $this->findById($id);
        if ($leadDTO->items) {
            $itemsCol = collect($leadDTO->items ?? [])
                ->filter(
                    fn($r) =>
                    // filter any row with price and quantity
                    isset($r['price'], $r['quantity']) &&
                        (int)$r['quantity'] > 0 &&
                        (float)$r['price'] >= 0
                );

            $leadDTO->deal_value = $itemsCol->sum(fn($r) => (float)$r['price'] * (int)$r['quantity']);

            // divide the items by the presence of variant_id
            [$withVariant, $withItemOnly] = $itemsCol->partition(fn($r) => !empty($r['variant_id']));

            $variantsPayload = $withVariant
                ->mapWithKeys(fn($r) => [
                    (int)$r['variant_id'] => [
                        'price'    => (float)$r['price'],
                        'quantity' => (int)$r['quantity'],
                    ],
                ])
                ->all();

            $itemsPayload = $withItemOnly
                ->filter(fn($r) => !empty($r['item_id']))
                ->mapWithKeys(fn($r) => [
                    (int)$r['item_id'] => [
                        'price'    => (float)$r['price'],
                        'quantity' => (int)$r['quantity'],
                    ],
                ])
                ->all();
        }
        
        // Get original data before update
        $originalStatus = $lead->status;
        $originalStageId = $lead->stage_id;
        
        $lead->update($leadDTO->toArray());

        if (!empty($variantsPayload)) {
            $lead->variants()->sync($variantsPayload, true);
        } else {
            $lead->variants()->detach();
        }

        if (!empty($itemsPayload)) {
            $lead->items()->sync($itemsPayload, true);
        } else {
            $lead->items()->detach();
        }

        if ($lead->wasChanged('assigned_to_id')) {
            $currentUser = $lead->user;
            $managers = $this->userService->getModel()->role('manager')->get();
            foreach ($managers as $manager) {
                $manager->notify(new UpdateAssignOpportunityNotification($lead->load('user')));
            }
            if ($currentUser) {
                $currentUser->notify(new UpdateAssignOpportunityNotification($lead->load('user')));
            }
        }
        return $lead->load('variants', 'items');
    }

    /**
     * Check if a lead became qualified based on status or stage changes
     */
    private function isLeadQualified(Lead $lead, $originalStatus, $originalStageId): bool
    {
        // Lead is qualified if:
        // 1. Status changed to 'active' (from any other status)
        // 2. Stage changed to Stage 2 or higher (assuming Stage 2+ represents qualification)
        // 3. Lead has a deal value > 0 and is active
        
        $statusQualified = $lead->status->value === 'active' && $originalStatus !== 'active';
        $stageQualified = $lead->stage_id !== $originalStageId && $lead->stage && $lead->stage->seq_number >= 2;
        $valueQualified = $lead->deal_value > 0 && $lead->status->value === 'active';
        
        return $statusQualified || $stageQualified || $valueQualified;
    }

    public function delete(int $id)
    {
        $lead = $this->findById($id);
        $lead->variants()->detach();
        $lead->items()->detach();
        return $lead->delete();
    }

    public function kanbanList()
    {
        return $this->stageService->queryGet(
            withRelations: [
                'leads' => function ($query) {
                    $query->where('assigned_to_id', Auth::user()->id)->with(['user', 'contact', 'variants', 'items']);
                },
                'pipeline'
            ],
            filters: ['assigned_to_id' => Auth::user()->id]
        )->get();
    }
}
