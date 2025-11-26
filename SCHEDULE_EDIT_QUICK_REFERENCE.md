# Schedule Edit - Quick Reference Guide

## 🚀 Quick Fix Commands

```bash
# Fix configuration and clear cache
php artisan schedule:fix-config

# Run migration (if not done)
php artisan migrate

# Clear all caches
php artisan cache:clear

# Run tests
php artisan test --filter=ScheduleEditConfigTest
```

## 🔧 Configuration Settings

### Unlimited Slots (Default)
```php
max_users_per_slot = null  // Recommended
```
✅ No restrictions on users per slot

### Limited Slots
```php
max_users_per_slot = 5  // Any positive integer
```
⚠️ Enforces maximum users per slot

## 📊 How It Works

### Adding Users to Slots

**Unlimited Mode (null or 0)**:
```
User clicks "Tambah" → Select user → ✅ Added (no limit check)
```

**Limited Mode (e.g., 5)**:
```
User clicks "Tambah" → Select user → Check count
  ├─ If count < 5: ✅ Added
  └─ If count >= 5: ❌ Error: "Slot sudah penuh (maksimal 5 user)"
```

## 🐛 Troubleshooting

### Problem: "Slot sudah penuh (maksimal 0 user)"

**Solution**:
```bash
php artisan schedule:fix-config
```

### Problem: Users can't be added to any slot

**Check**:
1. Configuration value: `SELECT * FROM schedule_configurations WHERE key = 'max_users_per_slot'`
2. Should be `NULL` or positive integer, NOT `0`

**Fix**:
```sql
UPDATE schedule_configurations 
SET value = NULL 
WHERE key = 'max_users_per_slot';
```

### Problem: Changes not taking effect

**Solution**:
```bash
php artisan cache:clear
php artisan config:clear
```

## 📝 Code Examples

### Check if Slot is Full
```php
$isFull = $editService->isSlotFull($schedule, $date, $session);

// Returns:
// - false: if max_users_per_slot is null or 0 (unlimited)
// - false: if current count < limit
// - true: if current count >= limit
```

### Add User to Slot
```php
try {
    $assignment = $editService->addUserToSlot(
        $schedule,
        '2025-11-24',
        1,
        $userId,
        'Manual assignment'
    );
    // Success
} catch (ValidationException $e) {
    // Slot full or other validation error
}
```

### Bulk Add Users
```php
$userIds = [1, 2, 3, 4, 5];
$assignments = $editService->bulkAddUsersToSlot(
    $schedule,
    '2025-11-24',
    1,
    $userIds
);
```

## 🎯 Performance Tips

### Before (Slow)
- ❌ Multiple cache layers
- ❌ Cache invalidation overhead
- ❌ Complex cache management

### After (Fast)
- ✅ Direct database queries
- ✅ Optimized column selection
- ✅ Simple, maintainable code

### Query Optimization
```php
// Only load what you need
ScheduleAssignment::where('schedule_id', $id)
    ->with(['user:id,name,photo,status'])
    ->select('id', 'user_id', 'date', 'session', 'status')
    ->get();
```

## 🔍 Validation Logic

### Configuration Value Handling
```php
// NULL or 0 = Unlimited
if ($maxUsersPerSlot === null || $maxUsersPerSlot === 0) {
    return false; // Never full
}

// Positive integer = Limited
if ($currentCount >= $maxUsersPerSlot) {
    return true; // Full
}
```

### Validation Checks (in order)
1. ✅ User exists
2. ✅ User is active
3. ✅ No duplicate in same slot
4. ✅ Slot not full (if limit set)
5. ✅ User availability (warning only)

## 📈 Monitoring

### Check Current Configuration
```bash
php artisan schedule:fix-config
```

### Check Database Directly
```sql
SELECT key, value, type, description 
FROM schedule_configurations 
WHERE key LIKE '%slot%';
```

### Check Logs
```bash
tail -f storage/logs/laravel.log | grep -i "schedule"
```

## 🎨 UI Behavior

### Slot Card States
- **Empty** (Gray): No users assigned
- **Normal** (Green): Has users, not full
- **Overstaffed** (Yellow): More than threshold (default: 3)
- **Conflict** (Red): Has validation conflicts
- **Edited** (Blue): Recently modified

### Button States
- **Tambah**: Enabled when slot not full (or unlimited)
- **Kosongkan**: Enabled when slot has users
- **Simpan**: Enabled when no critical conflicts

## 🔐 Security

### Authorization
```php
// Required for editing
$this->authorize('edit', $schedule);
```

### Audit Trail
All changes are logged in `assignment_edit_histories` table:
- Who made the change
- What was changed
- When it was changed
- Why it was changed (reason)

## 📚 Related Files

### Core Files
- `app/Livewire/Schedule/EditSchedule.php` - Main component
- `app/Services/ScheduleEditService.php` - Business logic
- `app/Services/ScheduleConfigurationService.php` - Config management

### Views
- `resources/views/livewire/schedule/edit-schedule.blade.php` - Main view
- `resources/views/components/schedule/slot-card.blade.php` - Slot cards

### Database
- `schedule_configurations` - System configuration
- `schedule_assignments` - User assignments
- `assignment_edit_histories` - Audit trail

## 💡 Best Practices

1. **Always use services** for business logic, not direct model access
2. **Validate before saving** to prevent invalid states
3. **Log important actions** for audit trail
4. **Clear cache** after configuration changes
5. **Test thoroughly** after making changes

## 🆘 Emergency Contacts

If critical issues occur:
1. Check logs: `storage/logs/laravel.log`
2. Run diagnostics: `php artisan schedule:fix-config`
3. Rollback migration if needed: `php artisan migrate:rollback --step=1`
4. Contact system administrator

## ✅ Checklist for Deployment

- [ ] Run migration: `php artisan migrate`
- [ ] Fix configuration: `php artisan schedule:fix-config`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Run tests: `php artisan test --filter=ScheduleEditConfigTest`
- [ ] Test edit page manually
- [ ] Verify users can be added to slots
- [ ] Check logs for errors
- [ ] Monitor performance

---

**Last Updated**: November 24, 2024
**Version**: 1.0
**Status**: ✅ Production Ready
