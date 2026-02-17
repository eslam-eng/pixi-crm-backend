<?php

namespace App\Traits;

use App\Models\Tenant\CustomField;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasCustomFields
{
    /**
     * Get the custom fields associated with the model.
     */
    public function customFields(): BelongsToMany
    {
        return $this->belongsToMany(CustomField::class, $this->getCustomFieldsPivotTable())
            ->withPivot('value')
            ->withTimestamps();
    }

    /**
     * Sync custom field values.
     *
     * @param array|null $customFields Array of [field_name => value] or [field_id => value]
     */
    public function syncCustomFields(?array $customFields): void
    {
        if (empty($customFields)) {
            return;
        }

        $syncData = [];

        foreach ($customFields as $identifier => $value) {
            $field = is_numeric($identifier)
                ? CustomField::find($identifier)
                : CustomField::where('name', $identifier)
                    ->where('module', $this->getCustomFieldsModule())
                    ->first();

            if ($field) {
                // If value is array (for multi-select/checkboxes), cast to JSON
                $formattedValue = is_array($value) ? json_encode($value) : $value;
                $syncData[$field->id] = ['value' => $formattedValue];
            }
        }

        if (!empty($syncData)) {
            $this->customFields()->syncWithoutDetaching($syncData);
        }
    }

    /**
     * Get the module name for custom fields (e.g., 'contacts', 'leads').
     */
    abstract protected function getCustomFieldsModule(): string;

    /**
     * Get the pivot table name (e.g., 'contact_custom_fields').
     */
    protected function getCustomFieldsPivotTable(): string
    {
        $module = \Illuminate\Support\Str::singular($this->getCustomFieldsModule());
        return "{$module}_custom_fields";
    }
}
