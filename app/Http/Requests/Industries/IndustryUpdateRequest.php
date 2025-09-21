<?php

namespace App\Http\Requests\Industries;

use App\DTO\Industry\IndustryDTO;
use App\Http\Requests\BaseRequest;

class IndustryUpdateRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

    public function toIndustryDTO(): \App\DTO\BaseDTO
    {
        return IndustryDTO::fromRequest($this);
    }
}
