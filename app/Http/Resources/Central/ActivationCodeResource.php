<?php

namespace App\Http\Resources\Central;

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
            'validity_days' => $this->validity_days,
            'expired_at' => $this->expired_at,
            //            'source' => FeatureGroupEnum::from($this->group)->getLabel(),
            'status' => $this->status,
            'status_text' => $this->status->getLabel(),
            'plan_name' => $this->whenLoaded('plan', fn() => $this->plan->name),
            'source' => SourceResource::make($this->whenLoaded('source')),
            'redeemed_at' => $this->redeemed_at,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
        ];
    }
}
