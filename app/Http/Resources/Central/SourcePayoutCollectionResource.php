<?php

namespace App\Http\Resources\Central;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SourcePayoutCollectionResource extends JsonResource
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
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'status' => $this->isCollected(),
            'total_amount' => $this->total_amount,
            'collected_at' => $this->collected_at,
            'total_items' => $this->whenCounted('payoutItems'),
            'collected_items' => $this->whenCounted('collectedItems'),
            'non_collected' => $this->whenCounted('nonCollectedItems'),
            'payout_items' => SourcePayoutItemsResource::collection($this->whenLoaded('payoutItems')),
        ];
    }
}
