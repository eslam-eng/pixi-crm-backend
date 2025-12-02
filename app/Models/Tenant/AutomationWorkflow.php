<?php

namespace App\Models\Tenant;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AutomationWorkflow extends Model
{
    protected $fillable = [
        'name',
        'description',
        'automation_trigger_id',
        'is_active',
        'total_runs',
        'last_run_at',
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
        return $this->hasMany(AutomationWorkflowStep::class)->orderBy('order','asc');
    }

    /**
     * Scope for active workflows
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function incrementExecutions(): void
    {
        $this->increment('total_runs');
        $this->update(['last_run_at' => Carbon::now()]);
    }

  
    public function shouldExecute(array $data): bool
    {
        // Check if workflow is active
        if (!$this->is_active) {
            return false;
        }

        // Check if workflow has steps
        if ($this->steps->isEmpty()) {
            return false;
        }

        // Additional validation logic can be added here

        return true;
    }
}
