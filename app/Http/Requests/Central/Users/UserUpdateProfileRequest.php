<?php

namespace App\Http\Requests\Central\Users;

use App\Http\Requests\BaseRequest;

class UserUpdateProfileRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.auth()->user()->id,
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'required|numeric|unique:users,phone,'.auth()->user()->id,
        ];
    }

}
