# EditSchedule Component Usage Guide

## Quick Start

### 1. Add Route
Add this route to your `routes/web.php`:

```php
use App\Livewire\Schedule\EditSchedule;

Route::middleware(['auth'])->group(function () {
    Route::get('/schedules/{schedule}/edit', EditSchedule::class)
        ->name('schedules.edit')
        ->middleware('can:edit,schedule');
});
```

### 2. Link from Schedule Index
Add an edit button to your schedule list:

```blade
<a href="{{ route('schedules.edit', $schedule) }}" 
   class="text-blue-600 hover:text-blue-800">
    Edit Schedule
</a>
```

### 3. Access the Component
Navigate to: `/schedules/{schedule_id}/edit`

## User Interface Guide

### Main Screen Layout

```
┌─────────────────────────────────────────────────────────┐
│ Edit Jadwal                    [2 perubahan] [Batal] [Simpan] │
│ 18 Nov - 21 Nov 2025                                    │
├─────────────────────────────────────────────────────────┤
│ ⚠️ Konflik Terdeteksi                    [Sembunyikan] │
│ ❌ 1 Konflik Kritis                                     │
│ ⚠️ 2 Peringatan                                         │
├─────────────────────────────────────────────────────────┤
│                    Schedule Grid                         │
│  ┌──────────┬──────────┬──────────┬──────────┐         │
│  │  Senin   │  Selasa  │  Rabu    │  Kamis   │         │
│  ├──────────┼──────────┼──────────┼──────────┤         │
│  │ Sesi 1   │ Sesi 1   │ Sesi 1   │ Sesi 1   │         │
│  │ 👥 3     │ 👥 2     │ [Empty]  │ 👥 1     │         │
│  │ User A×  │ User D×  │ + Tambah │ User G×  │         │
│  │ User B×  │ User E×  │          │ + Tambah │         │
│  │ User C×  │ + Tambah │          │          │         │
│  │ + Tambah │ 🗑️       │          │          │         │
│  │ 🗑️       │          │          │          │         │
└─────────────────────────────────────────────────────────┘
```

## Common Operations

### Adding a Single User to a Slot

1. Click the **"+ Tambah"** button on the desired slot
2. A modal will open showing available users
3. Search for a user (optional)
4. Click **"Tambah"** next to the user's name
5. The user will be added immediately
6. The slot will update to show the new user

### Adding Multiple Users (Bulk Add)

1. Click the **"+ Tambah"** button on the desired slot
2. Check the boxes next to multiple users
3. Click **"Tambah X User"** at the bottom
4. All selected users will be added at once

### Removing a User from a Slot

1. Find the user in the slot card
2. Click the **"×"** button next to their name
3. The user will be removed immediately
4. A notification will confirm the removal

### Clearing an Entire Slot

1. Click the **"🗑️"** button on the slot card
2. All users in that slot will be removed
3. The slot will become empty

### Searching for Users

1. Open the user selector modal
2. Type in the search box at the top
3. Results will filter as you type
4. Search works on name and email

## Understanding Slot Colors

### 🟢 Green Border - Normal
- Slot has users assigned
- No conflicts detected
- Everything is good

### 🔴 Red Border - Critical Conflict
- Duplicate user in slot
- Inactive user assigned
- Must be fixed before saving

### 🟡 Yellow Border - Warning
- User marked unavailable
- Slot overstaffed (too many users)
- Can save with acknowledgment

### 🔵 Blue Border - Recently Edited
- Slot was recently modified
- Shows who made the edit
- Informational only

### ⚪ Gray Border - Empty
- No users assigned to slot
- Allowed by default
- Can add users

### 🟠 Orange Border - Overstaffed
- More users than recommended threshold
- Not an error, just a warning
- Consider redistributing

## Conflict Resolution

### Critical Conflicts (Must Fix)

**Duplicate User in Slot**
- Same user appears twice in one slot
- Remove one of the duplicates
- Click × next to the duplicate entry

**Inactive User**
- User's status is not "active"
- Remove the inactive user
- Replace with an active user

### Warnings (Can Save)

**Availability Mismatch**
- User marked unavailable for this slot
- Review if intentional
- Can proceed if needed

**Overstaffed Slot**
- More users than threshold (default: 3)
- Consider moving users to empty slots
- Not blocking

## Saving Changes

### When to Save
- Changes are persisted immediately to database
- "Save" button mainly for final validation
- Use "Save" to:
  - Run final conflict check
  - Recalculate coverage
  - Clear change tracking
  - Invalidate caches

### Before Saving
1. Review the conflict panel
2. Fix any critical conflicts (red)
3. Review warnings (yellow)
4. Check statistics panel

### Save Button States
- **Enabled**: No critical conflicts
- **Disabled**: Critical conflicts exist
- **Badge**: Shows number of tracked changes

## Discarding Changes

**Note**: Since changes are persisted immediately, "Discard" will reload data from the database. This is useful if:
- You want to see the latest state
- Another admin made changes
- You want to refresh the view

## Statistics Panel

Shows real-time metrics:
- **Filled Slots**: X/12 slots have at least one user
- **Coverage Rate**: Percentage of slots filled
- **Total Assignments**: Total number of user assignments
- **Avg Users/Slot**: Average users across all slots

## Tips and Best Practices

### 1. Check Conflicts First
Always review the conflict panel before making changes. Fix critical issues first.

### 2. Use Bulk Add for Efficiency
When adding multiple users to the same slot, use the bulk add feature instead of adding one by one.

### 3. Balance Workload
Use the statistics panel to ensure fair distribution. Aim for similar user counts across slots.

### 4. Respect Availability
Pay attention to availability warnings. Users marked unavailable may have valid reasons.

### 5. Clear Empty Slots
If a slot is empty and should stay empty, that's fine. Empty slots are allowed by default.

### 6. Review Before Saving
Even though changes are immediate, use the Save button to run final validation and clear tracking.

### 7. Use Search Effectively
When looking for specific users, use the search feature in the user selector modal.

### 8. Monitor Overstaffing
Keep an eye on slots with many users. Consider redistributing for better balance.

## Keyboard Shortcuts

Currently not implemented, but planned for future:
- `Ctrl+S`: Save changes
- `Ctrl+Z`: Undo last change
- `Esc`: Close modal
- `Ctrl+F`: Focus search

## Troubleshooting

### "Cannot add user" Error
**Possible causes:**
- User is already in the slot
- User is inactive
- Slot is at maximum capacity

**Solution:**
- Check if user is already assigned
- Verify user status is "active"
- Check slot capacity settings

### "Cannot save: critical conflicts" Error
**Cause:** Critical conflicts exist in the schedule

**Solution:**
1. Open the conflict panel
2. Review critical conflicts (red)
3. Fix each conflict
4. Try saving again

### Changes Not Appearing
**Cause:** Cache or browser issue

**Solution:**
1. Click "Discard" to reload data
2. Refresh the page
3. Clear browser cache if needed

### User Not in Search Results
**Possible causes:**
- User is inactive
- User is already in the slot
- Search term doesn't match

**Solution:**
- Check user status in user management
- Verify user is not already assigned
- Try different search terms

## Configuration

### Adjusting Slot Capacity
Edit in database or via seeder:
```php
ScheduleConfiguration::updateOrCreate(
    ['key' => 'max_users_per_slot'],
    ['value' => '5', 'type' => 'integer']
);
```

### Allowing/Disallowing Empty Slots
```php
ScheduleConfiguration::updateOrCreate(
    ['key' => 'allow_empty_slots'],
    ['value' => 'true', 'type' => 'boolean']
);
```

### Overstaffed Threshold
```php
ScheduleConfiguration::updateOrCreate(
    ['key' => 'overstaffed_threshold'],
    ['value' => '3', 'type' => 'integer']
);
```

## API for Developers

### Accessing the Component Programmatically

```php
// Get component instance
$component = new EditSchedule();

// Mount with schedule
$component->mount($schedule);

// Add user to slot
$component->addUserToSlot('2025-11-18', 1, $userId);

// Remove user
$component->removeUserFromSlot($assignmentId);

// Clear slot
$component->clearSlot('2025-11-18', 1);

// Bulk add
$component->bulkAddUsers('2025-11-18', 1, [$userId1, $userId2]);

// Check conflicts
$conflicts = $component->conflicts;

// Get statistics
$stats = $component->statistics;
```

### Listening to Events

```javascript
// Listen for notifications
Livewire.on('notify', (event) => {
    console.log(event.type, event.message);
});

// Listen for data refresh
Livewire.on('data-refreshed', () => {
    console.log('Data has been refreshed');
});
```

## Support

For issues or questions:
1. Check the implementation summary document
2. Review the design document
3. Check application logs
4. Contact the development team

## Version History

- **v1.0** (Nov 23, 2025): Initial implementation
  - Multi-user slot management
  - Real-time conflict detection
  - Change tracking
  - Statistics panel
  - User selector modal
