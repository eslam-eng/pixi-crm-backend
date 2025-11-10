<?php

namespace App\Http\Resources\Opportunity;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemInOpportunity extends JsonResource
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
            'quantity' => $this->pivot->quantity,
            'price' => $this->pivot->price,
            'type' => $this->itemable_type,
            'service_type' => $this->whenLoaded('itemable', fn() => $this->itemable->service_type),
            'sub_category_id' => $this->whenLoaded('category', fn() => $this->category->id),
            'sub_category_name' => $this->whenLoaded('category', fn() => $this->category->name),
            'category_id' => $this->category->parent->id,
            'category_name' => $this->category->parent->name,
        ];
    }
}
