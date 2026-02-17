<?php

namespace App\Http\Resources\Tenant\CustomField;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomFieldValueResource extends JsonResource
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
            'label' => $this->label, // Translated label
            'value' => $this->pivot->value,
            'type' => $this->type,
            'options' => $this->options, // Options if applicable
        ];
    }
}
