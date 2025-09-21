<?php

namespace App\Http\Resources;

use App\Http\Resources\Tenant\ItemCategory\ItemCategoryDDLResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'description' => $this->description,
            'sku' => $this->when($this->sku !== null, $this->sku),
            'price' => $this->price,
            'type' => $this->type,
            'quantity' => $this->when($this->quantity !== null, $this->quantity),
            'duration' => $this->when($this->duration !== null, $this->duration),
            'category' => $this->whenLoaded('category', fn() => new ItemCategoryDDLResource($this->category)),
            'variants_count' => $this->variants->count(),
            'variants' => ItemProductVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
