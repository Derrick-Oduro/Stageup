<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(10);

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->whereKey($id)->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect()->route('notifications.index');
    }
}
