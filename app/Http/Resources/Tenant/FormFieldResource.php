<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
            'options' => $this->options,
            'required' => $this->required,
            'placeholder' => $this->placeholder,
            'order' => $this->order,
            'is_conditional' => $this->is_conditional,
            'depends_on_field_id' => $this->depends_on_field_id,
            'depends_on_value' => $this->depends_on_value,
            'condition_type' => $this->condition_type,
            'depend_on' => $this->whenLoaded('dependsOn', fn() => new FormFieldResource($this->dependsOn)),
        ];
    }
}
