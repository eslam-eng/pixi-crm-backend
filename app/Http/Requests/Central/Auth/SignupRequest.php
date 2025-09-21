<?php

namespace App\Http\Requests\Central\Auth;

use App\Http\Requests\BaseRequest;

class SignupRequest extends BaseRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'tenant' => 'required|string|unique:tenants,name',
            'email'   => 'required|email|unique:users',
            'password'   => 'required|min:6',
            'activation_code' => 'required|string|exists:activation_codes,code,used_at,NULL',
            'image' => 'nullable|image|max:2048', // 2MB max
        ];
    }
}
