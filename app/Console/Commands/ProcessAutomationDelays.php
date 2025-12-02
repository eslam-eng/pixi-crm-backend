<?php

namespace App\Console\Commands;

use App\Services\Tenant\Automation\AutomationWorkflowFireService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessAutomationDelays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automation:process-delays {--minutes=5 : Process delays due within the next X minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process delayed automation workflow steps that are ready to execute';

    /**
     * Execute the console command.
     */
    public function handle(AutomationWorkflowFireService $fireService): int
    {
        try {
            $this->info('Starting to process delayed automation steps...');

            // Process all ready delays and continue their workflows
            $fireService->processReadyDelays();

            $this->info('Successfully processed delayed automation steps.');
            $this->info('Workflow execution continued for subsequent steps where applicable.');
            Log::info("Processed delayed automation steps via cron job and continued workflow execution");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error processing delayed automation steps: " . $e->getMessage());
            Log::error("Error in ProcessAutomationDelays command: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return Command::FAILURE;
        }
    }
}
