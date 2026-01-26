<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogService;

class LogoutController extends Controller
{
    /**
     * Handle user logout
     */
    public function logout(Request $request): RedirectResponse
    {
        // Log activity before logout (while user is still authenticated)
        ActivityLogService::logLogout();
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
