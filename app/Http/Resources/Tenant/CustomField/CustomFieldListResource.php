<?php

namespace App\Http\Resources\Tenant\CustomField;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\FormSectionResource;

class CustomFieldListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'form_section' => new FormSectionResource($this->whenLoaded('formSection')),
            'type' => $this->type,
            'name' => $this->name,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'help_text' => $this->help_text,
            'options' => $this->options, // Options might also need translation if they are translatable, but for now I'll just return them as is or check if they are translatable.
            'is_required' => $this->is_required,
            'validation_rules' => $this->validation_rules,
        ];
    }
}
