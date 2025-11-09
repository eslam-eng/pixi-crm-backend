<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use App\Enums\Landlord\ActivationStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RoleDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $guard_name = 'landlord',
        public ?string $description = null,
        public array $permissions = [],
        public bool $is_active = ActivationStatusEnum::ACTIVE->value,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->name,
            description: $request->description,
            permissions: $request->get('permissions', []),
            is_active: $request->get('is_active'),
        );
    }

    /**
     * @return $this
     */
    public static function fromArray(array $data): static
    {
        return new self(
            name: Arr::get($data, 'name'),
            description: Arr::get($data, 'description'),
            permissions: Arr::get($data, 'permissions'),
            is_active: Arr::get($data, 'is_active', ActivationStatusEnum::ACTIVE->value),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'guard_name' => $this->guard_name,
            'is_active' => $this->is_active,
        ];
    }
}
