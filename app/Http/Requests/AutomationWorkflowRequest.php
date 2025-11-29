<?php

namespace App\Http\Requests;

use App\Enums\AutomationAssignStrategiesEnum;
use App\Enums\ConditionOperation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AutomationWorkflowRequest extends FormRequest
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
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'automation_trigger_id' => 'required|integer|exists:automation_triggers,id',
            'steps' => 'required|array|min:1|max:50',
            'steps.*.type' => 'required|in:condition,action,delay',
            'steps.*.order' => 'required|integer|min:1|max:100',

            // Condition step validation
            'steps.*.field' => 'required_if:steps.*.type,condition|string|max:255|min:2',
            'steps.*.operation' => ['required_if:steps.*.type,condition', 'string', Rule::in(ConditionOperation::values())],
            'steps.*.value' => 'required_if:steps.*.type,condition|string|max:1000',

            // Action step validation
            'steps.*.automation_action_id' => 'required_if:steps.*.type,action|integer|exists:automation_actions,id',
            'steps.*.assign_strategy' => ['required_if:steps.*.automation_action_id,22', Rule::in(AutomationAssignStrategiesEnum::values())],
            'steps.*.assign_user_id' => 'required_if:steps.*.assign_strategy,spasific_user|integer|exists:users,id',

            // Delay step validation
            'steps.*.duration' => 'required_if:steps.*.type,delay|integer|min:1|max:999999',
            'steps.*.unit' => 'required_if:steps.*.type,delay|string|in:minutes,hours,days',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Workflow name is required',
            'name.string' => 'Workflow name must be a string',
            'name.max' => 'Workflow name cannot exceed 255 characters',
            'name.min' => 'Workflow name must be at least 3 characters',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description cannot exceed 1000 characters',
            'automation_trigger_id.required' => 'Automation trigger is required',
            'automation_trigger_id.integer' => 'Automation trigger must be a valid ID',
            'automation_trigger_id.exists' => 'Selected automation trigger does not exist',
            'steps.required' => 'At least one step is required',
            'steps.array' => 'Steps must be an array',
            'steps.min' => 'At least one step is required',
            'steps.max' => 'Maximum 50 steps allowed per workflow',
            'steps.*.type.required' => 'Step type is required',
            'steps.*.type.in' => 'Step type must be condition, action, or delay',
            'steps.*.order.required' => 'Step order is required',
            'steps.*.order.integer' => 'Step order must be a number',
            'steps.*.order.min' => 'Step order must be at least 1',
            'steps.*.order.max' => 'Step order cannot exceed 100',

            // Condition step messages
            'steps.*.field.required_if' => 'Field is required for condition steps',
            'steps.*.field.string' => 'Field must be a string',
            'steps.*.field.max' => 'Field name cannot exceed 255 characters',
            'steps.*.field.min' => 'Field name must be at least 2 characters',
            'steps.*.operation.required_if' => 'Operation is required for condition steps',
            'steps.*.operation.string' => 'Operation must be a string',
            'steps.*.operation.in' => 'Operation must be one of: ' . implode(', ', ConditionOperation::values()),
            'steps.*.value.required_if' => 'Value is required for condition steps',
            'steps.*.value.string' => 'Value must be a string',
            'steps.*.value.max' => 'Value cannot exceed 1000 characters',

            // Action step messages
            'steps.*.automation_action_id.required_if' => 'Automation action is required for action steps',
            'steps.*.automation_action_id.integer' => 'Automation action must be a valid ID',
            'steps.*.automation_action_id.exists' => 'Selected automation action does not exist',

            // Delay step messages
            'steps.*.duration.required_if' => 'Duration is required for delay steps',
            'steps.*.duration.integer' => 'Duration must be a number',
            'steps.*.duration.min' => 'Duration must be at least 1',
            'steps.*.duration.max' => 'Duration cannot exceed 999999',
            'steps.*.unit.required_if' => 'Unit is required for delay steps',
            'steps.*.unit.string' => 'Unit must be a string',
            'steps.*.unit.in' => 'Unit must be one of: seconds, minutes, hours, days, weeks, months',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'workflow name',
            'description' => 'workflow description',
            'automation_trigger_id' => 'automation trigger',
            'steps' => 'workflow steps',
            'steps.*.type' => 'step type',
            'steps.*.order' => 'step order',
            'steps.*.field' => 'condition field',
            'steps.*.operation' => 'condition operation',
            'steps.*.value' => 'condition value',
            'steps.*.automation_action_id' => 'automation action',
            'steps.*.duration' => 'delay duration',
            'steps.*.unit' => 'delay unit',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $steps = $this->input('steps', []);

            if (empty($steps)) {
                return;
            }

            // Extract orders
            $orders = array_column($steps, 'order');

            // Debug: Log the orders to see what we're getting
            \Log::info('Step orders received:', $orders);

            // Remove any null/empty values
            $orders = array_filter($orders, function ($order) {
                return $order !== null && $order !== '';
            });

            \Log::info('Filtered orders:', $orders);

            // Check if we have any orders
            if (empty($orders)) {
                $validator->errors()->add('steps', 'All steps must have an order number');
                return;
            }

            // Check for duplicate orders
            $uniqueOrders = array_unique($orders);
            \Log::info('Unique orders:', $uniqueOrders);
            \Log::info('Count comparison:', [
                'original' => count($orders),
                'unique' => count($uniqueOrders)
            ]);

            if (count($orders) !== count($uniqueOrders)) {
                $validator->errors()->add('steps', 'Step orders must be unique within the workflow');
                return;
            }

            // Sort orders to check sequence
            $sortedOrders = array_values($uniqueOrders);
            sort($sortedOrders);

            \Log::info('Sorted orders:', $sortedOrders);

            // Check if orders start from 1

            if ($sortedOrders[0] != 1) {
                $validator->errors()->add('steps', 'Step orders must start from 1');
                return;
            }

            // Check if orders are sequential (1, 2, 3, 4, ...)
            $expectedSequence = range(1, count($sortedOrders));
            \Log::info('Expected sequence:', $expectedSequence);
            if ($sortedOrders != $expectedSequence) {
                $validator->errors()->add('steps', 'Step orders must be sequential starting from 1 (1, 2, 3, ...)');
                return;
            }
        });
    }
}
