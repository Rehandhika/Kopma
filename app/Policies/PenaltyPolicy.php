<?php

namespace App\Policies;

use App\Models\Penalty;
use App\Models\User;

class PenaltyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view.penalty.own') || $user->can('view.penalty.all');
    }

    public function view(User $user, Penalty $penalty): bool
    {
        return $user->id === $penalty->user_id ||
               $user->can('view.penalty.all');
    }

    public function appeal(User $user, Penalty $penalty): bool
    {
        return $user->id === $penalty->user_id &&
               $user->can('appeal.penalty') &&
               $penalty->status === 'active';
    }

    public function reviewAppeal(User $user, Penalty $penalty): bool
    {
        return $user->can('manage.penalty') &&
               $penalty->status === 'appealed';
    }

    public function dismiss(User $user, Penalty $penalty): bool
    {
        return $user->can('manage.penalty');
    }
}
