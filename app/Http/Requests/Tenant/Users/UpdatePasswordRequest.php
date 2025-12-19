<?php

namespace App\Http\Requests\Tenant\Users;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (! Hash::check($this->current_password, $this->user()->password)) {
                $validator->errors()->add('current_password', 'Current password is incorrect.');
            }
        });
    }
}
