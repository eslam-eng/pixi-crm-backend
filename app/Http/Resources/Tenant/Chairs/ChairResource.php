<?php

namespace App\Http\Resources\Tenant\Chairs;

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
            'ended_at' => $this->ended_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'monthly_targets' => ChairMonthlyTargetResource::collection($this->whenLoaded('monthlyTargets')),
            'Quarterly_targets' => ChairQuarterlyTargetResource::collection($this->whenLoaded('quarterlyTargets')),
        ];
    }
}
