<?php

namespace App\Http\Requests\Service;

use App\DTO\Service\ServiceDTO;
use Illuminate\Foundation\Http\FormRequest;

class ServiceUpdateRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'price' => 'nullable|numeric',
            'categories' => 'nullable|array',
            'categories.*.name' => 'nullable|string|max:255',
            'categories.*.price' => 'nullable|numeric|min:0',
        ];
    }
    public function toServiceDTO(): ServiceDTO
    {
        return ServiceDTO::fromRequest($this);
    }
}
