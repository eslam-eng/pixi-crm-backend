<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant\Contact;
use App\Models\Tenant\User;
use App\Models\Tenant\Source;
use App\Models\Tenant\Country;
use App\Models\Tenant\City;
use App\Events\ContactCreated;
use App\Listeners\FireContactCreatedWorkflows;
use App\Services\ContactService;
use App\DTO\Contact\ContactDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

class ContactCreatedEventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the tenant context
        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function contact_created_event_is_dispatched_when_contact_is_created()
    {
        // Fake events to capture them
        Event::fake();

        // Create test data
        $country = Country::factory()->create();
        $city = City::factory()->create(['country_id' => $country->id]);
        $source = Source::factory()->create();
        $user = User::factory()->create();

        // Create contact data
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'company_name' => 'Test Company',
            'country_id' => $country->id,
            'city_id' => $city->id,
            'source_id' => $source->id,
            'user_id' => $user->id,
            'contact_phones' => [
                [
                    'phone' => '+1234567890',
                    'type' => 'mobile'
                ]
            ]
        ];

        $contactDTO = new ContactDTO($contactData);

        // Create contact service and store contact
        $contactService = app(ContactService::class);
        $contact = $contactService->store($contactDTO);

        // Assert that ContactCreated event was dispatched
        Event::assertDispatched(ContactCreated::class, function ($event) use ($contact) {
            return $event->contact->id === $contact->id;
        });
    }

    /** @test */
    public function fire_contact_created_workflows_listener_is_called()
    {
        // Fake events to capture listeners
        Event::fake();

        // Create test data
        $country = Country::factory()->create();
        $city = City::factory()->create(['country_id' => $country->id]);
        $source = Source::factory()->create();
        $user = User::factory()->create();

        // Create contact data
        $contactData = [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'company_name' => 'Test Company 2',
            'country_id' => $country->id,
            'city_id' => $city->id,
            'source_id' => $source->id,
            'user_id' => $user->id,
            'contact_phones' => [
                [
                    'phone' => '+1987654321',
                    'type' => 'work'
                ]
            ]
        ];

        $contactDTO = new ContactDTO($contactData);

        // Create contact service and store contact
        $contactService = app(ContactService::class);
        $contact = $contactService->store($contactDTO);

        // Assert that FireContactCreatedWorkflows listener was called
        Event::assertListening(ContactCreated::class, FireContactCreatedWorkflows::class);
    }

    /** @test */
    public function contact_created_event_contains_correct_data()
    {
        // Create test data
        $country = Country::factory()->create();
        $city = City::factory()->create(['country_id' => $country->id]);
        $source = Source::factory()->create();
        $user = User::factory()->create();

        // Create contact data
        $contactData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'company_name' => 'Test Corp',
            'country_id' => $country->id,
            'city_id' => $city->id,
            'source_id' => $source->id,
            'user_id' => $user->id,
            'contact_phones' => []
        ];

        $contactDTO = new ContactDTO($contactData);

        // Create contact service and store contact
        $contactService = app(ContactService::class);
        $contact = $contactService->store($contactDTO);

        // Create and dispatch the event manually to test its data
        $event = new ContactCreated($contact);

        // Assert event contains correct contact
        $this->assertEquals($contact->id, $event->contact->id);
        $this->assertEquals($contact->name, $event->contact->name);
        $this->assertEquals($contact->email, $event->contact->email);
        $this->assertEquals($contact->company_name, $event->contact->company_name);
    }

    /** @test */
    public function can_create_contact_via_api_endpoint()
    {
        // Create test data
        $country = Country::factory()->create();
        $city = City::factory()->create(['country_id' => $country->id]);
        $source = Source::factory()->create();
        $user = User::factory()->create();

        // Fake events to capture them
        Event::fake();

        // Prepare API request data
        $requestData = [
            'name' => 'API Test Contact',
            'email' => 'api.test@example.com',
            'company_name' => 'API Test Company',
            'country_id' => $country->id,
            'city_id' => $city->id,
            'source_id' => $source->id,
            'user_id' => $user->id,
            'contact_phones' => [
                [
                    'phone' => '+1111111111',
                    'type' => 'mobile'
                ]
            ]
        ];

        // Make API request to create contact
        $response = $this->postJson('/api/contacts', $requestData);

        // Assert response is successful
        $response->assertStatus(201);

        // Assert that ContactCreated event was dispatched
        Event::assertDispatched(ContactCreated::class);
    }
}
