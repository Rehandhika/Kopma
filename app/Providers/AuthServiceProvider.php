<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Schedule;
use App\Models\SwapRequest;
use App\Models\LeaveRequest;
use App\Models\Penalty;
use App\Policies\SchedulePolicy;
use App\Policies\SwapRequestPolicy;
use App\Policies\LeaveRequestPolicy;
use App\Policies\PenaltyPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Schedule::class => SchedulePolicy::class,
        SwapRequest::class => SwapRequestPolicy::class,
        LeaveRequest::class => LeaveRequestPolicy::class,
        Penalty::class => PenaltyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
