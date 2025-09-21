<?php

namespace App\Http\Requests\Tenant\Tasks;

use App\Enums\TaskStatusEnum;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_type_id' => 'required|exists:task_types,id',
            'status' => ['required', Rule::in(TaskStatusEnum::values())],
            'priority_id' => 'required|exists:priorities,id',
            'due_date' => 'required|date',
            'due_time' => 'required|date_format:H:i',
            'assigned_to_id' => 'required|exists:users,id',
            'followers' => 'nullable|array',
            'followers.*' => 'nullable|exists:users,id',
            'lead_id' => 'required|exists:leads,id',
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string|max:255',
            'additional_notes' => 'nullable|string',
            'reminders' => 'nullable|array',
            'reminders.*' => 'required|exists:reminders,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.string' => 'Task title must be a string.',
            'title.max' => 'Task title may not be greater than 255 characters.',
            'description.string' => 'Description must be a string.',
            'task_type_id.required' => 'Task type is required.',
            'task_type_id.exists' => 'Selected task type does not exist.',
            'status.required' => 'Task status is required.',
            'status.in' => 'Invalid task status selected.',
            'priority_id.required' => 'Priority is required.',
            'priority_id.exists' => 'Selected priority does not exist.',
            'due_date.required' => 'Due date is required.',
            'due_date.date' => 'Due date must be a valid date.',
            'due_time.required' => 'Due time is required.',
            'due_time.date_format' => 'Due time must be in H:i format.',
            'assigned_to_id.required' => 'Assigned user is required.',
            'assigned_to_id.exists' => 'Selected user does not exist.',
            'followers.array' => 'Followers must be an array.',
            'followers.*.exists' => 'Selected follower does not exist.',
            'lead_id.required' => 'Lead is required.',
            'lead_id.exists' => 'Selected lead does not exist.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Each tag may not be greater than 255 characters.',
            'additional_notes.string' => 'Additional notes must be a string.',
            'reminders.array' => 'Reminders must be an array.',
            'reminders.*.required' => 'Each reminder ID is required.',
            'reminders.*.exists' => 'Selected reminder does not exist.',
        ];
    }
}
