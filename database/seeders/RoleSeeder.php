<?php

namespace Database\Seeders;

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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = [
            'Super Admin',
            'Ketua',
            'Wakil Ketua',
            'BPH',
            'Anggota',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $this->command->info('Roles created successfully!');

        // Create permissions (optional - add if needed)
        $permissions = [
            // Schedule permissions
            'view schedules',
            'create schedules',
            'edit schedules',
            'delete schedules',
            'publish schedules',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Availability permissions
            'submit availability',
            'view availability',
            
            // Report permissions
            'view reports',
            'export reports',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        $this->command->info('Permissions created successfully!');

        // Assign permissions to roles
        $superAdmin = Role::findByName('Super Admin');
        $superAdmin->givePermissionTo(Permission::all());

        $ketua = Role::findByName('Ketua');
        $ketua->givePermissionTo([
            'view schedules',
            'create schedules',
            'edit schedules',
            'delete schedules',
            'publish schedules',
            'view users',
            'view availability',
            'view reports',
            'export reports',
        ]);

        $wakilKetua = Role::findByName('Wakil Ketua');
        $wakilKetua->givePermissionTo([
            'view schedules',
            'create schedules',
            'edit schedules',
            'publish schedules',
            'view users',
            'view availability',
            'view reports',
        ]);

        $bph = Role::findByName('BPH');
        $bph->givePermissionTo([
            'view schedules',
            'edit schedules',
            'view users',
            'view availability',
            'view reports',
        ]);

        $anggota = Role::findByName('Anggota');
        $anggota->givePermissionTo([
            'view schedules',
            'submit availability',
        ]);

        $this->command->info('Permissions assigned to roles successfully!');
    }
}
