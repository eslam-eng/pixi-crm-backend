<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected $fillable = [
        'title',
        'description',
        'slug',
        'is_active',
        'submissions_count'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function incrementSubmissions(): void
    {
        $this->increment('submissions_count');
    }

    // Get fields that should be shown based on current data
    public function getVisibleFields(array $formData = [])
    {
        return $this->fields->filter(function ($field) use ($formData) {
            return $field->shouldBeShown($formData);
        });
    }

    /**
     * Get validation rules based on current form data
     */
    public function getDynamicValidationRules(array $formData = []): array
    {
        $rules = [];

        foreach ($this->getVisibleFields($formData) as $field) {
            $fieldRules = [];

            if ($field['required']) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add type-specific rules
            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    break;
                case 'select':
                case 'radio':
                    if (!empty($field['options'])) {
                        $fieldRules[] = 'in:' . implode(',', $field['options']);
                    }
                    break;
            }

            $rules[$field['name']] = $fieldRules;
        }

        return $rules;
    }

    // Get validation rules based on current form data
    public function getValidationRules(array $formData = []): array
    {
        $rules = [];

        foreach ($this->getVisibleFields($formData) as $field) {
            $fieldRules = [];

            if ($field->isActuallyRequired($formData)) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add type-specific rules
            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    break;
            }

            $rules[$field->name] = $fieldRules;
        }

        return $rules;
    }

    // Get conditional fields
    public function conditionalFields()
    {
        return $this->hasMany(FormField::class)
            ->where('is_conditional', true)
            ->orderBy('order');
    }

    // Get non-conditional fields
    public function unconditionalFields()
    {
        return $this->hasMany(FormField::class)
            ->where('is_conditional', false)
            ->orderBy('order');
    }
}
