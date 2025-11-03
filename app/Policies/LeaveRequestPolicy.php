<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view.leave.own') || $user->can('view.leave.all');
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->id === $leaveRequest->user_id ||
               $user->can('view.leave.all');
    }

    public function create(User $user): bool
    {
        return $user->can('create.leave.request') && $user->isActive();
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->id === $leaveRequest->user_id &&
               $leaveRequest->status === 'pending';
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->id === $leaveRequest->user_id &&
               $leaveRequest->status === 'pending';
    }

    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('approve.leave.request') &&
               $leaveRequest->status === 'pending';
    }
}
