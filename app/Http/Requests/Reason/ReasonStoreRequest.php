<?php

namespace App\Http\Requests\Reason;

use App\DTO\Reason\ReasonDTO;
use Illuminate\Foundation\Http\FormRequest;

class ReasonStoreRequest extends FormRequest
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
            'name' => 'required|string|min:2',
        ];
    }
    public function toReasonDTO(): ReasonDTO
    {
        return ReasonDTO::fromRequest($this);
    }
}
