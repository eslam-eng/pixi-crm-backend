<?php

namespace App\Services\Tenant\Automation;

use App\DTO\Contact\ContactDTO;
use App\DTO\Tenant\LeadDTO;
use App\Enums\AutomationActionsEnum;
use App\Enums\AutomationAssignStrategiesEnum;
use App\Enums\ConditionOperation;
use App\Enums\Landlord\ActivationStatusEnum;
use App\Enums\OpportunityStatus;
use App\Enums\TaskStatusEnum;
use App\Mail\TemplateMail;
use App\Models\Stage;
use App\Models\Tenant\AutomationStepsImplement;
use App\Models\Tenant\AutomationDelay;
use App\Models\Tenant\AutomationAction;
use App\Models\Tenant\AutomationLog;
use App\Models\Tenant\AutomationTriggerField;
use App\Models\Tenant\AutomationWorkflow;
use App\Models\Tenant\Contact;
use App\Models\Tenant\Deal;
use App\Models\Tenant\Lead;
use App\Models\Tenant\Task;
use App\Models\Tenant\Template;
use App\Models\Tenant\User;
use App\Notifications\Tenant\AutomationManagerNotification;
use App\Services\ContactService;
use App\Services\LeadService;
use App\Services\PipelineService;
use App\Services\Tenant\TemplateService;
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
    public TemplateService $templateService;
    public function __construct(
        LeadService $opportunityService,
        ConditionService $conditionService,
        PipelineService $pipelineService,
        ContactService $contactService,
        TemplateService $templateService
    ) {
        $this->opportunityService = $opportunityService;
        $this->conditionService = $conditionService;
        $this->pipelineService = $pipelineService;
        $this->contactService = $contactService;
        $this->templateService = $templateService;
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
                    'field_id' => $step->condition->field_id,
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

            // Check if it's a failed condition - stop workflow execution
            if (isset($result['condition_failed']) && $result['condition_failed']) {
                Log::info("Condition failed, stopping workflow execution", [
                    'workflow_id' => $workflow->id,
                    'step_id' => $stepImplement->id,
                    'triggerable_type' => get_class($triggerable),
                    'triggerable_id' => $triggerable->id,
                ]);
                break;
            }

            if (isset($result['delayed']) && $result['delayed']) {
                break;
            }

            if (!$result['success']) {
                // Stop execution if step failed (and maybe configured to stop)
                // For now, we'll just log it and maybe stop?
                // Let's assume we stop on failure for now
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
                    // If condition failed, mark as implemented but signal to stop workflow
                    if (!$result) {
                        $stepImplement->markAsImplemented();
                        Log::info("Condition step failed, workflow will stop", [
                            'step_id' => $stepImplement->id,
                            'workflow_id' => $stepImplement->automation_workflow_id,
                            'triggerable_type' => $stepImplement->triggerable_type,
                            'triggerable_id' => $stepImplement->triggerable_id,
                        ]);
                        return [
                            'success' => true, // Step executed successfully
                            'condition_failed' => true, // But condition was false
                            'message' => 'Condition evaluated to false, stopping workflow',
                        ];
                    }
                    break;
                case 'action':
                    $result = $this->executeActionStep($stepImplement);
                    break;
                case 'delay':
                    $result = $this->executeDelayStep($stepImplement);
                    break;
                default:
                    Log::error("Unknown step type: {$stepImplement->type}");
                    return [
                        'success' => false,
                        'message' => "Unknown step type: {$stepImplement->type}",
                    ];
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
     * Execute a condition step
     */
    private function executeConditionStep(AutomationStepsImplement $stepImplement): bool
    {
        $stepData = $stepImplement->step_data;
        if (!$stepData || !isset($stepData['field_id'], $stepData['operation'], $stepData['value'])) {
            Log::warning("Invalid condition step data for step {$stepImplement->id}", [
                'step_data' => $stepData,
                'has_field_id' => isset($stepData['field_id']),
                'has_operation' => isset($stepData['operation']),
                'has_value' => isset($stepData['value']),
            ]);
            return false;
        }

        $field_id = $stepData['field_id'];
        $operation = $stepData['operation'];
        $expectedValue = $stepData['value'];

        // Get the field definition to know the field name (which might be a relationship path like 'contact.email')
        $triggerField = AutomationTriggerField::find($field_id);

        if (!$triggerField) {
            Log::warning("Automation trigger field {$field_id} not found for step {$stepImplement->id}");
            return false;
        }

        // Get the actual value from the triggerable entity using the field name
        $actualValue = $this->getFieldValue($stepImplement->triggerable, $triggerField->field_name);

        $result = $this->evaluateCondition($actualValue, $operation, $expectedValue);

        Log::info("Condition step {$stepImplement->id} evaluated", [
            'field_id' => $field_id,
            'field_name' => $triggerField->field_name,
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
        $result = $this->executeActionById($actionId, $stepImplement->triggerable, $contextData, $stepImplement, $stepData);

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
        // if ($triggerable instanceof FormSubmission) {
        //     $data = $triggerable->data ?? [];
        //     if (isset($data['contact_id'])) {
        //         return $data['contact_id'];
        //     }
        // }

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
        if ($triggerable instanceof FormSubmission) {
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

    private function getAssignedUserIdFromTriggerable(Model $triggerable): ?int
    {
        $assigned_user_id = null;
        // If triggerable is Contact, use its id
        if ($triggerable instanceof Contact) {
            $assigned_user_id = $triggerable->user_id;
        }

        // If triggerable is Lead/Opportunity, use contact_id
        if ($triggerable instanceof Lead) {
            $assigned_user_id = $triggerable->assigned_to_id;
        }

        // If triggerable is Task, get contact_id through lead
        if ($triggerable instanceof Task) {
            $assigned_user_id = $triggerable->assigned_to_id;
        }

        // If triggerable is Deal, get contact_id through lead
        if ($triggerable instanceof Deal) {
            $assigned_user_id = $triggerable->assigned_to_id;
        }
        if ($assigned_user_id == null) {
            $assigned_user_id = $this->getContactUserIdFromTriggerable($triggerable);
        }
        // If triggerable is FormSubmission, check if contact_id is in data
        // if ($triggerable instanceof FormSubmission) {
        //     $data = $triggerable->data ?? [];
        //     if (isset($data['contact_id'])) {
        //         $assigned_user_id = $data['contact_id'];
        //     }
        // }
        if ($assigned_user_id == null) {
            Log::warning("Could not extract contact_id from triggerable", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id ?? null
            ]);
        }


        return $assigned_user_id;
    }
    /**
     * Get field value from triggerable entity
     */
    private function getFieldValue(Model $triggerable, string $field): mixed
    {
        Log::warning("getFieldValue called", [
            'field' => $field,
            'triggerable_type' => get_class($triggerable),
            'triggerable_id' => $triggerable->id ?? null,
        ]);

        // Handle nested field access (e.g., 'contact.email', 'source.title')
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $value = $triggerable;
            Log::warning('Nested field access', [
                'parts' => $parts,
                'triggerable_type' => get_class($triggerable),
            ]);

            foreach ($parts as $part) {
                if ($value && is_object($value)) {
                    // Try to load the relationship if it's not already loaded
                    if ($value instanceof Model && !$value->relationLoaded($part)) {
                        try {
                            $value->load($part);
                            Log::warning("Loaded relationship: {$part}");
                        } catch (\Exception $e) {
                            Log::warning("Failed to load relationship: {$part}", ['error' => $e->getMessage()]);
                        }
                    }
                    $value = $value->{$part} ?? null;
                    Log::warning("After accessing {$part}", ['value' => $value]);
                } else {
                    Log::warning("Value is not an object, returning null", ['part' => $part]);
                    return null;
                }
            }

            Log::warning("Nested field final value", ['value' => $value]);
            return $value;
        }

        // Direct field access - get from model attributes or relationships
        Log::warning("Direct field access for: {$field}");

        // First check if it's a loaded relationship
        if ($triggerable->relationLoaded($field)) {
            Log::warning("Field is a loaded relationship");
            return $triggerable->{$field};
        }

        // Try to get as attribute (database column)
        $attributes = $triggerable->getAttributes();
        Log::warning("Checking attributes", [
            'field' => $field,
            'has_field' => array_key_exists($field, $attributes),
            'all_attributes' => array_keys($attributes),
        ]);

        if (array_key_exists($field, $attributes)) {
            $value = $triggerable->getAttribute($field);
            Log::warning("Found in attributes", ['field' => $field, 'value' => $value]);
            return $value;
        }

        // Try to load as relationship
        Log::warning("Trying to load as relationship: {$field}");
        try {
            $triggerable->load($field);
            $value = $triggerable->{$field};
            Log::warning("Loaded as relationship", ['field' => $field, 'value' => $value]);
            return $value;
        } catch (\Exception $e) {
            Log::warning("Not a relationship, returning null", [
                'field' => $field,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Evaluate a condition
     */
    private function evaluateCondition(mixed $actualValue, string $operation, mixed $expectedValue): bool
    {
        $op = ConditionOperation::tryFrom($operation);

        if (!$op) {
            // Fallback for operations not in the enum yet (like contains, is_empty etc if they are not added to enum)
            // Or if the enum doesn't cover all cases used in the code
            return match ($operation) {
                'contains' => is_string($actualValue) && str_contains($actualValue, $expectedValue),
                'not_contains' => is_string($actualValue) && !str_contains($actualValue, $expectedValue),
                'is_empty' => empty($actualValue),
                'is_not_empty' => !empty($actualValue),
                'is_null' => is_null($actualValue),
                'is_not_null' => !is_null($actualValue),
                default => false,
            };
        }

        return match ($op) {
            ConditionOperation::EQUALS => $actualValue == $expectedValue,
            ConditionOperation::NOT_EQUALS => $actualValue != $expectedValue,
            ConditionOperation::GREATER_THAN => is_numeric($actualValue) && is_numeric($expectedValue) && $actualValue > $expectedValue,
            ConditionOperation::LESS_THAN => is_numeric($actualValue) && is_numeric($expectedValue) && $actualValue < $expectedValue,
            ConditionOperation::GREATER_THAN_OR_EQUAL_TO => is_numeric($actualValue) && is_numeric($expectedValue) && $actualValue >= $expectedValue,
            ConditionOperation::LESS_THAN_OR_EQUAL_TO => is_numeric($actualValue) && is_numeric($expectedValue) && $actualValue <= $expectedValue,
        };
    }

    /**
     * Execute action by key
     */
    private function executeActionById(int $actionId, Model $triggerable, array $contextData, ?AutomationStepsImplement $stepImplement = null, ?array $stepData = null): bool
    {
        switch ($actionId) {
            // Email Actions
            case AutomationActionsEnum::MOVE_STAGE->value:  // Done
                return $this->executeMoveStageAction($triggerable, $contextData);

            // Email Actions
            case AutomationActionsEnum::SEND_EMAIL->value:   //Done
                return $this->executeSendEmailAction($triggerable, $stepData);

            // Escalation Actions
            case AutomationActionsEnum::ESCALATE->value:
                return $this->executeEscalateAction($triggerable, $contextData);

            case AutomationActionsEnum::ESCALATE_TASK->value:
                return $this->executeEscalateTaskAction($triggerable, $contextData);

            // Task Actions
            case AutomationActionsEnum::CREATE_ONBOARDING_TASK->value: //Inprogress
                return $this->executeCreateOnboardingTaskAction($triggerable, $contextData);

            // Notification Actions
            case AutomationActionsEnum::NOTIFY_ADMIN->value://Done
                return $this->executeNotifyAdminAction($triggerable, $stepData);
            case AutomationActionsEnum::NOTIFY_OWNER->value://Done
                return $this->executeNotifyOwnerAction($triggerable, $stepData);
            case AutomationActionsEnum::NOTIFY_MANAGER->value://Done
                return $this->executeNotifyManagerAction($triggerable, $stepData);

            // Reminder Actions
            case AutomationActionsEnum::SEND_REMINDER->value:
                return $this->executeSendReminderAction($triggerable, $contextData);

            // Complex Actions
            // case 'create_contact':
            //     return $this->executeCreateContactAction($triggerable, $contextData);

            case AutomationActionsEnum::CREATE_OPPORTUNITY->value://Done
                return $this->executeCreateOpportunityAction($triggerable, $contextData);

            case AutomationActionsEnum::ASSIGN_TO_SALES->value://Done
                return $this->executeAssignToSalesAction($triggerable, $contextData, $stepImplement, $stepData);
            default:
                return false;
        }
    }

    /**
     * Execute send email action
     */
    private function executeSendEmailAction(Model $triggerable, array $stepData): bool
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
            $email_subject = $stepData['email_subject'] ?? 'Important Update';
            $email_message = $stepData['email_message'] ?? '';
            $email_template_id = $stepData['email_template_id'] ?? null;

            if ($email_template_id) {
                $template = Template::find($email_template_id);
                if ($template) {
                    $email_subject = $this->templateService->renderSubject($template->subject, $contact->email, $stepData['variables'] ?? []);
                    $email_message = $this->templateService->render($template, $contact->email, $stepData['variables'] ?? []);
                }
            }
            Mail::to($contact->email)->send(new TemplateMail($email_subject, $email_message, $contact->name));

            Log::info("Send email action executed", [
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'subject' => $email_subject,
                'template' => $template
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error executing send email action: " . $e->getMessage());
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
                // Get current stage
                $currentStage = $triggerable->stage;
                if ($currentStage) {
                    // Find next stage in the same pipeline with higher sequence number
                    $nextStage = Stage::where('pipeline_id', $currentStage->pipeline_id)
                        ->where('seq_number', '>', $currentStage->seq_number)
                        ->orderBy('seq_number', 'asc')
                        ->first();

                    if ($nextStage) {
                        $triggerable->update(['stage_id' => $nextStage->id]);

                        //TODO : add Activity Log to save stage change
                        Log::info("Move stage action executed", [
                            'lead_id' => $triggerable->id,
                            'from_stage' => $currentStage->name,
                            'to_stage' => $nextStage->name
                        ]);
                    } else {
                        Log::info("Move stage action skipped: Lead is already at the last stage", [
                            'lead_id' => $triggerable->id,
                            'current_stage' => $currentStage->name
                        ]);
                    }
                } else {
                    Log::warning("Move stage action skipped: Lead has no current stage", [
                        'lead_id' => $triggerable->id
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
     * Execute notify owner action
     */
    private function executeNotifyOwnerAction(Model $triggerable, ?array $stepData = null): bool
    {
        try {
            // 1. Identify the owner/assignee
            $userId = $this->getAssignedUserIdFromTriggerable($triggerable);

            if (!$userId) {
                Log::warning("Could not identify owner for notify owner action", [
                    'triggerable_type' => get_class($triggerable),
                    'triggerable_id' => $triggerable->id
                ]);
                return false;
            }

            $user = User::find($userId);
            if (!$user) {
                Log::warning("Owner user not found", ['user_id' => $userId]);
                return false;
            }

            // 2. Prepare Message
            if (isset($stepData['message']) && $stepData['message']) {
                $message = $stepData['message'] . ' - Action From Automation';
            } else {
                $message = "Action happened for " . (get_class($triggerable) ?? 'item');
            }

            // 3. Send Notification
            $user->notify(new AutomationManagerNotification($user, $message, $triggerable));

            Log::info("Notify owner action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id,
                'owner_id' => $user->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing notify owner action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute notify manager action
     */
    private function executeNotifyManagerAction(Model $triggerable, ?array $stepData = null): bool
    {
        try {
            // 1. Identify the user associated with the triggerable
            $userId = $this->getAssignedUserIdFromTriggerable($triggerable);
            if (!$userId) {
                Log::warning("Could not identify user for notify manager action", [
                    'triggerable_type' => get_class($triggerable),
                    'triggerable_id' => $triggerable->id
                ]);
                return false;
            }

            $user = User::with('team')->find($userId);
            if (!$user) {
                Log::warning("User not found for notify manager action", ['user_id' => $userId]);
                return false;
            }

            // 2. Find the manager (Team Leader)
            $managerId = null;

            // Check team leader
            if ($user->team && $user->team->leader_id) {
                $managerId = $user->team->leader_id;
            } else {
                $managerId = $user->id;
            }

            if (!$managerId) {
                Log::info("No manager found for user", ['user_id' => $user->id]);
                return true; // Action executed but no manager to notify
            }

            $manager = User::find($managerId);
            if (!$manager) {
                Log::warning("Manager user not found", ['manager_id' => $managerId]);
                return false;
            }

            // 3. Send Notification
            if ($stepData['message']) {
                $message = $stepData['message'] . ' - Action From Automation';
            } else {
                $message = "Action happened for Module " . (get_class($triggerable) ?? 'item');
            }

            $manager->notify(new AutomationManagerNotification($manager, $message, $triggerable));

            Log::info("Notify manager action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id,
                'user_id' => $user->id,
                'manager_id' => $manager->id,
                'message' => $message
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
     * Execute notify admin action
     */
    private function executeNotifyAdminAction(Model $triggerable, ?array $stepData = null): bool
    {
        try {
            // 1. Find all admins
            // Assuming 'admin' is the role name for administrators
            $admins = User::role('admin')->get();

            if ($admins->isEmpty()) {
                Log::warning("No admins found for notify admin action");
                return true; // Action executed but no one to notify
            }

            // 2. Prepare Message
            if (isset($stepData['message']) && $stepData['message']) {
                $message = $stepData['message'] . ' - Action From Automation';
            } else {
                $message = "Admin Alert: Action happened for " . (get_class($triggerable) ?? 'item');
            }

            // 3. Send Notification to all admins
            // Notification::send($admins, new AutomationManagerNotification(null, $message, $triggerable));

            foreach ($admins as $admin) {
                $admin->notify(new AutomationManagerNotification($admin, $message, $triggerable));
            }

            Log::info("Notify admin action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id,
                'admin_count' => $admins->count()
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
                    $result = $this->executeStep($nextStep);

                    // Check if condition failed - stop workflow execution
                    if (isset($result['condition_failed']) && $result['condition_failed']) {
                        Log::info("Condition failed after delay, stopping workflow", [
                            'step_id' => $nextStep->id,
                            'workflow_id' => $completedDelayStep->automation_workflow_id,
                        ]);
                        break;
                    }

                    if (isset($result['delayed']) && $result['delayed']) {
                        Log::info("Workflow paused for delay at step {$nextStep->id}");
                        break;
                    }

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
