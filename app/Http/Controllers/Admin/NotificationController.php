<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications (latest first), with unread count.
     */
    public function index()
    {
        $notifications = AdminNotification::latest()->take(20)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'icon' => $n->icon,
                'color' => $n->color,
                'link' => $n->link,
                'read' => $n->read,
                'time' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'unreadCount' => AdminNotification::where('read', false)->count(),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(AdminNotification $notification)
    {
        $notification->update(['read' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        AdminNotification::where('read', false)->update(['read' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * Clear all notifications.
     */
    public function clearAll()
    {
        AdminNotification::truncate();
        return response()->json(['success' => true]);
    }
}
