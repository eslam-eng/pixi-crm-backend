<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactDDLResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => "$this->first_name $this->last_name",
            'company_name' => $this->company_name,
            'email' => $this->email,
            'phones' => $this->whenLoaded('contactPhones', fn() => ContactPhoneResource::collection($this->contactPhones)),
        ];
    }
}
