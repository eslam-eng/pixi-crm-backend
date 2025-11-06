<?php

namespace App\Http\Resources;

use App\Http\Resources\Tenant\Chairs\ChairResource;
use App\Http\Resources\Tenant\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'leader' => new UserResource($this->whenLoaded('leader')),
            'sales' => UserResource::collection($this->whenLoaded('members')),
            'members' => ChairResource::collection($this->whenLoaded('chairs')),
            'is_target' => $this->is_target ? 'have team targets' : 'don\'t have team targets',
            'period_type' => $this->period_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
