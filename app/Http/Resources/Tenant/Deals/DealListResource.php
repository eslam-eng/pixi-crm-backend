<?php

namespace App\Http\Resources\Tenant\Deals;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealListResource extends JsonResource
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
            'deal_name' => $this->deal_name,
            'lead_id' => $this->lead->id,
            'contact' => $this->lead->contact?->name,
            'payment_status' => $this->payment_status,
            'total_amount' => $this->total_amount,
            'due_amount' => $this->amount_due,
            'approval_status' => $this->approval_status,
            'close_date' => $this->created_at->format('Y-m-d'),
            'assigned_to' => $this->assigned_to?->name,
        
        ];
    }
}
