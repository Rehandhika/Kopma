# Caching Usage Guide

## Overview
This guide explains how to use the caching system implemented for the schedule module.

## Cache Keys Reference

### Schedule Caches
```php
// Schedule grid (assignments grouped by slot)
"schedule_grid_{$scheduleId}"

// Conflict detection results
"schedule_conflicts_{$scheduleId}"

// Schedule statistics
"schedule_statistics_{$scheduleId}"

// Active users with availability for a schedule
"schedule_users_{$scheduleId}_{$weekStartDate}"
```

### User Availability Caches
```php
// User availability map for a specific week
"user_availability_{$userId}_{$weekStart}"
```

## Using Cached Data

### In Livewire Components

The EditSchedule component automatically uses caching:

```php
// This automatically checks cache first
$this->loadAssignments();      // Uses schedule_grid cache
$this->detectConflicts();      // Uses schedule_conflicts cache
$this->calculateStatistics();  // Uses schedule_statistics cache
```

### In Services

#### ScheduleEditService

Availability checking is automatically cached:

```php
// This method uses caching internally
$errors = $this->validateUserForSlot($schedule, $userId, $date, $session);

// The availability check is cached per user per week
```

#### EnhancedAutoAssignmentService

User loading is automatically cached:

```php
// This uses caching if enabled in configuration
$users = $this->getActiveUsersWithAvailability($schedule);
```

## Manual Cache Operations

### Invalidating Cache

#### For a Specific Schedule

```php
use App\Models\Schedule;

$schedule = Schedule::find($scheduleId);
$schedule->invalidateCache();
```

This flushes all caches related to the schedule:
- Schedule grid
- Conflicts
- Statistics
- Any other tagged caches

#### For User Availability

```php
use Illuminate\Support\Facades\Cache;

$userId = 123;
$weekStart = '2025-11-24';
$cacheKey = "user_availability_{$userId}_{$weekStart}";

Cache::forget($cacheKey);
```

#### For Schedule Users

```php
use Illuminate\Support\Facades\Cache;

$scheduleId = 456;
$weekStart = '2025-11-24';
$cacheKey = "schedule_users_{$scheduleId}_{$weekStart}";

Cache::forget($cacheKey);
```

### Checking Cache

```php
use Illuminate\Support\Facades\Cache;

// Check if cache exists
$cacheKey = "schedule_grid_{$scheduleId}";
$exists = Cache::has($cacheKey);

// Get cache value
$value = Cache::get($cacheKey);

// Get cache with default
$value = Cache::get($cacheKey, []);
```

### Manual Caching

If you need to cache custom data:

```php
use Illuminate\Support\Facades\Cache;

$scheduleId = 123;
$cacheTags = ['schedules', "schedule_{$scheduleId}"];
$cacheKey = "custom_data_{$scheduleId}";
$ttl = 3600; // 1 hour

// Store in cache
Cache::tags($cacheTags)->put($cacheKey, $data, $ttl);

// Retrieve from cache
$data = Cache::tags($cacheTags)->get($cacheKey);

// Remember pattern (get or store)
$data = Cache::tags($cacheTags)->remember($cacheKey, $ttl, function() {
    return $this->loadExpensiveData();
});
```

## Cache Invalidation Triggers

### Automatic Invalidation

Cache is automatically invalidated when:

1. **Schedule Edits**
   - Adding user to slot
   - Removing user from slot
   - Updating user in slot
   - Clearing slot
   - Bulk adding users

2. **Schedule Status Changes**
   - Publishing schedule
   - Archiving schedule

3. **Coverage Updates**
   - When `calculateCoverage()` is called

4. **Availability Updates**
   - When user saves availability
   - When user submits availability

### Manual Invalidation

You may need to manually invalidate cache when:

1. **Direct Database Updates**
   ```php
   // After direct DB update
   DB::table('schedule_assignments')->where(...)->update(...);
   
   // Invalidate cache
   $schedule->invalidateCache();
   ```

2. **Bulk Operations**
   ```php
   // After bulk operation
   ScheduleAssignment::where('schedule_id', $scheduleId)->delete();
   
   // Invalidate cache
   Cache::tags(['schedules', "schedule_{$scheduleId}"])->flush();
   ```

3. **External Updates**
   ```php
   // If data is updated from external source
   $schedule->invalidateCache();
   ```

## Configuration

### Enable/Disable Caching

```php
// In schedule_configurations table or config file
'enable_caching' => true,  // Set to false to disable
'cache_ttl' => 3600,       // Cache lifetime in seconds
```

### Check Configuration

```php
use App\Services\ScheduleConfigurationService;

$configService = app(ScheduleConfigurationService::class);

$cachingEnabled = $configService->get('enable_caching', true);
$cacheTTL = $configService->get('cache_ttl', 3600);
```

## Debugging Cache Issues

### Enable Cache Logging

Cache operations are logged at DEBUG level. To see them:

```php
// In config/logging.php, set level to 'debug'
'channels' => [
    'single' => [
        'level' => 'debug',
    ],
],
```

### Check Cache Contents

```php
use Illuminate\Support\Facades\Cache;

// Get all cache keys (if using array/file driver)
$cacheKey = "schedule_grid_{$scheduleId}";
$data = Cache::get($cacheKey);

dd($data); // Dump and die to inspect
```

### Clear All Schedule Caches

```php
use Illuminate\Support\Facades\Cache;

// Clear all schedule-related caches
Cache::tags(['schedules'])->flush();

// Or clear specific schedule
Cache::tags(["schedule_{$scheduleId}"])->flush();
```

### Disable Caching Temporarily

```php
// In your code
config(['schedule.enable_caching' => false]);

// Or via environment
// .env
SCHEDULE_CACHING_ENABLED=false
```

## Performance Tips

### 1. Cache Warming

Pre-populate cache for frequently accessed schedules:

```php
use App\Models\Schedule;

// Warm cache for current week's schedule
$schedule = Schedule::currentWeek()->first();
if ($schedule) {
    $schedule->getAssignmentGrid();      // Populates grid cache
    $schedule->detectConflicts();        // Populates conflicts cache
    $schedule->getStatistics();          // Populates statistics cache
}
```

### 2. Selective Cache Invalidation

Only invalidate what changed:

```php
// Instead of invalidating all caches
$schedule->invalidateCache();

// Invalidate specific cache
Cache::forget("schedule_grid_{$scheduleId}");
```

### 3. Cache Tags Strategy

Use cache tags for related data:

```php
// Tag all user-related caches
Cache::tags(['users', "user_{$userId}"])->put($key, $value, $ttl);

// Invalidate all user caches at once
Cache::tags(["user_{$userId}"])->flush();
```

### 4. Monitor Cache Hit Rate

```php
// Log cache hits and misses
if (Cache::has($cacheKey)) {
    Log::debug("Cache hit: {$cacheKey}");
} else {
    Log::debug("Cache miss: {$cacheKey}");
}
```

## Common Issues

### Issue: Stale Data After Edit

**Symptom:** Changes don't appear immediately

**Solution:** Ensure cache invalidation is called:
```php
// After any edit operation
$schedule->invalidateCache();
```

### Issue: Cache Not Working

**Symptom:** Always loading from database

**Solution:** Check cache driver configuration:
```php
// In .env
CACHE_DRIVER=redis  # or memcached, file, etc.

// Test cache
Cache::put('test', 'value', 60);
$value = Cache::get('test'); // Should return 'value'
```

### Issue: Memory Issues

**Symptom:** High memory usage

**Solution:** Reduce cache TTL or use Redis:
```php
// Reduce TTL
'cache_ttl' => 1800, // 30 minutes instead of 1 hour

// Or use Redis for better memory management
CACHE_DRIVER=redis
```

### Issue: Cache Inconsistency

**Symptom:** Different data in different requests

**Solution:** Use cache tags consistently:
```php
// Always use the same tags
$tags = ['schedules', "schedule_{$scheduleId}"];
Cache::tags($tags)->put($key, $value, $ttl);
Cache::tags($tags)->flush(); // Invalidates all related caches
```

## Best Practices

1. **Always use cache tags** for related data
2. **Invalidate cache immediately** after data changes
3. **Use consistent cache keys** across the application
4. **Log cache operations** for debugging
5. **Monitor cache hit rate** in production
6. **Use Redis or Memcached** for production environments
7. **Set appropriate TTL** based on data volatility
8. **Test cache invalidation** thoroughly
9. **Document custom cache keys** in code comments
10. **Handle cache failures gracefully** with fallbacks

## Example: Complete Cache Flow

```php
// 1. Load data (checks cache first)
$schedule = Schedule::find($scheduleId);
$grid = $schedule->getAssignmentGrid(); // Uses cache if available

// 2. Modify data
$editService = app(ScheduleEditService::class);
$editService->addUserToSlot($schedule, $date, $session, $userId);
// Cache is automatically invalidated inside addUserToSlot()

// 3. Reload data (cache miss, loads from DB)
$grid = $schedule->getAssignmentGrid(); // Fresh data from DB

// 4. Next request (cache hit)
$grid = $schedule->getAssignmentGrid(); // Served from cache
```

## Conclusion

The caching system is designed to be:
- **Automatic** - Works without manual intervention
- **Consistent** - Always invalidates when data changes
- **Performant** - Reduces database queries significantly
- **Configurable** - Can be enabled/disabled as needed
- **Debuggable** - Logs all operations for troubleshooting

Follow this guide to effectively use and maintain the caching system.
