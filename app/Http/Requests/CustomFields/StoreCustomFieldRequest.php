<?php

namespace App\Http\Requests\CustomFields;

use App\DTO\CustomField\CustomFieldDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,dropdown',
            'options' => 'nullable|array',//if type dropdown
            'options.*' => 'string',
        ];
    }


    public function withValidator(Validator $validator)
    {
        $validator->sometimes('options', 'required|array', function ($input) {
            return $input->type === 'dropdown';
        });
    }


    public function toCustomFieldDTO(): CustomFieldDTO
    {
        return CustomFieldDTO::fromRequest($this);
    }
}
