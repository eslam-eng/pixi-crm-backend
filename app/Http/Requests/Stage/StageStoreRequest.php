<?php

namespace App\Http\Requests\Stage;

use App\DTO\Stage\StageDTO;
use Illuminate\Foundation\Http\FormRequest;

class StageStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:stages,name',
            'probability' => 'required|integer|min:0|max:100',
        ];
    }



    public function toStageDTO(): StageDTO
    {
        return new StageDTO(
            name: $this->input('name'),
            probability: $this->input('probability'),
        );
    }
}
