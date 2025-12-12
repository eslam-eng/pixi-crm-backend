<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;

class MultiDeleteActivationCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'exists:activation_codes,id',
        ];
    }
}
