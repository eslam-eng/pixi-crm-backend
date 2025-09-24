<?php

namespace App\Http\Resources\Tenant\Deals;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
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
            'is_checked' => $this->is_checked,
            'is_default' => $this->is_default,
            'is_manual_added' => $this->is_manual_added,
            'can_delete' => $this->canDelete() ? 1 : 0,
        ];
    }
}
