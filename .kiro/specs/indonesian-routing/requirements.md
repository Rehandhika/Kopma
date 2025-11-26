# Requirements Document

## Introduction

Sistem routing aplikasi saat ini menggunakan bahasa Inggris untuk semua URL dan nama route. Fitur ini akan mengubah seluruh routing menjadi bahasa Indonesia untuk meningkatkan konsistensi dengan target pengguna lokal dan mempermudah pemahaman struktur URL bagi pengguna Indonesia.

## Glossary

- **Routing System**: Sistem yang mengelola URL dan endpoint aplikasi Laravel
- **Route Name**: Nama internal yang digunakan untuk mereferensikan route dalam kode
- **URL Path**: Path yang terlihat di browser (contoh: /admin/dashboard)
- **Livewire Component**: Komponen full-stack Laravel yang menangani logika halaman
- **Redirect Route**: Route yang mengarahkan dari URL lama ke URL baru
- **Middleware**: Filter yang dijalankan sebelum request mencapai controller/component
- **Blade View**: Template file yang merender HTML

## Requirements

### Requirement 1

**User Story:** Sebagai pengguna aplikasi, saya ingin semua URL menggunakan bahasa Indonesia, sehingga lebih mudah dipahami dan diingat.

#### Acceptance Criteria

1. WHEN pengguna mengakses halaman publik, THE Routing System SHALL menggunakan path berbahasa Indonesia untuk semua URL publik (contoh: /produk, /tentang-kami)
2. WHEN pengguna mengakses halaman admin, THE Routing System SHALL menggunakan path berbahasa Indonesia untuk semua URL admin (contoh: /admin/dasbor, /admin/kehadiran)
3. THE Routing System SHALL mempertahankan prefix "/admin" untuk membedakan area admin dari area publik
4. WHEN pengguna mengakses URL lama berbahasa Inggris, THE Routing System SHALL mengarahkan ke URL baru berbahasa Indonesia melalui redirect
5. THE Routing System SHALL menggunakan nama route berbahasa Indonesia untuk semua route internal

### Requirement 2

**User Story:** Sebagai developer, saya ingin semua referensi route di kode diperbarui, sehingga tidak ada broken link atau error routing.

#### Acceptance Criteria

1. THE Routing System SHALL memperbarui semua helper route() di Blade views dengan nama route baru
2. THE Routing System SHALL memperbarui semua redirect() di Livewire components dengan nama route baru
3. THE Routing System SHALL memperbarui semua route() di Livewire components dengan nama route baru
4. THE Routing System SHALL memperbarui semua link navigasi di layout dan menu dengan route baru
5. WHEN sistem melakukan redirect atau generate URL, THE Routing System SHALL menggunakan nama route berbahasa Indonesia

### Requirement 3

**User Story:** Sebagai pengguna, saya ingin URL yang konsisten dan mudah dibaca, sehingga navigasi lebih intuitif.

#### Acceptance Criteria

1. THE Routing System SHALL menggunakan kebab-case untuk semua URL path (contoh: /jadwal-saya, bukan /jadwalSaya)
2. THE Routing System SHALL menggunakan kata yang umum digunakan dalam bahasa Indonesia
3. THE Routing System SHALL menghindari singkatan yang tidak jelas
4. THE Routing System SHALL mempertahankan struktur hierarki URL yang logis (contoh: /admin/jadwal/buat)
5. WHEN URL memiliki parameter dinamis, THE Routing System SHALL mempertahankan format parameter (contoh: /{schedule}/edit menjadi /{schedule}/ubah)

## URL Mapping Reference

### Public Routes
- `/` → `/` (tetap)
- `/products` → `/produk`
- `/products/{slug}` → `/produk/{slug}`
- `/about` → `/tentang-kami`

### Admin Routes
- `/admin/dashboard` → `/admin/dasbor`
- `/admin/attendance` → `/admin/kehadiran`
- `/admin/attendance/check-in-out` → `/admin/kehadiran/absen`
- `/admin/attendance/history` → `/admin/kehadiran/riwayat`
- `/admin/schedule` → `/admin/jadwal`
- `/admin/schedule/create` → `/admin/jadwal/buat`
- `/admin/schedule/my-schedule` → `/admin/jadwal/jadwal-saya`
- `/admin/schedule/availability` → `/admin/jadwal/ketersediaan`
- `/admin/schedule/calendar` → `/admin/jadwal/kalender`
- `/admin/schedule/generator` → `/admin/jadwal/generator`
- `/admin/schedule/{schedule}/edit` → `/admin/jadwal/{schedule}/ubah`
- `/admin/schedule/{schedule}/history` → `/admin/jadwal/{schedule}/riwayat`
- `/admin/cashier` → `/admin/kasir`
- `/admin/cashier/pos` → `/admin/kasir/pos`
- `/admin/cashier/sales` → `/admin/kasir/penjualan`
- `/admin/products` → `/admin/produk`
- `/admin/products/list` → `/admin/produk/daftar`
- `/admin/products/create` → `/admin/produk/buat`
- `/admin/products/{product}/edit` → `/admin/produk/{product}/ubah`
- `/admin/stock` → `/admin/stok`
- `/admin/stock/adjustment` → `/admin/stok/penyesuaian`
- `/admin/purchase` → `/admin/pembelian`
- `/admin/purchase/list` → `/admin/pembelian/daftar`
- `/admin/leave` → `/admin/cuti`
- `/admin/leave/my-requests` → `/admin/cuti/permintaan-saya`
- `/admin/leave/create` → `/admin/cuti/buat`
- `/admin/leave/approvals` → `/admin/cuti/persetujuan`
- `/admin/swap` → `/admin/tukar-jadwal`
- `/admin/swap/my-requests` → `/admin/tukar-jadwal/permintaan-saya`
- `/admin/swap/create` → `/admin/tukar-jadwal/buat`
- `/admin/swap/approvals` → `/admin/tukar-jadwal/persetujuan`
- `/admin/penalties` → `/admin/sanksi`
- `/admin/penalties/my-penalties` → `/admin/sanksi/sanksi-saya`
- `/admin/penalties/manage` → `/admin/sanksi/kelola`
- `/admin/reports` → `/admin/laporan`
- `/admin/reports/attendance` → `/admin/laporan/kehadiran`
- `/admin/reports/sales` → `/admin/laporan/penjualan`
- `/admin/reports/penalties` → `/admin/laporan/sanksi`
- `/admin/users` → `/admin/pengguna`
- `/admin/users/management` → `/admin/pengguna/kelola`
- `/admin/roles` → `/admin/peran`
- `/admin/settings` → `/admin/pengaturan`
- `/admin/settings/general` → `/admin/pengaturan/umum`
- `/admin/settings/system` → `/admin/pengaturan/sistem`
- `/admin/settings/store` → `/admin/pengaturan/toko`
- `/admin/profile` → `/admin/profil`
- `/admin/profile/edit` → `/admin/profil/ubah`
- `/admin/notifications` → `/admin/notifikasi`
- `/admin/notifications/my-notifications` → `/admin/notifikasi/notifikasi-saya`

### Route Names
- `home` → `beranda`
- `public.products` → `publik.produk`
- `public.products.show` → `publik.produk.detail`
- `public.about` → `publik.tentang-kami`
- `admin.dashboard` → `admin.dasbor`
- `admin.attendance.*` → `admin.kehadiran.*`
- `admin.schedule.*` → `admin.jadwal.*`
- `admin.cashier.*` → `admin.kasir.*`
- `admin.products.*` → `admin.produk.*`
- `admin.stock.*` → `admin.stok.*`
- `admin.purchase.*` → `admin.pembelian.*`
- `admin.leave.*` → `admin.cuti.*`
- `admin.swap.*` → `admin.tukar-jadwal.*`
- `admin.penalties.*` → `admin.sanksi.*`
- `admin.reports.*` → `admin.laporan.*`
- `admin.users.*` → `admin.pengguna.*`
- `admin.roles.*` → `admin.peran.*`
- `admin.settings.*` → `admin.pengaturan.*`
- `admin.profile.*` → `admin.profil.*`
- `admin.notifications.*` → `admin.notifikasi.*`
