<?php

namespace App\Http\Requests\Central\Users;

use App\DTO\Tenant\UserDTO;
use App\Http\Requests\BaseRequest;

class UserStoreRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|numeric|unique:users,phone',
        ];
    }

    public function toUserDTO(): \App\DTO\BaseDTO
    {
        return UserDTO::fromRequest($this);
    }
}
