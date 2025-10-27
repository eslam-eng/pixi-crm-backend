<?php

namespace App\Http\Requests\Central;

use App\Enums\Landlord\SupportedLocalesEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class ChangeLocalRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', Rule::in(SupportedLocalesEnum::values())],
        ];
    }

    public function messages()
    {
        return [
            'locale.in' => __('validation.custom.locale.in'),
            'locale.required' => __('validation.custom.locale.required'),
        ];
    }
}
