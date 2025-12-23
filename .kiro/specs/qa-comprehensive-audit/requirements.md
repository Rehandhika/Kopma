# Requirements Document

## Introduction

This document defines the requirements for a comprehensive Quality Assurance (QA) audit of the SIKOPMA (Sistem Informasi Koperasi Mahasiswa) web application. SIKOPMA is a Laravel 12 + Livewire v3 cooperative management system with 12 core modules including attendance tracking, scheduling, POS, inventory management, and administrative functions. The audit will systematically verify all features, pages, user flows, error handling, performance, responsiveness, and security controls to produce a complete defect and improvement map.

## Glossary

- **SIKOPMA**: Sistem Informasi Koperasi Mahasiswa - the student cooperative management system under audit
- **Livewire Component**: A Laravel Livewire full-stack component that handles both backend logic and frontend rendering
- **POS**: Point of Sale - the cashier/checkout system module
- **RBAC**: Role-Based Access Control - permission system using Spatie Laravel Permission
- **NIM**: Nomor Induk Mahasiswa - Student ID number used for authentication
- **Audit Report**: Structured document containing all verified findings with severity, reproduction steps, and recommendations
- **User Flow**: Complete sequence of actions a user performs to accomplish a specific task
- **Edge Case**: Boundary condition or unusual input that may cause unexpected behavior
- **Critical Severity**: Issue that prevents core functionality or causes data loss/corruption
- **High Severity**: Issue that significantly impacts user experience or blocks important features
- **Medium Severity**: Issue that causes inconvenience but has workarounds
- **Low Severity**: Minor cosmetic or usability issues

## Requirements

### Requirement 1: Public Page Audit

**User Story:** As a QA engineer, I want to verify all public-facing pages load correctly and function as expected, so that visitors can access the cooperative's public information without issues.

#### Acceptance Criteria

1. WHEN a visitor accesses the home page (/) THEN the System SHALL display the public catalog with product listings within 3 seconds
2. WHEN a visitor accesses the products page (/products) THEN the System SHALL display all products marked as public with correct images, prices, and descriptions
3. WHEN a visitor accesses a product detail page (/products/{slug}) THEN the System SHALL display complete product information including name, description, price, and availability status
4. WHEN a visitor accesses the about page (/about) THEN the System SHALL display cooperative information with proper layout and no broken assets
5. WHEN a visitor accesses a non-existent public page THEN the System SHALL display a user-friendly 404 error page
6. WHEN a visitor accesses any public page on mobile viewport THEN the System SHALL render the page responsively without horizontal scrolling or overlapping elements

### Requirement 2: Authentication System Audit

**User Story:** As a QA engineer, I want to verify the authentication system works correctly for all user roles, so that only authorized users can access protected resources.

#### Acceptance Criteria

1. WHEN a guest accesses the login page (/admin/login) THEN the System SHALL display the login form with NIM and password fields
2. WHEN a user submits valid credentials (NIM and password) THEN the System SHALL authenticate the user and redirect to the dashboard within 2 seconds
3. WHEN a user submits invalid credentials THEN the System SHALL display an appropriate error message without revealing which field is incorrect
4. WHEN a user attempts more than 5 failed logins within 1 minute THEN the System SHALL implement rate limiting and display the remaining lockout time
5. WHEN an inactive user attempts to login THEN the System SHALL reject the login and display an appropriate message
6. WHEN an authenticated user clicks logout THEN the System SHALL terminate the session and redirect to the login page
7. WHEN a guest attempts to access any protected route (/admin/*) THEN the System SHALL redirect to the login page
8. WHEN a user's session expires THEN the System SHALL redirect to login on the next request with an appropriate message

### Requirement 3: Dashboard Functionality Audit

**User Story:** As a QA engineer, I want to verify the dashboard displays accurate statistics and information for all user roles, so that users can monitor their activities effectively.

#### Acceptance Criteria

1. WHEN an authenticated user accesses the dashboard THEN the System SHALL display role-appropriate statistics within 3 seconds
2. WHEN an admin user views the dashboard THEN the System SHALL display today's attendance count, sales total, active members count, and pending requests
3. WHEN a regular member views the dashboard THEN the System SHALL display their today's schedule, upcoming schedules, monthly attendance summary, and penalty points
4. WHEN the dashboard loads THEN the System SHALL display accurate notification count matching unread notifications
5. WHEN dashboard statistics reference database records THEN the System SHALL display values consistent with actual database state

### Requirement 4: Attendance Module Audit

**User Story:** As a QA engineer, I want to verify the attendance check-in/out system works correctly with all validation rules, so that attendance records are accurate and reliable.

#### Acceptance Criteria

1. WHEN a user with an active schedule accesses check-in page THEN the System SHALL display current schedule information and check-in form
2. WHEN a user attempts check-in before the allowed time window THEN the System SHALL display a message indicating when check-in becomes available
3. WHEN a user uploads a valid photo and submits check-in THEN the System SHALL record the attendance with timestamp and photo path
4. WHEN a user attempts check-in without uploading a photo THEN the System SHALL display a validation error requiring photo upload
5. WHEN a user uploads a file exceeding 5MB THEN the System SHALL display a validation error about file size limit
6. WHEN a user uploads a non-image file THEN the System SHALL display a validation error about file type
7. WHEN a checked-in user submits check-out THEN the System SHALL record check-out time and calculate work hours
8. WHEN a user attempts check-out without prior check-in THEN the System SHALL display an appropriate error message
9. WHEN a user has no scheduled assignment for today THEN the System SHALL display a message indicating no active schedule
10. WHEN attendance status is determined THEN the System SHALL correctly classify as 'present' or 'late' based on configured threshold

### Requirement 5: Schedule Management Audit

**User Story:** As a QA engineer, I want to verify schedule creation, editing, and viewing functions work correctly, so that schedule management is reliable for administrators and members.

#### Acceptance Criteria

1. WHEN an authorized user accesses schedule index THEN the System SHALL display list of schedules with correct status indicators
2. WHEN an admin creates a new schedule THEN the System SHALL validate required fields and create the schedule with draft status
3. WHEN an admin edits a schedule THEN the System SHALL load existing data and save changes without data loss
4. WHEN an admin publishes a schedule THEN the System SHALL change status to published and make it visible to assigned members
5. WHEN a member views their schedule (my-schedule) THEN the System SHALL display only their assigned shifts
6. WHEN schedule assignments are made THEN the System SHALL detect and prevent time conflicts for the same user
7. WHEN the schedule calendar is accessed THEN the System SHALL render all scheduled assignments in correct date positions
8. WHEN a schedule is deleted THEN the System SHALL handle related assignments appropriately

### Requirement 6: Swap Request Module Audit

**User Story:** As a QA engineer, I want to verify the schedule swap request workflow functions correctly, so that members can exchange shifts reliably.

#### Acceptance Criteria

1. WHEN a member creates a swap request THEN the System SHALL validate that both assignments exist and belong to different users
2. WHEN a swap request is submitted THEN the System SHALL notify the target user and relevant approvers
3. WHEN an approver views pending swap requests THEN the System SHALL display all requests awaiting their approval
4. WHEN an approver approves a swap request THEN the System SHALL update both schedule assignments and notify involved parties
5. WHEN an approver rejects a swap request THEN the System SHALL update status and notify the requester with rejection reason
6. WHEN a user views their swap requests THEN the System SHALL display request history with current status

### Requirement 7: Leave Request Module Audit

**User Story:** As a QA engineer, I want to verify the leave request workflow functions correctly, so that members can request time off reliably.

#### Acceptance Criteria

1. WHEN a member creates a leave request THEN the System SHALL validate date range and required fields
2. WHEN a leave request is submitted THEN the System SHALL notify relevant approvers
3. WHEN an approver views pending leave requests THEN the System SHALL display all requests awaiting approval
4. WHEN an approver approves a leave request THEN the System SHALL update status and handle affected schedules
5. WHEN an approver rejects a leave request THEN the System SHALL update status and notify requester
6. WHEN a user views their leave requests THEN the System SHALL display request history with status

### Requirement 8: POS/Cashier Module Audit

**User Story:** As a QA engineer, I want to verify the Point of Sale system processes transactions correctly, so that sales are recorded accurately.

#### Acceptance Criteria

1. WHEN a cashier accesses the POS page THEN the System SHALL display product catalog and cart interface
2. WHEN a cashier adds products to cart THEN the System SHALL update cart totals correctly
3. WHEN a cashier removes products from cart THEN the System SHALL recalculate totals correctly
4. WHEN a cashier processes a sale THEN the System SHALL create sale record with correct items and amounts
5. WHEN a sale is completed THEN the System SHALL update product stock quantities
6. WHEN a cashier views sales list THEN the System SHALL display transaction history with correct totals
7. WHEN processing a sale with insufficient stock THEN the System SHALL display appropriate error message

### Requirement 9: Product Management Audit

**User Story:** As a QA engineer, I want to verify product CRUD operations work correctly, so that inventory data remains accurate.

#### Acceptance Criteria

1. WHEN an admin accesses product list THEN the System SHALL display all products with correct information
2. WHEN an admin creates a new product THEN the System SHALL validate required fields and create the product
3. WHEN an admin edits a product THEN the System SHALL load existing data and save changes correctly
4. WHEN an admin deletes a product THEN the System SHALL handle the deletion appropriately
5. WHEN product images are uploaded THEN the System SHALL store and display images correctly
6. WHEN product stock is updated THEN the System SHALL reflect changes in inventory displays

### Requirement 10: Stock Management Audit

**User Story:** As a QA engineer, I want to verify stock adjustment operations work correctly, so that inventory levels are accurate.

#### Acceptance Criteria

1. WHEN an admin accesses stock index THEN the System SHALL display current stock levels for all products
2. WHEN an admin creates a stock adjustment THEN the System SHALL validate quantity and update product stock
3. WHEN stock falls below minimum threshold THEN the System SHALL flag the product as low stock
4. WHEN stock adjustment history is viewed THEN the System SHALL display all adjustments with timestamps and reasons

### Requirement 11: Purchase Management Audit

**User Story:** As a QA engineer, I want to verify purchase order operations work correctly, so that procurement is tracked accurately.

#### Acceptance Criteria

1. WHEN an admin accesses purchase list THEN the System SHALL display all purchase orders with status
2. WHEN an admin creates a purchase order THEN the System SHALL validate items and create the order
3. WHEN a purchase is received THEN the System SHALL update product stock quantities
4. WHEN purchase history is viewed THEN the System SHALL display complete order details

### Requirement 12: Penalty System Audit

**User Story:** As a QA engineer, I want to verify the penalty system calculates and tracks penalties correctly, so that disciplinary records are accurate.

#### Acceptance Criteria

1. WHEN a user views their penalties THEN the System SHALL display active penalties with points and reasons
2. WHEN an admin assigns a penalty THEN the System SHALL create penalty record and notify the user
3. WHEN penalty points are calculated THEN the System SHALL sum active penalties correctly
4. WHEN an admin manages penalties THEN the System SHALL allow viewing, editing, and resolving penalties

### Requirement 13: Reports Module Audit

**User Story:** As a QA engineer, I want to verify all report types generate accurate data, so that management decisions are based on correct information.

#### Acceptance Criteria

1. WHEN an admin accesses attendance report THEN the System SHALL display attendance statistics with filters
2. WHEN an admin accesses sales report THEN the System SHALL display sales data with correct totals
3. WHEN an admin accesses penalty report THEN the System SHALL display penalty statistics accurately
4. WHEN report filters are applied THEN the System SHALL update displayed data accordingly
5. WHEN reports are exported THEN the System SHALL generate files with correct data

### Requirement 14: User Management Audit

**User Story:** As a QA engineer, I want to verify user management operations work correctly, so that user accounts are properly maintained.

#### Acceptance Criteria

1. WHEN an admin accesses user list THEN the System SHALL display all users with roles and status
2. WHEN an admin creates a new user THEN the System SHALL validate fields and create the account
3. WHEN an admin edits a user THEN the System SHALL update user information correctly
4. WHEN an admin changes user status THEN the System SHALL update status and affect login ability
5. WHEN an admin assigns roles THEN the System SHALL update user permissions accordingly

### Requirement 15: Role-Based Access Control Audit

**User Story:** As a QA engineer, I want to verify RBAC is enforced correctly across all routes, so that unauthorized access is prevented.

#### Acceptance Criteria

1. WHEN a user without required role accesses a restricted page THEN the System SHALL return 403 Forbidden response
2. WHEN a Super Admin accesses any page THEN the System SHALL grant access to all features
3. WHEN role permissions are checked THEN the System SHALL enforce permissions consistently across all routes
4. WHEN a user's role is changed THEN the System SHALL immediately reflect new permissions

### Requirement 16: Notification System Audit

**User Story:** As a QA engineer, I want to verify notifications are delivered and displayed correctly, so that users receive timely information.

#### Acceptance Criteria

1. WHEN a notification is triggered THEN the System SHALL create notification record for target user
2. WHEN a user views notifications THEN the System SHALL display all notifications with read/unread status
3. WHEN a user marks notification as read THEN the System SHALL update read_at timestamp
4. WHEN notification count is displayed THEN the System SHALL show accurate unread count

### Requirement 17: Settings Module Audit

**User Story:** As a QA engineer, I want to verify system settings can be configured correctly, so that administrators can customize system behavior.

#### Acceptance Criteria

1. WHEN an admin accesses general settings THEN the System SHALL display current configuration values
2. WHEN an admin updates settings THEN the System SHALL save changes and apply them immediately
3. WHEN store settings are accessed THEN the System SHALL display store-specific configuration
4. WHEN system settings are modified THEN the System SHALL validate values before saving

### Requirement 18: Error Handling Audit

**User Story:** As a QA engineer, I want to verify error handling provides appropriate feedback, so that users understand issues and can take corrective action.

#### Acceptance Criteria

1. WHEN a validation error occurs THEN the System SHALL display specific field-level error messages
2. WHEN a server error occurs THEN the System SHALL display a user-friendly error page without exposing sensitive information
3. WHEN a 404 error occurs THEN the System SHALL display a helpful not-found page
4. WHEN a 403 error occurs THEN the System SHALL display an unauthorized access message
5. WHEN an exception is thrown THEN the System SHALL log the error with context for debugging

### Requirement 19: Performance Audit

**User Story:** As a QA engineer, I want to verify pages load within acceptable time limits, so that user experience is not degraded by slow performance.

#### Acceptance Criteria

1. WHEN any page is loaded THEN the System SHALL complete initial render within 3 seconds on standard connection
2. WHEN database queries are executed THEN the System SHALL avoid N+1 query problems
3. WHEN large data sets are displayed THEN the System SHALL implement pagination to limit response size
4. WHEN Livewire components update THEN the System SHALL complete updates within 1 second

### Requirement 20: Responsive Design Audit

**User Story:** As a QA engineer, I want to verify all pages render correctly on different screen sizes, so that users can access the system from any device.

#### Acceptance Criteria

1. WHEN pages are viewed on desktop (1920px width) THEN the System SHALL display full layout without issues
2. WHEN pages are viewed on tablet (768px width) THEN the System SHALL adapt layout appropriately
3. WHEN pages are viewed on mobile (375px width) THEN the System SHALL display mobile-optimized layout
4. WHEN navigation is accessed on mobile THEN the System SHALL provide accessible mobile menu
5. WHEN forms are used on mobile THEN the System SHALL render inputs at usable sizes

### Requirement 21: Data Integrity Audit

**User Story:** As a QA engineer, I want to verify data operations maintain integrity, so that business data remains accurate and consistent.

#### Acceptance Criteria

1. WHEN transactions are processed THEN the System SHALL maintain referential integrity between related records
2. WHEN concurrent operations occur THEN the System SHALL handle race conditions appropriately
3. WHEN data is deleted THEN the System SHALL handle cascading effects correctly
4. WHEN numeric calculations are performed THEN the System SHALL produce accurate results


