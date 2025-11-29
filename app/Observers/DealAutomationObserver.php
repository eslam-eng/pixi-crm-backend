<?php

namespace App\Observers;

use App\Models\Tenant\Deal;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;


class DealAutomationObserver
{
    public function __construct(
        private AutomationWorkflowFireService $triggerService
    ) {}

    /**
     * Handle the Deal "created" event.
     */
    public function created(Deal $deal): void
    {
        // Get related entities
        $opportunity = $deal->opportunity ?? null;
        $contact = $opportunity?->contact ?? null;

        $this->triggerService->fireTrigger('deal_created', [
            'triggerable_type' => get_class($deal),
            'triggerable_id' => $deal->id,
            'deal' => $deal,
            'opportunity' => $opportunity,
            'contact' => $contact,
            'entity' => $deal,
            'entity_type' => 'deal',
            'entity_id' => $deal->id,
        ]);
    }

    /**
     * Handle the Deal "updated" event.
     */
    public function updated(Deal $deal): void
    {
        $changes = $deal->getChanges();
        $original = $deal->getOriginal();

        // Get related entities
        $opportunity = $deal->opportunity ?? null;
        $contact = $opportunity?->contact ?? null;

        $this->triggerService->fireTrigger('deal_updated', [
            'triggerable_type' => get_class($deal),
            'triggerable_id' => $deal->id,
            'deal' => $deal,
            'opportunity' => $opportunity,
            'contact' => $contact,
            'entity' => $deal,
            'entity_type' => 'deal',
            'entity_id' => $deal->id,
            'changed_fields' => $changes,
            'original' => $original,
        ]);
    }
}
