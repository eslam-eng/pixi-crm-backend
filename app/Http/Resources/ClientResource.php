<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'company_name' => $this->company_name,
            'subdomain' => $this->subdomain,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'package' => $this->whenLoaded('package', fn() => new TierResource($this->package)),
            'subscription' => '',
            'status' => $this->status,
        ];
    }
}
