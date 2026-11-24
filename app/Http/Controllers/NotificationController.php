<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Cek kejadian baru (keterlambatan, kendala, perubahan status, tracking) lalu masukkan ke notifikasi.
        // Otomatis maksimal 1x per menit, atau paksa lewat tombol "Periksa Sekarang" (?refresh=1).
        if ($request->boolean('refresh') || Cache::add('notifications.sync.lock', 1, 60)) {
            try {
                $this->sync();
            } catch (\Throwable $e) {
                Log::warning('Sinkronisasi notifikasi gagal: ' . $e->getMessage());
            }
        }

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
            ->when($request->status === 'important', function ($query) {
                $query->where('data->important', true);
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

    /* =====================================================================
       SINKRONISASI OTOMATIS
       Mengubah kejadian pada pengiriman menjadi notifikasi untuk semua user.
       Tiap kejadian punya "key" unik, jadi tidak akan dobel walau dijalankan berulang.
       Bisa dipanggil dari mana saja, mis. scheduler:
         Schedule::call(fn () => app(\App\Http\Controllers\NotificationController::class)->sync())->everyMinute();
       ===================================================================== */
    public function sync(): int
    {
        if (!Schema::hasTable('notifications')) {
            return 0;
        }

        $events = $this->collectEvents();
        if ($events->isEmpty()) {
            return 0;
        }

        $created = 0;
        $morph = (new User())->getMorphClass();

        User::query()->select('id')->chunkById(100, function ($users) use ($events, $morph, &$created) {
            foreach ($users as $user) {
                // Key yang sudah pernah dibuat untuk user ini (3000 terbaru sudah cukup).
                $existing = DB::table('notifications')
                    ->where('notifiable_type', $morph)
                    ->where('notifiable_id', $user->id)
                    ->orderByDesc('created_at')
                    ->limit(3000)
                    ->pluck('data')
                    ->map(fn ($d) => (json_decode($d, true)['key'] ?? null))
                    ->filter()
                    ->flip();

                $rows = [];
                foreach ($events as $ev) {
                    if (isset($existing[$ev['key']])) {
                        continue;
                    }
                    $at = $ev['time'] ?? now();
                    $rows[] = [
                        'id' => (string) Str::uuid(),
                        'type' => 'App\\Notifications\\CargoTraceNotification',
                        'notifiable_type' => $morph,
                        'notifiable_id' => $user->id,
                        'data' => json_encode([
                            'key' => $ev['key'],
                            'type' => $ev['type'],
                            'title' => $ev['title'],
                            'message' => $ev['message'],
                            'shipment_number' => $ev['shipment_number'] ?? null,
                            'url' => $ev['url'] ?? null,
                            'important' => (bool) ($ev['important'] ?? false),
                        ], JSON_UNESCAPED_UNICODE),
                        'read_at' => null,
                        'created_at' => $at,
                        'updated_at' => $at,
                    ];
                }

                foreach (array_chunk($rows, 100) as $chunk) {
                    DB::table('notifications')->insert($chunk);
                }
                $created += count($rows);
            }
        });

        return $created;
    }

    /**
     * Kumpulkan semua kejadian yang layak jadi notifikasi.
     */
    private function collectEvents(): Collection
    {
        $now = now();
        $weekAgo = $now->copy()->subDays(7);
        $threeDays = $now->copy()->subDays(3);
        $events = collect();

        $url = fn ($id) => route('pelacakan.index', ['id' => $id]);
        $closed = ['DELIVERED', 'CANCELLED'];

        // 1. Pengiriman baru dibuat
        Shipment::where('created_at', '>=', $weekAgo)->latest()->limit(100)->get()->each(function ($s) use ($events, $url) {
            $events->push([
                'key' => "created:{$s->id}",
                'type' => 'shipment_created',
                'title' => 'Pengiriman baru dibuat',
                'message' => "Pengiriman {$s->shipment_number} untuk {$s->customer_name} dibuat dengan rute {$s->origin} → {$s->destination}.",
                'shipment_number' => $s->shipment_number,
                'url' => $url($s->id),
                'important' => false,
                'time' => $s->created_at,
            ]);
        });

        // 2. Perubahan status (dari data pengiriman yang baru diperbarui)
        Shipment::where('updated_at', '>=', $weekAgo)
            ->where('status', '!=', 'CREATED')
            ->latest('updated_at')->limit(200)->get()
            ->each(function ($s) use ($events, $url) {
                $status = strtoupper((string) $s->status);
                [$type, $title, $msg, $imp] = match ($status) {
                    'IN_TRANSIT' => ['shipment_in_transit', 'Pengiriman dalam perjalanan', "Pengiriman {$s->shipment_number} sedang dalam perjalanan ke {$s->destination}.", false],
                    'DELIVERED' => ['shipment_delivered', 'Pengiriman telah diterima', "Pengiriman {$s->shipment_number} telah sampai di {$s->destination}.", false],
                    'DELAYED' => ['shipment_delayed', 'Pengiriman tertunda', "Pengiriman {$s->shipment_number} ({$s->origin} → {$s->destination}) ditandai tertunda. Segera cek penyebabnya.", true],
                    'CANCELLED' => ['shipment_cancelled', 'Pengiriman dibatalkan', "Pengiriman {$s->shipment_number} untuk {$s->customer_name} dibatalkan.", true],
                    default => ['shipment_status_update', 'Status pengiriman diperbarui', "Status pengiriman {$s->shipment_number} berubah menjadi " . str_replace('_', ' ', $status) . '.', false],
                };
                $events->push([
                    'key' => "status:{$s->id}:{$status}",
                    'type' => $type,
                    'title' => $title,
                    'message' => $msg,
                    'shipment_number' => $s->shipment_number,
                    'url' => $url($s->id),
                    'important' => $imp,
                    'time' => $s->updated_at,
                ]);
            });

        // 3. Terlambat tiba: ETA sudah lewat tapi belum diterima
        Shipment::whereNotIn('status', $closed)
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', $now)
            ->orderBy('estimated_arrival')->limit(200)->get()
            ->each(function ($s) use ($events, $url, $now) {
                $eta = Carbon::parse($s->estimated_arrival);
                $events->push([
                    'key' => 'late:' . $s->id . ':' . $eta->format('YmdHi'),
                    'type' => 'shipment_delayed',
                    'title' => 'Pengiriman terlambat tiba',
                    'message' => "Pengiriman {$s->shipment_number} ({$s->origin} → {$s->destination}) melewati estimasi tiba {$eta->format('d M Y H:i')}, terlambat " . $this->duration($eta, $now) . '.',
                    'shipment_number' => $s->shipment_number,
                    'url' => $url($s->id),
                    'important' => true,
                    'time' => $eta,
                ]);
            });

        // 4. ETA mendekati (kurang dari 24 jam)
        Shipment::whereNotIn('status', array_merge($closed, ['DELAYED']))
            ->whereNotNull('estimated_arrival')
            ->whereBetween('estimated_arrival', [$now, $now->copy()->addDay()])
            ->orderBy('estimated_arrival')->limit(100)->get()
            ->each(function ($s) use ($events, $url, $now) {
                $eta = Carbon::parse($s->estimated_arrival);
                $events->push([
                    'key' => 'soon:' . $s->id . ':' . $eta->format('YmdH'),
                    'type' => 'eta_soon',
                    'title' => 'Estimasi tiba segera',
                    'message' => "Pengiriman {$s->shipment_number} diperkirakan tiba di {$s->destination} dalam " . $this->duration($now, $eta) . " ({$eta->format('d M Y H:i')}).",
                    'shipment_number' => $s->shipment_number,
                    'url' => $url($s->id),
                    'important' => false,
                    'time' => $now,
                ]);
            });

        // 5. Update tracking terbaru
        if (Schema::hasTable('shipment_trackings') || class_exists(ShipmentTracking::class)) {
            try {
                $tracks = ShipmentTracking::where('event_time', '>=', $threeDays)->orderByDesc('event_time')->limit(200)->get();
                $ships = Shipment::whereIn('id', $tracks->pluck('shipment_id')->filter()->unique())->get()->keyBy('id');

                $tracks->each(function ($t) use ($events, $ships, $url) {
                    $s = $ships[$t->shipment_id] ?? null;
                    if (!$s) {
                        return;
                    }
                    $status = strtoupper((string) $t->status);
                    $bad = in_array($status, ['DELAYED', 'EXCEPTION', 'FAILED', 'DAMAGED', 'LOST', 'RETURNED']);
                    $where = $t->location ? " di {$t->location}" : '';
                    $events->push([
                        'key' => "tracking:{$t->id}",
                        'type' => $bad ? 'shipment_delayed' : 'tracking_update',
                        'title' => $bad ? 'Kendala pada pelacakan' : 'Update pelacakan baru',
                        'message' => "Pengiriman {$s->shipment_number}: " . str_replace('_', ' ', $status) . $where . ($t->description ? '. ' . $t->description : '.'),
                        'shipment_number' => $s->shipment_number,
                        'url' => $url($s->id),
                        'important' => $bad,
                        'time' => $t->event_time,
                    ]);
                });
            } catch (\Throwable $e) {
                Log::info('Lewati notifikasi tracking: ' . $e->getMessage());
            }
        }

        // 6. Pengecualian / kendala (jika tabelnya ada)
        foreach (['exceptions', 'shipment_exceptions'] as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $has = fn ($c) => Schema::hasColumn($table, $c);
            if (!$has('shipment_id')) {
                continue;
            }
            $rows = DB::table($table)->where('created_at', '>=', $weekAgo)->orderByDesc('created_at')->limit(200)->get();
            $ships = Shipment::whereIn('id', $rows->pluck('shipment_id')->filter()->unique())->get()->keyBy('id');

            foreach ($rows as $r) {
                $s = $ships[$r->shipment_id] ?? null;
                if (!$s) {
                    continue;
                }
                $kind = $r->type ?? $r->exception_type ?? 'Pengecualian';
                $desc = $r->description ?? $r->message ?? null;
                $st = strtolower((string) ($r->status ?? $r->handling_status ?? 'open'));
                $resolved = in_array($st, ['resolved', 'selesai', 'closed']);

                $events->push([
                    'key' => "exception:{$r->id}",
                    'type' => 'new_exception',
                    'title' => 'Kendala baru: ' . str_replace('_', ' ', $kind),
                    'message' => "Pengiriman {$s->shipment_number} mengalami kendala" . ($desc ? ": {$desc}" : '.'),
                    'shipment_number' => $s->shipment_number,
                    'url' => $url($s->id),
                    'important' => true,
                    'time' => $r->created_at,
                ]);

                if ($resolved) {
                    $events->push([
                        'key' => "exception-resolved:{$r->id}",
                        'type' => 'exception_resolved',
                        'title' => 'Kendala telah diselesaikan',
                        'message' => "Kendala pada pengiriman {$s->shipment_number} ({$kind}) sudah ditangani.",
                        'shipment_number' => $s->shipment_number,
                        'url' => $url($s->id),
                        'important' => false,
                        'time' => $r->updated_at ?? $r->created_at,
                    ]);
                }
            }
            break;
        }

        return $events;
    }

    private function duration(Carbon $from, Carbon $to): string
    {
        $m = max(0, (int) $from->diffInMinutes($to, true));
        $d = intdiv($m, 1440);
        $h = intdiv($m % 1440, 60);
        $mi = $m % 60;

        if ($d > 0) {
            return "{$d} hari {$h} jam";
        }
        if ($h > 0) {
            return "{$h} jam {$mi} menit";
        }

        return max($mi, 1) . ' menit';
    }
}
