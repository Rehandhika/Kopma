<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ImportRolesPermissionsCommand extends Command
{
    protected $signature = 'import:roles-permissions 
                            {--fresh : Hapus semua roles dan permissions yang ada sebelum import}
                            {--dry-run : Simulasi tanpa menyimpan ke database}';

    protected $description = 'Import roles dan permissions dari CSV ke database';

    private string $dataPath;
    private bool $dryRun = false;

    public function handle(): int
    {
        $this->dataPath = database_path('Data');
        $this->dryRun = $this->option('dry-run');

        if ($this->dryRun) {
            $this->warn('🔍 Mode DRY-RUN: Tidak ada perubahan yang akan disimpan');
            $this->newLine();
        }

        // Validasi file CSV
        if (!$this->validateFiles()) {
            return Command::FAILURE;
        }

        $this->info('🚀 Memulai import roles dan permissions...');
        $this->newLine();

        try {
            if (!$this->dryRun) {
                DB::beginTransaction();
            }

            // Reset cache permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // Fresh mode - hapus data lama
            if ($this->option('fresh') && !$this->dryRun) {
                $this->freshDatabase();
            }

            // Step 1: Import Permissions
            $this->importPermissions();

            // Step 2: Import Roles
            $this->importRoles();

            // Step 3: Import Role-Permission mappings
            $this->importRolePermissions();

            if (!$this->dryRun) {
                DB::commit();
            }

            $this->newLine();
            $this->info('✅ Import roles dan permissions selesai!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            if (!$this->dryRun) {
                DB::rollBack();
            }
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    private function validateFiles(): bool
    {
        $requiredFiles = ['permissions.csv', 'roles.csv', 'role_permissions.csv'];
        $missing = [];

        foreach ($requiredFiles as $file) {
            if (!file_exists("{$this->dataPath}/{$file}")) {
                $missing[] = $file;
            }
        }

        if (!empty($missing)) {
            $this->error('❌ File CSV tidak ditemukan:');
            foreach ($missing as $file) {
                $this->line("   - {$file}");
            }
            return false;
        }

        $this->info('✓ Semua file CSV ditemukan');
        return true;
    }

    private function freshDatabase(): void
    {
        $this->warn('🗑️  Menghapus data roles dan permissions yang ada...');
        
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        Permission::query()->delete();
        Role::query()->delete();
        
        $this->info('   Data lama berhasil dihapus');
    }

    private function importPermissions(): void
    {
        $this->info('📋 Importing permissions...');
        
        $csv = $this->readCsv("{$this->dataPath}/permissions.csv");
        $bar = $this->output->createProgressBar(count($csv));
        $bar->start();

        $created = 0;
        $skipped = 0;

        foreach ($csv as $row) {
            if ($this->dryRun) {
                $this->line("   [DRY-RUN] Permission: {$row['name']}");
            } else {
                $permission = Permission::firstOrCreate(
                    ['name' => $row['name'], 'guard_name' => $row['guard_name'] ?? 'web']
                );
                
                if ($permission->wasRecentlyCreated) {
                    $created++;
                } else {
                    $skipped++;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        
        if (!$this->dryRun) {
            $this->info("   ✓ {$created} permissions dibuat, {$skipped} sudah ada");
        } else {
            $this->info("   ✓ " . count($csv) . " permissions akan diproses");
        }
    }

    private function importRoles(): void
    {
        $this->info('👥 Importing roles...');
        
        $csv = $this->readCsv("{$this->dataPath}/roles.csv");
        $bar = $this->output->createProgressBar(count($csv));
        $bar->start();

        $created = 0;
        $skipped = 0;

        foreach ($csv as $row) {
            if ($this->dryRun) {
                $this->line("   [DRY-RUN] Role: {$row['name']}");
            } else {
                $role = Role::firstOrCreate(
                    ['name' => $row['name'], 'guard_name' => $row['guard_name'] ?? 'web']
                );
                
                if ($role->wasRecentlyCreated) {
                    $created++;
                } else {
                    $skipped++;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        
        if (!$this->dryRun) {
            $this->info("   ✓ {$created} roles dibuat, {$skipped} sudah ada");
        } else {
            $this->info("   ✓ " . count($csv) . " roles akan diproses");
        }
    }

    private function importRolePermissions(): void
    {
        $this->info('🔗 Importing role-permission mappings...');
        
        $csv = $this->readCsv("{$this->dataPath}/role_permissions.csv");
        
        // Group by role
        $rolePermissions = [];
        foreach ($csv as $row) {
            $roleName = $row['role_name'];
            $permissionName = $row['permission_name'];
            
            if (!isset($rolePermissions[$roleName])) {
                $rolePermissions[$roleName] = [];
            }
            $rolePermissions[$roleName][] = $permissionName;
        }

        $bar = $this->output->createProgressBar(count($rolePermissions));
        $bar->start();

        foreach ($rolePermissions as $roleName => $permissions) {
            if ($this->dryRun) {
                $this->line("   [DRY-RUN] {$roleName}: " . count($permissions) . " permissions");
            } else {
                $role = Role::findByName($roleName);
                if ($role) {
                    $role->syncPermissions($permissions);
                } else {
                    $this->warn("   ⚠️  Role '{$roleName}' tidak ditemukan");
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("   ✓ " . count($rolePermissions) . " roles dengan permissions di-mapping");
    }

    private function readCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');
        
        // Read header
        $header = fgetcsv($handle);
        
        // Read data rows
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) === count($header)) {
                $rows[] = array_combine($header, $data);
            }
        }
        
        fclose($handle);
        return $rows;
    }
}
