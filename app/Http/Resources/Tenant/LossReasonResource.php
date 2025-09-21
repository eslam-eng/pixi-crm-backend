<?php

namespace App\Http\Resources\Tenant;

use App\Http\Resources\PipelineResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LossReasonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->value,
            'description' => $this->description,
            'pipeline' => new PipelineResource($this->whenLoaded('pipeline')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
