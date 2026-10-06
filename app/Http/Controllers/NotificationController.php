<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $filter = $request->input('filter', 'all');

        $query = $user->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(20)->withQueryString();

        return view('notifications.index', [
            'notifications' => $notifications,
            'filter'        => $filter,
            'unreadCount'   => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $target = $notification->data['url'] ?? route('notifications.index');

        return redirect($target);
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Manual trigger — for dev/testing without waiting for the scheduler.
     */
    public function scanNow(Request $request, NotificationService $service): RedirectResponse
    {
        $user = $request->user();

        $w = $service->scanBudgets($user);
        $d = $service->scanGoals($user);

        $total = $w['warning'] + $w['exceeded'] + $d;

        return back()->with(
            $total > 0 ? 'success' : 'info',
            $total > 0
                ? "Created {$total} new notification(s)."
                : 'No new notifications right now.'
        );
    }
}