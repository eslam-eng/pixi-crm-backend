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
            'client' => [
                'id' => $this->tenant_id,
                'name' => $this->tenant->name ?? null,
            ],
            'package' => $this->plan_name,
            'activation' => [
                'value' => $this->activation_method?->value,
                'label' => $this->activation_method?->getLabel(),
            ],
            'source' => $this->activationCode->source->name ?? $this->source->name ?? null,
            'start_date' => $this->starts_at?->format('M d, Y'),
            'end_date' => $this->ends_at?->format('M d, Y'),
            'status' => [
                'value' => $this->status?->value,
                'label' => $this->status?->getLabel(),
            ],
            'auto_renew' => (bool) $this->auto_renew,
            'payment' => [
                'value' => $this->payment_status?->value,
                'label' => $this->payment_status?->getLabel(),
            ],
            'last_updated' => $this->updated_at?->format('M d, Y'),
        ];
    }
}
