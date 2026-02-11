<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceShowResource extends JsonResource
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
            'bill_to' => [
                'client_name' => $this->tenant->name ?? null,
                'email' => $this->tenant->owner->email ?? null,
            ],
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'description' => $item->description,
                    'payment_cycle' => $this->subscription->billing_cycle?->getLabel() ?? 'N/A',
                    'qty' => $item->quantity,
                    'rate' => (float) $item->unit_price,
                    'amount' => (float) $item->total,
                ];
            }),
            'summary' => [
                'subtotal' => (float) $this->subtotal,
                'tax_rate' => 0.0, // Placeholder or fetch if exists
                'tax_amount' => (float) $this->tax_amount,
                'total' => (float) $this->total,
                'currency' => $this->currency,
            ],
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
            'paid_at' => $this->paid_at?->format('M d, Y H:i'),
            'notes' => $this->notes,
        ];
    }
}
