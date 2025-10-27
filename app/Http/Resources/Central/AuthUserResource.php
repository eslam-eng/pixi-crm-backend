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
            // 'locale' => $this->locale->value,
            // 'locale_text' => $this->locale->getLabel(),
            'phone' => $this->phone,
            'belongs_to' => 'LANDLORD',
            'role' => 'role',
            'permissions' => [],

        ];
    }
}
