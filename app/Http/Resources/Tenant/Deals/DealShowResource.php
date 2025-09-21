<?php

namespace App\Http\Resources\Tenant\Deals;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealShowResource extends JsonResource
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
            'contact_id' => $this->contact_id,
            'sale_date' => $this->sale_date,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'tax_rate' => $this->tax_rate,
            'payment_status' => $this->payment_status,
            'payment_method_id' => $this->payment_method_id,
            'notes' => $this->notes,
            'assigned_to_id' => $this->assigned_to_id,
            'stage_id' => $this->stage_id,
            'total_amount' => $this->total_amount,
            'partial_amount_paid' => $this->partial_amount_paid,
            'partial_amount_due' => $this->partial_amount_due,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'contact' => $this->whenLoaded('contact', function () {
                return [
                    'id' => $this->contact->id,
                    'name' => $this->contact->name,
                    'first_name' => $this->contact->first_name,
                    'last_name' => $this->contact->last_name,
                    'email' => $this->contact->email,
                    'company_name' => $this->contact->company_name,
                ];
            }),
            
            'stage' => $this->whenLoaded('stage', function () {
                return [
                    'id' => $this->stage->id,
                    'name' => $this->stage->name,
                    'pipeline' => $this->whenLoaded('stage.pipeline', function () {
                        return [
                            'id' => $this->stage->pipeline->id,
                            'name' => $this->stage->pipeline->name,
                        ];
                    }),
                ];
            }),
            
            'assigned_to' => $this->whenLoaded('assigned_to', function () {
                return [
                    'id' => $this->assigned_to->id,
                    'name' => $this->assigned_to->name,
                    'email' => $this->assigned_to->email,
                ];
            }),
            
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'price' => $item->pivot->price,
                        'quantity' => $item->pivot->quantity,
                        'total' => $item->pivot->total,
                    ];
                });
            }),
            
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'name' => $attachment->name,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'file_url' => $attachment->file_url,
                        'thumbnail_url' => $attachment->thumbnail_url,
                        'preview_url' => $attachment->preview_url,
                        'created_at' => $attachment->created_at,
                    ];
                });
            }),
        ];
    }
}