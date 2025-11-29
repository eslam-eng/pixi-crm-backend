<?php

namespace App\DTO\Central;

use App\DTO\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PlanDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public float $monthly_price,
        public float $annual_price,
        public float $lifetime_price,
        public string $currency_code,
        public ?string $description = null,
        public bool $is_active = true,
        public int $trial_days = 0,
        public int $monthly_credit_tokens = 0,
        public ?int $refund_days = null,
        public ?array $features = [],
        public ?array $limits = []
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            name: Arr::get($data, 'name'),
            monthly_price: Arr::get($data, 'monthly_price'),
            annual_price: Arr::get($data, 'annual_price'),
            lifetime_price: Arr::get($data, 'lifetime_price'),
            currency_code: Arr::get($data, 'currency_code'),
            description: Arr::get($data, 'description'),
            is_active: Arr::get($data, 'is_active', true),
            trial_days: Arr::get($data, 'trial_days', 0),
            monthly_credit_tokens: Arr::get($data, 'monthly_credit_tokens', 0),
            refund_days: Arr::get($data, 'refund_days'),
            features: Arr::get($data, 'features', []),
            limits: Arr::get($data, 'limits', []),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->name,
            monthly_price: $request->monthly_price,
            annual_price: $request->annual_price,
            lifetime_price: $request->lifetime_price,
            currency_code: $request->currency_code,
            description: $request->description,
            is_active: $request->is_active,
            trial_days: $request->trial_days,
            monthly_credit_tokens: $request->monthly_credit_tokens ?? 0,
            refund_days: $request->refund_days ?? 0,
            features: $request->features,
            limits: $request->limits,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'monthly_price' => $this->monthly_price,
            'annual_price' => $this->annual_price,
            'lifetime_price' => $this->lifetime_price,
            'currency_code' => $this->currency_code,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'trial_days' => $this->trial_days,
            'monthly_credit_tokens' => $this->monthly_credit_tokens,
            'refund_days' => $this->refund_days ?? 0,
            'features' => $this->features,
            'limits' => $this->limits,
        ];
    }
}
