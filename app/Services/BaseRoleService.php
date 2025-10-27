<?php

namespace App\Services;

use App\DTO\RoleDTO;
use App\Models\Central\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BaseRoleService extends BaseService
{
    protected function getFilterClass(): ?string
    {
        return null;
    }

    protected function baseQuery(): Builder
    {
        return Role::query();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->getQuery($filters)
            ->withCount('permissions')
            ->paginate();
    }

    public function roles(array $filters = []): Collection
    {
        return $this->getQuery($filters)
            ->withCount(['permissions', 'users'])
            ->get();
    }

    /**
     * @throws \Throwable
     */
    public function create(RoleDTO $roleDTO): Builder|Model|Role
    {
        return DB::connection('tenant')->transaction(function () use ($roleDTO) {
            $role = $this->baseQuery()->create($roleDTO->toArray());
            $role->syncPermissions($roleDTO->permissions);

            return $role;
        });
    }

    /**
     * @throws \Throwable
     */
    public function update(Role|int $role, RoleDTO $roleDTO)
    {
        return DB::connection('tenant')->transaction(function () use ($role, $roleDTO) {
            if (is_int($role)) {
                $role = parent::findById($role);
            }
            $role->update($roleDTO->toArray());
            $role->syncPermissions($roleDTO->permissions);
        });
    }

    public function delete(Role|int $role): ?bool
    {
        if (is_int($role) || is_string($role)) {
            $role = parent::findById($role);
        }

        return $role->delete();
    }

    public function getRoleBySlug(string $slug): Model|Builder|null
    {
        return $this->getQuery()->where('name', $slug)->first();
    }
}
