<?php

namespace App\Console\Commands\Central\Subscription;

use App\Services\Central\SubscriptionService;
use Illuminate\Console\Command;

class AutoRenewSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:auto-renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically renew subscriptions that have auto_renew enabled and are expiring today.';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $this->info('Starting auto-renewal process...');

        $count = $subscriptionService->batchAutoRenew();

        $this->info("Successfully renewed {$count} subscriptions.");
    }
}
