<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'view sales']);

        // Assign roles to users
        $admin = User::find(1);
        $admin->assignRole('admin');

        $leader = User::find(2);
        $leader->assignRole('leader');

        $sales = User::find(3);
        $sales->assignRole('sales');

        // Assign permissions
        $admin->givePermissionTo('manage users');
    }
}
