<?php

namespace App\Http\Resources\Tenant\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriorityResource extends JsonResource
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
            'color' => new PriorityColorResource($this->color),
            'level' => $this->level,
            'is_default' => $this->is_default,

        ];
    }
}
