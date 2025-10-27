<?php

namespace Database\Seeders;

use App\Enums\Landlord\PermissionsEnum;
use App\Models\Central\Permission;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        if (Permission::count() > 0) {
            return;
        }
        foreach (PermissionsEnum::cases() as $permission) {
            Permission::query()->updateOrCreate([
                'name' => $permission->value,
                'group' => $permission->getGroup(),
            ]);
        }
    }
}
