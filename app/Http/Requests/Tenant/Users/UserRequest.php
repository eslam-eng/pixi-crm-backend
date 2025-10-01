<?php

namespace App\Http\Requests\Tenant\Users;

use App\DTO\Tenant\UserDTO;
use App\Enums\TargetType;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UserRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->id ?? $this->user;

        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:6', Rule::requiredIf($this->isMethod('POST'))],
            'phone' => ['required', 'numeric', Rule::unique('users', 'phone')->ignore($userId)],
            'department_id' => 'required|exists:departments,id,is_active,1',
            'role' => ['required', Rule::exists('roles', 'name')],
            'job_title' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
            'target' => 'nullable|numeric',
            'target_type' => ['required_with:target', Rule::enum(TargetType::class)],
        ];
    }

    public function toUserDTO(): \App\DTO\BaseDTO
    {
        return UserDTO::fromRequest($this);
    }
}
