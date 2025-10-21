<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class AutomationWorkflowStepAction extends Model
{
    protected $fillable = [
        'automation_workflow_step_id',
        'automation_action_id',
    ];

    /**
     * Get the workflow step that owns this action
     */
    public function automationWorkflowStep()
    {
        return $this->belongsTo(AutomationWorkflowStep::class);
    }

    /**
     * Get the automation action
     */
    public function automationAction()
    {
        return $this->belongsTo(AutomationAction::class);
    }

    /**
     * Get the workflow steps that use this action
     */
    public function workflowSteps()
    {
        return $this->morphMany(AutomationWorkflowStep::class, 'target');
    }
}
