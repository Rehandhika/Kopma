<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;

class SchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view.schedule.all');
    }

    public function view(User $user, Schedule $schedule): bool
    {
        return $user->can('view.schedule.all') || $user->can('view.schedule.own');
    }

    public function create(User $user): bool
    {
        return $user->can('manage.schedule');
    }

    public function update(User $user, Schedule $schedule): bool
    {
        return $user->can('manage.schedule') && $schedule->canEdit();
    }

    public function delete(User $user, Schedule $schedule): bool
    {
        return $user->can('manage.schedule') && $schedule->isDraft();
    }

    public function generate(User $user): bool
    {
        return $user->can('generate.schedule');
    }

    public function publish(User $user, Schedule $schedule): bool
    {
        return $user->can('manage.schedule') && $schedule->isDraft();
    }
}
