<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\ActivationStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plans', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($this->plan),
            ],
            'description'     => 'nullable|string',
            'price'           => 'required|integer|min:1',
            'duration_unit'   => 'required|string',
            'duration'        => 'required|integer|min:1',
            'is_active'       => 'required|boolean',
            'refund_period'   => 'nullable|integer|min:0',
            'features'        => 'nullable|array|min:1',
            'features.*.id' => 'required|exists:features,id',
            'features.*.value' => 'required',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active')
                ? $this->boolean('is_active')
                : ActivationStatusEnum::ACTIVE->value,
        ]);
    }
}
