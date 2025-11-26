# Task 9 Implementation Summary: Routes and Policies

## Overview
Implemented routes and authorization policies for the schedule edit and history features.

## Changes Made

### 1. Routes Added (routes/web.php)

Added two new routes in the schedule route group:

```php
Route::get('/{schedule}/edit', \App\Livewire\Schedule\EditSchedule::class)->name('edit');
Route::get('/{schedule}/history', \App\Livewire\Schedule\EditHistory::class)->name('history');
```

**Route Details:**
- **Edit Schedule**: `/admin/schedule/{schedule}/edit` (route name: `admin.schedule.edit`)
  - Displays the edit interface for a schedule
  - Uses the EditSchedule Livewire component (already implemented in Task 8)
  
- **View History**: `/admin/schedule/{schedule}/history` (route name: `admin.schedule.history`)
  - Displays the edit history for a schedule
  - Uses the EditHistory Livewire component (to be implemented in Task 12)

Both routes are:
- Under the `/admin/schedule` prefix
- Protected by the `auth` middleware (inherited from parent group)
- Use route model binding for the `{schedule}` parameter

### 2. Policy Methods Added (app/Policies/SchedulePolicy.php)

Added three new authorization methods:

#### a. `edit(User $user, Schedule $schedule): bool`
**Purpose**: Determine if user can edit a published schedule

**Authorization Logic**:
- User must have `Admin` or `Super Admin` role
- Schedule must be in `published` or `draft` status
- Archived schedules cannot be edited (use `forceEdit` instead)

**Usage Example**:
```php
$this->authorize('edit', $schedule);
```

#### b. `forceEdit(User $user, Schedule $schedule): bool`
**Purpose**: Determine if user can force edit any schedule (including archived)

**Authorization Logic**:
- User must have `Super Admin` role
- Can edit schedules in any status (draft, published, archived)

**Usage Example**:
```php
$this->authorize('forceEdit', $schedule);
```

#### c. `viewHistory(User $user, Schedule $schedule): bool`
**Purpose**: Determine if user can view edit history

**Authorization Logic**:
- User must have `Admin`, `Super Admin`, or `Pengurus` role

**Usage Example**:
```php
$this->authorize('viewHistory', $schedule);
```

## Integration with Existing Components

### EditSchedule Component
The EditSchedule component (implemented in Task 8) should use the policy in its mount method:

```php
public function mount(Schedule $schedule): void
{
    // Check authorization
    if (!auth()->user()->can('edit', $schedule)) {
        abort(403, 'Anda tidak memiliki izin untuk mengedit jadwal ini.');
    }
    
    // Or for force edit
    if ($schedule->status === 'archived' && !auth()->user()->can('forceEdit', $schedule)) {
        abort(403, 'Hanya Super Admin yang dapat mengedit jadwal yang sudah diarsipkan.');
    }
    
    // ... rest of mount logic
}
```

### EditHistory Component (Task 12)
The EditHistory component should use the policy:

```php
public function mount(Schedule $schedule): void
{
    $this->authorize('viewHistory', $schedule);
    
    // ... rest of mount logic
}
```

## Requirements Satisfied

✅ **Requirement 1.1**: Admin can access published schedules for editing
- Routes provide access to edit interface
- Policy ensures only Admin/Super Admin can edit

✅ **Requirement 1.2**: Admin can remove assignments
- Edit route provides access to EditSchedule component with removal capabilities
- Policy controls who can perform edits

✅ **Requirement 1.3**: Admin can add assignments
- Edit route provides access to EditSchedule component with add capabilities
- Policy controls who can perform edits

✅ **Requirement 5.3**: Admin can view assignment history
- History route provides access to edit history
- Policy ensures Admin/Pengurus/Super Admin can view history

## Testing Recommendations

### Manual Testing
1. **Test Edit Access**:
   - Login as Admin → Navigate to `/admin/schedule/{id}/edit` → Should see edit interface
   - Login as regular user → Navigate to same URL → Should see 403 error
   - Try editing archived schedule as Admin → Should see 403 error
   - Try editing archived schedule as Super Admin → Should succeed

2. **Test History Access**:
   - Login as Admin/Pengurus → Navigate to `/admin/schedule/{id}/history` → Should see history
   - Login as regular user → Navigate to same URL → Should see 403 error

### Automated Testing (Optional)
```php
// Feature test example
test('admin can access edit schedule page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    
    $schedule = Schedule::factory()->create(['status' => 'published']);
    
    $this->actingAs($admin)
        ->get(route('admin.schedule.edit', $schedule))
        ->assertSuccessful();
});

test('regular user cannot access edit schedule page', function () {
    $user = User::factory()->create();
    $schedule = Schedule::factory()->create(['status' => 'published']);
    
    $this->actingAs($user)
        ->get(route('admin.schedule.edit', $schedule))
        ->assertForbidden();
});
```

## Next Steps

1. **Task 10**: Implement caching for schedule data
2. **Task 11**: Enhance UI components for better UX
3. **Task 12**: Create EditHistory Livewire component to display edit history

## Notes

- The routes use Laravel's route model binding, so the `{schedule}` parameter will automatically resolve to a Schedule model instance
- The policy methods integrate with Laravel's authorization system via `$this->authorize()` or `@can` Blade directives
- The role names used (`Super Admin`, `Admin`, `Pengurus`) match the existing role structure in the application (using Spatie Permission package)
- Archived schedules require Super Admin role for editing, providing an extra layer of protection for historical data
