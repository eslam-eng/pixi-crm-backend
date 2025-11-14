<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
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
            'users_count' => $this->users_count,

            'email' => $this->when(
                $this->relationLoaded('owner'),
                fn () => $this->owner?->email,
            ),
            'subscription' => SubscriptionResource::make($this->whenLoaded('activeSubscription')),
            'status' => $this->status->value,
            'status_text' => $this->status->getLabel(),
        ];
    }
}
