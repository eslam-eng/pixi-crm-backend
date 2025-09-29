<?php

namespace Tests\Unit;

use App\Models\Tenant\Deal;
use App\Models\Tenant\Lead;
use App\Models\Tenant\Contact;
use App\Models\Tenant\User;
use App\Models\Tenant\Department;
use App\Services\Tenant\Deals\DealService;
use App\Enums\PaymentStatusEnum;
use App\Notifications\DealPaymentTermsNotification;
use App\Settings\DealsSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DealServicePaymentTermsEmailTest extends TestCase
{
    use RefreshDatabase;

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
    public function sendPaymentTermsEmailIfNeeded_sends_email_for_unpaid_deal()
    {
        Notification::fake();
        Log::spy();
        
        // Create test data
        $contact = Contact::factory()->create(['email' => 'test@example.com']);
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $deal = Deal::factory()->create([
            'lead_id' => $lead->id,
            'payment_status' => PaymentStatusEnum::UNPAID->value,
            'total_amount' => 1000.00,
            'amount_due' => 1000.00,
        ]);

        $dealService = new DealService(
            new Deal(), 
            new \App\Models\Tenant\Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($dealService);
        $method = $reflection->getMethod('sendPaymentTermsEmailIfNeeded');
        $method->setAccessible(true);
        
        $settings = app(DealsSettings::class);
        $method->invoke($dealService, $deal, $settings);

        // Assert notification was sent
        Notification::assertSentTo($contact, DealPaymentTermsNotification::class, function ($notification) use ($deal) {
            return $notification->deal->id === $deal->id &&
                   $notification->paymentTerms === 'Payment is due within 30 days of invoice date.';
        });

        // Assert success log
        Log::shouldHaveReceived('info')
            ->with('Payment terms notification sent successfully', \Mockery::type('array'));
    }

    /** @test */
    public function sendPaymentTermsEmailIfNeeded_sends_email_for_partial_deal()
    {
        Notification::fake();
        Log::spy();
        
        // Create test data
        $contact = Contact::factory()->create(['email' => 'test@example.com']);
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $deal = Deal::factory()->create([
            'lead_id' => $lead->id,
            'payment_status' => PaymentStatusEnum::PARTIAL->value,
            'total_amount' => 1000.00,
            'partial_amount_paid' => 500.00,
            'amount_due' => 500.00,
        ]);

        $dealService = new DealService(
            new Deal(), 
            new \App\Models\Tenant\Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($dealService);
        $method = $reflection->getMethod('sendPaymentTermsEmailIfNeeded');
        $method->setAccessible(true);
        
        $settings = app(DealsSettings::class);
        $method->invoke($dealService, $deal, $settings);

        // Assert email was sent
        Notification::assertSentTo($contact, DealPaymentTermsNotification::class);

        // Assert success log
        Log::shouldHaveReceived('info')
            ->with('Payment terms notification sent successfully', \Mockery::type('array'));
    }

    /** @test */
    public function sendPaymentTermsEmailIfNeeded_does_not_send_email_for_paid_deal()
    {
        Notification::fake();
        Log::spy();
        
        // Create test data
        $contact = Contact::factory()->create(['email' => 'test@example.com']);
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $deal = Deal::factory()->create([
            'lead_id' => $lead->id,
            'payment_status' => PaymentStatusEnum::PAID->value,
            'total_amount' => 1000.00,
            'amount_due' => 0.00,
        ]);

        $dealService = new DealService(
            new Deal(), 
            new \App\Models\Tenant\Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($dealService);
        $method = $reflection->getMethod('sendPaymentTermsEmailIfNeeded');
        $method->setAccessible(true);
        
        $settings = app(DealsSettings::class);
        $method->invoke($dealService, $deal, $settings);

        // Assert email was not sent
        Notification::assertNothingSent();
        
        // Assert no log calls
        Log::shouldNotHaveReceived('info');
        Log::shouldNotHaveReceived('warning');
        Log::shouldNotHaveReceived('error');
    }

    /** @test */
    public function sendPaymentTermsEmailIfNeeded_logs_warning_when_contact_email_missing()
    {
        Notification::fake();
        Log::spy();
        
        // Create test data without email
        $contact = Contact::factory()->create(['email' => null]);
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $deal = Deal::factory()->create([
            'lead_id' => $lead->id,
            'payment_status' => PaymentStatusEnum::UNPAID->value,
            'total_amount' => 1000.00,
            'amount_due' => 1000.00,
        ]);

        $dealService = new DealService(
            new Deal(), 
            new \App\Models\Tenant\Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($dealService);
        $method = $reflection->getMethod('sendPaymentTermsEmailIfNeeded');
        $method->setAccessible(true);
        
        $settings = app(DealsSettings::class);
        $method->invoke($dealService, $deal, $settings);

        // Assert email was not sent
        Notification::assertNothingSent();
        
        // Assert warning log
        Log::shouldHaveReceived('warning')
            ->with('Cannot send payment terms email: Lead or contact email not found', \Mockery::type('array'));
    }

    /** @test */
    public function sendPaymentTermsEmailIfNeeded_logs_error_when_email_fails()
    {
        Notification::fake();
        Log::spy();
        
        // Create test data
        $contact = Contact::factory()->create(['email' => 'test@example.com']);
        
        // Mock contact to throw exception when notify is called
        $contact->shouldReceive('notify')->andThrow(new \Exception('SMTP Error'));
        $lead = Lead::factory()->create(['contact_id' => $contact->id]);
        $deal = Deal::factory()->create([
            'lead_id' => $lead->id,
            'payment_status' => PaymentStatusEnum::UNPAID->value,
            'total_amount' => 1000.00,
            'amount_due' => 1000.00,
        ]);

        $dealService = new DealService(
            new Deal(), 
            new \App\Models\Tenant\Item(), 
            new \App\Models\Tenant\ItemVariant(),
            new \App\Services\Tenant\Deals\DealPaymentService(new \App\Models\Tenant\DealPayment())
        );
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($dealService);
        $method = $reflection->getMethod('sendPaymentTermsEmailIfNeeded');
        $method->setAccessible(true);
        
        $settings = app(DealsSettings::class);
        $method->invoke($dealService, $deal, $settings);

        // Assert error log
        Log::shouldHaveReceived('error')
            ->with('Failed to send payment terms email', \Mockery::type('array'));
    }
}
