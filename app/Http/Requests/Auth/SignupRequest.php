<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserType;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Enum;

class SignupRequest extends BaseRequest
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
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email'   => 'required|email|unique:users',
            'type' => ['required', new Enum(UserType::class)],
            'password'   => 'required|min:6',
        ];
    }
}
