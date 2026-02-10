<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SubscriptionShowResource extends JsonResource
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
                'owner' => [
                    'id' => $this->tenant->owner->id ?? null,
                    'name' => $this->tenant->owner->full_name ?? null,
                    'email' => $this->tenant->owner->email ?? null,
                ],
            ],
            'plan' => [
                'id' => $this->plan_id,
                'name' => $this->plan_name,
                'snapshot' => $this->plan_snapshot,
            ],
            'activation' => [
                'method' => $this->activation_method?->value,
                'method_label' => $this->activation_method?->getLabel(),
                'code' => $this->activationCode->code ?? null,
                'source' => $this->activationCode->source->name ?? $this->source->name ?? null,
            ],
            'billing' => [
                'cycle' => $this->billing_cycle?->value,
                'cycle_label' => $this->billing_cycle?->getLabel(),
                'amount' => (float) $this->amount,
                'currency' => $this->currency,
            ],
            'dates' => [
                'starts_at' => $this->starts_at?->toDateTimeString(),
                'ends_at' => $this->ends_at?->toDateTimeString(),
                'trial_ends_at' => $this->trial_ends_at?->toDateTimeString(),
                'starts_at_formatted' => $this->starts_at?->format('M d, Y'),
                'ends_at_formatted' => $this->ends_at?->format('M d, Y'),
            ],
            'status' => [
                'value' => $this->status?->value,
                'label' => $this->status?->getLabel(),
            ],
            'payment' => [
                'status' => $this->payment_status?->value,
                'status_label' => $this->payment_status?->getLabel(),
            ],
            'settings' => [
                'auto_renew' => (bool) $this->auto_renew,
            ],
            'features' => $this->featureSubscriptions->map(function ($feature) {
                return [
                    'id' => $feature->id,
                    'feature_id' => $feature->feature_id,
                    'name' => $feature->name,
                    'slug' => $feature->slug,
                    'value' => $feature->value,
                    'usage' => $feature->usage,
                ];
            }),
            'invoices' => $this->invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->invoice_number,
                    'amount' => (float) $invoice->total_amount,
                    'status' => $invoice->status?->value,
                    'status_label' => $invoice->status?->getLabel(),
                    'created_at' => $invoice->created_at->format('M d, Y'),
                ];
            }),
            'notes' => $this->notes,
            'file' => $this->file ? url(Storage::url($this->file)) : null,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
