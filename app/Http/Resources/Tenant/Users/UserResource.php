<?php

namespace App\Http\Resources\Tenant\Users;

use App\Http\Resources\TeamDDLResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => "$this->first_name $this->last_name",
            'email' => $this->email,
            'job_title' => $this->job_title,
            // 'target' => $this->target,
            // 'target_type' => $this->target_type,
            'phone' => $this->phone,
            // 'team' => new TeamDDLResource($this->whenLoaded('team')),
            'department' => $this->department?->localized_name,
            'last_login_at' => $this->last_login_at?->translatedFormat('Y-m-d g:i a'),
            'is_active' => $this->is_active ? 'active' : 'inactive',
            // 'lang' => $this->lang,
            'role' => $this->whenLoaded('roles', fn() => $this->roles->first()?->name),
            // 'attendance_status' =>
            // $this->whenLoaded('latestAttendancePunch', fn() => $this->latestAttendancePunch?->type === 'in' ? true : false),
        ];
    }
}
