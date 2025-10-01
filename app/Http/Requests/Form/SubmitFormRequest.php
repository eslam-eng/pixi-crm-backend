<?php

namespace App\Http\Requests\Form;

use App\Models\Tenant\Form;
use Illuminate\Foundation\Http\FormRequest;

class SubmitFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $form = Form::with('fields.dependsOn')->findOrFail($this->form_id);

        return $form->getValidationRules($this->all());
    }
}
