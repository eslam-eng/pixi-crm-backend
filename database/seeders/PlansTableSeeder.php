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
        // Create two plans without trial
        $standardPlans = Plan::factory()->create([
            'trial_days' => 0,
        ]);

        // Create one plan with trial days
        $trialPlan = Plan::factory()->create([
            'trial_days' => 14,
            'name' => 'Starter Plan',  // Give it a distinctive name
            'sort_order' => 1,  // Put it first in the list
        ]);
    }
}
