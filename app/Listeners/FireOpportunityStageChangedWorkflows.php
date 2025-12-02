<?php

namespace App\Listeners;

use App\Events\Opportunity\OpportunityStageChanged;
use App\Services\Tenant\Automation\AutomationWorkflowFireService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class FireOpportunityStageChangedWorkflows implements ShouldQueue
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
    public function handle(OpportunityStageChanged $event): void
    {
        try {
            $fireService = app(AutomationWorkflowFireService::class);
            $fireService->fireOpportunityStageChangedWorkflows(
                $event->opportunity, 
                $event->oldStage, 
                $event->newStage, 
                $event->stageChangeData
            );

            Log::info("Opportunity stage changed workflows fired", [
                'opportunity_id' => $event->opportunity->id,
                'contact_id' => $event->opportunity->contact_id,
                'contact_name' => $event->opportunity->contact->name ?? 'Unknown',
                'old_stage_id' => $event->oldStage?->id,
                'old_stage_name' => $event->oldStage?->name,
                'new_stage_id' => $event->newStage?->id,
                'new_stage_name' => $event->newStage?->name,
                'stage_change_data' => $event->stageChangeData,
            ]);

        } catch (\Exception $e) {
            Log::error("Error firing opportunity stage changed workflows: " . $e->getMessage(), [
                'opportunity_id' => $event->opportunity->id,
                'contact_id' => $event->opportunity->contact_id,
                'old_stage_id' => $event->oldStage?->id,
                'new_stage_id' => $event->newStage?->id,
                'stage_change_data' => $event->stageChangeData,
                'exception' => $e
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(OpportunityStageChanged $event, \Throwable $exception): void
    {
        Log::error("Failed to fire opportunity stage changed workflows", [
            'opportunity_id' => $event->opportunity->id,
            'contact_id' => $event->opportunity->contact_id,
            'old_stage_id' => $event->oldStage?->id,
            'new_stage_id' => $event->newStage?->id,
            'stage_change_data' => $event->stageChangeData,
            'exception' => $exception
        ]);
    }
}
