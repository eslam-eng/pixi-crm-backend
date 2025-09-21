<?php

namespace App\Http\Requests\Tenant\Tasks;

use App\Enums\TaskStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class TaskChangeStatusRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(TaskStatusEnum::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Task status is required.',
            'status.in' => 'Invalid task status selected.',
        ];
    }
}