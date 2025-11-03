<?php

namespace App\Services;

use App\Models\Penalty;
use App\Models\PenaltyType;
use App\Models\User;
use Carbon\Carbon;

class PenaltyService
{
    /**
     * Create penalty for user
     */
    public function createPenalty(
        User $user,
        string $penaltyTypeCode,
        string $description,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?Carbon $date = null
    ): Penalty {
        $penaltyType = PenaltyType::where('code', $penaltyTypeCode)
            ->where('is_active', true)
            ->firstOrFail();

        $penalty = Penalty::create([
            'user_id' => $user->id,
            'penalty_type_id' => $penaltyType->id,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'points' => $penaltyType->points,
            'description' => $description,
            'date' => $date ?? now(),
            'status' => 'active',
        ]);

        // Notify user
        NotificationService::send(
            $user,
            'penalty_assigned',
            'Penalti Baru',
            "Anda mendapat penalti: {$penaltyType->name} ({$penaltyType->points} poin). {$description}",
            ['penalty_id' => $penalty->id],
            route('penalty.my-penalties')
        );

        // Check if threshold reached
        $this->checkThresholds($user);

        return $penalty;
    }

    /**
     * Get user total penalty points
     */
    public function getUserTotalPoints(User $user): int
    {
        return Penalty::where('user_id', $user->id)
            ->where('status', 'active')
            ->sum('points');
    }

    /**
     * Check penalty thresholds and take action
     */
    private function checkThresholds(User $user): void
    {
        $totalPoints = $this->getUserTotalPoints($user);

        $warningThreshold = (int) setting('penalty.warning_threshold', 20);
        $suspensionThreshold = (int) setting('penalty.suspension_threshold', 50);

        if ($totalPoints >= $suspensionThreshold && $user->status !== 'suspended') {
            // Suspend user
            $user->update(['status' => 'suspended']);

            NotificationService::send(
                $user,
                'account_suspended',
                'Akun Disuspend',
                "Akun Anda telah disuspend karena akumulasi penalti {$totalPoints} poin. Silakan hubungi administrator.",
                null,
                null
            );

            // Notify admins
            $admins = User::role(['Super Admin', 'Ketua', 'Wakil Ketua'])->get();
            foreach ($admins as $admin) {
                NotificationService::send(
                    $admin,
                    'user_suspended',
                    'User Disuspend',
                    "{$user->name} telah disuspend otomatis karena penalti {$totalPoints} poin.",
                    ['user_id' => $user->id],
                    route('users.index')
                );
            }

        } elseif ($totalPoints >= $warningThreshold) {
            // Warning notification
            NotificationService::send(
                $user,
                'penalty_warning',
                'Peringatan Penalti',
                "Total poin penalti Anda: {$totalPoints}. Batas suspend: {$suspensionThreshold} poin. Harap perhatikan kedisiplinan Anda.",
                null,
                route('penalty.my-penalties')
            );
        }
    }

    /**
     * Process penalty appeal
     */
    public function appealPenalty(Penalty $penalty, string $reason): bool
    {
        $penalty->update([
            'status' => 'appealed',
            'appeal_reason' => $reason,
            'appeal_status' => 'pending',
            'appealed_at' => now(),
        ]);

        // Notify admins
        $admins = User::role(['Super Admin', 'Ketua', 'Wakil Ketua'])->get();
        foreach ($admins as $admin) {
            NotificationService::send(
                $admin,
                'penalty_appeal',
                'Banding Penalti',
                "{$penalty->user->name} mengajukan banding untuk penalti {$penalty->penaltyType->name}.",
                ['penalty_id' => $penalty->id],
                route('penalty.list')
            );
        }

        return true;
    }
}
