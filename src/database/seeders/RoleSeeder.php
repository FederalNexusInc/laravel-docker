<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleName = 'User';
        $guardName = 'web';

        $userRole = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => $guardName]
        );

        $permissionIds = [
            1, 2, 3, 4, 5, 9, 13, 14, 15, 16, 17, 21, 25, 26, 27, 28, 29, 33,
            37, 38, 39, 40, 41, 45, 55, 56, 57, 58, 59, 63, 79, 80, 81, 82, 83, 87, 103
        ];

        $permissions = Permission::whereIn('id', $permissionIds)->get();

        $userRole->syncPermissions($permissions);

        $this->command->info("Role '{$roleName}' created/updated and permissions synced successfully.");
    }
}
