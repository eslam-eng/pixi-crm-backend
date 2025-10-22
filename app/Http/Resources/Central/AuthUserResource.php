<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
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
            'locale' => $this->locale,
            'tenant_id' => $this->tenant_id,
            'tenant_name' => $this->tenant?->name,
            'tenant_slug' => $this->tenant?->slug,
            'is_verified' => isset($this->email_verified_at),
            'belongs_to' => 'TENANT',
            'role' => 'role',
            'permissions' => [],
        ];
    }
}
