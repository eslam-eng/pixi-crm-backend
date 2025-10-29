<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use App\Enums\Landlord\ActivationStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TenantDTO extends BaseDTO
{
    public function __construct(
        public string $tenant_id,
        public string $name,
        public ?string $tenancy_db_name = null,
        public bool $is_active = ActivationStatusEnum::ACTIVE->value
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            tenant_id: Arr::get($data, 'tenant_id'),
            name: Arr::get($data, 'name'),
            tenancy_db_name: Arr::get($data, 'tenancy_db_name'),
            is_active: Arr::get($data, 'is_active', ActivationStatusEnum::ACTIVE->value)
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            tenant_id: $request->tenant_id,
            name: $request->name,
            tenancy_db_name: $request->tenancy_db_name,
            is_active: $request->is_active,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'tenancy_db_name' => $this->tenancy_db_name,
            'is_active' => $this->is_active ?? ActivationStatusEnum::ACTIVE->value,
        ];
    }
}
