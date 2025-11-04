<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Admin::count() == 0) {
            Admin::factory()->create(['email' => 'superadmin@localhost']);
            Admin::factory()->count(3)->create();
        }
    }
}
