<?php

namespace App\Services\Landlord;

use App\Models\Landlord\Role;
use App\Services\BaseRoleService;
use Illuminate\Database\Eloquent\Builder;

class RoleService extends BaseRoleService
{
    protected function getFilterClass(): ?string
    {
        return null;
    }

    protected function baseQuery(): Builder
    {
        return Role::query();
    }

    public function getRolesNameByIds(?array $role_ids = [])
    {
        return $this->baseQuery()->whereIn('id', $role_ids)->pluck('name')->toArray();
    }
}
