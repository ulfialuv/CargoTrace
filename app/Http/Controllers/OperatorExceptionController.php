<?php

namespace App\Http\Controllers;

use App\Models\ExceptionHistory;
use App\Models\Shipment;
use App\Models\ShipmentException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Pengecualian untuk Logistics Operator.
 *
 * Sesuai brief UAS (slide 5 & 8), Operator boleh:
 *  - mengelola exception (laporkan, perbarui data, tangani, ubah status)
 *  - melihat pemantauan ETA / delay beserta alasan & nilai yang dipakai
 *
 * Operator TIDAK punya (khusus Administrator):
 *  - menghapus pengecualian
 *  - menugaskan ulang petugas / mengelola user
 *  - memindahkan pengecualian ke shipment lain
 *  - membuka kembali pengecualian yang sudah RESOLVED atau memundurkan status
 */
class OperatorExceptionController extends Controller
{
    private const TYPES = [
        'CUSTOMS_HOLD',
        'WEATHER_DELAY',
        'PORT_CONGESTION',
        'DAMAGED',
        'MISROUTED',
        'DOCUMENTATION_ISSUE',
    ];

    private const SEVERITIES = ['LOW', 'MEDIUM', 'HIGH'];

    private const STATUSES = ['OPEN', 'INVESTIGATING', 'RESOLVED'];

    /** Penanda pengecualian otomatis (sama dengan versi admin). */
    private const AUTO_DELAY = '[AUTO-DELAY]';
    private const MARKER_RE = '/^\[AUTO-(?:DELAY|EVENT:\d+)\]\s*/';

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
        // Deteksi otomatis: paling sering 1x/menit (atau paksa dengan ?sync=1).
        // Cache key sengaja sama dengan versi admin agar tidak jalan dobel.
        $sync = null;
        if ($request->boolean('sync') || Cache::add('exceptions:auto-sync', true, now()->addSeconds(60))) {
            $sync = $this->runAutoDetection();
        }

        $query = ShipmentException::with($this->relations());

        if ($request->filled('search')) {
            $search = trim($request->search);

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
            'total' => ShipmentException::count(),
            'open' => ShipmentException::where('status', 'OPEN')->count(),
            'investigating' => ShipmentException::where('status', 'INVESTIGATING')->count(),
            'resolved' => ShipmentException::where('status', 'RESOLVED')->count(),
            'low' => ShipmentException::where('severity', 'LOW')->count(),
            'medium' => ShipmentException::where('severity', 'MEDIUM')->count(),
            'high' => ShipmentException::where('severity', 'HIGH')->count(),
            'auto' => ShipmentException::where('description', 'like', '[AUTO-%')->count(),
        ];

        $typeStats = ShipmentException::query()
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $shipments = Shipment::orderBy('shipment_number')
            ->get(['id', 'shipment_number', 'customer_name']);

        // Link lama (?id=...) tetap membuka detail walau barisnya tidak ada di halaman ini.
        $focus = $request->filled('id')
            ? ShipmentException::with($this->relations())->find($request->input('id'))
            : null;

        // Perhitungan ETA/delay (alasan + nilai yang dipakai) untuk tiap pengecualian yang tampil.
        $delayMap = $exceptions->getCollection()
            ->when($focus, fn ($c) => $c->concat([$focus]))
            ->mapWithKeys(fn ($e) => [$e->id => $this->delayInfo($e->shipment)])
            ->filter();

        $rules = $this->delayRules();
        $delayOverview = $this->delayedOverview();
        $etaCols = $this->etaColumns();

        return view('operator.pengecualian.index', compact(
            'exceptions',
            'stats',
            'typeStats',
            'shipments',
            'focus',
            'delayMap',
            'rules',
            'delayOverview',
            'etaCols',
            'sync'
        ));
    }

    // Link lama tetap berfungsi: buka detail di halaman index.
    public function show(string $id)
    {
        ShipmentException::findOrFail($id);

        return redirect()->route('operator.exceptions.index', ['buka' => 'detail', 'id' => $id]);
    }

    /* =====================================================================
     |  STORE & UPDATE
     | ===================================================================== */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_id' => ['required', 'exists:shipments,id'],
            'type' => ['required', Rule::in(self::TYPES)],
            'location' => ['required', 'string', 'max:255'],
            'occurred_at' => ['required', 'date'],
            'severity' => ['required', Rule::in(self::SEVERITIES)],
            'description' => ['required', 'string'],
            'cause' => ['nullable', 'string'],
            'impact' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ]);

        // Cegah input manual meniru penanda "otomatis".
        $validated['description'] = preg_replace(self::MARKER_RE, '', $validated['description']);

        DB::transaction(function () use ($request, $validated) {
            if ($request->hasFile('attachment')) {
                $validated['attachment_path'] = $request->file('attachment')->store('exceptions', 'public');
            }

            unset($validated['attachment']);

            $validated['status'] = 'OPEN';
            $validated['reported_by'] = auth()->id();
            $validated['assigned_to'] = auth()->id();

            $exception = ShipmentException::create($validated);

            $this->log($exception, 'CREATED', null, 'OPEN', 'Pengecualian baru dilaporkan oleh Operator.');
        });

        return redirect()
            ->route('operator.exceptions.index')
            ->with('success', 'Pengecualian berhasil dilaporkan.');
    }

    public function update(Request $request, string $id)
    {
        $exception = ShipmentException::findOrFail($id);

        // shipment_id & assigned_to sengaja tidak divalidasi/diubah oleh Operator.
        $validated = $request->validate([
            'type' => ['required', Rule::in(self::TYPES)],
            'location' => ['required', 'string', 'max:255'],
            'occurred_at' => ['required', 'date'],
            'severity' => ['required', Rule::in(self::SEVERITIES)],
            'status' => ['required', Rule::in(self::STATUSES)],
            'description' => ['required', 'string'],
            'cause' => ['nullable', 'string'],
            'impact' => ['nullable', 'string'],
            'note' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ]);

        DB::transaction(function () use ($request, $exception, $validated) {
            $exception = ShipmentException::whereKey($exception->id)->lockForUpdate()->firstOrFail();

            $oldStatus = $exception->status;
            $newStatus = $validated['status'];

            // RESOLVED bersifat final.
            if ($oldStatus === 'RESOLVED') {
                throw ValidationException::withMessages([
                    'status' => 'Pengecualian yang sudah selesai tidak dapat diubah atau dibuka kembali.',
                ]);
            }

            // Status tidak boleh mundur.
            $order = array_flip(self::STATUSES);
            if ($order[$newStatus] < $order[$oldStatus]) {
                throw ValidationException::withMessages([
                    'status' => 'Status pengecualian tidak boleh kembali ke tahap sebelumnya.',
                ]);
            }

            // Penanda "otomatis" milik data lama dipertahankan.
            $validated['description'] = preg_replace(self::MARKER_RE, '', $validated['description']);
            if (preg_match(self::MARKER_RE, (string) $exception->description, $m)) {
                $validated['description'] = $m[0] . $validated['description'];
            }

            if ($request->hasFile('attachment')) {
                if ($exception->attachment_path) {
                    Storage::disk('public')->delete($exception->attachment_path);
                }

                $validated['attachment_path'] = $request->file('attachment')->store('exceptions', 'public');
            }

            $note = $validated['note'] ?? null;
            unset($validated['attachment'], $validated['note']);

            if ($newStatus === 'RESOLVED') {
                $validated['resolved_at'] = now();
            }

            $exception->update($validated);

            if ($oldStatus !== $newStatus) {
                $this->log($exception, 'STATUS_CHANGED', $oldStatus, $newStatus, $note ?: 'Status pengecualian diperbarui.');
            } elseif ($note) {
                $this->log($exception, 'HANDLING_NOTE', $oldStatus, $newStatus, $note);
            } else {
                $this->log($exception, 'UPDATED', $oldStatus, $newStatus, 'Data pengecualian diperbarui.');
            }
        });

        return redirect()
            ->route('operator.exceptions.index')
            ->with('success', 'Pengecualian berhasil diperbarui.');
    }

    /* =====================================================================
     |  DETEKSI OTOMATIS (ETA > expected arrival)
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

    /** Tracking event bertipe exception otomatis menjadi pengecualian (hanya event baru). */
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
     |  ETA & DELAY (rule deterministik)
     | ===================================================================== */

    private function delayRules(): array
    {
        $type = config('cargotrace.delay.exception_type', 'PORT_CONGESTION');

        return [
            'threshold' => (float) config('cargotrace.delay.threshold_hours', 0),
            'low_max' => (float) config('cargotrace.delay.low_max_hours', 6),
            'medium_max' => (float) config('cargotrace.delay.medium_max_hours', 24),
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

    /** Ringkasan shipment aktif yang sedang terlambat (panel pemantauan). */
    private function delayedOverview(): array
    {
        $cols = $this->etaColumns();

        if (! $cols) {
            return ['total' => 0, 'rows' => []];
        }

        $items = Shipment::query()
            ->whereNotNull($cols['est'])
            ->whereNotNull($cols['exp'])
            ->whereColumn($cols['est'], '>', $cols['exp'])
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->get()
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
}
