<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\User;
use App\Services\ScheduleConfigurationService;
use App\Services\ScheduleEditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleEditConfigTest extends TestCase
{
    use RefreshDatabase;

    protected ScheduleConfigurationService $configService;
    protected ScheduleEditService $editService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configService = app(ScheduleConfigurationService::class);
        $this->editService = app(ScheduleEditService::class);
    }

    /** @test */
    public function it_handles_null_max_users_per_slot_as_unlimited()
    {
        // Set config to null
        $this->configService->set('max_users_per_slot', null, 'integer');

        $schedule = Schedule::factory()->create();
        $date = now()->format('Y-m-d');
        $session = 1;

        // Should return false (not full) when config is null
        $isFull = $this->editService->isSlotFull($schedule, $date, $session);
        
        $this->assertFalse($isFull, 'Slot should not be full when max_users_per_slot is null');
    }

    /** @test */
    public function it_handles_zero_max_users_per_slot_as_unlimited()
    {
        // Set config to 0
        $this->configService->set('max_users_per_slot', 0, 'integer');

        $schedule = Schedule::factory()->create();
        $date = now()->format('Y-m-d');
        $session = 1;

        // Should return false (not full) when config is 0
        $isFull = $this->editService->isSlotFull($schedule, $date, $session);
        
        $this->assertFalse($isFull, 'Slot should not be full when max_users_per_slot is 0');
    }

    /** @test */
    public function it_enforces_limit_when_max_users_per_slot_is_set()
    {
        // Set config to 2
        $this->configService->set('max_users_per_slot', 2, 'integer');

        $schedule = Schedule::factory()->create();
        $user1 = User::factory()->create(['status' => 'active']);
        $user2 = User::factory()->create(['status' => 'active']);
        
        $date = now()->format('Y-m-d');
        $session = 1;

        // Add 2 users
        $this->editService->addUserToSlot($schedule, $date, $session, $user1->id);
        $this->editService->addUserToSlot($schedule, $date, $session, $user2->id);

        // Should return true (full) when limit is reached
        $isFull = $this->editService->isSlotFull($schedule, $date, $session);
        
        $this->assertTrue($isFull, 'Slot should be full when max_users_per_slot limit is reached');
    }

    /** @test */
    public function it_allows_adding_users_when_no_limit_is_set()
    {
        // Set config to null (unlimited)
        $this->configService->set('max_users_per_slot', null, 'integer');

        $schedule = Schedule::factory()->create();
        $users = User::factory()->count(5)->create(['status' => 'active']);
        
        $date = now()->format('Y-m-d');
        $session = 1;

        // Should be able to add all 5 users without error
        foreach ($users as $user) {
            $assignment = $this->editService->addUserToSlot($schedule, $date, $session, $user->id);
            $this->assertNotNull($assignment);
        }

        // Verify all 5 users were added
        $count = $this->editService->getSlotUserCount($schedule, $date, $session);
        $this->assertEquals(5, $count);
    }

    /** @test */
    public function config_service_returns_null_for_empty_string()
    {
        // Simulate database returning empty string
        \DB::table('schedule_configurations')->updateOrInsert(
            ['key' => 'max_users_per_slot'],
            [
                'value' => '',
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Clear cache
        $this->configService->clearAllCache();

        // Should return null for empty string
        $value = $this->configService->get('max_users_per_slot');
        
        $this->assertNull($value, 'Empty string should be cast to null');
    }
}
