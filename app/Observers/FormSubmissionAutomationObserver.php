<?php

namespace App\Observers;

use App\Models\Tenant\FormSubmission;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;


class FormSubmissionAutomationObserver
{
    public function __construct(
        private AutomationWorkflowFireService $triggerService
    ) {}

    /**
     * Handle the FormSubmission "created" event.
     */
    public function created(FormSubmission $formSubmission): void
    {
        $form = $formSubmission->form;
        $submissionData = $formSubmission->data ?? [];

        // Check for required fields and mapping errors
        $requiredFields = ['email', 'phone', 'name']; // Can be configured
        $missingFields = [];
        $unmappedFields = [];

        foreach ($requiredFields as $field) {
            if (empty($submissionData[$field])) {
                $missingFields[] = $field;
            }
        }

        // If there are missing or unmapped fields, fire error trigger
        if (!empty($missingFields)) {
            $this->triggerService->fireTrigger('form_field_mapping_error', [
                'triggerable_type' => get_class($formSubmission),
                'triggerable_id' => $formSubmission->id,
                'form' => $form,
                'form_submission' => $formSubmission,
                'entity' => $formSubmission,
                'entity_type' => 'form_submission',
                'entity_id' => $formSubmission->id,
                'missing_fields' => $missingFields,
                'unmapped_fields' => $unmappedFields,
                'submission_data' => $submissionData,
            ]);
        }

        // Fire form submitted trigger
        $this->triggerService->fireTrigger('form_submitted', [
            'triggerable_type' => get_class($formSubmission),
            'triggerable_id' => $formSubmission->id,
            'form' => $form,
            'form_submission' => $formSubmission,
            'entity' => $formSubmission,
            'entity_type' => 'form_submission',
            'entity_id' => $formSubmission->id,
            'submission_data' => $submissionData,
            'ip_address' => $formSubmission->ip_address,
            'user_agent' => $formSubmission->user_agent,
        ]);
    }
}

