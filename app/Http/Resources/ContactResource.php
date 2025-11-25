<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => "$this->first_name $this->last_name",
            'job_title' => $this->job_title,
            'email' => $this->email,
            'contact_phone' => $this->whenLoaded('contactPhones', fn() => $this->contactPhones[0]->phone ?? null),
            'company_name' => $this->company_name,
            'status' => $this->status,
            'source' => $this->whenLoaded('source', fn() => $this->source->name),
            'user' => $this->whenLoaded('user', fn() => $this->user->first_name . ' ' . $this->user->last_name),
        ];
    }
}
