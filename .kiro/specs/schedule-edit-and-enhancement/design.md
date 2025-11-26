# Design Document

## Overview

This design document outlines the architecture and implementation approach for two major enhancements to the SIKOPMA scheduling system:

1. **Edit Published Schedule Feature**: Enable administrators to modify published schedules with full audit trail and notification support
2. **Enhanced Auto-Assignment Algorithm**: Improve the automatic scheduling algorithm to ensure fair workload distribution, respect availability, and optimize schedule quality

### Design Goals

- **Flexibility**: Allow schedule modifications without recreating entire schedules
- **Traceability**: Track all changes with comprehensive audit trails
- **Fairness**: Distribute workload equitably across all active members
- **Performance**: Maintain fast response times even with complex scheduling logic
- **User Experience**: Provide intuitive interfaces for schedule management
- **Data Integrity**: Prevent conflicts and maintain schedule consistency

### System Context

The scheduling system operates on a weekly cycle with:
- 4 working days (Monday - Thursday)
- 3 sessions per day (Sesi 1: 07:30-10:20, Sesi 2: 10:20-12:50, Sesi 3: 13:30-16:00)
- 12 total slots per week
- **Each slot can have 0 or more users assigned** (flexible staffing)
- Multiple active members with varying availability

### Key Concept Change

**Previous Model**: One-to-one relationship (1 slot = 1 user)
**New Model**: One-to-many relationship (1 slot = 0+ users)

This allows for:
- Multiple users working together in the same session
- Empty slots when no one is available
- Flexible staffing based on workload needs
- Better coverage during busy periods

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Presentation Layer                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ EditSchedule │  │InteractiveGrid│  │QuickEditModal│      │
│  │  Component   │  │   Component   │  │  Component   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────────┐
│                      Service Layer                           │
│  ┌──────────────────┐  ┌──────────────────────────────┐    │
│  │ScheduleEditService│  │EnhancedAutoAssignmentService│    │
│  └──────────────────┘  └──────────────────────────────┘    │
│  ┌──────────────────┐  ┌──────────────────────────────┐    │
│  │ConflictDetection │  │WorkloadBalancingService      │    │
│  │    Service       │  │                              │    │
│  └──────────────────┘  └──────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────────┐
│                       Data Layer                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Schedule   │  │  Assignment  │  │ EditHistory  │      │
│  │    Model     │  │    Model     │  │    Model     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│  ┌──────────────┐  ┌──────────────┐                        │
│  │ Availability │  │    User      │                        │
│  │    Model     │  │    Model     │                        │
│  └──────────────┘  └──────────────┘                        │
└─────────────────────────────────────────────────────────────┘
```

### Component Interaction Flow

**Edit Schedule Flow:**
```
User Action → EditSchedule Component → ScheduleEditService → 
Database Transaction → Audit Trail → Notification → Cache Invalidation
```

**Auto-Assignment Flow:**
```
Generate Request → EnhancedAutoAssignmentService → 
Load Availability → Calculate Scores → Assign Users → 
Balance Workload → Validate → Save Assignments
```

## Components and Interfaces

### 1. EditSchedule Livewire Component

**Purpose**: Main interface for editing published schedules with flexible multi-user slots

**Properties:**
```php
public Schedule $schedule;
public Collection $assignments; // Grouped by slot
public Collection $originalAssignments;
public array $changes = [];
public string $editMode = 'single'; // single, bulk, multi
public ?array $selectedSlot = null;
public bool $showUserSelector = false;
public bool $showMultiUserModal = false;
public array $conflicts = [];
public Collection $affectedUsers;
public bool $hasUnsavedChanges = false;
public int $maxUsersPerSlot = 5; // Configurable limit
public bool $allowEmptySlots = true;
```

**Key Methods:**
```php
// Initialization
public function mount(Schedule $schedule): void

// Multi-User Assignment Operations
public function addUserToSlot(string $date, int $session, int $userId): void
public function removeUserFromSlot(int $assignmentId, string $reason): void
public function updateUserInSlot(int $assignmentId, int $newUserId, ?string $reason = null): void
public function clearSlot(string $date, int $session, string $reason): void
public function bulkAddUsers(string $date, int $session, array $userIds): void

// Slot Management
public function getSlotAssignments(string $date, int $session): Collection
public function getSlotUserCount(string $date, int $session): int
public function isSlotFull(string $date, int $session): bool
public function isSlotEmpty(string $date, int $session): bool

// Validation (Updated for multi-user)
public function detectConflicts(): array
public function validateUserForSlot(int $userId, string $date, int $session): array
public function checkUserDoubleBooking(int $userId, string $date, int $session): bool

// Persistence
public function saveChanges(): void
public function discardChanges(): void

// Notifications
public function notifyAffectedUsers(): void

// Helpers
public function getAvailableUsers(string $date, int $session): Collection
public function getSlotStatistics(): array
public function trackChange(string $action, array $data): void
```

### 2. SlotManagementModal Livewire Component

**Purpose**: Modal for managing multiple users in a single slot

**Properties:**
```php
public ?string $slotDate = null;
public ?int $slotSession = null;
public Collection $currentUsers;
public Collection $availableUsers;
public array $selectedUserIds = [];
public bool $show = false;
public string $reason = '';
public int $maxUsersPerSlot = 5;
public bool $showUserSearch = false;
public string $searchTerm = '';
```

**Key Methods:**
```php
public function open(string $date, int $session): void
public function close(): void
public function addUser(int $userId): void
public function removeUser(int $assignmentId): void
public function saveSlot(): void
public function clearSlot(): void
public function searchUsers(): Collection
public function loadCurrentUsers(): void
public function loadAvailableUsers(): void
public function validateSlot(): array
```

### 3. InteractiveGrid Livewire Component

**Purpose**: Visual grid interface for multi-user slot management

**Properties:**
```php
public Schedule $schedule;
public array $grid = []; // [date][session] = Collection of assignments
public ?int $draggedUserId = null;
public ?int $draggedAssignmentId = null;
public array $highlightedSlots = [];
public bool $showConflicts = true;
public bool $showEmptySlots = true;
public string $viewMode = 'compact'; // compact, detailed
```

**Key Methods:**
```php
public function mount(Schedule $schedule): void
public function loadGrid(): void
public function handleUserDrop(int $userId, string $date, int $session): void
public function handleAssignmentMove(int $assignmentId, string $date, int $session): void
public function highlightValidSlots(int $userId): void
public function getSlotStatus(string $date, int $session): string
public function getSlotUserCount(string $date, int $session): int
public function toggleSlotView(string $date, int $session): void
```

### 4. ScheduleEditService

**Purpose**: Business logic for multi-user slot editing operations

**Methods:**
```php
// Multi-User Slot Operations
public function addUserToSlot(
    Schedule $schedule,
    string $date,
    int $session,
    int $userId,
    ?string $reason = null
): ScheduleAssignment

public function removeUserFromSlot(
    ScheduleAssignment $assignment,
    string $reason
): bool

public function updateUserInSlot(
    ScheduleAssignment $assignment,
    int $newUserId,
    ?string $reason = null
): ScheduleAssignment

public function clearSlot(
    Schedule $schedule,
    string $date,
    int $session,
    string $reason
): bool

public function bulkAddUsersToSlot(
    Schedule $schedule,
    string $date,
    int $session,
    array $userIds
): Collection

// Slot Queries
public function getSlotAssignments(
    Schedule $schedule,
    string $date,
    int $session
): Collection

public function getSlotUserCount(
    Schedule $schedule,
    string $date,
    int $session
): int

// Validation (Updated for multi-user)
public function validateUserForSlot(
    Schedule $schedule,
    int $userId,
    string $date,
    int $session,
    ?int $excludeAssignmentId = null
): array

public function checkUserDoubleBooking(
    int $userId,
    string $date,
    int $session,
    ?int $excludeAssignmentId = null
): bool

public function isSlotFull(
    Schedule $schedule,
    string $date,
    int $session
): bool

// Audit Trail
public function recordChange(
    Schedule $schedule,
    string $action,
    array $data,
    ?string $reason = null
): AssignmentEditHistory

// Notifications
public function notifyUser(
    User $user,
    string $notificationType,
    array $data,
    ?string $reason = null
): void

public function notifySlotUsers(
    Schedule $schedule,
    string $date,
    int $session,
    string $notificationType,
    ?string $reason = null
): void
```

### 5. EnhancedAutoAssignmentService

**Purpose**: Advanced algorithm for optimal multi-user schedule generation

**Methods:**
```php
// Main Generation
public function generateOptimalSchedule(
    Schedule $schedule,
    array $options = []
): array

// Options include:
// - min_users_per_slot: minimum users per slot (default: 0)
// - max_users_per_slot: maximum users per slot (default: 5)
// - target_users_per_slot: target users per slot (default: 1)
// - allow_empty_slots: allow slots with no users (default: true)
// - prioritize_coverage: prioritize filling all slots vs balanced workload

public function calculateSlotScores(
    Collection $users,
    array $grid,
    array $currentAssignments = []
): array

public function calculateUserScore(
    User $user,
    string $date,
    int $session,
    array $currentAssignments,
    int $currentSlotUserCount
): int

public function assignUsersToSlots(
    array $scores,
    array $grid,
    array $options
): array

public function balanceWorkload(
    array $assignments,
    Collection $users
): array

public function optimizeSlotCoverage(
    array $assignments,
    array $options
): array

public function validateAndFix(array $assignments): array

public function detectConflicts(array $assignments): array

public function resolveConflict(array $conflict, array $assignments): array

// Helper methods
private function isUserAvailable(User $user, string $date, int $session): bool
private function countUserAssignments(User $user, array $assignments): int
private function hasConsecutiveShift(User $user, string $date, int $session, array $assignments): bool
private function getUserAssignedDays(User $user, array $assignments): array
private function getSlotUserCount(string $date, int $session, array $assignments): int
private function sortSlotsByPriority(array $scores, array $options): array
private function shouldFillSlot(string $date, int $session, array $assignments, array $options): bool
```

### 6. ConflictDetectionService

**Purpose**: Detect and categorize scheduling conflicts in multi-user slots

**Methods:**
```php
public function detectAllConflicts(Schedule $schedule): array

public function detectDoubleAssignments(Schedule $schedule): array
// User assigned to same slot multiple times (should not happen)

public function detectInactiveUsers(Schedule $schedule): array

public function detectAvailabilityMismatches(Schedule $schedule): array

public function detectConsecutiveShifts(Schedule $schedule): array

public function detectOverstaffedSlots(Schedule $schedule, int $maxUsers): array
// Slots with more users than allowed

public function detectDuplicateUsersInSlot(Schedule $schedule): array
// Same user appears multiple times in one slot

public function categorizeConflicts(array $conflicts): array

public function getConflictSeverity(string $conflictType): string
```

### 7. WorkloadBalancingService

**Purpose**: Ensure fair distribution of assignments

**Methods:**
```php
public function calculateWorkloadDistribution(array $assignments): array

public function calculateFairnessScore(array $assignments, Collection $users): float

public function redistributeAssignments(
    array $assignments,
    Collection $overloadedUsers,
    Collection $underloadedUsers
): array

public function identifyImbalances(
    array $assignments,
    Collection $users,
    int $maxDeviation = 2
): array
```

## Multi-User Slot Design

### Conceptual Model

**Previous Approach (One-to-One):**
```
Slot (Senin, Sesi 1) → User A
Slot (Senin, Sesi 2) → User B
Slot (Senin, Sesi 3) → User C
```

**New Approach (One-to-Many):**
```
Slot (Senin, Sesi 1) → [User A, User B, User C]  // Multiple users
Slot (Senin, Sesi 2) → [User D]                  // Single user
Slot (Senin, Sesi 3) → []                        // Empty slot (allowed)
```

### Database Schema Impact

**No schema changes required!** The existing `schedule_assignments` table already supports this:

```sql
-- Multiple rows for same slot = multiple users in that slot
INSERT INTO schedule_assignments (schedule_id, date, session, user_id, ...)
VALUES 
  (1, '2025-11-24', 1, 101, ...),  -- User 101 in Senin Sesi 1
  (1, '2025-11-24', 1, 102, ...),  -- User 102 in Senin Sesi 1
  (1, '2025-11-24', 1, 103, ...);  -- User 103 in Senin Sesi 1
```

**Key Insight**: The relationship is already one-to-many at the database level. We just need to update the application logic to handle multiple assignments per slot.

### Query Patterns

**Get all users in a slot:**
```php
$users = ScheduleAssignment::where('schedule_id', $scheduleId)
    ->where('date', $date)
    ->where('session', $session)
    ->with('user')
    ->get();
```

**Count users in a slot:**
```php
$count = ScheduleAssignment::where('schedule_id', $scheduleId)
    ->where('date', $date)
    ->where('session', $session)
    ->count();
```

**Get slot grid with user counts:**
```php
$grid = ScheduleAssignment::where('schedule_id', $scheduleId)
    ->select('date', 'session', DB::raw('COUNT(*) as user_count'))
    ->groupBy('date', 'session')
    ->get()
    ->mapWithKeys(function($item) {
        return ["{$item->date}_{$item->session}" => $item->user_count];
    });
```

### Business Rules

1. **No Maximum Limit by Default**: Slots can have unlimited users (configurable)
2. **Empty Slots Allowed**: Slots with 0 users are valid
3. **No Duplicate Users**: Same user cannot appear twice in the same slot
4. **User Availability**: Users should be available for the slot (warning if not)
5. **Active Users Only**: Only active users can be assigned (enforced)

### Configuration Options

```php
// config/schedule.php
'multi_user_slots' => [
    'enabled' => true,
    'max_users_per_slot' => null, // null = unlimited, or set a number
    'min_users_per_slot' => 0,
    'target_users_per_slot' => 1, // For auto-assignment
    'allow_empty_slots' => true,
    'warn_on_empty_slots' => true,
    'warn_on_overstaffed_slots' => true,
    'overstaffed_threshold' => 3, // Warn if more than 3 users
],
```

## Data Models

### AssignmentEditHistory Model

**Purpose**: Track all changes to schedule assignments

**Schema:**
```php
Schema::create('assignment_edit_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assignment_id')->constrained('schedule_assignments')->onDelete('cascade');
    $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
    $table->foreignId('edited_by')->constrained('users');
    $table->string('action'); // created, updated, deleted, swapped
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->text('reason')->nullable();
    $table->timestamps();
    
    $table->index(['assignment_id', 'created_at']);
    $table->index(['schedule_id', 'created_at']);
    $table->index('edited_by');
});
```

**Relationships:**
```php
public function assignment(): BelongsTo
public function schedule(): BelongsTo
public function editor(): BelongsTo
```

**Methods:**
```php
public function getChangeSummary(): string
public function getAffectedFields(): array
public function scopeForSchedule($query, int $scheduleId)
public function scopeByEditor($query, int $userId)
public function scopeRecent($query, int $days = 30)
```

### Enhanced ScheduleAssignment Model

**Additional Fields:**
```php
$table->foreignId('edited_by')->nullable()->constrained('users');
$table->timestamp('edited_at')->nullable();
$table->text('edit_reason')->nullable();
$table->json('previous_values')->nullable();
```

**New Methods:**
```php
public function editHistory(): HasMany
public function editor(): BelongsTo
public function hasBeenEdited(): bool
public function getEditCount(): int
public function getLastEdit(): ?AssignmentEditHistory

// Multi-user slot helpers
public function getSlotmates(): Collection
{
    return self::where('schedule_id', $this->schedule_id)
        ->where('date', $this->date)
        ->where('session', $this->session)
        ->where('id', '!=', $this->id)
        ->with('user')
        ->get();
}

public function getSlotUserCount(): int
{
    return self::where('schedule_id', $this->schedule_id)
        ->where('date', $this->date)
        ->where('session', $this->session)
        ->count();
}

public function isOnlyUserInSlot(): bool
{
    return $this->getSlotUserCount() === 1;
}

public function hasSlotmates(): bool
{
    return $this->getSlotUserCount() > 1;
}
```

**New Scopes:**
```php
public function scopeForSlot($query, string $date, int $session)
{
    return $query->where('date', $date)->where('session', $session);
}

public function scopeEmptySlots($query, Schedule $schedule)
{
    // Get all possible slots
    $allSlots = [];
    $startDate = Carbon::parse($schedule->week_start_date);
    for ($day = 0; $day < 4; $day++) {
        $date = $startDate->copy()->addDays($day);
        for ($session = 1; $session <= 3; $session++) {
            $allSlots[] = ['date' => $date->format('Y-m-d'), 'session' => $session];
        }
    }
    
    // Get filled slots
    $filledSlots = $query->where('schedule_id', $schedule->id)
        ->select('date', 'session')
        ->distinct()
        ->get()
        ->map(fn($a) => ['date' => $a->date->format('Y-m-d'), 'session' => $a->session])
        ->toArray();
    
    // Return empty slots
    return array_diff_key($allSlots, $filledSlots);
}
```

### Schedule Configuration Model

**Purpose**: Store configurable scheduling parameters

**Schema:**
```php
Schema::create('schedule_configurations', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->string('value');
    $table->string('type'); // integer, float, boolean, json
    $table->text('description')->nullable();
    $table->timestamps();
});
```

**Default Configuration (Updated for Multi-User Slots):**
```php
[
    // User workload limits
    'max_assignments_per_user' => 4,
    'min_assignments_per_user' => 1,
    
    // Multi-user slot settings
    'max_users_per_slot' => null, // null = unlimited
    'min_users_per_slot' => 0,
    'target_users_per_slot' => 1,
    'allow_empty_slots' => true,
    'warn_on_empty_slots' => true,
    'overstaffed_threshold' => 3,
    
    // Coverage settings (now based on slots with at least 1 user)
    'min_coverage_rate' => 50, // % of slots that must have at least 1 user
    
    // Shift constraints
    'max_consecutive_shifts' => 2,
    
    // Scoring weights
    'availability_match_score' => 100,
    'workload_penalty_score' => 10,
    'consecutive_penalty_score' => 20,
    'day_variety_bonus_score' => 10,
    'preference_bonus_score' => 50,
    'slot_coverage_bonus' => 30, // Bonus for filling empty slots
    
    // Performance
    'enable_caching' => true,
    'cache_ttl' => 3600,
    'max_algorithm_iterations' => 1000,
    'enable_backtracking' => true,
]
```

## Error Handling

### Validation Errors

**Edit Operations:**
```php
// User not active
throw ValidationException::withMessages([
    'user_id' => 'User tidak aktif dan tidak dapat dijadwalkan.'
]);

// Double assignment conflict
throw ValidationException::withMessages([
    'user_id' => 'User sudah memiliki assignment pada waktu yang sama.'
]);

// Coverage too low
throw ValidationException::withMessages([
    'coverage' => 'Coverage rate tidak boleh di bawah 50% untuk jadwal published.'
]);

// Schedule not editable
throw new ScheduleNotEditableException(
    'Jadwal dengan status archived tidak dapat diubah.'
);
```

**Auto-Assignment Errors:**
```php
// Insufficient available users
throw new InsufficientUsersException(
    'Tidak cukup user tersedia untuk mengisi semua slot.'
);

// Algorithm timeout
throw new AlgorithmTimeoutException(
    'Algoritma penjadwalan melebihi batas waktu maksimal.'
);

// Unresolvable conflicts
throw new UnresolvableConflictException(
    'Terdapat konflik yang tidak dapat diselesaikan secara otomatis.'
);
```

### Error Recovery

**Transaction Rollback:**
```php
DB::beginTransaction();
try {
    // Perform operations
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    Log::error('Schedule edit failed', [
        'error' => $e->getMessage(),
        'schedule_id' => $schedule->id,
    ]);
    throw $e;
}
```

**Cache Invalidation on Error:**
```php
try {
    // Perform operations
} catch (\Exception $e) {
    Cache::tags(['schedules', "schedule_{$scheduleId}"])->flush();
    throw $e;
}
```

## Testing Strategy

### Unit Tests

**ScheduleEditService Tests:**
```php
test('can update assignment with valid user')
test('cannot update assignment with inactive user')
test('cannot create double assignment')
test('records change in audit trail')
test('sends notification to affected users')
test('invalidates cache after edit')
```

**EnhancedAutoAssignmentService Tests:**
```php
test('generates schedule with fair distribution')
test('respects user availability')
test('avoids consecutive shifts when possible')
test('spreads assignments across days')
test('completes within time limit')
test('handles insufficient users gracefully')
```

**ConflictDetectionService Tests:**
```php
test('detects double assignments')
test('detects inactive users')
test('detects availability mismatches')
test('categorizes conflicts correctly')
```

### Feature Tests

**Edit Schedule Feature:**
```php
test('admin can edit published schedule')
test('admin can swap assignments')
test('admin can add assignment to empty slot')
test('admin can remove assignment')
test('changes are tracked in audit trail')
test('affected users receive notifications')
test('non-admin cannot edit schedule')
```

**Auto-Assignment Feature:**
```php
test('generates complete schedule')
test('workload is balanced')
test('no critical conflicts in generated schedule')
test('respects configuration parameters')
test('caches results appropriately')
```

### Integration Tests

```php
test('edit and publish workflow')
test('generate, edit, and republish workflow')
test('concurrent edit handling')
test('notification delivery')
test('cache consistency')
```

### Performance Tests

```php
test('schedule generation completes within 5 seconds')
test('edit operation completes within 200ms')
test('grid loading completes within 500ms')
test('handles 50 concurrent users')
```

## Security Considerations

### Authorization

**Policy Rules:**
```php
// SchedulePolicy
public function edit(User $user, Schedule $schedule): bool
{
    return $user->hasRole(['Super Admin', 'Admin']);
}

public function viewHistory(User $user, Schedule $schedule): bool
{
    return $user->hasRole(['Super Admin', 'Admin', 'Pengurus']);
}

public function forceEdit(User $user, Schedule $schedule): bool
{
    return $user->hasRole('Super Admin');
}
```

### Input Validation

**Request Validation:**
```php
public function rules(): array
{
    return [
        'user_id' => 'required|exists:users,id',
        'date' => 'required|date|after_or_equal:today',
        'session' => 'required|in:1,2,3',
        'reason' => 'nullable|string|max:500',
    ];
}
```

### SQL Injection Prevention

- Use Eloquent ORM for all database operations
- Parameterized queries for raw SQL
- Validate and sanitize all user inputs

### XSS Prevention

- Escape all output in Blade templates
- Use `{{ }}` instead of `{!! !!}` for user-generated content
- Sanitize JSON data before storage

## Performance Optimization

### Database Optimization

**Indexes:**
```php
// schedule_assignments table
$table->index(['user_id', 'date', 'session']);
$table->index(['schedule_id', 'status']);
$table->index('date');

// assignment_edit_history table
$table->index(['assignment_id', 'created_at']);
$table->index(['schedule_id', 'created_at']);
$table->index('edited_by');

// availabilities table
$table->index(['user_id', 'schedule_id']);
$table->index('status');
```

**Query Optimization:**
```php
// Eager loading
$schedule = Schedule::with([
    'assignments.user:id,name,photo,status',
    'assignments.editHistory.editor:id,name',
])->find($scheduleId);

// Selective columns
$users = User::select('id', 'name', 'status')
    ->where('status', 'active')
    ->get();

// Chunking for large datasets
ScheduleAssignment::where('schedule_id', $scheduleId)
    ->chunk(100, function ($assignments) {
        // Process assignments
    });
```

### Caching Strategy

**Cache Keys:**
```php
"schedule_grid_{$scheduleId}"
"user_availability_{$userId}_{$weekStart}"
"assignment_scores_{$scheduleId}"
"schedule_conflicts_{$scheduleId}"
"schedule_statistics_{$scheduleId}"
```

**Cache Implementation:**
```php
// Cache with tags
Cache::tags(['schedules', "schedule_{$scheduleId}"])
    ->remember("schedule_grid_{$scheduleId}", 3600, function() use ($scheduleId) {
        return $this->loadScheduleGrid($scheduleId);
    });

// Invalidate on update
Cache::tags(['schedules', "schedule_{$scheduleId}"])->flush();

// Cache user availability
Cache::remember("user_availability_{$userId}_{$weekStart}", 3600, function() {
    return $this->loadUserAvailability($userId, $weekStart);
});
```

### Frontend Optimization

**Lazy Loading:**
```php
// Load assignment details on demand
public function loadAssignmentDetails(int $assignmentId): void
{
    $this->selectedAssignment = ScheduleAssignment::with([
        'user',
        'editHistory.editor',
    ])->find($assignmentId);
}
```

**Debouncing:**
```javascript
// Debounce search input
<input 
    wire:model.debounce.500ms="search"
    type="text"
    placeholder="Cari user..."
>
```

**Pagination:**
```php
// Paginate edit history
public function getEditHistory()
{
    return AssignmentEditHistory::where('schedule_id', $this->schedule->id)
        ->with('editor:id,name')
        ->latest()
        ->paginate(20);
}
```

## Deployment Considerations

### Migration Strategy

**Phase 1: Database Changes**
```bash
php artisan migrate --path=database/migrations/xxxx_create_assignment_edit_history_table.php
php artisan migrate --path=database/migrations/xxxx_add_edit_fields_to_assignments_table.php
php artisan migrate --path=database/migrations/xxxx_create_schedule_configurations_table.php
```

**Phase 2: Seed Configuration**
```bash
php artisan db:seed --class=ScheduleConfigurationSeeder
```

**Phase 3: Deploy Code**
- Deploy service classes
- Deploy Livewire components
- Deploy views and assets

**Phase 4: Cache Warming**
```bash
php artisan schedule:warm-cache
```

### Rollback Plan

**Database Rollback:**
```bash
php artisan migrate:rollback --step=3
```

**Code Rollback:**
- Revert to previous Git commit
- Clear application cache
- Restart queue workers

### Monitoring

**Key Metrics:**
- Schedule generation time
- Edit operation response time
- Cache hit rate
- Conflict detection accuracy
- User notification delivery rate
- Database query performance

**Logging:**
```php
Log::info('Schedule edited', [
    'schedule_id' => $schedule->id,
    'editor_id' => auth()->id(),
    'changes_count' => count($changes),
    'duration_ms' => $duration,
]);
```

**Alerts:**
- Schedule generation exceeds 5 seconds
- Edit operation fails
- Critical conflicts detected
- Cache invalidation failures
- Notification delivery failures

## UI/UX Design

### Edit Schedule Interface

**Layout Structure (Multi-User Slots):**
```
┌─────────────────────────────────────────────────────────────┐
│ Header: Schedule Week (Date Range) | Status Badge           │
├─────────────────────────────────────────────────────────────┤
│ Toolbar: [Save Changes] [Discard] [View History] [Export]  │
├─────────────────────────────────────────────────────────────┤
│ Conflict Panel (if conflicts exist)                         │
│ ⚠️ 2 Critical Conflicts | ⚠️ 3 Warnings | ℹ️ 2 Empty Slots  │
├─────────────────────────────────────────────────────────────┤
│                    Schedule Grid                             │
│  ┌──────────┬──────────┬──────────┬──────────┐             │
│  │  Senin   │  Selasa  │  Rabu    │  Kamis   │             │
│  ├──────────┼──────────┼──────────┼──────────┤             │
│  │ Sesi 1   │ Sesi 1   │ Sesi 1   │ Sesi 1   │             │
│  │ 07:30-   │ 07:30-   │ 07:30-   │ 07:30-   │             │
│  │ 10:20    │ 10:20    │ 10:20    │ 10:20    │             │
│  │ 👥 3     │ 👥 2     │ [Empty]  │ 👥 1     │             │
│  │ User A   │ User D   │ ➕ Add   │ User G   │             │
│  │ User B   │ User E   │          │          │             │
│  │ User C   │          │          │          │             │
│  │ [+Add]   │ [+Add]   │          │ [+Add]   │             │
│  ├──────────┼──────────┼──────────┼──────────┤             │
│  │ Sesi 2   │ Sesi 2   │ Sesi 2   │ Sesi 2   │             │
│  │ 10:20-   │ 10:20-   │ 10:20-   │ 10:20-   │             │
│  │ 12:50    │ 12:50    │ 12:50    │ 12:50    │             │
│  │ 👥 1     │ 👥 4     │ 👥 1     │ [Empty]  │             │
│  │ User F   │ User A   │ User H   │ ➕ Add   │             │
│  │ [+Add]   │ User B   │ [+Add]   │          │             │
│  │          │ User C   │          │          │             │
│  │          │ User D   │          │          │             │
│  │          │ [+Add]   │          │          │             │
│  ├──────────┼──────────┼──────────┼──────────┤             │
│  │ Sesi 3   │ Sesi 3   │ Sesi 3   │ Sesi 3   │             │
│  │ 13:30-   │ 13:30-   │ 13:30-   │ 13:30-   │             │
│  │ 16:00    │ 16:00    │ 16:00    │ 16:00    │             │
│  │ 👥 2     │ 👥 1     │ 👥 2     │ 👥 1     │             │
│  │ User I   │ User J   │ User K   │ User M   │             │
│  │ User L   │ [+Add]   │ User N   │ [+Add]   │             │
│  │ [+Add]   │          │ [+Add]   │          │             │
│  └──────────┴──────────┴──────────┴──────────┘             │
├─────────────────────────────────────────────────────────────┤
│ Statistics Panel                                             │
│ Filled Slots: 10/12 (83%) | Total Assignments: 18          │
│ Empty Slots: 2 | Avg Users/Slot: 1.8                       │
└─────────────────────────────────────────────────────────────┘
```

**Slot Card States (Multi-User):**
```
┌─────────────────────────────┐
│ Senin, Sesi 1               │  Multi-User Slot (Normal)
│ 07:30 - 10:20               │
│ ─────────────────────────── │
│ 👥 3 Users                  │
│ • User A [×]                │
│ • User B [×]                │
│ • User C [×]                │
│ [+ Add User]                │
└─────────────────────────────┘

┌─────────────────────────────┐
│ Senin, Sesi 2               │  Single User Slot
│ 10:20 - 12:50               │
│ ─────────────────────────── │
│ 👤 1 User                   │
│ • User D [×]                │
│ [+ Add User]                │
└─────────────────────────────┘

┌─────────────────────────────┐
│ Rabu, Sesi 1                │  Empty Slot (Allowed)
│ 07:30 - 10:20               │
│ ─────────────────────────── │
│ 📭 No users assigned        │
│ [+ Add User]                │
└─────────────────────────────┘

┌─────────────────────────────┐
│ Selasa, Sesi 2              │  Overstaffed Warning
│ 10:20 - 12:50               │  (Yellow Border)
│ ─────────────────────────── │
│ ⚠️ 5 Users (High)           │
│ • User A [×]                │
│ • User B [×]                │
│ • User C [×]                │
│ • User D [×]                │
│ • User E [×]                │
│ [+ Add User] [Clear All]    │
└─────────────────────────────┘

┌─────────────────────────────┐
│ Kamis, Sesi 1               │  Conflict State
│ 07:30 - 10:20               │  (Red Border)
│ ─────────────────────────── │
│ ❌ 2 Users                  │
│ • User A [×] ⚠️ Duplicate  │
│ • User A [×] ⚠️ Duplicate  │
│ [Fix] [Clear All]           │
└─────────────────────────────┘

┌─────────────────────────────┐
│ Rabu, Sesi 3                │  Edited State
│ 13:30 - 16:00               │  (Blue Border)
│ ─────────────────────────── │
│ 👥 2 Users                  │
│ • User F [×]                │
│ • User G [×] ✏️ Added       │
│ ✏️ Edited by Admin          │
│ [+ Add User]                │
└─────────────────────────────┘
```

### Slot Management Modal (Multi-User)

**Modal Layout:**
```
┌─────────────────────────────────────────────────────┐
│ Manage Slot - Senin, 23 Nov 2025 - Sesi 1     [X] │
│ ⏰ 07:30 - 10:20                                    │
├─────────────────────────────────────────────────────┤
│                                                     │
│ Current Users (3):                                  │
│ ┌─────────────────────────────────────────────┐   │
│ │ 👤 User A                              [×]  │   │
│ │ ✓ Available | 2 other assignments          │   │
│ ├─────────────────────────────────────────────┤   │
│ │ 👤 User B                              [×]  │   │
│ │ ⚠️ Not available | 3 other assignments     │   │
│ ├─────────────────────────────────────────────┤   │
│ │ 👤 User C                              [×]  │   │
│ │ ✓ Available | 1 other assignment           │   │
│ └─────────────────────────────────────────────┘   │
│                                                     │
│ ─────────────────────────────────────────────────  │
│                                                     │
│ Add Users:                                          │
│ [Search users...                          ] [🔍]   │
│                                                     │
│ Available Users (5):                                │
│ ┌─────────────────────────────────────────────┐   │
│ │ ☐ User D - Available, 2 assignments        │   │
│ │ ☐ User E - Available, 1 assignment         │   │
│ │ ☐ User F - Not available, 3 assignments    │   │
│ │ ☐ User G - Available, 0 assignments        │   │
│ │ ☐ User H - Available, 2 assignments        │   │
│ └─────────────────────────────────────────────┘   │
│                                                     │
│ [Select All Available] [Clear Selection]           │
│                                                     │
│ Reason (Optional):                                  │
│ [Text Area]                                         │
│                                                     │
│ ℹ️ Tip: You can add multiple users to this slot   │
│                                                     │
│    [Clear All Users] [Cancel] [Save Changes]       │
└─────────────────────────────────────────────────────┘
```

### Conflict Indicator

**Visual Design:**
```
Critical Conflicts (Red):
┌─────────────────────────────────────────────┐
│ ❌ Double Assignment                        │
│ User A has 2 assignments at the same time   │
│ • Senin, Sesi 1 (07:30-10:20)              │
│ • Senin, Sesi 1 (07:30-10:20)              │
│ [Fix Now]                                   │
└─────────────────────────────────────────────┘

Warnings (Yellow):
┌─────────────────────────────────────────────┐
│ ⚠️ Availability Mismatch                    │
│ User B is assigned but marked unavailable   │
│ • Selasa, Sesi 2 (10:20-12:50)             │
│ [Review]                                    │
└─────────────────────────────────────────────┘

Info (Blue):
┌─────────────────────────────────────────────┐
│ ℹ️ Consecutive Shifts                       │
│ User C has back-to-back sessions            │
│ • Rabu, Sesi 1 & Sesi 2                    │
│ [OK]                                        │
└─────────────────────────────────────────────┘
```

### Drag and Drop Interaction

**States:**
```
1. Idle State:
   - Assignment cards are draggable
   - Cursor shows grab icon on hover

2. Dragging State:
   - Dragged card becomes semi-transparent
   - Valid drop zones highlighted in green
   - Invalid drop zones highlighted in red
   - Cursor shows grabbing icon

3. Drop State:
   - Card animates to new position
   - Validation runs immediately
   - Conflict indicator appears if needed
   - Undo button appears

4. Invalid Drop:
   - Card returns to original position
   - Error message displays briefly
   - Red flash animation on invalid slot
```

### Edit History View

**Timeline Layout:**
```
┌─────────────────────────────────────────────┐
│ Edit History - Schedule Week 18-21 Nov     │
├─────────────────────────────────────────────┤
│                                             │
│ 🕐 23 Nov 2025, 14:30                      │
│ 👤 Admin User                               │
│ ✏️ Updated Assignment                       │
│ Changed User A → User B                     │
│ Slot: Senin, Sesi 1                        │
│ Reason: User A requested time off          │
│                                             │
│ ─────────────────────────────────────────  │
│                                             │
│ 🕐 23 Nov 2025, 10:15                      │
│ 👤 Admin User                               │
│ ➕ Added Assignment                         │
│ Added User C to Rabu, Sesi 1               │
│ Reason: Filling empty slot                 │
│                                             │
│ ─────────────────────────────────────────  │
│                                             │
│ 🕐 22 Nov 2025, 16:45                      │
│ 👤 Super Admin                              │
│ 🔄 Swapped Assignments                      │
│ User D ↔ User E                            │
│ Slots: Kamis Sesi 2 ↔ Kamis Sesi 3        │
│ Reason: Personal request                   │
│                                             │
│         [Load More]                         │
└─────────────────────────────────────────────┘
```

## Algorithm Details

### Enhanced Auto-Assignment Algorithm

**Pseudocode (Multi-User Slots):**
```
function generateOptimalSchedule(schedule, options):
    // Options:
    // - target_users_per_slot: target number of users per slot (default: 1)
    // - max_users_per_slot: maximum users per slot (default: null/unlimited)
    // - allow_empty_slots: allow slots with 0 users (default: true)
    // - prioritize_coverage: fill all slots vs balance workload
    
    // Step 1: Initialize
    users = getActiveUsersWithAvailability(schedule.week_start_date)
    slots = generateSlotGrid(schedule) // 4 days × 3 sessions = 12 slots
    assignments = [] // Can have multiple assignments per slot
    
    // Step 2: Calculate scores for all user-slot combinations
    scores = {}
    for each slot in slots:
        scores[slot] = []
        for each user in users:
            score = calculateUserScore(user, slot, assignments, 0) // 0 = current slot user count
            if score > 0:
                scores[slot].append({user: user, score: score})
        
        // Sort users by score (descending)
        scores[slot].sortByDesc('score')
    
    // Step 3: Determine slot priority
    if options.prioritize_coverage:
        // Fill each slot with at least 1 user first
        sortedSlots = slots.sortBy(slot => scores[slot].count())
    else:
        // Balance workload from the start
        sortedSlots = slots
    
    // Step 4: Assign users to slots
    for each slot in sortedSlots:
        currentSlotUserCount = countUsersInSlot(slot, assignments)
        targetUsers = options.target_users_per_slot
        maxUsers = options.max_users_per_slot ?? Infinity
        
        // Determine how many users to add to this slot
        usersToAdd = min(targetUsers - currentSlotUserCount, maxUsers - currentSlotUserCount)
        
        if usersToAdd <= 0:
            continue // Slot already has enough users
        
        // Get available users for this slot
        availableUsers = scores[slot].filter(u => 
            countAssignments(u.user, assignments) < MAX_ASSIGNMENTS_PER_USER &&
            !isUserInSlot(u.user, slot, assignments) // Prevent duplicates
        )
        
        if availableUsers.isEmpty():
            if options.allow_empty_slots:
                log("No available users for slot: " + slot)
                continue
            else:
                log("Warning: Cannot fill slot: " + slot)
        
        // Add users to slot (up to target or max)
        for i = 0 to min(usersToAdd, availableUsers.count()) - 1:
            selectedUser = availableUsers[i]
            
            assignments.append({
                user_id: selectedUser.user.id,
                date: slot.date,
                session: slot.session,
                day: slot.day,
                score: selectedUser.score
            })
            
            // Recalculate scores for remaining users in this slot
            currentSlotUserCount++
            for each remainingUser in availableUsers.slice(i+1):
                remainingUser.score = calculateUserScore(
                    remainingUser.user, 
                    slot, 
                    assignments,
                    currentSlotUserCount
                )
            
            // Re-sort by updated scores
            availableUsers.sortByDesc('score')
    
    // Step 5: Balance workload
    assignments = balanceWorkload(assignments, users, options)
    
    // Step 6: Validate and fix conflicts
    assignments = validateAndFix(assignments)
    
    return assignments

function calculateUserScore(user, slot, currentAssignments, currentSlotUserCount):
    score = 0
    
    // Factor 1: Availability (most important)
    if not isUserAvailable(user, slot.date, slot.session):
        return -1000 // Exclude unavailable users
    score += AVAILABILITY_MATCH_SCORE // +100
    
    // Factor 2: Current workload (balance)
    assignmentCount = countUserAssignments(user, currentAssignments)
    score -= (assignmentCount * WORKLOAD_PENALTY) // -10 per assignment
    
    // Factor 3: Consecutive shifts (avoid burnout)
    if hasConsecutiveShift(user, slot, currentAssignments):
        score -= CONSECUTIVE_PENALTY // -20
    
    // Factor 4: Day variety (spread across days)
    userDays = getUserAssignedDays(user, currentAssignments)
    if slot.day not in userDays:
        score += DAY_VARIETY_BONUS // +10
    
    // Factor 5: Preference (if implemented)
    if hasPreference(user, slot):
        score += PREFERENCE_BONUS // +50
    
    // Factor 6: Slot coverage (NEW - prioritize empty slots)
    if currentSlotUserCount == 0:
        score += SLOT_COVERAGE_BONUS // +30 for filling empty slot
    
    // Factor 7: Already in slot (prevent duplicates)
    if isUserInSlot(user, slot, currentAssignments):
        return -2000 // Exclude users already in this slot
    
    return score

function balanceWorkload(assignments, users):
    userCounts = countAssignmentsPerUser(assignments)
    avgAssignments = assignments.count() / users.count()
    maxDeviation = 2
    
    // Identify imbalances
    overloaded = userCounts.filter(count > avgAssignments + maxDeviation)
    underloaded = userCounts.filter(count < avgAssignments - maxDeviation)
    
    // Redistribute
    for each overloadedUser in overloaded:
        excessAssignments = userCounts[overloadedUser] - (avgAssignments + maxDeviation)
        
        for i = 0 to excessAssignments:
            // Find assignment to move
            assignment = findMovableAssignment(overloadedUser, assignments)
            
            // Find underloaded user who can take it
            newUser = findSuitableUser(assignment, underloaded, assignments)
            
            if newUser:
                assignment.user_id = newUser.id
                userCounts[overloadedUser]--
                userCounts[newUser]++
    
    return assignments

function validateAndFix(assignments):
    conflicts = detectConflicts(assignments)
    
    for each conflict in conflicts:
        if conflict.type == 'double_assignment':
            // Keep first assignment, remove duplicates
            duplicates = conflict.assignments.slice(1)
            for each duplicate in duplicates:
                assignments.remove(duplicate)
                
                // Try to reassign user to another slot
                alternativeSlot = findAlternativeSlot(duplicate.user_id, assignments)
                if alternativeSlot:
                    assignments.append(alternativeSlot)
        
        else if conflict.type == 'inactive_user':
            // Remove assignment with inactive user
            assignments.remove(conflict.assignment)
            
            // Try to fill with active user
            replacement = findReplacementUser(conflict.assignment, assignments)
            if replacement:
                assignments.append(replacement)
    
    return assignments
```

**Scoring Example (Multi-User Slots):**
```
Scenario: Filling Senin Sesi 1 (currently empty, target 2 users)

First User Selection:
User A - Senin Sesi 1:
+ 100 (Available)
-  20 (Has 2 existing assignments)
-   0 (No consecutive shift)
+  10 (New day for user)
+   0 (No preference)
+  30 (Filling empty slot)
= 120 points

User B - Senin Sesi 1:
+ 100 (Available)
-  10 (Has 1 existing assignment)
-  20 (Has Senin Sesi 2 - consecutive)
+   0 (Already has Senin assignment)
+   0 (No preference)
+  30 (Filling empty slot)
= 100 points

User C - Senin Sesi 1:
-1000 (Not available)
= -1000 points (excluded)

Result: User A selected (highest score: 120)

Second User Selection (slot now has 1 user):
User B - Senin Sesi 1:
+ 100 (Available)
-  10 (Has 1 existing assignment)
-  20 (Has Senin Sesi 2 - consecutive)
+   0 (Already has Senin assignment)
+   0 (No preference)
+   0 (Slot not empty anymore)
= 70 points

User D - Senin Sesi 1:
+ 100 (Available)
-  30 (Has 3 existing assignments)
-   0 (No consecutive shift)
+  10 (New day for user)
+   0 (No preference)
+   0 (Slot not empty anymore)
= 80 points

Result: User D selected (highest score: 80)

Final: Senin Sesi 1 has 2 users (User A, User D)
```

### Conflict Resolution Strategy

**Priority Order:**
1. **Critical Conflicts** (Must fix before save)
   - Double assignments
   - Inactive users
   - Invalid data

2. **Warnings** (Can save with acknowledgment)
   - Availability mismatches
   - Excessive consecutive shifts
   - Workload imbalance

3. **Info** (Informational only)
   - Preference mismatches
   - Suboptimal distribution

**Resolution Approaches (Multi-User Slots):**
```
Duplicate User in Same Slot:
1. Identify duplicate assignments (same user, same slot)
2. Keep the first assignment (by creation time)
3. Remove duplicates
4. This should never happen with proper validation

Inactive User:
1. Remove assignment
2. Find active replacement with similar availability
3. If no replacement, leave slot with fewer users (allowed)

Availability Mismatch:
1. Display warning to admin
2. Allow override with reason
3. Log the override decision

Consecutive Shifts:
1. Calculate penalty score
2. If possible, move user to non-consecutive slot
3. If unavoidable, allow with warning

Overstaffed Slot:
1. If slot has more users than max_users_per_slot
2. Identify users with highest workload
3. Move them to understaffed slots
4. If no alternative, display warning

Empty Slot:
1. If allow_empty_slots = true: No action needed
2. If allow_empty_slots = false: Display error
3. Suggest available users for the slot
```

## Statistics and Metrics (Multi-User Slots)

### Schedule Statistics

**Updated Metrics:**
```php
[
    // Slot-based metrics
    'total_slots' => 12,
    'filled_slots' => 10, // Slots with at least 1 user
    'empty_slots' => 2,
    'coverage_rate' => 83.33, // (filled_slots / total_slots) * 100
    
    // Assignment-based metrics
    'total_assignments' => 18, // Total user assignments
    'unique_users' => 12, // Number of different users
    'avg_users_per_slot' => 1.5, // total_assignments / total_slots
    'avg_users_per_filled_slot' => 1.8, // total_assignments / filled_slots
    
    // Workload distribution
    'min_assignments_per_user' => 0,
    'max_assignments_per_user' => 3,
    'avg_assignments_per_user' => 1.5,
    'workload_std_deviation' => 0.8,
    'fairness_score' => 0.85, // 1.0 = perfectly balanced
    
    // Slot distribution
    'slots_with_1_user' => 6,
    'slots_with_2_users' => 3,
    'slots_with_3_users' => 1,
    'slots_with_4_plus_users' => 0,
    
    // Availability compliance
    'assignments_with_availability' => 16,
    'assignments_without_availability' => 2,
    'availability_compliance_rate' => 88.89,
]
```

### Visualization Data

**Slot Heatmap:**
```php
// For each slot, show user count
[
    'monday' => [
        'session_1' => 3,  // 3 users
        'session_2' => 1,  // 1 user
        'session_3' => 2,  // 2 users
    ],
    'tuesday' => [
        'session_1' => 2,
        'session_2' => 4,
        'session_3' => 1,
    ],
    // ...
]
```

**Workload Distribution Chart:**
```php
// User workload histogram
[
    '0_assignments' => 3,  // 3 users with 0 assignments
    '1_assignment' => 5,   // 5 users with 1 assignment
    '2_assignments' => 4,  // 4 users with 2 assignments
    '3_assignments' => 2,  // 2 users with 3 assignments
    '4_assignments' => 1,  // 1 user with 4 assignments
]
```

## Configuration Management

### Configuration Interface

**Admin Settings Page (Updated for Multi-User Slots):**
```
┌─────────────────────────────────────────────┐
│ Schedule Configuration                      │
├─────────────────────────────────────────────┤
│                                             │
│ Assignment Limits                           │
│ Max assignments per user: [4] ▼            │
│ Min assignments per user: [1] ▼            │
│                                             │
│ Coverage Requirements                       │
│ Minimum coverage rate: [80]%               │
│                                             │
│ Scoring Weights                             │
│ Availability match:    [100] points        │
│ Workload penalty:      [10] points         │
│ Consecutive penalty:   [20] points         │
│ Day variety bonus:     [10] points         │
│ Preference bonus:      [50] points         │
│                                             │
│ Algorithm Settings                          │
│ ☑ Enable caching                           │
│ Cache TTL: [3600] seconds                  │
│ Max iterations: [1000]                     │
│ ☑ Enable backtracking                      │
│                                             │
│ Session Times                               │
│ Sesi 1: [07:30] - [10:20]                 │
│ Sesi 2: [10:20] - [12:50]                 │
│ Sesi 3: [13:30] - [16:00]                 │
│                                             │
│         [Reset to Defaults] [Save]          │
└─────────────────────────────────────────────┘
```

### Configuration Service

```php
class ScheduleConfigurationService
{
    public function get(string $key, $default = null)
    {
        return Cache::remember("config_{$key}", 3600, function() use ($key, $default) {
            $config = ScheduleConfiguration::where('key', $key)->first();
            return $config ? $this->castValue($config->value, $config->type) : $default;
        });
    }
    
    public function set(string $key, $value, string $type = 'string'): void
    {
        ScheduleConfiguration::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
        
        Cache::forget("config_{$key}");
    }
    
    public function getAll(): array
    {
        return Cache::remember('all_schedule_configs', 3600, function() {
            return ScheduleConfiguration::all()
                ->mapWithKeys(function($config) {
                    return [$config->key => $this->castValue($config->value, $config->type)];
                })
                ->toArray();
        });
    }
    
    private function castValue($value, string $type)
    {
        return match($type) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
```

## Notification Templates

### Assignment Added Notification

**Email Template:**
```
Subject: Jadwal Shift Baru - [Date Range]

Halo [User Name],

Anda telah ditambahkan ke jadwal shift:

📅 Tanggal: [Day], [Date]
⏰ Waktu: Sesi [Session] ([Time Start] - [Time End])
📍 Lokasi: Koperasi Mahasiswa

[If edited]
✏️ Perubahan ini dilakukan oleh [Editor Name]
💬 Alasan: [Reason]

Silakan konfirmasi kehadiran Anda melalui sistem.

Terima kasih,
Tim SIKOPMA
```

**In-App Notification:**
```
🔔 Jadwal Shift Baru
Anda dijadwalkan untuk [Day], [Date] - Sesi [Session]
[Time Start] - [Time End]
[View Schedule]
```

### Assignment Removed Notification

**Email Template:**
```
Subject: Perubahan Jadwal Shift - [Date Range]

Halo [User Name],

Jadwal shift Anda telah dibatalkan:

📅 Tanggal: [Day], [Date]
⏰ Waktu: Sesi [Session] ([Time Start] - [Time End])

✏️ Dibatalkan oleh: [Editor Name]
💬 Alasan: [Reason]

Jika ada pertanyaan, silakan hubungi admin.

Terima kasih,
Tim SIKOPMA
```

### Assignment Swapped Notification

**Email Template:**
```
Subject: Perubahan Jadwal Shift - [Date Range]

Halo [User Name],

Jadwal shift Anda telah diubah:

❌ Jadwal Lama:
📅 [Old Day], [Old Date] - Sesi [Old Session]
⏰ [Old Time Start] - [Old Time End]

✅ Jadwal Baru:
📅 [New Day], [New Date] - Sesi [New Session]
⏰ [New Time Start] - [New Time End]

✏️ Diubah oleh: [Editor Name]
💬 Alasan: [Reason]

Silakan konfirmasi kehadiran Anda untuk jadwal baru.

Terima kasih,
Tim SIKOPMA
```

## API Endpoints (for future mobile app)

### Schedule Endpoints

```
GET    /api/schedules
GET    /api/schedules/{id}
POST   /api/schedules
PUT    /api/schedules/{id}
DELETE /api/schedules/{id}
POST   /api/schedules/{id}/publish
GET    /api/schedules/{id}/assignments
GET    /api/schedules/{id}/conflicts
GET    /api/schedules/{id}/statistics
GET    /api/schedules/{id}/history
```

### Assignment Endpoints

```
GET    /api/assignments
GET    /api/assignments/{id}
POST   /api/assignments
PUT    /api/assignments/{id}
DELETE /api/assignments/{id}
POST   /api/assignments/{id}/swap
GET    /api/assignments/my-schedule
```

### Auto-Assignment Endpoints

```
POST   /api/schedules/{id}/auto-assign
GET    /api/schedules/{id}/preview-assignment
POST   /api/schedules/{id}/apply-assignment
```

## Glossary

- **Assignment**: Penugasan anggota ke slot waktu tertentu
- **Audit Trail**: Catatan riwayat perubahan
- **Auto-Assignment**: Penjadwalan otomatis menggunakan algoritma
- **Availability**: Ketersediaan anggota untuk dijadwalkan
- **Conflict**: Kondisi tidak valid dalam jadwal (double booking, dll)
- **Consecutive Shift**: Shift berturut-turut pada sesi berdekatan
- **Coverage Rate**: Persentase slot terisi
- **Fairness Score**: Metrik keadilan distribusi workload
- **Greedy Algorithm**: Algoritma yang memilih opsi terbaik di setiap langkah
- **Slot**: Unit waktu dalam jadwal (hari + sesi)
- **Workload**: Jumlah assignment per anggota
- **Session**: Periode waktu dalam satu hari (Sesi 1/2/3)

## References

### Laravel Documentation
- [Livewire v3](https://livewire.laravel.com/docs)
- [Laravel Events](https://laravel.com/docs/events)
- [Laravel Notifications](https://laravel.com/docs/notifications)
- [Laravel Caching](https://laravel.com/docs/cache)
- [Laravel Testing](https://laravel.com/docs/testing)

### Algorithm Resources
- Constraint Satisfaction Problems
- Greedy Algorithms
- Backtracking Algorithms
- Scoring and Ranking Systems

### UI/UX Resources
- Drag and Drop Interactions
- Real-time Validation Patterns
- Conflict Visualization
- Audit Trail Design Patterns


## Summary of Multi-User Slot Changes

### Key Design Changes

**1. Data Model**
- No database schema changes required
- Existing `schedule_assignments` table already supports multiple rows per slot
- Application logic updated to handle one-to-many relationship

**2. Business Logic**
- Slots can have 0, 1, or multiple users (configurable)
- Empty slots are allowed by default
- No duplicate users in the same slot
- Configurable maximum users per slot

**3. User Interface**
- Slot cards show user count and list of users
- "Add User" button on each slot
- Individual remove buttons for each user in a slot
- "Clear All" button to empty a slot
- Visual indicators for empty, normal, and overstaffed slots

**4. Auto-Assignment Algorithm**
- Updated scoring to consider current slot user count
- New "slot coverage bonus" for filling empty slots
- Target users per slot configuration
- Two modes: prioritize coverage vs balance workload
- Prevents duplicate users in same slot

**5. Statistics**
- Slot-based metrics (filled vs empty)
- Assignment-based metrics (total assignments)
- Average users per slot
- Slot distribution (1 user, 2 users, 3+ users)
- Workload distribution across users

**6. Validation**
- Check for duplicate users in same slot
- Check for overstaffed slots (warning)
- Check for empty slots (info/warning based on config)
- User availability validation (warning)
- Active user validation (error)

**7. Configuration**
- `max_users_per_slot`: null (unlimited) or number
- `target_users_per_slot`: default 1
- `allow_empty_slots`: default true
- `overstaffed_threshold`: default 3
- `slot_coverage_bonus`: default 30 points

### Migration Path

**Phase 1: Update Application Logic**
- Update services to handle multiple users per slot
- Update Livewire components for multi-user UI
- Update validation rules

**Phase 2: Update UI**
- Redesign slot cards for multiple users
- Add slot management modal
- Update statistics display

**Phase 3: Update Algorithm**
- Implement multi-user scoring
- Add slot coverage bonus
- Update workload balancing

**Phase 4: Testing**
- Test with various slot configurations
- Test empty slots
- Test overstaffed slots
- Test workload distribution

### Backward Compatibility

The system remains backward compatible:
- Existing schedules with 1 user per slot work as before
- Can gradually adopt multi-user slots
- Configuration allows restricting to 1 user per slot if needed
- No data migration required

### Benefits

1. **Flexibility**: Handle varying workload needs
2. **Realism**: Reflects actual staffing patterns
3. **Efficiency**: Better resource utilization
4. **Fairness**: More options for workload distribution
5. **Simplicity**: No complex schema changes needed
