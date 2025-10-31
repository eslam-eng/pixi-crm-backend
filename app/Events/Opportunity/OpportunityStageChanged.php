<?php

namespace App\Events\Opportunity;

use App\Models\Tenant\Lead;
use App\Models\Stage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OpportunityStageChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $opportunity;
    public ?Stage $oldStage;
    public ?Stage $newStage;
    public array $stageChangeData;

    /**
     * Create a new event instance.
     */
    public function __construct(Lead $opportunity, ?Stage $oldStage, ?Stage $newStage, array $stageChangeData = [])
    {
        $this->opportunity = $opportunity;
        $this->oldStage = $oldStage;
        $this->newStage = $newStage;
        $this->stageChangeData = $stageChangeData;
    }
}
