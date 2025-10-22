<?php

namespace App\Http\Requests\Central;

use App\Enum\CustomerStatusEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends BaseFormRequest
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
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($this->admin)],
            //            'password' => ['required', Password::min(8)->mixedCase()],
            'phone' => 'nullable|string',
            'is_active' => 'required|boolean',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'required|exists:roles,id',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->get('status', CustomerStatusEnum::ACTIVE->value),
        ]);
    }
}
