<?php

namespace App\Http\Resources;

use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientShowResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $area = $this->whenLoaded('area');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'city' => new LocationResource($area?->getCityAncestor()),
            'area' => $this->whenLoaded('area', fn() => new LocationResource($this->area)),
            'source' => $this->whenLoaded('source', fn() => new SourceResource($this->source)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
