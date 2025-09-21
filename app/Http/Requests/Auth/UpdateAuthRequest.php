<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class UpdateAuthRequest extends BaseRequest
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
            'name' => 'nullable|string',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|unique:users,phone,' . auth()->user()->id,
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg',
            'email'   => 'nullable|email|unique:users,email,' . auth()->user()->id,
            'at_least_one' => 'required_without_all:address,phone,profile_image,email,name',
        ];
    }

    public function messages()
    {
        return [
            'at_least_one.required_without_all' => 'At least one of address, phone, profile image, or email is required.',
        ];
    }
}
