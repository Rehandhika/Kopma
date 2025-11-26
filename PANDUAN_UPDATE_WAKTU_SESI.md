# Panduan Update Waktu Sesi SIKOPMA

## 📋 Ringkasan Perubahan

Sistem SIKOPMA telah diperbarui untuk menggunakan waktu sesi yang baru:

### ⏰ Waktu Sesi Baru
- **Sesi 1**: 07:30 - 10:20 (2 jam 50 menit)
- **Sesi 2**: 10:20 - 12:50 (2 jam 30 menit)
- **Sesi 3**: 13:30 - 16:00 (2 jam 30 menit)

### 📝 Catatan Khusus
Sesi 2 memiliki 2 periode:
- Pagi: 10:20 - 12:50
- Sore: 13:30 - 16:00 (ini sebenarnya Sesi 3)

## 🔧 File yang Telah Diubah

### 1. Konfigurasi (2 file)
- ✅ `config/sikopma.php` - Konfigurasi utama aplikasi
- ✅ `config/schedule.php` - Konfigurasi jadwal

### 2. Database Seeders (4 file)
- ✅ `database/seeders/SystemSettingSeeder.php` - Setting sistem
- ✅ `database/seeders/ScheduleSeeder.php` - Data jadwal contoh
- ✅ `database/seeders/AttendanceSeeder.php` - Data attendance contoh
- ✅ `database/seeders/StoreSettingSeeder.php` - Jam operasional toko

### 3. Services (3 file)
- ✅ `app/Services/AttendanceService.php` - Logika attendance
- ✅ `app/Services/AutoAssignmentService.php` - Auto-assignment jadwal
- ✅ `app/Services/ScheduleEditService.php` - Edit jadwal

### 4. Livewire Components (1 file)
- ✅ `app/Livewire/Schedule/EditSchedule.php` - Component edit jadwal

### 5. Tools Baru (3 file)
- ✅ `app/Console/Commands/UpdateSessionTimes.php` - Command untuk update
- ✅ `database/migrations/update_session_times.sql` - SQL script
- ✅ `WAKTU_SESI_UPDATE.md` - Dokumentasi teknis

## 🚀 Cara Mengupdate Database

### Opsi 1: Menggunakan Artisan Command (Recommended)

```bash
# Preview perubahan tanpa mengubah data (dry-run)
php artisan schedule:update-session-times --dry-run

# Update dengan konfirmasi
php artisan schedule:update-session-times

# Update tanpa konfirmasi (untuk automation)
php artisan schedule:update-session-times --force
```

### Opsi 2: Menggunakan SQL Script

```bash
# Jalankan SQL script
mysql -u username -p database_name < database/migrations/update_session_times.sql
```

### Opsi 3: Manual via Seeder

```bash
# Re-run system settings seeder
php artisan db:seed --class=SystemSettingSeeder
```

## 🧹 Clear Cache

Setelah update, **WAJIB** clear cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## ✅ Testing Checklist

Setelah update, test fitur-fitur berikut:

### 1. Schedule Management
- [ ] Generate jadwal baru
- [ ] Edit jadwal yang sudah ada
- [ ] View jadwal di halaman My Schedule
- [ ] View jadwal di halaman Schedule Management

### 2. Attendance
- [ ] Check-in pada waktu yang tepat (07:30)
- [ ] Check-in terlambat (setelah 07:45)
- [ ] Check-out
- [ ] View attendance history

### 3. Notifications
- [ ] Notifikasi jadwal menggunakan waktu yang benar
- [ ] Email notifikasi menampilkan waktu yang benar

### 4. Reports
- [ ] Laporan attendance menampilkan waktu yang benar
- [ ] Laporan jadwal menampilkan waktu yang benar

## 🔍 Verifikasi

### Cek System Settings
```sql
SELECT `key`, value 
FROM system_settings 
WHERE `key` LIKE 'schedule.session%'
ORDER BY `key`;
```

Expected output:
```
schedule.session_1_start | 07:30
schedule.session_1_end   | 10:20
schedule.session_2_start | 10:20
schedule.session_2_end   | 12:50
schedule.session_3_start | 13:30
schedule.session_3_end   | 16:00
```

### Cek Schedule Assignments
```sql
SELECT 
    session,
    time_start,
    time_end,
    COUNT(*) as total
FROM schedule_assignments
GROUP BY session, time_start, time_end
ORDER BY session;
```

Expected output:
```
1 | 07:30:00 | 10:20:00 | X
2 | 10:20:00 | 12:50:00 | X
3 | 13:30:00 | 16:00:00 | X
```

## 🔄 Rollback (Jika Diperlukan)

Jika perlu kembali ke waktu lama:

### Waktu Lama
- Sesi 1: 08:00 - 12:00
- Sesi 2: 13:00 - 17:00
- Sesi 3: 17:00 - 21:00

### Rollback Steps
1. Edit file konfigurasi kembali ke waktu lama
2. Jalankan SQL update untuk schedule_assignments
3. Clear cache

```sql
UPDATE schedule_assignments SET time_start = '08:00:00', time_end = '12:00:00' WHERE session = '1';
UPDATE schedule_assignments SET time_start = '13:00:00', time_end = '17:00:00' WHERE session = '2';
UPDATE schedule_assignments SET time_start = '17:00:00', time_end = '21:00:00' WHERE session = '3';
```

## 📊 Impact Analysis

### Data yang Terpengaruh
- ✅ **System Settings**: 6 records
- ✅ **Schedule Assignments**: Semua records dengan session 1, 2, 3
- ✅ **Config Cache**: Akan di-clear
- ⚠️ **Attendance Records**: Tidak terpengaruh (historical data)

### Fitur yang Terpengaruh
- ✅ Schedule Generation
- ✅ Schedule Editing
- ✅ Attendance Check-in/out
- ✅ Late Calculation
- ✅ Notifications
- ✅ Reports

### Backward Compatibility
- ✅ Helper functions tetap bekerja (menggunakan config)
- ✅ API endpoints tetap bekerja
- ✅ Livewire components tetap bekerja

## 🐛 Troubleshooting

### Issue: Waktu masih menampilkan yang lama
**Solution**: Clear cache
```bash
php artisan cache:clear
php artisan config:clear
```

### Issue: Schedule assignments tidak terupdate
**Solution**: Jalankan command update
```bash
php artisan schedule:update-session-times
```

### Issue: Error saat check-in
**Solution**: 
1. Cek system settings sudah terupdate
2. Clear cache
3. Restart queue worker jika menggunakan queue

## 📞 Support

Jika ada masalah atau pertanyaan:
1. Cek file `WAKTU_SESI_UPDATE.md` untuk detail teknis
2. Review logs di `storage/logs/laravel.log`
3. Jalankan diagnostics: `php artisan schedule:update-session-times --dry-run`

---

**Tanggal Update**: 24 November 2025  
**Status**: ✅ Completed  
**Tested**: ✅ All files validated, no syntax errors
