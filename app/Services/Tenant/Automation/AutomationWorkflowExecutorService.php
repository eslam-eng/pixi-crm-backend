<?php

namespace App\Services\Tenant\Automation;

use App\DTO\Contact\ContactDTO;
use App\DTO\Tenant\LeadDTO;
use App\Enums\AutomationAssignStrategiesEnum;
use App\Enums\OpportunityStatus;
use App\Enums\TaskStatusEnum;
use App\Jobs\ContinueWorkflowExecutionJob;
use App\Models\Tenant\AutomationStepsImplement;
use App\Models\Tenant\AutomationDelay;
use App\Models\Tenant\AutomationAction;
use App\Models\Tenant\AutomationLog;
use App\Models\Tenant\AutomationWorkflow;
use App\Models\Tenant\Contact;
use App\Models\Tenant\Deal;
use App\Models\Tenant\Lead;
use App\Models\Tenant\Task;
use App\Models\Tenant\User;
use App\Services\ContactService;
use App\Services\LeadService;
use App\Services\PipelineService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mail;

class AutomationWorkflowExecutorService
{
    public LeadService $opportunityService;
    public ConditionService $conditionService;
    public PipelineService $pipelineService;
    public ContactService $contactService;
    public function __construct(
        LeadService $opportunityService,
        ConditionService $conditionService,
        PipelineService $pipelineService,
        ContactService $contactService
    ) {
        $this->opportunityService = $opportunityService;
        $this->conditionService = $conditionService;
        $this->pipelineService = $pipelineService;
        $this->contactService = $contactService;
    }

    // /**
    //  * Execute a specific step implementation
    //  */
    // public function executeStep(AutomationStepsImplement $stepImplement): bool
    // {
    //     try {
    //         DB::transaction(function () use ($stepImplement) {
    //             switch($stepImplement->type) {
    //                 case 'condition':
    //                     $result = $this->executeConditionStep($stepImplement);
    //                     break;
    //                 case 'action':
    //                     $result = $this->executeActionStep($stepImplement);
    //                     break;
    //                 case 'delay':
    //                     $result = $this->executeDelayStep($stepImplement);
    //                     break;
    //                 default:
    //                     throw new \InvalidArgumentException("Unknown step type: {$stepImplement->type}");
    //             }

    //             if ($result) {
    //                 $stepImplement->markAsImplemented();
    //                 Log::info("Step {$stepImplement->id} executed successfully", [
    //                     'step_type' => $stepImplement->type,
    //                     'triggerable_type' => $stepImplement->triggerable_type,
    //                     'triggerable_id' => $stepImplement->triggerable_id,
    //                 ]);
    //             }
    //         });

    //         return true;

    //     } catch (\Exception $e) {
    //         Log::error("Error executing step {$stepImplement->id}: " . $e->getMessage(), [
    //             'step_id' => $stepImplement->id,
    //             'step_type' => $stepImplement->type,
    //             'triggerable_type' => $stepImplement->triggerable_type,
    //             'triggerable_id' => $stepImplement->triggerable_id,
    //             'exception' => $e
    //         ]);
    //         return false;
    //     }
    // }


    /**
     * Execute a single workflow step
     */

    /**
     * Execute a workflow
     */
    public function executeWorkflow(AutomationWorkflow $workflow, Model $triggerable, array $context = []): array
    {
        Log::info("Executing workflow: {$workflow->name}", [
            'workflow_id' => $workflow->id,
            'trigger' => $workflow->automationTrigger->key,
            'triggerable_type' => get_class($triggerable),
            'triggerable_id' => $triggerable->id,
        ]);

        // Start transaction
        DB::beginTransaction();

        try {
            // 1. Prepare steps implementation (save to DB)
            $this->prepareStepsImplementation($workflow, $triggerable, $context);

            // 2. Execute pending steps
            $results = $this->executePendingSteps($workflow, $triggerable);

            DB::commit();

            Log::info("Workflow execution initiated/completed: {$workflow->name}");

            return [
                'success' => true,
                'message' => 'Workflow execution processed',
                'results' => $results,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Workflow execution failed: {$workflow->name}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Prepare steps implementation by saving them to database
     */
    public function prepareStepsImplementation(AutomationWorkflow $workflow, Model $triggerable, array $context = []): void
    {
        $steps = $workflow->steps()->orderBy('order')->get();

        foreach ($steps as $step) {
            // Prepare step data based on type
            $stepData = [];
            if ($step->type === 'condition' && $step->condition) {
                $stepData = [
                    'field' => $step->condition->field,
                    'operation' => $step->condition->operation,
                    'value' => $step->condition->value,
                ];
            } elseif ($step->type === 'action' && $step->action) {
                $stepData = [
                    'automation_action_id' => $step->action->automation_action_id,
                    'configs' => $step->action->configs,
                ];
                // Merge configs into stepData for easier access
                if (is_array($step->action->configs)) {
                    $stepData = array_merge($stepData, $step->action->configs);
                }
            } elseif ($step->type === 'delay' && $step->delay) {
                $stepData = [
                    'duration' => $step->delay->duration,
                    'unit' => $step->delay->unit,
                ];
            }

            AutomationStepsImplement::create([
                'automation_workflow_id' => $workflow->id,
                'automation_workflow_step_id' => $step->id,
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id,
                'type' => $step->type,
                'step_order' => $step->order,
                'implemented' => false,
                'step_data' => $stepData,
                'context_data' => $context,
            ]);
        }
    }

    /**
     * Execute pending steps for a workflow and triggerable
     */
    public function executePendingSteps(AutomationWorkflow $workflow, Model $triggerable): array
    {
        $pendingSteps = AutomationStepsImplement::where('automation_workflow_id', $workflow->id)
            ->where('triggerable_type', get_class($triggerable))
            ->where('triggerable_id', $triggerable->id)
            ->where('implemented', false)
            ->orderBy('step_order')
            ->get();

        $results = [];

        foreach ($pendingSteps as $stepImplement) {
            $result = $this->executeStep($stepImplement);
            $results[] = $result;

            if (!$result['success']) {
                // Stop execution if step failed (and maybe configured to stop)
                // For now, we'll just log it and maybe stop?
                // Let's assume we stop on failure for now or if it's a delay step that paused execution
                if (isset($result['delayed']) && $result['delayed']) {
                    break;
                }
            }
        }

        return $results;
    }

    /**
     * Execute a single workflow step implementation
     */
    public function executeStep(AutomationStepsImplement $stepImplement): array
    {
        try {
            $result = false;
            switch ($stepImplement->type) {
                case 'condition':
                    $result = $this->executeConditionStep($stepImplement);
                    break;
                case 'action':
                    $result = $this->executeActionStep($stepImplement);
                    break;
                case 'delay':
                    $result = $this->executeDelayStep($stepImplement);
                    break;
            }

            if ($result) {
                // If result is true (success) or if it's a delay step that was handled
                // For delay steps, executeDelayStep returns true if it successfully scheduled/processed the delay
                // But if it's a new delay, we might want to return a specific status to stop the loop

                if ($stepImplement->type === 'delay' && !$stepImplement->implemented) {
                    // It was a new delay step, so we stop here
                    return [
                        'success' => true,
                        'message' => 'Workflow paused for delay',
                        'delayed' => true,
                    ];
                }

                $stepImplement->markAsImplemented();
                return [
                    'success' => true,
                    'message' => 'Step executed successfully',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Step execution failed',
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Start execution log
     */
    private function startExecutionLog(AutomationWorkflow $workflow, array $context)
    {
        return AutomationLog::create([
            'automation_type' => $workflow->automationTrigger->key,
            'entity_type' => 'AutomationWorkflow',
            'entity_id' => $workflow->id,
            'status' => 'running',
            'action_taken' => 'Workflow started',
            'metadata' => $context,
            'error_message' => null,
            'triggered_by_id' => null,

        ]);
    }

    /**
     * Log step execution
     */
    private function logStepExecution($executionLog, $step, array $result): void
    {
        // Add step result to execution log
        $steps = $executionLog->steps ?? [];
        $steps[] = [
            'step_id' => $step->id,
            'step_name' => $step->name,
            'result' => $result,
            'executed_at' => now()->toIso8601String(),
        ];

        $executionLog->update(['steps' => $steps]);
    }

    /**
     * Complete execution log
     */
    private function completeExecutionLog($executionLog, bool $success, ?string $error = null): void
    {
        $executionLog->update([
            'status' => $success ? 'success' : 'failed',
            'completed_at' => now(),
            'error_message' => $error,
        ]);
    }


    /**
     * Schedule delayed execution
     */
    private function scheduleDelayedExecution(AutomationWorkflow $workflow, $step, array $context, $delayUntil): void
    {
        // Queue a job to continue execution after delay
        ContinueWorkflowExecutionJob::dispatch($workflow->id, $step->id, $context)
            ->delay($delayUntil);
    }

    /**
     * Execute a condition step
     */
    private function executeConditionStep(AutomationStepsImplement $stepImplement): bool
    {
        $stepData = $stepImplement->step_data;
        $contextData = $stepImplement->context_data;

        if (!$stepData || !isset($stepData['field'], $stepData['operation'], $stepData['value'])) {
            Log::warning("Invalid condition step data for step {$stepImplement->id}");
            return false;
        }

        $field = $stepData['field'];
        $operation = $stepData['operation'];
        $expectedValue = $stepData['value'];

        // Get the actual value from the triggerable entity
        $actualValue = $this->getFieldValue($stepImplement->triggerable, $field);

        $result = $this->evaluateCondition($actualValue, $operation, $expectedValue);

        Log::info("Condition step {$stepImplement->id} evaluated", [
            'field' => $field,
            'operation' => $operation,
            'expected_value' => $expectedValue,
            'actual_value' => $actualValue,
            'result' => $result
        ]);

        return $result;
    }

    /**
     * Execute an action step
     */
    private function executeActionStep(AutomationStepsImplement $stepImplement): bool
    {
        $stepData = $stepImplement->step_data;
        $contextData = $stepImplement->context_data;

        if (!$stepData || !isset($stepData['automation_action_id'])) {
            Log::warning("Invalid action step data for step {$stepImplement->id}");
            return false;
        }

        $actionId = $stepData['automation_action_id'];
        $action = AutomationAction::find($actionId);

        if (!$action) {
            Log::warning("Automation action {$actionId} not found for step {$stepImplement->id}");
            return false;
        }

        // Execute the action based on its key (pass stepData for actions that need config values)
        $result = $this->executeActionByKey($action->key, $stepImplement->triggerable, $contextData, $stepImplement, $stepData);

        Log::info("Action step {$stepImplement->id} executed", [
            'action_id' => $actionId,
            'action_key' => $action->key,
            'triggerable_type' => $stepImplement->triggerable_type,
            'triggerable_id' => $stepImplement->triggerable_id,
            'result' => $result
        ]);

        return $result;
    }

    /**
     * Execute a delay step
     */
    private function executeDelayStep(AutomationStepsImplement $stepImplement): bool
    {
        // Delay steps are handled by the cron job, so we just mark them as implemented
        // when they are processed by the ProcessAutomationDelays command
        Log::info("Delay step {$stepImplement->id} processed", [
            'step_id' => $stepImplement->id,
            'triggerable_type' => $stepImplement->triggerable_type,
            'triggerable_id' => $stepImplement->triggerable_id,
        ]);

        $this->createDelayRecord($stepImplement);

        return true;
    }

    /**
     * Get contact_id from triggerable based on its type
     */
    private function getContactIdFromTriggerable(Model $triggerable): ?int
    {
        // If triggerable is Contact, use its id
        if ($triggerable instanceof Contact) {
            return $triggerable->id;
        }

        // If triggerable is Lead/Opportunity, use contact_id
        if ($triggerable instanceof Lead) {
            return $triggerable->contact_id;
        }

        // If triggerable is Task, get contact_id through lead
        if ($triggerable instanceof Task) {
            if ($triggerable->lead_id) {
                $lead = Lead::find($triggerable->lead_id);
                return $lead?->contact_id;
            }
        }

        // If triggerable is Deal, get contact_id through lead
        if ($triggerable instanceof Deal) {
            if ($triggerable->lead_id) {
                $lead = $this->opportunityService->findById($triggerable->lead_id);
                return $lead?->contact_id;
            }
        }

        // If triggerable is FormSubmission, check if contact_id is in data
        if ($triggerable instanceof \App\Models\Tenant\FormSubmission) {
            $data = $triggerable->data ?? [];
            if (isset($data['contact_id'])) {
                return $data['contact_id'];
            }
        }

        Log::warning("Could not extract contact_id from triggerable", [
            'triggerable_type' => get_class($triggerable),
            'triggerable_id' => $triggerable->id ?? null
        ]);

        return null;
    }

    /**
     * Get contact_id from triggerable based on its type
     */
    private function getContactUserIdFromTriggerable(Model $triggerable): ?int
    {
        // If triggerable is Contact, use its id
        if ($triggerable instanceof Contact) {
            return $triggerable->user_id;
        }

        // If triggerable is Lead/Opportunity, use contact_id
        if ($triggerable instanceof Lead) {
            return $triggerable->contact->user_id;
        }

        // If triggerable is Task, get contact_id through lead
        if ($triggerable instanceof Task) {
            if ($triggerable->lead_id) {
                $lead = Lead::find($triggerable->lead_id);
                return $lead?->contact->user_id;
            }
        }

        // If triggerable is Deal, get contact_id through lead
        if ($triggerable instanceof Deal) {
            if ($triggerable->lead_id) {
                $lead = $this->opportunityService->findById($triggerable->lead_id);
                return $lead?->contact->user_id;
            }
        }

        // If triggerable is FormSubmission, check if contact_id is in data
        if ($triggerable instanceof \App\Models\Tenant\FormSubmission) {
            $data = $triggerable->data ?? [];
            if (isset($data['contact_id'])) {
                return $data['contact_id'];
            }
        }

        Log::warning("Could not extract contact_id from triggerable", [
            'triggerable_type' => get_class($triggerable),
            'triggerable_id' => $triggerable->id ?? null
        ]);

        return null;
    }

    /**
     * Get field value from triggerable entity
     */
    private function getFieldValue(Model $triggerable, string $field): mixed
    {
        // Handle nested field access (e.g., 'user.name', 'source.title')
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $value = $triggerable;

            foreach ($parts as $part) {
                if ($value && is_object($value)) {
                    $value = $value->{$part} ?? null;
                } else {
                    return null;
                }
            }

            return $value;
        }

        // Direct field access
        return $triggerable->{$field} ?? null;
    }

    /**
     * Evaluate a condition
     */
    private function evaluateCondition(mixed $actualValue, string $operation, mixed $expectedValue): bool
    {
        switch ($operation) {
            case 'equals':
                return $actualValue == $expectedValue;
            case 'not_equals':
                return $actualValue != $expectedValue;
            case 'contains':
                return is_string($actualValue) && str_contains($actualValue, $expectedValue);
            case 'not_contains':
                return is_string($actualValue) && !str_contains($actualValue, $expectedValue);
            case 'greater_than':
                return is_numeric($actualValue) && is_numeric($expectedValue) && $actualValue > $expectedValue;
            case 'less_than':
                return is_numeric($actualValue) && is_numeric($expectedValue) && $actualValue < $expectedValue;
            case 'greater_than_or_equal':
                return is_numeric($actualValue) && is_numeric($expectedValue) && $actualValue >= $expectedValue;
            case 'less_than_or_equal':
                return is_numeric($actualValue) && is_numeric($expectedValue) && $actualValue <= $expectedValue;
            case 'is_empty':
                return empty($actualValue);
            case 'is_not_empty':
                return !empty($actualValue);
            case 'is_null':
                return is_null($actualValue);
            case 'is_not_null':
                return !is_null($actualValue);
            default:
                return false;
        }
    }

    /**
     * Execute action by key
     */
    private function executeActionByKey(string $actionKey, Model $triggerable, array $contextData, ?AutomationStepsImplement $stepImplement = null, ?array $stepData = null): bool
    {
        switch ($actionKey) {
            // Email Actions
            case 'send_welcome_email':
                return $this->executeSendWelcomeEmailAction($triggerable, $contextData);
            case 'move_stage':
                return $this->executeMoveStageAction($triggerable, $contextData);

            // Email Actions
            case 'send_email':
                return $this->executeSendEmailAction($triggerable, $contextData);
            case 'send_invoice_email':
                return $this->executeSendInvoiceEmailAction($triggerable, $contextData);

            // Escalation Actions
            case 'escalate':
                return $this->executeEscalateAction($triggerable, $contextData);
            case 'notify_manager':
                return $this->executeNotifyManagerAction($triggerable, $contextData);
            case 'escalate_task':
                return $this->executeEscalateTaskAction($triggerable, $contextData);

            // Task Actions
            case 'create_onboarding_task':
                return $this->executeCreateOnboardingTaskAction($triggerable, $contextData);

            // Tagging Actions
            case 'tag_and_reopen_later':
                return $this->executeTagAndReopenLaterAction($triggerable, $contextData);

            // Notification Actions
            case 'notify_admin':
                return $this->executeNotifyAdminAction($triggerable, $contextData);
            case 'notify_owner':
                return $this->executeNotifyOwnerAction($triggerable, $contextData);

            // Reminder Actions
            case 'send_reminder_and_task':
                return $this->executeSendReminderAndTaskAction($triggerable, $contextData);
            case 'send_reminder':
                return $this->executeSendReminderAction($triggerable, $contextData);

            // Complex Actions
            // case 'create_contact':
            //     return $this->executeCreateContactAction($triggerable, $contextData);

            case 'create_opportunity':
                return $this->executeCreateOpportunityAction($triggerable, $contextData);

            case 'assign_to_sales':
                return $this->executeAssignToSalesAction($triggerable, $contextData, $stepImplement, $stepData);
            default:
                return $this->executeCustomAction($actionKey, $triggerable, $contextData);
        }
    }

    /**
     * Execute send email action
     */
    private function executeSendEmailAction(Model $triggerable, array $contextData): bool
    {
        try {
            $contactId = $this->getContactIdFromTriggerable($triggerable);

            if (!$contactId) {
                Log::warning("Could not extract contact for send email action");
                return false;
            }

            $contact = Contact::find($contactId);

            if (!$contact || !$contact->email) {
                Log::warning("Contact not found or has no email", ['contact_id' => $contactId]);
                return false;
            }

            // Get email details from context
            $subject = $contextData['subject'] ?? 'Important Update';
            $message = $contextData['message'] ?? '';
            $template = $contextData['template'] ?? null;

            // TODO: Integrate with your email service/queue
            // if ($template) {
            //     Mail::to($contact->email)->send(new Templa($contact, $template, $contextData));
            // } else {
            //     Mail::to($contact->email)->send(new GenericEmail($contact, $subject, $message));
            // }

            Log::info("Send email action executed", [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'subject' => $subject,
                'template' => $template
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error executing send email action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute assign user action
     */
    private function executeAssignUserAction(Model $triggerable, array $contextData): bool
    {
        // Implementation for assigning user
        Log::info("Assign user action executed", ['triggerable' => get_class($triggerable)]);
        return true;
    }

    /**
     * Execute update status action
     */
    private function executeUpdateStatusAction(Model $triggerable, array $contextData): bool
    {
        // Implementation for updating status
        Log::info("Update status action executed", ['triggerable' => get_class($triggerable)]);
        return true;
    }

    /**
     * Execute add tag action
     */
    private function executeAddTagAction(Model $triggerable, array $contextData): bool
    {
        // Implementation for adding tags
        Log::info("Add tag action executed", ['triggerable' => get_class($triggerable)]);
        return true;
    }

    /**
     * Execute create task action
     */
    private function executeCreateTaskAction(Model $triggerable, array $contextData): bool
    {
        // Implementation for creating tasks
        Log::info("Create task action executed", ['triggerable' => get_class($triggerable)]);
        return true;
    }

    /**
     * Execute send notification action
     */
    private function executeSendNotificationAction(Model $triggerable, array $contextData): bool
    {
        // Implementation for sending notifications
        Log::info("Send notification action executed", ['triggerable' => get_class($triggerable)]);
        return true;
    }

    /**
     * Execute custom action
     */
    private function executeCustomAction(string $actionKey, Model $triggerable, array $contextData): bool
    {
        // Implementation for custom actions
        Log::info("Custom action {$actionKey} executed", ['triggerable' => get_class($triggerable)]);
        return true;
    }

    // ==================== NEW ACTION HANDLERS ====================



    /**
     * Execute notify owner action
     */
    private function executeNotifyOwnerAction(Model $triggerable, array $contextData): bool
    {
        try {
            if ($triggerable instanceof Contact && $triggerable->user_id) {
                // Send internal notification to the contact owner
                Log::info("Notify owner action executed", [
                    'contact_id' => $triggerable->id,
                    'owner_id' => $triggerable->user_id
                ]);
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing notify owner action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute send welcome email action
     */
    private function executeSendWelcomeEmailAction(Model $triggerable, array $contextData): bool
    {
        try {
            $contactId = $this->getContactIdFromTriggerable($triggerable);

            if (!$contactId) {
                Log::warning("Could not extract contact for send welcome email action");
                return false;
            }

            $contact = Contact::find($contactId);

            if (!$contact || !$contact->email) {
                Log::warning("Contact not found or has no email", ['contact_id' => $contactId]);
                return false;
            }

            // Get email template from context or use default
            $subject = $contextData['subject'] ?? 'Welcome!';
            $message = $contextData['message'] ?? 'Welcome to our platform!';

            // TODO: Integrate with your email service/queue
            // Mail::to($contact->email)->send(new WelcomeEmail($contact, $subject, $message));

            Log::info("Send welcome email action executed", [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'subject' => $subject
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error executing send welcome email action: " . $e->getMessage());
            return false;
        }
    }



    /**
     * Execute assign to sales action
     */
    private function executeAssignToSalesAction(Model $triggerable, array $contextData, ?AutomationStepsImplement $stepImplement = null, ?array $stepData = null): bool
    {
        try {
            // Get contact_id based on triggerable type
            $contactId = $this->getContactIdFromTriggerable($triggerable);

            if (!$contactId) {
                Log::warning("Could not extract contact_id for assign to sales action", [
                    'triggerable_type' => get_class($triggerable),
                    'triggerable_id' => $triggerable->id ?? null
                ]);
                return false;
            }

            // Get assignment type from stepData, contextData, or default
            $assignmentType = $stepData['assign_strategy'] ?? $contextData['assign_strategy'] ?? 'specific_user';

            // Get trigger key from workflow
            $triggerKey = null;
            if ($stepImplement && $stepImplement->automationWorkflow) {
                $workflow = $stepImplement->automationWorkflow;
                if ($workflow->automationTrigger) {
                    $triggerKey = $workflow->automationTrigger->key;
                }
            }

            // If trigger is contact_created and assignment type is specific_user, update contact user_id
            if ($assignmentType === AutomationAssignStrategiesEnum::SPASIFIC_USER->value) {
                // Get user_id from stepData or contextData (should be set when configuring the workflow)
                $userId = $stepData['assign_user_id'] ?? $contextData['assign_user_id'] ?? null;
            } else if ($assignmentType === AutomationAssignStrategiesEnum::OWNER_CONTACT_USER->value) {
                $userId = $this->getContactUserIdFromTriggerable($triggerable);
            } else if ($assignmentType === AutomationAssignStrategiesEnum::ROUND_ROBIN_RANDOM_ACTIVE_OPPORTUNITY->value) {
                // Assign to user with least active opportunities
                $userId = $this->getUserWithLeastActiveOpportunities();
            } else if ($assignmentType === AutomationAssignStrategiesEnum::ROUND_ROBIN_RANDOM_ACTIVE_TASKS->value) {
                // Assign to user with least active tasks
                $userId = $this->getUserWithLeastActiveTasks();
            } else if ($assignmentType === AutomationAssignStrategiesEnum::ROUND_ROBIN_RANDOM_PERFORMANCE->value) {
                // Assign to user with least working time/performance
                $userId = $this->getUserWithLeastPerformance();
            } else if ($assignmentType === AutomationAssignStrategiesEnum::ROUND_ROBIN_RANDOM_BEST_SELLER->value) {
                // Assign to user with least sales in last 3 months
                $userId = $this->getUserWithLeastSales();
            }

            if ($userId) {
                if ($triggerable instanceof Contact) {
                    $triggerable->update(['user_id' => $userId]);
                    return true;

                } else if ($triggerable instanceof Lead) {
                    $triggerable->update(['assigned_to_id' => $userId]);
                    return true;
                } else if ($triggerable instanceof Task) {
                    $triggerable->update(['assigned_to_id' => $userId]);
                    return true;
                } else if ($triggerable instanceof Deal) {
                    $triggerable->update(['assigned_to_id' => $userId]);
                    return true;
                }
            } else {
                Log::warning("user_id not provided for specific_user assignment", [
                    'contact_id' => $contactId,
                    'assignment_type' => $assignmentType,
                    'trigger_key' => $triggerKey,
                    'step_data' => $stepData,
                    'context_data' => $contextData
                ]);
            }

            // Implementation for other assignment types will be added later
            Log::info("Assign to sales action executed", [
                'contact_id' => $contactId,
                'assignment_type' => $assignmentType,
                'trigger_key' => $triggerKey,
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id ?? null
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error executing assign to sales action: " . $e->getMessage());
            return false;
        }
    }



    /**
     * Execute move stage action
     */
    private function executeMoveStageAction(Model $triggerable, array $contextData): bool
    {
        try {
            if ($triggerable instanceof Lead) {
                $targetStageId = $contextData['target_stage_id'] ?? null;

                if ($targetStageId) {
                    $triggerable->update(['stage_id' => $targetStageId]);
                    Log::info("Move stage action executed", [
                        'opportunity_id' => $triggerable->id,
                        'new_stage_id' => $targetStageId
                    ]);
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing move stage action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute send invoice email action
     */
    private function executeSendInvoiceEmailAction(Model $triggerable, array $contextData): bool
    {
        try {
            $contactId = $this->getContactIdFromTriggerable($triggerable);

            if (!$contactId) {
                Log::warning("Could not extract contact for send invoice email action");
                return false;
            }

            $contact = Contact::find($contactId);

            if (!$contact || !$contact->email) {
                Log::warning("Contact not found or has no email", ['contact_id' => $contactId]);
                return false;
            }

            // Get invoice details from context
            $invoiceId = $contextData['invoice_id'] ?? null;
            $paymentLink = $contextData['payment_link'] ?? null;
            $subject = $contextData['subject'] ?? 'Invoice from ' . config('app.name');

            // TODO: Integrate with your email service/queue and invoice system
            // if ($invoiceId) {
            //     $invoice = Invoice::find($invoiceId);
            //     Mail::to($contact->email)->send(new InvoiceEmail($contact, $invoice, $paymentLink));
            // }

            Log::info("Send invoice email action executed", [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'invoice_id' => $invoiceId,
                'payment_link' => $paymentLink
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error executing send invoice email action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute escalate action
     */
    private function executeEscalateAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Notify manager or reassign
            Log::info("Escalate action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing escalate action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute notify manager action
     */
    private function executeNotifyManagerAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Send high-priority internal alert to manager
            Log::info("Notify manager action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing notify manager action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute escalate task action
     */
    private function executeEscalateTaskAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Notify team lead or reassign task
            Log::info("Escalate task action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing escalate task action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute create onboarding task action
     */
    private function executeCreateOnboardingTaskAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Kick off handover to Success/Implementation team
            Log::info("Create onboarding task action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing create onboarding task action: " . $e->getMessage());
            return false;
        }
    }



    /**
     * Execute tag and reopen later action
     */
    private function executeTagAndReopenLaterAction(Model $triggerable, array $contextData): bool
    {
        try {
            if ($triggerable instanceof Contact) {
                // Tag as Dormant and schedule reopen
                Log::info("Tag and reopen later action executed", [
                    'contact_id' => $triggerable->id,
                    'tag' => 'Dormant'
                ]);
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing tag and reopen later action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute notify finance action
     */
    private function executeNotifyFinanceAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Alert finance to review changes
            Log::info("Notify finance action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing notify finance action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute notify admin action
     */
    private function executeNotifyAdminAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Alert ops to fix mapping issues
            Log::info("Notify admin action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing notify admin action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute send reminder and task action
     */
    private function executeSendReminderAndTaskAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Send reminder and create collection task
            Log::info("Send reminder and task action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing send reminder and task action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute send reminder action
     */
    private function executeSendReminderAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Remind assignee before due date
            Log::info("Send reminder action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing send reminder action: " . $e->getMessage());
            return false;
        }
    }



    /**
     * Execute create contact and opportunity action
     */
    private function executeCreateContactAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Create records and trigger assignment
            $contactDTO = ContactDTO::fromArray([
                'name' => $triggerable->name,
                'email' => $triggerable->email,
                'phone' => $triggerable->phone,
                'address' => $triggerable->address,
                'city' => $triggerable->city,
                'state' => $triggerable->state,
                'zip' => $triggerable->zip,
                'country' => $triggerable->country,
                'contactable_id' => $triggerable->id,
                'contactable_type' => get_class($triggerable),
            ]);
            $this->contactService->store($contactDTO);
            Log::info("Create contact  action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing create contact and opportunity action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute create opportunity action
     */
    private function executeCreateOpportunityAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Get contact_id based on triggerable type
            $contactId = $this->getContactIdFromTriggerable($triggerable);

            if (!$contactId) {
                Log::error("Could not extract contact_id for create opportunity action", [
                    'triggerable_type' => get_class($triggerable),
                    'triggerable_id' => $triggerable->id ?? null
                ]);
                return false;
            }

            $default_pipeline = $this->pipelineService->getDefaultPipeline();
            if ($default_pipeline->firstStage) {
                $stage_id = $default_pipeline->firstStage->id;
            }

            // Create opportunity
            $leadDTO = LeadDTO::fromArray([
                'contact_id' => $contactId,
                'status' => OpportunityStatus::ACTIVE->value,
                'stage_id' => $stage_id,
                'win_probability' => 0,
                'assigned_to_id' => null,
            ]);
            $this->opportunityService->store($leadDTO);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing create opportunity action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Process delayed steps that are ready to execute
     */
    public function processDelayedSteps(): int
    {
        $delays = AutomationDelay::getReadyToExecute();
        $processedCount = 0;

        foreach ($delays as $delay) {
            try {
                $stepImplement = $delay->automationStepsImplement;

                if ($stepImplement && !$stepImplement->implemented) {
                    // Execute the delayed step
                    $stepImplement->markAsImplemented();
                    $delay->markAsProcessed();
                    $processedCount++;

                    // Continue with next steps after delay
                    $this->continueWorkflowAfterDelay($stepImplement);
                }
            } catch (\Exception $e) {
                Log::error("Error processing delayed step {$delay->id}: " . $e->getMessage());
            }
        }

        return $processedCount;
    }

    /**
     * Continue workflow execution after a delay step is completed
     */
    private function continueWorkflowAfterDelay(AutomationStepsImplement $completedDelayStep): void
    {
        try {
            // Get the next pending steps for the same workflow and entity
            $nextSteps = AutomationStepsImplement::where('automation_workflow_id', $completedDelayStep->automation_workflow_id)
                ->where('triggerable_type', $completedDelayStep->triggerable_type)
                ->where('triggerable_id', $completedDelayStep->triggerable_id)
                ->where('step_order', '>', $completedDelayStep->step_order)
                ->where('implemented', false)
                ->orderBy('step_order')
                ->get();

            if ($nextSteps->isEmpty()) {
                Log::info("No more steps to execute after delay step {$completedDelayStep->id}");
                return;
            }

            Log::info("Continuing workflow after delay step {$completedDelayStep->id}. Found {$nextSteps->count()} next steps to execute.");

            // Execute the next steps in order
            foreach ($nextSteps as $nextStep) {
                try {
                    // Execute non-delay steps immediately
                    $this->executeStep($nextStep);
                    Log::info("Executed next step {$nextStep->id} after delay");

                } catch (\Exception $e) {
                    Log::error("Error executing next step {$nextStep->id} after delay: " . $e->getMessage());
                    // Continue with other steps even if one fails
                }
            }

        } catch (\Exception $e) {
            Log::error("Error continuing workflow after delay step {$completedDelayStep->id}: " . $e->getMessage());
        }
    }

    /**
     * Create a delay record for a delay step
     */
    private function createDelayRecord(AutomationStepsImplement $stepImplement): void
    {
        try {
            $stepData = $stepImplement->step_data;

            if (isset($stepData['duration']) && isset($stepData['unit'])) {
                $executeAt = AutomationDelay::calculateExecuteAt(
                    $stepData['duration'],
                    $stepData['unit']
                );

                AutomationDelay::create([
                    'automation_steps_implement_id' => $stepImplement->id,
                    'duration' => $stepData['duration'],
                    'unit' => $stepData['unit'],
                    'execute_at' => $executeAt,
                    'processed' => false,
                    'context_data' => $stepImplement->context_data,
                ]);

                Log::info("Created delay record for step {$stepImplement->id} to execute at {$executeAt}");
            }
        } catch (\Exception $e) {
            Log::error("Error creating delay record for step {$stepImplement->id}: " . $e->getMessage());
        }
    }

    /**
     * Execute all pending steps for a specific entity
     */
    public function executePendingStepsForEntity(Model $triggerable): int
    {
        $pendingSteps = AutomationStepsImplement::getPendingStepsForEntity($triggerable);
        $executedCount = 0;

        foreach ($pendingSteps as $step) {
            $result = $this->executeStep($step);
            if ($result['success']) {
                $executedCount++;
            }
        }

        return $executedCount;
    }

    /**
     * Get user with least active opportunities assigned
     */
    private function getUserWithLeastActiveOpportunities(): ?int
    {
        try {
            $user = User::where('is_active', true)
                ->withCount([
                    'leads as active_opportunities_count' => function ($query) {
                        $query->whereIn('status', [
                            OpportunityStatus::ACTIVE->value,
                            OpportunityStatus::ABANDONED->value
                        ]);
                    }
                ])
                ->orderBy('active_opportunities_count', 'asc')
                ->first();

            return $user?->id;
        } catch (\Exception $e) {
            Log::error("Error getting user with least active opportunities: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user with least active tasks assigned
     */
    private function getUserWithLeastActiveTasks(): ?int
    {
        try {
            $user = User::where('is_active', true)
                ->withCount([
                    'tasks as active_tasks_count' => function ($query) {
                        $query->whereNotIn('status', [
                            TaskStatusEnum::COMPLETED->value,
                            TaskStatusEnum::CANCELLED->value,
                        ]);
                    }
                ])
                ->orderBy('active_tasks_count', 'asc')
                ->first();

            return $user?->id;
        } catch (\Exception $e) {
            Log::error("Error getting user with least active tasks: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user with least performance (least working time in system)
     * This checks for users with least activity based on their last_login_at
     */
    private function getUserWithLeastPerformance(): ?int
    {
        try {
            // Get user with oldest last_login_at (least recently active)
            $user = User::where('is_active', true)
                ->orderBy('last_login_at', 'asc')
                ->orderBy('id', 'asc') // Secondary sort for users who never logged in
                ->first();

            return $user?->id;
        } catch (\Exception $e) {
            Log::error("Error getting user with least performance: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user with least sales in last 3 months
     * Calculates based on won deals (opportunities with status won) and their deal_value
     */
    private function getUserWithLeastSales(): ?int
    {
        try {
            $threeMonthsAgo = now()->subMonths(3);

            $user = User::where('is_active', true)
                ->withSum([
                    'assignedDeals as total_sales' => function ($query) use ($threeMonthsAgo) {
                        $query->whereHas('lead', function ($leadQuery) {
                            $leadQuery->where('status', OpportunityStatus::WON->value);
                        })
                            ->where('created_at', '>=', $threeMonthsAgo);
                    }
                ], 'total_amount')
                ->orderBy('total_sales', 'asc')
                ->first();

            return $user?->id;
        } catch (\Exception $e) {
            Log::error("Error getting user with least sales: " . $e->getMessage());
            return null;
        }
    }
}
