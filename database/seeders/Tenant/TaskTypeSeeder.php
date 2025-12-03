<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $types = [
            ['name' => 'Call', 'icon' => 'phone', 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
            ['name' => 'Meeting', 'icon' => null, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 1],
            ['name' => 'Email', 'icon' => 'mail', 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
            ['name' => 'Follow-up', 'icon' => null, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
            ['name' => 'Presentation', 'icon' => null, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
            ['name' => 'Demo', 'icon' => null, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
            ['name' => 'Other', 'icon' => null, 'created_at' => $now, 'updated_at' => $now, 'is_default' => 0],
        ];

        DB::table('task_types')->upsert(
            $types,
            ['name'],                 // unique-by
            ['icon', 'updated_at', 'is_default']     // columns to update on conflict
        );
    }
}
