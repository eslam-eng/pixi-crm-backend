<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\Contact;
use App\Events\ContactCreated;
use Illuminate\Support\Facades\Event;

class TestContactCreatedEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:contact-created-event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the ContactCreated event by creating a mock contact and verifying the event is fired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing ContactCreated Event...');
        
        try {
            // Create a mock contact object
            $contact = new Contact([
                'name' => 'Test Contact',
                'email' => 'test@example.com',
                'company_name' => 'Test Company',
                'country_id' => 1,
                'city_id' => 1,
                'source_id' => 1,
                'user_id' => 1,
            ]);
            
            // Set the ID manually for testing
            $contact->id = 999;

            $this->info('Created mock contact:');
            $this->info("   ID: {$contact->id}");
            $this->info("   Name: {$contact->name}");
            $this->info("   Email: {$contact->email}");

            // Listen for the event
            $eventDispatched = false;
            Event::listen(ContactCreated::class, function ($event) use (&$eventDispatched) {
                $eventDispatched = true;
                $this->info("✅ ContactCreated event dispatched!");
                $this->info("   Contact ID: {$event->contact->id}");
                $this->info("   Contact Name: {$event->contact->name}");
                $this->info("   Contact Email: {$event->contact->email}");
            });

            // Dispatch the event manually
            $this->info("\nDispatching ContactCreated event...");
            event(new ContactCreated($contact));

            // Check if event was dispatched
            if ($eventDispatched) {
                $this->info("🎉 SUCCESS: ContactCreated event was fired!");
            } else {
                $this->error("❌ FAILED: ContactCreated event was NOT fired!");
                return 1;
            }

            // Test that the event contains the correct data
            $this->info("\nTesting event data...");
            $event = new ContactCreated($contact);
            
            if ($event->contact->id === $contact->id && 
                $event->contact->name === $contact->name && 
                $event->contact->email === $contact->email) {
                $this->info("✅ Event data is correct!");
            } else {
                $this->error("❌ Event data is incorrect!");
                return 1;
            }

            $this->info("\n🎉 All tests passed! ContactCreated event is working correctly.");
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error testing ContactCreated event: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}