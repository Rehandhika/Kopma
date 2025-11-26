# Update Waktu Sesi - SIKOPMA

## Ringkasan Perubahan

Waktu sesi telah diperbarui untuk menyelaraskan dengan jadwal operasional yang baru.

### Waktu Lama
- **Sesi 1**: 08:00 - 10:00 / 08:00 - 12:00
- **Sesi 2**: 10:00 - 12:00 / 12:00 - 16:00 / 13:00 - 17:00
- **Sesi 3**: 13:00 - 15:00 / 16:00 - 20:00 / 17:00 - 21:00

### Waktu Baru
- **Sesi 1**: 07:30 - 10:20
- **Sesi 2**: 10:20 - 12:50, 13:30 - 16:00
- **Sesi 3**: 13:30 - 16:00

## File yang Diubah

### 1. Konfigurasi
- `config/sikopma.php` - Konfigurasi utama waktu sesi
- `config/schedule.php` - Konfigurasi jadwal

### 2. Seeders
- `database/seeders/SystemSettingSeeder.php` - Setting sistem untuk waktu sesi
- `database/seeders/ScheduleSeeder.php` - Data jadwal awal
- `database/seeders/AttendanceSeeder.php` - Data attendance contoh
- `database/seeders/StoreSettingSeeder.php` - Jam operasional toko

### 3. Services
- `app/Services/AttendanceService.php` - Service untuk attendance
- `app/Services/AutoAssignmentService.php` - Service untuk auto-assignment
- `app/Services/ScheduleEditService.php` - Service untuk edit jadwal (sudah benar)

### 4. Livewire Components
- `app/Livewire/Schedule/EditSchedule.php` - Component edit jadwal (sudah benar)

## Langkah-Langkah Update Database

### 1. Update System Settings
Jalankan command berikut untuk mengupdate system settings di database:

```bash
php artisan db:seed --class=SystemSettingSeeder
```

### 2. Update Existing Schedule Assignments (Opsional)
Jika Anda ingin mengupdate jadwal yang sudah ada, jalankan query SQL berikut:

```sql
-- Update Sesi 1: 07:30 - 10:20
UPDATE schedule_assignments 
SET time_start = '07:30:00', time_end = '10:20:00' 
WHERE session = 1;

-- Update Sesi 2: 10:20 - 12:50
UPDATE schedule_assignments 
SET time_start = '10:20:00', time_end = '12:50:00' 
WHERE session = 2;

-- Update Sesi 3: 13:30 - 16:00
UPDATE schedule_assignments 
SET time_start = '13:30:00', time_end = '16:00:00' 
WHERE session = 3;
```

### 3. Clear Cache
Setelah update, clear cache aplikasi:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Catatan Penting

1. **Backward Compatibility**: Semua fungsi helper yang menggunakan `config('sikopma.session_times')` akan otomatis menggunakan waktu yang baru.

2. **Attendance Check-in**: Sistem attendance akan menggunakan waktu baru untuk menentukan keterlambatan.

3. **Schedule Generation**: Jadwal baru yang dibuat akan menggunakan waktu sesi yang baru.

4. **Existing Data**: Data jadwal dan attendance yang sudah ada tidak akan terpengaruh kecuali Anda menjalankan query SQL update di atas.

## Testing

Setelah update, pastikan untuk test:

1. ✅ Generate jadwal baru
2. ✅ Check-in attendance
3. ✅ Edit jadwal yang sudah ada
4. ✅ View jadwal di berbagai halaman
5. ✅ Notifikasi jadwal

## Rollback

Jika perlu rollback ke waktu lama, edit kembali file-file di atas dengan waktu lama dan jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

---

**Tanggal Update**: 24 November 2025
**Diupdate oleh**: Kiro AI Assistant
