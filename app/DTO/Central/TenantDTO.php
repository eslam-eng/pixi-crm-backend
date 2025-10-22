<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use App\Enums\Central\ActivationStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TenantDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public bool $is_active = ActivationStatusEnum::ACTIVE->value
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: Arr::get($data, 'name'),
            slug: Arr::get($data, 'slug'),
            is_active: Arr::get($data, 'is_active', ActivationStatusEnum::ACTIVE->value)
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->name,
            slug: $request->slug,
            is_active: $request->is_active
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'is_active' => $this->is_active ?? ActivationStatusEnum::ACTIVE->value,
        ];
    }
}
