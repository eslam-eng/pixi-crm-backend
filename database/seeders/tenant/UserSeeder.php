<?php

namespace Database\Seeders\tenant;

use App\Models\Tenant\User;
use Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin1@gamil.com',
            ],
            [
                'first_name' => 'first',
                'last_name' => 'admin',
                'email' => 'admin1@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01569207834',
            ]
        )->syncRoles('admin');

        User::updateOrCreate(
            [
                'email' => 'admin2@gamil.com',
            ],
            [
                'first_name' => 'second',
                'last_name' => 'admin',
                'email' => 'admin2@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01207834569',
            ]
        )->syncRoles('admin');

        User::updateOrCreate(
            ['email' => 'manager1@gamil.com'],
            [
                'first_name' => 'first',
                'last_name' => 'manager',
                'email' => 'manager1@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01234569078',
            ]
        )->syncRoles('manager');

        User::updateOrCreate(
            ['email' => 'manager2@gamil.com'],
            [
                'first_name' => 'second',
                'last_name' => 'manager',
                'email' => 'manager2@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01234569078',
            ]
        )->syncRoles('manager');

        User::updateOrCreate(
            ['email' => 'agent1@gamil.com'],
            [
                'first_name' => 'first',
                'last_name' => 'agent',
                'email' => 'agent1@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01234567890',
            ]
        )->syncRoles('agent');

        User::updateOrCreate(
            ['email' => 'agent2@gamil.com'],
            [
                'first_name' => 'second',
                'last_name' => 'agent',
                'email' => 'agent2@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '23401567890',
            ]
        )->syncRoles('agent');
    }
}
