<?php

namespace App\Http\Resources\Tenant\Users;

use App\Http\Resources\Tenant\Chairs\ChairResource;
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
class UserShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'department' => $this->whenLoaded('department', function () {
                return [
                        'id' => $this->department?->id,
                        'name' => $this->department?->name
                ];
            }),
            'is_active' => $this->is_active,
            'role' => $this->whenLoaded('roles', function () {
                return [
                        'id' => $this->roles->first()?->id,
                        'name' => $this->roles->first()?->name
                ];
            }),
            'activeIndividualChair' => $this->whenLoaded('activeIndividualChair', new ChairResource($this->activeIndividualChair)),
        ];
    }
}
