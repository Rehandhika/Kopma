# Quick Reference - Update Waktu Sesi

## ⏰ Waktu Sesi Baru

```
Sesi 1: 07:30 - 10:20 (2 jam 50 menit)
Sesi 2: 10:20 - 12:50 (2 jam 30 menit)  
Sesi 3: 13:30 - 16:00 (2 jam 30 menit)
```

## 🚀 Quick Start

### 1. Update Database (Pilih salah satu)

```bash
# Recommended: Artisan Command
php artisan schedule:update-session-times

# Alternative: SQL Script
mysql -u username -p database_name < database/migrations/update_session_times.sql
```

### 2. Clear Cache (Wajib!)

```bash
php artisan cache:clear && php artisan config:clear && php artisan view:clear
```

### 3. Verify

```sql
-- Cek system settings
SELECT `key`, value FROM system_settings WHERE `key` LIKE 'schedule.session%';

-- Cek schedule assignments
SELECT session, time_start, time_end, COUNT(*) FROM schedule_assignments GROUP BY session, time_start, time_end;
```

## 📁 Files Changed

```
✅ config/sikopma.php
✅ config/schedule.php
✅ database/seeders/SystemSettingSeeder.php
✅ database/seeders/ScheduleSeeder.php
✅ database/seeders/AttendanceSeeder.php
✅ database/seeders/StoreSettingSeeder.php
✅ app/Services/AttendanceService.php
✅ app/Services/AutoAssignmentService.php
✅ app/Services/ScheduleEditService.php
✅ app/Livewire/Schedule/EditSchedule.php
```

## 🆕 New Files

```
✅ app/Console/Commands/UpdateSessionTimes.php
✅ database/migrations/update_session_times.sql
✅ WAKTU_SESI_UPDATE.md
✅ PANDUAN_UPDATE_WAKTU_SESI.md
✅ SUMMARY_PERUBAHAN_WAKTU_SESI.md
```

## ✅ Testing Checklist

```
[ ] Generate jadwal baru
[ ] Edit jadwal existing
[ ] Check-in attendance (07:30)
[ ] Check-in terlambat (07:50)
[ ] View jadwal di My Schedule
[ ] View notifikasi jadwal
[ ] Generate laporan
```

## 🐛 Troubleshooting

**Waktu masih lama?**
```bash
php artisan cache:clear
php artisan config:clear
```

**Schedule tidak terupdate?**
```bash
php artisan schedule:update-session-times --force
```

**Error saat check-in?**
```bash
# Cek system settings
php artisan tinker
>>> App\Models\SystemSetting::where('key', 'like', 'schedule.session%')->get()
```

## 📚 Documentation

- **Technical Details**: `WAKTU_SESI_UPDATE.md`
- **Complete Guide**: `PANDUAN_UPDATE_WAKTU_SESI.md`
- **Summary**: `SUMMARY_PERUBAHAN_WAKTU_SESI.md`

## 🔄 Rollback (Emergency)

```sql
UPDATE schedule_assignments SET time_start = '08:00:00', time_end = '12:00:00' WHERE session = '1';
UPDATE schedule_assignments SET time_start = '13:00:00', time_end = '17:00:00' WHERE session = '2';
UPDATE schedule_assignments SET time_start = '17:00:00', time_end = '21:00:00' WHERE session = '3';
```

Then:
```bash
php artisan cache:clear && php artisan config:clear
```

---

**Status**: ✅ Ready to Deploy  
**Last Updated**: 24 November 2025
