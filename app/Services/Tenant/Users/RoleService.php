<?php

namespace App\Services\Tenant\Users;

use App\DTO\Tenant\Role\RoleDTO;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class RoleService extends BaseService
{
    public function __construct(private Role $model) {}

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

    /**
     * Apply filters to the query
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['name']) && $filters['name']) {
            $query->where('name', 'LIKE', '%' . $filters['name'] . '%');
        }

        if (isset($filters['guard_name']) && $filters['guard_name']) {
            $query->where('guard_name', $filters['guard_name']);
        }

        if (isset($filters['created_at_from']) && $filters['created_at_from']) {
            $query->whereDate('created_at', '>=', $filters['created_at_from']);
        }

        if (isset($filters['created_at_to']) && $filters['created_at_to']) {
            $query->whereDate('created_at', '<=', $filters['created_at_to']);
        }

        return $query;
    }

    public function create(RoleDTO $dto)
    {
        $role = $this->model->create($dto->toArray());
        if ($dto->permissions) {
            $role->syncPermissions($dto->permissions);
        }
        return $role;
    }

    public function update(int $role_id, RoleDTO $dto)
    {
        $role = $this->findById($role_id);

        if ($role->is_system) {
            return throw new \Exception('Cannot modify system roles');
        }
        $role->update($dto->toArray());
        if ($dto->permissions) {
            $role->syncPermissions($dto->permissions);
        }
        return $role;
    }

    public function destroy(int $role_id)
    {
        $role = $this->findById($role_id);
        if ($role->is_system) {
            return throw new \Exception('Cannot delete system roles');
        }

        if ($role->users()->count() > 0) {
            return throw new \Exception('Cannot delete role with assigned users');
        }
        return $role->delete();
    }
}
