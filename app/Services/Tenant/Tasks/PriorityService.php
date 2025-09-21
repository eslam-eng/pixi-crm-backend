<?php

namespace App\Services\Tenant\Tasks;

use App\Exceptions\GeneralException;
use App\Models\Tenant\Priority;
use Illuminate\Database\Eloquent\Builder;
use App\DTO\Priority\PriorityDTO;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class PriorityService extends BaseService
{
    public function __construct(
        public Priority $model,
    ) {}

    public function getModel(): Priority
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
        $defaultRelations = ['color'];
        $withRelations = array_merge($defaultRelations, $withRelations);
        $priorities = $this->model->with($withRelations)->ordered();
        return $priorities;
    }

    public function store(PriorityDTO $priorityDTO): Priority
    {
        try {
            DB::beginTransaction();
            
            // If this priority is set as default, unset all other defaults
            if ($priorityDTO->is_default) {
                $this->model->where('is_default', true)->update(['is_default' => false]);
            }
            
            $priority = $this->model->create($priorityDTO->toArray());
            
            DB::commit();
            return $priority;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to create priority: ' . $e->getMessage());
        }
    }

    public function update(int $id, PriorityDTO $priorityDTO): Priority
    {
        try {
            DB::beginTransaction();
            
            $priority = $this->findById($id);
            
            // If this priority is set as default, unset all other defaults
            if ($priorityDTO->is_default) {
                $this->model->where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
            }
            
            $priority->update($priorityDTO->toArray());
            
            DB::commit();
            return $priority->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to update priority: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $priority = $this->findById($id);
            // Check if priority is being used by tasks
            if ($priority->tasks()->exists()) {
                throw new GeneralException(__('app.cannot_delete_priority_used_by_tasks'));
            }
            
            $result = $priority->delete();
            
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to delete priority: ' . $e->getMessage());
        }
    }

    public function setDefault(int $id): Priority
    {
        try {
            DB::beginTransaction();
            
            $priority = $this->findById($id);
            
            // Unset all other defaults
            $this->model->where('is_default', true)->update(['is_default' => false]);
            
            // Set this priority as default
            $priority->update(['is_default' => true]);
            
            DB::commit();
            return $priority->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to set default priority: ' . $e->getMessage());
        }
    }

    public function getDefault(): ?Priority
    {
        return $this->model->default()->first();
    }
}