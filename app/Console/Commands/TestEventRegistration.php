<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\Contacts\ContactCreated;
use App\Listeners\FireContactCreatedWorkflows;
use App\Providers\EventServiceProvider;
use Illuminate\Support\Facades\Event;

class TestEventRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:event-registration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test that events and listeners are properly registered';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Event Registration...');
        
        try {
            // Test ContactCreated event registration
            $this->info('Testing ContactCreated event registration...');
            
            // Check if the event is registered
            $listeners = Event::getListeners(ContactCreated::class);
            
            if (empty($listeners)) {
                $this->error("❌ No listeners found for ContactCreated event!");
                return 1;
            }
            
            $this->info("✅ Found " . count($listeners) . " listener(s) for ContactCreated event:");
            
            foreach ($listeners as $listener) {
                $listenerClass = is_string($listener) ? $listener : get_class($listener);
                $this->info("   - {$listenerClass}");
                
                if ($listenerClass === FireContactCreatedWorkflows::class) {
                    $this->info("     ✅ FireContactCreatedWorkflows listener is registered!");
                }
            }
            
            // Test other events
            $eventsToTest = [
                'App\Events\Contacts\ContactUpdated' => 'App\Listeners\FireContactUpdatedWorkflows',
                'App\Events\Contacts\ContactLeadQualified' => 'App\Listeners\FireContactLeadQualifiedWorkflows',
                'App\Events\Opportunity\OpportunityCreated' => 'App\Listeners\FireOpportunityCreatedWorkflows',
                'App\Events\Opportunity\OpportunityStageChanged' => 'App\Listeners\FireOpportunityStageChangedWorkflows',
                'App\Events\Contacts\ContactTagAdded' => 'App\Listeners\FireContactTagAddedWorkflows',
            ];
            
            $this->info("\nTesting other event registrations...");
            
            foreach ($eventsToTest as $eventClass => $expectedListener) {
                $listeners = Event::getListeners($eventClass);
                
                if (empty($listeners)) {
                    $this->warn("⚠️  No listeners found for {$eventClass}");
                } else {
                    $this->info("✅ {$eventClass} has " . count($listeners) . " listener(s)");
                    
                    foreach ($listeners as $listener) {
                        $listenerClass = is_string($listener) ? $listener : get_class($listener);
                        if ($listenerClass === $expectedListener) {
                            $this->info("   ✅ {$expectedListener} is registered!");
                        }
                    }
                }
            }
            
            $this->info("\n🎉 Event registration test completed!");
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error testing event registration: " . $e->getMessage());
            return 1;
        }
    }
}
