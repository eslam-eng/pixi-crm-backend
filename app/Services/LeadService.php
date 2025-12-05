<?php

namespace App\Services;

use App\DTO\Tenant\LeadDTO;
use App\DTO\Tenant\LeadItemDTO;
use App\DTO\Tenant\LogCallDTO;
use App\DTO\Tenant\Opportunity\ActivityLogDTO;
use App\DTO\Tenant\Opportunity\SendOpportunityItemsDTO;
use App\Enums\OpportunityStatus;
use App\Mail\OpportunityItemsMail;
use App\QueryFilters\LeadFilters;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\GeneralException;
use App\Models\Tenant\Item;
use App\Models\Tenant\Lead;
use App\Notifications\Tenant\UpdateAssignOpportunityNotification;
use App\Services\Tenant\ItemService;
use App\Services\Tenant\Users\UserService;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class LeadService extends BaseService
{
    public function __construct(
        public Lead $model,
        public Item $itemModel,
        public StageService $stageService,
        public UserService $userService,
        public ItemService $itemService,
    ) {
    }

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
        $items = collect($leadDTO->items ?? [])->map(fn($r) => LeadItemDTO::fromArray($r));

        $itemsCol = $items
            ->filter(fn($r) => !empty($r->item_id));

        $leadDTO->deal_value = $itemsCol->sum(fn($r) => (float) $r->price * (int) ($r->quantity ?? 1));

        // divide the items by the presence of variant_id
        [$withVariant, $withItemOnly] = $itemsCol->partition(fn($r) => !empty($r->variant_id));

        $this->checkUncountableServiceItems($withItemOnly);

        $variantsPayload = $withVariant
            ->mapWithKeys(fn($r) => [
                (int) $r->variant_id => [
                    'price' => (float) $r->price,
                    'quantity' => (int) $r->quantity,
                ],
            ])
            ->all();

        $itemsPayload = $withItemOnly
            ->mapWithKeys(fn($r) => [
                (int) $r->item_id => [
                    'price' => (float) $r->price,
                    'quantity' => $r->quantity ?? 1,
                ],
            ])
            ->all();

        $lead = $this->model->create($leadDTO->toArray());

        Model::withoutEvents(function () use ($lead, $variantsPayload, $itemsPayload, $leadDTO) {
            if (!empty($variantsPayload)) {
                $lead->variants()->sync($variantsPayload, true);
            }

            if (!empty($itemsPayload)) {
                $lead->items()->sync($itemsPayload, true);
            }
            $this->updateAssignedAt($lead, $leadDTO->assigned_to_id);
        });

        return $lead->load('variants', 'items.product', 'items.service', 'items.category.parent');
    }

    public function show(int $id)
    {
        $lead = $this->findById($id);
        return $lead->load(['contact.contactPhones', 'city', 'stage', 'items.itemable', 'variants.product', 'user', 'items.category.parent']);
    }


    public function update(int $id, LeadDTO $leadDTO)
    {
        $lead = $this->findById($id);
        if ($leadDTO->items) {
            $itemsCol = collect($leadDTO->items ?? [])->map(fn($r) => LeadItemDTO::fromArray($r))
                ->filter(fn($r) => !empty($r->item_id));

            $leadDTO->deal_value = $itemsCol->sum(fn($r) => (float) $r->price * (int) ($r->quantity ?? 1));

            // divide the items by the presence of variant_id
            [$withVariant, $withItemOnly] = $itemsCol->partition(fn($r) => !empty($r->variant_id));

            $this->checkUncountableServiceItems($withItemOnly);

            $variantsPayload = $withVariant
                ->mapWithKeys(fn($r) => [
                    (int) $r->variant_id => [
                        'price' => (float) $r->price,
                        'quantity' => (int) $r->quantity,
                    ],
                ])
                ->all();

            $itemsPayload = $withItemOnly
                ->filter(fn($r) => !empty($r->item_id))
                ->mapWithKeys(fn($r) => [
                    (int) $r->item_id => [
                        'price' => (float) $r->price,
                        'quantity' => (int) $r->quantity,
                    ],
                ])
                ->all();
        }
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
            $this->updateAssignedAt($lead, $leadDTO->assigned_to_id);

            $currentUser = $lead->user;
            $managers = $this->userService->getModel()->role('manager')->get();
            foreach ($managers as $manager) {
                $manager->notify(new UpdateAssignOpportunityNotification($lead->load('user')));
            }
            if ($currentUser) {
                $currentUser->notify(new UpdateAssignOpportunityNotification($lead->load('user')));
            }
        } else {
            $this->updateActionTimes($lead);
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

    public function checkUncountableServiceItems(Collection $withItemOnly): void
    {
        foreach ($withItemOnly as $item) {
            /** @var LeadItemDTO $item */
            $itemInOrder = $this->itemService->getModel()->find($item->item_id);
            if ($itemInOrder && $itemInOrder->itemable_type == 'service' && $item->quantity > 1) {
                throw ValidationException::withMessages(['items' => __('app.service_items_not_countable') . " with id :" . ' ' . $itemInOrder->id]);
            }
        }
    }

    /**
     * Update assigned_at timestamp when assignment changes
     */
    private function updateAssignedAt(Lead $lead, ?int $assignedToId): void
    {
        if (!empty($assignedToId)) {
            $lead->assigned_at = Carbon::now();
            $lead->first_action_at = null;
            $lead->avg_action_time = null;
            $lead->save();
        }
    }

    /**
     * Update first_action_at and avg_action_time after an update
     */
    private function updateActionTimes(Lead $lead): void
    {
        if (user_id() == $lead->assigned_to_id) {
            // Only update if the lead is assigned
            if (empty($lead->assigned_at)) {
                return;
            }

            if (!empty($lead->first_action_at)) {
                return;
            }

            $now = Carbon::now();
            $lead->first_action_at = $now;

            $assignedAt = Carbon::parse($lead->assigned_at);
            $firstActionAt = Carbon::parse($lead->first_action_at);
            $avgActionTime = $assignedAt->diffInSeconds($firstActionAt);

            $lead->avg_action_time = $avgActionTime;
            $lead->save();
        }
    }

    /**
     * Update opportunity status to WON
     */
    public function markAsWon(int $id): Lead
    {
        $lead = $this->findById($id);
        if ($lead->status !== \App\Enums\OpportunityStatus::WON) {
            $lead->update(['status' => \App\Enums\OpportunityStatus::WON]);
        }
        return $lead;
    }

    public function changeStatus(int $id, OpportunityStatus $status): void
    {
        $lead = $this->findById($id);

        // if ($status === OpportunityStatus::WON) {
        //     throw new GeneralException('Opportunity cannot be marked as won without a deal');
        // }

        if ($lead->status !== $status) {
            $lead->update(['status' => $status]);
            $this->updateActionTimes($lead);
        } else {
            throw new GeneralException('Opportunity status is already ' . $status->value);
        }
    }

    /**
     * Change the stage of an opportunity
     */
    public function changeStage(int $id, int $stageId): Lead
    {
        $lead = $this->findById($id);
        $lead->update(['stage_id' => $stageId]);

        // Update action times after stage change
        $this->updateActionTimes($lead);

        activity()
            ->causedBy(user_id())
            ->performedOn($lead)
            ->withProperties([
                'old_stage' => $lead->stage_id,
                'new_stage' => $stageId
            ])
            ->useLog('lead')
            ->log('stage_changed');

        return $lead;
    }

    public function logCall(int $id, LogCallDTO $data): void
    {
        $lead = $this->findById($id);
        activity()
            ->causedBy(user_id()) // optional
            ->performedOn($lead) // optional model
            ->withProperties([
                'call_notes' => $data->call_notes,
                'call_direction' => $data->call_direction,
            ])
            ->useLog('lead') // optional log name
            ->log('log_call');
    }

    public function addActivityLog(int $id, ActivityLogDTO $dto): void
    {
        $opportunity = $this->findById($id);
        activity()
            ->causedBy(Auth::user()) // optional
            ->performedOn($opportunity)     // optional model
            ->withProperties($dto->toArray())
            ->useLog('lead')
            ->log('opportunity_activity_added');
    }

    public function sendItems(int $id, SendOpportunityItemsDTO $dto): void
    {
        $lead = $this->findById($id);

        // Get all items associated with the lead
        $items = $lead->items()->get();

        if ($dto->channel === 'email') {
            if (!$lead->contact || !$lead->contact->email) {
                throw new GeneralException('Contact email is missing.');
            }

            \Mail::to($lead->contact->email)->send(new OpportunityItemsMail($lead, $items, $dto->selected_item_columns, $dto->subject));
        } elseif ($dto->channel === 'whatsapp') {
            if (!$lead->contact || !$lead->contact->phone) {
                throw new GeneralException('Contact phone is missing.');
            }
            // Logic for WhatsApp integration would go here
            // For now, we can log it or throw a not implemented exception if no provider is set up
            // This is a placeholder for the actual WhatsApp sending logic
        }
    }
}
