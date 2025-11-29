<?php

namespace App\Http\Resources\Central;

use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->is_active->value,
            'is_active_text' => $this->is_active->getLabel(),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
        ];
    }
}
