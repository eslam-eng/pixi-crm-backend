<?php

namespace App\Http\Resources\Tenant\Items\Attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemAttributeValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'attribute' => $this->when($this->relationLoaded('attribute'), [
                'id' => $this->attribute->id,
                'name' => $this->attribute->name,
            ])
        ];
    }
}
