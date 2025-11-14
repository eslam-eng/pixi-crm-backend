<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantDetailsResource extends JsonResource
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

            'active_subscription' => SubscriptionResource::make($this->whenLoaded('activeSubscription')),

            'feature_usage' => $this->when(
                $this->relationLoaded('activeSubscription') &&
                $this->activeSubscription?->relationLoaded('features'),
                fn () => FeatureResource::collection($this->activeSubscription->features)
            ),

            'subscriptions' => SubscriptionResource::collection($this->whenLoaded('subscriptions')),

            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),
            //
            //            'status' => $this->status->value,
            //
            //            'status_text' => $this->status->getLabel(),
        ];
    }
}
