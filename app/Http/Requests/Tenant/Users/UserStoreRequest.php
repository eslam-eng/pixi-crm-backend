<?php

namespace App\Http\Requests\Users;

use App\DTO\Tenant\UserDTO;
use App\Enums\UserType;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Enum;

class UserStoreRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        // Merge additional fields into the request before validation
        $this->merge([
            'type' => $this->determineType(), // Add computed status
        ]);
    }

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
    /**
     * Determine type based on request data
     */
    protected function determineType(): string
    {
        return match ($this->role) {
            1 => UserType::ADMIN->value,
            2 => UserType::LEADER->value,
            default => UserType::SALES->value,
        };
    }
}
