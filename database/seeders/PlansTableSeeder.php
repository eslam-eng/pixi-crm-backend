<?php

namespace Database\Seeders;

use App\Models\Central\Plan;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create one plan with trial
        Plan::factory()->create([
            'name' => [
                'ar' => 'خطة تجريبية',
                'en' => 'Trial Plan',
            ],
            'monthly_price' => 0,
            'annual_price' => 0,
            'lifetime_price' => 0,
            'is_trial' => true,
            'trial_days' => 15,
            'sort_order' => 1,
        ]);

        // Create standard plans
        Plan::factory()->count(2)->create([
            'is_trial' => false,
        ]);


    }
}
