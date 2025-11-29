<?php

namespace App\Http\Resources\Tenant\Users;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDDLResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image,
            'name' => $this->name,
            'role' => $this->roles?->first()?->name,
            // 'role' => 'No Role',
            'department' => $this->department?->localized_name,
        ];
    }
}
