<?php

namespace App\Http\Resources\Tenant\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTargetResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'period_type' => $this->period_type,
            'year' => $this->year,
            'period' => $this->period_type->value === "monthly" ? now()->month($this->period_number)->format('M') : $this->period_number . "Q",
            'target_value' => $this->target_value,
            'effective_from' => $this->effective_from,
            'effective_to' => $this->effective_to,
        ];
    }
}
