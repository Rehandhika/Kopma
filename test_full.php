<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\ScheduleChangeRequest;
use App\Models\ScheduleAssignment;
use Illuminate\Support\Facades\Auth;

// Login as first user
$user = User::first();
if ($user) {
    Auth::login($user);
    echo "Logged in as: " . $user->name . "\n";
    echo "User ID: " . $user->id . "\n";
    
    // Check if user has any assignments
    $assignments = ScheduleAssignment::where('user_id', $user->id)
        ->where('date', '>=', now()->format('Y-m-d'))
        ->count();
    echo "Future assignments: " . $assignments . "\n";
    
    // Check schedule change requests
    $requests = ScheduleChangeRequest::where('user_id', $user->id)->count();
    echo "Schedule change requests: " . $requests . "\n";
    
    // Try to instantiate and render component
    $component = new \App\Livewire\Schedule\ScheduleChangeManager();
    $component->mount();
    
    echo "\nComponent state:\n";
    echo "- activeTab: " . $component->activeTab . "\n";
    echo "- statusFilter: " . $component->statusFilter . "\n";
    echo "- showForm: " . ($component->showForm ? 'true' : 'false') . "\n";
    
    // Test render
    try {
        $view = $component->render();
        echo "\nView rendered successfully!\n";
        echo "View name: " . $view->name() . "\n";
    } catch (Exception $e) {
        echo "\nError rendering view: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "No user found in database\n";
}
