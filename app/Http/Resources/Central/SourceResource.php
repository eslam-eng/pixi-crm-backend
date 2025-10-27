<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SourceResource extends JsonResource
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
            'name' => $this->name,
            'payout_percentage' => $this->payout_percentage,
            // 'is_active' => $this->is_active->value,
            // 'is_active_text' => $this->is_active->getLabel(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'uncollected_amount' => $this->payout_batch_amount ?? 0 + $this->pending_activation_codes_used_amount ?? 0,
        ];
    }
}
