<?php

namespace App\Http\Resources;

use App\Models\Tier;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivationCodeResource extends JsonResource
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
            'code' => $this->code,
            'package' => $this->whenLoaded('tier', fn() => new TierResource($this->tier)),
            'status' => $this->status,
            'create_by' => $this->whenLoaded('createBy', fn() => new UserResource($this->createBy)),
            'used_at' => $this->used_at,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
