<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomFieldResource extends JsonResource
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
            'form_section_id' => $this->form_section_id,
            'module' => $this->module,
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'help_text' => $this->help_text,
            'options' => $this->options,
            'validation_rules' => $this->validation_rules,
            'is_required' => $this->is_required,
            'is_active' => $this->is_active,
            'ordering' => $this->ordering,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
