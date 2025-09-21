<?php

namespace App\Services\Tenant\Tasks;

use App\Exceptions\GeneralException;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Tenant\TaskType;
use App\Services\BaseService;
use DB;

class TaskTypeService extends BaseService
{
    public function __construct(
        public TaskType $model,
    ) {}

    public function getModel(): TaskType
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $data = $this->model->with($withRelations)->ordered();
        return $data;
    }

    public function setDefault(int $id): TaskType
    {
        try {
            DB::beginTransaction();
            
            $record = $this->findById($id);
            
            // Unset all other defaults
            $this->model->where('is_default', true)->update(['is_default' => false]);
            
            // Set this priority as default
            $record->update(['is_default' => true]);
            
            DB::commit();
            return $record->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new GeneralException('Failed to set default priority: ' . $e->getMessage());
        }
    }



 
}