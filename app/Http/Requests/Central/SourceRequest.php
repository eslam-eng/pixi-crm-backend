<?php

namespace App\Http\Requests\Central;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class SourceRequest extends BaseRequest
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
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sources', 'name')->ignore($this->source),
            ],
            'payout_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->get('is_active', true),
        ]);
    }
}
