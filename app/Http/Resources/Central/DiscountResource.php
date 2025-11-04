<?php

namespace App\Http\Resources\Central;

use App\Enums\Landlord\DiscountTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'discount_code' => $this->discount_code,
            'discount_type' => DiscountTypeEnum::PERCENTAGE->value,
            'discount_type_label' => DiscountTypeEnum::PERCENTAGE->getLabel(),
            'discount_percentage' => (float) $this->discount_percentage,
            'users_limit' => $this->users_limit,
            'usage_limit' => $this->usage_limit,
            'expires_at' => $this->expires_at,
            'status' => $this->status->value,
            'status_label' => $this->status->getLabel(),
            'is_expired' => $this->isExpired(),
            'is_active' => $this->isActive(),
            'plan' => $this->whenLoaded('plan', fn() => [
                'id' => $this->plan->id,
                'name' => $this->plan->name,
            ]),
        ];
    }
}
