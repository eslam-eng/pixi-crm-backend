<?php
// database/seeders/AppSettingsSeeder.php

namespace Database\Seeders\tenant;

use App\Models\Tenant\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'send_email_notifications',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Send email notifications to users'
            ],
            [
                'key' => 'max_file_upload_size',
                'value' => '10485760',
                'type' => 'integer',
                'description' => 'Maximum file upload size in bytes (10MB)'
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable maintenance mode'
            ],
            [
                'key' => 'default_currency',
                'value' => 'USD',
                'type' => 'string',
                'description' => 'Default currency for transactions'
            ],
            [
                'key' => 'feature_flags',
                'value' => json_encode(['new_dashboard' => true, 'beta_features' => false]),
                'type' => 'json',
                'description' => 'Feature toggle flags'
            ]
        ];

        foreach ($settings as $setting) {
            AppSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
