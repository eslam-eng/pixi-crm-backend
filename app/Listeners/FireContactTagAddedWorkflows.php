<?php

namespace App\Listeners;

use App\Events\Contacts\ContactTagAdded;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FireContactTagAddedWorkflows implements ShouldQueue
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
    public function handle(ContactTagAdded $event): void
    {
        try {
            $fireService = app(AutomationWorkflowFireService::class);
            $fireService->fireContactTagAddedWorkflows($event->contact, $event->tag, $event->tagData);

            Log::info("Contact tag added workflows fired", [
                'contact_id' => $event->contact->id,
                'contact_name' => $event->contact->name,
                'tag' => $event->tag,
                'tag_data' => $event->tagData,
            ]);

        } catch (\Exception $e) {
            Log::error("Error firing contact tag added workflows: " . $e->getMessage(), [
                'contact_id' => $event->contact->id,
                'tag' => $event->tag,
                'tag_data' => $event->tagData,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(ContactTagAdded $event, \Throwable $exception): void
    {
        Log::error("Failed to fire contact tag added workflows", [
            'contact_id' => $event->contact->id,
            'tag' => $event->tag,
            'tag_data' => $event->tagData,
            'exception' => $exception
        ]);
    }
}
