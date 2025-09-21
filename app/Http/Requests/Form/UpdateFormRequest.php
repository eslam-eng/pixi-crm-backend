<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $formId = $this->route('form');

        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('forms')->ignore($formId)],
            'is_active' => 'boolean',
        ];
    }
}
