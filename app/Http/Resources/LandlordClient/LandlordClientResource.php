<?php

namespace App\Http\Resources\LandlordClient;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $stages
 */
class LandlordClientResource extends JsonResource
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
            'tiers' => $this->whenLoaded('tiers', fn() => $this->tiers->map(fn($tier) => [
                'id' => $tier->id,
                'name' => $tier->name,
                'price' => $tier->price,
                'description' => $tier->description,
            ])),
            'roles' => $this->whenLoaded('roles', fn() => $this->roles->pluck('name')->toArray()), // ✅ Only permission names
            'permissions' => $this->whenLoaded('permissions', fn() => $this->permissions->pluck('name')->toArray()), // ✅ Only permission names
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
