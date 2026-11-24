<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OperatorShipmentController extends Controller
{
    private const STATUSES = [
        'CREATED',
        'BOOKED',
        'PICKED_UP',
        'IN_TRANSIT',
        'DELIVERED',
    ];

    /** Barang baru boleh dijemput (PICKED_UP) kalau pembayaran LUNAS. Set null untuk mematikan aturan ini. */
    private const PAYMENT_GATE = 'PICKED_UP';

    private function shipmentRules(): array
    {
        return [
            'shipment_number' => ['required', 'string', 'max:255', 'unique:shipments,shipment_number'],
            'customer_name' => ['required', 'string', 'max:255'],
            'shipment_name' => ['required', 'string', 'max:255'],
            'cargo_type' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0.01'],
            'volume' => ['nullable', 'numeric', 'min:0'],
            'carrier' => ['required', 'string', 'max:255'],
            'transport_type' => ['required', Rule::in(['udara', 'laut', 'darat'])],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'estimated_departure' => ['required', 'date'],
            'estimated_arrival' => ['required', 'date', 'after_or_equal:estimated_departure'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /* ====================================================================
     * DAFTAR PENGIRIMAN (OPERATOR)
     * Operator: lihat, tambah, edit, lacak, dan majukan status.
     * Tidak ada: hapus, refund, buka kembali status terminal, kelola tagihan.
     * ==================================================================== */

    public function index(Request $request)
    {
        $query = Shipment::with([
            'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc'),
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('shipment_name', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('carrier')) {
            $query->where('carrier', $request->carrier);
        }

        if ($request->filled('origin')) {
            $query->where('origin', 'like', '%' . $request->origin . '%');
        }

        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $shipments = $query->latest()->paginate(10)->withQueryString();

        $carriers = Shipment::query()
            ->whereNotNull('carrier')
            ->where('carrier', '!=', '')
            ->distinct()
            ->orderBy('carrier')
            ->pluck('carrier');

        $statusCounts = Shipment::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $carrierStats = Shipment::query()
            ->select(
                'carrier',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'IN_TRANSIT' THEN 1 ELSE 0 END) as in_transit"),
                DB::raw("SUM(CASE WHEN status = 'DELIVERED' THEN 1 ELSE 0 END) as delivered")
            )
            ->whereNotNull('carrier')
            ->where('carrier', '!=', '')
            ->groupBy('carrier')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $topRoutes = Shipment::query()
            ->select('origin', 'destination', DB::raw('COUNT(*) as total'))
            ->groupBy('origin', 'destination')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $cargoStats = Shipment::query()
            ->select(
                'cargo_type',
                DB::raw('COUNT(*) as total'),
                DB::raw('COALESCE(SUM(weight),0) as weight'),
                DB::raw('COALESCE(SUM(volume),0) as volume')
            )
            ->whereNotNull('cargo_type')
            ->groupBy('cargo_type')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $totalWeight = (float) Shipment::sum('weight');
        $totalVolume = (float) Shipment::sum('volume');

        $upcoming = Shipment::query()
            ->whereIn('status', ['CREATED', 'BOOKED'])
            ->whereNotNull('estimated_departure')
            ->orderBy('estimated_departure')
            ->limit(5)
            ->get();

        $nextNumber = 'CT-' . now()->year . '-' . str_pad((string) ((int) Shipment::max('id') + 1), 3, '0', STR_PAD_LEFT);

        $states = $this->statesFor($shipments->getCollection());

        return view('operator.pengiriman.index', compact(
            'shipments',
            'carriers',
            'statusCounts',
            'carrierStats',
            'topRoutes',
            'cargoStats',
            'totalWeight',
            'totalVolume',
            'upcoming',
            'nextNumber',
            'states'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->shipmentRules());

        DB::transaction(function () use ($validated) {
            $shipment = Shipment::create([
                ...$validated,
                'user_id' => null,
                'status' => 'CREATED',
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'status' => 'CREATED',
                'location' => $shipment->origin,
                'event_time' => now(),
                'description' => 'Pengiriman berhasil dibuat oleh operator.',
                'updated_by' => auth()->user()->name,
            ]);

            $this->logStatus($shipment->id, null, 'CREATED', 'CREATE', null);
        });

        return redirect()
            ->route('operator.pengiriman.index')
            ->with('success', 'Pengiriman baru berhasil ditambahkan.');
    }

    public function update(Request $request, Shipment $shipment)
    {
        // Aksi AJAX dari halaman (tanpa menambah route baru).
        if ($request->filled('_action')) {
            return $this->handleAction($request, $shipment, (string) $request->input('_action'));
        }

        $rules = $this->shipmentRules();

        $rules['shipment_number'] = [
            'required',
            'string',
            'max:255',
            Rule::unique('shipments', 'shipment_number')->ignore($shipment->id),
        ];

        $validated = $request->validate($rules);

        // Status TIDAK diubah dari form edit; gunakan tombol "Lanjutkan status".
        unset($validated['status']);

        $shipment->update($validated);

        return redirect()
            ->route('operator.pengiriman.index')
            ->with('success', 'Informasi pengiriman berhasil diperbarui.');
    }

    /* ====================================================================
     * AKSI AJAX
     * ==================================================================== */

    private function handleAction(Request $request, Shipment $shipment, string $action)
    {
        if ($action !== 'advance') {
            abort(403, 'Aksi ini hanya tersedia untuk Administrator.');
        }

        $message = DB::transaction(function () use ($request, $shipment) {
            $locked = Shipment::lockForUpdate()->findOrFail($shipment->id);

            return $this->doAdvance($request, $locked);
        });

        $fresh = Shipment::with(['trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc')])
            ->findOrFail($shipment->id);

        return response()->json([
            'success' => true,
            'message' => $message,
            'state'   => $this->statesFor([$fresh])[$fresh->id],
            'tracks'  => $this->tracksOf($fresh),
        ]);
    }

    /** Maju satu tahap + catat event tracking (lokasi untuk peta). */
    private function doAdvance(Request $request, Shipment $shipment): string
    {
        $data = $request->validate([
            'location'    => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $from = (string) $shipment->status;

        if (in_array($from, ['DELIVERED'], true)) {
            $this->fail('status', 'Status DELIVERED bersifat terminal. Hanya Administrator yang dapat membukanya kembali.');
        }

        $to = $this->nextOf($from);

        if (! $to) {
            $this->fail('status', 'Tidak ada status berikutnya.');
        }

        if (self::PAYMENT_GATE !== null && $to === self::PAYMENT_GATE && ! $this->isPaid($shipment->id)) {
            $this->fail('status', 'Pembayaran belum lunas. Barang belum dapat berstatus ' . $this->nice($to) . '.');
        }

        $location = trim((string) ($data['location'] ?? ''));
        if ($location === '') {
            $last = $shipment->trackingEvents()->orderByDesc('event_time')->first();
            $location = $to === 'DELIVERED'
                ? $shipment->destination
                : ($last->location ?? $shipment->origin);
        }

        $shipment->status = $to;
        $shipment->save();

        ShipmentTracking::create([
            'shipment_id' => $shipment->id,
            'status'      => $to,
            'location'    => $location,
            'event_time'  => now(),
            'latitude'    => $data['latitude'] ?? null,
            'longitude'   => $data['longitude'] ?? null,
            'description' => $data['description'] ?? null,
            'updated_by'  => auth()->user()->name,
        ]);

        $this->logStatus($shipment->id, $from, $to, 'TRANSITION', null);

        return 'Status berubah: ' . $this->nice($from) . ' → ' . $this->nice($to) . '.';
    }

    private function logStatus(int $shipmentId, ?string $from, string $to, string $type, ?string $reason): void
    {
        // Log status dibuat oleh modul admin. Kalau tabelnya belum ada, lewati saja.
        if (! Schema::hasTable('shipment_status_logs')) {
            return;
        }

        DB::table('shipment_status_logs')->insert([
            'shipment_id' => $shipmentId,
            'from_status' => $from,
            'to_status'   => $to,
            'type'        => $type,
            'reason'      => $reason,
            'actor'       => (string) (auth()->user()->name ?? 'operator'),
            'created_at'  => now(),
        ]);
    }

    private function nextOf(string $from): ?string
    {
        $i = array_search($from, self::STATUSES, true);

        return ($i === false || ! isset(self::STATUSES[$i + 1])) ? null : self::STATUSES[$i + 1];
    }

    private function isPaid(int $shipmentId): bool
    {
        if (! Schema::hasTable('shipment_payments')) {
            return true; // modul pembayaran belum aktif -> gerbang pembayaran tidak berlaku
        }

        return DB::table('shipment_payments')
            ->where('shipment_id', $shipmentId)
            ->where('status', 'PAID')
            ->exists();
    }

    /** Status pembayaran (read-only) + status berikutnya untuk banyak shipment sekaligus. */
    private function statesFor($shipments): array
    {
        $shipments = collect($shipments);
        $ids = $shipments->pluck('id')->all();

        if (! $ids) {
            return [];
        }

        $hasPay = Schema::hasTable('shipment_payments');
        $pays = $hasPay
            ? DB::table('shipment_payments')->whereIn('shipment_id', $ids)->orderByDesc('id')->get()->groupBy('shipment_id')
            : collect();

        $out = [];

        foreach ($shipments as $s) {
            $list = ($pays[$s->id] ?? collect())->pluck('status')->all();
            $pay = in_array('PAID', $list, true) ? 'PAID' : (in_array('PENDING', $list, true) ? 'PENDING' : ($list[0] ?? 'NONE'));

            $next = $this->nextOf((string) $s->status);
            $blocked = null;

            if ($next && self::PAYMENT_GATE !== null && $hasPay && $next === self::PAYMENT_GATE && $pay !== 'PAID') {
                $blocked = 'Pembayaran harus lunas sebelum barang dapat dijemput (PICKED UP). Pembayaran dikelola Administrator.';
            }

            $out[$s->id] = [
                'pay'     => $pay,
                'next'    => $next,
                'blocked' => $blocked,
            ];
        }

        return $out;
    }

    private function tracksOf(Shipment $s): array
    {
        return $s->trackingEvents->map(fn ($t) => [
            'status'   => $t->status,
            'location' => $t->location,
            'desc'     => $t->description,
            'time'     => $t->event_time ? Carbon::parse($t->event_time)->format('d M Y H:i') : '-',
            'lat'      => is_numeric($t->latitude) ? (float) $t->latitude : null,
            'lng'      => is_numeric($t->longitude) ? (float) $t->longitude : null,
            'by'       => $t->updated_by,
        ])->values()->all();
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => [$message]]);
    }

    private function nice(?string $v): string
    {
        return str_replace('_', ' ', (string) $v);
    }

    /* ====================================================================
     * HALAMAN OPERATOR LAIN (tidak diubah)
     * ==================================================================== */

    public function trackingIndex(Request $request)
    {
        $query = Shipment::with([
            'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc')
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('carrier', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('carrier')) {
            $query->where('carrier', $request->carrier);
        }

        $shipments = $query->latest()->paginate(10)->withQueryString();

        $totalShipments = Shipment::count();
        $inTransit = Shipment::where('status', 'IN_TRANSIT')->count();
        $pickedUp = Shipment::where('status', 'PICKED_UP')->count();
        $delivered = Shipment::where('status', 'DELIVERED')->count();

        $delayed = Shipment::whereNotIn('status', ['DELIVERED'])
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', now())
            ->count();

        $carriers = Shipment::whereNotNull('carrier')
            ->distinct()
            ->orderBy('carrier')
            ->pluck('carrier');

        $selected = null;

        if ($request->filled('id')) {
            $selected = Shipment::with([
                'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc')
            ])->findOrFail($request->id);
        }

        if (!$selected) {
            $selected = Shipment::with([
                'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc')
            ])->where('status', 'IN_TRANSIT')->latest()->first();
        }

        if (!$selected) {
            $selected = Shipment::with([
                'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc')
            ])->latest()->first();
        }

        $latestEvent = $selected
            ? $selected->trackingEvents->last()
            : null;

        return view('operator.pelacakan.index', compact(
            'shipments',
            'selected',
            'latestEvent',
            'totalShipments',
            'inTransit',
            'pickedUp',
            'delivered',
            'delayed',
            'carriers'
        ));
    }

    public function statusUpdateIndex(Request $request)
    {
        // DAFTAR PENGIRIMAN
        $shipmentQuery = Shipment::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $shipmentQuery->where(function ($query) use ($search) {
                $query->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('carrier', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $shipmentQuery->where('status', $request->status);
        }

        $shipments = $shipmentQuery
            ->latest()
            ->paginate(10, ['*'], 'shipments_page')
            ->withQueryString();

        // PENGIRIMAN YANG DIPILIH
        $selected = null;

        if ($request->filled('id')) {
            $selected = Shipment::with('trackingEvents')
                ->find($request->id);
        }

        if (!$selected) {
            $selected = Shipment::with('trackingEvents')
                ->latest()
                ->first();
        }

        // RIWAYAT PEMBARUAN STATUS
        $historyQuery = ShipmentTracking::with('shipment');

        if ($request->filled('history_search')) {
            $search = trim($request->history_search);

            $historyQuery->where(function ($query) use ($search) {
                $query->where('location', 'like', "%{$search}%")
                    ->orWhere('updated_by', 'like', "%{$search}%")
                    ->orWhereHas('shipment', function ($shipmentQuery) use ($search) {
                        $shipmentQuery
                            ->where('shipment_number', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('history_status')) {
            $historyQuery->where('status', $request->history_status);
        }

        if ($request->filled('operator')) {
            $historyQuery->where('updated_by', 'like', '%' . $request->operator . '%');
        }

        if ($request->filled('location')) {
            $historyQuery->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('date_from')) {
            $historyQuery->whereDate('event_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $historyQuery->whereDate('event_time', '<=', $request->date_to);
        }

        $history = $historyQuery
            ->latest('event_time')
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        // STATISTIK
        $totalShipments = Shipment::count();

        $pendingShipments = Shipment::whereNotIn('status', ['DELIVERED'])
            ->count();

        $deliveredShipments = Shipment::where('status', 'DELIVERED')
            ->count();

        $totalUpdates = ShipmentTracking::count();

        // DAFTAR STATUS
        $statuses = self::STATUSES;

        return view('operator.pembaruan-status.index', compact(
            'shipments',
            'selected',
            'history',
            'statuses',
            'totalShipments',
            'pendingShipments',
            'deliveredShipments',
            'totalUpdates'
        ));
    }

    public function addTrackingEvent(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'location' => ['required', 'string', 'max:255'],
            'event_time' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        ShipmentTracking::create([
            'shipment_id' => $shipment->id,
            'status' => $validated['status'],
            'location' => $validated['location'],
            'event_time' => $validated['event_time'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'description' => $validated['description'] ?? null,
            'updated_by' => auth()->user()->name,
        ]);

        return redirect()
            ->route('operator.pelacakan.index', ['id' => $shipment->id])
            ->with('success', 'Tracking event berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $statusOrder = array_flip(self::STATUSES);

        $currentStatus = $shipment->status;
        $newStatus = $validated['status'];

        if ($currentStatus === 'DELIVERED' && $newStatus !== 'DELIVERED') {
            return back()
                ->withErrors(['status' => 'Pengiriman yang sudah DELIVERED tidak dapat dikembalikan ke status sebelumnya.'])
                ->withInput();
        }

        if ($statusOrder[$newStatus] < $statusOrder[$currentStatus]) {
            return back()
                ->withErrors(['status' => 'Urutan status tidak valid. Status tidak boleh mundur.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $shipment, $newStatus) {
            $shipment->update([
                'status' => $newStatus,
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'status' => $newStatus,
                'location' => $validated['location'],
                'event_time' => now(),
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'description' => $validated['description'] ?? null,
                'updated_by' => auth()->user()->name,
            ]);
        });

        return redirect()
            ->route('operator.pengiriman.index')
            ->with('success', 'Status dan riwayat tracking berhasil diperbarui.');
    }
}
