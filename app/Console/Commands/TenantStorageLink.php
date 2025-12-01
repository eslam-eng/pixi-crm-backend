<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TenantStorageLink extends Command
{
    protected $signature = 'tenant:storage-link 
                            {tenant? : Specific tenant ID (optional)}
                            {--force : Recreate existing links}';
    
    protected $description = 'Create symbolic links for tenant storage';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $force = $this->option('force');

        if ($tenantId) {
            $this->createTenantLink($tenantId, $force);
        } else {
            $this->createLinksForAllTenants($force);
        }

        $this->newLine();
        $this->info('✓ Tenant storage link process completed!');
        return 0;
    }

    protected function createLinksForAllTenants($force)
    {
        $storagePath = storage_path();
        
        if (!File::exists($storagePath)) {
            $this->error('Storage directory does not exist!');
            return;
        }

        $tenantDirs = File::directories($storagePath);
        
        if (empty($tenantDirs)) {
            $this->warn('No tenant directories found in storage/');
            return;
        }

        $this->info('Found ' . count($tenantDirs) . ' directory(ies) in storage/');
        $this->newLine();

        foreach ($tenantDirs as $dir) {
            $dirName = basename($dir);
            
            // Only process directories starting with 'tenant'
            if (str_starts_with($dirName, 'tenant')) {
                $this->createTenantLink($dirName, $force);
            }
        }
    }

    protected function createTenantLink($tenantId, $force = false)
    {
        $this->info("Processing: {$tenantId}");
        
        $target = storage_path("{$tenantId}/app/public");
        $link = public_path("storage/{$tenantId}");

        // Ensure public/storage directory exists
        $this->ensurePublicStorageExists();

        // Check if target storage directory exists
        if (!File::exists($target)) {
            $this->warn("  ⚠ Storage path doesn't exist, creating: {$target}");
            
            try {
                File::makeDirectory($target, 0755, true, true);
                $this->info("  ✓ Created: {$target}");
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to create directory: " . $e->getMessage());
                return;
            }
        } else {
            $this->line("  ℹ Target exists: {$target}");
        }

        // Handle existing symlink or directory
        if (File::exists($link)) {
            if (is_link($link)) {
                if ($force) {
                    File::delete($link);
                    $this->info("  ✓ Removed existing symlink");
                } else {
                    $this->warn("  ⚠ Symlink already exists: {$link}");
                    $this->line("  → Use --force to recreate");
                    return;
                }
            } else {
                $this->error("  ✗ Path exists but is NOT a symlink: {$link}");
                $this->warn("  → Please manually remove this directory/file first");
                return;
            }
        }

        // Create the symlink
        try {
            File::link($target, $link);
            $this->info("  ✓ Symlink created successfully!");
            $this->line("  → {$link} → {$target}");
        } catch (\Exception $e) {
            $this->error("  ✗ Failed to create symlink: " . $e->getMessage());
        }

        $this->newLine();
    }

    protected function ensurePublicStorageExists()
    {
        $publicStorage = public_path('storage');
        
        if (!File::exists($publicStorage)) {
            try {
                File::makeDirectory($publicStorage, 0755, true, true);
                $this->info("✓ Created public/storage directory");
            } catch (\Exception $e) {
                // Directory might have been created by another process, check again
                if (!File::exists($publicStorage)) {
                    $this->error("Failed to create public/storage: " . $e->getMessage());
                }
            }
        }
    }
}