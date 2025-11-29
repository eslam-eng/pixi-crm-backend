<?php

namespace App\Http\Requests\Tenant;

use App\Models\Tenant\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingRequest extends FormRequest
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
        $setting = $this->input('setting');

        return [
            'setting' => 'required|string',
            'value' => $this->getValueValidationRules($setting),
            'group' => 'required|string'
        ];
    }

    /**
     * Get validation rules for the value based on the setting name
     */
    private function getValueValidationRules(string $setting): array
    {
        $rules = [];

        switch ($setting) {
            // Tasks Settings
            case 'enable_escalation':
            case 'enable_priority':
            case 'notify_manager':
            case 'mail_notification':
            case 'all_deals_required_approval':
            case 'system_notification':
                $rules[] = 'required';
                $rules[] = 'boolean';
                break;

            case 'escalation_time_hours':
                $rules[] = 'required';
                $rules[] = 'integer';
                $rules[] = 'min:1';
                $rules[] = 'max:8760';
                break;

            case 'third_phase_type':
                $rules[] = 'required';
                $rules[] = 'integer';
                $rules[] = 'exists:task_types,id';
                break;

            case 'default_followers_users':
                $rules[] = 'nullable';
                $rules[] = 'array';
                $rules[] = function ($attribute, $value, $fail) {
                    // Allow null values
                    if (is_null($value)) {
                        return; // Null is valid
                    }

                    if (is_array($value)) {
                        // Allow empty array
                        if (empty($value)) {
                            return; // Empty array is valid
                        }

                        // Check if all items are integers
                        foreach ($value as $item) {
                            if (!is_numeric($item) || (int)$item != $item) {
                                $fail('All items in default_followers_users must be integers.');
                                return;
                            }
                        }

                        // Convert to integers and check if user IDs exist
                        $userIds = array_map('intval', $value);

                        // Filter out 0 values (which can occur from empty strings or null values)
                        $userIds = array_filter($userIds, function ($id) {
                            return $id > 0;
                        });

                        // If no valid user IDs after filtering, skip database check
                        if (empty($userIds)) {
                            return;
                        }

                        $existingUserIds = User::whereIn('id', $userIds)->pluck('id')->toArray();
                        $missingUserIds = array_diff($userIds, $existingUserIds);

                        if (!empty($missingUserIds)) {
                            $missingIdsString = implode(', ', $missingUserIds);
                            $fail("The following user IDs do not exist: {$missingIdsString}");
                        }
                    }
                };
                break;

            // Deals Settings
            case 'default_currency':
                $rules[] = 'required';
                $rules[] = Rule::in(['USD', 'EUR', 'GBP', 'AED', 'SAR']);
                break;

            case 'default_tax_rate':
            case 'maximum_discount_percentage':
                $rules[] = 'required';
                $rules[] = 'integer';
                $rules[] = 'min:0';
                $rules[] = 'max:100';
                break;


            case 'attachment_size_limit_mb':
                $rules[] = 'required';
                $rules[] = 'integer';
                $rules[] = 'min:1';
                $rules[] = 'max:100';
                break;

            case 'approval_threshold_amount':
                $rules[] = 'required';
                $rules[] = 'integer';
                $rules[] = 'min:0';
                break;

            case 'min_payed_percentage':
                $rules[] = 'required';
                $rules[] = 'integer';
                $rules[] = 'min:0';
                $rules[] = 'max:100';
                break;

            case 'payment_terms_text':
                $rules[] = 'required';
                $rules[] = 'string';
                $rules[] = 'max:1000';
                break;

            default:
                $rules[] = 'required';
                $rules[] = 'string';
                break;
        }

        return $rules;
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'setting.required' => 'The setting name is required.',
            'setting.string' => 'The setting name must be a string.',
            'value.required' => 'The setting value is required.',
            'value.boolean' => 'The setting must be a boolean value (true/false).',
            'value.integer' => 'The setting must be an integer.',
            'value.min' => 'The setting value is too small.',
            'value.max' => 'The setting value is too large.',
            'value.array' => 'The setting must be an array.',
            'value.in' => 'The selected setting value is invalid.',
            'group.string' => 'The group must be a string.',
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'setting' => 'setting name',
            'value' => 'setting value',
            'group' => 'settings group',
        ];
    }

    /**
     * Prepare the data for validation and ensure proper types
     */
    protected function prepareForValidation(): void
    {
        $setting = $this->input('setting');
        $value = $this->input('value');

        // Handle default_followers_users array conversion
        if ($setting === 'default_followers_users') {
            // Allow null values
            if (is_null($value)) {
                $convertedValue = null;
            } elseif (is_array($value)) {
                // Convert to integers, but preserve empty arrays and filter out invalid values
                if (empty($value)) {
                    $convertedValue = [];
                } else {
                    // Convert to integers and filter out 0, negative, and invalid values
                    $convertedValue = array_filter(array_map('intval', $value), function ($id) {
                        return $id > 0;
                    });
                }
            } else {
                $convertedValue = $value; // Keep as is for other types
            }

            $this->merge([
                'value' => $convertedValue
            ]);
        }
    }
}
