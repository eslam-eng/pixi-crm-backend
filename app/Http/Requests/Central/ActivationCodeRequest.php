<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\ActivationCodeStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ActivationCodeRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan_id' => 'required|integer|exists:plans,id',
            'source_id' => 'required|integer|exists:sources,id',
            'validity_days' => 'required|integer',
            'status' => ['required', 'string', Rule::in(ActivationCodeStatusEnum::values())],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge(['status' => $this->status ?? ActivationCodeStatusEnum::AVAILABLE->value]);
    }
}
