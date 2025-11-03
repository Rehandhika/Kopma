<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view.users',
            'create.users',
            'edit.users',
            'delete.users',

            // Role management
            'view.roles',
            'create.roles',
            'edit.roles',
            'delete.roles',

            // Attendance
            'view.attendance.all',
            'view.attendance.own',
            'checkin.attendance',

            // Schedule management
            'view.schedule.all',
            'view.schedule.own',
            'create.schedule',
            'edit.schedule',
            'delete.schedule',
            'generate.schedule',
            'publish.schedule',
            'input.availability',

            // Swap requests
            'view.swap.all',
            'view.swap.own',
            'create.swap.request',
            'approve.swap.target',
            'approve.swap.admin',

            // Leave requests
            'view.leave.all',
            'view.leave.own',
            'create.leave.request',
            'approve.leave.request',

            // Penalties
            'view.penalty.all',
            'view.penalty.own',
            'create.penalty',
            'edit.penalty',
            'delete.penalty',
            'appeal.penalty',
            'manage.penalty',

            // Sales/Cashier
            'view.sales.all',
            'view.sales.own',
            'create.sales',
            'edit.sales',
            'delete.sales',

            // Products
            'view.products',
            'create.products',
            'edit.products',
            'delete.products',

            // Reports
            'view.reports',

            // System settings
            'manage.settings',

            // Audit logs
            'view.audit.logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $ketuaRole = Role::firstOrCreate(['name' => 'Ketua']);
        $ketuaRole->givePermissionTo([
            'view.users', 'create.users', 'edit.users',
            'view.attendance.all',
            'view.schedule.all', 'create.schedule', 'edit.schedule', 'generate.schedule', 'publish.schedule',
            'view.swap.all', 'approve.swap.admin',
            'view.leave.all', 'approve.leave.request',
            'view.penalty.all', 'create.penalty', 'manage.penalty',
            'view.sales.all',
            'view.products', 'create.products', 'edit.products',
            'view.reports',
            'manage.settings',
            'view.audit.logs',
        ]);

        $wakilKetuaRole = Role::firstOrCreate(['name' => 'Wakil Ketua']);
        $wakilKetuaRole->givePermissionTo([
            'view.users', 'edit.users',
            'view.attendance.all',
            'view.schedule.all', 'edit.schedule',
            'view.swap.all', 'approve.swap.admin',
            'view.leave.all', 'approve.leave.request',
            'view.penalty.all', 'create.penalty',
            'view.sales.all',
            'view.products', 'edit.products',
            'view.reports',
        ]);

        $bphRole = Role::firstOrCreate(['name' => 'BPH']);
        $bphRole->givePermissionTo([
            'view.attendance.all',
            'view.schedule.all', 'edit.schedule',
            'view.swap.all', 'approve.swap.admin',
            'view.leave.all', 'approve.leave.request',
            'view.penalty.all',
            'view.sales.all',
            'view.products',
            'view.reports',
        ]);

        $anggotaRole = Role::firstOrCreate(['name' => 'Anggota']);
        $anggotaRole->givePermissionTo([
            'view.attendance.own',
            'checkin.attendance',
            'view.schedule.own',
            'input.availability',
            'view.swap.own',
            'create.swap.request',
            'approve.swap.target',
            'view.leave.own',
            'create.leave.request',
            'view.penalty.own',
            'appeal.penalty',
            'view.sales.own',
            'create.sales',
        ]);
    }
}
