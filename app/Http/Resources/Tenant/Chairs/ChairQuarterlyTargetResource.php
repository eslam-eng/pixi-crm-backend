<?php

namespace App\Http\Resources\Tenant\Chairs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChairQuarterlyTargetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quarterly' => 'Q'.$this->period_number,
            'year' => $this->year,
            'target_value' => $this->target_value,
            'effective_from' => $this->effective_from,
            'effective_to' => $this->effective_to,
        ];
    }
}
