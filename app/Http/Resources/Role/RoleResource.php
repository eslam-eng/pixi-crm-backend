<?php

namespace App\Http\Resources\Role;

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
            'description' => $this->description,
            'is_active' => $this->is_active->value,
            'is_active_text' => $this->is_active->getLabel(),
            'permissions_count' => $this->whenCounted('permissions'),
            'users_count' => $this->whenCounted('users'),
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions
                    ->groupBy('group')
                    ->map(fn($groupPermissions) => PermissionResource::collection($groupPermissions))
                    ->toArray();
            }),
        ];
    }
}
