<?php

namespace App\Listeners;

use App\Events\Contacts\ContactUpdated;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FireContactUpdatedWorkflows implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ContactUpdated $event): void
    {
        try {
            $fireService = app(AutomationWorkflowFireService::class);
            $fireService->fireContactUpdatedWorkflows($event->contact, $event->changedFields);

            Log::info("Contact updated workflows fired", [
                'contact_id' => $event->contact->id,
                'contact_name' => $event->contact->name,
                'changed_fields' => $event->changedFields,
            ]);

        } catch (\Exception $e) {
            Log::error("Error firing contact updated workflows: " . $e->getMessage(), [
                'contact_id' => $event->contact->id,
                'changed_fields' => $event->changedFields,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ContactUpdated $event, \Throwable $exception): void
    {
        Log::error("Failed to fire contact updated workflows", [
            'contact_id' => $event->contact->id,
            'changed_fields' => $event->changedFields,
            'exception' => $exception
        ]);
    }
}
