<?php

namespace App\Console\Commands;

use App\Services\ScheduleConfigurationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FixScheduleConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:fix-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix schedule configuration values and clear cache';

    /**
     * Execute the console command.
     */
    public function handle(ScheduleConfigurationService $configService): int
    {
        $this->info('Fixing schedule configuration...');

        // Get current max_users_per_slot value
        $currentValue = $configService->get('max_users_per_slot');
        $this->info("Current max_users_per_slot: " . ($currentValue === null ? 'null (unlimited)' : $currentValue));

        // If it's 0, set to null (unlimited)
        if ($currentValue === 0) {
            $this->warn('Detected max_users_per_slot = 0, fixing to null (unlimited)...');
            $configService->set('max_users_per_slot', null, 'integer', 'Maximum users allowed per slot (null = unlimited)');
            $this->info('✓ Fixed max_users_per_slot to null (unlimited)');
        }

        // Clear all configuration caches
        $this->info('Clearing configuration cache...');
        $configService->clearAllCache();
        
        // Clear schedule-related caches
        Cache::flush();
        
        $this->info('✓ All caches cleared');

        // Display current configuration
        $this->newLine();
        $this->info('Current Schedule Configuration:');
        $this->table(
            ['Key', 'Value', 'Type'],
            [
                ['max_users_per_slot', $configService->get('max_users_per_slot') ?? 'null (unlimited)', 'integer'],
                ['allow_empty_slots', $configService->get('allow_empty_slots') ? 'true' : 'false', 'boolean'],
                ['overstaffed_threshold', $configService->get('overstaffed_threshold', 3), 'integer'],
            ]
        );

        $this->newLine();
        $this->info('✓ Configuration fixed successfully!');

        return Command::SUCCESS;
    }
}
