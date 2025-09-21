<?php

namespace App\Http\Resources\Tenant\Stage;

use App\Http\Resources\Opportunity\OpportunityResource;
use App\Http\Resources\PipelineResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StageWithOpportunityResource extends JsonResource
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
            'probability' => $this->probability,
            'seq_number' => $this->seq_number,
            'pipeline_id' => $this->pipeline_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pipeline' => $this->whenLoaded('pipeline', fn() => new PipelineResource($this->pipeline)),
            'leads' => OpportunityResource::collection($this->leads),
        ];
    }
}
