<?php

namespace App\Http\Requests\CustomFields;

use App\DTO\CustomField\CustomFieldDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Enums\CustomFieldTypeEnum;
class StoreCustomFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'form_section_id' => 'required|exists:form_sections,id',
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::enum(CustomFieldTypeEnum::class)],
            'label' => 'required|array',
            'label.*' => 'required|string',
            'placeholder' => 'nullable|array',
            'placeholder.*' => 'nullable|string',
            'help_text' => 'nullable|array',
            'help_text.*' => 'nullable|string',
            'is_required' => 'required|boolean',
            'is_active' => 'required|boolean',
            'options' => 'nullable|array',
            'options.*' => 'string',
        ];
    }


    public function withValidator(Validator $validator)
    {
        $validator->sometimes('options', 'required|array', function ($input) {
            return in_array($input->type, [
                CustomFieldTypeEnum::DROPDOWN->value,
                CustomFieldTypeEnum::MULTI_SELECT->value,
                CustomFieldTypeEnum::CHECKBOX->value,
                CustomFieldTypeEnum::RADIO->value,
            ]);
        });
    }


    public function toCustomFieldDTO(): CustomFieldDTO
    {
        return CustomFieldDTO::fromRequest($this);
    }
}
