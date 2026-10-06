<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->with('profile');

        // Search by name or email
        if ($q = trim((string) $request->input('q'))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            if (in_array($role, array_column(UserRole::cases(), 'value'), true)) {
                $query->where('role', $role);
            }
        }

        // Filter by status
        if ($status = $request->input('status')) {
            if ($status === 'active')   $query->where('is_active', true);
            if ($status === 'inactive') $query->where('is_active', false);
        }

        $users = $query
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users'   => $users,
            'filters' => [
                'q'      => $request->input('q'),
                'role'   => $request->input('role'),
                'status' => $request->input('status'),
            ],
            'roles' => UserRole::cases(),
        ]);
    }

    public function show(User $user): View
    {
        $user->load('profile');

        $counts = [
            'income'    => $user->income()->count(),
            'expense'   => $user->expenses()->count(),
            'budgets'   => $user->budgets()->count(),
            'goals'     => $user->financialGoals()->count(),
            'recurring' => $user->recurringTransactions()->count(),
        ];

        return view('admin.users.show', [
            'user'   => $user,
            'counts' => $counts,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        // Prevent admin from deactivating or demoting themselves
        if ($user->id === $request->user()->id) {
            if (! $request->boolean('is_active')) {
                return back()->with('error', 'You cannot deactivate your own account.');
            }
            if ($request->input('role') !== UserRole::Admin->value) {
                return back()->with('error', 'You cannot demote your own admin role.');
            }
        }

        $user->update([
            'name'      => $request->input('name'),
            'role'      => $request->input('role'),
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLogger::log(
            'admin.user.updated',
            $user,
            'Admin updated user ' . $user->email
        );

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        ActivityLogger::log(
            $user->is_active ? 'admin.user.activated' : 'admin.user.deactivated',
            $user,
            'Admin ' . ($user->is_active ? 'activated' : 'deactivated') . ' user ' . $user->email
        );

        return back()->with(
            'success',
            $user->is_active ? 'User activated.' : 'User deactivated.'
        );
    }
}