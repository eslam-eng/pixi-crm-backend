<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class AutomationWorkflowStepCondition extends Model
{
    protected $fillable = [
        'automation_workflow_step_id',
        'field_id',
        'operation',
        'value',
    ];

    /**
     * Get the workflow step that owns this condition
     */
    public function automationWorkflowStep()
    {
        return $this->belongsTo(AutomationWorkflowStep::class);
    }

    /**
     * Get the workflow steps that use this condition
     */
    public function workflowSteps()
    {
        return $this->morphMany(AutomationWorkflowStep::class, 'target');
    }

    public function field()
    {
        return $this->belongsTo(AutomationTriggerField::class);
    }
}
