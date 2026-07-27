<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller Notifikasi
 * Mengelola notifikasi pengguna: menampilkan, menandai dibaca, dan menandai semua dibaca.
 */
class NotificationController extends Controller
{
    /**
     * Mengambil semua notifikasi untuk pengguna yang sedang login.
     * Juga menjalankan pengecekan jatuh tempo secara dinamis.
     */
    public function index()
    {
        // 1. Jalankan pengecekan dinamis untuk jatuh tempo yang mendesak
        Notification::checkJatuhTempoReminders();

        // 2. Ambil semua notifikasi milik pengguna yang login
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

        // Hitung jumlah notifikasi yang belum dibaca
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead($id)
    {
        // Cari notifikasi milik pengguna yang login
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json([
            'message'      => 'Notifikasi ditandai telah dibaca.',
            'unread_count' => Notification::where('user_id', Auth::id())->where('is_read', false)->count(),
        ]);
    }

    /**
     * Menandai semua notifikasi pengguna sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        // Update semua notifikasi yang belum dibaca menjadi sudah dibaca
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message'      => 'Semua notifikasi ditandai telah dibaca.',
            'unread_count' => 0,
        ]);
    }
}
