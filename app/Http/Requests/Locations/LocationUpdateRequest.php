<?php

namespace App\Http\Requests\Locations;

use App\DTO\Location\LocationDTO;
use App\Enums\ActivationStatus;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class LocationUpdateRequest extends BaseRequest
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
            'title' => 'required|string',
            'status' => ['required', Rule::enum(ActivationStatus::class)],
        ];
    }

    public function toLocationDTO(): \App\DTO\BaseDTO
    {
        return LocationDTO::fromRequest($this);
    }
}
