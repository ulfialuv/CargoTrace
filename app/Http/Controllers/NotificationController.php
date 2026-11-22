<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('data->title', 'like', "%{$search}%")
                      ->orWhere('data->message', 'like', "%{$search}%")
                      ->orWhere('data->shipment_number', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('data->type', $request->type);
            })
            ->when($request->status === 'unread', function ($query) {
                $query->whereNull('read_at');
            })
            ->when($request->status === 'read', function ($query) {
                $query->whereNotNull('read_at');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalNotifications = $user->notifications()->count();
        $unreadNotifications = $user->unreadNotifications()->count();
        $readNotifications = $user->readNotifications()->count();

        $importantNotifications = $user->notifications()
            ->where('data->important', true)
            ->count();

        return view('notifications.index', compact(
            'notifications',
            'totalNotifications',
            'unreadNotifications',
            'readNotifications',
            'importantNotifications'
        ));
    }

    public function markRead(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return back()->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    public function destroy(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
