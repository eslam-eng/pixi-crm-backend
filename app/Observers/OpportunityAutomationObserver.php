<?php

namespace App\Observers;

use App\Models\Tenant\Lead;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;


class OpportunityAutomationObserver
{
    public function __construct(
        private AutomationWorkflowFireService $triggerService
    ) {}

    /**
     * Handle the Opportunity "created" event.
     */
    public function created(Lead $opportunity): void
    {
        // Fire generic opportunity created trigger
        $this->triggerService->fireTrigger('opportunity_created', [
            'triggerable_type' => get_class($opportunity),
            'triggerable_id' => $opportunity->id,
            'opportunity' => $opportunity,
            'entity' => $opportunity,
            'entity_type' => 'opportunity',
            'entity_id' => $opportunity->id,
            'contact' => $opportunity->contact,
        ]);

        // Check if this is a high-value opportunity
        // This will be checked against workflow configurations with thresholds
        if ($opportunity->deal_value && $opportunity->deal_value > 0) {
            $this->triggerService->fireTrigger('opportunity_high_value', [
                'triggerable_type' => get_class($opportunity),
                'triggerable_id' => $opportunity->id,
                'opportunity' => $opportunity,
                'entity' => $opportunity,
                'entity_type' => 'opportunity',
                'entity_id' => $opportunity->id,
                'contact' => $opportunity->contact,
                'deal_value' => $opportunity->deal_value,
            ]);
        }
    }

    /**
     * Handle the Opportunity "updated" event.
     */
    public function updated(Lead $opportunity): void
    {
        $changes = $opportunity->getChanges();
        $original = $opportunity->getOriginal();

        // Check if stage changed
        if ($opportunity->wasChanged('stage_id')) {
            $this->triggerService->fireTrigger('opportunity_stage_changed', [
                'triggerable_type' => get_class($opportunity),
                'triggerable_id' => $opportunity->id,
                'opportunity' => $opportunity,
                'entity' => $opportunity,
                'entity_type' => 'opportunity',
                'entity_id' => $opportunity->id,
                'from_stage_id' => $original['stage_id'] ?? null,
                'to_stage_id' => $opportunity->stage_id,
                'contact' => $opportunity->contact,
            ]);
        }

        // Check if opportunity became qualified (is_qualifying changed from false to true)
        if (isset($changes['is_qualifying']) && $changes['is_qualifying'] === true && !($original['is_qualifying'] ?? false)) {
            $this->triggerService->fireTrigger('opportunity_qualified', [
                'triggerable_type' => get_class($opportunity),
                'triggerable_id' => $opportunity->id,
                'opportunity' => $opportunity,
                'entity' => $opportunity,
                'entity_type' => 'opportunity',
                'entity_id' => $opportunity->id,
                'contact' => $opportunity->contact,
                'stage' => $opportunity->stage,
            ]);
        }

        // Check if deal value crossed a threshold (fire opportunity_high_value)
        if (isset($changes['deal_value']) && $opportunity->deal_value > 0) {
            $oldValue = $original['deal_value'] ?? 0;
            $newValue = $opportunity->deal_value;

            // Fire high_value trigger if value increased
            // The actual threshold check will be done in workflow configuration
            if ($newValue > $oldValue) {
                $this->triggerService->fireTrigger('opportunity_high_value', [
                    'triggerable_type' => get_class($opportunity),
                    'triggerable_id' => $opportunity->id,
                    'opportunity' => $opportunity,
                    'entity' => $opportunity,
                    'entity_type' => 'opportunity',
                    'entity_id' => $opportunity->id,
                    'contact' => $opportunity->contact,
                    'deal_value' => $newValue,
                    'previous_value' => $oldValue,
                ]);
            }
        }

        // Generic update trigger
        $this->triggerService->fireTrigger('opportunity_updated', [
            'triggerable_type' => get_class($opportunity),
            'triggerable_id' => $opportunity->id,
            'opportunity' => $opportunity,
            'entity' => $opportunity,
            'entity_type' => 'opportunity',
            'entity_id' => $opportunity->id,
            'changed_fields' => $changes,
            'original' => $original,
            'contact' => $opportunity->contact,
        ]);
    }
}

