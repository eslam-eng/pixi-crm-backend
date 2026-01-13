<?php

namespace App\Http\Resources\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivationCodeDetailResource extends JsonResource
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
            'validity_days' => $this->validity_days,
            'plan_id' => $this->plan_id,
            'plan' => $this->whenLoaded('plan', fn() => [
                'id' => $this->plan->id,
                'name' => $this->plan->name,
            ]),
            'source_id' => $this->source_id,
            'source' => $this->whenLoaded('source', fn() => [
                'id' => $this->source->id,
                'name' => $this->source->name,
            ]),
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
            ]),
            'created_by_id' => $this->created_by_id,
            'created_by' => $this->whenLoaded('createdBy', fn() => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ]),
            'redeemed_at' => $this->redeemed_at?->format('Y-m-d g:i a'),
            'collected_at' => $this->collected_at?->format('Y-m-d g:i a'),
            'expired_at' => $this->expired_at?->format('Y-m-d'),
            'created_at' => $this->created_at?->format('Y-m-d g:i a'),
            'updated_at' => $this->updated_at?->format('Y-m-d g:i a'),
        ];
    }
}
