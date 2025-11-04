<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SourcePayoutItemsResource extends JsonResource
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
            'activation_code' => ActivationCodeResource::make($this->whenLoaded('activationCode')),
            'collected_at' => $this->collected_at,
            'status' => $this->isCollected(),
            'payout_amount' => $this->payout_amount,
        ];
    }
}
