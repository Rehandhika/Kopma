<?php

namespace App\Policies;

use App\Models\SwapRequest;
use App\Models\User;

class SwapRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('create.swap.request');
    }

    public function view(User $user, SwapRequest $swapRequest): bool
    {
        return $user->id === $swapRequest->requester_id ||
               $user->id === $swapRequest->target_id ||
               $user->can('view.swap.all');
    }

    public function create(User $user): bool
    {
        return $user->can('create.swap.request') && $user->isActive();
    }

    public function targetRespond(User $user, SwapRequest $swapRequest): bool
    {
        return $user->id === $swapRequest->target_id &&
               $swapRequest->status === 'pending' &&
               $user->can('approve.swap.target');
    }

    public function adminRespond(User $user, SwapRequest $swapRequest): bool
    {
        return $user->can('approve.swap.admin') &&
               $swapRequest->status === 'target_approved';
    }

    public function cancel(User $user, SwapRequest $swapRequest): bool
    {
        return $user->id === $swapRequest->requester_id &&
               $swapRequest->status === 'pending';
    }
}
