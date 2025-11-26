# Seeder Information

## Seeder yang Tersedia

### 1. ScheduleSeeder
Membuat data jadwal contoh untuk sistem penjadwalan.

**Data yang dibuat:**
- 3 jadwal: minggu lalu (published), minggu ini (published), minggu depan (draft)
- Setiap jadwal memiliki 12 slot (4 hari × 3 sesi)
- Assignment otomatis untuk anggota aktif (1-2 user per slot)
- Coverage rate dihitung otomatis

**Cara menjalankan:**
```bash
php artisan db:seed --class=ScheduleSeeder
```

### 2. ProductSeeder (Updated)
Membuat data produk contoh untuk toko KOPMA.

**Data yang dibuat:**
- 25 produk dengan 4 kategori:
  - Minuman (5 produk)
  - Makanan (7 produk)
  - ATK (7 produk)
  - Kebutuhan Harian (6 produk)
- Setiap produk memiliki SKU unik, harga, stok awal, dan deskripsi

**Cara menjalankan:**
```bash
php artisan db:seed --class=ProductSeeder
```

## Menjalankan Semua Seeder

Untuk menjalankan semua seeder sekaligus:

```bash
php artisan db:seed
```

Atau untuk fresh migration + seeding:

```bash
php artisan migrate:fresh --seed
```

## Catatan

- ScheduleSeeder memerlukan UserSeeder dijalankan terlebih dahulu
- ProductSeeder akan skip jika sudah ada data produk
- ScheduleSeeder akan skip jika sudah ada data jadwal
