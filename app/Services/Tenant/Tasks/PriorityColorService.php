<?php

namespace App\Services\Tenant\Tasks;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Tenant\PriorityColor;
use App\Services\BaseService;

class PriorityColorService extends BaseService
{
    public function __construct(
        public PriorityColor $model,
    ) {}

    public function getModel(): PriorityColor
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
        $priorities = $this->model->with($withRelations)->ordered();
        return $priorities;
    }
}