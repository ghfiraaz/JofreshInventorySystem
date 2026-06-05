<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index()
    {
        // 1. Run dynamic checks for due dates
        Notification::checkJatuhTempoReminders();

        // 2. Fetch notifications for current user
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($notif) {
                return [
                    'id'          => $notif->id,
                    'title'       => $notif->title,
                    'message'     => $notif->message,
                    'type'        => $notif->type,
                    'is_read'     => $notif->is_read,
                    'time_ago'    => $notif->time_ago,
                    'created_at'  => $notif->created_at->toISOString(),
                ];
            });

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json([
            'message'      => 'Notifikasi ditandai telah dibaca.',
            'unread_count' => Notification::where('user_id', Auth::id())->where('is_read', false)->count(),
        ]);
    }

    /**
     * Mark all notifications of the user as read.
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message'      => 'Semua notifikasi ditandai telah dibaca.',
            'unread_count' => 0,
        ]);
    }
}
