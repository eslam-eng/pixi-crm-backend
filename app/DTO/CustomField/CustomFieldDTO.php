<?php

namespace App\DTO\CustomField;

use App\DTO\BaseDTO;
use Illuminate\Support\Arr;

class CustomFieldDTO extends BaseDTO
{
    public function __construct(
        public int $form_section_id,
        public string $module,
        public string $type,
        public string $name,
        public array $label = [],
        public array $placeholder = [],
        public array $help_text = [],
        public array $options = [],
        public array $validation_rules = [],
        public bool $is_required = false,
        public bool $is_active = true,
        public int $ordering = 0,
    ) {
    }

    public static function fromRequest($request): self
    {
        return new self(
            form_section_id: (int) $request->input('form_section_id'),
            module: $request->input('module'),
            type: $request->input('type'),
            name: $request->input('name'),
            label: $request->input('label', []),
            placeholder: $request->input('placeholder', []),
            help_text: $request->input('help_text', []),
            options: $request->input('options', []),
            validation_rules: $request->input('validation_rules', []),
            is_required: (bool) $request->input('is_required', false),
            is_active: (bool) $request->input('is_active', true),
            ordering: (int) $request->input('ordering', 0),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            form_section_id: (int) Arr::get($data, 'form_section_id'),
            module: Arr::get($data, 'module'),
            type: Arr::get($data, 'type'),
            name: Arr::get($data, 'name'),
            label: Arr::get($data, 'label', []),
            placeholder: Arr::get($data, 'placeholder', []),
            help_text: Arr::get($data, 'help_text', []),
            options: Arr::get($data, 'options', []),
            validation_rules: Arr::get($data, 'validation_rules', []),
            is_required: (bool) Arr::get($data, 'is_required', false),
            is_active: (bool) Arr::get($data, 'is_active', true),
            ordering: (int) Arr::get($data, 'ordering', 0),
        );
    }

    public function toArray(): array
    {
        return [
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
        ];
    }
}
