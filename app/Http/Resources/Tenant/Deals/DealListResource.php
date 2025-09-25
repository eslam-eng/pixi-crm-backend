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
            'sale_date' => $this->sale_date,
            'payment_status' => $this->payment_status,
            'assigned_to' => $this->assigned_to?->name,
            'stage' => $this->whenLoaded('stage', fn() => $this->stage->name),
            'total_amount' => $this->total_amount,
            'approval_status' => $this->approval_status,
        
        ];
    }
}
