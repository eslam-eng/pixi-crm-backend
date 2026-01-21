<?php

namespace App\Console\Commands\Central\Subscription;

use App\Enums\Landlord\SubscriptionStatusEnum;
use App\Enums\Landlord\TenantStatusEnum;
use App\Models\Central\Subscription;
use App\Models\Central\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire subscriptions and tenants that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting subscription expiration check...');

        // Part 1: Mark passed subscriptions as expired
        // Find active or trial subscriptions where ends_at is in the past
        $expiredSubscriptions = Subscription::query()
            ->whereIn('status', [SubscriptionStatusEnum::ACTIVE, SubscriptionStatusEnum::TRIAL])
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $this->info("Expiring subscription ID: {$subscription->id}");
            $subscription->update(['status' => SubscriptionStatusEnum::EXPIRED]);
        }

        $this->info('Finished expiring subscriptions.');

        // Part 2: Mark tenants as expired if they have no active subscription
        $this->info('Starting tenant expiration check...');

        // Optimize: Get all tenants that are currently ACTIVE or TRIAL
        Tenant::query()
            ->whereIn('status', [TenantStatusEnum::ACTIVE, TenantStatusEnum::TRIAL])
            ->chunk(100, function ($tenants) {
                foreach ($tenants as $tenant) {
                    // Check if tenant has any active subscription
                    // We can reuse the logic from Tenant::activeSubscription() or similar
                    // But here we want to know if *any* valid subscription exists.
    
                    // Logic: count subscriptions that are active/trial AND (ends_at is null OR ends_at > now)
                    $hasActiveSubscription = $tenant->subscriptions()
                        ->whereIn('status', [SubscriptionStatusEnum::ACTIVE, SubscriptionStatusEnum::TRIAL])
                        ->where(function ($query) {
                        $query->whereNull('ends_at')
                            ->orWhere('ends_at', '>', now());
                    })
                        ->exists();

                    if (!$hasActiveSubscription) {
                        $this->info("Expiring tenant ID: {$tenant->id} (Name: {$tenant->name})");
                        $tenant->update(['status' => TenantStatusEnum::EXPIRED]);
                    }
                }
            });

        $this->info('Finished expiring tenants.');
    }
}
