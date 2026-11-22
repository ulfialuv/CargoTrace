<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerNotificationController extends Controller
{
    /**
     * Daftar, pencarian, filter, dan detail notifikasi Customer.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Selalu hanya notifikasi milik Customer yang sedang login.
        $all = $user->notifications()->latest()->get();

        // Ubah setiap notifikasi menjadi array siap tampil (judul, ikon, kategori, dst).
        $items = $all->map(fn ($n) => $this->present($n))->values();

        $totalNotifications = $items->count();
        $unreadNotifications = $items->where('read', false)->count();
        $readNotifications = $totalNotifications - $unreadNotifications;
        $importantNotifications = $items->where('important', true)->count();

        // Jumlah per kategori untuk label pada tombol filter.
        $counts = [
            'all' => $totalNotifications,
            'shipment' => $items->where('category', 'shipment')->count(),
            'delay' => $items->where('category', 'delay')->count(),
            'exception' => $items->where('category', 'exception')->count(),
        ];

        $statusFilter = $request->query('status', 'all');
        $typeFilter = $request->query('type', 'all');
        $search = trim((string) $request->query('search', ''));

        $filtered = $items->filter(function ($item) use ($statusFilter, $typeFilter, $search) {
            if ($statusFilter === 'unread' && $item['read']) {
                return false;
            }

            if ($statusFilter === 'read' && !$item['read']) {
                return false;
            }

            if ($typeFilter !== 'all' && $item['category'] !== $typeFilter) {
                return false;
            }

            if ($search !== '') {
                $haystack = strtolower(implode(' ', [
                    $item['title'],
                    $item['message'],
                    $item['tracking'] ?? '',
                    $item['data']['shipment_name'] ?? '',
                    $item['data']['item_name'] ?? '',
                ]));

                if (!str_contains($haystack, strtolower($search))) {
                    return false;
                }
            }

            return true;
        })->values();


        // Detail dipilih lewat ?detail=ID, dan otomatis ditandai sudah dibaca.
        $selected = null;

        if ($request->filled('detail')) {
            $found = $all->firstWhere('id', $request->query('detail'));

            if ($found) {
                if (!$found->read_at) {
                    $found->markAsRead();
                    $found->refresh();
                }

                $selected = $this->present($found);
                $selected['shipment'] = $this->findShipment($selected['tracking']);
            }
        }

        // Pagination koleksi hasil filter.
        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();

        $notifications = new LengthAwarePaginator(
            $filtered->slice(($page - 1) * $perPage, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('customer.notifikasi.index', compact(
            'notifications',
            'totalNotifications',
            'unreadNotifications',
            'readNotifications',
            'importantNotifications',
            'counts',
            'selected',
            'statusFilter',
            'typeFilter',
            'search'
        ));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markRead($id)
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail()
            ->markAsRead();

        return redirect()
            ->route('customer.notifikasi.index', ['detail' => $id])
            ->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Tandai seluruh notifikasi Customer sebagai sudah dibaca.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()
            ->route('customer.notifikasi.index')
            ->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Hapus satu notifikasi milik Customer.
     */
    public function destroy($id)
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail()
            ->delete();

        return redirect()
            ->route('customer.notifikasi.index')
            ->with('success', 'Notifikasi berhasil dihapus.');
    }


    /**
     * Mengubah satu notifikasi menjadi data siap tampil.
     */
    private function present($notification): array
    {
        $data = $notification->data ?? [];

        $type = strtoupper((string) ($data['event_type'] ?? $data['type'] ?? $notification->type ?? ''));
        $title = (string) ($data['title'] ?? $data['subject'] ?? 'Pembaruan Pengiriman');
        $message = (string) ($data['message'] ?? $data['body'] ?? $data['description'] ?? 'Terdapat pembaruan informasi pengiriman.');
        $tracking = $data['tracking_number'] ?? $data['shipment_number'] ?? $data['resi'] ?? null;

        $text = strtolower($title . ' ' . $message);
        $lowTitle = strtolower($title);

        $isDelay = str_contains($type, 'DELAY') || str_contains($text, 'terlambat');

        $isException = str_contains($type, 'EXCEPTION')
            || str_contains($type, 'DAMAGE')
            || str_contains($type, 'MISROUTED')
            || str_contains($text, 'kendala')
            || str_contains($text, 'rusak')
            || str_contains($lowTitle, 'bea cukai')
            || str_contains($lowTitle, 'salah rute');

        $category = $isDelay ? 'delay' : ($isException ? 'exception' : 'shipment');

        if ($isDelay) {
            [$icon, $tone] = ['fa-triangle-exclamation', 'danger'];
        } elseif ($isException) {
            [$icon, $tone] = ['fa-circle-exclamation', 'warning'];
        } elseif (str_contains($type, 'DELIVERED') || str_contains($lowTitle, 'tiba')) {
            [$icon, $tone] = ['fa-circle-check', 'success'];
        } elseif (str_contains($type, 'PICKED')) {
            [$icon, $tone] = ['fa-box', 'success'];
        } elseif (str_contains($type, 'LOCATION') || str_contains($type, 'TRANSIT')) {
            [$icon, $tone] = ['fa-location-dot', 'blue'];
        } else {
            [$icon, $tone] = ['fa-bell', 'blue'];
        }

        return [
            'id' => $notification->id,
            'title' => $title,
            'message' => $message,
            'tracking' => $tracking,
            'icon' => $icon,
            'tone' => $tone,
            'category' => $category,
            'important' => $isDelay || $isException,
            'read' => $notification->read_at !== null,
            'created_at' => $notification->created_at,
            'data' => $data,
        ];
    }

    /**
     * Data pengiriman asli untuk melengkapi detail notifikasi.
     * Tetap dibatasi pada pengiriman milik Customer ini.
     */
    private function findShipment(?string $tracking): ?Shipment
    {
        if (!$tracking) {
            return null;
        }

        return Shipment::where('user_id', auth()->id())
            ->where('shipment_number', $tracking)
            ->first();
    }
}
