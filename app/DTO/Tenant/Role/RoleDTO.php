<?php

namespace App\DTO\Tenant\Role;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class RoleDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly string $guard_name,
        public readonly ?bool $is_system,
        public readonly ?array $permissions,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            guard_name: $request->input('guard_name'),
            is_system: $request->input('is_system'),
            permissions: $request->input('permissions'),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'guard_name' => $this->guard_name,
            'is_system' => $this->is_system,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: Arr::get($data, 'name'),
            description: Arr::get($data, 'description'),
            guard_name: Arr::get($data, 'guard_name'),
            is_system: Arr::get($data, 'is_system'),
            permissions: Arr::get($data, 'permissions'),
        );
    }
}
