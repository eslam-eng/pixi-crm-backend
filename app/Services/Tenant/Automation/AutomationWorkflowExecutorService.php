<?php

namespace App\Services\Tenant\Automation;

use App\DTO\Tenant\LeadDTO;
use App\Models\Tenant\AutomationStepsImplement;
use App\Models\Tenant\AutomationDelay;
use App\Models\Tenant\AutomationAction;
use App\Models\Tenant\Contact;
use App\Models\Tenant\Lead;
use App\Services\LeadService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomationWorkflowExecutorService
{
    public LeadService $opportunityService;

    public function __construct(LeadService $opportunityService)
    {
        $this->opportunityService = $opportunityService;
    }

    /**
     * Execute a specific step implementation
     */
    public function executeStep(AutomationStepsImplement $stepImplement): bool
    {
        try {
            DB::transaction(function () use ($stepImplement) {
                switch($stepImplement->type) {
                    case 'condition':
                        $result = $this->executeConditionStep($stepImplement);
                        break;
                    case 'action':
                        $result = $this->executeActionStep($stepImplement);
                        break;
                    case 'delay':
                        $result = $this->executeDelayStep($stepImplement);
                        break;
                    default:
                        throw new \InvalidArgumentException("Unknown step type: {$stepImplement->type}");
                }

                if ($result) {
                    $stepImplement->markAsImplemented();
                    Log::info("Step {$stepImplement->id} executed successfully", [
                        'step_type' => $stepImplement->type,
                        'triggerable_type' => $stepImplement->triggerable_type,
                        'triggerable_id' => $stepImplement->triggerable_id,
                    ]);
                }
            });

            return true;

        } catch (\Exception $e) {
            Log::error("Error executing step {$stepImplement->id}: " . $e->getMessage(), [
                'step_id' => $stepImplement->id,
                'step_type' => $stepImplement->type,
                'triggerable_type' => $stepImplement->triggerable_type,
                'triggerable_id' => $stepImplement->triggerable_id,
                'exception' => $e
            ]);
            return false;
        }
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
        if ($triggerable instanceof \App\Models\Tenant\Task) {
            if ($triggerable->lead_id) {
                $lead = Lead::find($triggerable->lead_id);
                return $lead?->contact_id;
            }
        }

        // If triggerable is Deal, get contact_id through lead
        if ($triggerable instanceof \App\Models\Tenant\Deal) {
            if ($triggerable->lead_id) {
                $lead = $this->opportunityService->findById($triggerable->lead_id);
                return $lead?->contact_id;
            }
        }

        // If triggerable has contact_id property directly
        if (isset($triggerable->contact_id)) {
            return $triggerable->contact_id;
        }

        // If triggerable has lead_id, try to get contact_id through lead
        if (isset($triggerable->lead_id)) {
            $lead = Lead::find($triggerable->lead_id);
            return $lead?->contact_id;
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
                    switch($operation) {
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
        switch($actionKey) {
            // Contact Actions
            case 'assign_contact':
                return $this->executeAssignContactAction($triggerable, $contextData);
            case 'notify_owner':
                return $this->executeNotifyOwnerAction($triggerable, $contextData);
            case 'send_welcome_email':
                return $this->executeSendWelcomeEmailAction($triggerable, $contextData);
            case 'assign_to_team':
                return $this->executeAssignToTeamAction($triggerable, $contextData);
            
            // Opportunity Actions
            case 'assign_opportunity':
                return $this->executeAssignOpportunityAction($triggerable, $contextData);
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
            case 'trigger_next_step':
                return $this->executeTriggerNextStepAction($triggerable, $contextData);
            
            // Tagging Actions
            case 'tag_and_reopen_later':
                return $this->executeTagAndReopenLaterAction($triggerable, $contextData);
            
            // Notification Actions
            case 'notify_finance':
                return $this->executeNotifyFinanceAction($triggerable, $contextData);
            case 'notify_admin':
                return $this->executeNotifyAdminAction($triggerable, $contextData);
            
            // Reminder Actions
            case 'send_reminder_and_task':
                return $this->executeSendReminderAndTaskAction($triggerable, $contextData);
            case 'send_reminder':
                return $this->executeSendReminderAction($triggerable, $contextData);
            case 'notify_owner_and_reschedule':
                return $this->executeNotifyOwnerAndRescheduleAction($triggerable, $contextData);
            case 'send_reminder_reschedule':
                return $this->executeSendReminderRescheduleAction($triggerable, $contextData);
            
            // Complex Actions
            case 'create_contact':
                return $this->executeCreateContactAction($triggerable, $contextData);
            
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
        // Implementation for sending email
        Log::info("Send email action executed", ['triggerable' => get_class($triggerable)]);
        return true;
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
     * Execute assign contact action
     */
    private function executeAssignContactAction(Model $triggerable, array $contextData): bool
    {
        try {
            if ($triggerable instanceof Contact) {
                // Get assignment criteria from context or step data
                $assignmentCriteria = $contextData['assignment_criteria'] ?? null;
                
                if ($assignmentCriteria) {
                    // Implement contact assignment logic based on criteria
                    // This could involve assigning to specific users, teams, or based on round-robin
                    Log::info("Assign contact action executed", [
                        'contact_id' => $triggerable->id,
                        'criteria' => $assignmentCriteria
                    ]);
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing assign contact action: " . $e->getMessage());
            return false;
        }
    }

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
            if ($triggerable instanceof Contact && $triggerable->email) {
                // Send welcome/onboarding email
                Log::info("Send welcome email action executed", [
                    'contact_id' => $triggerable->id,
                    'email' => $triggerable->email
                ]);
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing send welcome email action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute assign to team action
     */
    private function executeAssignToTeamAction(Model $triggerable, array $contextData): bool
    {
        try {
            if ($triggerable instanceof Contact) {
                // Assign contact to specific team (Key Accounts/Partners)
                $teamId = $contextData['team_id'] ?? null;
                
                if ($teamId) {
                    Log::info("Assign to team action executed", [
                        'contact_id' => $triggerable->id,
                        'team_id' => $teamId
                    ]);
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing assign to team action: " . $e->getMessage());
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
            $assignmentType = $stepData['assignment_type'] ?? $contextData['assignment_type'] ?? 'specific_user';
            
            // Get trigger key from workflow
            $triggerKey = null;
            if ($stepImplement && $stepImplement->automationWorkflow) {
                $workflow = $stepImplement->automationWorkflow;
                if ($workflow->automationTrigger) {
                    $triggerKey = $workflow->automationTrigger->key;
                }
            }

            // If trigger is contact_created and assignment type is specific_user, update contact user_id
            if ($triggerKey === 'contact_created' && $assignmentType === 'specific_user') {
                if ($triggerable instanceof Contact) {
                    // Get user_id from stepData or contextData (should be set when configuring the workflow)
                    $userId = $stepData['user_id'] ?? $contextData['user_id'] ?? null;
                    
                    if ($userId) {
                        $triggerable->update(['user_id' => $userId]);
                        Log::info("Contact user_id updated via assign to sales action", [
                            'contact_id' => $contactId,
                            'user_id' => $userId,
                            'assignment_type' => $assignmentType,
                            'trigger_key' => $triggerKey
                        ]);
                        return true;
                    } else {
                        Log::warning("user_id not provided for specific_user assignment", [
                            'contact_id' => $contactId,
                            'assignment_type' => $assignmentType,
                            'trigger_key' => $triggerKey,
                            'step_data' => $stepData,
                            'context_data' => $contextData
                        ]);
                    }
                }
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
     * Execute assign opportunity action
     */
    private function executeAssignOpportunityAction(Model $triggerable, array $contextData): bool
    {
        try {
            if ($triggerable instanceof Lead) {
                // Distribute opportunity to sales automatically
                Log::info("Assign opportunity action executed", [
                    'opportunity_id' => $triggerable->id,
                    'opportunity_name' => $triggerable->name
                ]);
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing assign opportunity action: " . $e->getMessage());
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
            // Send invoice or payment link email to client
            Log::info("Send invoice email action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
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
     * Execute trigger next step action
     */
    private function executeTriggerNextStepAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Create next task or move stage
            Log::info("Trigger next step action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing trigger next step action: " . $e->getMessage());
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
     * Execute notify owner and reschedule action
     */
    private function executeNotifyOwnerAndRescheduleAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Alert rep and send reschedule link
            Log::info("Notify owner and reschedule action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing notify owner and reschedule action: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute send reminder reschedule action
     */
    private function executeSendReminderRescheduleAction(Model $triggerable, array $contextData): bool
    {
        try {
            // Reminder if accepted; reschedule if declined
            Log::info("Send reminder reschedule action executed", [
                'triggerable_type' => get_class($triggerable),
                'triggerable_id' => $triggerable->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Error executing send reminder reschedule action: " . $e->getMessage());
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
            Log::info("Create contact and opportunity action executed", [
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

            // Create opportunity
            $leadDTO = LeadDTO::fromArray([
                'contact_id' => $contactId,
                'status' => 'open',
                'stage_id' => $contextData['stage_id'] ?? null,
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
                    $this->executeStep($stepImplement);
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
                    // Check if this step is also a delay that needs to be scheduled
                    if ($nextStep->type === 'delay') {
                        // Create delay record for this step
                        $this->createDelayRecord($nextStep);
                        Log::info("Scheduled next delay step {$nextStep->id} for later execution");
                        break; // Stop execution chain at delay steps
                    } else {
                        // Execute non-delay steps immediately
                        $this->executeStep($nextStep);
                        Log::info("Executed next step {$nextStep->id} after delay");
                    }
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
            if ($this->executeStep($step)) {
                $executedCount++;
            }
        }

        return $executedCount;
    }
}
