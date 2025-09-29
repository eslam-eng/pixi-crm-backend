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
            'partial_amount_paid' => $this->partial_amount_paid,
            'amount_due' => $this->amount_due,
            'approval_status' => $this->approval_status,
            'created_by_id' => $this->created_by_id,

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

            'assigned_to' => $this->whenLoaded('assigned_to', function () {
                return [
                    'id' => $this->assigned_to->id,
                    'name' => $this->assigned_to->name,
                    'email' => $this->assigned_to->email,
                ];
            }),

            'items' => $this->whenLoaded('deal_items', function () {
                return $this->deal_items->map(function ($dealItem) {
                    return [
                        'id' => $dealItem->item_id,
                        'name' => $dealItem->item->name ?? 'Unknown Item',
                        'price' => $dealItem->price,
                        'quantity' => $dealItem->quantity,
                        'total' => $dealItem->total,
                        'subscription' => $this->when(
                            $dealItem->subscription && $dealItem->subscription->exists,
                            function () use ($dealItem) {
                                return [
                                    'id' => $dealItem->subscription->id,
                                    'start_at' => $dealItem->subscription->start_at->format('Y-m-d'),
                                    'end_at' => $dealItem->subscription->end_at->format('Y-m-d'),
                                    'billing_cycle' => $dealItem->subscription->billing_cycle->value,
                                    // 'billing_cycle_label' => $dealItem->subscription->billing_cycle->getLabel(),
                                    // 'is_active' => $dealItem->subscription->isActive(),
                                    // 'is_expired' => $dealItem->subscription->isExpired(),
                                    // 'duration_in_days' => $dealItem->subscription->getDurationInDays(),
                                    // 'next_billing_date' => $dealItem->subscription->getNextBillingDate()?->format('Y-m-d'),
                                ];
                            }
                        ),
                    ];
                });
            }),

            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->filter()->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->product->item->name ?? 'Unknown Variant',
                        'price' => $variant->pivot->price ?? 0,
                        'quantity' => $variant->pivot->quantity ?? 0,
                        'total' => $variant->pivot->total ?? 0,
                    ];
                });
            }),

            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->filter()->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'name' => $attachment->name ?? 'Unknown Attachment',
                        'file_type' => $attachment->file_type ?? '',
                        'file_size' => $attachment->file_size ?? 0,
                        'file_url' => $attachment->file_url ?? '',
                        'thumbnail_url' => $attachment->thumbnail_url ?? '',
                        'preview_url' => $attachment->preview_url ?? '',
                        'created_at' => $attachment->created_at,
                    ];
                });
            }),

            'payments' => $this->whenLoaded('payments', function () {
                return $this->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'pay_date' => $payment->pay_date,
                        'payment_method' =>  $payment->payment_method->name,
                    ];
                });
            }),
        ];
    }
}
