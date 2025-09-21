<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1], // always use id=1 for the single record
            [
                'sources' => json_encode(['Website', 'Email', 'Phone', 'Referral', 'Social Media']),
                'industries' => json_encode(['IT', 'Finance', 'Healthcare']),
                'departments' => json_encode(['Sales', 'Support', 'HR']),
            ]
        );
    }
}
