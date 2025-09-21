<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() == 0) {
            User::updateOrCreate([
                'email' => 'client1@example.com',
            ], [
                'first_name' => 'client',
                'last_name' => 'one',
                'password' => Hash::make('123456'),
            ]);
            $this->command->info('UserSeeder: Created default user.');
            User::factory(10)->create();
        }

    }
}
