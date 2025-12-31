<?php

namespace App\Http\Resources\Central;

use App\Enums\Landlord\SubscriptionBillingCycleEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Termwind\Components\Li;

class PlanResource extends JsonResource
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
            'price' => calculateSubscriptionAmount($this->resource, $billing_cycle),
            'is_active_text' => $this->is_active->getLabel(),
            'features' => FeatureResource::collection($this->whenLoaded('addonFeatures')),
            'limits' => LimitResource::collection($this->whenLoaded('limitFeatures')),
        ];
    }
}
