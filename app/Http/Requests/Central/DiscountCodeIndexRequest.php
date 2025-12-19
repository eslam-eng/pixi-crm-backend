<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Enums\Landlord\DiscountUsageEnum;
use Illuminate\Foundation\Http\FormRequest;

class DiscountCodeIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'discount_code' => 'nullable|string',
            'status' => 'nullable|string|in:' . implode(',', ActivationCodeStatusEnum::values()),
            'plan_id' => 'nullable|integer|exists:plans,id',
            'discount_type' => 'nullable|string|in:' . implode(',', DiscountUsageEnum::values()),
        ];
    }
}
