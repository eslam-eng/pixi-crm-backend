<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Central\Tenant;
use Illuminate\Support\Str;

class GenerateTenantApiKey extends Command
{
    protected $signature = 'tenant:generate-api-key {id}';
    protected $description = 'Generate a new API Key for a Tenant (useful for Zapier)';

    public function handle()
    {
        $id = $this->argument('id');
        $tenant = Tenant::find($id);

        if (!$tenant) {
            // Try to find by domain or other field if needed, but ID is safest
            $this->error("Tenant with ID {$id} not found.");
            return 1;
        }

        $apiKey = Str::random(32);

        // Update the zapier_api_key in data column
        $data = $tenant->data ?? [];
        $data['zapier_api_key'] = $apiKey;
        $tenant->update(['data' => $data]);

        $this->info("API Key generated for Tenant {$tenant->name} ({$id}) in Settings:");
        $this->line($apiKey);

        return 0;
    }
}
