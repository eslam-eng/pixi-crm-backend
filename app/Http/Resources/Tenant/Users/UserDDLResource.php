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
class UserDDLResource extends JsonResource
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
            'image' => $this->image,
            'name' => $this->name,
            // 'role' => $this->roles?->first()?->name,
            'role' => 'No Role',
            'department' => $this->department?->localized_name,
        ];
    }
}
