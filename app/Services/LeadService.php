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
        if ($leadDTO->items) {
            $leadDTO->deal_value = 0;
            $map = collect($leadDTO->items)->mapWithKeys(function ($row) use ($leadDTO) {
                $leadDTO->deal_value += $row['price'] * $row['quantity'];
                return [
                    (int) $row['id'] => [
                        'price'    => (float) $row['price'],
                        'quantity' => (int) $row['quantity'],
                    ],
                ];
            })->all();
        }
        $lead = $this->model->create($leadDTO->toArray());
        if ($leadDTO->items) {
            $lead->variants()->sync($map, false);
        }
        return $lead->load('variants.item');
    }

    public function show(int $id)
    {
        $lead = $this->findById($id);
        return $lead->load('contact', 'city', 'stage', 'user', 'variants.item');
    }


    public function update(int $id, LeadDTO $leadDTO)
    {
        $lead = $this->findById($id);
        if ($leadDTO->items) {
            $leadDTO->deal_value = 0;
            $map = collect($leadDTO->items)->mapWithKeys(function ($row) use ($leadDTO) {
                $leadDTO->deal_value += $row['price'] * $row['quantity'];
                return [
                    (int) $row['id'] => [
                        'price'    => (float) $row['price'],
                        'quantity' => (int) $row['quantity'],
                    ],
                ];
            })->all();
            $lead->variants()->sync($map, false);
        }
        $lead->update($leadDTO->toArray());

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
        return $lead->load('variants.item');
    }

    public function delete(int $id)
    {
        $lead = $this->findById($id);
        $lead->variants()->detach();
        return $lead->delete();
    }

    public function kanbanList()
    {
        return $this->stageService->queryGet(
            withRelations: [
                'leads' => function ($query) {
                    $query->where('assigned_to_id', Auth::user()->id)->with(['user', 'contact', 'variants.item']);
                },
                'pipeline'
            ],
            filters: ['assigned_to_id' => Auth::user()->id]
        )->get();
    }
}
