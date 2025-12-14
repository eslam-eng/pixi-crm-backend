<?php

namespace App\Http\Resources\Tenant\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // dd($this);
        return [
            'id' => $this->id,
            'lang' => $this->lang,
            'image' => $this->image,
            'first' => $this->first_name,
            'last' => $this->last_name,
            'full_name' => "$this->first_name $this->last_name",
            'role' => $this->whenLoaded('roles', fn() => $this->roles->first()?->name),
            'email' => $this->email,
            'job_title' => $this->job_title,
            'phone' => $this->phone,
            'department' => $this->department?->localized_name,
            'last_login_at' => $this->last_login_at?->translatedFormat('Y-m-d g:i a'),
            'created_at' => $this->created_at?->translatedFormat('Y-m-d g:i a'),
            'is_active' => $this->is_active ? 'active' : 'inactive',
            'permissions' => $this->whenLoaded('roles', fn() => $this->roles->first()?->permissions->pluck('name')),
        ];
    }
}
