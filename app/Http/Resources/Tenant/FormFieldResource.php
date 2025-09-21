<?php
// app/Http/Resources/FormFieldResource.php

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
        ];
    }
}