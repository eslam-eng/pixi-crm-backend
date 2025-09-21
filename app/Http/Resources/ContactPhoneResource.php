<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactPhoneResource extends JsonResource
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
            'phone' => $this->phone,
            'is_primary' => $this->is_primary,
            'enable_whatsapp' => $this->enable_whatsapp,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
