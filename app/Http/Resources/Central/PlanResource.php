<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'currency' => $this->currency,
            'monthly_price' => $this->monthly_price,
            'annual_price' => $this->annual_price,
            'lifetime_price' => $this->lifetime_price,
            'is_active' => $this->is_active,
            'is_active_text' => $this->is_active->getLabel(),
            'trial_days' => $this->trial_days,
            'refund_days' => $this->refund_days,
            'limits' => FeatureResource::collection($this->whenLoaded('limitFeatures')),
            'features' => FeatureResource::collection($this->whenLoaded('addonFeatures')),
        ];
    }
}
