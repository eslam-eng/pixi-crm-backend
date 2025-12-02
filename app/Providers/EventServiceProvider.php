<?php

namespace App\Providers;

use App\Events\Contacts\ContactCreated;
use App\Events\Contacts\ContactUpdated;
use App\Events\Contacts\ContactLeadQualified;
use App\Events\Opportunity\OpportunityCreated;
use App\Events\Opportunity\OpportunityStageChanged;
use App\Events\Contacts\ContactTagAdded;
use App\Listeners\FireContactCreatedWorkflows;
use App\Listeners\FireContactUpdatedWorkflows;
use App\Listeners\FireContactLeadQualifiedWorkflows;
use App\Listeners\FireOpportunityCreatedWorkflows;
use App\Listeners\FireOpportunityStageChangedWorkflows;
use App\Listeners\FireContactTagAddedWorkflows;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ContactCreated::class => [
            FireContactCreatedWorkflows::class,
        ],
        ContactUpdated::class => [
            FireContactUpdatedWorkflows::class,
        ],
        ContactLeadQualified::class => [
            FireContactLeadQualifiedWorkflows::class,
        ],
        OpportunityCreated::class => [
            FireOpportunityCreatedWorkflows::class,
        ],
        OpportunityStageChanged::class => [
            FireOpportunityStageChangedWorkflows::class,
        ],
        ContactTagAdded::class => [
            FireContactTagAddedWorkflows::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
