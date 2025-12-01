<?php

namespace App\Http\Requests\Tenant\Opportunity;

use Illuminate\Foundation\Http\FormRequest;

class ActivityLogReuest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activity_type' => 'required|in:meeting,call,email,note,task',
            'title' => ['required', 'string', 'max:255'],
            'description' => 'nullable|string',
        ];
    }
}
