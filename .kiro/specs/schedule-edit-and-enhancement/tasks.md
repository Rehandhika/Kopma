# Implementation Plan

## Overview

This implementation plan breaks down the schedule edit and enhancement features into discrete, manageable coding tasks. Each task builds incrementally on previous tasks, with all code properly integrated.

The implementation is divided into major phases:
1. Database and Model enhancements
2. Core services for multi-user slot management
3. Edit schedule functionality with audit trail
4. Enhanced auto-assignment algorithm
5. UI components and interactions
6. Testing and validation

## Tasks

- [x] 1. Database and Model Setup





  - Create migration for assignment edit history tracking
  - Add edit tracking fields to schedule_assignments table
  - Create schedule configuration table and seeder
  - Add database indexes for performance
  - _Requirements: 1.4, 5.1_

- [x] 1.1 Create assignment_edit_history migration


  - Write migration file to create `assignment_edit_history` table
  - Include fields: id, assignment_id, schedule_id, edited_by, action, old_values, new_values, reason, timestamps
  - Add foreign key constraints and indexes
  - _Requirements: 1.4_

- [x] 1.2 Add edit tracking fields to schedule_assignments


  - Create migration to add `edited_by`, `edited_at`, `edit_reason`, `previous_values` columns
  - Make fields nullable for backward compatibility
  - _Requirements: 1.4_

- [x] 1.3 Create schedule_configurations table


  - Write migration for configuration storage
  - Include fields: id, key, value, type, description, timestamps
  - Add unique index on key field
  - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_



- [x] 1.4 Create ScheduleConfigurationSeeder
  - Seed default configuration values for multi-user slots
  - Include max_users_per_slot, target_users_per_slot, allow_empty_slots, scoring weights


  - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_




- [x] 1.5 Add performance indexes
  - Add composite indexes on schedule_assignments (user_id, date, session)
  - Add indexes on assignment_edit_history (assignment_id, created_at)
  - Add index on schedule_id and status
  - _Requirements: 10.3, 10.4_

- [x] 2. Enhanced Models



  - Update Schedule model with multi-user slot methods
  - Update ScheduleAssignment model with slot helpers
  - Create AssignmentEditHistory model
  - Create ScheduleConfiguration model


  - _Requirements: 1.1, 1.2, 1.3, 5.1_

- [x] 2.1 Create AssignmentEditHistory model
  - Define model with fillable fields


  - Add relationships: assignment(), schedule(), editor()
  - Add scopes: forSchedule(), byEditor(), recent()
  - Add helper methods: getChangeSummary(), getAffectedFields()
  - _Requirements: 5.1_

- [x] 2.2 Create ScheduleConfiguration model
  - Define model with fillable fields


  - Add unique validation on key field
  - Add type casting methods for integer, float, boolean, json
  - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_


- [x] 2.3 Update ScheduleAssignment model for multi-user slots
  - Add editHistory() relationship
  - Add editor() relationship
  - Add getSlotmates() method to get other users in same slot
  - Add getSlotUserCount() method

  - Add isOnlyUserInSlot() and hasSlotmates() helpers
  - Add scopeForSlot() scope
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 2.4 Update Schedule model for multi-user slots
  - Update getAssignmentGrid() to handle multiple users per slot
  - Update calculateCoverage() to count filled slots (slots with ≥1 user)
  - Update detectConflicts() to check for duplicate users in same slot
  - Add getSlotStatistics() method for multi-user metrics
  - _Requirements: 1.1, 1.2, 1.3, 6.1, 6.2, 6.3_

- [x] 3. Configuration Service



  - Create ScheduleConfigurationService for managing settings
  - Implement caching for configuration values
  - Add methods to get, set, and retrieve all configurations
  - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_

- [x] 3.1 Implement ScheduleConfigurationService

  - Create service class in app/Services
  - Implement get() method with caching
  - Implement set() method with cache invalidation
  - Implement getAll() method for bulk retrieval
  - Add castValue() helper for type conversion
  - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_
- [x] 4. Multi-User Slot Edit Service




- [ ] 4. Multi-User Slot Edit Service

  - Create ScheduleEditService for slot management operations
  - Implement add, remove, update, and clear slot operations
  - Add validation for multi-user slots
  - Implement audit trail recording
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 3.1, 3.2, 3.3_

- [x] 4.1 Create ScheduleEditService base structure


  - Create service class in app/Services
  - Inject dependencies: ScheduleConfigurationService
  - Define method signatures for all operations
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 4.2 Implement addUserToSlot method


  - Validate user is active
  - Check for duplicate user in same slot
  - Check slot capacity (max_users_per_slot)
  - Create assignment record
  - Record change in audit trail
  - Update schedule coverage
  - _Requirements: 1.1, 1.3, 3.1, 3.3_

- [x] 4.3 Implement removeUserFromSlot method


  - Validate schedule is editable or user has permission
  - Delete assignment record
  - Record change in audit trail
  - Update schedule coverage
  - _Requirements: 1.2, 3.2, 3.3_

- [x] 4.4 Implement updateUserInSlot method


  - Validate new user is active
  - Check for conflicts with new user
  - Update assignment record
  - Record change in audit trail with old and new values
  - _Requirements: 1.1, 1.4, 3.3_

- [x] 4.5 Implement clearSlot method


  - Get all assignments for the slot
  - Delete all assignments in transaction
  - Record bulk change in audit trail
  - Update schedule coverage
  - _Requirements: 3.2, 3.3_

- [x] 4.6 Implement bulkAddUsersToSlot method


  - Validate all users are active and available
  - Check slot capacity
  - Create multiple assignment records in transaction
  - Record bulk change in audit trail
  - _Requirements: 1.1, 3.1, 3.3_

- [x] 4.7 Implement validation methods


  - validateUserForSlot(): check active status, availability, duplicates
  - checkUserDoubleBooking(): verify user not in same slot
  - isSlotFull(): check against max_users_per_slot configuration
  - _Requirements: 1.3, 4.1, 4.2, 4.3_

- [x] 4.8 Implement audit trail recording


  - recordChange() method to create AssignmentEditHistory records
  - Include action type, old/new values, reason, editor
  - Handle both single and bulk operations
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_
- [x] 5. Conflict Detection Service




- [ ] 5. Conflict Detection Service

  - Create ConflictDetectionService for multi-user slot conflicts
  - Implement detection for duplicates, inactive users, availability mismatches
  - Add conflict categorization (critical, warning, info)
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [x] 5.1 Create ConflictDetectionService structure


  - Create service class in app/Services
  - Define conflict types and severity levels
  - _Requirements: 4.1, 4.2, 4.3_

- [x] 5.2 Implement detectAllConflicts method


  - Call all specific detection methods
  - Aggregate results
  - Categorize by severity
  - Return structured conflict array
  - _Requirements: 4.1, 11.1, 11.2, 11.3_

- [x] 5.3 Implement specific conflict detectors


  - detectDuplicateUsersInSlot(): same user multiple times in one slot
  - detectDoubleAssignments(): user in multiple slots at same time (shouldn't happen)
  - detectInactiveUsers(): assignments with inactive users
  - detectAvailabilityMismatches(): users assigned when marked unavailable
  - detectOverstaffedSlots(): slots exceeding max_users_per_slot
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [x] 5.4 Implement conflict categorization


  - categorizeConflicts(): group by critical/warning/info
  - getConflictSeverity(): determine severity level
  - Format conflict messages for display
  - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_

- [x] 6. Enhanced Auto-Assignment Service





  - Create EnhancedAutoAssignmentService for multi-user slot generation
  - Implement scoring system with slot coverage bonus
  - Add workload balancing for multi-user slots
  - Implement conflict resolution
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 7.1, 7.2, 7.3, 7.4, 7.5, 8.1, 8.2, 8.3, 8.4, 8.5, 9.1, 9.2, 9.3, 9.4, 9.5_

- [x] 6.1 Create EnhancedAutoAssignmentService structure


  - Create service class in app/Services
  - Inject ScheduleConfigurationService and ConflictDetectionService
  - Define main generateOptimalSchedule method signature
  - _Requirements: 6.1, 6.2, 6.3_

- [x] 6.2 Implement user and slot data loading


  - getActiveUsersWithAvailability(): load users with availability data
  - generateSlotGrid(): create 12 slots (4 days × 3 sessions)
  - Use eager loading for performance
  - Cache availability data
  - _Requirements: 6.1, 10.1, 10.2, 10.3, 10.4_

- [x] 6.3 Implement scoring system


  - calculateSlotScores(): score all user-slot combinations
  - calculateUserScore(): implement multi-factor scoring
  - Include availability (+100), workload penalty (-10/assignment), consecutive penalty (-20)
  - Add day variety bonus (+10), slot coverage bonus (+30 for empty slots)
  - Exclude unavailable users (-1000) and duplicate users in slot (-2000)
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 7.2, 8.1, 8.2, 8.3, 9.1, 9.2_

- [x] 6.4 Implement assignment algorithm


  - assignUsersToSlots(): main assignment logic
  - Support target_users_per_slot configuration
  - Support max_users_per_slot limit
  - Handle allow_empty_slots configuration
  - Implement two modes: prioritize coverage vs balance workload
  - Prevent duplicate users in same slot
  - _Requirements: 6.1, 6.2, 6.3, 7.1, 7.2, 7.3_

- [x] 6.5 Implement workload balancing


  - balanceWorkload(): redistribute assignments for fairness
  - Calculate average assignments per user
  - Identify overloaded and underloaded users
  - Move assignments from overloaded to underloaded users
  - Respect max deviation (default: 2 assignments)
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [x] 6.6 Implement validation and conflict resolution


  - validateAndFix(): final validation pass
  - detectConflicts(): use ConflictDetectionService
  - resolveConflict(): fix detected conflicts
  - Handle duplicates, inactive users, overstaffed slots
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [x] 6.7 Implement helper methods


  - isUserAvailable(): check availability for slot
  - countUserAssignments(): count user's total assignments
  - hasConsecutiveShift(): check for adjacent sessions
  - getUserAssignedDays(): get days user is already assigned
  - getSlotUserCount(): count users in specific slot
  - isUserInSlot(): check if user already in slot
  - sortSlotsByPriority(): order slots for assignment
  - _Requirements: 6.1, 6.2, 6.3, 8.1, 8.2, 8.3, 9.1, 9.2_

- [x] 7. Notification Service Updates





  - Update notification templates for multi-user slots
  - Implement slot-based notifications
  - Add notification for added, removed, and updated assignments
  - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_

- [x] 7.1 Update ScheduleEditService notification methods


  - notifyUser(): send notification to individual user
  - notifySlotUsers(): send notification to all users in a slot
  - Support notification types: assignment_added, assignment_removed, assignment_updated
  - Include slot details and reason in notifications
  - _Requirements: 12.1, 12.2, 12.3, 12.5_


- [x] 7.2 Create notification templates

  - Assignment added: include date, session, time, slotmates
  - Assignment removed: include reason
  - Assignment updated: include old and new details
  - Schedule published: include total assignments and slotmates
  - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_

- [x] 8. EditSchedule Livewire Component






  - Create main edit schedule component for multi-user slots
  - Implement slot management operations
  - Add real-time conflict detection
  - Implement change tracking and undo functionality
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 11.1, 11.2, 11.3, 11.4, 11.5_


- [x] 8.1 Create EditSchedule component structure


  - Create Livewire component in app/Livewire/Schedule
  - Define properties: schedule, assignments, changes, conflicts, etc.
  - Inject ScheduleEditService and ConflictDetectionService
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 8.2 Implement mount and data loading


  - mount(): initialize component with schedule
  - loadAssignments(): load assignments grouped by slot
  - loadAvailableUsers(): get users available for assignment
  - Cache original assignments for change tracking
  - _Requirements: 1.1_

- [x] 8.3 Implement slot management methods


  - addUserToSlot(): add user to specific slot
  - removeUserFromSlot(): remove user from slot
  - clearSlot(): remove all users from slot
  - bulkAddUsers(): add multiple users to slot
  - Track changes for undo functionality
  - _Requirements: 1.1, 1.2, 1.3, 3.1, 3.2, 3.3_

- [x] 8.4 Implement validation and conflict detection


  - detectConflicts(): run conflict detection on current state
  - validateUserForSlot(): validate before adding user
  - Real-time validation on user actions
  - Display conflicts in UI
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 11.1, 11.2, 11.3, 11.4, 11.5_

- [x] 8.5 Implement save and discard operations


  - saveChanges(): persist all changes to database
  - discardChanges(): revert to original state
  - Use database transactions for atomicity
  - Trigger notifications after save
  - Invalidate caches
  - _Requirements: 1.4, 1.5, 12.1, 12.2, 12.3_

- [x] 8.6 Implement helper methods


  - getSlotAssignments(): get users for specific slot
  - getSlotUserCount(): count users in slot
  - isSlotFull(): check if slot at capacity
  - isSlotEmpty(): check if slot has no users
  - getSlotStatistics(): calculate slot metrics
  - trackChange(): record change for audit
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 9. Routes and Policies





  - Add routes for edit schedule functionality
  - Update authorization policies
  - Add middleware for permission checks
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 9.1 Add routes in web.php


  - Route for edit schedule page: /admin/schedule/{schedule}/edit
  - Route for view edit history: /admin/schedule/{schedule}/history
  - Group under schedule prefix with auth middleware
  - _Requirements: 1.1_



- [ ] 9.2 Update SchedulePolicy
  - Add edit() method: check for Admin/Super Admin role and schedule is editable
  - Add forceEdit() method: check for Super Admin role (can edit any schedule)
  - Add viewHistory() method: check for Admin/Pengurus role
  - _Requirements: 1.1, 1.2, 5.3_

- [x] 10. Caching Implementation


  - Implement caching for schedule data
  - Add cache invalidation on updates
  - Cache user availability
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

- [x] 10.1 Implement schedule caching in EditSchedule component


  - Cache schedule grid data with key "schedule_grid_{$scheduleId}"
  - Cache conflict detection results with key "schedule_conflicts_{$scheduleId}"
  - Cache statistics with key "schedule_statistics_{$scheduleId}"
  - Use cache tags ['schedules', "schedule_{$scheduleId}"] for easy invalidation
  - TTL of 1 hour (3600 seconds)
  - _Requirements: 10.1, 10.2, 10.5_

- [x] 10.2 Implement cache invalidation in ScheduleEditService


  - Invalidate on assignment add/remove/update using Cache::tags()->flush()
  - Invalidate on schedule publish
  - Invalidate specific schedule caches when changes are saved
  - _Requirements: 10.5_

- [x] 10.3 Implement user availability caching


  - Cache user availability data with key "user_availability_{$userId}_{$weekStart}"
  - TTL of 1 hour
  - Invalidate when user updates their availability
  - _Requirements: 10.2, 10.3_

- [x] 11. Enhanced UI Components




  - Improve edit schedule interface with better UX
  - Add visual indicators for slot status
  - Implement inline editing capabilities
  - Add statistics panel
  - _Requirements: 13.1, 13.2, 13.4, 13.5_



- [x] 11.1 Enhance edit-schedule.blade.php view





  - Add statistics panel showing filled slots, empty slots, total assignments
  - Improve slot card design with user count badges
  - Add color coding for slot status (empty, normal, overstaffed, conflict)
  - Add inline user management (add/remove buttons on each slot)
  - Show user avatars or initials in slot cards


  - _Requirements: 13.1, 13.2, 13.4, 13.5_





- [x] 11.2 Create reusable blade components

  - Create components/schedule/slot-card.blade.php for displaying slots
  - Create components/schedule/user-badge.blade.php for user display


  - Create components/schedule/conflict-indicator.blade.php for conflicts
  - Create components/schedule/statistics-panel.blade.php for metrics
  - _Requirements: 13.1, 13.4, 13.5_


- [x] 11.3 Add Alpine.js interactions







  - Add tooltips for user details on hover
  - Add confirmation dialogs for destructive actions
  - Add loading states for async operations
  - Add smooth transitions for UI changes
  - _Requirements: 13.2, 13.3_

- [ ] 12. Edit History View
  - Create component to display assignment edit history
  - Show timeline of changes
  - Filter by date, user, action type
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [ ] 12.1 Create EditHistory Livewire component
  - Create component in app/Livewire/Schedule/EditHistory.php
  - Load edit history for schedule with pagination (20 per page)
  - Implement filtering by action type, editor, date range
  - Implement search by user name
  - _Requirements: 5.1, 5.2, 5.3_

- [ ] 12.2 Create edit-history.blade.php view
  - Timeline layout with timestamps
  - Display editor name and action type
  - Show old and new values for updates
  - Show reason for each change
  - Add filter controls
  - Add pagination
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ]* 13. Testing
  - Write unit tests for services
  - Write feature tests for components
  - Write integration tests for workflows
  - Test multi-user slot scenarios
  - _Requirements: All_

- [ ]* 13.1 Unit tests for ScheduleEditService
  - Test addUserToSlot with valid and invalid users
  - Test removeUserFromSlot
  - Test clearSlot
  - Test validation methods
  - Test duplicate prevention
  - Test slot capacity limits
  - _Requirements: 1.1, 1.2, 1.3, 3.1, 3.2, 3.3, 4.1, 4.2, 4.3_

- [ ]* 13.2 Unit tests for EnhancedAutoAssignmentService
  - Test scoring calculation
  - Test workload balancing
  - Test conflict detection
  - Test multi-user slot generation
  - Test empty slot handling
  - Test overstaffed slot prevention
  - _Requirements: 6.1, 6.2, 6.3, 7.1, 7.2, 7.3, 7.4, 7.5_

- [x]* 13.3 Unit tests for ConflictDetectionService
  - Test duplicate user detection
  - Test inactive user detection
  - Test availability mismatch detection
  - Test overstaffed slot detection
  - Test conflict categorization
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [ ]* 13.4 Feature tests for EditSchedule component
  - Test adding user to slot
  - Test removing user from slot
  - Test clearing slot
  - Test bulk add users
  - Test save changes
  - Test discard changes
  - Test conflict detection
  - Test notifications
  - _Requirements: 1.1, 1.2, 1.3, 3.1, 3.2, 3.3, 4.1, 12.1, 12.2, 12.3_

- [ ]* 13.5 Integration tests for complete workflows
  - Test create schedule → auto-assign → edit → publish workflow
  - Test edit published schedule workflow
  - Test multi-user slot scenarios
  - Test concurrent editing
  - Test cache consistency
  - _Requirements: All_

- [ ]* 14. Documentation and Cleanup
  - Update API documentation
  - Add inline code comments
  - Create user guide for edit schedule feature
  - Update CHANGELOG.md
  - _Requirements: All_

- [ ]* 14.1 Add inline documentation
  - Document all service methods with PHPDoc
  - Document component properties and methods
  - Add code comments for complex logic
  - _Requirements: All_

- [ ]* 14.2 Create user guide
  - Document how to edit published schedules
  - Document multi-user slot management
  - Document conflict resolution
  - Add screenshots and examples
  - _Requirements: 1.1, 1.2, 1.3, 4.1, 11.1_

- [ ]* 14.3 Update CHANGELOG.md
  - Document new features
  - Document breaking changes (if any)
  - Document configuration options
  - _Requirements: All_

## Summary

This implementation plan has been refreshed based on the current codebase state. The following major components have been completed:

**Completed (Tasks 1-8):**
- ✅ Database migrations and models
- ✅ Configuration service
- ✅ Multi-user slot edit service
- ✅ Conflict detection service
- ✅ Enhanced auto-assignment algorithm
- ✅ Notification system
- ✅ EditSchedule Livewire component (core functionality)

**Remaining (Tasks 9-14):**
- ⏳ Routes and authorization policies
- ⏳ Caching implementation
- ⏳ Enhanced UI components and views
- ⏳ Edit history view component
- ⏳ Testing (optional)
- ⏳ Documentation (optional)

The core backend functionality is complete. The remaining tasks focus on:
1. Adding routes and authorization
2. Implementing caching for performance
3. Enhancing the UI/UX
4. Adding edit history viewing capability
5. Optional testing and documentation
