<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Tenant\Contact;
use App\Models\Tenant\Deal;
use App\Models\Tenant\Task;
use App\Models\Tenant\FormSubmission;
use App\Models\Tenant\Lead;
use App\Observers\ContactAutomationObserver;
use App\Observers\OpportunityAutomationObserver;
use App\Observers\DealAutomationObserver;
use App\Observers\TaskAutomationObserver;
use App\Observers\FormSubmissionAutomationObserver;
use Stancl\Tenancy\Events\TenancyInitialized;

class AutomationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register observers when tenancy is initialized
        Event::listen(TenancyInitialized::class, function () {
            Contact::observe(ContactAutomationObserver::class);
            Lead::observe(OpportunityAutomationObserver::class);
            Deal::observe(DealAutomationObserver::class);
            Task::observe(TaskAutomationObserver::class);
            FormSubmission::observe(FormSubmissionAutomationObserver::class);
        });

        // Also register immediately if tenancy is already initialized
        // This handles cases where the provider boots after tenancy initialization
        $this->app->booted(function () {
            if (function_exists('tenancy') && tenancy()->initialized) {
                if (!Contact::getEventDispatcher()->hasListeners('eloquent.created: ' . Contact::class)) {
                    Contact::observe(ContactAutomationObserver::class);
                    Lead::observe(OpportunityAutomationObserver::class);
                    Deal::observe(DealAutomationObserver::class);
                    Task::observe(TaskAutomationObserver::class);
                    FormSubmission::observe(FormSubmissionAutomationObserver::class);
                }
            }
        });
    }
}

