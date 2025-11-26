# Fix: Attendance Check-In Sinkronisasi dengan Jadwal Aktif

## Masalah yang Ditemukan

1. **Tidak memeriksa status schedule**: Query tidak memvalidasi apakah schedule dalam status 'published'
2. **Logika waktu tidak tepat**: Hanya memeriksa waktu tanpa mempertimbangkan toleransi check-in
3. **Tidak ada fallback**: Tidak menampilkan jadwal upcoming atau past jika tidak ada jadwal aktif
4. **Tidak ada status visual**: User tidak tahu apakah jadwal sedang aktif, akan datang, atau sudah lewat

## Solusi yang Diterapkan

### 1. Perbaikan Query Schedule (CheckInOut.php)

**Prioritas pencarian jadwal:**
- **Priority 1**: Jadwal yang sedang aktif (dalam rentang waktu sesi)
- **Priority 2**: Jadwal upcoming hari ini (belum dimulai)
- **Priority 3**: Jadwal past hari ini (untuk late check-in)

**Validasi tambahan:**
```php
->whereHas('schedule', function($query) {
    $query->where('status', 'published');
})
```

### 2. Status Jadwal

Menambahkan property `$scheduleStatus` dengan 3 nilai:
- `active`: Jadwal sedang berlangsung
- `upcoming`: Jadwal belum dimulai
- `past`: Jadwal sudah lewat

### 3. Toleransi Check-In

- Check-in dapat dilakukan **30 menit sebelum** jadwal dimulai
- Konfigurasi: `sikopma.attendance.check_in_tolerance_minutes`
- Late check-in tetap diperbolehkan untuk jadwal yang sudah lewat

### 4. UI/UX Improvements

**Status Badge:**
- 🟢 Hijau: Sedang Berlangsung
- 🔵 Biru: Akan Datang
- 🟡 Kuning: Sudah Lewat

**Informasi Tambahan:**
- Menampilkan periode jadwal (week_start_date - week_end_date)
- Countdown untuk jadwal upcoming
- Alert untuk jadwal past

**Tombol Check-In:**
- Disabled jika belum waktunya dengan informasi countdown
- Enabled dengan toleransi 30 menit sebelum jadwal

### 5. Auto-Refresh

Menambahkan polling setiap 60 detik untuk memperbarui status jadwal:
```blade
wire:poll.60s="refreshSchedule"
```

## Best Practices yang Diterapkan

1. **Eager Loading**: Load relasi `schedule` dan `user` untuk menghindari N+1 query
2. **Separation of Concerns**: Method `canCheckInNow()` dan `getTimeUntilCheckIn()` untuk logika bisnis
3. **Configuration**: Toleransi waktu dapat dikonfigurasi via config file
4. **User Feedback**: Status visual yang jelas dan pesan error yang informatif
5. **Real-time Updates**: Auto-refresh untuk memastikan data selalu up-to-date

## Testing

Untuk testing, pastikan:
1. Ada schedule dengan status 'published'
2. User memiliki assignment untuk hari ini
3. Test pada berbagai waktu:
   - Sebelum jadwal (> 30 menit)
   - Dalam toleransi (< 30 menit sebelum)
   - Saat jadwal aktif
   - Setelah jadwal lewat

## File yang Diubah

1. `app/Livewire/Attendance/CheckInOut.php`
   - Method `loadCurrentSchedule()` - Query dan logika pencarian jadwal
   - Method `checkIn()` - Validasi tambahan
   - Method `canCheckInNow()` - Helper untuk validasi
   - Method `getTimeUntilCheckIn()` - Helper untuk countdown
   - Method `refreshSchedule()` - Auto-refresh handler

2. `resources/views/livewire/attendance/check-in-out.blade.php`
   - Status badge dengan warna dinamis
   - Alert untuk upcoming/past schedule
   - Tombol check-in dengan kondisi
   - Auto-refresh polling
