<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(10);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('view', $notification);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function delete(Notification $notification)
    {
        $this->authorize('delete', $notification);
        $notification->delete();
        return response()->json(['success' => true]);
    }

    public function getUnread()
    {
        $user = Auth::user();
        $unread = $user->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($unread);
    }

    public function getCount()
    {
        $user = Auth::user();
        $count = $user->notifications()->whereNull('read_at')->count();
        return response()->json(['count' => $count]);
    }
}
