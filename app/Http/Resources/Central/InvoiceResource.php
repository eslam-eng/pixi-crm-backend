<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'invoice_number' => $this->invoice_number,
            'client' => [
                'id' => $this->tenant_id,
                'name' => $this->tenant->name ?? null,
            ],
            'subscription' => [
                'id' => $this->subscription_id,
                'plan_name' => $this->subscription->plan_name ?? 'N/A',
            ],
            'amount' => (float) $this->total,
            'currency' => $this->currency,
            'status' => [
                'value' => $this->status?->value,
                'label' => $this->status?->getLabel(),
            ],
            'payment_method' => [
                'value' => $this->payment_method?->value,
                'label' => $this->payment_method?->getLabel(),
            ],
            'billing_date' => $this->created_at?->format('M d, Y'),
            'due_date' => $this->due_date?->format('M d, Y'),
            'notes' => $this->notes,
        ];
    }
}
