<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class AutomationTriggerField extends Model
{
    protected $fillable = [
        'automation_trigger_id',
        'field_name',
        'field_type',
        'field_label',
        'field_category',
        'is_relationship',
        'description',
        'example_value',
        'order',
    ];

    protected $casts = [
        'is_relationship' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the trigger that owns this field
     */
    public function automationTrigger()
    {
        return $this->belongsTo(AutomationTrigger::class);
    }

    /**
     * Scope for ordering fields
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope for direct fields only
     */
    public function scopeDirectFields($query)
    {
        return $query->where('is_relationship', false);
    }

    /**
     * Scope for relationship fields only
     */
    public function scopeRelationshipFields($query)
    {
        return $query->where('is_relationship', true);
    }
}
