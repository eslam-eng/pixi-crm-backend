<?php

namespace App\Http\Resources;

use App\Http\Resources\Tenant\Chairs\ChairResource;
use App\Http\Resources\Tenant\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


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
            'chairs' => ChairResource::collection($this->whenLoaded('chairs')),
            'leader' => new UserResource($this->whenLoaded('leader')),
            'sales' => UserResource::collection($this->whenLoaded('sales')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
