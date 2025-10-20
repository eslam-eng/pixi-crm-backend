<?php

namespace Database\Seeders;

use App\Models\Tenant\Integration;
use App\Enums\IntegrationStatusEnum;
use App\Enums\PlatformEnum;
use Illuminate\Database\Seeder;

class IntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $integrations = [
            [
                'name' => 'Google Ads Campaign Sync',
                'platform' => PlatformEnum::GOOGLE->value,
                'status' => IntegrationStatusEnum::DISCONNECTED->value,
                'is_active' => false,
            ],
            [
                'name' => 'Meta Business Integration',
                'platform' => PlatformEnum::META->value,
                'status' => IntegrationStatusEnum::DISCONNECTED->value,
                'is_active' => false,
            ],
            [
                'name' => 'TikTok Ads Analytics',
                'platform' => PlatformEnum::TIKTOK->value,
                'status' => IntegrationStatusEnum::DISCONNECTED->value,
                'is_active' => false,
            ],
        ];

        foreach ($integrations as $integration) {
            Integration::firstOrCreate(
                ['name' => $integration['name']],
                $integration
            );
        }
    }
}
