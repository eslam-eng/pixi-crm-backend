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
        public float $price,
        public string $duration_unit,
        public int $duration,
        public int $refund_period,
        public bool $is_active = true,
        public ?array $features = [],
        public ?array $limits = []
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: Arr::get($data, 'name'),
            description: Arr::get($data, 'description'),
            price: Arr::get($data, 'price'),
            duration_unit: Arr::get($data, 'duration_unit'),
            duration: Arr::get($data, 'duration'),
            refund_period: Arr::get($data, 'refund_period'),
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
            price: $request->price,
            duration_unit: $request->duration_unit,
            duration: $request->duration,
            refund_period: $request->refund_period,
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
            'price' => $this->price,
            'duration_unit' => $this->duration_unit,
            'duration' => $this->duration,
            'refund_period' => $this->refund_period,
            'is_active' => $this->is_active,
            'features' => $this->features,
            'limits' => $this->limits,
        ];
    }
}
