<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'required|string|max:255|unique:forms,slug',
            'is_active' => 'boolean',

            // Fields
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,email,textarea,select,checkbox,radio,number,file',
            'fields.*.options' => 'nullable|array',
            'fields.*.required' => 'boolean',
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.order' => 'integer|min:0',

            // Actions
            'actions' => 'nullable|array',
            'actions.*.type' => 'required|in:redirect,email,webhook',
            'actions.*.settings' => 'required|array',
            'actions.*.is_active' => 'boolean',
            'actions.*.order' => 'integer|min:0',
        ];
    }
}
