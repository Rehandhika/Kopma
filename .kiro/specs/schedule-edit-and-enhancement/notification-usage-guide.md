# Schedule Notification System - Usage Guide

## Overview
The schedule notification system automatically sends email notifications to users when their assignments are added, removed, or updated.

## Automatic Notifications

All ScheduleEditService methods automatically send notifications:

### 1. Adding a User to a Slot
```php
$scheduleEditService = app(ScheduleEditService::class);

$assignment = $scheduleEditService->addUserToSlot(
    schedule: $schedule,
    date: '2024-11-25',
    session: 1,
    userId: $userId,
    reason: 'Menggantikan anggota yang berhalangan'
);

// Automatically sends "assignment_added" notification to the user
// Includes: date, session, time, slotmates, reason
```

### 2. Removing a User from a Slot
```php
$scheduleEditService->removeUserFromSlot(
    assignment: $assignment,
    reason: 'Anggota berhalangan hadir'
);

// Automatically sends "assignment_removed" notification to the user
// Includes: date, session, time, reason
```

### 3. Updating a User in a Slot
```php
$assignment = $scheduleEditService->updateUserInSlot(
    assignment: $assignment,
    newUserId: $newUserId,
    reason: 'Pertukaran shift'
);

// Automatically sends TWO notifications:
// 1. "assignment_removed" to the old user
// 2. "assignment_added" to the new user
```

### 4. Clearing a Slot
```php
$scheduleEditService->clearSlot(
    schedule: $schedule,
    date: '2024-11-25',
    session: 1,
    reason: 'Slot tidak diperlukan'
);

// Automatically sends "assignment_removed" to ALL users in the slot
```

### 5. Bulk Adding Users
```php
$assignments = $scheduleEditService->bulkAddUsersToSlot(
    schedule: $schedule,
    date: '2024-11-25',
    session: 1,
    userIds: [1, 2, 3]
);

// Automatically sends "assignment_added" to ALL added users
```

## Manual Notifications

You can also send notifications manually:

### Send to Single User
```php
$scheduleEditService = app(ScheduleEditService::class);

$scheduleEditService->notifyUser(
    user: $user,
    notificationType: 'assignment_added',
    data: [
        'schedule_id' => $schedule->id,
        'date' => '2024-11-25',
        'session' => 1,
        'assignment_id' => $assignment->id,
    ],
    reason: 'Custom reason'
);
```

### Send to All Users in a Slot
```php
$scheduleEditService->notifySlotUsers(
    schedule: $schedule,
    date: '2024-11-25',
    session: 1,
    notificationType: 'assignment_added',
    reason: 'Slot update notification'
);
```

## Notification Types

### 1. assignment_added
**When:** User is assigned to a new slot
**Includes:**
- Date and day name (e.g., "Senin, 25 November 2024")
- Session (Sesi 1, 2, or 3)
- Time range (e.g., "07:30 - 10:20")
- Slotmates (other users in the same slot)
- Reason for assignment
- Link to view full schedule

**Example:**
```
Title: ✅ Jadwal Baru Ditambahkan
Message: Anda telah dijadwalkan untuk bertugas pada Senin, 25 November 2024, 
         Sesi 1 (07:30 - 10:20).
```

### 2. assignment_removed
**When:** User is removed from a slot
**Includes:**
- Date and day name
- Session
- Time range
- Reason for removal
- Link to view full schedule

**Example:**
```
Title: ❌ Jadwal Dihapus
Message: Jadwal Anda pada Senin, 25 November 2024, Sesi 1 (07:30 - 10:20) 
         telah dihapus.
```

### 3. assignment_updated
**When:** User's assignment is changed
**Includes:**
- Old schedule details
- New schedule details
- Reason for update
- Link to view full schedule

**Example:**
```
Title: 🔄 Jadwal Diperbarui
Message: Jadwal Anda telah diperbarui.
Old: Senin, 25 November 2024, Sesi 1 (07:30 - 10:20)
New: Selasa, 26 November 2024, Sesi 2 (10:20 - 12:50)
```

### 4. schedule_published
**When:** New schedule is published
**Includes:**
- Total number of assignments
- List of all assignments
- Link to view full schedule

**Example:**
```
Title: 📅 Jadwal Baru Dipublikasikan
Message: Jadwal baru telah dipublikasikan. Anda memiliki 3 jadwal tugas.
Assignments:
• Senin, 25 Nov, Sesi 1 (07:30 - 10:20)
• Rabu, 27 Nov, Sesi 2 (10:20 - 12:50)
• Kamis, 28 Nov, Sesi 3 (13:30 - 16:00)
```

## Notification Data Structure

### Required Data Fields
```php
[
    'schedule_id' => 1,           // Schedule ID
    'date' => '2024-11-25',       // Date in Y-m-d format
    'session' => 1,               // Session number (1, 2, or 3)
]
```

### Optional Data Fields
```php
[
    'assignment_id' => 123,       // Assignment ID (for slotmate info)
    'old_date' => '2024-11-25',   // Old date (for updates)
    'old_session' => 1,           // Old session (for updates)
    'assignment_count' => 3,      // Total assignments (for published)
    'assignments' => [            // List of assignments (for published)
        ['date' => '2024-11-25', 'session' => 1],
        ['date' => '2024-11-27', 'session' => 2],
    ],
]
```

## Email Priority Levels

Notifications are sent with different priority levels:

- **High Priority:** assignment_removed, assignment_updated
- **Normal Priority:** assignment_added, schedule_published

## Slotmate Information

When a user is added to a slot with other users, the notification includes:
- Number of slotmates
- Names of all slotmates
- Avatars (if available)

Example:
```
👥 Rekan Kerja Anda (2 orang)
• Ahmad Rizki
• Siti Nurhaliza
```

## Error Handling

The notification system handles errors gracefully:

1. **No Email:** If user has no email, notification is skipped (logged as warning)
2. **Send Failure:** If email fails to send, error is logged but operation continues
3. **Invalid Data:** Missing data fields use defaults or are omitted

## Logging

All notification activities are logged:

```php
// Success
Log::info("Notification sent to user", [
    'user_id' => 123,
    'email' => 'user@example.com',
    'type' => 'assignment_added',
]);

// Failure
Log::error("Failed to send notification", [
    'user_id' => 123,
    'type' => 'assignment_added',
    'error' => 'SMTP connection failed',
]);
```

## Testing Notifications

### Test in Development
```php
// In tinker or test
$user = User::find(1);
$schedule = Schedule::find(1);

$service = app(ScheduleEditService::class);
$service->notifyUser(
    $user,
    'assignment_added',
    [
        'schedule_id' => $schedule->id,
        'date' => now()->format('Y-m-d'),
        'session' => 1,
    ],
    'Test notification'
);
```

### Preview Email Template
```php
// Create a test mailable
$mailable = new \App\Mail\ScheduleNotification(
    '✅ Jadwal Baru Ditambahkan',
    'Anda telah dijadwalkan untuk bertugas pada Senin, 25 November 2024, Sesi 1 (07:30 - 10:20).',
    [
        'Tanggal' => 'Senin, 25 November 2024',
        'Hari' => 'Senin',
        'Sesi' => 'Sesi 1',
        'Waktu' => '07:30 - 10:20',
        'Alasan' => 'Test',
        'schedule_info' => 'Pastikan Anda hadir tepat waktu.',
        'action_url' => url('/schedule/1'),
        'action_text' => 'Lihat Jadwal Lengkap',
    ],
    'assignment_added',
    'normal'
);

// Preview in browser
return $mailable;
```

## Configuration

### Mail Configuration
Ensure your `.env` file has mail settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sikopma.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Session Times
Session times are defined in `ScheduleEditService::prepareNotificationData()`:
- Sesi 1: 07:30 - 10:20
- Sesi 2: 10:20 - 12:50
- Sesi 3: 13:30 - 16:00

To change these, update the `$sessionTimes` array in the method.

## Best Practices

1. **Always Provide Reason:** Include a reason when making changes
   ```php
   $service->addUserToSlot(..., reason: 'Menggantikan anggota yang sakit');
   ```

2. **Use Descriptive Reasons:** Help users understand why changes were made
   ```php
   // Good
   reason: 'Pertukaran shift dengan Ahmad karena konflik jadwal kuliah'
   
   // Bad
   reason: 'Update'
   ```

3. **Test Before Production:** Always test notifications in development
   ```php
   // Use Mailtrap or similar service in development
   ```

4. **Monitor Logs:** Check logs regularly for failed notifications
   ```bash
   tail -f storage/logs/laravel.log | grep "notification"
   ```

5. **Handle Bulk Operations:** For bulk operations, notifications are sent to all affected users
   ```php
   // This will send 5 notifications
   $service->bulkAddUsersToSlot(..., userIds: [1, 2, 3, 4, 5]);
   ```

## Troubleshooting

### Notifications Not Sending
1. Check mail configuration in `.env`
2. Verify user has email address
3. Check logs for errors: `storage/logs/laravel.log`
4. Test mail connection: `php artisan tinker` → `Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));`

### Wrong Template Rendering
1. Clear view cache: `php artisan view:clear`
2. Check template path: `resources/views/emails/schedule-notification.blade.php`
3. Verify data being passed to template

### Missing Slotmate Information
1. Ensure `assignment_id` is included in notification data
2. Check that assignment exists and has slotmates
3. Verify `getSlotmates()` method on ScheduleAssignment model

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review this guide
3. Contact system administrator
