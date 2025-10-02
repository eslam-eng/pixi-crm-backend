<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    protected $fillable = [
        'form_id',
        'name',
        'label',
        'type',
        'options',
        'required',
        'placeholder',
        'order',
        'is_conditional',
        'depends_on_field_id',
        'depends_on_value',
        'condition_type'
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'is_conditional' => 'boolean',
    ];

    protected $attributes = [
        'is_conditional' => false,
        'condition_type' => 'equals',
        'required' => false,
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    // Relationship to the field this field depends on
    public function dependsOn()
    {
        return $this->belongsTo(FormField::class, 'depends_on_field_id');
    }

    // Check if this field should be shown based on form data
    public function shouldBeShown(array $formData): bool
    {
        if (!$this->is_conditional || !$this->dependsOn) {
            return true;
        }

        $dependentFieldValue = $formData[$this->dependsOn->name] ?? null;

        if ($dependentFieldValue === null) {
            return false;
        }

        return match ($this->condition_type) {
            'equals' => $dependentFieldValue == $this->depends_on_value,
            'not_equals' => $dependentFieldValue != $this->depends_on_value,
            'contains' => str_contains($dependentFieldValue, $this->depends_on_value),
            'in' => in_array($dependentFieldValue, (array) $this->depends_on_value),
            'greater_than' => $dependentFieldValue > $this->depends_on_value,
            'less_than' => $dependentFieldValue < $this->depends_on_value,
            default => $dependentFieldValue == $this->depends_on_value,
        };
    }

    // Check if this field is required based on conditions
    public function isActuallyRequired(array $formData): bool
    {
        if (!$this->shouldBeShown($formData)) {
            return false;
        }

        return $this->required;
    }
}
