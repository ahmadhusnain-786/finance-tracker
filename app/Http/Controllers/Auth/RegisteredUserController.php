<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'     => $request->string('name'),
            'email'    => $request->string('email')->lower(),
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));

        ActivityLogger::log('auth.register', $user, 'New registration', $user->id);

        Auth::login($user);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Welcome, ' . $user->name . '! Your account is ready.');
    }
}