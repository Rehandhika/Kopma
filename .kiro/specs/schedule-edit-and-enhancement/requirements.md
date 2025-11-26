# Requirements Document

## Introduction

Sistem SIKOPMA saat ini memiliki modul penjadwalan yang sudah berjalan dengan fitur pembuatan jadwal, tampilan kalender, dan auto-assignment. Namun, terdapat kebutuhan untuk meningkatkan fleksibilitas dan kualitas penjadwalan melalui dua pengembangan utama:

1. **Kemampuan Edit Jadwal Terpublikasi**: Saat ini jadwal yang sudah dipublikasi tidak dapat diedit, menyebabkan kesulitan ketika ada perubahan mendadak atau kebutuhan penyesuaian.

2. **Peningkatan Algoritma Auto-Assignment**: Algoritma penjadwalan otomatis perlu ditingkatkan untuk menghasilkan distribusi shift yang lebih adil, mempertimbangkan ketersediaan anggota, dan menghindari konflik.

Sistem penjadwalan menggunakan pola 4 hari kerja (Senin-Kamis) dengan 3 sesi per hari:
- Sesi 1: 07:30 - 10:20 (2 jam 50 menit)
- Sesi 2: 10:20 - 12:50 (2 jam 30 menit)  
- Sesi 3: 13:30 - 16:00 (2 jam 30 menit)

Total 12 slot per minggu yang perlu diisi dengan anggota yang tersedia.

## Glossary

- **Schedule System**: Sistem penjadwalan SIKOPMA yang mengelola assignment anggota ke slot waktu
- **Assignment**: Penugasan seorang anggota ke slot waktu tertentu (hari dan sesi)
- **Published Schedule**: Jadwal yang sudah dipublikasikan dan aktif digunakan
- **Slot**: Unit waktu dalam jadwal (kombinasi hari dan sesi)
- **Session**: Periode waktu dalam satu hari (Sesi 1, 2, atau 3)
- **Coverage Rate**: Persentase slot yang terisi dari total slot tersedia
- **Workload**: Jumlah assignment yang dimiliki seorang anggota dalam satu periode
- **Availability**: Ketersediaan anggota untuk dijadwalkan pada slot tertentu
- **Conflict**: Kondisi dimana seorang anggota memiliki lebih dari satu assignment pada waktu yang sama
- **Audit Trail**: Catatan riwayat perubahan pada jadwal untuk keperluan tracking
- **Auto-Assignment Algorithm**: Algoritma yang secara otomatis mengisi slot jadwal dengan anggota yang tersedia
- **Fairness Score**: Metrik yang mengukur keadilan distribusi workload antar anggota
- **Consecutive Shift**: Assignment berturut-turut pada sesi yang berdekatan

## Requirements

### Requirement 1: Edit Published Schedule

**User Story:** As an admin, I want to edit published schedules, so that I can handle last-minute changes and adjustments without recreating the entire schedule

#### Acceptance Criteria

1. WHEN an admin accesses a published schedule, THE Schedule System SHALL display an edit interface with all current assignments
2. WHEN an admin selects an assignment to edit, THE Schedule System SHALL display available users who can replace the current assignee
3. WHEN an admin updates an assignment with a new user, THE Schedule System SHALL validate that the new user is active and has no conflicting assignments
4. WHEN an admin saves assignment changes, THE Schedule System SHALL record the change in an audit trail with timestamp, editor identity, and reason
5. WHEN assignment changes are saved, THE Schedule System SHALL send notifications to all affected users (removed and newly assigned)

### Requirement 2: Assignment Swap Functionality

**User Story:** As an admin, I want to swap assignments between users, so that I can quickly reorganize schedules when needed

#### Acceptance Criteria

1. WHEN an admin initiates a swap between two assignments, THE Schedule System SHALL validate that both users can work in the swapped slots
2. WHEN a swap is executed, THE Schedule System SHALL update both assignments atomically in a single transaction
3. WHEN a swap is completed, THE Schedule System SHALL record the swap action in the audit trail with both user identities
4. IF a swap would create a conflict for either user, THEN THE Schedule System SHALL reject the swap and display an error message
5. WHEN a swap is successful, THE Schedule System SHALL notify both affected users of their new assignments

### Requirement 3: Add and Remove Assignments

**User Story:** As an admin, I want to add or remove individual assignments, so that I can fill empty slots or handle absences

#### Acceptance Criteria

1. WHEN an admin adds a new assignment to an empty slot, THE Schedule System SHALL validate user availability and active status
2. WHEN an admin removes an assignment, THE Schedule System SHALL update the schedule coverage rate
3. WHEN an assignment is removed, THE Schedule System SHALL notify the affected user with the removal reason
4. IF removing an assignment causes coverage rate to drop below 50%, THEN THE Schedule System SHALL display a warning but allow the action
5. WHEN an assignment is added or removed, THE Schedule System SHALL record the action in the audit trail

### Requirement 4: Conflict Detection and Prevention

**User Story:** As an admin, I want the system to detect and prevent scheduling conflicts, so that no user is double-booked

#### Acceptance Criteria

1. WHEN an admin attempts to assign a user to a slot, THE Schedule System SHALL check for existing assignments at the same date and time
2. IF a user already has an assignment at the requested time, THEN THE Schedule System SHALL reject the assignment and display a conflict error
3. WHEN displaying the edit interface, THE Schedule System SHALL highlight any existing conflicts in the current schedule
4. WHEN an admin saves changes, THE Schedule System SHALL perform a final conflict validation before committing
5. IF availability data indicates a user is unavailable for a slot, THEN THE Schedule System SHALL display a warning but allow the assignment

### Requirement 5: Audit Trail and Change History

**User Story:** As an admin, I want to view the history of schedule changes, so that I can track who made what changes and when

#### Acceptance Criteria

1. WHEN an assignment is created, updated, or deleted, THE Schedule System SHALL record the action with timestamp and editor identity
2. WHEN recording a change, THE Schedule System SHALL store both old and new values for comparison
3. WHEN an admin views assignment history, THE Schedule System SHALL display all changes in chronological order
4. WHEN viewing history, THE Schedule System SHALL show the reason provided for each change
5. THE Schedule System SHALL retain audit trail records for at least 12 months

### Requirement 6: Enhanced Auto-Assignment Algorithm

**User Story:** As an admin, I want the auto-assignment to distribute workload fairly, so that no member is overloaded or underutilized

#### Acceptance Criteria

1. WHEN generating assignments, THE Schedule System SHALL calculate a score for each user-slot combination based on availability, current workload, and preferences
2. WHEN assigning users to slots, THE Schedule System SHALL prioritize users with higher scores
3. WHEN all slots are filled, THE Schedule System SHALL ensure that the difference between maximum and minimum assignments per user does not exceed 2
4. IF a user has availability marked for a slot, THEN THE Schedule System SHALL add 100 points to their score for that slot
5. IF a user is not available for a slot, THEN THE Schedule System SHALL exclude them from consideration for that slot

### Requirement 7: Workload Balancing

**User Story:** As an admin, I want workload to be balanced across all members, so that the schedule is fair and sustainable

#### Acceptance Criteria

1. WHEN calculating assignment scores, THE Schedule System SHALL reduce a user's score by 10 points for each existing assignment they have
2. WHEN generating a schedule, THE Schedule System SHALL limit each user to a maximum of 4 assignments per week
3. WHEN generating a schedule, THE Schedule System SHALL ensure each active user receives at least 1 assignment if possible
4. WHEN workload distribution is complete, THE Schedule System SHALL calculate and display a fairness score
5. IF the fairness score is below 0.8, THEN THE Schedule System SHALL attempt to rebalance assignments

### Requirement 8: Consecutive Shift Prevention

**User Story:** As an admin, I want to avoid assigning users to consecutive shifts, so that members don't experience burnout

#### Acceptance Criteria

1. WHEN calculating assignment scores, THE Schedule System SHALL reduce a user's score by 20 points if they already have an assignment in an adjacent session on the same day
2. WHEN a user is assigned to Sesi 1, THE Schedule System SHALL deprioritize assigning them to Sesi 2 on the same day
3. WHEN a user is assigned to Sesi 2, THE Schedule System SHALL deprioritize assigning them to Sesi 3 on the same day
4. WHERE consecutive shifts are unavoidable due to availability constraints, THE Schedule System SHALL allow the assignment with a warning
5. WHEN displaying the schedule, THE Schedule System SHALL highlight consecutive shift assignments for admin review

### Requirement 9: Day Variety Optimization

**User Story:** As an admin, I want assignments spread across different days, so that members have variety in their schedules

#### Acceptance Criteria

1. WHEN calculating assignment scores, THE Schedule System SHALL add 10 points if assigning a user to a day they don't currently have assignments
2. WHEN generating assignments, THE Schedule System SHALL prioritize spreading each user's assignments across multiple days
3. WHEN all assignments are complete, THE Schedule System SHALL calculate the day variety distribution
4. THE Schedule System SHALL display a report showing how many users work on each day
5. IF a user must work multiple sessions, THEN THE Schedule System SHALL attempt to distribute them across different days first

### Requirement 10: Performance Optimization

**User Story:** As an admin, I want schedule generation to complete quickly, so that I can iterate and adjust schedules efficiently

#### Acceptance Criteria

1. WHEN generating a schedule with 12 slots, THE Schedule System SHALL complete the operation within 5 seconds
2. WHEN loading user availability data, THE Schedule System SHALL cache the results for 1 hour
3. WHEN calculating assignment scores, THE Schedule System SHALL use database indexes for date and user queries
4. WHEN displaying the schedule grid, THE Schedule System SHALL eager load all related user and availability data
5. WHEN an admin edits a schedule, THE Schedule System SHALL invalidate relevant caches to ensure data consistency

### Requirement 11: Real-Time Validation Feedback

**User Story:** As an admin, I want immediate feedback on conflicts and issues, so that I can correct problems before saving

#### Acceptance Criteria

1. WHEN an admin drags a user to a new slot, THE Schedule System SHALL validate the assignment in real-time
2. WHEN a conflict is detected, THE Schedule System SHALL display an error indicator on the affected slot immediately
3. WHEN hovering over a conflict indicator, THE Schedule System SHALL display details about the conflict
4. WHEN all conflicts are resolved, THE Schedule System SHALL enable the save button
5. WHILE conflicts exist, THE Schedule System SHALL disable the save button and display a summary of issues

### Requirement 12: Notification System

**User Story:** As a member, I want to receive notifications when my schedule changes, so that I'm always aware of my assignments

#### Acceptance Criteria

1. WHEN an assignment is added for a user, THE Schedule System SHALL send a notification with the date, session, and time details
2. WHEN an assignment is removed for a user, THE Schedule System SHALL send a notification with the removal reason
3. WHEN an assignment is swapped, THE Schedule System SHALL send notifications to both users with their new assignments
4. WHEN a schedule is published, THE Schedule System SHALL send notifications to all assigned users
5. THE Schedule System SHALL include session times in all notifications (Sesi 1: 07:30-10:20, Sesi 2: 10:20-12:50, Sesi 3: 13:30-16:00)

### Requirement 13: Interactive Schedule Grid

**User Story:** As an admin, I want an intuitive drag-and-drop interface, so that I can easily manage assignments visually

#### Acceptance Criteria

1. WHEN viewing the schedule grid, THE Schedule System SHALL display a 4-column layout representing Monday through Thursday
2. WHEN an admin drags an assignment card, THE Schedule System SHALL highlight valid drop zones
3. WHEN an assignment is dropped on a new slot, THE Schedule System SHALL update the assignment immediately
4. WHEN hovering over an assignment, THE Schedule System SHALL display user details and availability status
5. THE Schedule System SHALL use color coding to indicate assignment status (scheduled, completed, missed, swapped)

### Requirement 14: Configuration Management

**User Story:** As an admin, I want to configure scheduling rules and constraints, so that the system adapts to changing needs

#### Acceptance Criteria

1. THE Schedule System SHALL allow configuration of maximum assignments per user (default: 4)
2. THE Schedule System SHALL allow configuration of minimum coverage rate (default: 80%)
3. THE Schedule System SHALL allow configuration of scoring weights for availability, workload, and preferences
4. THE Schedule System SHALL allow configuration of consecutive shift penalty (default: 20 points)
5. WHEN configuration is updated, THE Schedule System SHALL apply the new rules to future schedule generations
