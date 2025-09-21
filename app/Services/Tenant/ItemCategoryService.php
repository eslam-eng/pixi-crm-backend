<?php

namespace App\Services\Tenant;

use App\Exceptions\GeneralException;
use App\Models\Tenant\ItemCategory;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\ItemCategory\ItemCategoryDTO;
use App\Models\Filters\ItemCategoryFilters;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ItemCategoryService extends BaseService
{
    public function __construct(
        public ItemCategory $model,
    ) {}

    public function getModel(): ItemCategory
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
        $defaultRelations = [];
        $withRelations = array_merge($defaultRelations, $withRelations);
        if ($filters['parent_id'] ?? null) {
            $query = $this->model->with($withRelations)->ordered();
        } else {
            $query = $this->model->with($withRelations)->roots()->ordered();
        }
        return $query->filter(new ItemCategoryFilters($filters));
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);
        if ($perPage) {
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    public function store(ItemCategoryDTO $itemCategoryDTO): ItemCategory
    {
        try {
            DB::beginTransaction();
            $itemCategory = $this->model->create($itemCategoryDTO->toArray());
            DB::commit();
            return $itemCategory;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to create item category: ' . $e->getMessage());
        }
    }

    public function update(int $id, ItemCategoryDTO $itemCategoryDTO): ItemCategory
    {
        try {
            DB::beginTransaction();
            $itemCategory = $this->findById($id);
            $itemCategory->update(array_filter($itemCategoryDTO->toArray()));
            DB::commit();
            return $itemCategory->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to update category: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): bool
    {
        try {
            DB::beginTransaction();

            $itemCategory = $this->findById($id);
            // Check if priority is being used by tasks
            if ($itemCategory->items()->exists()) {
                throw new GeneralException(__('app.cannot_delete_item_category_used_by_items'));
            }
            if ($itemCategory->children()->exists()) {
                throw new GeneralException(__('app.cannot_delete_item_category_used_by_children'));
            }
            $result = $itemCategory->delete();

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to delete category: ' . $e->getMessage());
        }
    }
}
