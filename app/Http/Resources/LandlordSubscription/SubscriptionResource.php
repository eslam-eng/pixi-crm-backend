<?php

namespace App\Http\Resources\LandlordSubscription;

use App\Http\Resources\ClientResource;
use App\Http\Resources\TierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'client' => $this->whenLoaded('client', fn() => new ClientResource($this->client)),
            'package' => $this->whenLoaded('tier', fn() => new TierResource($this->tier)),
            'subscription_start_date' => $this->subscription_start_date,
            'subscription_end_date' => $this->subscription_end_date,
            'subscription_status' => $this->subscription_status,
            'activition_method' => $this->activition_method,
            'source' => $this->source,
            'auto_renew' => $this->auto_renew,
            'payment_status' => $this->payment_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'file' => $this->getFirstMediaUrl(),
            'note' => $this->note,
        ];
    }
}
