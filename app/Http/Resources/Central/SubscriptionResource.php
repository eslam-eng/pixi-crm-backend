<?php

namespace App\Http\Resources\Central;

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
            'subscription_number' => $this->subscription_number,

            'tenant' => $this->relationLoaded('tenant') ? [
                'id' => $this->tenant->id,
                'name' => $this->tenant->name,
            ] : null,

            'plan' => $this->relationLoaded('plan') ? [
                'id' => $this->plan->id,
                'name' => $this->plan->name,
            ] : null,

            'currency' => $this->currency,
            'billing_cycle' => $this->billing_cycle?->value,
            'billing_cycle_text' => $this->billing_cycle?->getLabel(),

            'status' => $this->status?->value,
            'status_text' => $this->status?->getLabel(),

            'amount' => $this->amount,
            'starts_at' => $this->starts_at_formatted,
            'ends_at' => $this->ends_at_formatted,
            'trial_ends_at' => $this->trial_ends_at_formatted,
            'invoices' => InvoiceResource::collection($this->whenLoaded('invoices')),

        ];
    }
}
