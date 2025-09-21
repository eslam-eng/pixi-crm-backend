<?php

namespace App\Http\Requests\Teams;

use App\DTO\Tenant\UserDTO;
use App\Http\Requests\BaseRequest;

class TeamUpdateRequest extends BaseRequest
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
            'email' => 'required|email|unique:users,email,' . $this->user,
            'password' => 'nullable|string|min:6|confirmed',
            'phone' => 'required|numeric|unique:users,phone,' . $this->user,
            'profile_image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg',
        ];
    }


    public function toUserDTO(): \App\DTO\BaseDTO
    {
        return UserDTO::fromRequest($this);
    }
}
