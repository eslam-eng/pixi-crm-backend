<?php

namespace App\DTO\Central;

use App\Enum\ActivationStatusEnum;
use App\Enum\DiscountTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DiscountCodeDTO
{
    public function __construct(
        public string $discountCode,
        public int $planId,
        public string $discountType,
        public float $discountPercentage,
        public ?int $usersLimit,
        public ?int $usageLimit,
        public ?string $expires_at = null,
        public string $status = ActivationStatusEnum::ACTIVE->value,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            discountCode: Arr::get($data, 'discount_code'),
            planId: Arr::get($data, 'plan_id'),
            discountType: Arr::get($data, 'discount_type', DiscountTypeEnum::PERCENTAGE->value),
            discountPercentage: (float) Arr::get($data, 'discount_percentage'),
            usersLimit: Arr::get($data, 'users_limit'),
            usageLimit: Arr::get($data, 'usage_limit'),
            expires_at: Arr::get($data, 'expires_at'),
            status: Arr::get($data, 'status', ActivationStatusEnum::ACTIVE->value),
        );
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            discountCode: $request->discount_code,
            planId: $request->plan_id,
            discountType: $request->discount_type ?? DiscountTypeEnum::PERCENTAGE->value,
            discountPercentage: (float) $request->discount_percentage,
            usersLimit: $request->users_limit,
            usageLimit: $request->usage_limit,
            expires_at: $request->expires_at,
            status: $request->status ?? ActivationStatusEnum::ACTIVE->value,
        );
    }

    public function toArray(): array
    {
        return [
            'discount_code' => $this->discountCode,
            'plan_id' => $this->planId,
            'discount_type' => $this->discountType ?? DiscountTypeEnum::PERCENTAGE->value,
            'discount_percentage' => $this->discountPercentage,
            'users_limit' => $this->usersLimit,
            'usage_limit' => $this->usageLimit,
            'expires_at' => $this->expires_at,
            'status' => $this->status,
        ];
    }
}
