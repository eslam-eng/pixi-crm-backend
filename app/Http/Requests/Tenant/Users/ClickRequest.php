<?php

namespace App\Http\Requests\Tenant\Users;

use App\Http\Requests\BaseRequest;

class ClickRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:in,out'],
            'location' => ['nullable', 'array'],
            'location.lat' => ['required_with:location', 'numeric', 'between:-90,90'],
            'location.lng' => ['required_with:location', 'numeric', 'between:-180,180'],
            'location.accuracy' => ['nullable', 'numeric', 'min:0', 'max:5000'], // meters
            'location.source' => ['nullable', 'in:browser,ip,manual'],
            'location.place' => ['nullable', 'string', 'max:255'],
        ];
    }
}
