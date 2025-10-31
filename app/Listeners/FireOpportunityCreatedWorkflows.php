<?php

namespace App\Listeners;

use App\Events\Opportunity\OpportunityCreated;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FireOpportunityCreatedWorkflows implements ShouldQueue
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
    public function handle(OpportunityCreated $event): void
    {
        try {
            $fireService = app(AutomationWorkflowFireService::class);
            $fireService->fireOpportunityCreatedWorkflows($event->opportunity, $event->creationData);

            Log::info("Opportunity created workflows fired", [
                'opportunity_id' => $event->opportunity->id,
                'contact_id' => $event->opportunity->contact_id,
                'contact_name' => $event->opportunity->contact->name ?? 'Unknown',
                'creation_data' => $event->creationData,
                'configuration' => $event->configuration,
                'required_fields' => $event->configuration['required_fields'],
                'default_pipeline' => $event->configuration['default_pipeline'],
            ]);

        } catch (\Exception $e) {
            Log::error("Error firing opportunity created workflows: " . $e->getMessage(), [
                'opportunity_id' => $event->opportunity->id,
                'contact_id' => $event->opportunity->contact_id,
                'creation_data' => $event->creationData,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(OpportunityCreated $event, \Throwable $exception): void
    {
        Log::error("Failed to fire opportunity created workflows", [
            'opportunity_id' => $event->opportunity->id,
            'contact_id' => $event->opportunity->contact_id,
            'creation_data' => $event->creationData,
            'exception' => $exception
        ]);
    }
}
