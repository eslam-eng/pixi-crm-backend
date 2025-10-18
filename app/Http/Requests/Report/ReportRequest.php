<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'report_type' => 'required|string|in:sales_performance,lead_management,team_performance,task_completion,revenue_analysis,opportunity_pipeline,call_activity,contact_management,product_performance,forecasting',
            'category' => 'required|string|max:255',
            'is_active' => 'boolean',
            'is_scheduled' => 'boolean',
            'schedule_frequency' => 'nullable|string|in:daily,weekly,monthly,quarterly,yearly',
            'schedule_time' => 'nullable|date_format:H:i',
            'recipients' => 'nullable|array',
            'recipients.*' => 'email',
            'settings' => 'nullable|array',
            'permissions' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Report name is required',
            'report_type.required' => 'Report type is required',
            'report_type.in' => 'Invalid report type',
            'category.required' => 'Report category is required',
            'schedule_frequency.in' => 'Invalid schedule frequency',
            'schedule_time.date_format' => 'Schedule time must be in HH:MM format',
            'recipients.*.email' => 'Each recipient must be a valid email address',
        ];
    }
}
