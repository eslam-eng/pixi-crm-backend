<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use App\Enum\ActivationStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CurrencyDTO extends BaseDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public string $symbol,
        public int $decimal_places,
        public bool $is_active = ActivationStatusEnum::ACTIVE->value,
        public bool $is_default = false
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            code: Arr::get($data, 'code'),
            name: Arr::get($data, 'name'),
            symbol: Arr::get($data, 'symbol'),
            decimal_places: Arr::get($data, 'decimal_places'),
            is_active: Arr::get($data, 'is_active', ActivationStatusEnum::ACTIVE->value),
            is_default: Arr::get($data, 'is_default', ActivationStatusEnum::INACTIVE->value)
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            code: $request->code,
            name: $request->name,
            symbol: $request->symbol,
            decimal_places: $request->decimal_places,
            is_active: $request->is_active,
            is_default: $request->is_default
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'decimal_places' => $this->decimal_places,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
        ];
    }
}
