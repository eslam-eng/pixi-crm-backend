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
        $isTenantLoaded = $this->relationLoaded('tenant');

        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,

            'tenant' => $isTenantLoaded ? [
                'id' => $this->tenant->id,
                'name' => $this->tenant->name,
            ] : null,

            'tenant_owner' => $isTenantLoaded && $this->tenant->relationLoaded('owner') ? [
                'id' => $this->tenant->owner?->id,
                'name' => $this->tenant->owner?->name,
                'email' => $this->tenant->owner?->email,
            ] : null,

            'discount_percentage' => $this->discount_percentage,

            'subtotal' => $this->subtotal,

            'payment_method' => $this->payment_method?->value,

            'payment_method_text' => $this->payment_method?->getLabel(),

            'payment_reference' => $this->payment_reference,

            'total' => $this->total,

            'currency' => $this->currency,

            'status' => $this->status?->value,

            'status_text' => $this->status?->getLabel(),

            'starts_at' => $this->starts_at,

            'due_date' => $this->due_date,

            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),

        ];
    }
}
