<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $roles
 */
class TeamResource extends JsonResource
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
            'title' => $this->title,
            'leader' => $this->whenLoaded('leader', fn() =>  new UserResource($this->leader)),
            'source' => $this->whenLoaded('source', fn() => new SourceResource($this->source)),
            'location' => $this->whenLoaded('location', fn() =>  new LocationResource($this->location)),
            'sales' => $this->whenLoaded('sales', fn() => new UserCollection($this->sales)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
