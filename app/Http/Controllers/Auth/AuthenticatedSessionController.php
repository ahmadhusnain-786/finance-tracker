<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Record last login time + activity log
        $user = Auth::user();
        if ($user) {
            $user->forceFill(['last_login_at' => now()])->save();
            ActivityLogger::log('auth.login', $user, 'Login from ' . request()->ip());
        }

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogger::log('auth.logout', $user, 'Logout');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'You have been logged out.');
    }
}