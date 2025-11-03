<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\ScheduleAssignment;
use App\Models\Penalty;
use App\Models\Notification;
use App\Services\NotificationService;
use Carbon\Carbon;

class Index extends Component
{
    public $todaySchedule;
    public $upcomingSchedules;
    public $recentPenalties;
    public $unreadNotifications;
    public $penaltyPoints;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = auth()->user();

        // Today's schedule
        $this->todaySchedule = ScheduleAssignment::where('user_id', $user->id)
            ->where('date', today())
            ->where('status', 'scheduled')
            ->first();

        // Upcoming schedules (next 7 days)
        $this->upcomingSchedules = ScheduleAssignment::where('user_id', $user->id)
            ->whereBetween('date', [today(), today()->addDays(7)])
            ->where('status', 'scheduled')
            ->orderBy('date')
            ->orderBy('time_start')
            ->get();

        // Recent penalties
        $this->recentPenalties = Penalty::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        // Unread notifications
        $this->unreadNotifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Total penalty points
        $this->penaltyPoints = Penalty::where('user_id', $user->id)
            ->where('status', 'active')
            ->sum('points');
    }

    public function markNotificationAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === auth()->id()) {
            NotificationService::markAsRead($notification);
            $this->loadData();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.index')
            ->layout('layouts.app');
    }
}
