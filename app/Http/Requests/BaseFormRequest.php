<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->wantsJson() || $this->is('api/*')) {
            $errors = $validator->errors()->toArray();

            throw new HttpResponseException(ApiResponse::validationErrors(errors: $errors));
        }

        parent::failedValidation($validator); // fallback to default
    }
}
