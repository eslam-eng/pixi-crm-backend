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
            'type' => ['sometimes', 'in:in,out'],
            'lat' => ['sometimes', 'numeric', 'between:-90,90'],
            'lng' => ['sometimes', 'numeric', 'between:-180,180'],
        ];
    }
}
