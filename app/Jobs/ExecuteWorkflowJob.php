<?php

namespace App\Jobs;

use App\Models\Tenant\AutomationWorkflow;
use App\Services\Tenant\Automation\AutomationWorkflowExecutorService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteWorkflowJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $workflowId,
        public array $context
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(AutomationWorkflowExecutorService $executionService): void
    {
        try {
            $workflow = AutomationWorkflow::with(['automationTrigger', 'steps.action', 'steps.condition', 'steps.delay'])
                ->findOrFail($this->workflowId);

            Log::info("Executing workflow job named: {$workflow->name}", [
                'workflow_id' => $this->workflowId,
                'workflow_name' => $workflow->name,
            ]);

            // Generate triggerable from context
            $triggerable = $this->getTriggerable();

            // Execute the workflow
            $result = $executionService->executeWorkflow($workflow, $triggerable, $this->context);

            if (!$result['success']) {
                Log::warning("Workflow execution unsuccessful", [
                    'workflow_id' => $this->workflowId,
                    'message' => $result['message'] ?? 'Unknown error',
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Workflow job failed", [
                'workflow_id' => $this->workflowId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Get the triggerable model based on the context
     */
    private function getTriggerable()
    {
        if (!isset($this->context['triggerable_type']) || !isset($this->context['triggerable_id'])) {
            throw new \InvalidArgumentException('Context must contain triggerable_type and triggerable_id');
        }

        $triggerableType = $this->context['triggerable_type'];
        $triggerableId = $this->context['triggerable_id'];

        Log::info('triggerable values is : ', [
            'triggerable_type' => $triggerableType,
            'triggerable_id' => $triggerableId,
        ]);

        // Resolve the model class
        $modelClass = $triggerableType;

        // Check if the class exists
        if (!class_exists($modelClass)) {
            throw new \InvalidArgumentException("Model class {$modelClass} does not exist");
        }

        // Find the model instance
        $triggerable = $modelClass::find($triggerableId);

        if (!$triggerable) {
            throw new \InvalidArgumentException("Triggerable {$modelClass}#{$triggerableId} not found");
        }

        return $triggerable;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ExecuteWorkflowJob failed permanently', [
            'workflow_id' => $this->workflowId,
            'error' => $exception->getMessage(),
        ]);
    }
}

