<?php

namespace Tests\Feature;

use App\Models\Tenant\Deal;
use App\Models\Tenant\Lead;
use App\Models\Tenant\Contact;
use App\Models\Tenant\User;
use App\Models\Tenant\Department;
use App\Models\Tenant\Item;
use App\Services\Tenant\Deals\DealService;
use App\DTO\Tenant\DealDTO;
use App\Enums\PaymentStatusEnum;
use App\Notifications\DealPaymentTermsNotification;
use App\Settings\DealsSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DealPaymentTermsEmailTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test department and user
        Department::factory()->create();
        User::factory()->create();
        
        // Set up DealsSettings
        app(DealsSettings::class)->payment_terms_text = 'Payment is due within 30 days of invoice date.';
    }

    /** @test */
    public function it_sends_payment_terms_email_for_unpaid_deal()
    {
        Notification::fake();
        
        // Create test data
        $contact = Contact::factory()->create(['email' => 'test@example.com']);
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $item = Item::factory()->create();
        
        // Create deal data
        $dealData = [
            'deal_type' => 'product_sale',
            'deal_name' => 'Test Deal',
            'lead_id' => $lead->id,
            'sale_date' => now()->toDateString(),
            'payment_status' => PaymentStatusEnum::UNPAID->value,
            'total_amount' => 1000.00,
            'amount_due' => 1000.00,
            'items' => [
                [
                    'id' => $item->id,
                    'quantity' => 1,
                    'price' => 1000.00,
                    'total' => 1000.00
                ]
            ]
        ];

        $dealDTO = DealDTO::fromArray($dealData);
        $dealService = new DealService(
            new Deal(), 
            new Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );

        // Create the deal
        $deal = $dealService->store($dealDTO);

        // Assert email was sent
        Notification::assertSentTo($contact, DealPaymentTermsNotification::class, function ($notification) use ($deal) {
            return $notification->deal->id === $deal->id &&
                   $notification->paymentTerms === 'Payment is due within 30 days of invoice date.';
        });
    }

    /** @test */
    public function it_sends_payment_terms_email_for_partial_deal()
    {
        Notification::fake();
        
        // Create test data
        $contact = Contact::factory()->create(['email' => 'test@example.com']);
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $item = Item::factory()->create();
        
        // Create deal data
        $dealData = [
            'deal_type' => 'product_sale',
            'deal_name' => 'Test Deal',
            'lead_id' => $lead->id,
            'sale_date' => now()->toDateString(),
            'payment_status' => PaymentStatusEnum::PARTIAL->value,
            'total_amount' => 1000.00,
            'partial_amount_paid' => 500.00,
            'amount_due' => 500.00,
            'items' => [
                [
                    'id' => $item->id,
                    'quantity' => 1,
                    'price' => 1000.00,
                    'total' => 1000.00
                ]
            ]
        ];

        $dealDTO = DealDTO::fromArray($dealData);
        $dealService = new DealService(
            new Deal(), 
            new Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );

        // Create the deal
        $deal = $dealService->store($dealDTO);

        // Assert email was sent
        Notification::assertSentTo($contact, DealPaymentTermsNotification::class, function ($notification) use ($deal) {
            return $notification->deal->id === $deal->id &&
                   $notification->deal->payment_status === PaymentStatusEnum::PARTIAL->value;
        });
    }

    /** @test */
    public function it_does_not_send_email_for_paid_deal()
    {
        Notification::fake();
        
        // Create test data
        $contact = Contact::factory()->create(['email' => 'test@example.com']);
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $item = Item::factory()->create();
        
        // Create deal data
        $dealData = [
            'deal_type' => 'product_sale',
            'deal_name' => 'Test Deal',
            'lead_id' => $lead->id,
            'sale_date' => now()->toDateString(),
            'payment_status' => PaymentStatusEnum::PAID->value,
            'total_amount' => 1000.00,
            'amount_due' => 0.00,
            'items' => [
                [
                    'id' => $item->id,
                    'quantity' => 1,
                    'price' => 1000.00,
                    'total' => 1000.00
                ]
            ]
        ];

        $dealDTO = DealDTO::fromArray($dealData);
        $dealService = new DealService(
            new Deal(), 
            new Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );

        // Create the deal
        $deal = $dealService->store($dealDTO);

        // Assert email was not sent
        Notification::assertNothingSent();
    }

    /** @test */
    public function it_logs_warning_when_contact_email_is_missing()
    {
        Notification::fake();
        \Log::spy();
        
        // Create test data without email
        $contact = Contact::factory()->create(['email' => null]);
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $item = Item::factory()->create();
        
        // Create deal data
        $dealData = [
            'deal_type' => 'product_sale',
            'deal_name' => 'Test Deal',
            'lead_id' => $lead->id,
            'sale_date' => now()->toDateString(),
            'payment_status' => PaymentStatusEnum::UNPAID->value,
            'total_amount' => 1000.00,
            'amount_due' => 1000.00,
            'items' => [
                [
                    'id' => $item->id,
                    'quantity' => 1,
                    'price' => 1000.00,
                    'total' => 1000.00
                ]
            ]
        ];

        $dealDTO = DealDTO::fromArray($dealData);
        $dealService = new DealService(
            new Deal(), 
            new Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );

        // Create the deal
        $deal = $dealService->store($dealDTO);

        // Assert email was not sent and warning was logged
        Notification::assertNothingSent();
        \Log::shouldHaveReceived('warning')
            ->with('Cannot send payment terms email: Lead or contact email not found', \Mockery::type('array'));
    }

    /** @test */
    public function email_contains_correct_deal_information()
    {
        Notification::fake();
        
        // Create test data
        $contact = Contact::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com'
        ]);
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $item = Item::factory()->create();
        
        // Create deal data
        $dealData = [
            'deal_type' => 'service_sale',
            'deal_name' => 'Premium Service Deal',
            'lead_id' => $lead->id,
            'sale_date' => now()->toDateString(),
            'payment_status' => PaymentStatusEnum::PARTIAL->value,
            'total_amount' => 2500.00,
            'partial_amount_paid' => 1000.00,
            'amount_due' => 1500.00,
            'notes' => 'Special pricing for bulk order',
            'items' => [
                [
                    'id' => $item->id,
                    'quantity' => 1,
                    'price' => 2500.00,
                    'total' => 2500.00
                ]
            ]
        ];

        $dealDTO = DealDTO::fromArray($dealData);
        $dealService = new DealService(
            new Deal(), 
            new Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );

        // Create the deal
        $deal = $dealService->store($dealDTO);

        // Assert email was sent with correct data
        Notification::assertSentTo($contact, DealPaymentTermsNotification::class, function ($notification) use ($deal) {
            return $notification->deal->deal_name === 'Premium Service Deal' &&
                   $notification->deal->payment_status === PaymentStatusEnum::PARTIAL->value &&
                   $notification->deal->total_amount == 2500.00 &&
                   $notification->deal->partial_amount_paid == 1000.00 &&
                   $notification->deal->amount_due == 1500.00;
        });
    }
}
