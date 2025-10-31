<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class AutomationWorkflowStep extends Model
{
    protected $fillable = [
        'automation_workflow_id',
        'type',
        'order',
    ];

    /**
     * Get the workflow that owns this step
     */
    public function automationWorkflow()
    {
        return $this->belongsTo(AutomationWorkflow::class);
    }

    /**
     * Get the condition step data
     */
    public function condition()
    {
        return $this->hasOne(AutomationWorkflowStepCondition::class);
    }

    /**
     * Get the action step data
     */
    public function action()
    {
        return $this->hasOne(AutomationWorkflowStepAction::class);
    }

    /**
     * Get the delay step data
     */
    public function delay()
    {
        return $this->hasOne(AutomationWorkflowStepDelay::class);
    }

    /**
     * Get the step data based on type
     */
    public function getStepDataAttribute()
    {
        switch($this->type) {
            case 'condition':
                return $this->condition;
            case 'action':
                return $this->action;
            case 'delay':
                return $this->delay;
            default:
                return null;
        }
    }

    /**
     * Scope for ordering steps
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope for specific step types
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
