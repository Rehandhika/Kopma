<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicApi\HomeController as PublicHomeApiController;
use App\Http\Controllers\FileDownloadController;
use App\Livewire\Dashboard\Index as DashboardIndex;

/*
|--------------------------------------------------------------------------
| File Download Routes (Signed URLs for Private Files)
|--------------------------------------------------------------------------
*/

Route::middleware(['signed'])->group(function () {
    Route::get('/berkas/unduh/{path}/{disk?}', [FileDownloadController::class, 'download'])
        ->name('file.download')
        ->where('path', '.*');
    
    Route::get('/berkas/lihat/{path}/{disk?}', [FileDownloadController::class, 'view'])
        ->name('file.view')
        ->where('path', '.*');
});

// Authenticated file access (for private files that require login)
Route::middleware(['auth', 'signed'])->group(function () {
    Route::get('/berkas/aman/{path}/{disk?}', [FileDownloadController::class, 'download'])
        ->name('file.secure.download')
        ->where('path', '.*');
});

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Public Catalog (Home)
Route::get('/', [PublicPageController::class, 'home'])->name('home');

// Public Products
Route::get('/produk', [PublicPageController::class, 'home'])->name('public.products');

// Public Product Detail
Route::get('/produk/{slug}', [PublicPageController::class, 'product'])->name('public.products.show');

// Public About page
Route::get('/tentang', [PublicPageController::class, 'about'])->name('public.about');

// Public JSON API (for React public pages)
Route::prefix('api/publik')
    ->middleware('throttle-api')
    ->name('api.public.')
    ->group(function () {
        Route::get('/tentang', [PublicHomeApiController::class, 'about'])->name('about');
        Route::get('/banner', [PublicHomeApiController::class, 'banners'])->name('banners');
        Route::get('/kategori', [PublicHomeApiController::class, 'categories'])->name('categories');
        Route::get('/produk', [PublicHomeApiController::class, 'products'])->name('products');
        Route::get('/produk/{slug}', [PublicHomeApiController::class, 'product'])->name('products.show');
        Route::get('/status-toko', [PublicHomeApiController::class, 'storeStatus'])->name('store-status');
        Route::get('/pengaturan-waktu', [PublicHomeApiController::class, 'dateTimeSettings'])->name('datetime-settings');
    });

// Temporary test route for public layout
Route::get('/public-test', function () {
    return view('public.test-layout');
})->name('public.test');

// Component Demo Routes (for testing - remove in production)
Route::get('/demo/button', function () {
    return view('components.ui.button-demo');
})->name('demo.button');

Route::get('/demo/input', function () {
    return view('components.ui.input-demo');
})->name('demo.input');

Route::get('/demo/input-livewire', \App\Livewire\TestInputComponent::class)->name('demo.input-livewire');

Route::get('/demo/form-components', function () {
    return view('components.ui.form-components-demo');
})->name('demo.form-components');

Route::get('/demo/form-validation', \App\Livewire\TestFormComponents::class)->name('demo.form-validation');

Route::get('/demo/card', function () {
    return view('components.ui.card-demo');
})->name('demo.card');

Route::get('/demo/badge', function () {
    return view('components.ui.badge-demo');
})->name('demo.badge');

Route::get('/demo/alert', function () {
    return view('components.ui.alert-demo');
})->name('demo.alert');

Route::get('/demo/modal', function () {
    return view('components.ui.modal-test');
})->name('demo.modal');

Route::get('/demo/modal-debug', function () {
    return view('modal-debug');
})->name('demo.modal.debug');

Route::get('/demo/modal-example', function () {
    return view('components.ui.modal-example');
})->name('demo.modal-example');

Route::get('/demo/feedback', function () {
    return view('components.ui.feedback-components-test');
})->name('demo.feedback');

Route::get('/demo/dropdown', function () {
    return view('components.ui.dropdown-test');
})->name('demo.dropdown');

Route::get('/demo/page-header', function () {
    return view('components.layout.page-header-test');
})->name('demo.page-header');

Route::get('/demo/stat-card', function () {
    return view('components.layout.stat-card-test');
})->name('demo.stat-card');

Route::get('/demo/empty-state', function () {
    return view('components.layout.empty-state-test');
})->name('demo.empty-state');

Route::get('/demo/table', function () {
    return view('components.data.table-test');
})->name('demo.table');

Route::get('/demo/breadcrumb', function () {
    return view('components.data.breadcrumb-test');
})->name('demo.breadcrumb');

// Alpine.js Test Page
Route::get('/alpine-test', function () {
    return view('alpine-test');
})->name('alpine.test');

/*
|--------------------------------------------------------------------------
| Guest Routes (Login)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/admin/masuk', \App\Livewire\Auth\LoginForm::class)->name('login');
});

/*
|--------------------------------------------------------------------------
| Backward Compatibility Redirects
|--------------------------------------------------------------------------
*/

Route::redirect('/login', '/admin/masuk');
Route::redirect('/admin/login', '/admin/masuk');
Route::redirect('/dashboard', '/admin/beranda');
Route::redirect('/attendance', '/admin/absensi');
Route::redirect('/schedule', '/admin/jadwal');
Route::redirect('/cashier', '/admin/kasir');
Route::redirect('/stock', '/admin/stok');
Route::redirect('/purchase', '/admin/pembelian');
Route::redirect('/leave', '/admin/cuti');
Route::redirect('/swap', '/admin/tukar-jadwal');
Route::redirect('/penalties', '/admin/penalti');
Route::redirect('/reports', '/admin/laporan');
Route::redirect('/users', '/admin/pengguna');
Route::redirect('/roles', '/admin/peran');
Route::redirect('/settings', '/admin/pengaturan');
Route::redirect('/profile', '/admin/profil');
Route::redirect('/notifications', '/admin/notifikasi');

// Old English routes redirect to new Indonesian routes
Route::redirect('/products', '/produk');
Route::redirect('/about', '/tentang');

/*
|--------------------------------------------------------------------------
| Admin Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {
        // Dashboard (Beranda)
        Route::get('/beranda', DashboardIndex::class)->name('dashboard');
        
        // Logout (Keluar)
        Route::post('/keluar', [LogoutController::class, 'logout'])->name('logout');
        
        // Attendance (Absensi)
        Route::prefix('absensi')->name('attendance.')->group(function () {
            Route::get('/masuk-keluar', \App\Livewire\Attendance\CheckInOut::class)->name('check-in-out');
            Route::get('/', \App\Livewire\Admin\AttendanceManagement::class)->name('index');
            Route::get('/riwayat', \App\Livewire\Attendance\History::class)->name('history');
        });
        
        // Schedule (Jadwal)
        Route::prefix('jadwal')->name('schedule.')->group(function () {
            Route::get('/', \App\Livewire\Schedule\Index::class)->name('index');
            Route::get('/buat', \App\Livewire\Schedule\CreateSchedule::class)->name('create');
            Route::get('/jadwal-saya', \App\Livewire\Schedule\MySchedule::class)->name('my-schedule');
            Route::get('/ketersediaan', \App\Livewire\Schedule\AvailabilityManager::class)->name('availability');
            Route::get('/test-ketersediaan', \App\Livewire\Schedule\TestAvailability::class)->name('test-availability');
            Route::get('/kalender', \App\Livewire\Schedule\ScheduleCalendar::class)->name('calendar');
            Route::get('/generator', \App\Livewire\Schedule\ScheduleGenerator::class)->name('generator');
            Route::get('/{schedule}/ubah', \App\Livewire\Schedule\EditSchedule::class)->name('edit');
            Route::get('/{schedule}/riwayat', \App\Livewire\Schedule\EditHistory::class)->name('history');
        });
        
        // Cashier / POS (Kasir)
        Route::prefix('kasir')->name('cashier.')->group(function () {
            Route::get('/pos', \App\Livewire\Cashier\Pos::class)->name('pos');
            
            // POS Entry - restricted to admin roles only (Requirements 10.1, 10.2)
            Route::get('/entri-pos', \App\Livewire\Cashier\PosEntry::class)
                ->middleware('role:Super Admin|Ketua|Wakil Ketua')
                ->name('pos-entry');
        });
        
        // Products (Produk)
        Route::prefix('produk')->name('products.')->group(function () {
            Route::get('/', \App\Livewire\Product\Index::class)->name('index');
            Route::get('/daftar', \App\Livewire\Product\ProductList::class)->name('list');
            Route::get('/buat', \App\Livewire\Product\CreateProduct::class)->name('create');
            Route::get('/{product}/ubah', \App\Livewire\Product\EditProduct::class)->name('edit');
        });
        
        // Stock (Stok) - Unified single page management
        Route::prefix('stok')->name('stock.')->group(function () {
            Route::get('/', \App\Livewire\Stock\StockManager::class)->name('index');
            // Legacy routes redirect to new unified page
            Route::redirect('/penyesuaian', '/admin/stok?activeTab=history')->name('adjustment');
        });
        
        // Purchase (Pembelian)
        Route::prefix('pembelian')->name('purchase.')->group(function () {
            Route::get('/', \App\Livewire\Purchase\Index::class)->name('index');
            Route::get('/daftar', \App\Livewire\Purchase\PurchaseList::class)->name('list');
        });
        
        // Leave Requests (Cuti) - Redesigned Single Page
        Route::prefix('cuti')->name('leave.')->group(function () {
            Route::get('/', \App\Livewire\Leave\LeaveManager::class)->name('index');
            // Legacy routes redirect to new unified page
            Route::redirect('/permintaan-saya', '/admin/cuti')->name('my-requests');
            Route::redirect('/buat', '/admin/cuti')->name('create');
            Route::redirect('/persetujuan', '/admin/cuti?tab=approvals')->name('approvals');
        });
        
        // Schedule Change Requests (Tukar Jadwal) - Unified Single Page
        Route::prefix('tukar-jadwal')->name('swap.')->group(function () {
            Route::get('/', \App\Livewire\Schedule\ScheduleChangeManager::class)->name('index');
            // Legacy routes redirect
            Route::redirect('/permintaan-saya', '/admin/tukar-jadwal')->name('my-requests');
            Route::redirect('/buat', '/admin/tukar-jadwal')->name('create');
            Route::redirect('/persetujuan', '/admin/tukar-jadwal?tab=admin')->name('approvals');
        });
        
        // Penalties (Penalti)
        Route::prefix('penalti')->name('penalties.')->group(function () {
            Route::get('/', \App\Livewire\Penalty\Index::class)->name('index');
            Route::get('/penalti-saya', \App\Livewire\Penalty\MyPenalties::class)->name('my-penalties');
            Route::get('/kelola', \App\Livewire\Penalty\ManagePenalties::class)->name('manage');
        });
        
        // Reports (Laporan)
        Route::prefix('laporan')->name('reports.')->group(function () {
            Route::get('/absensi', \App\Livewire\Report\AttendanceReport::class)->name('attendance');
            Route::get('/penjualan', \App\Livewire\Report\SalesReport::class)->name('sales');
            Route::get('/penalti', \App\Livewire\Report\PenaltyReport::class)->name('penalties');
        });
        

        // Users Management (Pengguna)
        Route::prefix('pengguna')->name('users.')->group(function () {
            Route::get('/', \App\Livewire\User\Index::class)->name('index');
            Route::get('/manajemen', \App\Livewire\User\UserManagement::class)->name('management');
        });
        
        // Roles & Permissions (Peran)
        Route::prefix('peran')->name('roles.')->group(function () {
            Route::get('/', \App\Livewire\Role\Index::class)->name('index');
        });
        
        // Settings (Pengaturan)
        Route::prefix('pengaturan')->name('settings.')->group(function () {
            Route::get('/sistem', \App\Livewire\Settings\SystemSettings::class)->name('system');
            Route::get('/toko', \App\Livewire\Admin\Settings\StoreSettings::class)
                ->middleware('role:Super Admin|Ketua|Wakil Ketua')
                ->name('store');
            Route::get('/banner', \App\Livewire\Admin\BannerManagement::class)
                ->middleware('role:Super Admin|Ketua')
                ->name('banners');
        });
        
        // Profile (Profil)
        Route::prefix('profil')->name('profile.')->group(function () {
            Route::get('/ubah', \App\Livewire\Profile\Edit::class)->name('edit');
        });
        
        // Notifications (Notifikasi)
        Route::prefix('notifikasi')->name('notifications.')->group(function () {
            Route::get('/', \App\Livewire\Notification\Index::class)->name('index');
            Route::get('/notifikasi-saya', \App\Livewire\Notification\MyNotifications::class)->name('my-notifications');
        });
    });
