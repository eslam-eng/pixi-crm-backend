<?php

namespace App\Http\Requests\Tenant\Users;

use App\DTO\Tenant\UserDTO;
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
        $userId = $this->user;

        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:6', Rule::requiredIf($this->isMethod('POST'))],
            'phone' => ['nullable', 'numeric', Rule::unique('users', 'phone')->ignore($userId)],
            'department_id' => 'required|exists:departments,id,is_active,1',
            'role' => ['required', Rule::exists('roles', 'name')],
            'job_title' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
            'lang' => ['required', 'string', Rule::in(['ar', 'en', 'fr', 'es'])],
            'is_active' => 'required|boolean',
            'monthly_target' => 'nullable|array',
            'monthly_target.*.amount' => 'required|numeric|min:1',
            'monthly_target.*.month' => 'required|distinct|integer|min:1|max:12',
            'quarterly_target' => 'nullable|array',
            'quarterly_target.*.amount' => 'required|numeric|min:1',
            'quarterly_target.*.quarter' => 'required|distinct|integer|min:1|max:4',
        ];
    }

    public function toUserDTO(): \App\DTO\BaseDTO
    {
        return UserDTO::fromRequest($this);
    }
}
