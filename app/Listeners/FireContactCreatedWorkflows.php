<?php

namespace App\Listeners;

use App\Events\Contacts\ContactCreated;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FireContactCreatedWorkflows implements ShouldQueue
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
    public function handle(ContactCreated $event): void
    {
        try {
            $fireService = app(AutomationWorkflowFireService::class);
            $fireService->fireContactCreatedWorkflows($event->contact);

            Log::info("Contact created workflows fired", [
                'contact_id' => $event->contact->id,
                'contact_name' => $event->contact->name,
            ]);

        } catch (\Exception $e) {
            Log::error("Error firing contact created workflows: " . $e->getMessage(), [
                'contact_id' => $event->contact->id,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ContactCreated $event, \Throwable $exception): void
    {
        Log::error("Failed to fire contact created workflows", [
            'contact_id' => $event->contact->id,
            'exception' => $exception
        ]);
    }
}
