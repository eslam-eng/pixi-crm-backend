<?php

namespace App\Http\Resources\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivationCodeResource extends JsonResource
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
            'code' => $this->code,
            'status' => $this->status,
            'status_text' => $this->status?->getLabel(),
            'plan_name' => $this->whenLoaded('plan', fn() => $this->plan->name),
            'source' => $this->whenLoaded('source', fn() => $this->source->name),
            'redeemed_at' => $this->redeemed_at?->format('Y-m-d g:i a'),
            'created_by' => $this->whenLoaded('createdBy', fn() => $this->createdBy->name),
            'expired_at' => $this->expired_at?->format('Y-m-d'),
            'created_at' => $this->created_at?->format('Y-m-d g:i a'),

        ];
    }
}
