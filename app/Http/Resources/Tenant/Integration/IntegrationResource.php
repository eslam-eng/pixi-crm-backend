<?php

namespace App\Http\Resources\Tenant\Integration;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationResource extends JsonResource
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
            'platform' => $this->platform,
            'status' => $this->status->value,
            'last_sync' => $this->last_sync ? $this->last_sync->format('n/j/Y') : null,
            'enabled' => $this->is_active,
        ];
    }
}
