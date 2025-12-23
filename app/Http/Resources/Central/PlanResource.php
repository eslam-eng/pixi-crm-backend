<?php

namespace App\Http\Resources\Central;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_unit' => $this->duration_unit,
            'duration' => $this->duration,
            'is_active_text' => $this->is_active->getLabel(),
            'features' => FeatureResource::collection($this->whenLoaded('addonFeatures')),
            'limits' => LimitResource::collection($this->whenLoaded('limitFeatures')),
        ];
    }
}
