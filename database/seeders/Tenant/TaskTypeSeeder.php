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
            ['name' => 'Call', 'icon' => 'phone', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Meeting', 'icon' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Email', 'icon' => 'mail', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Follow-up', 'icon' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Presentation', 'icon' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Demo', 'icon' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Other', 'icon' => null, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('task_types')->upsert(
            $types,
            ['name'],                 // unique-by
            ['icon', 'updated_at']     // columns to update on conflict
        );
    }
}
