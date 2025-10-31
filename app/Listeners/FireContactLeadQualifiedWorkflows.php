<?php

namespace App\Listeners;

use App\Events\Contacts\ContactLeadQualified;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FireContactLeadQualifiedWorkflows implements ShouldQueue
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
    public function handle(ContactLeadQualified $event): void
    {
        try {
            $fireService = app(AutomationWorkflowFireService::class);
            $fireService->fireContactLeadQualifiedWorkflows($event->contact, $event->lead, $event->qualificationData);

            Log::info("Contact lead qualified workflows fired", [
                'contact_id' => $event->contact->id,
                'contact_name' => $event->contact->name,
                'lead_id' => $event->lead->id,
                'qualification_data' => $event->qualificationData,
            ]);

        } catch (\Exception $e) {
            Log::error("Error firing contact lead qualified workflows: " . $e->getMessage(), [
                'contact_id' => $event->contact->id,
                'lead_id' => $event->lead->id,
                'qualification_data' => $event->qualificationData,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ContactLeadQualified $event, \Throwable $exception): void
    {
        Log::error("Failed to fire contact lead qualified workflows", [
            'contact_id' => $event->contact->id,
            'lead_id' => $event->lead->id,
            'qualification_data' => $event->qualificationData,
            'exception' => $exception
        ]);
    }
}
