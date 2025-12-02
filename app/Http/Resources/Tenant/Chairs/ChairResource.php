<?php

namespace App\Http\Resources\Tenant\Chairs;

use App\Http\Resources\TeamResource;
use App\Http\Resources\Tenant\Deals\DealShowResource;
use App\Http\Resources\Tenant\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChairResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'started_at' => $this->started_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'team' => TeamResource::make($this->whenLoaded('team')),
            'period_type' => $this->whenLoaded('targets')?->first()?->period_type,
            'targets' => ChairTargetResource::collection($this->whenLoaded('targets')),
            'deals' => DealShowResource::collection($this->whenLoaded('deals')),
        ];
    }
}
