<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class OperatorNotificationController extends Controller
{
    private const TYPES = [
        'new_shipment',
        'shipment_status_update',
        'tracking_update',
        'shipment_delayed',
        'new_exception',
        'high_severity_exception',
        'exception_update',
        'exception_resolved',
    ];

    /**
     * Menampilkan notifikasi milik Operator.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->notifications()->latest();

        // Pencarian judul, pesan, dan nomor pengiriman.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('data', 'LIKE', "%{$search}%");
            });
        }

        // Filter jenis notifikasi.
        if ($request->filled('type')) {
            $type = $request->type;

            $query->where(function ($q) use ($type) {
                $q->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(data, '$.notification_type')) = ?",
                    [$type]
                )->orWhereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(data, '$.type')) = ?",
                    [$type]
                );
            });
        }

        // Filter status dibaca.
        if ($request->status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->status === 'read') {
            $query->whereNotNull('read_at');
        }

        // Filter tingkat keparahan.
        if ($request->filled('severity')) {
            $query->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(data, '$.severity')) = ?",
                [$request->severity]
            );
        }

        // Filter tanggal.
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $query->paginate(10)->withQueryString();

        // Ringkasan notifikasi milik Operator.
        $allNotifications = $user->notifications();

        $stats = [
            'total' => (clone $allNotifications)->count(),
            'unread' => (clone $allNotifications)->whereNull('read_at')->count(),
            'read' => (clone $allNotifications)->whereNotNull('read_at')->count(),
            'urgent' => (clone $allNotifications)
                ->whereNull('read_at')
                ->where(function ($q) {
                    $q->whereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(data, '$.severity')) = 'HIGH'"
                    )->orWhereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(data, '$.priority')) IN ('HIGH', 'URGENT')"
                    );
                })
                ->count(),
        ];

        return view('operator.notifikasi.index', compact(
            'notifications',
            'stats'
        ));
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        $data = $notification->data;

        if (!empty($data['url'])) {
            return redirect()->to($data['url']);
        }

        if (!empty($data['exception_id'])) {
            return redirect()->route('operator.exceptions.show', $data['exception_id']);
        }

        if (!empty($data['shipment_id'])) {
            return redirect()->route('operator.pelacakan.show', $data['shipment_id']);
        }

        return redirect()
            ->route('operator.notifications.index')
            ->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Menandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        Auth::user()
            ->unreadNotifications
            ->each(function ($notification) {
                $notification->markAsRead();
            });

        return redirect()
            ->route('operator.notifications.index')
            ->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Menghapus satu notifikasi milik Operator.
     */
    public function destroy(string $id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return redirect()
            ->route('operator.notifications.index')
            ->with('success', 'Notifikasi berhasil dihapus.');
    }
}
