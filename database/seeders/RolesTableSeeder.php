<?php

namespace Database\Seeders;

use App\Models\Central\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Role::count() > 0) {
            return;
        }
        Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['is_active' => true, 'description' => 'Full access to all features']
        );
    }
}
