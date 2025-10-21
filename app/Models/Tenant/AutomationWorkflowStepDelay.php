<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class AutomationWorkflowStepDelay extends Model
{
    protected $fillable = [
        'automation_workflow_step_id',
        'duration',
        'unit',
    ];

    /**
     * Get the workflow step that owns this delay
     */
    public function automationWorkflowStep()
    {
        return $this->belongsTo(AutomationWorkflowStep::class);
    }

    /**
     * Get the workflow steps that use this delay
     */
    public function workflowSteps()
    {
        return $this->morphMany(AutomationWorkflowStep::class, 'target');
    }
}
