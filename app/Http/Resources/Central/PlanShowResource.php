<?php

namespace App\Http\Resources\Central;

use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $billing_cycle = $request->input('billing_cycle', SubscriptionBillingCycleEnum::MONTHLY->value);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'monthly_price' => $this->monthly_price,
            'annual_price' => $this->annual_price,
            'lifetime_price' => $this->lifetime_price,
            'refund_days' => $this->refund_days,
            'is_active' => $this->is_active,
            'features' => FeatureResource::collection($this->whenLoaded('addonFeatures')),
            'limits' => LimitResource::collection($this->whenLoaded('limitFeatures')),
        ];
    }
}
