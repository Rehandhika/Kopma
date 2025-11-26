# Design Document - Indonesian Routing System

## Overview

Dokumen ini menjelaskan desain teknis untuk mengubah seluruh sistem routing aplikasi dari bahasa Inggris ke bahasa Indonesia. Perubahan ini mencakup URL paths, route names, dan semua referensi route di seluruh aplikasi tanpa backward compatibility.

## Architecture

### High-Level Approach

1. **Route Definition Layer**: Update file `routes/web.php` dengan URL dan route names berbahasa Indonesia
2. **Component Layer**: Update semua Livewire components yang menggunakan route() atau redirect()
3. **View Layer**: Update semua Blade views yang menggunakan route() helper
4. **Navigation Layer**: Update navigation component yang berisi menu links

### Affected Components

```
routes/
  └── web.php (PRIMARY - route definitions)

app/Livewire/
  ├── Auth/LoginForm.php
  ├── Schedule/CreateSchedule.php
  ├── Product/CreateProduct.php
  ├── Product/EditProduct.php
  └── Leave/CreateRequest.php
  └── [other components with route references]

resources/views/
  └── components/
      └── navigation.blade.php (CRITICAL - main navigation)
  └── livewire/
      └── [all blade files with route() calls]
```

## Components and Interfaces

### 1. Route Definition Component (routes/web.php)

**Responsibility**: Mendefinisikan semua URL paths dan route names

**Changes Required**:
- Update semua URL prefix dari bahasa Inggris ke Indonesia
- Update semua route names dari bahasa Inggris ke Indonesia
- Hapus backward compatibility redirects (tidak diperlukan)
- Pertahankan struktur middleware dan grouping

**Pattern**:
```php
// BEFORE
Route::get('/admin/dashboard', DashboardIndex::class)->name('admin.dashboard');
Route::prefix('schedule')->name('schedule.')->group(function () {
    Route::get('/', Index::class)->name('index');
    Route::get('/create', CreateSchedule::class)->name('create');
});

// AFTER
Route::get('/admin/dasbor', DashboardIndex::class)->name('admin.dasbor');
Route::prefix('jadwal')->name('jadwal.')->group(function () {
    Route::get('/', Index::class)->name('index');
    Route::get('/buat', CreateSchedule::class)->name('buat');
});
```

### 2. Navigation Component

**File**: `resources/views/components/navigation.blade.php`

**Responsibility**: Render menu navigasi dengan links ke semua halaman

**Changes Required**:
- Update semua `route('admin.xxx')` calls dengan route names baru
- Update semua `request()->routeIs('admin.xxx')` checks dengan route names baru
- Tidak perlu update text label (sudah bahasa Indonesia)

**Pattern**:
```php
// BEFORE
<a href="{{ route('admin.dashboard') }}" 
   class="{{ request()->routeIs('admin.dashboard') ? $linkActiveClasses : $linkInactiveClasses }}">

// AFTER
<a href="{{ route('admin.dasbor') }}" 
   class="{{ request()->routeIs('admin.dasbor') ? $linkActiveClasses : $linkInactiveClasses }}">
```

### 3. Livewire Components

**Affected Files**: Components yang menggunakan redirect() atau route()

**Changes Required**:
- Update `route('xxx')` calls dengan route names baru
- Update `redirect()->route('xxx')` dengan route names baru
- Update `$this->redirect(route('xxx'))` dengan route names baru

**Pattern**:
```php
// BEFORE
return $this->redirect(route('admin.schedule.index'), navigate: true);
return redirect()->route('products.index');
return redirect()->intended(route('admin.dashboard'));

// AFTER
return $this->redirect(route('admin.jadwal.index'), navigate: true);
return redirect()->route('produk.index');
return redirect()->intended(route('admin.dasbor'));
```

### 4. Blade Views

**Affected Files**: Semua blade files yang menggunakan route() helper

**Changes Required**:
- Search dan replace semua `route('xxx')` dengan route names baru
- Update `@if(request()->routeIs('xxx'))` dengan route names baru

## Data Models

### Route Mapping Table

Mapping lengkap dari route lama ke route baru:

#### Public Routes

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `home` | `beranda` | `/` | `/` |
| `public.products` | `publik.produk` | `/products` | `/produk` |
| `public.products.show` | `publik.produk.detail` | `/products/{slug}` | `/produk/{slug}` |
| `public.about` | `publik.tentang-kami` | `/about` | `/tentang-kami` |

#### Admin Routes - Main

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `login` | `masuk` | `/admin/login` | `/admin/masuk` |
| `admin.dashboard` | `admin.dasbor` | `/admin/dashboard` | `/admin/dasbor` |
| `admin.logout` | `admin.keluar` | `/admin/logout` | `/admin/keluar` |

#### Admin Routes - Attendance (Kehadiran)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.attendance.check-in-out` | `admin.kehadiran.absen` | `/admin/attendance/check-in-out` | `/admin/kehadiran/absen` |
| `admin.attendance.index` | `admin.kehadiran.index` | `/admin/attendance` | `/admin/kehadiran` |
| `admin.attendance.history` | `admin.kehadiran.riwayat` | `/admin/attendance/history` | `/admin/kehadiran/riwayat` |

#### Admin Routes - Schedule (Jadwal)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.schedule.index` | `admin.jadwal.index` | `/admin/schedule` | `/admin/jadwal` |
| `admin.schedule.create` | `admin.jadwal.buat` | `/admin/schedule/create` | `/admin/jadwal/buat` |
| `admin.schedule.my-schedule` | `admin.jadwal.jadwal-saya` | `/admin/schedule/my-schedule` | `/admin/jadwal/jadwal-saya` |
| `admin.schedule.availability` | `admin.jadwal.ketersediaan` | `/admin/schedule/availability` | `/admin/jadwal/ketersediaan` |
| `admin.schedule.test-availability` | `admin.jadwal.tes-ketersediaan` | `/admin/schedule/test-availability` | `/admin/jadwal/tes-ketersediaan` |
| `admin.schedule.calendar` | `admin.jadwal.kalender` | `/admin/schedule/calendar` | `/admin/jadwal/kalender` |
| `admin.schedule.generator` | `admin.jadwal.generator` | `/admin/schedule/generator` | `/admin/jadwal/generator` |
| `admin.schedule.edit` | `admin.jadwal.ubah` | `/admin/schedule/{schedule}/edit` | `/admin/jadwal/{schedule}/ubah` |
| `admin.schedule.history` | `admin.jadwal.riwayat` | `/admin/schedule/{schedule}/history` | `/admin/jadwal/{schedule}/riwayat` |

#### Admin Routes - Cashier (Kasir)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.cashier.pos` | `admin.kasir.pos` | `/admin/cashier/pos` | `/admin/kasir/pos` |
| `admin.cashier.sales` | `admin.kasir.penjualan` | `/admin/cashier/sales` | `/admin/kasir/penjualan` |

#### Admin Routes - Products (Produk)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.products.index` | `admin.produk.index` | `/admin/products` | `/admin/produk` |
| `admin.products.list` | `admin.produk.daftar` | `/admin/products/list` | `/admin/produk/daftar` |
| `admin.products.create` | `admin.produk.buat` | `/admin/products/create` | `/admin/produk/buat` |
| `admin.products.edit` | `admin.produk.ubah` | `/admin/products/{product}/edit` | `/admin/produk/{product}/ubah` |

#### Admin Routes - Stock (Stok)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.stock.index` | `admin.stok.index` | `/admin/stock` | `/admin/stok` |
| `admin.stock.adjustment` | `admin.stok.penyesuaian` | `/admin/stock/adjustment` | `/admin/stok/penyesuaian` |

#### Admin Routes - Purchase (Pembelian)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.purchase.index` | `admin.pembelian.index` | `/admin/purchase` | `/admin/pembelian` |
| `admin.purchase.list` | `admin.pembelian.daftar` | `/admin/purchase/list` | `/admin/pembelian/daftar` |

#### Admin Routes - Leave (Cuti)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.leave.index` | `admin.cuti.index` | `/admin/leave` | `/admin/cuti` |
| `admin.leave.my-requests` | `admin.cuti.permintaan-saya` | `/admin/leave/my-requests` | `/admin/cuti/permintaan-saya` |
| `admin.leave.create` | `admin.cuti.buat` | `/admin/leave/create` | `/admin/cuti/buat` |
| `admin.leave.approvals` | `admin.cuti.persetujuan` | `/admin/leave/approvals` | `/admin/cuti/persetujuan` |

#### Admin Routes - Swap (Tukar Jadwal)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.swap.index` | `admin.tukar-jadwal.index` | `/admin/swap` | `/admin/tukar-jadwal` |
| `admin.swap.my-requests` | `admin.tukar-jadwal.permintaan-saya` | `/admin/swap/my-requests` | `/admin/tukar-jadwal/permintaan-saya` |
| `admin.swap.create` | `admin.tukar-jadwal.buat` | `/admin/swap/create` | `/admin/tukar-jadwal/buat` |
| `admin.swap.approvals` | `admin.tukar-jadwal.persetujuan` | `/admin/swap/approvals` | `/admin/tukar-jadwal/persetujuan` |

#### Admin Routes - Penalties (Sanksi)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.penalties.index` | `admin.sanksi.index` | `/admin/penalties` | `/admin/sanksi` |
| `admin.penalties.my-penalties` | `admin.sanksi.sanksi-saya` | `/admin/penalties/my-penalties` | `/admin/sanksi/sanksi-saya` |
| `admin.penalties.manage` | `admin.sanksi.kelola` | `/admin/penalties/manage` | `/admin/sanksi/kelola` |

#### Admin Routes - Reports (Laporan)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.reports.attendance` | `admin.laporan.kehadiran` | `/admin/reports/attendance` | `/admin/laporan/kehadiran` |
| `admin.reports.sales` | `admin.laporan.penjualan` | `/admin/reports/sales` | `/admin/laporan/penjualan` |
| `admin.reports.penalties` | `admin.laporan.sanksi` | `/admin/reports/penalties` | `/admin/laporan/sanksi` |

#### Admin Routes - Users (Pengguna)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.users.index` | `admin.pengguna.index` | `/admin/users` | `/admin/pengguna` |
| `admin.users.management` | `admin.pengguna.kelola` | `/admin/users/management` | `/admin/pengguna/kelola` |

#### Admin Routes - Roles (Peran)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.roles.index` | `admin.peran.index` | `/admin/roles` | `/admin/peran` |

#### Admin Routes - Settings (Pengaturan)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.settings.general` | `admin.pengaturan.umum` | `/admin/settings/general` | `/admin/pengaturan/umum` |
| `admin.settings.system` | `admin.pengaturan.sistem` | `/admin/settings/system` | `/admin/pengaturan/sistem` |
| `admin.settings.store` | `admin.pengaturan.toko` | `/admin/settings/store` | `/admin/pengaturan/toko` |

#### Admin Routes - Profile (Profil)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.profile.edit` | `admin.profil.ubah` | `/admin/profile/edit` | `/admin/profil/ubah` |

#### Admin Routes - Notifications (Notifikasi)

| Old Route Name | New Route Name | Old URL | New URL |
|---------------|----------------|---------|---------|
| `admin.notifications.index` | `admin.notifikasi.index` | `/admin/notifications` | `/admin/notifikasi` |
| `admin.notifications.my-notifications` | `admin.notifikasi.notifikasi-saya` | `/admin/notifications/my-notifications` | `/admin/notifikasi/notifikasi-saya` |

## Error Handling

### Potential Issues

1. **Missing Route References**: Jika ada route() call yang terlewat
   - **Solution**: Comprehensive search menggunakan regex pattern
   - **Validation**: Test semua halaman setelah perubahan

2. **Dynamic Route Building**: Jika ada kode yang build route names secara dinamis
   - **Solution**: Manual review untuk pattern seperti `route("admin.{$module}.index")`
   - **Validation**: Search untuk string concatenation dengan 'route'

3. **JavaScript Route References**: Jika ada JS yang reference route names
   - **Solution**: Search di file .js dan .blade.php untuk route references
   - **Validation**: Test interaksi yang menggunakan AJAX/Livewire

4. **Middleware Route Checks**: Jika ada middleware yang check route names
   - **Solution**: Review semua middleware files
   - **Validation**: Test authorization flows

## Testing Strategy

### 1. Pre-Implementation Testing

- [ ] Inventory semua file yang menggunakan route() helper
- [ ] Inventory semua file yang menggunakan redirect()
- [ ] Inventory semua file yang menggunakan request()->routeIs()
- [ ] Document semua route names yang ada saat ini

### 2. Implementation Testing

- [ ] Test setiap route definition dengan `php artisan route:list`
- [ ] Verify tidak ada duplicate route names
- [ ] Verify tidak ada broken route references

### 3. Post-Implementation Testing

- [ ] Manual testing: Navigate ke setiap halaman dari menu
- [ ] Test semua form submissions (redirect setelah save)
- [ ] Test authentication flow (login redirect)
- [ ] Test authorization (middleware route checks)
- [ ] Test Livewire navigation (wire:navigate)
- [ ] Test breadcrumbs dan active menu states
- [ ] Browser testing: Check URL di address bar
- [ ] Test semua CRUD operations (create, edit, delete redirects)

### 4. Validation Commands

```bash
# List all routes untuk verify
php artisan route:list

# Search untuk old route names yang mungkin terlewat
grep -r "admin\.dashboard" app/ resources/
grep -r "admin\.schedule\." app/ resources/
grep -r "admin\.attendance\." app/ resources/

# Clear cache
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

## Implementation Notes

### Critical Files Priority

1. **HIGHEST PRIORITY**: `routes/web.php` - Core route definitions
2. **HIGH PRIORITY**: `resources/views/components/navigation.blade.php` - Main navigation
3. **MEDIUM PRIORITY**: Livewire components dengan redirect
4. **LOW PRIORITY**: Blade views dengan route() calls

### Search Patterns

Untuk menemukan semua referensi route:

```bash
# Route helper calls
grep -r "route\(" app/ resources/

# Redirect calls
grep -r "redirect\(\)" app/ resources/
grep -r "->route\(" app/ resources/

# Route checks
grep -r "routeIs\(" app/ resources/

# Specific old route names
grep -r "admin\.dashboard" app/ resources/
grep -r "admin\.schedule" app/ resources/
grep -r "admin\.attendance" app/ resources/
```

### Naming Conventions

- Gunakan kebab-case untuk URL paths: `/jadwal-saya`, `/tukar-jadwal`
- Gunakan kebab-case untuk route names: `admin.jadwal-saya`, `admin.tukar-jadwal`
- Pertahankan konsistensi dengan kata yang umum digunakan
- Hindari singkatan yang tidak jelas

## Rollback Strategy

Karena tidak ada backward compatibility, rollback harus dilakukan dengan:

1. Revert semua perubahan di Git
2. Clear cache: `php artisan route:clear && php artisan view:clear`
3. Restart server jika diperlukan

**Note**: Tidak ada partial rollback - harus all or nothing karena route names saling terkait.
