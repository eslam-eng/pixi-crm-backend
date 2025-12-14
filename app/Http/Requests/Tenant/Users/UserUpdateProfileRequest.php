<?php

namespace App\Http\Requests\Tenant\Users;

use App\Http\Requests\BaseRequest;

class UserUpdateProfileRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required_without_all:last_name,profile_image|nullable|string',
            'last_name' => 'required_without_all:last_name,profile_image|nullable|string',
            'profile_image' => 'nullable|file|mimes:jpg,png,jpeg,gif,svg,webp|max:2048',
        ];
    }

}
