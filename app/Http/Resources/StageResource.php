<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class StageResource extends JsonResource
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
        ];
    }
}
