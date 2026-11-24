<?php

namespace App\Http\Controllers;

use App\Models\ExceptionHistory;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Manajemen Pengecualian (Exception Management) + deteksi otomatis ETA/delay.
 *
 * Sesuai brief UAS (slide 8):
 *  - Jenis      : CUSTOMS_HOLD, WEATHER_DELAY, PORT_CONGESTION, DAMAGED, MISROUTED, DOCUMENTATION_ISSUE
 *  - Severity   : LOW / MEDIUM / HIGH   -> untuk delay ditentukan RULE (0-6 jam LOW, >6-24 MEDIUM, >24 HIGH)
 *  - Status     : OPEN / INVESTIGATING / RESOLVED
 *  - Waktu      : waktu deteksi (created_at) dan penyelesaian (resolved_at) disimpan
 *  - Rule delay : jika ETA > expected arrival -> delayed; threshold configurable (config/cargotrace.php)
 */
class ShipmentExceptionController extends Controller
{
    private const TYPES = [
        'CUSTOMS_HOLD',
        'WEATHER_DELAY',
        'PORT_CONGESTION',
        'DAMAGED',
        'MISROUTED',
        'DOCUMENTATION_ISSUE',
    ];

    /** Penanda pengecualian yang dibuat otomatis (disimpan di awal kolom description). */
    private const AUTO_DELAY = '[AUTO-DELAY]';
    private const MARKER_RE = '/^\[AUTO-(?:DELAY|EVENT:\d+)\]\s*/';

    /** Kandidat nama kolom di tabel shipments (dipakai yang pertama ditemukan). */
    private const ETA_COLUMNS = ['estimated_arrival', 'estimated_arrival_at', 'eta', 'estimated_arrival_time'];
    private const EXPECTED_COLUMNS = ['expected_arrival', 'expected_arrival_at', 'expected_arrival_date', 'expected_arrival_time'];

    private ?array $etaCols = null;
    private bool $etaResolved = false;

    private function relations(): array
    {
        return ['shipment', 'assignedStaff', 'reporter', 'histories.user'];
    }

    /* =====================================================================
     |  INDEX
     | ===================================================================== */

    public function index(Request $request)
    {
        // Deteksi otomatis: dijalankan paling sering 1x/menit (atau paksa dengan ?sync=1).
        $sync = null;
        if ($request->boolean('sync') || Cache::add('exceptions:auto-sync', true, now()->addSeconds(60))) {
            $sync = $this->runAutoDetection();
        }

        $base = $this->baseQuery();
        $query = (clone $base)->with($this->relations());

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhereHas('shipment', function ($shipment) use ($search) {
                        $shipment->where('shipment_number', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%")
                            ->orWhere('carrier', 'like', "%{$search}%");
                    });
            });
        }

        foreach (['type', 'severity', 'status'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        if ($request->filled('source')) {
            $request->input('source') === 'AUTO'
                ? $query->where('description', 'like', '[AUTO-%')
                : $query->where('description', 'not like', '[AUTO-%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }

        $exceptions = $query
            ->latest('occurred_at')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => (clone $base)->count(),
            'open' => (clone $base)->where('status', 'OPEN')->count(),
            'investigating' => (clone $base)->where('status', 'INVESTIGATING')->count(),
            'resolved' => (clone $base)->where('status', 'RESOLVED')->count(),
            'low' => (clone $base)->where('severity', 'LOW')->count(),
            'medium' => (clone $base)->where('severity', 'MEDIUM')->count(),
            'high' => (clone $base)->where('severity', 'HIGH')->count(),
            'auto' => (clone $base)->where('description', 'like', '[AUTO-%')->count(),
        ];

        $typeStats = (clone $base)
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        // Data untuk form tambah dan edit.
        $shipments = Shipment::orderBy('shipment_number')
            ->get(['id', 'shipment_number', 'customer_name']);

        $staff = User::orderBy('name')->get(['id', 'name']);

        // Pengecualian yang diminta lewat link lama (?id=...), meski tidak ada di halaman ini.
        $focus = $request->filled('id')
            ? (clone $base)->with($this->relations())->find($request->input('id'))
            : null;

        // Perhitungan ETA/delay (alasan + nilai yang dipakai) untuk tiap pengecualian yang tampil.
        $delayMap = $exceptions->getCollection()
            ->when($focus, fn ($c) => $c->concat([$focus]))
            ->mapWithKeys(fn ($e) => [$e->id => $this->delayInfo($e->shipment)])
            ->filter();

        $rules = $this->delayRules();
        $delayOverview = $this->delayedOverview();
        $etaCols = $this->etaColumns();
        $canManage = ! $this->isCustomer();

        return view('exceptions.index', compact(
            'exceptions',
            'stats',
            'typeStats',
            'shipments',
            'staff',
            'focus',
            'delayMap',
            'rules',
            'delayOverview',
            'etaCols',
            'canManage',
            'sync'
        ));
    }

    // Link lama tetap berfungsi dan membuka form/detail di halaman index.
    public function create()
    {
        $this->authorizeManage();

        return redirect()->route('exceptions.index', ['buka' => 'tambah']);
    }

    public function show(string $id)
    {
        $this->baseQuery()->findOrFail($id);

        return redirect()->route('exceptions.index', ['buka' => 'detail', 'id' => $id]);
    }

    public function edit(string $id)
    {
        $this->authorizeManage();
        ShipmentException::findOrFail($id);

        return redirect()->route('exceptions.index', ['buka' => 'edit', 'id' => $id]);
    }

    /* =====================================================================
     |  CRUD
     | ===================================================================== */

    public function store(Request $request)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'shipment_id' => ['required', 'exists:shipments,id'],
            'type' => ['required', Rule::in(self::TYPES)],
            'location' => 'required|string|max:255',
            'occurred_at' => 'required|date',
            'severity' => ['required', Rule::in(['LOW', 'MEDIUM', 'HIGH'])],
            'description' => 'required|string',
            'cause' => 'nullable|string',
            'impact' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // Cegah input manual meniru penanda "otomatis".
        $validated['description'] = preg_replace(self::MARKER_RE, '', $validated['description']);

        DB::transaction(function () use ($request, $validated) {

            if ($request->hasFile('attachment')) {
                $validated['attachment_path'] =
                    $request->file('attachment')->store('exceptions', 'public');
            }

            unset($validated['attachment']);

            $validated['status'] = 'OPEN';
            $validated['reported_by'] = auth()->id();

            $exception = ShipmentException::create($validated);

            $this->log($exception, 'CREATED', null, 'OPEN', 'Pengecualian baru dilaporkan.');
        });

        return redirect()
            ->route('exceptions.index')
            ->with('success', 'Pengecualian berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $this->authorizeManage();

        $exception = ShipmentException::findOrFail($id);

        $validated = $request->validate([
            'shipment_id' => 'required|exists:shipments,id',
            'type' => ['required', Rule::in(self::TYPES)],
            'location' => 'required|string|max:255',
            'occurred_at' => 'required|date',
            'severity' => ['required', Rule::in(['LOW', 'MEDIUM', 'HIGH'])],
            'status' => ['required', Rule::in(['OPEN', 'INVESTIGATING', 'RESOLVED'])],
            'description' => 'required|string',
            'cause' => 'nullable|string',
            'impact' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // Penanda "otomatis" milik data lama dipertahankan (form menampilkan deskripsi tanpa penanda).
        $validated['description'] = preg_replace(self::MARKER_RE, '', $validated['description']);
        if (preg_match(self::MARKER_RE, (string) $exception->description, $m)) {
            $validated['description'] = $m[0] . $validated['description'];
        }

        DB::transaction(function () use ($request, $exception, $validated) {
            $oldStatus = $exception->status;

            if ($request->hasFile('attachment')) {
                if ($exception->attachment_path) {
                    Storage::disk('public')->delete($exception->attachment_path);
                }

                $validated['attachment_path'] =
                    $request->file('attachment')->store('exceptions', 'public');
            }

            unset($validated['attachment']);

            if ($validated['status'] === 'RESOLVED') {
                if ($oldStatus !== 'RESOLVED') {
                    $validated['resolved_at'] = now();
                }
            } else {
                $validated['resolved_at'] = null;
            }

            $exception->update($validated);

            if ($oldStatus !== $exception->status) {
                $this->log($exception, 'STATUS_CHANGED', $oldStatus, $exception->status, 'Status pengecualian diperbarui.');
            }

            $this->log($exception, 'UPDATED', $oldStatus, $exception->status, 'Data pengecualian diperbarui.');
        });

        return redirect()
            ->route('exceptions.index')
            ->with('success', 'Pengecualian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $this->authorizeManage();

        $exception = ShipmentException::findOrFail($id);

        if ($exception->attachment_path) {
            Storage::disk('public')->delete($exception->attachment_path);
        }

        $exception->delete();

        return redirect()
            ->route('exceptions.index')
            ->with('success', 'Pengecualian berhasil dihapus.');
    }

    /* =====================================================================
     |  DETEKSI OTOMATIS
     |  Bisa dipanggil dari tempat lain, mis. setelah shipment / tracking
     |  event disimpan:  app(ShipmentExceptionController::class)->runAutoDetection();
     | ===================================================================== */

    public function runAutoDetection(): array
    {
        $result = ['created' => 0, 'updated' => 0, 'resolved' => 0, 'error' => false];

        try {
            $delay = $this->syncDelays();
            $events = $this->syncTrackingEvents();

            $result['created'] = $delay['created'] + $events;
            $result['updated'] = $delay['updated'];
            $result['resolved'] = $delay['resolved'];
        } catch (\Throwable $e) {
            report($e);
            $result['error'] = true;
        }

        return $result;
    }

    /**
     * Rule delay: ETA > expected arrival -> delayed.
     * Buat pengecualian OPEN, naikkan/turunkan severity mengikuti durasi,
     * dan otomatis RESOLVED bila shipment sudah tidak terlambat / sudah DELIVERED.
     */
    public function syncDelays(): array
    {
        $out = ['created' => 0, 'updated' => 0, 'resolved' => 0];
        $cols = $this->etaColumns();

        if (! $cols) {
            return $out;
        }

        $rules = $this->delayRules();

        DB::transaction(function () use (&$out, $cols, $rules) {
            $delayedIds = [];

            $shipments = Shipment::query()
                ->whereNotNull($cols['est'])
                ->whereNotNull($cols['exp'])
                ->whereColumn($cols['est'], '>', $cols['exp'])
                ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
                ->get();

            foreach ($shipments as $s) {
                $info = $this->delayInfo($s);

                if (! $info || ! $info['delayed']) {
                    continue;
                }

                $delayedIds[] = $s->id;
                $text = self::AUTO_DELAY . ' ' . $info['reason'];

                $existing = ShipmentException::where('shipment_id', $s->id)
                    ->where('description', 'like', self::AUTO_DELAY . '%')
                    ->latest('id')
                    ->first();

                if (! $existing) {
                    $ex = ShipmentException::create([
                        'shipment_id' => $s->id,
                        'type' => $rules['type'],
                        'location' => Str::limit('Rute ' . ($s->origin ?? '-') . ' → ' . ($s->destination ?? '-'), 250, ''),
                        'occurred_at' => now(),
                        'severity' => $info['severity'],
                        'status' => 'OPEN',
                        'description' => $text,
                        'cause' => 'Estimasi kedatangan (ETA) melewati jadwal kedatangan yang diharapkan.',
                        'impact' => 'Pengiriman terlambat sekitar ' . $info['duration'] . '.',
                        'assigned_to' => null,
                        'reported_by' => auth()->id(),
                    ]);

                    $this->log($ex, 'CREATED', null, 'OPEN', '[AUTO] Delay terdeteksi otomatis. ' . $info['reason']);
                    $out['created']++;
                    continue;
                }

                if ($existing->status === 'RESOLVED') {
                    // Dibuka lagi hanya jika sebelumnya ditutup otomatis oleh sistem.
                    if ($this->wasAutoResolved($existing)) {
                        $existing->update(['status' => 'OPEN', 'resolved_at' => null, 'severity' => $info['severity'], 'description' => $text]);
                        $this->log($existing, 'STATUS_CHANGED', 'RESOLVED', 'OPEN', '[AUTO] Shipment kembali terlambat. ' . $info['reason']);
                        $out['updated']++;
                    }
                    continue;
                }

                if ($existing->severity !== $info['severity'] || $existing->description !== $text) {
                    $old = $existing->severity;
                    $existing->update(['severity' => $info['severity'], 'description' => $text]);
                    $this->log($existing, 'UPDATED', $existing->status, $existing->status, "[AUTO] Perhitungan delay diperbarui (severity {$old} → {$info['severity']}). " . $info['reason']);
                    $out['updated']++;
                }
            }

            // Tutup otomatis jika sudah tidak terlambat / sudah terkirim.
            ShipmentException::where('description', 'like', self::AUTO_DELAY . '%')
                ->where('status', '!=', 'RESOLVED')
                ->whereNotIn('shipment_id', $delayedIds)
                ->get()
                ->each(function ($ex) use (&$out) {
                    $old = $ex->status;
                    $ex->update(['status' => 'RESOLVED', 'resolved_at' => now()]);
                    $this->log($ex, 'STATUS_CHANGED', $old, 'RESOLVED', '[AUTO-RESOLVED] Shipment sudah tidak terlambat atau sudah terkirim.');
                    $out['resolved']++;
                });
        });

        return $out;
    }

    /**
     * Tracking event yang bertipe exception (mis. event_type = CUSTOMS_HOLD)
     * otomatis masuk sebagai pengecualian. Hanya event baru yang diimpor.
     */
    public function syncTrackingEvents(): int
    {
        if (! Schema::hasTable('tracking_events') || ! Schema::hasColumn('tracking_events', 'shipment_id')) {
            return 0;
        }

        $typeCol = $this->firstColumn('tracking_events', ['event_type', 'type', 'status']);
        if (! $typeCol) {
            return 0;
        }

        $timeCol = $this->firstColumn('tracking_events', ['event_time', 'occurred_at', 'event_at', 'timestamp', 'created_at']);
        $locCol = $this->firstColumn('tracking_events', ['location', 'location_name']);
        $descCol = $this->firstColumn('tracking_events', ['description', 'notes', 'note']);

        $lastId = (int) Cache::get('exceptions:last-event-id', 0);

        $events = DB::table('tracking_events')
            ->where('id', '>', $lastId)
            ->whereIn($typeCol, self::TYPES)
            ->orderBy('id')
            ->get();

        $maxId = (int) DB::table('tracking_events')->max('id');
        $created = 0;

        foreach ($events as $ev) {
            $type = $ev->{$typeCol};

            $ex = ShipmentException::create([
                'shipment_id' => $ev->shipment_id,
                'type' => $type,
                'location' => Str::limit((string) ($locCol ? ($ev->{$locCol} ?? '') : '') ?: 'Tidak diketahui', 250, ''),
                'occurred_at' => ($timeCol && ! empty($ev->{$timeCol})) ? Carbon::parse($ev->{$timeCol}) : now(),
                'severity' => in_array($type, ['DAMAGED', 'MISROUTED'], true) ? 'HIGH' : 'MEDIUM',
                'status' => 'OPEN',
                'description' => "[AUTO-EVENT:{$ev->id}] " . (($descCol ? ($ev->{$descCol} ?? '') : '') ?: 'Tracking event bertipe exception tercatat.'),
                'reported_by' => auth()->id(),
            ]);

            $this->log($ex, 'CREATED', null, 'OPEN', '[AUTO] Dibuat otomatis dari tracking event #' . $ev->id . '.');
            $created++;
        }

        Cache::forever('exceptions:last-event-id', $maxId);

        return $created;
    }

    /* =====================================================================
     |  ETA & DELAY (rule deterministik, mudah diuji)
     | ===================================================================== */

    /** Threshold bisa diubah lewat config/cargotrace.php (lihat catatan di bawah). */
    private function delayRules(): array
    {
        $type = config('cargotrace.delay.exception_type', 'PORT_CONGESTION');

        return [
            'threshold' => (float) config('cargotrace.delay.threshold_hours', 0),   // minimal telat agar dianggap delayed
            'low_max' => (float) config('cargotrace.delay.low_max_hours', 6),       // 0 - 6 jam  => LOW
            'medium_max' => (float) config('cargotrace.delay.medium_max_hours', 24), // >6 - 24 jam => MEDIUM, >24 => HIGH
            'type' => in_array($type, self::TYPES, true) ? $type : 'PORT_CONGESTION',
        ];
    }

    private function classify(float $hours, array $rules): string
    {
        if ($hours <= $rules['low_max']) {
            return 'LOW';
        }

        return $hours <= $rules['medium_max'] ? 'MEDIUM' : 'HIGH';
    }

    /** Hitung delay satu shipment, lengkap dengan alasan dan nilai yang dipakai. */
    public function delayInfo(?Shipment $s): ?array
    {
        $cols = $this->etaColumns();

        if (! $s || ! $cols) {
            return null;
        }

        $est = $s->{$cols['est']};
        $exp = $s->{$cols['exp']};

        if (! $est || ! $exp) {
            return null;
        }

        $rules = $this->delayRules();
        $est = Carbon::parse($est);
        $exp = Carbon::parse($exp);

        $minutes = (float) $exp->diffInMinutes($est, false);   // positif jika ETA setelah expected
        $hours = $minutes / 60;
        $delayed = $minutes > 0 && $hours > $rules['threshold'];
        $severity = $delayed ? $this->classify($hours, $rules) : null;
        $duration = $this->durationText($minutes);

        $ruleText = sprintf(
            '0–%s jam LOW; >%s–%s jam MEDIUM; >%s jam HIGH (threshold minimal %s jam)',
            $this->num($rules['low_max']), $this->num($rules['low_max']), $this->num($rules['medium_max']),
            $this->num($rules['medium_max']), $this->num($rules['threshold'])
        );

        $reason = $delayed
            ? sprintf('ETA %s melewati expected arrival %s selama %s (%s jam) → severity %s.',
                $est->format('d M Y H:i'), $exp->format('d M Y H:i'), $duration, $this->num($hours), $severity)
            : sprintf('ETA %s tidak melewati expected arrival %s (selisih %s jam) → tepat waktu.',
                $est->format('d M Y H:i'), $exp->format('d M Y H:i'), $this->num($hours));

        return [
            'expected' => $exp->format('d M Y H:i'),
            'estimated' => $est->format('d M Y H:i'),
            'minutes' => $minutes,
            'hours' => round($hours, 2),
            'duration' => $delayed ? $duration : 'Tidak terlambat',
            'delayed' => $delayed,
            'severity' => $severity,
            'threshold' => $this->num($rules['threshold']),
            'rule' => $ruleText,
            'reason' => $reason,
        ];
    }

    /** Ringkasan shipment aktif yang sedang terlambat (untuk panel pemantauan). */
    private function delayedOverview(): array
    {
        $cols = $this->etaColumns();

        if (! $cols) {
            return ['total' => 0, 'rows' => []];
        }

        $q = Shipment::query()
            ->whereNotNull($cols['est'])
            ->whereNotNull($cols['exp'])
            ->whereColumn($cols['est'], '>', $cols['exp'])
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED']);

        if ($this->isCustomer()) {
            $this->customerShipmentScope($q);
        }

        $items = $q->get()
            ->map(fn ($s) => [$s, $this->delayInfo($s)])
            ->filter(fn ($p) => $p[1] && $p[1]['delayed'])
            ->sortByDesc(fn ($p) => $p[1]['minutes'])
            ->values();

        $exMap = ShipmentException::whereIn('shipment_id', $items->map(fn ($p) => $p[0]->id)->all())
            ->where('description', 'like', self::AUTO_DELAY . '%')
            ->get()
            ->keyBy('shipment_id');

        return [
            'total' => $items->count(),
            'rows' => $items->take(8)->map(function ($p) use ($exMap) {
                [$s, $info] = $p;
                $ex = $exMap->get($s->id);

                return $info + [
                    'shipment_number' => $s->shipment_number,
                    'customer' => $s->customer_name,
                    'exception_id' => $ex?->id,
                    'exception_code' => $ex ? 'EXC-' . str_pad($ex->id, 5, '0', STR_PAD_LEFT) : null,
                    'exception_status' => $ex?->status,
                ];
            })->all(),
        ];
    }

    /* =====================================================================
     |  HELPER
     | ===================================================================== */

    private function log(ShipmentException $e, string $action, ?string $old, ?string $new, string $note): void
    {
        ExceptionHistory::create([
            'exception_id' => $e->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_status' => $old,
            'new_status' => $new,
            'note' => $note,
        ]);
    }

    private function wasAutoResolved(ShipmentException $e): bool
    {
        $note = ExceptionHistory::where('exception_id', $e->id)->latest('id')->value('note');

        return is_string($note) && str_starts_with($note, '[AUTO-RESOLVED]');
    }

    private function etaColumns(): ?array
    {
        if (! $this->etaResolved) {
            $this->etaResolved = true;
            $est = $this->firstColumn('shipments', self::ETA_COLUMNS);
            $exp = $this->firstColumn('shipments', self::EXPECTED_COLUMNS);
            $this->etaCols = ($est && $exp) ? ['est' => $est, 'exp' => $exp] : null;
        }

        return $this->etaCols;
    }

    private function firstColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $col) {
            if (Schema::hasColumn($table, $col)) {
                return $col;
            }
        }

        return null;
    }

    private function durationText(float $minutes): string
    {
        $m = max((int) round($minutes), 0);
        $d = intdiv($m, 1440);
        $h = intdiv($m % 1440, 60);
        $mm = $m % 60;

        $parts = [];
        if ($d) { $parts[] = $d . ' hari'; }
        if ($h) { $parts[] = $h . ' jam'; }
        if ($mm || ! $parts) { $parts[] = $mm . ' menit'; }

        return implode(' ', $parts);
    }

    private function num(float $v): string
    {
        return rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.') ?: '0';
    }

    /* ---------- Otorisasi (dicek di server, bukan hanya menyembunyikan tombol) ---------- */

    private function role(): string
    {
        $role = auth()->user()->role ?? '';

        if (is_object($role)) {
            $role = $role->name ?? $role->slug ?? '';
        }

        return strtolower((string) $role);
    }

    private function isCustomer(): bool
    {
        return $this->role() === 'customer';
    }

    private function authorizeManage(): void
    {
        abort_if($this->isCustomer(), 403, 'Anda tidak memiliki izin mengelola pengecualian.');
    }

    private function customerShipmentScope($query)
    {
        $user = auth()->user();

        foreach (['user_id', 'customer_id'] as $col) {
            if (Schema::hasColumn('shipments', $col)) {
                return $query->where($col, $user->id);
            }
        }

        return $query->where('customer_name', $user->name);
    }

    /** Customer hanya melihat pengecualian milik shipment-nya. */
    private function baseQuery()
    {
        $q = ShipmentException::query();

        if ($this->isCustomer()) {
            $q->whereHas('shipment', fn ($s) => $this->customerShipmentScope($s));
        }

        return $q;
    }
}
