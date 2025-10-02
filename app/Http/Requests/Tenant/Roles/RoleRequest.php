<?php

namespace App\Http\Requests\Tenant\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('put') ? 'sometimes' : 'required';
        $roleId = $this->route('role');
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) {
                    return $query->where('guard_name', $this->guard_name ?? 'api_tenant');
                })->ignore($roleId),
            ],
            'description' => 'nullable|string|max:255',
            'permissions' => $required . '|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'is_system' => false,
            'guard_name' => 'api_tenant',
        ]);
    }
}
