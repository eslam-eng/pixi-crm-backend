<?php

namespace Database\Seeders;

use App\Models\Tenant\Integration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integrations = [
            ['name' => 'Meta (Facebook)'],
            ['name' => 'Google Ads'],
            ['name' => 'TikTok'],
            ['name' => 'WhatsApp'],
        ];

        foreach ($integrations as $integration) {
            Integration::firstOrCreate(
                ['name' => $integration['name']],
                $integration
            );
        }
    }
}
