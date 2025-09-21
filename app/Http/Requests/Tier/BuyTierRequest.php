<?php

namespace App\Http\Requests\Tier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuyTierRequest extends FormRequest
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
            'activation_code' => [
                'required',
                'string',
                Rule::exists('activation_codes', 'activation_code')->where(function ($query) {
                    $query->whereNull('used_at');
                }),
            ],
            'tenant_id' => [
                'required',
                'exists:tenants,id',
            ],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => request()->user()->id,
        ]);
    }

    public function messages(): array
    {
        return [
            'activation_code.required' => 'The activation code is required.',
            'activation_code.exists' => 'The provided activation code does not exist or has been used before.',
            'tenant_id.required' => 'The tenant ID is required.',
            'tenant_id.exists' => 'The provided tenant ID does not exist.',
        ];
    }
}
