<?php

namespace App\Http\Requests\Central\Auth;

use App\Http\Requests\BaseRequest;

class AuthFormRequest extends BaseRequest
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
