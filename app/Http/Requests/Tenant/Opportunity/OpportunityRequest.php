<?php

namespace App\Http\Requests\Tenant\Opportunity;

use App\Enums\OpportunityStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        $required = $this->isMethod('put') ? 'sometimes' : 'required';
        return [
            'contact_id' => $required . '|exists:contacts,id',
            'status' => [$required, Rule::in(OpportunityStatus::values())],
            'stage_id' => $required . '|exists:stages,id',
            'deal_value' => $required . '|numeric',
            'win_probability' => $required . '|numeric',
            'expected_close_date' => $required . '|date',
            'assigned_to_id' => $required . '|exists:users,id',
            'notes' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.id' => 'required|exists:item_variants,id',
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.price' => 'nullable|numeric',
        ];
    }
}
