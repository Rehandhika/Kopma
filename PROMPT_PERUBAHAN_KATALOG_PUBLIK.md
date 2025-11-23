# PROMPT AI: Transformasi SIKOPMA menjadi Website Koperasi Mahasiswa dengan Katalog Publik

## 📋 KONTEKS PROYEK

**Proyek**: SIKOPMA (Sistem Informasi Koperasi Mahasiswa)
**Stack**: Laravel 12, Livewire v3, Tailwind CSS v4, Alpine.js
**Tujuan**: Mengubah sistem dari admin-only menjadi website publik dengan katalog produk dan status operasional

---

## 🎯 OBJEKTIF PERUBAHAN

### Konsep Baru
Ketika user mengakses domain utama (root `/`), mereka akan melihat:
1. **Katalog Produk** - Daftar produk yang dijual koperasi
2. **Status Koperasi** - Buka/Tutup (terintegrasi dengan sistem absensi)
   - **Jika BUKA**: Tampilkan nama pengurus yang sedang berjaga
   - **Jika TUTUP**: Tampilkan alasan dan kapan buka lagi
3. **Informasi Koperasi** - Jam operasional, kontak, dll
4. **Login untuk Pengurus** - Akses ke halaman admin yang sudah ada

### Alur User
- **Pengunjung Umum**: Lihat katalog → Cek status buka/tutup → Lihat siapa yang jaga → Informasi kontak
- **Pengurus Koperasi**: Login → Akses dashboard admin (sistem yang sudah ada)

### ⚠️ CRITICAL REQUIREMENTS
- ✅ **Hari Operasional**: Koperasi **HANYA BUKA SENIN - KAMIS**
- ✅ **Status Real-Time**: Update otomatis saat check-in/check-out
- ✅ **Show Attendees**: Tampilkan nama pengurus yang sedang berjaga saat BUKA
- ✅ **Manual Override**: Admin dapat manual tutup dengan alasan
- ✅ **Best Practice**: Implementasi dengan Laravel best practice
- ⚠️ **NO TESTING**: Skip testing untuk hemat waktu
- ⚠️ **NO DOCUMENTATION**: Skip dokumentasi untuk hemat waktu

---

## 📐 ARSITEKTUR & ROUTING (BEST PRACTICE)

### Struktur Routing yang Direkomendasikan

```php
// ============================================
// PUBLIC ROUTES (Guest & Authenticated)
// ============================================
Route::get('/', PublicCatalog::class)->name('home');
Route::get('/products', PublicProductList::class)->name('public.products');
Route::get('/products/{product:slug}', PublicProductDetail::class)->name('public.products.show');
Route::get('/about', PublicAbout::class)->name('public.about');
Route::get('/contact', PublicContact::class)->name('public.contact');

// ============================================
// AUTH ROUTES (Guest Only)
// ============================================
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', LoginForm::class)->name('login');
});

// ============================================
// ADMIN ROUTES (Authenticated Only)
// ============================================
Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    
    // Semua route admin yang sudah ada dipindahkan ke sini
    // dengan prefix 'admin.' pada name
});
```

### Prinsip Best Practice

1. **Separation of Concerns**: Route publik dan admin terpisah jelas
2. **RESTful Convention**: Gunakan resource naming yang konsisten
3. **Middleware Grouping**: Group route berdasarkan authentication requirement
4. **Prefix untuk Admin**: Semua route admin menggunakan prefix `/admin`
5. **Named Routes**: Semua route memiliki name yang deskriptif
6. **Route Model Binding**: Gunakan slug untuk URL yang SEO-friendly

---

## 🗂️ STRUKTUR FILE YANG PERLU DIBUAT

### 1. Livewire Components (Public)
```
app/Livewire/Public/
├── Catalog.php                 # Halaman utama katalog
├── ProductList.php             # List produk dengan filter
├── ProductDetail.php           # Detail produk
├── StoreStatus.php             # Component status buka/tutup
└── About.php                   # Tentang koperasi
```

### 2. Views (Public)
```
resources/views/livewire/public/
├── catalog.blade.php
├── product-list.blade.php
├── product-detail.blade.php
├── store-status.blade.php
└── about.blade.php

resources/views/layouts/
└── public.blade.php            # Layout untuk halaman publik
```

### 3. Database Migrations
```
database/migrations/
├── xxxx_add_slug_to_products_table.php
├── xxxx_add_is_featured_to_products_table.php
├── xxxx_create_store_settings_table.php
└── xxxx_add_public_fields_to_products_table.php
```

### 4. Models Enhancement
```
app/Models/Product.php          # Tambah slug, featured, public visibility
app/Models/StoreSetting.php     # Setting jam operasional, status
```

---

## 💾 DATABASE SCHEMA CHANGES

### Products Table Enhancement

```sql
ALTER TABLE products ADD COLUMN:
- slug VARCHAR(255) UNIQUE          # URL-friendly identifier
- image_url VARCHAR(255)            # Product image
- is_featured BOOLEAN DEFAULT 0     # Featured products
- is_public BOOLEAN DEFAULT 1       # Show in public catalog
- display_order INT DEFAULT 0       # Sort order
```

### Store Settings Table (NEW)
```sql
CREATE TABLE store_settings (
    id BIGINT PRIMARY KEY,
    
    -- Status Fields
    is_open BOOLEAN DEFAULT 0,              # Status buka/tutup REAL-TIME
    status_reason TEXT,                     # Alasan status (auto-generated)
    last_status_change TIMESTAMP,           # Kapan terakhir status berubah
    
    -- Auto/Manual Mode
    auto_status BOOLEAN DEFAULT 1,          # TRUE = Auto (ikuti absensi), FALSE = Manual
    
    -- Manual Override Fields
    manual_mode BOOLEAN DEFAULT 0,          # Manual mode aktif
    manual_is_open BOOLEAN DEFAULT 0,       # Status manual (jika manual_mode = true)
    manual_close_reason TEXT,               # Alasan tutup manual
    manual_close_until TIMESTAMP,           # Tutup sampai kapan (auto kembali ke auto mode)
    manual_open_override BOOLEAN DEFAULT 0, # Override hari/jam operasional
    manual_set_by BIGINT,                   # User ID yang set manual
    manual_set_at TIMESTAMP,                # Kapan di-set manual
    
    -- Operating Hours
    operating_hours JSON,                   # Jam operasional per hari (SENIN-KAMIS)
    
    -- Contact Info
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    contact_address TEXT,
    contact_whatsapp VARCHAR(20),           # WhatsApp untuk quick order
    about_text TEXT,
    
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (manual_set_by) REFERENCES users(id)
);
```

### Operating Hours JSON Structure
```json
{
  "monday": {"open": "08:00", "close": "16:00", "is_open": true},
  "tuesday": {"open": "08:00", "close": "16:00", "is_open": true},
  "wednesday": {"open": "08:00", "close": "16:00", "is_open": true},
  "thursday": {"open": "08:00", "close": "16:00", "is_open": true},
  "friday": {"open": null, "close": null, "is_open": false},
  "saturday": {"open": null, "close": null, "is_open": false},
  "sunday": {"open": null, "close": null, "is_open": false}
}
```

**⚠️ PENTING**: Koperasi **HANYA BUKA SENIN - KAMIS**

---

## 🔄 INTEGRASI STATUS BUKA/TUTUP DENGAN ABSENSI (REAL-TIME & CANGGIH)

### ⚡ Konsep Real-Time Integration

**CRITICAL REQUIREMENT**: Status koperasi **HARUS** terintegrasi sempurna dengan sistem absensi secara real-time:
- ✅ **Ada yang absen (check-in)** → Status **OTOMATIS BUKA**
- ❌ **Tidak ada yang absen** → Status **OTOMATIS TUTUP**
- 🔄 **Check-out terakhir** → Status **OTOMATIS TUTUP**
- ⏰ **Real-time updates** → Tanpa delay, langsung update
- 📅 **Hari operasional** → **HANYA SENIN - KAMIS**

### Logika Status Otomatis (Advanced)

```php
<?php

namespace App\Services;

use App\Models\{Attendance, StoreSetting};
use Illuminate\Support\Facades\{Cache, Log, DB};
use Carbon\Carbon;

class StoreStatusService
{
    /**
     * Update status koperasi berdasarkan absensi REAL-TIME
     * Dipanggil setiap kali ada check-in/check-out
     */
    public function updateStoreStatus(): void
    {
        $setting = StoreSetting::firstOrCreate([]);
        
        // PRIORITY 1: Manual mode - admin punya kontrol penuh
        if ($setting->manual_mode) {
            Log::info('Store status: Manual mode active', [
                'is_open' => $setting->manual_is_open,
                'reason' => $setting->manual_close_reason,
            ]);
            
            $setting->update([
                'is_open' => $setting->manual_is_open,
                'status_reason' => $setting->manual_close_reason ?? 'Mode manual aktif',
            ]);
            return;
        }
        
        // PRIORITY 2: Manual close dengan durasi
        if ($setting->manual_close_until && now() < $setting->manual_close_until) {
            Log::info('Store status: Manual close until', [
                'until' => $setting->manual_close_until,
            ]);
            
            $this->closeStore($setting, $setting->manual_close_reason ?? 'Tutup sementara');
            return;
        }
        
        // PRIORITY 3: Manual close expired - reset ke auto mode
        if ($setting->manual_close_until && now() >= $setting->manual_close_until) {
            Log::info('Store status: Manual close expired, back to auto mode');
            
            $setting->update([
                'manual_close_until' => null,
                'manual_close_reason' => null,
            ]);
        }
        
        // PRIORITY 4: Manual open override (buka di hari/jam tidak biasa)
        if ($setting->manual_open_override) {
            Log::info('Store status: Manual open override active');
            
            // Tetap cek ada pengurus yang absen
            $activeAttendances = $this->getActiveAttendances();
            
            if ($activeAttendances->count() > 0) {
                $attendees = $activeAttendances->pluck('user.name')->join(', ');
                $this->openStore($setting, "Buka khusus - Dijaga oleh: {$attendees}");
            } else {
                $this->closeStore($setting, 'Tidak ada pengurus yang bertugas');
            }
            return;
        }
        
        // PRIORITY 5: Auto mode - cek hari operasional (HANYA SENIN - KAMIS)
        $today = now()->dayOfWeek; // 1=Monday, 4=Thursday
        if ($today < 1 || $today > 4) {
            $this->closeStore($setting, 'Koperasi hanya buka Senin - Kamis');
            return;
        }
        
        // Cek jam operasional
        $operatingHours = $setting->operating_hours[strtolower(now()->format('l'))];
        if (!$operatingHours['is_open']) {
            $this->closeStore($setting, 'Hari ini koperasi tutup');
            return;
        }
        
        $now = now()->format('H:i');
        $isWithinHours = $now >= $operatingHours['open'] 
                      && $now <= $operatingHours['close'];
        
        if (!$isWithinHours) {
            $this->closeStore($setting, 'Di luar jam operasional');
            return;
        }
        
        // CORE LOGIC: Cek apakah ada pengurus yang sedang absen (check-in tanpa check-out)
        $activeAttendances = $this->getActiveAttendances();
        
        if ($activeAttendances->count() > 0) {
            // Ada pengurus yang absen = BUKA
            $attendees = $activeAttendances->pluck('user.name')->join(', ');
            $this->openStore($setting, "Dijaga oleh: {$attendees}");
            
            Log::info('Store OPENED - Active attendances', [
                'count' => $activeAttendances->count(),
                'attendees' => $attendees,
            ]);
        } else {
            // Tidak ada yang absen = TUTUP
            $this->closeStore($setting, 'Tidak ada pengurus yang bertugas');
            
            Log::info('Store CLOSED - No active attendances');
        }
    }
    
    /**
     * Buka koperasi dengan reason
     */
    protected function openStore(StoreSetting $setting, string $reason): void
    {
        $wasOpen = $setting->is_open;
        
        $setting->update([
            'is_open' => true,
            'status_reason' => $reason,
            'last_status_change' => now(),
        ]);
        
        // Clear cache
        Cache::forget('store_status');
        
        // Broadcast event jika status berubah
        if (!$wasOpen) {
            event(new \App\Events\StoreStatusChanged(true, $reason));
            Log::info('🟢 STORE OPENED', ['reason' => $reason]);
        }
    }
    
    /**
     * Tutup koperasi dengan reason
     */
    protected function closeStore(StoreSetting $setting, string $reason): void
    {
        $wasOpen = $setting->is_open;
        
        $setting->update([
            'is_open' => false,
            'status_reason' => $reason,
            'last_status_change' => now(),
        ]);
        
        // Clear cache
        Cache::forget('store_status');
        
        // Broadcast event jika status berubah
        if ($wasOpen) {
            event(new \App\Events\StoreStatusChanged(false, $reason));
            Log::info('🔴 STORE CLOSED', ['reason' => $reason]);
        }
    }
    
    /**
     * Get active attendances (helper method)
     */
    protected function getActiveAttendances()
    {
        return Attendance::whereDate('check_in', today())
            ->whereNull('check_out') // Belum check-out = masih di koperasi
            ->whereHas('user', function($q) {
                $q->where('status', 'active')
                  ->whereHas('roles', function($r) {
                      $r->whereIn('name', ['Super Admin', 'Ketua', 'Wakil Ketua', 'BPH', 'Pengurus', 'Kasir']);
                  });
            })
            ->with('user:id,name')
            ->get();
    }
    
    /**
     * Manual close dengan durasi
     */
    public function manualClose(string $reason, ?Carbon $until = null): void
    {
        $setting = StoreSetting::first();
        
        $setting->update([
            'manual_close_until' => $until ?? now()->addHours(2), // Default 2 jam
            'manual_close_reason' => $reason,
            'manual_set_by' => auth()->id(),
            'manual_set_at' => now(),
        ]);
        
        $this->forceUpdate();
        
        Log::info('Manual close activated', [
            'reason' => $reason,
            'until' => $until,
            'by' => auth()->user()->name,
        ]);
    }
    
    /**
     * Manual open override
     */
    public function manualOpenOverride(bool $enable): void
    {
        $setting = StoreSetting::first();
        
        $setting->update([
            'manual_open_override' => $enable,
            'manual_set_by' => auth()->id(),
            'manual_set_at' => now(),
        ]);
        
        $this->forceUpdate();
        
        Log::info('Manual open override', [
            'enabled' => $enable,
            'by' => auth()->user()->name,
        ]);
    }
    
    /**
     * Toggle manual mode
     */
    public function toggleManualMode(bool $isOpen, ?string $reason = null): void
    {
        $setting = StoreSetting::first();
        
        $setting->update([
            'manual_mode' => true,
            'manual_is_open' => $isOpen,
            'manual_close_reason' => $reason,
            'manual_set_by' => auth()->id(),
            'manual_set_at' => now(),
        ]);
        
        $this->forceUpdate();
        
        Log::info('Manual mode activated', [
            'is_open' => $isOpen,
            'reason' => $reason,
            'by' => auth()->user()->name,
        ]);
    }
    
    /**
     * Back to auto mode
     */
    public function backToAutoMode(): void
    {
        $setting = StoreSetting::first();
        
        $setting->update([
            'manual_mode' => false,
            'manual_open_override' => false,
            'manual_close_until' => null,
            'manual_close_reason' => null,
        ]);
        
        $this->forceUpdate();
        
        Log::info('Back to auto mode', [
            'by' => auth()->user()->name,
        ]);
    }
    
    /**
     * Get current store status dengan detail lengkap
     */
    public function getStatus(): array
    {
        return Cache::remember('store_status', 30, function () {
            $setting = StoreSetting::first();
            
            if (!$setting) {
                return [
                    'is_open' => false,
                    'reason' => 'Sistem belum dikonfigurasi',
                    'next_open' => null,
                    'current_attendees' => [],
                ];
            }
            
            $currentAttendees = [];
            if ($setting->is_open) {
                $currentAttendees = Attendance::whereDate('check_in', today())
                    ->whereNull('check_out')
                    ->with('user:id,name')
                    ->get()
                    ->pluck('user.name')
                    ->toArray();
            }
            
            return [
                'is_open' => $setting->is_open,
                'reason' => $setting->status_reason ?? $this->getStatusReason($setting),
                'next_open' => $this->getNextOpenTime($setting),
                'current_attendees' => $currentAttendees,
                'last_updated' => $setting->last_status_change?->diffForHumans(),
            ];
        });
    }
    
    /**
     * Generate status reason
     */
    protected function getStatusReason(StoreSetting $setting): string
    {
        if (!$setting->is_open) {
            $today = now()->dayOfWeek;
            
            // Cek hari
            if ($today < 1 || $today > 4) {
                return 'Koperasi hanya buka Senin - Kamis';
            }
            
            // Cek jam
            $operatingHours = $setting->operating_hours[strtolower(now()->format('l'))];
            $now = now()->format('H:i');
            
            if ($now < $operatingHours['open']) {
                return "Belum buka. Buka pukul {$operatingHours['open']}";
            }
            
            if ($now > $operatingHours['close']) {
                return "Sudah tutup. Tutup pukul {$operatingHours['close']}";
            }
            
            return 'Tidak ada pengurus yang bertugas';
        }
        
        return 'Koperasi sedang buka';
    }
    
    /**
     * Get next open time
     */
    protected function getNextOpenTime(StoreSetting $setting): ?string
    {
        $now = now();
        $today = $now->dayOfWeek;
        
        // Jika hari Jumat-Minggu, next open adalah Senin
        if ($today >= 5) {
            $nextMonday = $now->next(Carbon::MONDAY);
            return $nextMonday->format('l, d M Y') . ' pukul 08:00';
        }
        
        // Jika Senin-Kamis tapi sudah lewat jam tutup
        $operatingHours = $setting->operating_hours[strtolower($now->format('l'))];
        if ($now->format('H:i') > $operatingHours['close']) {
            // Next open besok (jika Kamis, maka Senin)
            if ($today == 4) { // Thursday
                $nextMonday = $now->next(Carbon::MONDAY);
                return $nextMonday->format('l, d M Y') . ' pukul 08:00';
            } else {
                return $now->addDay()->format('l, d M Y') . ' pukul ' . $operatingHours['open'];
            }
        }
        
        return null;
    }
    
    /**
     * Force update status (dipanggil dari Attendance Observer)
     */
    public function forceUpdate(): void
    {
        Cache::forget('store_status');
        $this->updateStoreStatus();
    }
}
```

### Real-Time Integration dengan Attendance Observer

**PENTING**: Status harus update **LANGSUNG** saat check-in/check-out, bukan menunggu scheduled task!

```php
<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Services\StoreStatusService;
use Illuminate\Support\Facades\Log;

class AttendanceObserver
{
    protected $storeStatusService;
    
    public function __construct(StoreStatusService $storeStatusService)
    {
        $this->storeStatusService = $storeStatusService;
    }
    
    /**
     * Handle the Attendance "created" event (CHECK-IN)
     * Langsung update status koperasi
     */
    public function created(Attendance $attendance): void
    {
        Log::info('Attendance CHECK-IN detected', [
            'user' => $attendance->user->name,
            'time' => $attendance->check_in,
        ]);
        
        // REAL-TIME UPDATE: Langsung update status
        $this->storeStatusService->forceUpdate();
    }

    /**
     * Handle the Attendance "updated" event (CHECK-OUT)
     * Langsung update status koperasi
     */
    public function updated(Attendance $attendance): void
    {
        // Hanya trigger jika check_out baru diisi
        if ($attendance->wasChanged('check_out') && $attendance->check_out) {
            Log::info('Attendance CHECK-OUT detected', [
                'user' => $attendance->user->name,
                'time' => $attendance->check_out,
            ]);
            
            // REAL-TIME UPDATE: Langsung update status
            $this->storeStatusService->forceUpdate();
        }
    }
}
```

**Register Observer di AppServiceProvider:**
```php
// app/Providers/AppServiceProvider.php
use App\Models\Attendance;
use App\Observers\AttendanceObserver;

public function boot(): void
{
    Attendance::observe(AttendanceObserver::class);
}
```

### Scheduled Task (Backup/Fallback)
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Backup check setiap 1 menit (jika observer gagal)
    $schedule->call(function () {
        app(StoreStatusService::class)->updateStoreStatus();
    })->everyMinute();
    
    // Force close di luar jam operasional
    $schedule->call(function () {
        $service = app(StoreStatusService::class);
        $service->updateStoreStatus();
    })->hourly();
}
```

### Event Broadcasting untuk Real-Time UI Update

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public bool $isOpen;
    public string $reason;
    public array $attendees;

    public function __construct(bool $isOpen, string $reason, array $attendees = [])
    {
        $this->isOpen = $isOpen;
        $this->reason = $reason;
        $this->attendees = $attendees;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('store-status');
    }
    
    public function broadcastAs(): string
    {
        return 'status.changed';
    }
}
```

---

## 🎨 UI/UX DESIGN GUIDELINES

### Public Layout (resources/views/layouts/public.blade.php)

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Koperasi Mahasiswa' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">
    <!-- Public Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-blue-600">KOPMA</h1>
                </div>
                
                <!-- Store Status Badge -->
                <livewire:public.store-status />
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600">Katalog</a>
                    <a href="{{ route('public.about') }}" class="text-gray-700 hover:text-blue-600">Tentang</a>
                    <a href="{{ route('login') }}" class="btn-primary">Login Pengurus</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold mb-2">Koperasi Mahasiswa</h3>
                    <p class="text-gray-400 text-sm">Melayani kebutuhan mahasiswa</p>
                </div>
                <div>
                    <h3 class="font-bold mb-2">Kontak</h3>
                    <p class="text-gray-400 text-sm">{{ $contact->phone }}</p>
                    <p class="text-gray-400 text-sm">{{ $contact->email }}</p>
                </div>
                <div>
                    <h3 class="font-bold mb-2">Jam Operasional</h3>
                    <p class="text-gray-400 text-sm">Senin - Jumat: 08:00 - 16:00</p>
                    <p class="text-gray-400 text-sm">Sabtu: 09:00 - 14:00</p>
                </div>
            </div>
        </div>
    </footer>
    
    @livewireScripts
</body>
</html>
```

### Store Status Component (Real-Time dengan Echo)
```blade
<!-- resources/views/livewire/public/store-status.blade.php -->
<div class="flex items-center space-x-3" 
     wire:poll.10s
     x-data="{ 
         isOpen: @entangle('isOpen'),
         reason: @entangle('reason'),
         attendees: @entangle('attendees'),
         lastUpdated: @entangle('lastUpdated')
     }">
    
    <!-- Status Badge -->
    <div class="relative">
        <template x-if="isOpen">
            <div class="flex items-center space-x-2 bg-green-50 px-4 py-2 rounded-full border-2 border-green-500">
                <!-- Animated pulse dot -->
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="font-bold text-green-700 text-sm uppercase tracking-wide">BUKA</span>
            </div>
        </template>
        
        <template x-if="!isOpen">
            <div class="flex items-center space-x-2 bg-red-50 px-4 py-2 rounded-full border-2 border-red-500">
                <span class="relative flex h-3 w-3">
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                </span>
                <span class="font-bold text-red-700 text-sm uppercase tracking-wide">TUTUP</span>
            </div>
        </template>
    </div>
    
    <!-- Status Info -->
    <div class="flex flex-col">
        <span class="text-sm text-gray-700" x-text="reason"></span>
        
        <!-- Show attendees if open -->
        <template x-if="isOpen && attendees.length > 0">
            <div class="flex items-center space-x-1 text-xs text-gray-500 mt-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
                <span x-text="'Dijaga: ' + attendees.join(', ')"></span>
            </div>
        </template>
        
        <!-- Last updated -->
        <span class="text-xs text-gray-400 mt-1" x-text="'Update: ' + lastUpdated"></span>
    </div>
</div>

@script
<script>
    // Listen to real-time status changes via Laravel Echo
    Echo.channel('store-status')
        .listen('.status.changed', (e) => {
            console.log('Store status changed:', e);
            @this.refresh(); // Refresh component
            
            // Show toast notification
            if (e.isOpen) {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { 
                        type: 'success', 
                        message: '🟢 Koperasi sekarang BUKA!' 
                    }
                }));
            } else {
                window.dispatchEvent(new CustomEvent('toast', {
                    detail: { 
                        type: 'info', 
                        message: '🔴 Koperasi sekarang TUTUP' 
                    }
                }));
            }
        });
</script>
@endscript
```

**Livewire Component:**
```php
<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Services\StoreStatusService;

class StoreStatus extends Component
{
    public bool $isOpen = false;
    public string $reason = '';
    public array $attendees = [];
    public string $lastUpdated = '';
    
    protected $storeStatusService;
    
    public function boot(StoreStatusService $storeStatusService)
    {
        $this->storeStatusService = $storeStatusService;
    }
    
    public function mount()
    {
        $this->refresh();
    }
    
    public function refresh()
    {
        $status = $this->storeStatusService->getStatus();
        
        $this->isOpen = $status['is_open'];
        $this->reason = $status['reason'];
        $this->attendees = $status['current_attendees'] ?? [];
        $this->lastUpdated = $status['last_updated'] ?? 'Baru saja';
    }
    
    public function render()
    {
        return view('livewire.public.store-status');
    }
}
```

### Product Catalog Grid
```blade
<!-- resources/views/livewire/public/catalog.blade.php -->
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Katalog Produk</h1>
        <p class="text-gray-600">Temukan berbagai kebutuhan mahasiswa di sini</p>
    </div>
    
    <!-- Filters -->
    <div class="mb-8 flex flex-wrap gap-4">
        <input type="text" wire:model.live="search" placeholder="Cari produk..." 
               class="input-primary flex-1 min-w-[200px]">
        
        <select wire:model.live="category" class="input-primary">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    
    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                <img src="{{ $product->image_url ?? '/images/no-image.png' }}" 
                     alt="{{ $product->name }}"
                     class="w-full h-48 object-cover">
                
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-blue-600">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                        
                        @if($product->stock > 0)
                            <span class="text-xs text-green-600 font-medium">Tersedia</span>
                        @else
                            <span class="text-xs text-red-600 font-medium">Habis</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">Tidak ada produk ditemukan</p>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
```

---

## 🔧 IMPLEMENTATION STEPS

### Phase 1: Database & Models (2-3 jam)


1. **Create Migrations**
   ```bash
   php artisan make:migration add_public_fields_to_products_table
   php artisan make:migration create_store_settings_table
   ```

2. **Update Product Model**
   - Add slug generation (use Str::slug)
   - Add featured scope
   - Add public scope
   - Add image accessor

3. **Create StoreSetting Model**
   - Add operating hours cast
   - Add helper methods

4. **Create Seeder**
   ```bash
   php artisan make:seeder StoreSettingSeeder
   ```

### Phase 2: Services & Logic (3-4 jam) ⚡ CRITICAL

1. **Create StoreStatusService**
   ```bash
   php artisan make:service StoreStatusService
   ```

2. **Implement Real-Time Status Logic**
   - ✅ Auto status update berdasarkan absensi
   - ✅ Check-in → Auto BUKA
   - ✅ Check-out terakhir → Auto TUTUP
   - ✅ Validasi hari operasional (SENIN-KAMIS ONLY)
   - ✅ Validasi jam operasional
   - ✅ Manual override untuk admin
   - ✅ Status reason generator
   - ✅ Next open time calculator
   - ✅ Current attendees tracker

3. **Create Attendance Observer** ⚡ PENTING
   ```bash
   php artisan make:observer AttendanceObserver --model=Attendance
   ```
   - Listen to `created` event (check-in)
   - Listen to `updated` event (check-out)
   - Trigger `StoreStatusService::forceUpdate()`

4. **Register Observer**
   - Update `app/Providers/AppServiceProvider.php`
   - Register `AttendanceObserver`

5. **Create Event for Broadcasting**
   ```bash
   php artisan make:event StoreStatusChanged
   ```
   - Implement `ShouldBroadcast`
   - Broadcast ke channel `store-status`

6. **Add Scheduled Task (Backup)**
   - Update `app/Console/Kernel.php`
   - Every minute check (fallback)
   - Hourly force close check
   - Test with `php artisan schedule:work`

7. **Configure Laravel Echo (Optional tapi Recommended)**
   - Setup Pusher/Soketi untuk real-time
   - Configure broadcasting
   - Test real-time updates

### Phase 3: Public Components (3-4 jam)

1. **Create Livewire Components**
   ```bash
   php artisan make:livewire Public/Catalog
   php artisan make:livewire Public/ProductList
   php artisan make:livewire Public/ProductDetail
   php artisan make:livewire Public/StoreStatus
   php artisan make:livewire Public/About
   ```

2. **Implement Component Logic**
   - Product filtering
   - Search functionality
   - Pagination
   - Real-time status polling

3. **Create Views**
   - Follow Tailwind v4 guidelines
   - Responsive design
   - Accessibility compliant

### Phase 4: Routing Refactor (1-2 jam)

1. **Backup Current Routes**
   ```bash
   cp routes/web.php routes/web.backup.php
   ```

2. **Restructure Routes**
   - Move all admin routes to `/admin` prefix
   - Add public routes
   - Update middleware groups
   - Update route names

3. **Update Navigation**
   - Update `resources/views/components/navigation.blade.php`
   - Add admin prefix to all links
   - Test all navigation links

### Phase 5: Admin Panel Updates (1-2 jam)

1. **Add Store Settings Management**
   ```bash
   php artisan make:livewire Admin/Settings/StoreSettings
   ```

2. **Features - Manual Override System**
   - **Toggle Auto/Manual Mode**
     - Auto Mode: Status mengikuti absensi (default)
     - Manual Mode: Admin control penuh
   
   - **Manual Close Options**
     - Tutup sementara dengan alasan (contoh: "Rapat pengurus", "Stok habis")
     - Set durasi tutup (1 jam, 2 jam, sampai besok, custom)
     - Auto kembali ke mode auto setelah durasi habis
   
   - **Manual Open Options** (override hari/jam operasional)
     - Buka di hari Jumat-Minggu (special case)
     - Buka di luar jam operasional
     - Tetap cek ada pengurus yang absen
   
   - **Operating Hours Management**
     - Set jam buka/tutup per hari
     - Default: Senin-Kamis 08:00-16:00
   
   - **Contact Information**
     - Phone, email, WhatsApp, address

3. **Manual Override Logic**
   ```php
   // Jika manual mode aktif
   if ($setting->manual_mode) {
       // Admin punya kontrol penuh
       return $setting->manual_is_open;
   }
   
   // Jika manual close dengan durasi
   if ($setting->manual_close_until && now() < $setting->manual_close_until) {
       return false; // Tetap tutup sampai durasi habis
   }
   
   // Jika manual open (override)
   if ($setting->manual_open_override) {
       // Tetap cek ada pengurus yang absen
       return $hasActiveAttendance;
   }
   
   // Default: Auto mode (ikuti absensi)
   return $this->autoStatusLogic();
   ```

4. **Add to Settings Menu**
   - Update navigation
   - Add permissions (only Super Admin, Ketua, Wakil Ketua)

### Phase 6: Final Polish (30 menit - 1 jam)

1. **Quick Manual Testing**
   - Test public catalog page
   - Test status buka/tutup
   - Test check-in → auto buka
   - Test check-out → auto tutup
   - Test responsive design

2. **Performance Optimization**
   - Add caching for products
   - Optimize queries
   - Add indexes

**⚠️ SKIP**: Unit tests, feature tests, documentation (untuk hemat waktu)

---

## 📝 CHECKLIST IMPLEMENTASI

### Database
- [ ] Migration: add_public_fields_to_products_table
- [ ] Migration: create_store_settings_table
- [ ] Model: Update Product with slug, featured, public
- [ ] Model: Create StoreSetting
- [ ] Seeder: StoreSettingSeeder
- [ ] Run migrations and seeders

### Services ⚡ CRITICAL - REAL-TIME INTEGRATION
- [ ] Create StoreStatusService
- [ ] Implement real-time auto status logic
- [ ] Implement check-in → auto BUKA logic
- [ ] Implement check-out → auto TUTUP logic
- [ ] Validate hari operasional (SENIN-KAMIS only)
- [ ] Validate jam operasional
- [ ] Implement manual override
- [ ] Create AttendanceObserver
- [ ] Register AttendanceObserver
- [ ] Create StoreStatusChanged event
- [ ] Implement broadcasting
- [ ] Add scheduled task (backup)
- [ ] Test real-time status updates
- [ ] Test dengan multiple check-in/check-out
- [ ] Test di hari Jumat-Minggu (harus tutup)
- [ ] Test di luar jam operasional (harus tutup)

### Components (Public)
- [ ] Livewire: Public/Catalog
- [ ] Livewire: Public/ProductList
- [ ] Livewire: Public/ProductDetail
- [ ] Livewire: Public/StoreStatus
- [ ] Livewire: Public/About
- [ ] View: layouts/public.blade.php
- [ ] Views: All public component views

### Routing
- [ ] Backup current routes
- [ ] Create public routes
- [ ] Move admin routes to /admin prefix
- [ ] Update all route names with admin. prefix
- [ ] Update middleware groups
- [ ] Test all routes

### Admin Panel
- [ ] Livewire: Admin/Settings/StoreSettings
- [ ] Add store settings to navigation
- [ ] Add permissions for store settings
- [ ] Test admin functionality

### UI/UX
- [ ] Public navigation component
- [ ] Store status badge component
- [ ] Product card component
- [ ] Footer component
- [ ] Responsive design testing
- [ ] Accessibility testing

### Testing (SKIP untuk hemat waktu)
- [ ] ~~Feature test: Public catalog access~~ SKIP
- [ ] ~~Feature test: Store status updates~~ SKIP
- [ ] ~~Feature test: Admin access control~~ SKIP
- [x] Manual testing: Critical user flows only
- [ ] ~~Performance testing~~ SKIP

### Documentation (SKIP untuk hemat waktu)
- [ ] ~~Update README.md~~ SKIP
- [ ] ~~Document new routes~~ SKIP
- [ ] ~~Document store status logic~~ SKIP
- [ ] ~~Add deployment notes~~ SKIP

---

## 🚨 IMPORTANT CONSIDERATIONS

### Security
1. **Rate Limiting**: Add rate limiting untuk public routes
2. **CSRF Protection**: Pastikan semua form protected
3. **Input Validation**: Validate semua user input
4. **SQL Injection**: Gunakan Eloquent/Query Builder
5. **XSS Protection**: Escape output dengan Blade

### Performance
1. **Caching**: Cache product list dan store status
2. **Eager Loading**: Prevent N+1 queries
3. **Image Optimization**: Compress dan resize images
4. **CDN**: Consider CDN untuk static assets
5. **Database Indexes**: Add indexes untuk search columns

### SEO
1. **Meta Tags**: Add proper meta tags
2. **Structured Data**: Add JSON-LD for products
3. **Sitemap**: Generate sitemap.xml
4. **Robots.txt**: Configure robots.txt
5. **Canonical URLs**: Use canonical URLs

### Accessibility
1. **ARIA Labels**: Add proper ARIA labels
2. **Keyboard Navigation**: Test keyboard navigation
3. **Screen Reader**: Test with screen reader
4. **Color Contrast**: Ensure proper contrast ratios
5. **Alt Text**: Add alt text untuk images

---

## 🔄 MIGRATION STRATEGY

### Backward Compatibility


1. **Route Redirects**: Add redirects dari old routes ke new routes
   ```php
   // Redirect old admin routes to new prefixed routes
   Route::redirect('/dashboard', '/admin/dashboard');
   Route::redirect('/products', '/admin/products');
   // ... etc
   ```

2. **Gradual Rollout**: Deploy dalam tahap
   - Stage 1: Deploy public pages (read-only)
   - Stage 2: Deploy store status integration
   - Stage 3: Migrate admin routes
   - Stage 4: Remove old routes

3. **Fallback Mechanism**: Jika ada error, fallback ke old system

### Data Migration
1. **Product Images**: Upload default images untuk existing products
2. **Slugs**: Generate slugs untuk existing products
3. **Store Settings**: Initialize dengan default values

---

## 📊 MONITORING & ANALYTICS

### Metrics to Track
1. **Public Page Views**: Track katalog page views
2. **Product Views**: Track individual product views
3. **Store Status Changes**: Log status changes
4. **Admin Login**: Track admin access
5. **Performance**: Monitor page load times

### Logging
```php
// Log store status changes
Log::channel('store')->info('Store status changed', [
    'is_open' => $isOpen,
    'reason' => $reason,
    'changed_by' => auth()->user()?->name ?? 'system',
]);

// Log public access
Log::channel('public')->info('Public catalog accessed', [
    'ip' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

---

## 🎓 BEST PRACTICES SUMMARY

### Laravel Best Practices
1. ✅ Use Eloquent ORM, avoid raw queries
2. ✅ Use route model binding
3. ✅ Use form requests untuk validation
4. ✅ Use service classes untuk business logic
5. ✅ Use events dan listeners untuk decoupling
6. ✅ Use queues untuk long-running tasks
7. ✅ Use caching untuk frequently accessed data
8. ✅ Use database transactions untuk data integrity

### Livewire Best Practices
1. ✅ Use wire:poll untuk real-time updates
2. ✅ Use wire:loading untuk loading states
3. ✅ Use wire:model.live untuk reactive inputs
4. ✅ Optimize dengan lazy loading
5. ✅ Use computed properties untuk expensive operations
6. ✅ Dispatch events untuk component communication

### Tailwind CSS Best Practices
1. ✅ Use utility classes, avoid custom CSS
2. ✅ Use @apply sparingly
3. ✅ Follow mobile-first approach
4. ✅ Use consistent spacing scale
5. ✅ Use semantic color names
6. ✅ Optimize dengan PurgeCSS (already configured)

### Security Best Practices
1. ✅ Never trust user input
2. ✅ Use CSRF tokens
3. ✅ Sanitize output
4. ✅ Use prepared statements
5. ✅ Implement rate limiting
6. ✅ Use HTTPS in production
7. ✅ Keep dependencies updated

---

## 📚 REFERENCE DOCUMENTATION

### Existing Documentation
- [MASTER_DEVELOPMENT_GUIDE.md](MASTER_DEVELOPMENT_GUIDE.md) - Development standards
- [README.md](README.md) - Project overview
- [FEATURE_BACKLOG.md](FEATURE_BACKLOG.md) - Feature documentation

### External Resources
- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Livewire v3 Documentation](https://livewire.laravel.com/docs)
- [Tailwind CSS v4 Documentation](https://tailwindcss.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev)

---

## 🎯 SUCCESS CRITERIA

### Functional Requirements
- ✅ Public dapat melihat katalog produk tanpa login
- ✅ Status buka/tutup update otomatis berdasarkan absensi
- ✅ Pengurus dapat login dan akses admin panel
- ✅ Admin dapat manage store settings
- ✅ Responsive di semua device sizes

### Non-Functional Requirements
- ✅ Page load time < 2 seconds
- ✅ Mobile-friendly (Google Mobile-Friendly Test)
- ✅ Accessibility score > 90 (Lighthouse)
- ✅ SEO score > 90 (Lighthouse)
- ✅ No console errors
- ✅ All tests passing

### User Experience
- ✅ Intuitive navigation
- ✅ Clear visual hierarchy
- ✅ Consistent design language
- ✅ Helpful error messages
- ✅ Fast and responsive interactions

---

## 💡 ADDITIONAL FEATURES (OPTIONAL)

### Phase 2 Enhancements
1. **Product Search dengan Algolia/Meilisearch**
2. **Product Categories dengan hierarchy**
3. **Product Reviews/Ratings**
4. **Wishlist functionality**
5. **Product comparison**
6. **Advanced filters (price range, stock status)**
7. **Product recommendations**
8. **Newsletter subscription**
9. **Social media integration**
10. **WhatsApp quick order**

### Admin Enhancements
1. **Bulk product import/export**
2. **Product analytics dashboard**
3. **Inventory alerts**
4. **Sales forecasting**
5. **Customer insights**

---

## 🚀 DEPLOYMENT CHECKLIST

### Pre-Deployment
- [ ] All tests passing
- [ ] Code review completed
- [ ] Database backup created
- [ ] Environment variables configured
- [ ] Assets compiled for production
- [ ] Cache cleared

### Deployment
- [ ] Deploy to staging first
- [ ] Test on staging
- [ ] Deploy to production
- [ ] Run migrations
- [ ] Seed store settings
- [ ] Clear production cache
- [ ] Test production

### Post-Deployment
- [ ] Monitor error logs
- [ ] Check performance metrics
- [ ] Verify all features working
- [ ] Test from different devices
- [ ] Collect user feedback

---

## 📞 SUPPORT & MAINTENANCE

### Regular Maintenance Tasks
1. **Daily**: Monitor error logs
2. **Weekly**: Review performance metrics
3. **Monthly**: Update dependencies
4. **Quarterly**: Security audit

### Troubleshooting Common Issues
1. **Store status not updating**: Check scheduled task
2. **Products not showing**: Check is_public flag
3. **Images not loading**: Check storage link
4. **Slow page load**: Check query optimization

---

## ✅ FINAL NOTES

Prompt ini dirancang untuk memberikan panduan lengkap dalam mengimplementasikan perubahan dari sistem admin-only menjadi website koperasi mahasiswa dengan katalog publik. 

**Key Points:**
- Ikuti best practices Laravel dan Livewire
- Prioritaskan security dan performance
- Test thoroughly sebelum deploy
- Document semua changes
- Maintain backward compatibility

**Estimated Total Time**: 12-18 jam development + 4-6 jam testing

Gunakan prompt ini sebagai checklist dan reference selama development. Sesuaikan dengan kebutuhan spesifik proyek Anda.

---

**Created**: {{ date('Y-m-d H:i:s') }}
**Version**: 1.0
**Author**: AI Assistant
**Project**: SIKOPMA - Sistem Informasi Koperasi Mahasiswa


---

## ⚡ REAL-TIME STATUS INTEGRATION - TECHNICAL DETAILS

### Flow Diagram: Check-In → Auto BUKA

```
User Check-In
    ↓
Attendance::create()
    ↓
AttendanceObserver::created()
    ↓
StoreStatusService::forceUpdate()
    ↓
Check: Hari Senin-Kamis? ✓
Check: Dalam jam operasional? ✓
Check: Ada yang absen? ✓ (baru saja check-in)
    ↓
StoreSetting::update(['is_open' => true])
    ↓
event(StoreStatusChanged)
    ↓
Broadcast ke channel 'store-status'
    ↓
Frontend (Livewire + Echo) auto refresh
    ↓
UI Update: Badge berubah HIJAU "BUKA"
```

### Flow Diagram: Check-Out Terakhir → Auto TUTUP

```
User Check-Out (terakhir)
    ↓
Attendance::update(['check_out' => now()])
    ↓
AttendanceObserver::updated()
    ↓
StoreStatusService::forceUpdate()
    ↓
Check: Ada yang masih absen? ✗ (semua sudah check-out)
    ↓
StoreSetting::update(['is_open' => false])
    ↓
event(StoreStatusChanged)
    ↓
Broadcast ke channel 'store-status'
    ↓
Frontend (Livewire + Echo) auto refresh
    ↓
UI Update: Badge berubah MERAH "TUTUP"
```

### Testing Scenarios (WAJIB DITEST!)

#### Scenario 1: Normal Operation (Senin-Kamis)
```
08:00 - Pengurus A check-in → Status: BUKA ✓
10:00 - Pengurus B check-in → Status: TETAP BUKA ✓
12:00 - Pengurus A check-out → Status: TETAP BUKA (B masih ada) ✓
15:00 - Pengurus B check-out → Status: TUTUP (tidak ada yang absen) ✓
```

#### Scenario 2: Hari Jumat-Minggu
```
Jumat 08:00 - Pengurus A check-in → Status: TETAP TUTUP ✗
Sabtu 10:00 - Pengurus B check-in → Status: TETAP TUTUP ✗
Minggu 12:00 - Pengurus C check-in → Status: TETAP TUTUP ✗
```

#### Scenario 3: Di Luar Jam Operasional
```
Senin 07:00 - Pengurus A check-in → Status: TUTUP (belum jam buka) ✗
Senin 17:00 - Pengurus B check-in → Status: TUTUP (sudah lewat jam tutup) ✗
```

#### Scenario 4: Manual Override
```
Admin set manual close → Status: TUTUP (ignore absensi) ✓
Pengurus check-in → Status: TETAP TUTUP (manual mode) ✓
Admin set auto mode → Status: BUKA (ada yang absen) ✓
```

### Performance Considerations

1. **Caching Strategy**
   ```php
   // Cache status selama 30 detik
   Cache::remember('store_status', 30, function() {
       return $this->getStatus();
   });
   
   // Clear cache saat status berubah
   Cache::forget('store_status');
   ```

2. **Database Optimization**
   ```sql
   -- Index untuk query cepat
   CREATE INDEX idx_attendance_today ON attendances(check_in, check_out);
   CREATE INDEX idx_attendance_user ON attendances(user_id);
   ```

3. **Query Optimization**
   ```php
   // Gunakan exists() bukan count() untuk performance
   $hasActiveAttendance = Attendance::whereDate('check_in', today())
       ->whereNull('check_out')
       ->exists(); // Lebih cepat dari count() > 0
   ```

### Monitoring & Logging

```php
// Log setiap perubahan status
Log::channel('store')->info('Status changed', [
    'from' => $oldStatus,
    'to' => $newStatus,
    'reason' => $reason,
    'triggered_by' => $triggeredBy,
    'timestamp' => now(),
]);

// Alert jika status tidak update dalam 5 menit
if ($lastStatusChange->diffInMinutes(now()) > 5) {
    Log::warning('Store status not updated for 5 minutes');
}
```

### Troubleshooting Guide

**Problem**: Status tidak update saat check-in
- ✓ Cek apakah AttendanceObserver terdaftar
- ✓ Cek log: `tail -f storage/logs/laravel.log`
- ✓ Cek hari operasional (Senin-Kamis)
- ✓ Cek jam operasional
- ✓ Cek role user (harus Pengurus/Kasir/BPH)

**Problem**: Status tidak update di frontend
- ✓ Cek wire:poll berjalan
- ✓ Cek Laravel Echo configured
- ✓ Cek broadcasting driver
- ✓ Cek browser console untuk errors

**Problem**: Status terlalu sering berubah
- ✓ Cek scheduled task tidak terlalu frequent
- ✓ Cek observer tidak trigger multiple times
- ✓ Add debouncing di service

---

## 🎯 ACCEPTANCE CRITERIA - REAL-TIME STATUS

### Must Have (Critical)
- ✅ Status BUKA otomatis saat ada yang check-in (Senin-Kamis, jam operasional)
- ✅ Status TUTUP otomatis saat semua check-out
- ✅ Status TUTUP di hari Jumat-Minggu (apapun kondisinya)
- ✅ Status TUTUP di luar jam operasional
- ✅ Real-time update tanpa refresh page (< 10 detik delay)
- ✅ Show current attendees saat status BUKA
- ✅ Show reason kenapa TUTUP
- ✅ Admin dapat manual override
- ✅ Logging semua status changes

### Should Have
- ✅ Broadcasting dengan Laravel Echo
- ✅ Toast notification saat status berubah
- ✅ Animated status badge
- ✅ Show next open time saat TUTUP
- ✅ Performance optimized (< 100ms query time)

### Nice to Have
- ⭕ Historical status log
- ⭕ Status analytics dashboard
- ⭕ Email notification ke admin saat status berubah
- ⭕ WhatsApp notification
- ⭕ Predictive status (based on schedule)

---

## 📊 METRICS & KPI

### Technical Metrics
- **Status Update Latency**: < 1 second dari check-in/out
- **Frontend Update Latency**: < 10 seconds
- **Query Performance**: < 100ms
- **Cache Hit Rate**: > 80%
- **Uptime**: 99.9%

### Business Metrics
- **Status Accuracy**: 100% (status selalu reflect kondisi real)
- **User Satisfaction**: Pengunjung tahu kapan koperasi buka
- **Operational Efficiency**: Pengurus tidak perlu manual update status

---

**CATATAN PENTING**: 
- ⚠️ Koperasi **HANYA BUKA SENIN - KAMIS**
- ⚠️ Status **HARUS** real-time, tidak boleh delay > 10 detik
- ⚠️ Integrasi dengan absensi **HARUS** sempurna
- ⚠️ Testing **WAJIB** dilakukan untuk semua scenarios



---

## 🎛️ MANUAL OVERRIDE SYSTEM - DETAILED DESIGN

### Konsep Manual Override (Best Practice)

Manual override dirancang dengan **priority system** yang jelas dan **user-friendly**:

```
Priority 1: Manual Mode (Full Control)
    ↓
Priority 2: Manual Close dengan Durasi (Temporary)
    ↓
Priority 3: Manual Close Expired → Auto Reset
    ↓
Priority 4: Manual Open Override (Special Case)
    ↓
Priority 5: Auto Mode (Default - Ikuti Absensi)
```

### Use Cases & Scenarios

#### Scenario 1: Tutup Sementara (Rapat Pengurus)
```
Admin: "Tutup sementara - Rapat pengurus"
Durasi: 2 jam
Sistem: Status TUTUP selama 2 jam
Setelah 2 jam: Auto kembali ke mode auto (cek absensi)
```

#### Scenario 2: Tutup Hari Ini (Stok Habis)
```
Admin: "Tutup hari ini - Stok habis"
Durasi: Sampai besok
Sistem: Status TUTUP sampai besok 00:00
Besok: Auto kembali ke mode auto
```

#### Scenario 3: Buka di Hari Jumat (Special Event)
```
Admin: Enable "Manual Open Override"
Sistem: Cek ada pengurus yang absen?
  - Ya → Status BUKA (show attendees)
  - Tidak → Status TETAP TUTUP
```

#### Scenario 4: Full Manual Control
```
Admin: Enable "Manual Mode"
Admin: Set status BUKA/TUTUP manual
Sistem: Ignore absensi, ignore hari operasional
Admin: Disable "Manual Mode" → Kembali ke auto
```

### Admin UI Design (Livewire Component)

```blade
<!-- resources/views/livewire/admin/settings/store-settings.blade.php -->
<div class="space-y-6">
    <!-- Current Status Display -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Status Koperasi Saat Ini</h3>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                @if($currentStatus['is_open'])
                    <span class="flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-4 w-4 rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500"></span>
                    </span>
                    <span class="text-2xl font-bold text-green-600">BUKA</span>
                @else
                    <span class="flex h-4 w-4 bg-red-500 rounded-full"></span>
                    <span class="text-2xl font-bold text-red-600">TUTUP</span>
                @endif
            </div>
            
            <div class="text-right">
                <p class="text-sm text-gray-600">{{ $currentStatus['reason'] }}</p>
                @if($currentStatus['current_attendees'])
                    <p class="text-xs text-gray-500 mt-1">
                        Dijaga: {{ implode(', ', $currentStatus['current_attendees']) }}
                    </p>
                @endif
            </div>
        </div>
        
        <!-- Mode Indicator -->
        <div class="mt-4 pt-4 border-t">
            @if($setting->manual_mode)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                    🎛️ Mode Manual Aktif
                </span>
            @elseif($setting->manual_close_until)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                    ⏰ Tutup Sementara sampai {{ $setting->manual_close_until->format('H:i') }}
                </span>
            @elseif($setting->manual_open_override)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    🔓 Override Buka Aktif
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    ✅ Mode Auto (Ikuti Absensi)
                </span>
            @endif
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Aksi Cepat</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Tutup Sementara -->
            <div class="border rounded-lg p-4">
                <h4 class="font-medium mb-2">Tutup Sementara</h4>
                <p class="text-sm text-gray-600 mb-3">Tutup koperasi untuk sementara waktu</p>
                
                <input type="text" wire:model="closeReason" placeholder="Alasan tutup..." class="input-primary mb-2">
                
                <div class="flex space-x-2 mb-3">
                    <button wire:click="closeFor(1)" class="btn-secondary text-xs">1 Jam</button>
                    <button wire:click="closeFor(2)" class="btn-secondary text-xs">2 Jam</button>
                    <button wire:click="closeFor(4)" class="btn-secondary text-xs">4 Jam</button>
                    <button wire:click="closeUntilTomorrow" class="btn-secondary text-xs">Sampai Besok</button>
                </div>
                
                <button wire:click="closeCustom" class="btn-primary w-full">
                    Tutup Sementara
                </button>
            </div>
            
            <!-- Buka Override -->
            <div class="border rounded-lg p-4">
                <h4 class="font-medium mb-2">Buka Override</h4>
                <p class="text-sm text-gray-600 mb-3">Buka di hari/jam tidak biasa</p>
                
                @if($setting->manual_open_override)
                    <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-3">
                        <p class="text-sm text-blue-800">Override aktif - Koperasi bisa buka di luar jadwal</p>
                    </div>
                    <button wire:click="disableOpenOverride" class="btn-danger w-full">
                        Nonaktifkan Override
                    </button>
                @else
                    <button wire:click="enableOpenOverride" class="btn-primary w-full">
                        Aktifkan Override Buka
                    </button>
                @endif
            </div>
            
            <!-- Manual Mode -->
            <div class="border rounded-lg p-4">
                <h4 class="font-medium mb-2">Mode Manual</h4>
                <p class="text-sm text-gray-600 mb-3">Kontrol penuh status (ignore absensi)</p>
                
                @if($setting->manual_mode)
                    <div class="space-y-2 mb-3">
                        <button wire:click="setManualStatus(true)" 
                                class="btn-success w-full {{ $setting->manual_is_open ? 'ring-2 ring-green-500' : '' }}">
                            Set BUKA
                        </button>
                        <button wire:click="setManualStatus(false)" 
                                class="btn-danger w-full {{ !$setting->manual_is_open ? 'ring-2 ring-red-500' : '' }}">
                            Set TUTUP
                        </button>
                    </div>
                    <button wire:click="disableManualMode" class="btn-secondary w-full">
                        Kembali ke Mode Auto
                    </button>
                @else
                    <button wire:click="enableManualMode" class="btn-warning w-full">
                        Aktifkan Mode Manual
                    </button>
                @endif
            </div>
            
            <!-- Reset to Auto -->
            <div class="border rounded-lg p-4">
                <h4 class="font-medium mb-2">Reset ke Auto</h4>
                <p class="text-sm text-gray-600 mb-3">Kembali ke mode otomatis (ikuti absensi)</p>
                
                <button wire:click="resetToAuto" class="btn-primary w-full">
                    🔄 Reset ke Mode Auto
                </button>
            </div>
        </div>
    </div>
    
    <!-- Operating Hours -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Jam Operasional</h3>
        
        <div class="space-y-3">
            @foreach(['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis'] as $day => $label)
                <div class="flex items-center space-x-4">
                    <span class="w-24 font-medium">{{ $label }}</span>
                    <input type="time" wire:model="operatingHours.{{ $day }}.open" class="input-primary">
                    <span>-</span>
                    <input type="time" wire:model="operatingHours.{{ $day }}.close" class="input-primary">
                </div>
            @endforeach
        </div>
        
        <button wire:click="saveOperatingHours" class="btn-primary mt-4">
            Simpan Jam Operasional
        </button>
    </div>
</div>
```

### Livewire Component Logic

```php
<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\StoreSetting;
use App\Services\StoreStatusService;

class StoreSettings extends Component
{
    public $setting;
    public $currentStatus;
    public $closeReason = '';
    public $operatingHours = [];
    
    protected $storeStatusService;
    
    public function boot(StoreStatusService $storeStatusService)
    {
        $this->storeStatusService = $storeStatusService;
    }
    
    public function mount()
    {
        $this->setting = StoreSetting::firstOrCreate([]);
        $this->operatingHours = $this->setting->operating_hours;
        $this->refreshStatus();
    }
    
    public function refreshStatus()
    {
        $this->currentStatus = $this->storeStatusService->getStatus();
    }
    
    // Quick Actions
    public function closeFor(int $hours)
    {
        $this->storeStatusService->manualClose(
            $this->closeReason ?: "Tutup sementara {$hours} jam",
            now()->addHours($hours)
        );
        
        $this->closeReason = '';
        $this->refreshStatus();
        $this->dispatch('toast', type: 'success', message: "Koperasi ditutup untuk {$hours} jam");
    }
    
    public function closeUntilTomorrow()
    {
        $this->storeStatusService->manualClose(
            $this->closeReason ?: "Tutup sampai besok",
            now()->addDay()->startOfDay()
        );
        
        $this->closeReason = '';
        $this->refreshStatus();
        $this->dispatch('toast', type: 'success', message: 'Koperasi ditutup sampai besok');
    }
    
    public function enableOpenOverride()
    {
        $this->storeStatusService->manualOpenOverride(true);
        $this->refreshStatus();
        $this->dispatch('toast', type: 'success', message: 'Override buka diaktifkan');
    }
    
    public function disableOpenOverride()
    {
        $this->storeStatusService->manualOpenOverride(false);
        $this->refreshStatus();
        $this->dispatch('toast', type: 'success', message: 'Override buka dinonaktifkan');
    }
    
    public function enableManualMode()
    {
        $this->storeStatusService->toggleManualMode(false, 'Mode manual aktif');
        $this->refreshStatus();
        $this->dispatch('toast', type: 'warning', message: 'Mode manual diaktifkan');
    }
    
    public function setManualStatus(bool $isOpen)
    {
        $this->storeStatusService->toggleManualMode(
            $isOpen, 
            $isOpen ? 'Dibuka manual oleh admin' : 'Ditutup manual oleh admin'
        );
        $this->refreshStatus();
        $this->dispatch('toast', type: 'success', message: $isOpen ? 'Status: BUKA' : 'Status: TUTUP');
    }
    
    public function disableManualMode()
    {
        $this->storeStatusService->backToAutoMode();
        $this->refreshStatus();
        $this->dispatch('toast', type: 'success', message: 'Kembali ke mode auto');
    }
    
    public function resetToAuto()
    {
        $this->storeStatusService->backToAutoMode();
        $this->refreshStatus();
        $this->dispatch('toast', type: 'success', message: 'Reset ke mode auto berhasil');
    }
    
    public function saveOperatingHours()
    {
        $this->setting->update([
            'operating_hours' => $this->operatingHours,
        ]);
        
        $this->dispatch('toast', type: 'success', message: 'Jam operasional disimpan');
    }
    
    public function render()
    {
        return view('livewire.admin.settings.store-settings')
            ->layout('layouts.app')
            ->title('Pengaturan Koperasi');
    }
}
```

---

## 📊 SUMMARY: IMPLEMENTATION TIMELINE (TANPA TESTING & DOCS)

### Total Estimasi: 10-14 jam

| Phase | Task | Estimasi | Priority |
|-------|------|----------|----------|
| 1 | Database & Models | 2-3 jam | 🔴 Critical |
| 2 | Services & Logic (Real-Time) | 3-4 jam | 🔴 Critical |
| 3 | Public Components | 2-3 jam | 🟡 High |
| 4 | Routing Refactor | 1-2 jam | 🟡 High |
| 5 | Admin Panel (Manual Override) | 2-3 jam | 🟡 High |
| 6 | Final Polish | 0.5-1 jam | 🟢 Medium |

### ⚠️ SKIPPED (Hemat Waktu):
- ❌ Unit Testing (2-3 jam saved)
- ❌ Feature Testing (2-3 jam saved)
- ❌ Documentation (1-2 jam saved)
- ❌ Performance Testing (1 jam saved)

**Total Time Saved**: 6-9 jam

---

## ✅ FINAL CHECKLIST (SIMPLIFIED)

### Must Implement
- [x] Database migrations (products + store_settings)
- [x] StoreStatusService dengan real-time logic
- [x] AttendanceObserver untuk auto update
- [x] Public catalog page
- [x] Store status component (show attendees)
- [x] Manual override system (4 modes)
- [x] Admin settings page
- [x] Routing refactor (public + admin)

### Must Test Manually
- [x] Check-in → Auto BUKA
- [x] Check-out → Auto TUTUP
- [x] Hari Jumat-Minggu → TUTUP
- [x] Manual override works
- [x] Show attendees saat BUKA

### Skip
- [ ] ~~Unit tests~~
- [ ] ~~Feature tests~~
- [ ] ~~Documentation~~
- [ ] ~~Performance tests~~

---

**READY TO IMPLEMENT!** 🚀

Gunakan prompt ini untuk implementasi dengan fokus pada:
1. ✅ Best practice Laravel & Livewire
2. ✅ Real-time integration sempurna
3. ✅ Manual override yang user-friendly
4. ✅ Show attendees saat status BUKA
5. ⚠️ Skip testing & documentation untuk hemat waktu

