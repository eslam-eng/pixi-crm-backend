<?php

namespace App\Http\Requests\Central;

use App\Enum\CustomerStatusEnum;
use App\Enum\LandlordPermissionsEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends BaseFormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($this->role)],
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'permissions' => 'required|array|min:1',
            'permissions.*' => ['required', Rule::in(LandlordPermissionsEnum::values())],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->get('status', CustomerStatusEnum::ACTIVE->value),
        ]);
    }
}
