<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class AutomationWorkflow extends Model
{
    protected $fillable = [
        'name',
        'description',
        'automation_trigger_id',
        'is_active',
        'total_runs',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the automation trigger for this workflow
     */
    public function automationTrigger()
    {
        return $this->belongsTo(AutomationTrigger::class);
    }

    /**
     * Get the workflow steps
     */
    public function steps()
    {
        return $this->hasMany(AutomationWorkflowStep::class)->orderBy('order');
    }

    /**
     * Scope for active workflows
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
