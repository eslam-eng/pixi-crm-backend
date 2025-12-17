<?php

namespace App\Http\Resources\Central;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminShowResource extends JsonResource
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
            'department' => $this->department?->name,
            'department_id' => $this->department_id,
            'job_title' => $this->job_title,
            'is_active' => $this->is_active->value,
            'is_active_text' => $this->is_active->getLabel(),
            'role' => RoleResource::make($this->roles->first()),
        ];
    }
}
