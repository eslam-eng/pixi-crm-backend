<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class ActivationCodeIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'nullable|string',
            'status' => 'nullable|string|in:' . implode(',', ActivationCodeStatusEnum::values()),
            'source_id' => 'nullable|integer|exists:sources,id',
            'plan_id' => 'nullable|integer|exists:plans,id',
            'redeem_at_from' => 'nullable|date|required_with:redeem_at_to',
            'redeem_at_to' => 'nullable|date|required_with:redeem_at_from|after_or_equal:redeem_at_from',
        ];
    }
}
