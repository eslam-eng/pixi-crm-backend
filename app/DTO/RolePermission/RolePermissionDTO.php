<?php
namespace App\DTO\RolePermission;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class RolePermissionDTO extends BaseDTO
{
    public function __construct(
        public int $roleId,
        public array $permissions
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            roleId: $request->route('role')->id,
            permissions: $request->permissions ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'role_id' => $this->roleId,
            'permissions' => $this->permissions,
        ];
    }

    public static function fromArray(array $data): BaseDTO
    {
        return new self(
            roleId: Arr::get($data, 'role_id'),
            permissions: Arr::get($data, 'permissions', [])
        );
    }
}
