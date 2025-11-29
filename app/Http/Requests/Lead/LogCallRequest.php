<?php

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;

class LogCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'call_notes' => 'required|string',
            'call_direction'  => 'required|in:incoming,outcoming',
        ];
    }
}
