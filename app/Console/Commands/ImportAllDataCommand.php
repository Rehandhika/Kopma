<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportAllDataCommand extends Command
{
    protected $signature = 'import:all 
                            {--fresh : Hapus semua data sebelum import}
                            {--dry-run : Simulasi tanpa menyimpan ke database}
                            {--password= : Password default untuk users}
                            {--send-credentials : Kirim email kredensial ke user baru}';

    protected $description = 'Import semua data (roles, permissions, users) dari CSV ke database';

    public function handle(): int
    {
        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║           IMPORT DATA KOPMA - WIRUS ANGKATAN 66              ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $fresh = $this->option('fresh');

        if ($dryRun) {
            $this->warn('🔍 Mode DRY-RUN aktif - tidak ada perubahan yang akan disimpan');
            $this->newLine();
        }

        if ($fresh) {
            if (!$dryRun && !$this->confirm('⚠️  Mode FRESH akan menghapus data yang ada. Lanjutkan?', false)) {
                $this->info('Import dibatalkan.');
                return Command::SUCCESS;
            }
        }

        // Step 1: Import Roles & Permissions
        $this->info('');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('STEP 1: Import Roles & Permissions');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $options = [];
        if ($fresh) $options[] = '--fresh';
        if ($dryRun) $options[] = '--dry-run';
        
        $exitCode = $this->call('import:roles-permissions', 
            array_fill_keys($options, true)
        );
        
        if ($exitCode !== Command::SUCCESS) {
            $this->error('❌ Gagal import roles & permissions');
            return Command::FAILURE;
        }

        // Step 2: Import Users
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('STEP 2: Import Users');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $userOptions = [];
        if ($fresh) $userOptions['--fresh'] = true;
        if ($dryRun) $userOptions['--dry-run'] = true;
        if ($this->option('password')) $userOptions['--password'] = $this->option('password');
        if ($this->option('send-credentials')) $userOptions['--send-credentials'] = true;
        
        $exitCode = $this->call('import:users', $userOptions);
        
        if ($exitCode !== Command::SUCCESS) {
            $this->error('❌ Gagal import users');
            return Command::FAILURE;
        }

        // Summary
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║                    IMPORT SELESAI! ✅                        ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        if (!$dryRun) {
            $this->displayFinalSummary();
        }

        return Command::SUCCESS;
    }

    private function displayFinalSummary(): void
    {
        $rolesCount = \Spatie\Permission\Models\Role::count();
        $permissionsCount = \Spatie\Permission\Models\Permission::count();
        $usersCount = \App\Models\User::count();

        $this->table(
            ['Data', 'Jumlah'],
            [
                ['Roles', $rolesCount],
                ['Permissions', $permissionsCount],
                ['Users', $usersCount],
            ]
        );

        $this->newLine();
        $this->info('📌 Langkah selanjutnya:');
        $this->line('   1. Test login dengan user yang sudah dibuat');
        $this->line('   2. Verifikasi permission setiap role');
        $this->line('   3. Pastikan user mengganti password default');
        $this->newLine();
    }
}
