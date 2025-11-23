# Implementation Plan: Public Catalog & Real-Time Store Status

## Overview

This implementation plan breaks down the development into discrete, manageable coding tasks. Each task builds incrementally on previous work, ensuring the system remains functional throughout development. Tasks are ordered to implement core functionality first, then enhance with additional features.

## Task List

- [x] 1. Database migrations and model enhancements




  - Create migration to add public-facing fields to products table (slug, image_url, is_featured, is_public, display_order)
  - Create migration for store_settings table with all status and configuration fields
  - Add database indexes for performance (products slug, public fields, attendance queries)
  - Update Product model with new fillable fields, casts, and scopes (public, featured, ordered)
  - Implement automatic slug generation in Product model boot method
  - Create StoreSetting model with fillable fields, casts, and relationship to User
  - Create seeder for store_settings with default operating hours (Monday-Thursday 08:00-16:00)
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_






- [ ] 2. Core StoreStatusService implementation
  - Create StoreStatusService class in app/Services directory
  - Implement updateStoreStatus() method with priority-based logic (manual mode, temporary close, auto mode)
  - Implement operating day validation (Monday-Thursday only) in status calculation
  - Implement operating hours validation in status calculation
  - Implement getActiveAttendances() helper method with optimized query
  - Implement openStore() and closeStore() helper methods with cache clearing
  - Implement getStatus() method returning current status with attendees array




  - Implement getStatusReason() method for human-readable status messages
  - Implement getNextOpenTime() method calculating next opening time
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 5.1, 5.2, 5.3, 5.4, 15.1, 15.2, 15.3, 15.4, 15.5_


- [x] 3. Manual override functionality in StoreStatusService




  - Implement manualClose() method with duration parameter
  - Implement manualOpenOverride() method to enable/disable override mode
  - Implement toggleManualMode() method for full manual control
  - Implement backToAutoMode() method to reset all manual settings
  - Implement forceUpdate() method to trigger immediate status recalculation



  - Add logging for all manual override actions with admin name and timestamp
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 5.5, 7.5_






- [ ] 4. Real-time attendance integration with Observer
  - Create AttendanceObserver class in app/Observers directory
  - Implement created() method to handle check-in events and trigger status update
  - Implement updated() method to handle check-out events and trigger status update
  - Add logging for check-in and check-out events with user name and timestamp




  - Register AttendanceObserver in AppServiceProvider boot method
  - _Requirements: 3.1, 3.2, 3.5, 4.5_


- [ ] 5. Event broadcasting for real-time updates
  - Create StoreStatusChanged event implementing ShouldBroadcast interface
  - Configure event to broadcast on 'store-status' public channel
  - Dispatch event from openStore() and closeStore() methods in StoreStatusService
  - Include isOpen, reason, and attendees array in event payload
  - _Requirements: 10.1, 10.2, 10.3_

- [-] 6. Public layout and navigation components



  - Create public.blade.php layout file in resources/views/layouts
  - Implement navigation header with logo, menu links (Katalog, Tentang), and Login button
  - Add store status badge placeholder in navigation header
  - Implement footer with contact information and operating hours sections
  - Style layout with Tailwind CSS ensuring mobile-responsive design
  - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_

- [ ] 7. Public StoreStatus Livewire component

  - Create StoreStatus Livewire component in app/Livewire/Public
  - Implement mount() method to load initial status from StoreStatusService
  - Implement refresh() method to update status data
  - Create store-status.blade.php view with animated status badge (green for open, red for closed)
  - Display status reason and attendee names when store is open
  - Add wire:poll.10s for automatic status refresh every 10 seconds
  - Implement Alpine.js for smooth animations and transitions
  - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 4.1, 4.2, 4.3, 4.4, 10.2, 10.4_

- [ ]* 7.1 Add Laravel Echo integration for real-time status updates
  - Configure Echo to listen to 'store-status' channel in component
  - Display toast notification when status changes
  - _Requirements: 10.1, 10.3_

- [x] 8. Public Catalog Livewire component



  - Create Catalog Livewire component in app/Livewire/Public
  - Implement properties for search query, category filter, and pagination
  - Implement render() method querying public products with filters and caching
  - Create catalog.blade.php view with hero section and product grid
  - Implement search input with wire:model.live for reactive filtering
  - Implement category dropdown filter with wire:model.live
  - Display products in responsive grid (1 col mobile, 2 col tablet, 4 col desktop)
  - Show product card with image, name, price, description, and stock status
  - Add pagination with 12 items per page
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 11.1, 11.4_

- [x] 9. Public ProductDetail Livewire component



  - Create ProductDetail Livewire component in app/Livewire/Public
  - Implement mount() method accepting product slug parameter
  - Query product by slug with route model binding
  - Create product-detail.blade.php view showing full product information
  - Display large product image, full description, price, and stock availability
  - Handle product not found with 404 error
  - _Requirements: 1.2_

- [x] 10. Public About Livewire component





  - Create About Livewire component in app/Livewire/Public
  - Load store settings for contact information and about text
  - Create about.blade.php view displaying cooperative information
  - Show contact details (phone, email, WhatsApp, address)
  - Display operating hours from store settings
  - Show about text with formatted content
  - _Requirements: 12.3_
- [x] 11. Routing refactor for public and admin separation




- [ ] 11. Routing refactor for public and admin separation

  - Create public routes at root level (/, /products, /products/{slug}, /about)
  - Move login route to /admin/login with guest middleware
  - Wrap all existing admin routes in Route::prefix('admin') group
  - Add 'admin.' prefix to all admin route names
  - Update middleware groups ensuring auth required for admin routes
  - Add backward compatibility redirects from old routes to new admin routes
  - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

- [x] 12. Update navigation links in admin layout





  - Update resources/views/components/navigation.blade.php with admin route prefixes
  - Update all route() calls to use 'admin.' prefix
  - Update sidebar menu links to point to /admin/* routes
  - Test all navigation links work correctly
  - _Requirements: 9.5_


- [x] 13. Admin StoreSettings Livewire component




  - Create StoreSettings Livewire component in app/Livewire/Admin/Settings
  - Inject StoreStatusService in boot() method
  - Implement mount() method loading store settings and current status
  - Implement refreshStatus() method to update current status display
  - Create store-settings.blade.php view with current status section showing mode indicator
  - Add quick action buttons for common tasks (close 1hr, 2hr, 4hr, until tomorrow)
  - Implement closeFor() method calling StoreStatusService::manualClose()
  - Implement closeUntilTomorrow() method for temporary close until next day
  - _Requirements: 7.1, 7.2, 7.4, 6.2, 6.3_




- [ ] 14. Manual override controls in StoreSettings component

  - Implement enableOpenOverride() and disableOpenOverride() methods
  - Implement enableManualMode() method to activate full manual control
  - Implement setManualStatus() method to set open/closed in manual mode
  - Implement disableManualMode() method to return to auto mode
  - Implement resetToAuto() method to clear all manual settings
  - Add UI sections for each override type with clear status indicators


  - Display current mode (Auto, Manual, Temporary Close, Override) prominently

  - Add toast notifications for all status changes
  - _Requirements: 6.1, 6.4, 6.5, 5.5, 7.2_

- [ ] 15. Operating hours configuration in StoreSettings component

  - Add operating hours form section in store-settings.blade.php




  - Create time inputs for each day (Monday-Thursday) with open and close times
  - Bind inputs to operatingHours property with wire:model
  - Implement saveOperatingHours() method to update store settings
  - Validate time format and ensure close time is after open time
  - Display success message after saving
  - _Requirements: 7.3_

- [ ] 16. Contact information management in StoreSettings component

  - Add contact information form section in store-settings.blade.php
  - Create inputs for phone, email, WhatsApp, address, and about text
  - Bind inputs to component properties with wire:model
  - Implement saveContactInfo() method to update store settings
  - Validate email format and phone number format
  - Display success message after saving
  - _Requirements: 7.1_

- [ ] 17. Authorization and access control for store settings


  - Add role middleware to store settings route (Super Admin, Ketua, Wakil Ketua only)
  - Implement authorization check in StoreSettings component mount method
  - Display unauthorized error if user lacks permission
  - Add store settings link to admin navigation menu for authorized users only
  - _Requirements: 7.1, 13.3_

- [ ] 18. Scheduled task for status update fallback
  - Add scheduled command in app/Console/Kernel.php schedule method
  - Call StoreStatusService::updateStoreStatus() every minute as backup
  - Add hourly task to force close outside operating hours
  - Log scheduled task execution for monitoring
  - _Requirements: 3.5_

- [ ] 19. Caching implementation for performance
  - Implement cache for product catalog in Catalog component (5 minute TTL)
  - Implement cache for store status in StoreStatusService (30 second TTL)
  - Clear product cache on product create/update/delete events
  - Clear status cache in openStore() and closeStore() methods
  - Use cache tags for easier cache management
  - _Requirements: 11.1, 11.2, 11.5_

- [ ] 20. Security enhancements and input validation
  - Add CSRF protection verification to all forms
  - Implement input sanitization in search query (max 100 chars)
  - Validate manual override reason text (max 500 chars)
  - Validate duration parameter (max 7 days)
  - Add rate limiting to public routes (60 requests/minute per IP)
  - Add rate limiting to admin routes (120 requests/minute per user)
  - _Requirements: 13.1, 13.2, 13.4, 13.5_

- [ ] 21. Logging and monitoring implementation
  - Create 'store' log channel in config/logging.php
  - Add status change logging in openStore() and closeStore() methods
  - Add manual override logging in all manual methods
  - Add error logging with context in try-catch blocks



  - Log attendance events that trigger status changes

  - _Requirements: 14.1, 14.2, 14.3, 14.4_

- [ ] 22. Error handling and fallback mechanisms
  - Add try-catch blocks in StoreStatusService methods with error logging
  - Implement fallback to cached status if database unavailable
  - Implement fallback to wire:poll if broadcasting unavailable
  - Add error display in UI for critical failures
  - Implement exponential backoff for database reconnection attempts
  - _Requirements: 10.4_

- [ ] 23. Data seeding and migration execution

  - Run migrations for products and store_settings tables
  - Execute StoreSettingSeeder to create default settings
  - Generate slugs for all existing products using Artisan command
  - Set default image URLs for products without images
  - Verify all migrations completed successfully
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ] 24. Manual testing and verification
  - Test public catalog access without authentication
  - Test check-in triggers status to BUKA within 1 second
  - Test check-out triggers status to TUTUP within 1 second
  - Test status remains TUTUP on Friday, Saturday, Sunday regardless of attendance
  - Test status remains TUTUP outside operating hours (before 08:00, after 16:00)
  - Test attendee names display correctly when store is open
  - Test manual close with duration auto-resets after expiry
  - Test manual open override allows opening on non-operating days
  - Test manual mode gives full control ignoring attendance
  - Test responsive design on mobile, tablet, and desktop
  - Test all navigation links work correctly
  - Test admin authorization for store settings
  - Verify page load times under 2 seconds
  - Verify no console errors in browser
  - _Requirements: All requirements_

## Notes

- Tasks are ordered to build incrementally with core functionality first
- Each task references specific requirements from requirements.md
- Testing tasks marked with * are optional to save time
- Manual testing (task 24) covers critical user flows only
- No automated tests or documentation tasks included per project constraints
- Estimated total time: 10-14 hours of focused development

## Dependencies

- Task 2 depends on Task 1 (models must exist)
- Task 4 depends on Task 2 (service must exist)
- Task 5 depends on Task 2 (service must exist)
- Task 7 depends on Task 2 (service must exist)
- Tasks 8-10 depend on Task 6 (layout must exist)
- Task 11 depends on Tasks 7-10 (components must exist)
- Task 13 depends on Task 2 (service must exist)
- Tasks 14-16 depend on Task 13 (component must exist)
- Task 23 depends on Task 1 (migrations must exist)
- Task 24 depends on all previous tasks (full system must be implemented)
