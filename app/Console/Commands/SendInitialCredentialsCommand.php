<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CredentialService;
use Illuminate\Console\Command;

/**
 * Artisan command untuk mengirim email kredensial awal
 * 
 * Usage:
 * - Single user: php artisan credentials:send --user=1
 * - Multiple users: php artisan credentials:send --users=1,2,3
 * - All users tanpa password: php artisan credentials:send --all-new
 * - Dengan custom password: php artisan credentials:send --user=1 --password=MyPassword123
 */
class SendInitialCredentialsCommand extends Command
{
    protected $signature = 'credentials:send 
                            {--user= : ID user tunggal}
                            {--users= : ID users dipisah koma (1,2,3)}
                            {--all-new : Kirim ke semua user yang belum pernah login}
                            {--password= : Custom password (hanya untuk single user)}
                            {--delay=5 : Delay antar email dalam detik untuk bulk send}
                            {--dry-run : Simulasi tanpa mengirim email}';

    protected $description = 'Kirim email kredensial awal (NIM & password) ke user';

    protected CredentialService $credentialService;

    public function __construct(CredentialService $credentialService)
    {
        parent::__construct();
        $this->credentialService = $credentialService;
    }

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - Tidak ada email yang akan dikirim');
            $this->newLine();
        }

        // Single user
        if ($userId = $this->option('user')) {
            return $this->sendToSingleUser((int) $userId, $isDryRun);
        }

        // Multiple users
        if ($userIds = $this->option('users')) {
            $ids = array_map('intval', explode(',', $userIds));
            return $this->sendToMultipleUsers($ids, $isDryRun);
        }

        // All new users
        if ($this->option('all-new')) {
            return $this->sendToAllNewUsers($isDryRun);
        }

        $this->error('Pilih salah satu opsi: --user, --users, atau --all-new');
        return Command::FAILURE;
    }

    protected function sendToSingleUser(int $userId, bool $isDryRun): int
    {
        $user = User::find($userId);

        if (!$user) {
            $this->error("User dengan ID {$userId} tidak ditemukan");
            return Command::FAILURE;
        }

        $this->info("📧 Mengirim kredensial ke: {$user->name} ({$user->email})");
        $this->table(
            ['Field', 'Value'],
            [
                ['NIM', $user->nim],
                ['Email', $user->email],
                ['Name', $user->name],
            ]
        );

        if ($isDryRun) {
            $this->info('✅ [DRY RUN] Email akan dikirim ke user ini');
            return Command::SUCCESS;
        }

        if (!$this->confirm('Lanjutkan mengirim email?', true)) {
            $this->warn('Dibatalkan');
            return Command::SUCCESS;
        }

        $customPassword = $this->option('password');
        $result = $this->credentialService->sendInitialCredentials(
            $user, 
            $customPassword,
            true
        );

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
            $this->warn("Password: {$result['password']} (simpan jika diperlukan)");
        } else {
            $this->error('❌ ' . $result['message']);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function sendToMultipleUsers(array $userIds, bool $isDryRun): int
    {
        $users = User::whereIn('id', $userIds)->get();

        if ($users->isEmpty()) {
            $this->error('Tidak ada user yang ditemukan');
            return Command::FAILURE;
        }

        $this->info("📧 Akan mengirim kredensial ke {$users->count()} user:");
        $this->newLine();

        $tableData = $users->map(fn($u) => [
            $u->id,
            $u->nim,
            $u->name,
            $u->email ?: '❌ No email',
        ])->toArray();

        $this->table(['ID', 'NIM', 'Name', 'Email'], $tableData);

        if ($isDryRun) {
            $this->info("✅ [DRY RUN] {$users->count()} email akan dikirim");
            return Command::SUCCESS;
        }

        if (!$this->confirm('Lanjutkan mengirim email?', true)) {
            $this->warn('Dibatalkan');
            return Command::SUCCESS;
        }

        $delay = (int) $this->option('delay');
        $this->info("Mengirim dengan delay {$delay} detik antar email...");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $results = $this->credentialService->bulkSendCredentials($userIds, $delay);

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Berhasil: {$results['success']}");
        $this->error("❌ Gagal: {$results['failed']}");

        return $results['failed'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    protected function sendToAllNewUsers(bool $isDryRun): int
    {
        // User yang belum pernah login (email_verified_at null atau created recently)
        $users = User::whereNull('email_verified_at')
            ->whereNotNull('email')
            ->where('status', 'active')
            ->get();

        if ($users->isEmpty()) {
            $this->info('Tidak ada user baru yang perlu dikirim kredensial');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  Akan mengirim kredensial ke {$users->count()} user baru");
        
        return $this->sendToMultipleUsers($users->pluck('id')->toArray(), $isDryRun);
    }
}
