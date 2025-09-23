<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->load('variants.attributeValues.attribute');
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'variants' => ItemVariantResource::collection($this->load('variants.attributeValues.attribute')->variants),
        ];
    }
}
