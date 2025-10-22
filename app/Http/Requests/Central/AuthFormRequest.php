<?php

namespace App\Http\Requests\Central;

use App\Http\Requests\BaseFormRequest;

class AuthFormRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'identifier.required' => __('validation.identifier_required'),
            'password.*' => __('validation.password_invalid'),
        ];
    }
}
