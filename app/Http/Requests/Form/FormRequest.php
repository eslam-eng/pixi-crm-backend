<?php

namespace App\Http\Requests\Form;

use App\Http\Requests\BaseRequest;
use App\Services\FormService;
use Illuminate\Validation\Rule;

class FormRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $formId = $this->route('form');

        $required = $this->isMethod('PUT') ? 'sometimes' : 'required';
        return [
            'title' => $required . '|string|max:255',
            'description' => 'nullable|string',
            'slug' => [$required, 'string', 'max:255', Rule::unique('forms')->ignore($formId)],
            'is_active' => $required . '|boolean',

            // Fields
            'fields' => $required . '|array|min:1',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,email,textarea,select,checkbox,radio,number,file,url,tel',
            'fields.*.options' => 'nullable|array',
            'fields.*.conditions' => 'nullable|array',
            'fields.*.required' => 'nullable|boolean',
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.order' => 'nullable|integer|min:0',
            'fields.*.is_conditional' => 'sometimes|boolean',
            'fields.*.depends_on_field_id' => 'sometimes|exists:form_fields,id',
            'fields.*.depends_on_value' => 'sometimes|string',
            'fields.*.condition_type' => 'sometimes|string|in:equals,not_equals,contains,in,greater_than,less_than',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->isMethod('PUT')) {
            $formId = $this->route('form'); // الـ ID;
            $form = app(FormService::class)->findById($formId);
            $this->merge([
                'title' => $this->input('title', $form->title),
                'description' => $this->input('description', $form->description),
                'slug' => $this->input('slug', $form->slug),
                'is_active' => $this->input('is_active', $form->is_active),
            ]);
        }
    }
}
