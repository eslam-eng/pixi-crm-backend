<?php

namespace App\Http\Resources\Tenant\Users;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $roles
 */
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
            'name' => $this->name,
            'email' => $this->email,
            'department' => $this->department?->localized_name,
            'last_login_at' => $this->last_login_at?->translatedFormat('Y-m-d g:i a'),
            'is_active' => $this->is_active,
            'lang' => $this->lang,
            'role' => $this->whenLoaded('roles', fn() => $this->roles->first()?->name),
        ];
    }
}
