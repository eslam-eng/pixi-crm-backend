<?php

namespace App\Services\Tenant\Automation;

use App\Jobs\ExecuteWorkflowJob;
use App\Models\Tenant\AutomationWorkflow;
use App\Models\Tenant\AutomationStepsImplement;
use App\Models\Tenant\AutomationWorkflowStep;
use App\Models\Tenant\AutomationDelay;
use App\Models\Tenant\Contact;
use App\Models\Tenant\Lead;
use App\Models\Stage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomationWorkflowFireService
{
    protected AutomationWorkflowExecutorService $executorService;

    public function __construct(AutomationWorkflowExecutorService $executorService)
    {
        $this->executorService = $executorService;
    }
    /**
     * Fire workflows for a specific trigger
     */
    public function fireWorkflows(string $triggerKey, Model $triggerable): void
    {
        try {
            // Get all active workflows for this trigger
            $workflows = AutomationWorkflow::with(['automationTrigger', 'steps.condition', 'steps.action', 'steps.delay'])
                ->whereHas('automationTrigger', function ($query) use ($triggerKey) {
                    $query->where('key', $triggerKey)->where('is_active', true);
                })
                ->where('is_active', true)
                ->get();

            if ($workflows->isEmpty()) {
                Log::info("No active workflows found for trigger: {$triggerKey}");
                return;
            }

            foreach ($workflows as $workflow) {
                $this->processWorkflow($workflow, $triggerable);
            }

        } catch (\Exception $e) {
            Log::error("Error firing workflows for trigger {$triggerKey}: " . $e->getMessage(), [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id,
                'exception' => $e
            ]);
        }
    }

    public function fireTrigger(string $triggerKey, array $context): void
    {
        Log::info("Trigger fired: {$triggerKey}", [
            'context_keys' => array_keys($context),
        ]);

        // Find all active workflows for this trigger
        $workflows = AutomationWorkflow::active()
            ->whereHas('automationTrigger', function($query) use ($triggerKey) {
                $query->where('key', $triggerKey) 
                    ->where('is_active', true);
            })
            ->with(['automationTrigger', 'steps'])
            ->get();

        Log::info("Found {$workflows->count()} workflows for trigger: {$triggerKey}");

        // Queue each workflow for execution
        foreach ($workflows as $workflow) {
            Log::info("Queuing workflow: {$workflow->name}", [
                'workflow_id' => $workflow->id,
            ]);

            ExecuteWorkflowJob::dispatch($workflow->id, $context);
        }
    }

    /**
     * Process a specific workflow for a triggerable entity
     */
    private function processWorkflow(AutomationWorkflow $workflow, Model $triggerable): void
    {
        DB::transaction(function () use ($workflow, $triggerable) {
            try {
                // Create step implementations for this workflow
                $this->createStepImplementations($workflow, $triggerable);

                // Increment workflow run count
                $workflow->increment('total_runs');

                Log::info("Workflow {$workflow->name} fired for " . get_class($triggerable) . " ID: {$triggerable->id}");

            } catch (\Exception $e) {
                Log::error("Error processing workflow {$workflow->id}: " . $e->getMessage(), [
                    'workflow_id' => $workflow->id,
                    'triggerable_type' => get_class($triggerable),
                    'triggerable_id' => $triggerable->id,
                    'exception' => $e
                ]);
                throw $e;
            }
        });
    }

    /**
     * Create step implementations for a workflow
     */
    private function createStepImplementations(AutomationWorkflow $workflow, Model $triggerable): void
    {
        $steps = $workflow->steps()->ordered()->get();

        foreach ($steps as $step) {
            $stepData = $this->prepareStepData($step);
            $contextData = $this->prepareContextData($triggerable);

            $stepImplement = AutomationStepsImplement::create([
                'automation_workflow_id' => $workflow->id,
                'automation_workflow_step_id' => $step->id,
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id,
                'type' => $step->type,
                'step_order' => $step->order,
                'implemented' => false,
                'step_data' => $stepData,
                'context_data' => $contextData,
            ]);

            // If it's a delay step, create a delay record
            if ($step->type === 'delay') {
                $this->createDelayRecord($stepImplement, $step);
            }
        }

        // Execute steps step by step
        $this->executeStepsSequentially($workflow, $triggerable);
    }

    /**
     * Execute steps sequentially, handling delays appropriately
     */
    private function executeStepsSequentially(AutomationWorkflow $workflow, Model $triggerable): void
    {
        $steps = AutomationStepsImplement::where('automation_workflow_id', $workflow->id)
            ->where('triggerable_type', get_class($triggerable))
            ->where('triggerable_id', $triggerable->id)
            ->ordered()
            ->get();

        foreach ($steps as $stepImplement) {
            // Skip if already implemented
            if ($stepImplement->implemented) {
                continue;
            }

            // Handle delay steps differently
            if ($stepImplement->type === 'delay') {
                // For delay steps, we just create the delay record and wait
                // The delay will be processed by the cron job
                Log::info("Delay step {$stepImplement->id} created, waiting for delay to complete", [
                    'step_id' => $stepImplement->id,
                    'triggerable_type' => $stepImplement->triggerable_type,
                    'triggerable_id' => $stepImplement->triggerable_id,
                ]);
                break; // Stop execution until delay is processed
            }

            // Execute immediate steps (condition, action)
            try {
                $success = $this->executorService->executeStep($stepImplement, $stepImplement->context_data ?? []);
                
                if ($success) {
                    Log::info("Step {$stepImplement->id} executed successfully", [
                        'step_type' => $stepImplement->type,
                        'step_order' => $stepImplement->step_order,
                        'triggerable_type' => $stepImplement->triggerable_type,
                        'triggerable_id' => $stepImplement->triggerable_id,
                    ]);
                } else {
                    Log::warning("Step {$stepImplement->id} execution failed", [
                        'step_type' => $stepImplement->type,
                        'step_order' => $stepImplement->step_order,
                        'triggerable_type' => $stepImplement->triggerable_type,
                        'triggerable_id' => $stepImplement->triggerable_id,
                    ]);
                    break; // Stop execution on failure
                }
            } catch (\Exception $e) {
                Log::error("Error executing step {$stepImplement->id}: " . $e->getMessage(), [
                    'step_type' => $stepImplement->type,
                    'step_order' => $stepImplement->step_order,
                    'triggerable_type' => $stepImplement->triggerable_type,
                    'triggerable_id' => $stepImplement->triggerable_id,
                    'exception' => $e
                ]);
                break; // Stop execution on error
            }
        }
    }

    /**
     * Prepare step data based on step type
     */
    private function prepareStepData(AutomationWorkflowStep $step): array
    {
        $stepData = [];

        switch ($step->type) {
            case 'condition':
                if ($step->condition) {
                    $stepData = [
                        'field' => $step->condition->field,
                        'operation' => $step->condition->operation,
                        'value' => $step->condition->value,
                    ];
                }
                break;

            case 'action':
                if ($step->action) {
                    $stepData = [
                        'automation_action_id' => $step->action->automation_action_id,
                    ];
                }
                break;

            case 'delay':
                if ($step->delay) {
                    $stepData = [
                        'duration' => $step->delay->duration,
                        'unit' => $step->delay->unit,
                    ];
                }
                break;
        }

        return $stepData;
    }

    /**
     * Prepare context data for the triggerable entity
     */
    private function prepareContextData(Model $triggerable): array
    {
        $contextData = [];

        // Add basic entity data
        $contextData['entity_type'] = get_class($triggerable);
        $contextData['entity_id'] = $triggerable->id;
        $contextData['entity_data'] = $triggerable->toArray();

        // Add specific data based on entity type
        if ($triggerable instanceof Contact) {
            $contextData['contact_data'] = [
                'name' => $triggerable->name,
                'email' => $triggerable->email,
                'company_name' => $triggerable->company_name,
                'source_id' => $triggerable->source_id,
                'user_id' => $triggerable->user_id,
            ];
        }

        return $contextData;
    }

    /**
     * Create a delay record for delay steps
     */
    private function createDelayRecord(AutomationStepsImplement $stepImplement, AutomationWorkflowStep $step): void
    {
        if ($step->delay) {
            $executeAt = AutomationDelay::calculateExecuteAt(
                $step->delay->duration,
                $step->delay->unit
            );

            AutomationDelay::create([
                'automation_steps_implement_id' => $stepImplement->id,
                'duration' => $step->delay->duration,
                'unit' => $step->delay->unit,
                'execute_at' => $executeAt,
                'processed' => false,
                'context_data' => $stepImplement->context_data,
            ]);
        }
    }

    /**
     * Fire workflows specifically for contact creation
     */
    public function fireContactCreatedWorkflows(Contact $contact): void
    {
        $this->fireWorkflows('contact_created', $contact);
    }

    /**
     * Fire workflows specifically for contact updates
     */
    public function fireContactUpdatedWorkflows(Contact $contact, array $changedFields = []): void
    {
        $this->fireWorkflows('contact_updated', $contact);
    }

    /**
     * Fire workflows specifically for contact lead qualification
     */
    public function fireContactLeadQualifiedWorkflows(Contact $contact, Lead $lead, array $qualificationData = []): void
    {
        $this->fireWorkflows('contact_lead_qualified', $contact);
    }

    /**
     * Fire workflows specifically for opportunity creation
     */
    public function fireOpportunityCreatedWorkflows(Lead $opportunity, array $creationData = []): void
    {
        $this->fireWorkflows('opportunity_created', $opportunity);
    }

    /**
     * Fire workflows specifically for contact tag addition
     */
    public function fireContactTagAddedWorkflows(Contact $contact, string $tag, array $tagData = []): void
    {
        $this->fireWorkflows('contact_tag_added', $contact);
    }

    /**
     * Fire workflows specifically for opportunity stage changes
     */
    public function fireOpportunityStageChangedWorkflows(Lead $opportunity, ?Stage $oldStage, ?Stage $newStage, array $stageChangeData = []): void
    {
        $this->fireWorkflows('opportunity_stage_changed', $opportunity);
    }

    /**
     * Get pending step implementations for a specific entity
     */
    public function getPendingStepsForEntity(Model $triggerable): \Illuminate\Database\Eloquent\Collection
    {
        return AutomationStepsImplement::where('triggerable_type', get_class($triggerable))
            ->where('triggerable_id', $triggerable->id)
            ->pending()
            ->ordered()
            ->get();
    }

    /**
     * Get all pending step implementations
     */
    public function getAllPendingSteps(): \Illuminate\Database\Eloquent\Collection
    {
        return AutomationStepsImplement::pending()
            ->ordered()
            ->with(['automationWorkflow', 'automationWorkflowStep', 'triggerable'])
            ->get();
    }

    /**
     * Continue workflow execution after a delay has been processed
     */
    public function continueWorkflowAfterDelay(AutomationDelay $delay): void
    {
        try {
            $stepImplement = $delay->automationStepsImplement;
            
            if (!$stepImplement) {
                Log::warning("No step implementation found for delay {$delay->id}");
                return;
            }

            // Mark the delay step as implemented
            $stepImplement->markAsImplemented();
            
            // Mark the delay as processed
            $delay->markAsProcessed();

            Log::info("Delay {$delay->id} processed, continuing workflow", [
                'step_id' => $stepImplement->id,
                'triggerable_type' => $stepImplement->triggerable_type,
                'triggerable_id' => $stepImplement->triggerable_id,
            ]);

            // Continue executing the remaining steps
            $this->executeStepsSequentially(
                $stepImplement->automationWorkflow,
                $stepImplement->triggerable
            );

        } catch (\Exception $e) {
            Log::error("Error continuing workflow after delay {$delay->id}: " . $e->getMessage(), [
                'delay_id' => $delay->id,
                'exception' => $e
            ]);
        }
    }

    /**
     * Process all ready delays and continue their workflows
     */
    public function processReadyDelays(): void
    {
        $readyDelays = AutomationDelay::getReadyToExecute();

        foreach ($readyDelays as $delay) {
            $this->continueWorkflowAfterDelay($delay);
        }

        Log::info("Processed {count} ready delays", ['count' => $readyDelays->count()]);
    }
}
