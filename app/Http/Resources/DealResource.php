<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
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
            'deal_type' => $this->deal_type,
            'deal_name' => $this->deal_name,
            'lead_id' => $this->lead_id,
            'sale_date' => $this->sale_date,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'tax_rate' => $this->tax_rate,
            'payment_status' => $this->payment_status,
            'payment_method_id' => $this->payment_method_id,
            'notes' => $this->notes,
            'assigned_to_id' => $this->assigned_to_id,
            'total_amount' => $this->total_amount,
            // Map over related items, include pivot data
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(fn($item) => [
                    'item_id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'type' => $item->type,
                    'quantity' => $item->pivot->quantity,
                    'price' => $item->pivot->price,
                    'total' => $item->pivot->total,
                ]);
            }),
        ];
    }
}
