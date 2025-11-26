<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\User;
use App\Models\ScheduleAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * EnhancedAutoAssignmentService
 * 
 * Advanced algorithm for optimal multi-user schedule generation.
 * Implements scoring system with workload balancing, availability matching,
 * and conflict resolution for multi-user slots.
 * 
 * Features:
 * - Multi-user slot support (0+ users per slot)
 * - Configurable slot capacity and targets
 * - Fair workload distribution
 * - Availability-based scoring
 * - Consecutive shift prevention
 * - Day variety optimization
 * - Conflict detection and resolution
 * 
 * @package App\Services
 */
class EnhancedAutoAssignmentService
{
    protected ScheduleConfigurationService $configService;
    protected ConflictDetectionService $conflictService;

    /**
     * Constructor
     */
    public function __construct(
        ScheduleConfigurationService $configService,
        ConflictDetectionService $conflictService
    ) {
        $this->configService = $configService;
        $this->conflictService = $conflictService;
    }

    /**
     * Generate optimal schedule with multi-user slot support
     * 
     * @param Schedule $schedule
     * @param array $options Configuration options
     * @return array Generated assignments
     */
    public function generateOptimalSchedule(Schedule $schedule, array $options = []): array
    {
        Log::info('Starting enhanced auto-assignment', [
            'schedule_id' => $schedule->id,
            'week_start' => $schedule->week_start_date,
            'options' => $options,
        ]);

        // Merge options with defaults from configuration
        $options = $this->mergeOptions($options);

        // Step 1: Load users and slots
        $users = $this->getActiveUsersWithAvailability($schedule);
        $slots = $this->generateSlotGrid($schedule);

        if ($users->isEmpty()) {
            Log::warning('No active users available for scheduling');
            return [];
        }

        // Step 2: Calculate scores for all user-slot combinations
        $scores = $this->calculateSlotScores($users, $slots, []);

        // Step 3: Assign users to slots
        $assignments = $this->assignUsersToSlots($scores, $slots, $options);

        // Step 4: Balance workload
        $assignments = $this->balanceWorkload($assignments, $users, $options);

        // Step 5: Validate and fix conflicts
        $assignments = $this->validateAndFix($assignments);

        Log::info('Enhanced auto-assignment completed', [
            'schedule_id' => $schedule->id,
            'total_assignments' => count($assignments),
            'unique_users' => count(array_unique(array_column($assignments, 'user_id'))),
        ]);

        return $assignments;
    }

    /**
     * Merge user options with configuration defaults
     * 
     * @param array $options
     * @return array
     */
    protected function mergeOptions(array $options): array
    {
        return array_merge([
            'min_users_per_slot' => $this->configService->get('min_users_per_slot', 0),
            'max_users_per_slot' => $this->configService->get('max_users_per_slot'),
            'target_users_per_slot' => $this->configService->get('target_users_per_slot', 1),
            'allow_empty_slots' => $this->configService->get('allow_empty_slots', true),
            'prioritize_coverage' => $options['prioritize_coverage'] ?? true,
            'max_assignments_per_user' => $this->configService->get('max_assignments_per_user', 4),
            'min_assignments_per_user' => $this->configService->get('min_assignments_per_user', 1),
            'max_deviation' => 2, // Max difference between user workloads
        ], $options);
    }

    /**
     * Get active users with their availability data
     * Uses eager loading and caching for performance
     * 
     * @param Schedule $schedule
     * @return Collection
     */
    protected function getActiveUsersWithAvailability(Schedule $schedule): Collection
    {
        $cacheKey = "schedule_users_{$schedule->id}_{$schedule->week_start_date}";
        $cacheTTL = $this->configService->get('cache_ttl', 3600);

        if (!$this->configService->get('enable_caching', true)) {
            return $this->loadUsersWithAvailability($schedule);
        }

        return cache()->remember($cacheKey, $cacheTTL, function () use ($schedule) {
            return $this->loadUsersWithAvailability($schedule);
        });
    }

    /**
     * Load users with availability data from database
     * 
     * @param Schedule $schedule
     * @return Collection
     */
    protected function loadUsersWithAvailability(Schedule $schedule): Collection
    {
        return User::where('status', 'active')
            ->with([
                'availabilities' => function ($query) use ($schedule) {
                    $query->where('schedule_id', $schedule->id)
                        ->where('status', 'submitted')
                        ->with('details');
                },
            ])
            ->get()
            ->map(function ($user) use ($schedule) {
                // Build availability map for quick lookup
                $availabilityMap = [];
                
                $availability = $user->availabilities->first();
                if ($availability && $availability->details) {
                    foreach ($availability->details as $detail) {
                        $key = $detail->day . '_' . $detail->session;
                        $availabilityMap[$key] = $detail->is_available;
                    }
                }

                $user->availability_map = $availabilityMap;
                return $user;
            });
    }

    /**
     * Generate slot grid for the schedule
     * Creates 12 slots (4 days × 3 sessions)
     * 
     * @param Schedule $schedule
     * @return array
     */
    protected function generateSlotGrid(Schedule $schedule): array
    {
        $slots = [];
        $startDate = Carbon::parse($schedule->week_start_date);

        // 4 working days (Monday - Thursday)
        $days = ['monday', 'tuesday', 'wednesday', 'thursday'];
        
        for ($dayIndex = 0; $dayIndex < 4; $dayIndex++) {
            $date = $startDate->copy()->addDays($dayIndex);
            $dayName = $days[$dayIndex];

            // 3 sessions per day
            for ($session = 1; $session <= 3; $session++) {
                $slots[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $dayName,
                    'session' => $session,
                    'day_index' => $dayIndex,
                ];
            }
        }

        return $slots;
    }

    /**
     * Calculate scores for all user-slot combinations
     * 
     * @param Collection $users
     * @param array $slots
     * @param array $currentAssignments
     * @return array
     */
    public function calculateSlotScores(Collection $users, array $slots, array $currentAssignments = []): array
    {
        $scores = [];

        foreach ($slots as $slot) {
            $slotKey = $slot['date'] . '_' . $slot['session'];
            $scores[$slotKey] = [];

            $currentSlotUserCount = $this->getSlotUserCount($slot['date'], $slot['session'], $currentAssignments);

            foreach ($users as $user) {
                $score = $this->calculateUserScore($user, $slot, $currentAssignments, $currentSlotUserCount);

                // Only include users with positive scores
                if ($score > 0) {
                    $scores[$slotKey][] = [
                        'user' => $user,
                        'user_id' => $user->id,
                        'score' => $score,
                    ];
                }
            }

            // Sort by score descending
            usort($scores[$slotKey], function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });
        }

        return $scores;
    }

    /**
     * Calculate score for a specific user-slot combination
     * Implements multi-factor scoring algorithm
     * 
     * @param User $user
     * @param array $slot
     * @param array $currentAssignments
     * @param int $currentSlotUserCount
     * @return int
     */
    public function calculateUserScore(User $user, array $slot, array $currentAssignments, int $currentSlotUserCount): int
    {
        $score = 0;

        // Factor 1: Availability (most important)
        if (!$this->isUserAvailable($user, $slot['day'], $slot['session'])) {
            return -1000; // Exclude unavailable users
        }
        $score += $this->configService->get('availability_match_score', 100);

        // Factor 2: Already in slot (prevent duplicates)
        if ($this->isUserInSlot($user->id, $slot['date'], $slot['session'], $currentAssignments)) {
            return -2000; // Exclude users already in this slot
        }

        // Factor 3: Current workload (balance)
        $assignmentCount = $this->countUserAssignments($user->id, $currentAssignments);
        $workloadPenalty = $this->configService->get('workload_penalty_score', 10);
        $score -= ($assignmentCount * $workloadPenalty);

        // Factor 4: Consecutive shifts (avoid burnout)
        if ($this->hasConsecutiveShift($user->id, $slot['date'], $slot['session'], $currentAssignments)) {
            $consecutivePenalty = $this->configService->get('consecutive_penalty_score', 20);
            $score -= $consecutivePenalty;
        }

        // Factor 5: Day variety (spread across days)
        $userDays = $this->getUserAssignedDays($user->id, $currentAssignments);
        if (!in_array($slot['day'], $userDays)) {
            $dayVarietyBonus = $this->configService->get('day_variety_bonus_score', 10);
            $score += $dayVarietyBonus;
        }

        // Factor 6: Slot coverage (prioritize empty slots)
        if ($currentSlotUserCount == 0) {
            $slotCoverageBonus = $this->configService->get('slot_coverage_bonus', 30);
            $score += $slotCoverageBonus;
        }

        // Factor 7: Preference bonus (if implemented in future)
        // This can be extended to include user preferences
        // if ($this->hasPreference($user, $slot)) {
        //     $score += $this->configService->get('preference_bonus_score', 50);
        // }

        return $score;
    }

    /**
     * Assign users to slots based on calculated scores
     * Main assignment logic with multi-user slot support
     * 
     * @param array $scores
     * @param array $slots
     * @param array $options
     * @return array
     */
    public function assignUsersToSlots(array $scores, array $slots, array $options): array
    {
        $assignments = [];
        $maxAssignmentsPerUser = $options['max_assignments_per_user'];
        $targetUsersPerSlot = $options['target_users_per_slot'];
        $maxUsersPerSlot = $options['max_users_per_slot'];
        $allowEmptySlots = $options['allow_empty_slots'];
        $prioritizeCoverage = $options['prioritize_coverage'];

        // Sort slots by priority
        $sortedSlots = $this->sortSlotsByPriority($slots, $scores, $prioritizeCoverage);

        foreach ($sortedSlots as $slot) {
            $slotKey = $slot['date'] . '_' . $slot['session'];
            $currentSlotUserCount = $this->getSlotUserCount($slot['date'], $slot['session'], $assignments);

            // Determine how many users to add to this slot
            $usersToAdd = $targetUsersPerSlot - $currentSlotUserCount;

            if ($maxUsersPerSlot !== null) {
                $usersToAdd = min($usersToAdd, $maxUsersPerSlot - $currentSlotUserCount);
            }

            if ($usersToAdd <= 0) {
                continue; // Slot already has enough users
            }

            // Get available users for this slot
            $availableUsers = $scores[$slotKey] ?? [];
            $availableUsers = array_filter($availableUsers, function ($userScore) use ($assignments, $maxAssignmentsPerUser, $slot) {
                $userId = $userScore['user_id'];
                $userAssignmentCount = $this->countUserAssignments($userId, $assignments);
                $alreadyInSlot = $this->isUserInSlot($userId, $slot['date'], $slot['session'], $assignments);

                return $userAssignmentCount < $maxAssignmentsPerUser && !$alreadyInSlot;
            });

            if (empty($availableUsers)) {
                if (!$allowEmptySlots && $currentSlotUserCount == 0) {
                    Log::warning('Cannot fill required slot', [
                        'date' => $slot['date'],
                        'session' => $slot['session'],
                    ]);
                }
                continue;
            }

            // Add users to slot (up to target or max)
            $addedCount = 0;
            foreach ($availableUsers as $userScore) {
                if ($addedCount >= $usersToAdd) {
                    break;
                }

                $assignments[] = [
                    'user_id' => $userScore['user_id'],
                    'date' => $slot['date'],
                    'session' => $slot['session'],
                    'day' => $slot['day'],
                    'score' => $userScore['score'],
                ];

                $addedCount++;

                // Recalculate scores for remaining users if needed
                // This ensures dynamic scoring as slot fills up
            }
        }

        return $assignments;
    }

    /**
     * Balance workload across users
     * Redistributes assignments for fairness
     * 
     * @param array $assignments
     * @param Collection $users
     * @param array $options
     * @return array
     */
    public function balanceWorkload(array $assignments, Collection $users, array $options): array
    {
        if (empty($assignments)) {
            return $assignments;
        }

        $maxDeviation = $options['max_deviation'];
        $maxIterations = $this->configService->get('max_algorithm_iterations', 1000);
        $iteration = 0;

        // Calculate workload distribution
        $userCounts = [];
        foreach ($assignments as $assignment) {
            $userId = $assignment['user_id'];
            $userCounts[$userId] = ($userCounts[$userId] ?? 0) + 1;
        }

        // Calculate average assignments per user
        $totalAssignments = count($assignments);
        $activeUserCount = $users->count();
        $avgAssignments = $activeUserCount > 0 ? $totalAssignments / $activeUserCount : 0;

        // Identify overloaded and underloaded users
        $overloaded = [];
        $underloaded = [];

        foreach ($users as $user) {
            $count = $userCounts[$user->id] ?? 0;
            
            if ($count > $avgAssignments + $maxDeviation) {
                $overloaded[$user->id] = [
                    'user' => $user,
                    'count' => $count,
                    'excess' => $count - ($avgAssignments + $maxDeviation),
                ];
            } elseif ($count < $avgAssignments - $maxDeviation) {
                $underloaded[$user->id] = [
                    'user' => $user,
                    'count' => $count,
                    'deficit' => ($avgAssignments - $maxDeviation) - $count,
                ];
            }
        }

        // Redistribute assignments
        while (!empty($overloaded) && !empty($underloaded) && $iteration < $maxIterations) {
            $iteration++;
            $madeChange = false;

            foreach ($overloaded as $overloadedUserId => $overloadedData) {
                if ($overloadedData['excess'] <= 0) {
                    unset($overloaded[$overloadedUserId]);
                    continue;
                }

                // Find an assignment to move
                $movableAssignment = $this->findMovableAssignment($overloadedUserId, $assignments, $underloaded);

                if ($movableAssignment === null) {
                    continue;
                }

                // Find suitable underloaded user for this assignment
                $newUserId = $this->findSuitableUser($movableAssignment, $underloaded, $assignments);

                if ($newUserId === null) {
                    continue;
                }

                // Move the assignment
                foreach ($assignments as &$assignment) {
                    if ($assignment === $movableAssignment) {
                        $assignment['user_id'] = $newUserId;
                        $assignment['balanced'] = true;
                        $madeChange = true;

                        // Update counts
                        $overloaded[$overloadedUserId]['count']--;
                        $overloaded[$overloadedUserId]['excess']--;
                        
                        if (isset($underloaded[$newUserId])) {
                            $underloaded[$newUserId]['count']++;
                            $underloaded[$newUserId]['deficit']--;
                            
                            if ($underloaded[$newUserId]['deficit'] <= 0) {
                                unset($underloaded[$newUserId]);
                            }
                        }

                        break;
                    }
                }

                if ($overloaded[$overloadedUserId]['excess'] <= 0) {
                    unset($overloaded[$overloadedUserId]);
                }

                if ($madeChange) {
                    break; // Restart the loop with updated data
                }
            }

            if (!$madeChange) {
                break; // No more changes possible
            }
        }

        Log::info('Workload balancing completed', [
            'iterations' => $iteration,
            'remaining_overloaded' => count($overloaded),
            'remaining_underloaded' => count($underloaded),
        ]);

        return $assignments;
    }

    /**
     * Find a movable assignment for an overloaded user
     * 
     * @param int $userId
     * @param array $assignments
     * @param array $underloaded
     * @return array|null
     */
    protected function findMovableAssignment(int $userId, array $assignments, array $underloaded): ?array
    {
        $userAssignments = array_filter($assignments, function ($assignment) use ($userId) {
            return $assignment['user_id'] === $userId;
        });

        // Prefer assignments with lower scores (less optimal for this user)
        usort($userAssignments, function ($a, $b) {
            return $a['score'] <=> $b['score'];
        });

        return $userAssignments[0] ?? null;
    }

    /**
     * Find a suitable underloaded user for an assignment
     * 
     * @param array $assignment
     * @param array $underloaded
     * @param array $currentAssignments
     * @return int|null
     */
    protected function findSuitableUser(array $assignment, array $underloaded, array $currentAssignments): ?int
    {
        foreach ($underloaded as $userId => $data) {
            $user = $data['user'];

            // Check if user is available for this slot
            if (!$this->isUserAvailable($user, $assignment['day'], $assignment['session'])) {
                continue;
            }

            // Check if user is already in this slot
            if ($this->isUserInSlot($userId, $assignment['date'], $assignment['session'], $currentAssignments)) {
                continue;
            }

            // Check for consecutive shifts
            if ($this->hasConsecutiveShift($userId, $assignment['date'], $assignment['session'], $currentAssignments)) {
                // Allow but deprioritize
                continue;
            }

            return $userId;
        }

        return null;
    }

    /**
     * Validate and fix conflicts in assignments
     * Final validation pass before returning
     * 
     * @param array $assignments
     * @return array
     */
    public function validateAndFix(array $assignments): array
    {
        if (empty($assignments)) {
            return $assignments;
        }

        // Detect conflicts
        $conflicts = $this->detectConflicts($assignments);

        if (empty($conflicts)) {
            Log::info('No conflicts detected in assignments');
            return $assignments;
        }

        Log::warning('Conflicts detected, attempting to resolve', [
            'conflict_count' => count($conflicts),
        ]);

        // Resolve each conflict
        foreach ($conflicts as $conflict) {
            $assignments = $this->resolveConflict($conflict, $assignments);
        }

        // Re-check for conflicts after resolution
        $remainingConflicts = $this->detectConflicts($assignments);

        if (!empty($remainingConflicts)) {
            Log::warning('Some conflicts could not be resolved', [
                'remaining_conflicts' => count($remainingConflicts),
            ]);
        }

        return $assignments;
    }

    /**
     * Detect conflicts in assignments array
     * 
     * @param array $assignments
     * @return array
     */
    public function detectConflicts(array $assignments): array
    {
        $conflicts = [];

        // Check for duplicate users in same slot
        $slotUsers = [];
        foreach ($assignments as $index => $assignment) {
            $slotKey = $assignment['date'] . '_' . $assignment['session'];
            $userId = $assignment['user_id'];

            if (!isset($slotUsers[$slotKey])) {
                $slotUsers[$slotKey] = [];
            }

            if (in_array($userId, $slotUsers[$slotKey])) {
                $conflicts[] = [
                    'type' => 'duplicate_user_in_slot',
                    'assignment_index' => $index,
                    'user_id' => $userId,
                    'date' => $assignment['date'],
                    'session' => $assignment['session'],
                ];
            } else {
                $slotUsers[$slotKey][] = $userId;
            }
        }

        // Check for inactive users (if user data is available)
        // This would require loading user data, which we'll skip for performance
        // The ConflictDetectionService will handle this after assignments are saved

        return $conflicts;
    }

    /**
     * Resolve a specific conflict
     * 
     * @param array $conflict
     * @param array $assignments
     * @return array
     */
    public function resolveConflict(array $conflict, array $assignments): array
    {
        switch ($conflict['type']) {
            case 'duplicate_user_in_slot':
                // Remove the duplicate assignment
                $index = $conflict['assignment_index'];
                if (isset($assignments[$index])) {
                    Log::info('Removing duplicate assignment', [
                        'user_id' => $conflict['user_id'],
                        'date' => $conflict['date'],
                        'session' => $conflict['session'],
                    ]);
                    unset($assignments[$index]);
                    $assignments = array_values($assignments); // Re-index array
                }
                break;

            case 'inactive_user':
                // Remove assignment with inactive user
                $index = $conflict['assignment_index'];
                if (isset($assignments[$index])) {
                    Log::info('Removing assignment for inactive user', [
                        'user_id' => $conflict['user_id'],
                    ]);
                    unset($assignments[$index]);
                    $assignments = array_values($assignments);
                }
                break;

            default:
                Log::warning('Unknown conflict type', ['type' => $conflict['type']]);
        }

        return $assignments;
    }

    /**
     * Check if user is available for a specific slot
     * 
     * @param User $user
     * @param string $day Day name (monday, tuesday, etc.)
     * @param int $session Session number (1, 2, 3)
     * @return bool
     */
    protected function isUserAvailable(User $user, string $day, int $session): bool
    {
        $key = $day . '_' . $session;
        
        // Check availability map
        if (isset($user->availability_map[$key])) {
            return $user->availability_map[$key] === true;
        }

        // If no availability data, assume unavailable
        return false;
    }

    /**
     * Count total assignments for a user
     * 
     * @param int $userId
     * @param array $assignments
     * @return int
     */
    protected function countUserAssignments(int $userId, array $assignments): int
    {
        return count(array_filter($assignments, function ($assignment) use ($userId) {
            return $assignment['user_id'] === $userId;
        }));
    }

    /**
     * Check if user has consecutive shift
     * 
     * @param int $userId
     * @param string $date
     * @param int $session
     * @param array $assignments
     * @return bool
     */
    protected function hasConsecutiveShift(int $userId, string $date, int $session, array $assignments): bool
    {
        // Check for adjacent sessions on the same day
        $adjacentSessions = [];
        
        if ($session == 1) {
            $adjacentSessions = [2]; // Sesi 1 is adjacent to Sesi 2
        } elseif ($session == 2) {
            $adjacentSessions = [1, 3]; // Sesi 2 is adjacent to both Sesi 1 and 3
        } elseif ($session == 3) {
            $adjacentSessions = [2]; // Sesi 3 is adjacent to Sesi 2
        }

        foreach ($assignments as $assignment) {
            if ($assignment['user_id'] === $userId && 
                $assignment['date'] === $date && 
                in_array($assignment['session'], $adjacentSessions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get days where user is already assigned
     * 
     * @param int $userId
     * @param array $assignments
     * @return array
     */
    protected function getUserAssignedDays(int $userId, array $assignments): array
    {
        $days = [];

        foreach ($assignments as $assignment) {
            if ($assignment['user_id'] === $userId) {
                $days[] = $assignment['day'];
            }
        }

        return array_unique($days);
    }

    /**
     * Count users in a specific slot
     * 
     * @param string $date
     * @param int $session
     * @param array $assignments
     * @return int
     */
    protected function getSlotUserCount(string $date, int $session, array $assignments): int
    {
        return count(array_filter($assignments, function ($assignment) use ($date, $session) {
            return $assignment['date'] === $date && $assignment['session'] === $session;
        }));
    }

    /**
     * Check if user is already in a specific slot
     * 
     * @param int $userId
     * @param string $date
     * @param int $session
     * @param array $assignments
     * @return bool
     */
    protected function isUserInSlot(int $userId, string $date, int $session, array $assignments): bool
    {
        foreach ($assignments as $assignment) {
            if ($assignment['user_id'] === $userId && 
                $assignment['date'] === $date && 
                $assignment['session'] === $session) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sort slots by priority for assignment
     * 
     * @param array $slots
     * @param array $scores
     * @param bool $prioritizeCoverage
     * @return array
     */
    protected function sortSlotsByPriority(array $slots, array $scores, bool $prioritizeCoverage): array
    {
        if ($prioritizeCoverage) {
            // Sort by number of available users (ascending)
            // This ensures we fill harder-to-fill slots first
            usort($slots, function ($a, $b) use ($scores) {
                $keyA = $a['date'] . '_' . $a['session'];
                $keyB = $b['date'] . '_' . $b['session'];
                
                $countA = count($scores[$keyA] ?? []);
                $countB = count($scores[$keyB] ?? []);
                
                return $countA <=> $countB;
            });
        } else {
            // Keep original order (chronological)
            // This balances workload from the start
        }

        return $slots;
    }

    /**
     * Calculate statistics for generated assignments
     * 
     * @param array $assignments
     * @param Collection $users
     * @return array
     */
    public function calculateStatistics(array $assignments, Collection $users): array
    {
        if (empty($assignments)) {
            return [
                'total_assignments' => 0,
                'unique_users' => 0,
                'filled_slots' => 0,
                'empty_slots' => 12,
                'coverage_rate' => 0,
                'avg_assignments_per_user' => 0,
                'min_assignments' => 0,
                'max_assignments' => 0,
                'fairness_score' => 0,
            ];
        }

        // Count assignments per user
        $userCounts = [];
        foreach ($assignments as $assignment) {
            $userId = $assignment['user_id'];
            $userCounts[$userId] = ($userCounts[$userId] ?? 0) + 1;
        }

        // Count filled slots
        $filledSlots = [];
        foreach ($assignments as $assignment) {
            $slotKey = $assignment['date'] . '_' . $assignment['session'];
            $filledSlots[$slotKey] = true;
        }

        $totalSlots = 12; // 4 days × 3 sessions
        $filledSlotCount = count($filledSlots);
        $emptySlotCount = $totalSlots - $filledSlotCount;

        // Calculate workload statistics
        $counts = array_values($userCounts);
        $minAssignments = !empty($counts) ? min($counts) : 0;
        $maxAssignments = !empty($counts) ? max($counts) : 0;
        $avgAssignments = !empty($counts) ? array_sum($counts) / count($counts) : 0;

        // Calculate fairness score (1.0 = perfectly balanced)
        $fairnessScore = 0;
        if ($maxAssignments > 0) {
            $deviation = $maxAssignments - $minAssignments;
            $fairnessScore = max(0, 1 - ($deviation / $maxAssignments));
        }

        return [
            'total_assignments' => count($assignments),
            'unique_users' => count($userCounts),
            'filled_slots' => $filledSlotCount,
            'empty_slots' => $emptySlotCount,
            'coverage_rate' => ($filledSlotCount / $totalSlots) * 100,
            'avg_assignments_per_user' => round($avgAssignments, 2),
            'min_assignments' => $minAssignments,
            'max_assignments' => $maxAssignments,
            'fairness_score' => round($fairnessScore, 2),
            'workload_distribution' => $userCounts,
        ];
    }
}
