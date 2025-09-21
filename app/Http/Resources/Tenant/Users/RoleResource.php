<?php

namespace App\Http\Resources\Tenant\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'display_name' => ucfirst(str_replace('_', ' ', $this->name)),
            'guard_name' => $this->guard_name,
            'permissions_count' => $this->permissions_count ?? $this->permissions->count(),
            'users_count' => $this->users_count ?? $this->users->count(),
        ];
    }
}
