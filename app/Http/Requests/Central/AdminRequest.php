<?php

namespace App\Http\Requests\Central;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends BaseRequest
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
            'phone' => 'nullable|string',
            'is_active' => 'required|boolean',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
            'job_title' => 'required|string',
            'password' => ['nullable', 'string', 'min:6', Rule::requiredIf($this->isMethod('POST'))],
        ];
    }

}
