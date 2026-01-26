<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ImportUsersCommand extends Command
{
    protected $signature = 'import:users 
                            {--fresh : Hapus semua users yang ada sebelum import (kecuali Super Admin)}
                            {--dry-run : Simulasi tanpa menyimpan ke database}
                            {--password= : Password default untuk semua user (default: password)}
                            {--send-credentials : Kirim email kredensial ke user baru}';

    protected $description = 'Import users dari CSV ke database dengan role assignment';

    private string $dataPath;
    private bool $dryRun = false;
    private string $defaultPassword;

    public function handle(): int
    {
        $this->dataPath = database_path('Data');
        $this->dryRun = $this->option('dry-run');
        $this->defaultPassword = $this->option('password') ?? 'password';

        if ($this->dryRun) {
            $this->warn('🔍 Mode DRY-RUN: Tidak ada perubahan yang akan disimpan');
            $this->newLine();
        }

        // Validasi file CSV
        if (!$this->validateFile()) {
            return Command::FAILURE;
        }

        // Validasi roles sudah ada
        if (!$this->validateRoles()) {
            return Command::FAILURE;
        }

        $this->info('🚀 Memulai import users...');
        $this->newLine();

        try {
            if (!$this->dryRun) {
                DB::beginTransaction();
            }

            // Fresh mode
            if ($this->option('fresh') && !$this->dryRun) {
                $this->freshUsers();
            }

            // Import users
            $result = $this->importUsers();

            if (!$this->dryRun) {
                DB::commit();
            }

            $this->newLine();
            $this->displaySummary($result);

            // Kirim kredensial jika diminta
            if ($this->option('send-credentials') && !$this->dryRun && $result['created'] > 0) {
                $this->sendCredentials($result['new_users']);
            }

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

    private function validateFile(): bool
    {
        $file = "{$this->dataPath}/users_complete.csv";
        
        if (!file_exists($file)) {
            $this->error("❌ File tidak ditemukan: users_complete.csv");
            return false;
        }

        $this->info('✓ File users_complete.csv ditemukan');
        return true;
    }

    private function validateRoles(): bool
    {
        $csv = $this->readCsv("{$this->dataPath}/users_complete.csv");
        $requiredRoles = array_unique(array_column($csv, 'role'));
        $missingRoles = [];

        foreach ($requiredRoles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                $missingRoles[] = $roleName;
            }
        }

        if (!empty($missingRoles)) {
            $this->error('❌ Role berikut belum ada di database:');
            foreach ($missingRoles as $role) {
                $this->line("   - {$role}");
            }
            $this->newLine();
            $this->warn('💡 Jalankan terlebih dahulu: php artisan import:roles-permissions');
            return false;
        }

        $this->info('✓ Semua roles yang dibutuhkan sudah ada');
        return true;
    }

    private function freshUsers(): void
    {
        $this->warn('🗑️  Menghapus users yang ada (kecuali Super Admin pertama)...');
        
        // Simpan Super Admin pertama
        $superAdmin = User::role('Super Admin')->first();
        
        // Hapus semua user kecuali super admin
        User::where('id', '!=', $superAdmin?->id ?? 0)->forceDelete();
        
        $this->info('   Users lama berhasil dihapus');
    }

    private function importUsers(): array
    {
        $this->info('👤 Importing users...');
        
        $csv = $this->readCsv("{$this->dataPath}/users_complete.csv");
        $bar = $this->output->createProgressBar(count($csv));
        $bar->start();

        $created = 0;
        $updated = 0;
        $newUsers = [];

        foreach ($csv as $row) {
            if ($this->dryRun) {
                $bar->advance();
                continue;
            }

            // Cek apakah user sudah ada (by NIM)
            $existingUser = User::where('nim', $row['nim'])->first();

            if ($existingUser) {
                // Update user yang sudah ada
                $existingUser->update([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'] ?? null,
                    'address' => $row['address'] ?? null,
                    'status' => $row['status'] ?? 'active',
                ]);
                
                // Sync role
                $existingUser->syncRoles([$row['role']]);
                $updated++;
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $row['name'],
                    'nim' => $row['nim'],
                    'email' => $row['email'],
                    'password' => Hash::make($this->defaultPassword),
                    'phone' => $row['phone'] ?? null,
                    'address' => $row['address'] ?? null,
                    'status' => $row['status'] ?? 'active',
                ]);
                
                // Assign role
                $user->assignRole($row['role']);
                
                $newUsers[] = [
                    'user' => $user,
                    'password' => $this->defaultPassword,
                ];
                $created++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return [
            'total' => count($csv),
            'created' => $created,
            'updated' => $updated,
            'new_users' => $newUsers,
        ];
    }

    private function displaySummary(array $result): void
    {
        $this->info('✅ Import users selesai!');
        $this->newLine();
        
        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Total diproses', $result['total']],
                ['User baru dibuat', $result['created']],
                ['User diupdate', $result['updated']],
            ]
        );

        if ($result['created'] > 0) {
            $this->newLine();
            $this->warn("🔐 Password default: {$this->defaultPassword}");
            $this->warn('   Pastikan user mengganti password setelah login pertama!');
        }

        // Tampilkan daftar user baru
        if (!empty($result['new_users'])) {
            $this->newLine();
            $this->info('📋 Daftar user baru:');
            
            $tableData = [];
            foreach ($result['new_users'] as $item) {
                $user = $item['user'];
                $tableData[] = [
                    $user->name,
                    $user->nim,
                    $user->email,
                    $user->getRoleNames()->first(),
                ];
            }
            
            $this->table(['Nama', 'NIM', 'Email', 'Role'], $tableData);
        }
    }

    private function sendCredentials(array $newUsers): void
    {
        $this->newLine();
        $this->info('📧 Mengirim email kredensial...');
        
        foreach ($newUsers as $item) {
            try {
                // Dispatch job untuk kirim email
                \App\Jobs\SendInitialCredentialsJob::dispatch(
                    $item['user'],
                    $item['password']
                );
                $this->line("   ✓ Email dijadwalkan untuk: {$item['user']->email}");
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Gagal kirim ke {$item['user']->email}: {$e->getMessage()}");
            }
        }
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
