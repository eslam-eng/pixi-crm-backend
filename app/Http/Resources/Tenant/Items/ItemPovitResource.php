<?php

namespace App\Http\Resources\Tenant\Items;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemPovitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->pivot->quantity,
            'price' => $this->pivot->price,
        ];
    }
}