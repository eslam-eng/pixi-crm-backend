<?php

namespace App\Http\Requests\Central;

use App\Http\Requests\BaseFormRequest;
use App\Models\Landlord\ActivationCode;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterWithActivationCodeRequest extends BaseFormRequest
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
            'name' => 'required|string|max:255',
            'organization_name' => ['required', 'string', 'max:255', Rule::unique('tenants', 'slug')->whereNull('deleted_at')],
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()],
            'activation_code' => ['required', 'string', 'exists:activation_codes,code'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $code = $this->input('activation_code');
            $activationCode = ActivationCode::firstWhere('code', $code);

            if (! $activationCode) {
                $validator->errors()->add('activation_code', 'Activation code not found.');

                return;
            }

            if ($activationCode->isExpired()) {
                $validator->errors()->add('activation_code', 'Activation code is expired.');
            }

            if ($activationCode->isRedeemed()) {
                $validator->errors()->add('activation_code', 'Activation code has already been redeemed.');
            }
        });
    }
}
