<?php

namespace App\Http\Resources\Tenant\Users;

use App\Http\Resources\TeamResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'job_title' => $this->job_title,
            'target' => $this->target,
            'target_type' => $this->target_type,
            'phone' => $this->phone,
            'team' => new TeamResource($this->whenLoaded('team')),
            'department' => $this->department?->localized_name,
            'last_login_at' => $this->last_login_at?->translatedFormat('Y-m-d g:i a'),
            'is_active' => $this->is_active,
            'lang' => $this->lang,
            'role' => $this->whenLoaded('roles', fn() => $this->roles->first()?->name),
        ];
    }
}
