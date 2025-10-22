<?php

namespace App\Http\Requests\Central;

use App\Enum\AvailableSocialProvidersEnum;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class SocialAuthRequest extends BaseFormRequest
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
            'provider_name' => ['required', 'string', Rule::in(AvailableSocialProvidersEnum::values())],
            'access_token' => 'required|string',
        ];
    }
}
