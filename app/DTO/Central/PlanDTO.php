<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PlanDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public float $monthly_price,
        public float $annual_price,
        public float $lifetime_price,
        public int $refund_days,
        public bool $is_active = true,
        public ?array $features = [],
        public ?array $limits = []
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new self(
            name: Arr::get($data, 'name'),
            description: Arr::get($data, 'description'),
            monthly_price: Arr::get($data, 'monthly_price'),
            annual_price: Arr::get($data, 'annual_price'),
            lifetime_price: Arr::get($data, 'lifetime_price'),
            refund_days: Arr::get($data, 'refund_days'),
            is_active: Arr::get($data, 'is_active', true),
            features: Arr::get($data, 'features', []),
            limits: Arr::get($data, 'limits', []),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->name,
            description: $request->description,
            monthly_price: $request->monthly_price,
            annual_price: $request->annual_price,
            lifetime_price: $request->lifetime_price,
            refund_days: $request->refund_days,
            is_active: $request->is_active,
            features: $request->features,
            limits: $request->limits,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'monthly_price' => $this->monthly_price,
            'annual_price' => $this->annual_price,
            'lifetime_price' => $this->lifetime_price,
            'refund_days' => $this->refund_days,
            'is_active' => $this->is_active,
            'features' => $this->features,
            'limits' => $this->limits,
        ];
    }
}
