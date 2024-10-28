<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create permissions
        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'manage users']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(['view dashboard', 'manage users']);
        $userRole->givePermissionTo('view dashboard');
    }
}