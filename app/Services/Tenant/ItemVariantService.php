<?php

namespace App\Services\Tenant;

use App\Exceptions\GeneralException;
use App\Models\Tenant\Item;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\Item\ItemDTO;
use App\Enums\ItemType;
use App\Models\Filters\ItemFilter;
use App\Models\Tenant\ItemAttribute;
use App\QueryFilters\Tenant\ItemVariantFilters;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class ItemVariantService extends BaseService
{
    public function __construct(
        public Item $model,
        public ItemAttribute $itemAttribute,
    ) {}

    public function getModel(): Item
    {
        return $this->model;
    }

    public function getAll(int $itemId, array $filters = [])
    {
        return $this->queryGet($itemId, $filters)->get();
    }

    public function queryVariantGet(array $filters = [], array $withRelations = []): Builder
    {
        $query = $this->model->with($withRelations)->ordered();
        return $query->filter(new ItemVariantFilters($filters));
    }

    public function getAllVariant(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryVariantGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function getTableName(): string
    {
        return $this->getModel()->getTable();
    }

    public function listing(int $itemId, array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(itemId: $itemId, filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(int $itemId, array $filters = [], array $withRelations = []): HasMany
    {
        $item = $this->model->query()->findOrFail($itemId);
        if ($item->isProduct) {
            $query = $item->itemable
                ->variants()
                ->with($withRelations);
        } else {
            throw new GeneralException('Item is not a product');
        }
        return $query;
    }

    public function index(int $itemId, array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryGet(itemId: $itemId, filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function store(ItemDTO $itemDTO): Item | array | bool
    {
        if ($itemDTO->type == ItemType::SERVICE->value) {
            $itemDTO->quantity = null;
            $itemDTO->sku = null;
            $item = $this->model->create($itemDTO->toArray());
        } elseif ($itemDTO->type == ItemType::PRODUCT->value) {
            $itemDTO->duration = null;
            $item = $this->model->create($itemDTO->toArray());
        }

        return  $item;
    }

    public function update(int $id, ItemDTO $itemDTO): Item
    {
        try {
            DB::beginTransaction();
            $item = $this->findById($id);
            $item->update($itemDTO->toArray());
            DB::commit();
            return $item->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to update priority: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): bool
    {
        $item = $this->findById($id);
        if ($item->opportunities()->exists()) {
            throw new GeneralException(__('app.cannot_delete_item_used_by_opportunities'));
        }
        $result = $item->delete();
        return $result;
    }

    public function destroyVariant(int $id): bool
    {
        $variant = $this->findById($id);
        $variant->attributeValues()->detach();
        return $variant->delete();
    }
}
