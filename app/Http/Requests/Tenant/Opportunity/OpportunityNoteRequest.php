<?php

namespace App\Http\Requests\Tenant\Opportunity;

use App\Enums\OpportunityNoteTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class OpportunityNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(OpportunityNoteTypeEnum::class)],
            'content' => ['required', 'string'],
        ];

        if ($this->isMethod('post')) {
            $rules['opportunity_id'] = ['required', 'exists:leads,id'];
        } else {
            $rules['opportunity_id'] = ['sometimes', 'exists:leads,id'];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        // If route has opportunity parameter, merge it?
        if ($this->route('opportunity')) {
            $this->merge(['opportunity_id' => $this->route('opportunity')]);
        }
    }
}
