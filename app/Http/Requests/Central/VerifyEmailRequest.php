<?php

namespace App\Http\Requests\Central;

use App\Http\Requests\BaseFormRequest;

class VerifyEmailRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
        ];
    }
}
