# Design Document: Public Catalog & Real-Time Store Status

## Overview

This design document outlines the technical architecture for transforming SIKOPMA from an admin-only system into a public-facing website with product catalog and real-time store status integration. The solution leverages Laravel 12, Livewire v3, Tailwind CSS v4, and Alpine.js to create a responsive, real-time experience.

### Key Design Principles

1. **Separation of Concerns**: Clear separation between public and admin functionality
2. **Real-Time Integration**: Seamless integration with existing attendance system
3. **Performance First**: Aggressive caching and query optimization
4. **Security by Default**: Proper authentication and authorization
5. **Mobile-First**: Responsive design for all screen sizes
6. **Maintainability**: Follow Laravel and Livewire best practices

### Technology Stack

- **Backend**: Laravel 12 with Eloquent ORM
- **Frontend**: Livewire v3 for reactive components
- **Styling**: Tailwind CSS v4 utility-first framework
- **Interactivity**: Alpine.js for lightweight JavaScript
- **Real-Time**: Laravel Echo with Pusher/Soketi (optional)
- **Caching**: Redis/File cache for performance
- **Database**: MySQL/PostgreSQL

## Architecture

### High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                        Public Layer                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Catalog    │  │ Store Status │  │    About     │      │
│  │   Component  │  │  Component   │  │  Component   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Service Layer                           │
│  ┌──────────────────────────────────────────────────────┐   │
│  │         StoreStatusService (Core Logic)              │   │
│  │  - Auto status calculation                           │   │
│  │  - Manual override management                        │   │
│  │  - Attendee tracking                                 │   │
│  │  - Operating hours validation                        │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                      Data Layer                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Product    │  │  Attendance  │  │StoreSetting  │      │
│  │    Model     │  │    Model     │  │    Model     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Observer Layer                            │
│  ┌──────────────────────────────────────────────────────┐   │
│  │         AttendanceObserver                           │   │
│  │  - Listen to check-in events                         │   │
│  │  - Listen to check-out events                        │   │
│  │  - Trigger status updates                            │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    Event Layer                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │         StoreStatusChanged Event                     │   │
│  │  - Broadcast to public channel                       │   │
│  │  - Update cache                                      │   │
│  │  - Log changes                                       │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### Request Flow Diagrams

#### Public Catalog Access Flow
```
User Request (/)
    ↓
Public Route (no auth)
    ↓
Catalog Livewire Component
    ↓
Query Products (cached)
    ↓
Render Public Layout
    ↓
Display with Store Status Badge
```

#### Check-In Triggers Status Update Flow
```
Staff Check-In
    ↓
Attendance::create()
    ↓
AttendanceObserver::created()
    ↓
StoreStatusService::forceUpdate()
    ↓
Validate: Operating Day? ✓
Validate: Operating Hours? ✓
Query: Active Attendances? ✓
    ↓
Update StoreSetting (is_open = true)
    ↓
Clear Cache
    ↓
Dispatch StoreStatusChanged Event
    ↓
Broadcast to 'store-status' channel
    ↓
Frontend Auto-Refresh (Livewire + Echo)
```

## Components and Interfaces

### 1. Database Schema

#### Products Table Enhancement
```sql
ALTER TABLE products ADD COLUMN:
- slug VARCHAR(255) UNIQUE NOT NULL
- image_url VARCHAR(255) NULL
- is_featured BOOLEAN DEFAULT 0
- is_public BOOLEAN DEFAULT 1
- display_order INT DEFAULT 0

CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_products_public ON products(is_public, is_featured, display_order);
```

#### Store Settings Table (New)
```sql
CREATE TABLE store_settings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Real-Time Status
    is_open BOOLEAN DEFAULT 0,
    status_reason TEXT NULL,
    last_status_change TIMESTAMP NULL,
    
    -- Mode Control
    auto_status BOOLEAN DEFAULT 1,
    manual_mode BOOLEAN DEFAULT 0,
    manual_is_open BOOLEAN DEFAULT 0,
    manual_close_reason TEXT NULL,
    manual_close_until TIMESTAMP NULL,
    manual_open_override BOOLEAN DEFAULT 0,
    manual_set_by BIGINT UNSIGNED NULL,
    manual_set_at TIMESTAMP NULL,
    
    -- Operating Configuration
    operating_hours JSON NOT NULL,
    
    -- Contact Information
    contact_phone VARCHAR(20) NULL,
    contact_email VARCHAR(100) NULL,
    contact_address TEXT NULL,
    contact_whatsapp VARCHAR(20) NULL,
    about_text TEXT NULL,
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (manual_set_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_store_status (is_open, manual_mode)
);
```

#### Operating Hours JSON Structure
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

### 2. Models

#### Product Model Enhancement
```php
// Additional properties
protected $fillable = [
    // ... existing fields
    'slug',
    'image_url',
    'is_featured',
    'is_public',
    'display_order',
];

protected $casts = [
    // ... existing casts
    'is_featured' => 'boolean',
    'is_public' => 'boolean',
    'display_order' => 'integer',
];

// New scopes
public function scopePublic($query) {
    return $query->where('is_public', true);
}

public function scopeFeatured($query) {
    return $query->where('is_featured', true);
}

public function scopeOrdered($query) {
    return $query->orderBy('display_order')->orderBy('name');
}

// Slug generation
protected static function boot() {
    parent::boot();
    
    static::creating(function ($product) {
        if (empty($product->slug)) {
            $product->slug = Str::slug($product->name);
        }
    });
}
```

#### StoreSetting Model (New)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'is_open',
        'status_reason',
        'last_status_change',
        'auto_status',
        'manual_mode',
        'manual_is_open',
        'manual_close_reason',
        'manual_close_until',
        'manual_open_override',
        'manual_set_by',
        'manual_set_at',
        'operating_hours',
        'contact_phone',
        'contact_email',
        'contact_address',
        'contact_whatsapp',
        'about_text',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'auto_status' => 'boolean',
        'manual_mode' => 'boolean',
        'manual_is_open' => 'boolean',
        'manual_open_override' => 'boolean',
        'last_status_change' => 'datetime',
        'manual_close_until' => 'datetime',
        'manual_set_at' => 'datetime',
        'operating_hours' => 'array',
    ];

    public function manualSetBy()
    {
        return $this->belongsTo(User::class, 'manual_set_by');
    }
}
```

### 3. Service Layer

#### StoreStatusService (Core Business Logic)

**Location**: `app/Services/StoreStatusService.php`

**Responsibilities**:
- Calculate store status based on attendance and operating hours
- Handle manual override modes
- Manage status transitions
- Provide status information for display
- Clear caches on status changes

**Key Methods**:
```php
public function updateStoreStatus(): void
public function getStatus(): array
public function manualClose(string $reason, ?Carbon $until): void
public function manualOpenOverride(bool $enable): void
public function toggleManualMode(bool $isOpen, ?string $reason): void
public function backToAutoMode(): void
public function forceUpdate(): void
protected function getActiveAttendances(): Collection
protected function openStore(StoreSetting $setting, string $reason): void
protected function closeStore(StoreSetting $setting, string $reason): void
protected function getStatusReason(StoreSetting $setting): string
protected function getNextOpenTime(StoreSetting $setting): ?string
```

**Status Priority Logic**:
```
1. Manual Mode (highest priority)
   - Admin has full control
   - Ignores all other conditions
   
2. Manual Close with Duration
   - Temporary close until specified time
   - Auto-resets to auto mode after expiry
   
3. Manual Close Expired
   - Reset to auto mode
   - Re-evaluate status
   
4. Manual Open Override
   - Allow opening outside normal hours/days
   - Still requires active attendance
   
5. Auto Mode (default)
   - Check operating day (Monday-Thursday only)
   - Check operating hours
   - Check active attendances
   - Update status accordingly
```

### 4. Observer Pattern

#### AttendanceObserver

**Location**: `app/Observers/AttendanceObserver.php`

**Purpose**: Real-time status updates on attendance changes

**Implementation**:
```php
namespace App\Observers;

use App\Models\Attendance;
use App\Services\StoreStatusService;
use Illuminate\Support\Facades\Log;

class AttendanceObserver
{
    public function __construct(
        protected StoreStatusService $storeStatusService
    ) {}
    
    public function created(Attendance $attendance): void
    {
        Log::info('Attendance CHECK-IN', [
            'user' => $attendance->user->name,
            'time' => $attendance->check_in,
        ]);
        
        $this->storeStatusService->forceUpdate();
    }
    
    public function updated(Attendance $attendance): void
    {
        if ($attendance->wasChanged('check_out') && $attendance->check_out) {
            Log::info('Attendance CHECK-OUT', [
                'user' => $attendance->user->name,
                'time' => $attendance->check_out,
            ]);
            
            $this->storeStatusService->forceUpdate();
        }
    }
}
```

**Registration**: `app/Providers/AppServiceProvider.php`
```php
use App\Models\Attendance;
use App\Observers\AttendanceObserver;

public function boot(): void
{
    Attendance::observe(AttendanceObserver::class);
}
```

### 5. Event Broadcasting

#### StoreStatusChanged Event

**Location**: `app/Events/StoreStatusChanged.php`

**Purpose**: Broadcast status changes to connected clients

**Implementation**:
```php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public bool $isOpen,
        public string $reason,
        public array $attendees = []
    ) {}

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

### 6. Livewire Components

#### Public Components

**Catalog Component**
- **Location**: `app/Livewire/Public/Catalog.php`
- **Purpose**: Display product catalog with search and filters
- **Features**: Search, category filter, pagination, featured products
- **Caching**: 5 minutes for product list

**StoreStatus Component**
- **Location**: `app/Livewire/Public/StoreStatus.php`
- **Purpose**: Display real-time store status badge
- **Features**: Auto-refresh, show attendees, animated indicators
- **Polling**: Every 10 seconds (with Echo as primary)

**ProductDetail Component**
- **Location**: `app/Livewire/Public/ProductDetail.php`
- **Purpose**: Display individual product details
- **Features**: Full product info, stock status, related products

**About Component**
- **Location**: `app/Livewire/Public/About.php`
- **Purpose**: Display cooperative information
- **Features**: Contact info, operating hours, about text

#### Admin Components

**StoreSettings Component**
- **Location**: `app/Livewire/Admin/Settings/StoreSettings.php`
- **Purpose**: Manage store settings and manual overrides
- **Features**: 
  - Current status display with mode indicator
  - Quick actions (close for duration, open override)
  - Manual mode toggle
  - Operating hours configuration
  - Contact information management
- **Authorization**: Super Admin, Ketua, Wakil Ketua only

### 7. Routing Architecture

#### Route Structure
```php
// Public Routes (no auth required)
Route::get('/', PublicCatalog::class)->name('home');
Route::get('/products', PublicProductList::class)->name('public.products');
Route::get('/products/{product:slug}', PublicProductDetail::class)
    ->name('public.products.show');
Route::get('/about', PublicAbout::class)->name('public.about');

// Auth Routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', LoginForm::class)->name('login');
});

// Admin Routes (auth required)
Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {
        // All existing admin routes moved here
        Route::get('/dashboard', DashboardIndex::class)->name('dashboard');
        
        // Store settings
        Route::get('/settings/store', StoreSettings::class)
            ->middleware('role:Super Admin|Ketua|Wakil Ketua')
            ->name('settings.store');
        
        // ... all other admin routes
    });

// Backward compatibility redirects
Route::redirect('/dashboard', '/admin/dashboard');
Route::redirect('/products', '/admin/products');
// ... other redirects as needed
```

## Data Models

### Product Model Attributes

**Existing**:
- id, name, sku, price, stock, min_stock, category, description, status
- timestamps, soft deletes

**New**:
- slug (string, unique, indexed)
- image_url (string, nullable)
- is_featured (boolean, default false)
- is_public (boolean, default true)
- display_order (integer, default 0)

### StoreSetting Model Attributes

**Status Fields**:
- is_open (boolean) - Current status
- status_reason (text) - Why open/closed
- last_status_change (timestamp) - Last update time

**Mode Control**:
- auto_status (boolean) - Auto mode enabled
- manual_mode (boolean) - Manual mode active
- manual_is_open (boolean) - Manual status value
- manual_close_reason (text) - Manual close reason
- manual_close_until (timestamp) - Temporary close expiry
- manual_open_override (boolean) - Override normal schedule
- manual_set_by (foreign key) - Admin who set manual
- manual_set_at (timestamp) - When manual was set

**Configuration**:
- operating_hours (JSON) - Hours per day
- contact_phone, contact_email, contact_address, contact_whatsapp
- about_text (text) - About the cooperative

## Error Handling

### Status Update Failures

**Scenario**: Observer fails to update status
**Handling**:
- Log error with full context
- Fallback to scheduled task (runs every minute)
- Alert admin if status hasn't updated in 5 minutes

**Scenario**: Database connection lost
**Handling**:
- Cache last known status
- Display cached status with warning
- Retry connection with exponential backoff

### Broadcasting Failures

**Scenario**: Echo/Pusher unavailable
**Handling**:
- Fallback to wire:poll (10 second intervals)
- No error shown to user
- Log warning for monitoring

### Cache Failures

**Scenario**: Redis/cache unavailable
**Handling**:
- Fallback to direct database queries
- Log warning
- Continue operation (slower but functional)

## Testing Strategy

### Manual Testing Focus (No Automated Tests)

**Critical Scenarios to Test**:

1. **Normal Operation (Monday-Thursday)**
   - Staff check-in → Status BUKA
   - Multiple staff → Show all names
   - Last staff check-out → Status TUTUP
   - Verify < 10 second update time

2. **Operating Day Validation**
   - Friday/Saturday/Sunday → Always TUTUP
   - Check-in on weekend → Status stays TUTUP

3. **Operating Hours Validation**
   - Before 08:00 → TUTUP
   - After 16:00 → TUTUP
   - Check-in outside hours → Status stays TUTUP

4. **Manual Override**
   - Manual close → Status TUTUP (ignore attendance)
   - Manual close with duration → Auto-reset after expiry
   - Manual open override → Can open on weekend if staff present
   - Manual mode → Full admin control

5. **Performance**
   - Page load < 2 seconds
   - Status update < 1 second after check-in/out
   - Frontend update < 10 seconds
   - Cache hit rate > 80%

6. **UI/UX**
   - Responsive on mobile, tablet, desktop
   - Status badge visible and clear
   - Attendee names display correctly
   - Toast notifications work
   - No console errors

## Performance Optimization

### Caching Strategy

**Product Catalog**:
- Cache key: `products:public:page:{page}:search:{search}:category:{category}`
- TTL: 5 minutes
- Clear on: Product create/update/delete

**Store Status**:
- Cache key: `store_status`
- TTL: 30 seconds
- Clear on: Status change, manual override

**Active Attendances**:
- No caching (must be real-time)
- Optimized query with indexes

### Database Optimization

**Indexes**:
```sql
CREATE INDEX idx_products_public ON products(is_public, is_featured, display_order);
CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_attendance_today ON attendances(check_in, check_out);
CREATE INDEX idx_attendance_user ON attendances(user_id);
CREATE INDEX idx_store_status ON store_settings(is_open, manual_mode);
```

**Query Optimization**:
- Use `exists()` instead of `count() > 0`
- Eager load relationships to prevent N+1
- Select only needed columns
- Use database-level date filtering

### Frontend Optimization

**Livewire**:
- Use `wire:poll.10s` for status updates
- Lazy load product images
- Paginate results (12 per page)
- Defer non-critical components

**Assets**:
- Vite for bundling and minification
- Tailwind CSS purging for smaller CSS
- Image optimization (WebP format)
- CDN for static assets (optional)

## Security Considerations

### Public Access

**Allowed**:
- View product catalog
- View store status
- View contact information
- View about page

**Prevented**:
- Access to admin routes
- View sensitive business data (costs, profits)
- Modify any data
- Access user information

### Admin Access

**Authentication**:
- Required for all `/admin/*` routes
- Session-based with CSRF protection
- Logout on inactivity (configurable)

**Authorization**:
- Store settings: Super Admin, Ketua, Wakil Ketua only
- Role-based access control using Spatie Permission
- Audit log for manual overrides

### Input Validation

**Product Search**:
- Sanitize search query
- Limit length (max 100 chars)
- Escape for SQL (using Eloquent)

**Manual Override**:
- Validate reason text (max 500 chars)
- Validate duration (max 7 days)
- Validate operating hours format

### Rate Limiting

**Public Routes**:
- 60 requests per minute per IP
- Throttle on search endpoint

**Admin Routes**:
- 120 requests per minute per user
- Stricter limits on settings changes

## Deployment Considerations

### Environment Configuration

**Required**:
```env
APP_URL=https://kopma.example.com
BROADCAST_DRIVER=pusher  # or log for development
CACHE_DRIVER=redis  # or file for development
QUEUE_CONNECTION=redis  # or sync for development
```

**Optional (for real-time)**:
```env
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=
```

### Migration Strategy

**Phase 1**: Database changes
- Run migrations for products table
- Run migration for store_settings table
- Seed default store settings
- Generate slugs for existing products

**Phase 2**: Code deployment
- Deploy new routes
- Deploy Livewire components
- Deploy service classes
- Register observer

**Phase 3**: Verification
- Test public catalog access
- Test status updates
- Test manual overrides
- Monitor logs for errors

**Phase 4**: Cleanup
- Remove demo routes (if any)
- Update documentation
- Train admin users

### Rollback Plan

**If issues occur**:
1. Revert code to previous version
2. Database changes are backward compatible (new columns nullable)
3. Old admin routes still work (redirects in place)
4. No data loss risk

## Monitoring and Logging

### Key Metrics

**Application**:
- Store status update latency
- Page load times
- Cache hit rates
- Error rates

**Business**:
- Public page views
- Product views
- Status change frequency
- Manual override usage

### Logging Strategy

**Status Changes**:
```php
Log::channel('store')->info('Status changed', [
    'from' => $oldStatus,
    'to' => $newStatus,
    'reason' => $reason,
    'triggered_by' => $trigger,
    'timestamp' => now(),
]);
```

**Manual Overrides**:
```php
Log::channel('store')->info('Manual override', [
    'action' => $action,
    'admin' => auth()->user()->name,
    'reason' => $reason,
    'duration' => $duration,
]);
```

**Errors**:
```php
Log::error('Status update failed', [
    'exception' => $e->getMessage(),
    'trace' => $e->getTraceAsString(),
    'context' => $context,
]);
```

### Alerts

**Critical**:
- Status hasn't updated in 5 minutes
- Database connection failures
- Broadcasting failures

**Warning**:
- Cache failures
- High error rates
- Slow query performance

## Future Enhancements

### Phase 2 Features (Optional)

1. **Advanced Search**
   - Full-text search with Meilisearch/Algolia
   - Filters: price range, stock status
   - Sort options: price, name, popularity

2. **Product Features**
   - Product categories with hierarchy
   - Product reviews/ratings
   - Wishlist functionality
   - Product comparison

3. **WhatsApp Integration**
   - Quick order via WhatsApp
   - Status notifications
   - Order confirmations

4. **Analytics**
   - Public page analytics
   - Product view tracking
   - Popular products dashboard
   - Visitor insights

5. **SEO Optimization**
   - Meta tags per product
   - Structured data (JSON-LD)
   - Sitemap generation
   - Open Graph tags

6. **Admin Enhancements**
   - Bulk product import/export
   - Product analytics
   - Status history dashboard
   - Automated reports

## Conclusion

This design provides a comprehensive, production-ready solution for transforming SIKOPMA into a public-facing website with real-time store status integration. The architecture prioritizes:

- **Real-time accuracy**: Status updates within 1 second of attendance changes
- **Reliability**: Multiple fallback mechanisms for critical features
- **Performance**: Aggressive caching and query optimization
- **Security**: Proper authentication and authorization
- **Maintainability**: Clean separation of concerns and Laravel best practices

The implementation can be completed in 10-14 hours of focused development, with manual testing ensuring critical functionality works correctly.
