<?php

namespace App\Http\Requests\Tenant\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class TaskCalendarRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'calendar_type' => 'required|in:day,month,week',
            'due_date' => 'required_if:calendar_type,day|date_format:Y-m-d',
            'task_type_id' => 'nullable|exists:task_types,id',
            'team_id' => 'nullable|exists:teams,id',
            'status' => 'nullable|string',
            'contact_id' => 'nullable|exists:contacts,id',
            'month' => 'required_if:calendar_type,month|integer|between:1,12',
            'year' => 'required_if:calendar_type,month|integer|min:2000|max:2100',
            'due_date_range' => 'required_if:calendar_type,week|array',
            'due_date_range.start' => 'required_with:due_date_range|date_format:Y-m-d',
            'due_date_range.end' => 'required_with:due_date_range|date_format:Y-m-d|after_or_equal:due_date_range.start',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'calendar_type.required' => 'Calendar type is required',
            'calendar_type.in' => 'Calendar type must be day, month, or week',
            'day.required_if' => 'Day is required when calendar type is day',
            'day.date_format' => 'Day must be in Y-m-d format',
            'month.required_if' => 'Month is required when calendar type is month',
            'month.between' => 'Month must be between 1 and 12',
            'year.required_if' => 'Year is required when calendar type is month',
            'due_date_range.required_if' => 'Due date range is required when calendar type is week',
            'due_date_range.start.required_with' => 'Start date is required in due date range',
            'due_date_range.end.after_or_equal' => 'End date must be after or equal to start date',
        ];
    }
}
