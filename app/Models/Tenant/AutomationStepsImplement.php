<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AutomationStepsImplement extends Model
{
    protected $fillable = [
        'automation_workflow_id',
        'automation_workflow_step_id',
        'triggerable_type',
        'triggerable_id',
        'type',
        'step_order',
        'implemented',
        'step_data',
        'context_data',
        'implemented_at',
    ];

    protected $casts = [
        'implemented' => 'boolean',
        'step_data' => 'array',
        'context_data' => 'array',
        'implemented_at' => 'datetime',
    ];

    /**
     * Get the workflow that owns this step implementation
     */
    public function automationWorkflow(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflow::class);
    }

    /**
     * Get the original workflow step
     */
    public function automationWorkflowStep(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflowStep::class);
    }

    /**
     * Get the entity that triggered this workflow (polymorphic)
     */
    public function triggerable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the delay record for this step implementation
     */
    public function delay(): HasOne
    {
        return $this->hasOne(AutomationDelay::class);
    }

    /**
     * Scope for implemented steps
     */
    public function scopeImplemented($query)
    {
        return $query->where('implemented', true);
    }

    /**
     * Scope for pending steps
     */
    public function scopePending($query)
    {
        return $query->where('implemented', false);
    }

    /**
     * Scope for specific step types
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific triggerable type
     */
    public function scopeForTriggerable($query, $type, $id)
    {
        return $query->where('triggerable_type', $type)->where('triggerable_id', $id);
    }

    /**
     * Scope for ordering by step order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step_order');
    }

    /**
     * Mark this step as implemented
     */
    public function markAsImplemented(): bool
    {
        return $this->update([
            'implemented' => true,
            'implemented_at' => now(),
        ]);
    }

    /**
     * Get the next pending step for this workflow and triggerable
     */
    public static function getNextPendingStep($workflowId, $triggerableType, $triggerableId): ?self
    {
        return self::where('automation_workflow_id', $workflowId)
            ->where('triggerable_type', $triggerableType)
            ->where('triggerable_id', $triggerableId)
            ->pending()
            ->ordered()
            ->first();
    }

    /**
     * Get all pending steps for this workflow and triggerable
     */
    public static function getPendingSteps($workflowId, $triggerableType, $triggerableId)
    {
        return self::where('automation_workflow_id', $workflowId)
            ->where('triggerable_type', $triggerableType)
            ->where('triggerable_id', $triggerableId)
            ->pending()
            ->ordered()
            ->get();
    }
}
