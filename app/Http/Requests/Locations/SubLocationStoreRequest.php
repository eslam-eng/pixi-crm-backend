<?php

namespace App\Http\Requests\Locations;

use App\Enums\ActivationStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class SubLocationStoreRequest extends BaseRequest
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
            'title' => 'required|string',
            'status' => ['required', Rule::enum(ActivationStatus::class)],
            'parent_id' => 'required|integer|exists:locations,id',
        ];
    }
}
