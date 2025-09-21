<?php

namespace App\Http\Requests\Tenant\Tasks;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class PriorityRequest extends BaseRequest
{
    public function rules(): array
    {
        $priorityId = $this->id ?? $this->priority;
        $levelRule = ['required', 'integer', 'min:1', Rule::unique('priorities', 'level')];
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $levelRule[3] = Rule::unique('priorities', 'level')->ignore($priorityId);
        }

        $nameRule = ['required', 'string', 'max:255', Rule::unique('priorities', 'name')];
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $nameRule[3] = Rule::unique('priorities', 'name')->ignore($priorityId);
        }
    
        return [
            'name' => $nameRule,
            'color_id' => ['required', 'integer', 'exists:priority_colors,id'],
            'level' => $levelRule,
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Priority name is required.',
            'name.string' => 'Priority name must be a string.',
            'name.max' => 'Priority name may not be greater than 255 characters.',
            'name.unique' => 'This priority name already exists.',
            'color_id.required' => 'Priority color is required.',
            'color_id.integer' => 'Priority color must be a valid color ID.',
            'color_id.exists' => 'Selected priority color does not exist.',
            'level.required' => 'Priority level is required.',
            'level.integer' => 'Priority level must be an integer.',
            'level.min' => 'Priority level must be at least 1.',
            'level.unique' => 'This priority level already exists.',
            'is_default.boolean' => 'Default status must be true or false.',
        ];
    }
}
