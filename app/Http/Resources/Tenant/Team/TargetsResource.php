<?php

namespace App\Http\Resources\Tenant\Team;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'period_type' => $this->period_type,
            'year' => $this->year,
            'period' => $this->period_type->value === "monthly" ? now()->month($this->period_number)->format('M') : $this->period_number . "Q",
            'effective_from' => $this->effective_from,
            'effective_to' => $this->effective_to,
            'target_value' => $this->target_value,
        ];
    }
}
