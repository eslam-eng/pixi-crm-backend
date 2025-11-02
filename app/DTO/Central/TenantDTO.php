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
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            tenant_id: Arr::get($data, 'tenant_id'),
            name: Arr::get($data, 'name'),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            tenant_id: $request->tenant_id,
            name: $request->name,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
        ];
    }
}
