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
        Log::info('Starting subscription expiration check...');

        // Part 1: Mark passed subscriptions as expired
        // Find active or trial subscriptions where ends_at is in the past
        $expiredSubscriptions = Subscription::query()
            ->whereIn('status', [SubscriptionStatusEnum::ACTIVE->value, SubscriptionStatusEnum::TRIAL->value])
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $statusValue = SubscriptionStatusEnum::EXPIRED->value;
            Log::info("Expiring subscription ID: {$subscription->id}. Old Status: {$subscription->status->value}. Setting to: {$statusValue}");

            // Explicitly update using value
            $subscription->update(['status' => $statusValue]);
        }

        Log::info('Finished expiring subscriptions.');

        // Part 2: Mark tenants as expired if they have no active subscription
        Log::info('Starting tenant expiration check...');

        // Optimize: Get all tenants that are currently ACTIVE or TRIAL but have NO matching active subscription
        Tenant::query()
            ->where('status','!=', TenantStatusEnum::EXPIRED->value)
            ->whereDoesntHave('subscriptions', function ($query) {
                $query->whereIn('status', [SubscriptionStatusEnum::ACTIVE->value, SubscriptionStatusEnum::TRIAL->value])
                    ->where(function ($q) {
                        $q->whereNull('ends_at')
                            ->orWhere('ends_at', '>', now());
                    });
            })
            ->get()
            ->each(function ($tenant) {
                Log::info("Expiring tenant ID: {$tenant->id} (Name: {$tenant->name}).");
                $tenant->update(['status' => TenantStatusEnum::EXPIRED->value]);
            });

        Log::info('Finished expiring tenants.');
    }
}
