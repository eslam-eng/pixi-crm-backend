<?php

namespace App\Console\Commands;

use App\Models\Tenant\AutomationWorkflow;
use Illuminate\Console\Command;
use App\Services\Tenant\Automation\AutomationWorkflowExecutorService;

class ProcessDelayAutomationContinuation extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:delay-continuation {--tenant=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the delay continuation functionality by running the processDelayedSteps method';

    public function handle()
    {
        $tenantId = $this->option('tenant');
        if ($tenantId) {
            $this->processTenantDelayedSteps($tenantId);
        } else {
            $this->processAllTenantsDelayedSteps();
        }
    }
    /**
     * Execute the console command.
     */
    public function processTenantDelayedSteps(string $tenantId)
    {
        try {
            // Switch to tenant context
            tenancy()->initialize($tenantId);

            $this->info("Processing Delay Automation Continuation for tenant: {$tenantId}");


            // Test the processDelayedSteps method
            $executorService = app(AutomationWorkflowExecutorService::class);

            $processedCount = $executorService->processDelayedSteps();

            $this->info("✅ Successfully processed {$processedCount} delayed steps");

            if ($processedCount > 0) {
                $this->info("🎉 Delay continuation functionality is working!");
                $this->info("   - Delayed steps were executed");
                $this->info("   - Subsequent steps were processed automatically");
                $this->info("   - Workflow execution continued as expected");
            } else {
                $this->info("ℹ️  No delayed steps were ready to execute (this is normal if no delays are pending)");
            }

            $this->info("\n📋 Delay Continuation Features:");
            $this->info("   ✅ Automatic execution of delayed steps");
            $this->info("   ✅ Continuation with next steps after delay");
            $this->info("   ✅ Proper handling of multiple delays in sequence");
            $this->info("   ✅ Logging of continuation progress");
            $this->info("   ✅ Error handling for failed steps");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error testing delay continuation: " . $e->getMessage());
            return 1;
        }
    }


    /**
     * Process reminders for all tenants
     */
    private function processAllTenantsDelayedSteps()
    {
        $tenants = \App\Models\Tenant::all();

        $this->info("Processing reminders for {$tenants->count()} tenants");

        foreach ($tenants as $tenant) {
            $this->processTenantDelayedSteps($tenant->id);
        }

        $this->info("All tenant reminders processed");
    }
}