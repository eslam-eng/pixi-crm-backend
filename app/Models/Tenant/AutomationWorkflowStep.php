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

      /**
     * Execute this step
     */
    public function execute(array $context): array
    {
        switch ($this->type) {
            case 'action':
                return $this->executeAction($context);
            case 'condition':
                return $this->evaluateCondition($context);
            case 'delay':
                return $this->handleDelay($context);
            default:
                return ['success' => false, 'message' => 'Unknown step type'];
        }
    }

    /**
     * Execute the single action in this step
     */
    private function executeAction(array $context): array
    {
        // Get the single action for this step
        $action = $this->action;
        
        if (!$action) {
            return [
                'success' => false,
                'message' => 'No action found for this step',
            ];
        }

        // Action execution is handled by the executor service
        // This method should return a structure indicating the action needs to be executed
        return [
            'success' => true,
            'action_id' => $action->automation_action_id,
            'action' => $action,
            'message' => 'Action ready for execution',
        ];
    }

    /**
     * Evaluate the single condition in this step
     */
    private function evaluateConditions(array $context): array
    {
        // Get the single condition for this step
        $condition = $this->condition;
        
        if (!$condition) {
            // No condition means it always passes
            return [
                'success' => true,
                'message' => 'No condition to evaluate',
            ];
        }

        // Condition evaluation is handled by the executor service
        // This method should return a structure indicating the condition needs to be evaluated
        return [
            'success' => true,
            'condition' => $condition,
            'field' => $condition->field,
            'operation' => $condition->operation,
            'value' => $condition->value,
            'message' => 'Condition ready for evaluation',
        ];
    }

    /**
     * Handle delay for this step
     */
    private function handleDelay(array $context): array
    {
        if ($this->delay) {
            return [
                'success' => true,
                'delay_until' => $this->delay->calculateDelayUntil(),
            ];
        }
        
        return ['success' => true];
    }
}
