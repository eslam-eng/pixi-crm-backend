<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Priority;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get color IDs by name
        $greenColor = \App\Models\Tenant\PriorityColor::where('name', 'green')->first();
        $yellowColor = \App\Models\Tenant\PriorityColor::where('name', 'yellow')->first();
        $orangeColor = \App\Models\Tenant\PriorityColor::where('name', 'orange')->first();
        $redColor = \App\Models\Tenant\PriorityColor::where('name', 'red')->first();

        $priorities = [
            [
                'name' => 'Low',
                'color_id' => $greenColor->id,
                'level' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'Medium',
                'color_id' => $yellowColor->id,
                'level' => 2,
                'is_default' => false,
            ],
            [
                'name' => 'High',
                'color_id' => $orangeColor->id,
                'level' => 3,
                'is_default' => false,
            ],
            [
                'name' => 'Urgent',
                'color_id' => $redColor->id,
                'level' => 4,
                'is_default' => false,
            ],
        ];

        foreach ($priorities as $priority) {
            Priority::create($priority);
        }
    }
}
