<?php

namespace App\Services;

use App\QueryFilters\RoleFilters;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;
use App\DTO\RolePermission\RolePermissionDTO;

class RolePermissionService extends BaseService
{
    public function __construct(
        public Role               $model,
    ) {}

    public function getModel(): Role
    {
        return $this->model;
    }

    public function getAll(array $filters = [])
    {
        return $this->queryGet($filters)->get();
    }

    public function getTableName(): String
    {
        return $this->getModel()->getTable();
    }

    public function listing(array $filters = [], array $withRelations = [], $perPage = 5): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate($perPage);
    }


    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $roles = $this->model->with($withRelations)->orderBy('id', 'desc');
        return (new RoleFilters($filters))->apply($roles);
    }

    public function datatable(array $filters = [], array $withRelations = []): Builder
    {
        $roles = $this->model->with($withRelations)->orderBy('id', 'desc');
        return (new RoleFilters($filters))->apply($roles);
    }



    public function getRolePermissions(Role $role)
    {
        return [
            'role' => $role,
            'permissions' => Permission::all(),
        ];
    }

    public function updateRolePermissions(Role $role, RolePermissionDTO $dto): void
    {
        $permissions = Permission::whereIn('id', $dto->permissions)->pluck('name')->toArray();
        $role->syncPermissions($permissions);
    }

    // Retrieve all permissions
    public function getAllPermissions()
    {
        return Permission::all();
    }
    // Create a new role and assign permissions
    public function createRoleWithPermissions(array $data)
    {
        // Create role
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        // Assign permissions
        if (!empty($data['permissions'])) {
            $permissions = Permission::whereIn('id', $data['permissions'])->get();
            $role->syncPermissions($permissions);
        }
        return $role;
    }
    public function delete(int $id)
    {
        return $this->getQuery()->where('id', $id)->delete();
    }
}
