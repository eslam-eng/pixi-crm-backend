<?php

namespace App\Observers;

use App\Models\Tenant\Contact;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;

class ContactAutomationObserver
{
    public function __construct(
        private AutomationWorkflowFireService $triggerService
    ) {}

    /**
     * Handle the Contact "created" event.
     */
    public function created(Contact $contact): void
    {
        \Log::info('Observer Begain Contact Created', [
            'contact_id' => $contact->id,
            'contact_name' => $contact->name,
        ]);
        
        try {
            $this->triggerService->fireTrigger('contact_created', [
                'contact_id' => $contact->id,
                'entity_type' => 'contact',
                'entity_id' => $contact->id,
                'contact_data' => $contact->toArray(),
            ]);
            
            \Log::info('Observer fired trigger successfully', [
                'contact_id' => $contact->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Observer failed to fire trigger', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the Contact "updated" event.
     */
    public function updated(Contact $contact): void
    {
        $changes = $contact->getChanges();
        $original = $contact->getOriginal();

        // Fire generic contact updated trigger
        $this->triggerService->fireTrigger('contact_updated', [
            'contact' => $contact,
            'entity' => $contact,
            'entity_type' => 'contact',
            'entity_id' => $contact->id,
            'changed_fields' => $changes,
            'original' => $original,
        ]);

        // Check if tags were added (contact_tag_added trigger)
        if (isset($changes['tags'])) {
            $oldTags = is_array($original['tags'] ?? null) ? $original['tags'] : [];
            $newTags = is_array($contact->tags) ? $contact->tags : [];
            $addedTags = array_diff($newTags, $oldTags);

            if (!empty($addedTags)) {
                $this->triggerService->fireTrigger('contact_tag_added', [
                    'contact' => $contact,
                    'entity' => $contact,
                    'entity_type' => 'contact',
                    'entity_id' => $contact->id,
                    'added_tags' => $addedTags,
                    'all_tags' => $newTags,
                ]);
            }
        }

        // Fire field-specific triggers
        foreach ($changes as $field => $newValue) {
            $this->triggerService->fireTrigger('field_value_changed', [
                'contact' => $contact,
                'entity' => $contact,
                'entity_type' => 'contact',
                'field_name' => $field,
                'old_value' => $original[$field] ?? null,
                'new_value' => $newValue,
            ]);
        }
    }
}

