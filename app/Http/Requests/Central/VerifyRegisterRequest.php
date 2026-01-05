<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Enums\Landlord\ActivationStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;


class VerifyRegisterRequest extends BaseRequest
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
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'domain' => ['required', 'string', 'max:255', Rule::unique('tenants', 'name')],
            'email' => 'required|email|unique:users,email',
            'plan_id' => 'nullable|integer|exists:plans,id',
            'activation_code' => [
                'nullable',
                'string',
                Rule::exists('activation_codes', 'code')->where(function ($query) {
                    $query->where('status', ActivationCodeStatusEnum::AVAILABLE->value());
                }),
            ],
        ];
    }
}
