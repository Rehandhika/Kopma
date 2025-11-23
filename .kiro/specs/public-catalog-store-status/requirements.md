# Requirements Document: Public Catalog & Real-Time Store Status

## Introduction

Transformasi SIKOPMA dari sistem admin-only menjadi website koperasi mahasiswa dengan katalog produk publik dan status operasional real-time. Sistem ini memungkinkan pengunjung umum melihat katalog produk tanpa login, mengetahui status buka/tutup koperasi secara real-time berdasarkan absensi pengurus, dan melihat siapa yang sedang berjaga. Pengurus tetap dapat mengakses dashboard admin yang sudah ada melalui login.

## Glossary

- **SIKOPMA**: Sistem Informasi Koperasi Mahasiswa - aplikasi berbasis Laravel 12, Livewire v3, Tailwind CSS v4
- **Public Catalog**: Halaman katalog produk yang dapat diakses tanpa autentikasi
- **Store Status System**: Sistem yang menampilkan status buka/tutup koperasi secara real-time
- **Attendance Integration**: Integrasi dengan sistem absensi untuk update status otomatis
- **Operating Days**: Hari operasional koperasi (Senin - Kamis)
- **Manual Override**: Fitur admin untuk mengontrol status koperasi secara manual
- **Active Attendance**: Record absensi dengan check-in tanpa check-out (pengurus masih di koperasi)
- **Auto Mode**: Mode otomatis dimana status mengikuti data absensi
- **Manual Mode**: Mode manual dimana admin memiliki kontrol penuh terhadap status

## Requirements

### Requirement 1: Public Product Catalog Access

**User Story:** As a visitor, I want to view the product catalog without logging in, so that I can browse available products easily

#### Acceptance Criteria

1. WHEN a visitor accesses the root URL ("/"), THE SIKOPMA SHALL display the public catalog page with product listings
2. THE SIKOPMA SHALL display product information including name, price, image, description, and stock availability status
3. WHEN a visitor searches for products, THE SIKOPMA SHALL filter and display matching products based on the search query
4. WHEN a visitor selects a category filter, THE SIKOPMA SHALL display only products belonging to the selected category
5. THE SIKOPMA SHALL display products with pagination of 12 items per page

### Requirement 2: Real-Time Store Status Display

**User Story:** As a visitor, I want to see if the store is currently open or closed, so that I know when I can visit the cooperative

#### Acceptance Criteria

1. THE SIKOPMA SHALL display the current store status (open or closed) on all public pages
2. WHEN the store is open, THE SIKOPMA SHALL display a green badge with "BUKA" text and animated pulse indicator
3. WHEN the store is closed, THE SIKOPMA SHALL display a red badge with "TUTUP" text
4. THE SIKOPMA SHALL display the reason for the current status (e.g., "Dijaga oleh: [names]" or "Tidak ada pengurus yang bertugas")
5. THE SIKOPMA SHALL update the status display automatically every 10 seconds without page refresh

### Requirement 3: Automatic Status Update Based on Attendance

**User Story:** As a system, I want to automatically update store status based on staff attendance, so that the status always reflects the actual operational condition

#### Acceptance Criteria

1. WHEN a staff member performs check-in during operating hours on operating days, THE SIKOPMA SHALL automatically set the store status to open
2. WHEN the last staff member performs check-out, THE SIKOPMA SHALL automatically set the store status to closed
3. WHILE the current day is Friday, Saturday, or Sunday, THE SIKOPMA SHALL maintain the store status as closed regardless of attendance records
4. WHILE the current time is outside operating hours (before 08:00 or after 16:00), THE SIKOPMA SHALL maintain the store status as closed regardless of attendance records
5. THE SIKOPMA SHALL update the store status within 1 second after any check-in or check-out event

### Requirement 4: Display Current Attendees

**User Story:** As a visitor, I want to see which staff members are currently on duty, so that I know who is available to serve me

#### Acceptance Criteria

1. WHEN the store status is open, THE SIKOPMA SHALL display the names of all staff members with active attendance records
2. THE SIKOPMA SHALL retrieve attendee names from attendance records where check-in exists without check-out for the current day
3. WHEN multiple staff members are on duty, THE SIKOPMA SHALL display their names separated by commas
4. WHEN the store status changes to closed, THE SIKOPMA SHALL hide the attendee list
5. THE SIKOPMA SHALL update the attendee list within 1 second after any check-in or check-out event

### Requirement 5: Operating Days Validation

**User Story:** As a system, I want to enforce operating days (Monday to Thursday only), so that the store status accurately reflects the cooperative's schedule

#### Acceptance Criteria

1. WHILE the current day is Monday, Tuesday, Wednesday, or Thursday, THE SIKOPMA SHALL allow the store status to be open if other conditions are met
2. WHILE the current day is Friday, Saturday, or Sunday, THE SIKOPMA SHALL force the store status to closed with reason "Koperasi hanya buka Senin - Kamis"
3. IF a staff member performs check-in on Friday, Saturday, or Sunday, THEN THE SIKOPMA SHALL keep the store status as closed
4. THE SIKOPMA SHALL validate operating days before processing any status change request
5. WHERE manual open override is enabled, THE SIKOPMA SHALL allow opening on non-operating days if staff attendance exists

### Requirement 6: Manual Status Override for Administrators

**User Story:** As an administrator, I want to manually control the store status, so that I can handle special situations like meetings or stock shortages

#### Acceptance Criteria

1. WHEN an administrator enables manual mode, THE SIKOPMA SHALL ignore attendance records and use the manually set status
2. WHEN an administrator sets a temporary close with duration, THE SIKOPMA SHALL maintain closed status until the specified time expires
3. WHEN the temporary close duration expires, THE SIKOPMA SHALL automatically return to auto mode and update status based on attendance
4. WHEN an administrator enables manual open override, THE SIKOPMA SHALL allow opening outside operating days or hours if staff attendance exists
5. WHEN an administrator disables manual mode, THE SIKOPMA SHALL immediately return to auto mode and update status based on current attendance

### Requirement 7: Admin Settings Management Interface

**User Story:** As an administrator, I want to configure store settings including operating hours and manual overrides, so that I can manage the cooperative's operational parameters

#### Acceptance Criteria

1. THE SIKOPMA SHALL provide an admin interface accessible only to users with Super Admin, Ketua, or Wakil Ketua roles
2. THE SIKOPMA SHALL display the current store status with mode indicator (Auto, Manual, Temporary Close, or Override)
3. WHEN an administrator modifies operating hours, THE SIKOPMA SHALL save the changes and apply them to future status calculations
4. THE SIKOPMA SHALL provide quick action buttons for common tasks (close for 1 hour, 2 hours, 4 hours, until tomorrow)
5. THE SIKOPMA SHALL log all manual status changes with administrator name and timestamp

### Requirement 8: Product Data Enhancement

**User Story:** As a system administrator, I want products to have public-facing attributes, so that they can be properly displayed in the public catalog

#### Acceptance Criteria

1. THE SIKOPMA SHALL store a unique slug for each product for SEO-friendly URLs
2. THE SIKOPMA SHALL store an image URL for each product with fallback to default image
3. THE SIKOPMA SHALL store a featured flag for each product to highlight special items
4. THE SIKOPMA SHALL store a public visibility flag for each product to control catalog display
5. THE SIKOPMA SHALL store a display order value for each product to control sorting in the catalog

### Requirement 9: Routing Separation

**User Story:** As a developer, I want clear separation between public and admin routes, so that the application structure is maintainable and secure

#### Acceptance Criteria

1. THE SIKOPMA SHALL serve public catalog pages at the root URL ("/") without authentication requirement
2. THE SIKOPMA SHALL serve all admin pages under the "/admin" prefix with authentication requirement
3. THE SIKOPMA SHALL redirect unauthenticated users to the login page when accessing admin routes
4. THE SIKOPMA SHALL provide a login link on public pages for staff members to access the admin panel
5. THE SIKOPMA SHALL maintain backward compatibility by redirecting old admin routes to new prefixed routes

### Requirement 10: Real-Time Status Broadcasting

**User Story:** As a visitor, I want to see status changes immediately without refreshing the page, so that I always have current information

#### Acceptance Criteria

1. WHEN the store status changes, THE SIKOPMA SHALL broadcast the change event to all connected clients
2. THE SIKOPMA SHALL update the status display on all public pages within 10 seconds of a status change
3. WHEN a status change occurs, THE SIKOPMA SHALL display a toast notification to inform users
4. THE SIKOPMA SHALL use polling as a fallback mechanism if real-time broadcasting is unavailable
5. THE SIKOPMA SHALL cache the current status for 30 seconds to optimize performance

### Requirement 11: Performance and Caching

**User Story:** As a system, I want to optimize database queries and use caching, so that the application performs efficiently under load

#### Acceptance Criteria

1. THE SIKOPMA SHALL cache the product catalog for 5 minutes to reduce database load
2. THE SIKOPMA SHALL cache the store status for 30 seconds to optimize status checks
3. THE SIKOPMA SHALL use database indexes on attendance check-in and check-out columns for fast queries
4. THE SIKOPMA SHALL use eager loading to prevent N+1 query problems when loading products with relationships
5. THE SIKOPMA SHALL clear relevant caches immediately when data changes occur

### Requirement 12: Public Layout and Navigation

**User Story:** As a visitor, I want a clean and intuitive public interface, so that I can easily navigate and find information

#### Acceptance Criteria

1. THE SIKOPMA SHALL provide a public layout with navigation menu including Katalog, Tentang, and Login links
2. THE SIKOPMA SHALL display the store status badge prominently in the navigation header
3. THE SIKOPMA SHALL provide a footer with contact information and operating hours
4. THE SIKOPMA SHALL ensure all public pages are responsive and mobile-friendly
5. THE SIKOPMA SHALL maintain consistent styling using Tailwind CSS v4 utility classes

### Requirement 13: Security and Access Control

**User Story:** As a system, I want to enforce proper security measures, so that sensitive data and admin functions are protected

#### Acceptance Criteria

1. THE SIKOPMA SHALL allow public access to catalog pages without exposing sensitive business data
2. THE SIKOPMA SHALL require authentication for all admin routes under the "/admin" prefix
3. THE SIKOPMA SHALL validate user roles before allowing access to store settings management
4. THE SIKOPMA SHALL protect all forms with CSRF tokens to prevent cross-site request forgery
5. THE SIKOPMA SHALL sanitize all user inputs to prevent XSS and SQL injection attacks

### Requirement 14: Logging and Monitoring

**User Story:** As a system administrator, I want comprehensive logging of status changes, so that I can audit and troubleshoot issues

#### Acceptance Criteria

1. THE SIKOPMA SHALL log every store status change with timestamp, old status, new status, and trigger reason
2. THE SIKOPMA SHALL log all manual override actions with administrator name and timestamp
3. WHEN a status update fails, THE SIKOPMA SHALL log the error with context information for debugging
4. THE SIKOPMA SHALL log attendance events that trigger status changes for audit trail
5. THE SIKOPMA SHALL provide log retention for at least 30 days for historical analysis

### Requirement 15: Next Open Time Calculation

**User Story:** As a visitor, I want to know when the store will open next, so that I can plan my visit accordingly

#### Acceptance Criteria

1. WHEN the store is closed outside operating hours, THE SIKOPMA SHALL display the next opening time
2. WHEN the store is closed on Thursday after hours, THE SIKOPMA SHALL display Monday as the next opening day
3. WHEN the store is closed on Friday, Saturday, or Sunday, THE SIKOPMA SHALL display Monday as the next opening day
4. WHEN the store is temporarily closed with a specified duration, THE SIKOPMA SHALL display when the temporary close expires
5. THE SIKOPMA SHALL format the next open time in a human-readable format (e.g., "Senin, 25 Nov 2025 pukul 08:00")
