<?php

namespace App\Events\Opportunity;

use App\Models\Tenant\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OpportunityCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $opportunity;
    public array $creationData;
    public array $configuration;

    /**
     * Create a new event instance.
     */
    public function __construct(Lead $opportunity, array $creationData = [], array $configuration = [])
    {
        $this->opportunity = $opportunity;
        $this->creationData = $creationData;
        $this->configuration = array_merge([
            'required_fields' => ['amount', 'country'],
            'default_pipeline' => 'Sales',
            'validation_rules' => [
                'amount' => 'required|numeric|min:0',
                'country' => 'required|string|max:255'
            ]
        ], $configuration);
    }
}
