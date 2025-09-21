<?php

namespace App\Http\Requests\Industries;

use App\DTO\Industry\IndustryDTO;

use App\Http\Requests\BaseRequest;

class IndustryStoreRequest extends BaseRequest
{
    /**
     * Determine if the industry is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
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
