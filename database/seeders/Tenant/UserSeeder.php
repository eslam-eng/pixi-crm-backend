<?php

namespace Database\Seeders\Tenant;

use App\Models\Team;
use App\Models\Tenant\User;
use Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {



        $admin1 = User::updateOrCreate(
            [
                'email' => 'ashraf@gamil.com',
            ],
            [
                'first_name' => 'ashraf',
                'last_name' => 'mohamed',
                'email' => 'ashraf@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01569207834',
                'department_id' => 1,
            ]
        )->syncRoles('admin');

        $admin2 = User::updateOrCreate(
            [
                'email' => 'ahmed@gamil.com',
            ],
            [
                'first_name' => 'ahmed',
                'last_name' => 'mohamed',
                'email' => 'ahmed@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01207834569',
                'department_id' => 1,
            ]
        )->syncRoles('admin');


        // Create Teams
        $team1 = Team::updateOrCreate(
            [
                'title' => 'Team 1',
            ],
            [
                'leader_id' => $admin1->id,
                'title' => 'Team 1',
            ]
        );

        $team2 = Team::updateOrCreate(
            [
                'title' => 'Team 2',
            ],
            [
                'leader_id' => $admin2->id,
                'title' => 'Team 2',
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager1@gamil.com'],
            [
                'first_name' => 'ali',
                'last_name' => 'hassen',
                'email' => 'manager1@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01234569078',
                'department_id' => 1,
            ]
        )->syncRoles('manager');

        User::updateOrCreate(
            ['email' => 'manager2@gamil.com'],
            [
                'first_name' => 'ahmed',
                'last_name' => 'nasser',
                'email' => 'manager2@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01234569078',
                'department_id' => 1,
            ]
        )->syncRoles('manager');

        $agent1 = User::updateOrCreate(
            ['email' => 'agent1@gamil.com'],
            [
                'first_name' => 'ramy',
                'last_name' => 'gamaal',
                'email' => 'agent1@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01234567540',
                'department_id' => 1,
                'team_id' => $team1->id,
            ]
        )->syncRoles('agent');

        $agent2 = User::updateOrCreate(
            ['email' => 'agent2@gamil.com'],
            [
                'first_name' => 'mohamed',
                'last_name' => 'tamer',
                'email' => 'agent2@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '01234546890',
                'department_id' => 1,
                'team_id' => $team1->id,
            ]
        )->syncRoles('agent');

        $agent3 = User::updateOrCreate(
            ['email' => 'agent3@gamil.com'],
            [
                'first_name' => 'ahmed',
                'last_name' => 'kahlid',
                'email' => 'agent3@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '23404567890',
                'department_id' => 1,
                'team_id' => $team2->id,
            ]
        )->syncRoles('agent');

        $agent4 = User::updateOrCreate(
            ['email' => 'agent4@gamil.com'],
            [
                'first_name' => 'ahmed',
                'last_name' => 'al-sayed',
                'email' => 'agent4@gamil.com',
                'password' => Hash::make(123456),
                'phone' => '24501567890',
                'department_id' => 1,
                'team_id' => $team2->id,
            ]
        )->syncRoles('agent');
    }
}
