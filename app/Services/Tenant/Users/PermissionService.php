<?php

namespace App\Services\Tenant\Users;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;

class PermissionService extends BaseService
{
    public function __construct(private Permission $model) {}

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 10): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $query = $this->getQuery()->with($withRelations);

        // Apply filters if any
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }

        return $query;
    }

    public function index(array $filters = [], array $withRelations = [], ?int $perPage = null)
    {
        $query = $this->queryGet(filters: $filters, withRelations: $withRelations);

        if ($perPage) {
            return $query->paginate($perPage);
        }

        return $query->get();
    }

    public function getGroupedPermissions()
    {
        $permissions = Permission::query()->get();

        // Group by 'group' field
        $groupedPermissions = $permissions->groupBy('group');

        return $groupedPermissions;
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['group_name']) && $filters['group_name']) {
            $query->where('group',  $filters['group_name']);
        }
        return $query;
    }
}
