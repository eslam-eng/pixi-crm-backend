<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;


class BaseRequest extends FormRequest
{
    //extend it if you validate api request
    public function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $mappedErrors = collect($validator->errors())->map(function ($error, $key) {
                return [
                    "key" => $key,
                    "error" => Arr::first($error),
                ];
            })->values()->toArray();
            throw new HttpResponseException(response(['message' => __('app.invalid inputs'), 'errors' => $mappedErrors], 422));
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    protected function validateRequiredCustomFields($validator, string $module)
    {
        $requiredFields = \App\Models\Tenant\CustomField::byModule($module)
            ->active()
            ->where('is_required', true)
            ->get();

        if ($requiredFields->isEmpty()) {
            return;
        }

        $providedFields = $this->input('custom_fields', []);

        foreach ($requiredFields as $field) {
            // Find the provided data for this required field
            $providedData = null;
            foreach ($providedFields as $data) {
                if (isset($data['custom_field_id']) && $data['custom_field_id'] == $field->id) {
                    $providedData = $data;
                    break;
                }
            }

            if (!$providedData || (!isset($providedData['value']) || $providedData['value'] === '' || $providedData['value'] === null)) {
                $validator->errors()->add("customFields.required_field_{$field->id}", "The custom field '{$field->name}' is required for module {$module}.");
            }
        }
    }
}
