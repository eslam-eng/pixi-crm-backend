<?php

namespace App\Console\Commands;

use App\Models\Tenant\Reminder;
use App\Services\Tenant\Tasks\ReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:reminders {--tenant=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending reminders for tasks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->option('tenant');
        
        if ($tenantId) {
            // Process reminders for specific tenant
            $this->processTenantReminders($tenantId);
        } else {
            // Process reminders for all tenants
            $this->processAllTenantsReminders();
        }
    }

    /**
     * Process reminders for a specific tenant
     */
    private function processTenantReminders(string $tenantId)
    {
        try {
            // Switch to tenant context
            tenancy()->initialize($tenantId);
            
            $this->info("Processing reminders for tenant: {$tenantId}");
            
            $reminderService = new ReminderService(new Reminder());
            $reminderService->processPendingReminders();
            
            $this->info("Reminders processed successfully for tenant: {$tenantId}");
            
        } catch (\Exception $e) {
            $this->error("Error processing reminders for tenant {$tenantId}: " . $e->getMessage());
            Log::error("Reminder processing error for tenant {$tenantId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Process reminders for all tenants
     */
    private function processAllTenantsReminders()
    {
        $tenants = \App\Models\Tenant::all();
        
        $this->info("Processing reminders for {$tenants->count()} tenants");
        
        foreach ($tenants as $tenant) {
            $this->processTenantReminders($tenant->id);
        }
        
        $this->info("All tenant reminders processed");
    }
}