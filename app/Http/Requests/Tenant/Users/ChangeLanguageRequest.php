<?php

namespace App\Http\Requests\Tenant\Users;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ChangeLanguageRequest extends BaseRequest
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
     */
    public function rules(): array
    {
        return [
            'lang' => ['required', 'string', Rule::in(['ar', 'en', 'fr', 'es'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'lang.required' => 'Language is required.',
            'lang.in' => 'Language must be one of: ar, en, fr, es.',
        ];
    }
}
