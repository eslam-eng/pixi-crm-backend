<?php

namespace App\Models\Tenant;

use App\Enums\DelayDurationUnits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AutomationWorkflowStepDelay extends Model
{
    protected $fillable = [
        'automation_workflow_step_id',
        'duration', // Duration value (e.g., 5)
        'unit',     // Time unit: 'minutes', 'hours', 'days'
    ];

    protected $casts = [
        'duration' => 'integer',
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

    /**
     * Calculate when the delay should end
     */
    public function calculateDelayUntil(): Carbon
    {
        $now = now();

        return match($this->unit) {
            DelayDurationUnits::MINUTES->value => $now->addMinutes($this->duration),
            DelayDurationUnits::HOURS->value => $now->addHours($this->duration),
            DelayDurationUnits::DAYS->value => $now->addDays($this->duration),
            default => $now,
        };
    }

    /**
     * Get human-readable delay description
     */
    public function getDelayDescription(): string
    {
        if ($this->duration && $this->unit) {
            return "{$this->duration} {$this->unit}";
        }

        return "No delay configured";
    }
}
