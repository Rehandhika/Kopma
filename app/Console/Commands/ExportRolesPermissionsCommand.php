<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ExportRolesPermissionsCommand extends Command
{
    protected $signature = 'export:roles-permissions 
                            {--path= : Path untuk menyimpan file CSV (default: database/Data)}';

    protected $description = 'Export roles, permissions, dan users dari database ke CSV';

    private string $exportPath;

    public function handle(): int
    {
        $this->exportPath = $this->option('path') ?? database_path('Data/export');

        // Buat folder jika belum ada
        if (!is_dir($this->exportPath)) {
            mkdir($this->exportPath, 0755, true);
        }

        $this->info('📤 Memulai export data...');
        $this->newLine();

        try {
            $this->exportPermissions();
            $this->exportRoles();
            $this->exportRolePermissions();
            $this->exportUsers();

            $this->newLine();
            $this->info('✅ Export selesai!');
            $this->info("📁 File disimpan di: {$this->exportPath}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function exportPermissions(): void
    {
        $this->info('📋 Exporting permissions...');
        
        $permissions = Permission::all();
        $file = fopen("{$this->exportPath}/permissions.csv", 'w');
        
        // Header
        fputcsv($file, ['id', 'name', 'guard_name', 'group', 'description']);
        
        foreach ($permissions as $permission) {
            fputcsv($file, [
                $permission->id,
                $permission->name,
                $permission->guard_name,
                '', // group
                '', // description
            ]);
        }
        
        fclose($file);
        $this->info("   ✓ {$permissions->count()} permissions exported");
    }

    private function exportRoles(): void
    {
        $this->info('👥 Exporting roles...');
        
        $roles = Role::all();
        $file = fopen("{$this->exportPath}/roles.csv", 'w');
        
        // Header
        fputcsv($file, ['id', 'name', 'guard_name', 'description']);
        
        foreach ($roles as $role) {
            fputcsv($file, [
                $role->id,
                $role->name,
                $role->guard_name,
                '',
            ]);
        }
        
        fclose($file);
        $this->info("   ✓ {$roles->count()} roles exported");
    }

    private function exportRolePermissions(): void
    {
        $this->info('🔗 Exporting role-permission mappings...');
        
        $file = fopen("{$this->exportPath}/role_permissions.csv", 'w');
        
        // Header
        fputcsv($file, ['role_name', 'permission_name']);
        
        $count = 0;
        $roles = Role::with('permissions')->get();
        
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                fputcsv($file, [$role->name, $permission->name]);
                $count++;
            }
        }
        
        fclose($file);
        $this->info("   ✓ {$count} role-permission mappings exported");
    }

    private function exportUsers(): void
    {
        $this->info('👤 Exporting users...');
        
        $users = User::with('roles')->get();
        $file = fopen("{$this->exportPath}/users_complete.csv", 'w');
        
        // Header
        fputcsv($file, ['name', 'nim', 'email', 'jabatan', 'role', 'phone', 'address', 'status']);
        
        foreach ($users as $user) {
            fputcsv($file, [
                $user->name,
                $user->nim,
                $user->email,
                '', // jabatan - tidak disimpan di DB
                $user->getRoleNames()->first() ?? '',
                $user->phone ?? '',
                $user->address ?? '',
                $user->status,
            ]);
        }
        
        fclose($file);
        $this->info("   ✓ {$users->count()} users exported");
    }
}
