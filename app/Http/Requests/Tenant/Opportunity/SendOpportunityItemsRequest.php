<?php

namespace App\Http\Requests\Tenant\Opportunity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendOpportunityItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel' => ['required', Rule::in(['email', 'whatsapp'])],
            'subject' => ['required_if:channel,email', 'nullable', 'string', 'max:255'],
            'selected_item_columns' => ['array'],
            'selected_item_columns.*' => ['string'],
        ];
    }
}
