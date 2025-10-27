<?php

namespace App\Http\Resources\Central;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\Landlord\FeatureGroupEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $feature_subscription_pivot_loaded = $this->hasPivotLoaded('feature_subscriptions');
        $feature_plan_pivot_loaded = $this->hasPivotLoaded('feature_plans');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->getTranslatedFallback('description'),
            'group' => $this->group,
            'group_text' => FeatureGroupEnum::from($this->group)->getLabel(),
            'is_active' => $this->is_active,
            'is_active_text' => ActivationStatusEnum::from($this->is_active)->getLabel(),
            'value' => $feature_subscription_pivot_loaded || $feature_plan_pivot_loaded ? $this->pivot->value : null,
            'usage' => $feature_subscription_pivot_loaded ? $this->pivot->usage : null,
        ];
    }
}
