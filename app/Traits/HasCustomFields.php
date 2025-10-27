<?php

namespace App\Traits;

use App\Models\CustomField;
use App\Models\CustomFieldValue;

trait HasCustomFields
{
    public function customFields()
    {
        return $this->morphMany(CustomFieldValue::class, 'model');
    }

    public function getCustomFieldsAttribute(): array
    {
        $fields = CustomField::query()
            ->where('model_type', get_class($this))
            ->get();

        $result = [];

        foreach ($fields as $field) {
            $value = $this->customFields->where('custom_field_id', $field->id)->first();
            $result[$field->field_name] = $value ? $value->value : null;
        }

        return $result;
    }

    public function setCustomField($fieldName, $value)
    {
        $field = CustomField::where('tenant_id', $this->tenant_id)
            ->where('model_type', get_class($this))
            ->where('field_name', $fieldName)
            ->firstOrFail();

        $customFieldValue = $this->customFields()
            ->where('custom_field_id', $field->id)
            ->firstOrNew();

        $customFieldValue->value = $value;
        $customFieldValue->save();

        return $this;
    }

    protected function getValidationRulesForField(CustomField $field): array
    {
        $rules = [];

        // Add required rule if field is marked as required
        if ($field->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Add type-specific rules
        switch ($field->field_type) {
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'boolean':
                $rules[] = 'boolean';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'email':
                $rules[] = 'email';
                break;
            case 'string':
            default:
                $rules[] = 'string|max:255';
        }

        return $rules;
    }
}
