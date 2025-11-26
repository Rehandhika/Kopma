# Prompt AI: Pengembangan Fitur Edit Jadwal & Penyempurnaan Penyusunan Jadwal

## 📋 Konteks Proyek

**SIKOPMA** adalah sistem manajemen koperasi mahasiswa berbasis Laravel 12 + Livewire v3 + Tailwind CSS v4 + Alpine.js. Sistem ini memiliki modul penjadwalan yang sudah berjalan dengan fitur:

### Fitur Jadwal yang Sudah Ada:
1. **Schedule Calendar** - Tampilan kalender jadwal mingguan
2. **Create Schedule** - Pembuatan jadwal baru dengan:
   - Manual assignment (drag & drop user ke slot)
   - Auto-assignment (algoritma otomatis)
   - Template-based (menggunakan template tersimpan)
3. **My Schedule** - Jadwal personal anggota
4. **Availability Manager** - Manajemen ketersediaan anggota
5. **Schedule Generator** - Generator jadwal otomatis

### Struktur Database Jadwal:

```sql
-- schedules: Master jadwal mingguan
- id, week_start_date, week_end_date, status (draft/published/archived)
- generated_by, generated_at, published_at, published_by
- total_slots, filled_slots, coverage_rate, notes

-- schedule_assignments: Assignment individual
- id, schedule_id, user_id, day, session (1/2/3)
- date, time_start, time_end
- status (scheduled/completed/missed/swapped/excused)
- swapped_to_user_id, notes

-- schedule_templates: Template jadwal yang bisa digunakan ulang
- id, name, description, created_by
- pattern (JSON: [{day, session, user_id}])
- is_public, usage_count

-- availabilities: Ketersediaan anggota per minggu
- id, user_id, week_start_date, status

-- availability_details: Detail ketersediaan per hari & sesi
- id, availability_id, day, session, is_available
```

### Pola Jadwal Saat Ini:
- **4 hari kerja**: Senin - Kamis
- **3 sesi per hari**: 
  - Sesi 1: 07:30 - 10:20 (2 jam 50 menit)
  - Sesi 2: 10:20 - 12:50 (2 jam 30 menit)
  - Sesi 3: 13:30 - 16:00 (2 jam 30 menit)
- **Total 12 slot** per minggu (4 hari × 3 sesi)

---

## 🎯 Tujuan Pengembangan

### 1. **Fitur Edit Jadwal yang Sudah Dipublikasi**
Saat ini jadwal yang sudah dipublikasi tidak bisa diedit. Perlu fitur untuk:
- Edit assignment individual tanpa mengubah seluruh jadwal
- Swap/ganti anggota pada slot tertentu
- Tambah/hapus assignment pada slot kosong
- Track perubahan (audit trail)
- Notifikasi otomatis ke anggota yang terdampak

### 2. **Penyempurnaan Algoritma Penyusunan Jadwal**
Algoritma auto-assignment perlu ditingkatkan untuk:
- **Fairness**: Distribusi shift yang lebih adil
- **Availability**: Prioritas pada ketersediaan anggota
- **Workload Balance**: Hindari overload pada anggota tertentu
- **Conflict Detection**: Deteksi dan hindari konflik jadwal
- **Performance**: Optimasi query dan caching

---

## 🔧 Spesifikasi Teknis

### A. Fitur Edit Jadwal (Priority: HIGH)

#### 1. Edit Published Schedule Component
**File**: `app/Livewire/Schedule/EditSchedule.php`

**Fitur Utama**:
```php
class EditSchedule extends Component
{
    public Schedule $schedule;
    public $assignments = [];
    public $originalAssignments = [];
    public $changes = [];
    
    // Edit modes
    public $editMode = 'single'; // single, bulk, swap
    
    // UI state
    public $selectedSlot = null;
    public $showUserSelector = false;
    public $showSwapModal = false;
    
    // Validation
    public $conflicts = [];
    public $affectedUsers = [];
    
    // Methods
    public function mount(Schedule $schedule);
    public function editAssignment($date, $session);
    public function updateAssignment($assignmentId, $newUserId);
    public function removeAssignment($assignmentId);
    public function addAssignment($date, $session, $userId);
    public function swapAssignments($assignment1Id, $assignment2Id);
    public function bulkEdit($assignments);
    public function detectConflicts();
    public function saveChanges();
    public function notifyAffectedUsers();
    public function trackChanges($action, $data);
}
```

**Validasi**:
- User harus aktif
- Tidak ada double assignment (user yang sama di waktu yang sama)
- Cek availability mismatch (warning, bukan error)
- Minimum coverage 50% untuk tetap published
- Track semua perubahan untuk audit

**Notifikasi**:
- Kirim notifikasi ke user yang assignment-nya berubah
- Kirim notifikasi ke user baru yang ditambahkan
- Kirim notifikasi ke user yang assignment-nya dihapus

#### 2. Assignment History Tracking
**File**: `database/migrations/xxxx_add_edit_tracking_to_assignments.php`

```php
Schema::table('schedule_assignments', function (Blueprint $table) {
    $table->foreignId('edited_by')->nullable()->constrained('users');
    $table->timestamp('edited_at')->nullable();
    $table->text('edit_reason')->nullable();
    $table->json('previous_values')->nullable();
});

// Atau buat tabel terpisah untuk history
Schema::create('assignment_edit_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('assignment_id')->constrained('schedule_assignments');
    $table->foreignId('edited_by')->constrained('users');
    $table->string('action'); // updated, deleted, created
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->text('reason')->nullable();
    $table->timestamps();
});
```

#### 3. Quick Edit Modal Component
**File**: `app/Livewire/Schedule/QuickEditModal.php`

Modal ringan untuk edit cepat assignment:
- Ganti user
- Hapus assignment
- Swap dengan assignment lain
- Tambah catatan

---

### B. Penyempurnaan Algoritma Auto-Assignment (Priority: HIGH)

#### 1. Enhanced Auto-Assignment Service
**File**: `app/Services/EnhancedAutoAssignmentService.php`

**Algoritma Baru**:

```php
class EnhancedAutoAssignmentService
{
    /**
     * Generate optimal schedule assignments
     * 
     * Algorithm Steps:
     * 1. Load all active users with availability
     * 2. Calculate user scores for each slot
     * 3. Assign users using weighted scoring
     * 4. Balance workload across users
     * 5. Validate and resolve conflicts
     */
    public function generateOptimalSchedule(Schedule $schedule): array
    {
        // 1. Get available users with their availability
        $users = $this->getAvailableUsers($schedule);
        
        // 2. Initialize assignment grid
        $grid = $this->initializeGrid($schedule);
        
        // 3. Calculate scores for each user-slot combination
        $scores = $this->calculateSlotScores($users, $grid);
        
        // 4. Assign users using greedy algorithm with backtracking
        $assignments = $this->assignUsersOptimally($scores, $grid);
        
        // 5. Balance workload
        $balanced = $this->balanceWorkload($assignments, $users);
        
        // 6. Validate and fix conflicts
        $validated = $this->validateAndFix($balanced);
        
        return $validated;
    }
    
    /**
     * Calculate score for user-slot combination
     * 
     * Scoring Factors:
     * - Availability match: +100 points
     * - Current workload: -10 points per existing assignment
     * - Preference (if implemented): +50 points
     * - Consecutive shifts: -20 points (avoid burnout, especially Sesi 1→2 or 2→3)
     * - Day variety: +10 points (spread across days)
     * 
     * Note: Sesi 1 (07:30-10:20), Sesi 2 (10:20-12:50), Sesi 3 (13:30-16:00)
     */
    private function calculateSlotScore(User $user, $slot, $currentAssignments): int
    {
        $score = 0;
        
        // Availability match
        if ($this->isUserAvailable($user, $slot)) {
            $score += 100;
        } else {
            return -1000; // Not available, skip
        }
        
        // Workload balance
        $userAssignmentCount = $this->countUserAssignments($user, $currentAssignments);
        $score -= ($userAssignmentCount * 10);
        
        // Avoid consecutive shifts
        if ($this->hasConsecutiveShift($user, $slot, $currentAssignments)) {
            $score -= 20;
        }
        
        // Day variety bonus
        $userDays = $this->getUserAssignedDays($user, $currentAssignments);
        if (!in_array($slot['day'], $userDays)) {
            $score += 10;
        }
        
        return $score;
    }
    
    /**
     * Balance workload across users
     * 
     * Ensure no user has significantly more/less assignments than others
     */
    private function balanceWorkload(array $assignments, Collection $users): array
    {
        $userCounts = $this->countAssignmentsPerUser($assignments);
        $avgAssignments = count($assignments) / $users->count();
        $maxDeviation = 2; // Max difference from average
        
        // Identify overloaded and underloaded users
        $overloaded = $userCounts->filter(fn($count) => $count > $avgAssignments + $maxDeviation);
        $underloaded = $userCounts->filter(fn($count) => $count < $avgAssignments - $maxDeviation);
        
        // Redistribute assignments
        foreach ($overloaded as $userId => $count) {
            $this->redistributeAssignments($userId, $underloaded, $assignments);
        }
        
        return $assignments;
    }
    
    /**
     * Validate assignments and fix conflicts
     */
    private function validateAndFix(array $assignments): array
    {
        $conflicts = $this->detectConflicts($assignments);
        
        foreach ($conflicts as $conflict) {
            $assignments = $this->resolveConflict($conflict, $assignments);
        }
        
        return $assignments;
    }
}
```

#### 2. Scoring System Configuration
**File**: `config/schedule.php`

```php
return [
    'auto_assignment' => [
        'scoring' => [
            'availability_match' => 100,
            'workload_penalty' => 10,
            'consecutive_penalty' => 20,
            'day_variety_bonus' => 10,
            'preference_bonus' => 50,
        ],
        'constraints' => [
            'max_assignments_per_user' => 4,
            'min_assignments_per_user' => 1,
            'max_consecutive_shifts' => 2, // Hindari Sesi 1→2 atau 2→3 berturut-turut
            'min_coverage_rate' => 80,
        ],
        'session_times' => [
            1 => ['start' => '07:30', 'end' => '10:20', 'duration' => 170], // 2h 50m
            2 => ['start' => '10:20', 'end' => '12:50', 'duration' => 150], // 2h 30m
            3 => ['start' => '13:30', 'end' => '16:00', 'duration' => 150], // 2h 30m
        ],
        'optimization' => [
            'enable_caching' => true,
            'cache_ttl' => 3600,
            'max_iterations' => 1000,
            'enable_backtracking' => true,
        ],
    ],
];
```

#### 3. Performance Optimization

**Caching Strategy**:
```php
// Cache user availability for the week
$cacheKey = "user_availability_{$userId}_{$weekStart}";
$availability = Cache::remember($cacheKey, 3600, function() use ($userId, $weekStart) {
    return $this->loadUserAvailability($userId, $weekStart);
});

// Cache assignment scores
$cacheKey = "assignment_scores_{$scheduleId}";
$scores = Cache::remember($cacheKey, 1800, function() use ($scheduleId) {
    return $this->calculateAllScores($scheduleId);
});
```

**Query Optimization**:
```php
// Eager load relationships
$users = User::with([
    'availabilities' => fn($q) => $q->where('week_start_date', $weekStart),
    'availabilities.details',
    'scheduleAssignments' => fn($q) => $q->whereBetween('date', [$weekStart, $weekEnd])
])->where('status', 'active')->get();

// Use indexes
Schema::table('schedule_assignments', function (Blueprint $table) {
    $table->index(['user_id', 'date', 'session']);
    $table->index(['schedule_id', 'status']);
});
```

---

### C. UI/UX Improvements

#### 1. Interactive Schedule Grid
**Component**: `app/Livewire/Schedule/InteractiveGrid.php`

**Fitur**:
- Drag & drop untuk swap assignments
- Click untuk quick edit
- Hover untuk info detail
- Color coding untuk status
- Real-time conflict detection
- Undo/redo functionality

**Alpine.js Integration**:
```javascript
<div x-data="scheduleGrid()" x-init="init()">
    <!-- Grid dengan drag & drop -->
    <div class="grid grid-cols-4 gap-4">
        @foreach($days as $day)
            <div class="day-column">
                @foreach($sessions as $session)
                    <div 
                        class="slot"
                        x-on:drop="handleDrop($event, '{{ $day }}', {{ $session }})"
                        x-on:dragover.prevent
                    >
                        <!-- Assignment card -->
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<script>
function scheduleGrid() {
    return {
        draggedItem: null,
        
        handleDrop(event, date, session) {
            // Handle drop logic
            this.$wire.swapAssignments(this.draggedItem, {date, session});
        },
        
        // ... more methods
    }
}
</script>
```

#### 2. Conflict Visualization
**Component**: `resources/views/components/schedule/conflict-indicator.blade.php`

```blade
@props(['conflicts'])

<div class="conflict-panel">
    @if(count($conflicts['critical']) > 0)
        <div class="alert alert-error">
            <h4>Critical Conflicts ({{ count($conflicts['critical']) }})</h4>
            <ul>
                @foreach($conflicts['critical'] as $conflict)
                    <li>{{ $conflict['message'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if(count($conflicts['warning']) > 0)
        <div class="alert alert-warning">
            <h4>Warnings ({{ count($conflicts['warning']) }})</h4>
            <ul>
                @foreach($conflicts['warning'] as $conflict)
                    <li>{{ $conflict['message'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Info: Jam operasional -->
    <div class="text-sm text-gray-600 mt-2">
        <strong>Jam Operasional:</strong> Sesi 1 (07:30-10:20), Sesi 2 (10:20-12:50), Sesi 3 (13:30-16:00)
    </div>
</div>
```

---

## 📊 Best Practices & Optimizations

### 1. Database Optimization

```php
// Use database transactions for consistency
DB::transaction(function() use ($schedule, $assignments) {
    $schedule->update(['status' => 'published']);
    
    foreach ($assignments as $assignment) {
        ScheduleAssignment::create($assignment);
    }
    
    $this->notifyUsers($assignments);
});

// Use bulk inserts for performance
ScheduleAssignment::insert($assignments);

// Use indexes for faster queries
Schema::table('schedule_assignments', function (Blueprint $table) {
    $table->index(['user_id', 'date']);
    $table->index(['schedule_id', 'status']);
    $table->index('date');
});
```

### 2. Caching Strategy

```php
// Cache frequently accessed data
Cache::tags(['schedules', "schedule_{$scheduleId}"])
    ->remember("schedule_grid_{$scheduleId}", 3600, function() use ($scheduleId) {
        return $this->loadScheduleGrid($scheduleId);
    });

// Invalidate cache on updates
Cache::tags(['schedules', "schedule_{$scheduleId}"])->flush();
```

### 3. Event-Driven Architecture

```php
// Events
event(new SchedulePublished($schedule));
event(new AssignmentChanged($assignment, $oldUser, $newUser));
event(new ScheduleEdited($schedule, $changes));

// Listeners
class NotifyAffectedUsers implements ShouldQueue
{
    public function handle(AssignmentChanged $event)
    {
        // Send notifications
        Notification::send($event->oldUser, new AssignmentRemovedNotification());
        Notification::send($event->newUser, new AssignmentAddedNotification());
    }
}
```

### 4. Testing Strategy

```php
// Feature test for edit schedule
test('admin can edit published schedule', function() {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');
    
    $schedule = Schedule::factory()->published()->create();
    $assignment = ScheduleAssignment::factory()->for($schedule)->create();
    
    $newUser = User::factory()->create();
    
    actingAs($admin)
        ->livewire(EditSchedule::class, ['schedule' => $schedule])
        ->call('updateAssignment', $assignment->id, $newUser->id)
        ->assertDispatched('alert', type: 'success');
    
    expect($assignment->fresh()->user_id)->toBe($newUser->id);
});

// Unit test for auto-assignment algorithm
test('auto assignment distributes workload fairly', function() {
    $users = User::factory()->count(5)->create();
    $schedule = Schedule::factory()->create();
    
    $service = app(EnhancedAutoAssignmentService::class);
    $assignments = $service->generateOptimalSchedule($schedule);
    
    $userCounts = collect($assignments)->groupBy('user_id')->map->count();
    $maxCount = $userCounts->max();
    $minCount = $userCounts->min();
    
    // Max difference should be <= 2
    expect($maxCount - $minCount)->toBeLessThanOrEqual(2);
});
```

---

## 🚀 Implementation Roadmap

### Phase 1: Edit Jadwal (Week 1-2)
1. ✅ Buat migration untuk edit tracking
2. ✅ Implement `EditSchedule` component
3. ✅ Implement `QuickEditModal` component
4. ✅ Add audit trail functionality
5. ✅ Implement notification system
6. ✅ Add tests
7. ✅ Update UI/UX

### Phase 2: Enhanced Auto-Assignment (Week 3-4)
1. ✅ Implement `EnhancedAutoAssignmentService`
2. ✅ Add scoring system
3. ✅ Implement workload balancing
4. ✅ Add conflict detection & resolution
5. ✅ Optimize queries & add caching
6. ✅ Add configuration options
7. ✅ Add tests

### Phase 3: UI/UX Polish (Week 5)
1. ✅ Implement interactive grid
2. ✅ Add drag & drop functionality
3. ✅ Improve conflict visualization
4. ✅ Add undo/redo
5. ✅ Mobile responsiveness
6. ✅ Performance testing

### Phase 4: Testing & Deployment (Week 6)
1. ✅ Integration testing
2. ✅ User acceptance testing
3. ✅ Performance optimization
4. ✅ Documentation
5. ✅ Deployment

---

## 💡 Additional Features (Optional)

### 1. Schedule Templates Enhancement
- Save current schedule as template
- Template categories (weekly, monthly, special events)
- Template sharing between users
- Template versioning

### 2. Smart Suggestions
- AI-powered user suggestions based on:
  - Historical performance
  - Attendance rate
  - Preference patterns
  - Workload history

### 3. Schedule Analytics
- Coverage trends
- User workload distribution
- Availability utilization
- Conflict patterns

### 4. Mobile App Integration
- Push notifications for schedule changes
- Quick availability update
- Schedule view on mobile
- Check-in/out integration

---

## 📝 Code Examples

### Example 1: Edit Assignment
```php
public function updateAssignment(int $assignmentId, int $newUserId): void
{
    DB::transaction(function() use ($assignmentId, $newUserId) {
        $assignment = ScheduleAssignment::findOrFail($assignmentId);
        $oldUser = $assignment->user;
        $newUser = User::findOrFail($newUserId);
        
        // Validate
        if ($newUser->status !== 'active') {
            throw new \Exception('User tidak aktif');
        }
        
        // Check conflicts
        if ($this->hasConflict($newUser, $assignment->date, $assignment->session)) {
            throw new \Exception('User sudah memiliki assignment pada waktu yang sama');
        }
        
        // Track changes
        AssignmentEditHistory::create([
            'assignment_id' => $assignment->id,
            'edited_by' => auth()->id(),
            'action' => 'updated',
            'old_values' => ['user_id' => $oldUser->id],
            'new_values' => ['user_id' => $newUser->id],
            'reason' => $this->editReason,
        ]);
        
        // Update assignment
        $assignment->update([
            'user_id' => $newUser->id,
            'edited_by' => auth()->id(),
            'edited_at' => now(),
        ]);
        
        // Notify users
        $oldUser->notify(new AssignmentRemovedNotification($assignment));
        $newUser->notify(new AssignmentAddedNotification($assignment));
        
        // Invalidate cache
        Cache::tags(['schedules', "schedule_{$assignment->schedule_id}"])->flush();
    });
    
    $this->dispatch('alert', type: 'success', message: 'Assignment berhasil diupdate');
    $this->loadData();
}
```

### Example 2: Auto-Assignment with Scoring
```php
public function assignUsersOptimally(array $scores, array $grid): array
{
    $assignments = [];
    $userAssignments = [];
    
    // Sort slots by difficulty (slots with fewer available users first)
    $sortedSlots = $this->sortSlotsByDifficulty($scores);
    
    foreach ($sortedSlots as $slot) {
        // Get available users for this slot, sorted by score
        $availableUsers = $scores[$slot['key']]
            ->sortByDesc('score')
            ->filter(fn($user) => ($userAssignments[$user['id']] ?? 0) < 4);
        
        if ($availableUsers->isEmpty()) {
            continue; // Skip if no available users
        }
        
        // Assign user with highest score
        $selectedUser = $availableUsers->first();
        
        $assignments[] = [
            'user_id' => $selectedUser['id'],
            'date' => $slot['date'],
            'session' => $slot['session'],
            'day' => $slot['day'],
            'score' => $selectedUser['score'],
        ];
        
        // Track user assignment count
        $userAssignments[$selectedUser['id']] = ($userAssignments[$selectedUser['id']] ?? 0) + 1;
    }
    
    return $assignments;
}
```

---

## 🎓 Learning Resources

### Laravel Best Practices
- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [Laravel Performance Tips](https://laravel.com/docs/optimization)

### Algorithm Design
- Greedy Algorithms
- Constraint Satisfaction Problems
- Backtracking Algorithms
- Scoring & Ranking Systems

### Testing
- [Pest PHP Documentation](https://pestphp.com)
- [Laravel Testing](https://laravel.com/docs/testing)
- Feature Testing Best Practices

---

## ✅ Success Criteria

### Functional Requirements
- ✅ Admin dapat edit jadwal yang sudah dipublikasi
- ✅ Perubahan jadwal ter-track dengan audit trail
- ✅ User yang terdampak menerima notifikasi otomatis
- ✅ Auto-assignment menghasilkan jadwal yang fair dan optimal
- ✅ Conflict detection berjalan real-time
- ✅ UI responsif dan user-friendly

### Non-Functional Requirements
- ✅ Response time < 200ms untuk operasi CRUD
- ✅ Auto-assignment selesai dalam < 5 detik untuk 12 slot
- ✅ Coverage rate minimal 80% dari auto-assignment
- ✅ Fairness score minimal 0.8 (max deviation 2 assignments)
- ✅ Zero critical conflicts setelah validation
- ✅ Mobile responsive (viewport 320px+)

### Code Quality
- ✅ Test coverage minimal 80%
- ✅ PSR-12 compliant
- ✅ No N+1 queries
- ✅ Proper error handling
- ✅ Comprehensive documentation

---

## 🤝 Collaboration Guidelines

### Code Review Checklist
- [ ] Follows Laravel conventions
- [ ] Proper validation & error handling
- [ ] Tests included and passing
- [ ] No performance regressions
- [ ] Documentation updated
- [ ] UI/UX reviewed
- [ ] Security considerations addressed

### Git Workflow
```bash
# Feature branch
git checkout -b feature/edit-schedule

# Commit with descriptive message
git commit -m "feat: implement edit schedule functionality"

# Push and create PR
git push origin feature/edit-schedule
```

### Commit Message Format
```
feat: add edit schedule component
fix: resolve conflict detection bug
refactor: optimize auto-assignment algorithm
docs: update schedule API documentation
test: add tests for edit functionality
```

---

## 📞 Support & Questions

Jika ada pertanyaan atau butuh klarifikasi:
1. Baca dokumentasi di `MASTER_DEVELOPMENT_GUIDE.md`
2. Cek `FEATURE_BACKLOG.md` untuk konteks fitur
3. Review existing code di `app/Livewire/Schedule/`
4. Lihat test examples di `tests/Feature/Schedule/`

---

**Last Updated**: 2025-11-23  
**Version**: 1.0  
**Author**: AI Development Team
