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
            'leader' => $this->whenLoaded('leader', $this->leader->first_name . ' ' . $this->leader->last_name),
            'sales' => UserResource::collection($this->whenLoaded('members'))->count(),
            'status' => $this->status,
            'is_target' => $this->is_target ? 'Set' : 'Not Set',
            // 'members' => ChairResource::collection($this->whenLoaded('chairs')),
            'period_type' => $this->period_type,
            'year_total' => $this->whenLoaded('year_total', $this->year_total->sum('target_value')),
            'created_at' => $this->created_at,
            
            // 'updated_at' => $this->updated_at,
        ];
    }
}
