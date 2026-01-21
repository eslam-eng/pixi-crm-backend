<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $activeSubscription = $this->activeSubscription;
        $latestSubscription = $this->latestSubscription;
        $owner = $this->owner;

        return [
            'id' => $this->id,
            'company_details' => [
                'name' => $owner?->company_name ?? $this->name,
                'domain' => $this->name . '.' . config('tenancy.central_domains')[0], // assuming typical setup
            ],
            'contact' => [
                'name' => $owner?->first_name . ' ' . $owner?->last_name,
                'email' => $owner?->email,
            ],
            'package' => [
                'name' => $latestSubscription?->plan?->name ?? 'N/A',
                'end_at' => $latestSubscription?->ends_at?->format('Y-m-d') ?? 'N/A',
                'price' => isset($latestSubscription?->amount) ? $latestSubscription->amount . '/' . ($latestSubscription->billing_cycle?->value ?? 'month') : 'N/A',
            ],
            'status' => [
                'value' => $this->status,
                'label' => $this->status?->getLabel() ?? 'No Status',
            ],
            'subscription' => $activeSubscription ? 'Active' : 'No subscription',
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
