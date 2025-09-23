<?php

namespace App\Http\Resources;

use App\Http\Resources\Tenant\ItemCategory\ItemCategoryDDLResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this);
        $attrsLoaded = $this->relationLoaded('attributeValues');
        $hasAttrs    = $attrsLoaded && $this->attributeValues->isNotEmpty();

        return [
            'id' => $this->id,
            'sku' => $this->when($this->sku !== null, $this->sku),
            'stock' => $this->when($this->stock !== null, $this->stock),
            'price' => $this->price,
            'attributes' => $this->when($hasAttrs, function () {
                return $this->attributeValues->mapWithKeys(function ($attributeValue) {
                    return [
                        $attributeValue->attribute->name => [
                            'attribute_id'  => $attributeValue->attribute->id,
                            'attribute_name'  => $attributeValue->attribute->name,
                            'value_id' => $attributeValue->id,
                            'value_name' => $attributeValue->value,
                        ],
                    ];
                });
            }),
            'attributes_display' => $this->when($hasAttrs, function () {
                return $this->attributeValues
                    ->map(fn($av) => $av->attribute->name . ': ' . $av->value)
                    ->join(', ');
            }),
        ];
    }
}
