<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PengirimanController extends Controller
{
    /* ====================================================================
     * KONFIGURASI BUSINESS RULES
     * ==================================================================== */

    /** Urutan lifecycle yang valid (hanya boleh maju satu tahap). */
    private const FLOW = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];

    /** Status terminal: tidak boleh berubah lewat jalur biasa. Hanya prosedur "Buka Kembali". */
    private const TERMINAL = ['DELIVERED'];

    /** Barang baru boleh dijemput (PICKED_UP) kalau pembayaran sudah LUNAS. Set null untuk mematikan aturan ini. */
    private const PAYMENT_GATE = 'PICKED_UP';

    /** Masa berlaku tagihan (menit). */
    private const PAY_TTL_MINUTES = 1440;

    private const TAX_PCT = 11;
    private const MIN_FARE = 75000;

    /** Tarif per kg (IDR) dan faktor volume (kg per m3) menurut moda transport. */
    private const TARIFF = [
        'udara' => ['per_kg' => 18000, 'kg_per_m3' => 167],
        'laut'  => ['per_kg' => 2500,  'kg_per_m3' => 1000],
        'darat' => ['per_kg' => 4500,  'kg_per_m3' => 333],
    ];

    /** Metode pembayaran gateway (sandbox). fee = pct% dari (ongkir + pajak) + fixed. */
    private const METHODS = [
        'VA_BCA'     => ['label' => 'BCA Virtual Account',     'group' => 'va',      'pct' => 0,   'fixed' => 4000, 'prefix' => '39158'],
        'VA_MANDIRI' => ['label' => 'Mandiri Virtual Account', 'group' => 'va',      'pct' => 0,   'fixed' => 4000, 'prefix' => '88608'],
        'VA_BNI'     => ['label' => 'BNI Virtual Account',     'group' => 'va',      'pct' => 0,   'fixed' => 4000, 'prefix' => '8808'],
        'QRIS'       => ['label' => 'QRIS',                    'group' => 'qris',    'pct' => 0.7, 'fixed' => 0],
        'GOPAY'      => ['label' => 'GoPay',                   'group' => 'ewallet', 'pct' => 2.0, 'fixed' => 0],
        'OVO'        => ['label' => 'OVO',                     'group' => 'ewallet', 'pct' => 2.0, 'fixed' => 0],
        'CARD'       => ['label' => 'Kartu Kredit / Debit',    'group' => 'card',    'pct' => 2.9, 'fixed' => 2000],
    ];

    /* ====================================================================
     * DAFTAR PENGIRIMAN
     * ==================================================================== */

    public function index(Request $request)
    {
        // Endpoint JSON kurs (dipanggil halaman: /pengiriman?kurs=1). Tidak butuh route tambahan.
        if ($request->boolean('kurs')) {
            return $this->kurs();
        }

        $this->bootstrapPayments();

        $query = Shipment::query()
            ->with(['trackingEvents' => fn ($q) => $q->orderBy('event_time')]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('shipment_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('carrier')) {
            $query->where('carrier', $request->carrier);
        }

        if ($request->filled('origin')) {
            $query->where('origin', 'like', "%{$request->origin}%");
        }

        if ($request->filled('destination')) {
            $query->where('destination', 'like', "%{$request->destination}%");
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter status pembayaran: PAID | PENDING | UNPAID
        $pay = $request->input('pay');
        $paidSub = DB::table('shipment_payments')->where('status', 'PAID')->select('shipment_id');
        $pendSub = DB::table('shipment_payments')->where('status', 'PENDING')->select('shipment_id');
        if ($pay === 'PAID') {
            $query->whereIn('id', $paidSub);
        } elseif ($pay === 'PENDING') {
            $query->whereIn('id', $pendSub);
        } elseif ($pay === 'UNPAID') {
            $query->whereNotIn('id', $paidSub);
        }

        $shipments = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $carriers = Shipment::query()
            ->whereNotNull('carrier')
            ->where('carrier', '!=', '')
            ->distinct()
            ->orderBy('carrier')
            ->pluck('carrier');

        // Jumlah per status (untuk alur status).
        $statusCounts = Shipment::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Ringkasan per perusahaan pengiriman.
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

        // Rute asal-tujuan paling sering.
        $topRoutes = Shipment::query()
            ->select('origin', 'destination', DB::raw('COUNT(*) as total'))
            ->groupBy('origin', 'destination')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Ringkasan barang.
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

        // Jadwal keberangkatan terdekat (belum berangkat).
        $upcoming = Shipment::query()
            ->whereIn('status', ['CREATED', 'BOOKED'])
            ->whereNotNull('estimated_departure')
            ->orderBy('estimated_departure')
            ->limit(5)
            ->get();

        $nextNumber = 'CT-' . now()->year . '-' . str_pad((string) ((int) Shipment::max('id') + 1), 3, '0', STR_PAD_LEFT);

        // ---- Pembayaran ----
        $states = $this->statesFor($shipments->getCollection());

        $agg = DB::table('shipment_payments')->selectRaw("
            COALESCE(SUM(CASE WHEN status = 'PAID' THEN total ELSE 0 END), 0) AS paid_total,
            COALESCE(SUM(CASE WHEN status = 'PAID' THEN 1 ELSE 0 END), 0) AS paid_count,
            COALESCE(SUM(CASE WHEN status = 'PENDING' THEN total ELSE 0 END), 0) AS pending_total,
            COALESCE(SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END), 0) AS pending_count,
            COALESCE(SUM(CASE WHEN status = 'REFUNDED' THEN total ELSE 0 END), 0) AS refunded_total,
            COALESCE(SUM(CASE WHEN status = 'REFUNDED' THEN 1 ELSE 0 END), 0) AS refunded_count
        ")->first();

        $payStats = [
            'paid_total'     => (int) $agg->paid_total,
            'paid_count'     => (int) $agg->paid_count,
            'pending_total'  => (int) $agg->pending_total,
            'pending_count'  => (int) $agg->pending_count,
            'refunded_total' => (int) $agg->refunded_total,
            'refunded_count' => (int) $agg->refunded_count,
            'unpaid'         => Shipment::whereNotIn('id', DB::table('shipment_payments')->where('status', 'PAID')->select('shipment_id'))->count(),
        ];

        $recentPayments = DB::table('shipment_payments as p')
            ->leftJoin('shipments as s', 's.id', '=', 'p.shipment_id')
            ->orderByDesc('p.id')
            ->limit(8)
            ->get(['p.invoice_number', 'p.method', 'p.total', 'p.status', 'p.created_at', 's.shipment_number'])
            ->map(fn ($r) => [
                'invoice'      => $r->invoice_number,
                'number'       => $r->shipment_number ?? '-',
                'method_label' => self::METHODS[$r->method]['label'] ?? $r->method,
                'total'        => (int) $r->total,
                'status'       => $r->status,
                'time'         => Carbon::parse($r->created_at)->format('d M Y H:i'),
            ])->all();

        $canAdmin = $this->isAdmin();
        $sandbox = $this->sandbox();

        return view('pengiriman.index', compact(
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
            'states',
            'payStats',
            'recentPayments',
            'canAdmin',
            'sandbox'
        ));
    }

    // FORM TAMBAH PENGIRIMAN
    public function create()
    {
        return redirect()->route('pengiriman.index', ['buka' => 'tambah']);
    }

    // SIMPAN PENGIRIMAN BARU
    public function store(Request $request)
    {
        $this->bootstrapPayments();

        $validated = $request->validate($this->rules());

        // Business rule: shipment baru wajib dimulai dari CREATED.
        if (($validated['status'] ?? 'CREATED') !== 'CREATED') {
            $this->fail('status', 'Pengiriman baru harus dimulai dari status CREATED.');
        }
        $validated['status'] = 'CREATED';

        DB::transaction(function () use ($validated) {
            $shipment = Shipment::create($validated);

            DB::table('shipment_status_logs')->insert([
                'shipment_id' => $shipment->id,
                'from_status' => null,
                'to_status'   => 'CREATED',
                'type'        => 'CREATE',
                'reason'      => null,
                'actor'       => $this->actor(),
                'created_at'  => now(),
            ]);
        });

        return redirect()
            ->route('pengiriman.index')
            ->with('success', 'Pengiriman berhasil ditambahkan!');
    }

    // MENAMPILKAN DETAIL PENGIRIMAN
    public function show($id)
    {
        $shipment = Shipment::findOrFail($id);

        return redirect()->route('pengiriman.index', [
            'buka' => 'detail',
            'id' => $shipment->id,
            'search' => $shipment->shipment_number,
        ]);
    }

    // FORM EDIT PENGIRIMAN
    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);

        return redirect()->route('pengiriman.index', [
            'buka' => 'edit',
            'id' => $shipment->id,
            'search' => $shipment->shipment_number,
        ]);
    }

    /**
     * UPDATE PENGIRIMAN
     * - Tanpa "_action"  : form edit biasa (status divalidasi lewat state machine).
     * - Dengan "_action" : aksi AJAX (advance, reopen, pay_create, pay_simulate, pay_cancel, pay_refund, sync).
     *   Dipakai supaya tidak perlu menambah route baru.
     */
    public function update(Request $request, $id)
    {
        $this->bootstrapPayments();

        if ($request->filled('_action')) {
            return $this->handleAction($request, (int) $id, (string) $request->input('_action'));
        }

        $shipment = Shipment::findOrFail($id);
        $validated = $request->validate($this->rules($shipment->id));

        DB::transaction(function () use ($shipment, $validated) {
            $locked = Shipment::lockForUpdate()->findOrFail($shipment->id);

            $newStatus = $validated['status'] ?? $locked->status;
            unset($validated['status']);

            $this->assertTariffUnlocked($locked, $validated);
            $this->assertTransition($locked, $newStatus);

            $oldStatus = $locked->status;
            $locked->update($validated);

            if ($newStatus !== $oldStatus) {
                $this->applyTransition($locked, $newStatus, 'TRANSITION', null);
            }
        });

        return redirect()
            ->route('pengiriman.index')
            ->with('success', 'Pengiriman berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->bootstrapPayments();

        $shipment = Shipment::findOrFail($id);

        if (DB::table('shipment_payments')->where('shipment_id', $shipment->id)->where('status', 'PAID')->exists()) {
            return redirect()
                ->route('pengiriman.index')
                ->withErrors(['delete' => 'Pengiriman sudah dibayar. Lakukan refund terlebih dahulu sebelum menghapus.']);
        }

        DB::transaction(function () use ($shipment) {
            DB::table('shipment_payments')
                ->where('shipment_id', $shipment->id)
                ->where('status', 'PENDING')
                ->update(['status' => 'CANCELLED', 'note' => 'Pengiriman dihapus', 'updated_at' => now()]);

            $shipment->delete();
        });

        return redirect()
            ->route('pengiriman.index')
            ->with('success', 'Pengiriman berhasil dihapus!');
    }

    /* ====================================================================
     * AKSI AJAX (status + pembayaran)
     * ==================================================================== */

    private function handleAction(Request $request, int $id, string $action)
    {
        [$shipment, $message] = DB::transaction(function () use ($request, $id, $action) {
            $shipment = Shipment::lockForUpdate()->findOrFail($id);

            switch ($action) {
                case 'sync':
                    $message = 'Data diperbarui.';
                    break;
                case 'advance':
                    $message = $this->doAdvance($request, $shipment);
                    break;
                case 'reopen':
                    $message = $this->doReopen($request, $shipment);
                    break;
                case 'pay_create':
                    $message = $this->doPayCreate($request, $shipment);
                    break;
                case 'pay_simulate':
                    $message = $this->doPaySimulate($request, $shipment);
                    break;
                case 'pay_cancel':
                    $message = $this->doPayCancel($shipment);
                    break;
                case 'pay_refund':
                    $message = $this->doPayRefund($request, $shipment);
                    break;
                default:
                    abort(422, 'Aksi tidak dikenal.');
            }

            return [$shipment, $message];
        });

        $fresh = Shipment::findOrFail($shipment->id);

        return response()->json([
            'success' => true,
            'message' => $message,
            'state'   => $this->statesFor([$fresh])[$fresh->id],
        ]);
    }

    /* ---------------------------- STATUS ---------------------------- */

    private function doAdvance(Request $request, Shipment $shipment): string
    {
        $to = (string) $request->input('to', $this->allowedNext($shipment->status)[0] ?? '');

        if ($to === $shipment->status) {
            $this->fail('status', 'Pengiriman sudah berstatus ' . $this->nice($to) . '.');
        }

        $this->assertTransition($shipment, $to);
        $from = $shipment->status;
        $this->applyTransition($shipment, $to, 'TRANSITION', null);

        return 'Status berubah: ' . $this->nice($from) . ' → ' . $this->nice($to) . '.';
    }

    /**
     * PROSEDUR KHUSUS: membuka kembali status terminal (mis. DELIVERED).
     * Syarat: Administrator, shipment benar-benar terminal, alasan >= 15 karakter,
     * konfirmasi dengan mengetik nomor resi. Dicatat permanen di shipment_status_logs.
     */
    private function doReopen(Request $request, Shipment $shipment): string
    {
        if (! $this->isAdmin()) {
            abort(403, 'Hanya Administrator yang dapat menjalankan prosedur Buka Kembali.');
        }

        $data = $request->validate([
            'target_status'  => ['required', Rule::in(array_values(array_diff(self::FLOW, self::TERMINAL)))],
            'reason'         => 'required|string|min:15|max:500',
            'confirm_number' => 'required|string',
        ]);

        if (! in_array($shipment->status, self::TERMINAL, true)) {
            $this->fail('status', 'Prosedur Buka Kembali hanya untuk shipment berstatus terminal.');
        }

        if (trim($data['confirm_number']) !== (string) $shipment->shipment_number) {
            $this->fail('confirm_number', 'Nomor resi konfirmasi tidak cocok.');
        }

        $from = $shipment->status;
        $this->applyTransition($shipment, $data['target_status'], 'REOPEN', trim($data['reason']));

        return 'Status dibuka kembali: ' . $this->nice($from) . ' → ' . $this->nice($data['target_status']) . ' (tercatat di log).';
    }

    private function allowedNext(string $from): array
    {
        $i = array_search($from, self::FLOW, true);

        if ($i === false || in_array($from, self::TERMINAL, true) || ! isset(self::FLOW[$i + 1])) {
            return [];
        }

        return [self::FLOW[$i + 1]];
    }

    /** State machine: melempar ValidationException bila transisi tidak sah. */
    private function assertTransition(Shipment $s, string $to): void
    {
        $from = (string) $s->status;

        if ($from === $to) {
            return;
        }

        if (! in_array($to, self::FLOW, true)) {
            $this->fail('status', 'Status tujuan tidak dikenal.');
        }

        if (in_array($from, self::TERMINAL, true)) {
            $this->fail('status', 'Status ' . $this->nice($from) . ' bersifat terminal dan tidak dapat diubah dari form biasa. Gunakan prosedur Buka Kembali (khusus Administrator).');
        }

        if (! in_array($to, $this->allowedNext($from), true)) {
            $next = $this->allowedNext($from)[0] ?? '-';
            $this->fail('status', 'Transisi ' . $this->nice($from) . ' → ' . $this->nice($to) . ' tidak valid. Status berikutnya hanya ' . $this->nice($next) . '.');
        }

        if (self::PAYMENT_GATE !== null && $to === self::PAYMENT_GATE && ! $this->isPaid($s->id)) {
            $this->fail('status', 'Pembayaran belum lunas. Barang belum dapat berstatus ' . $this->nice($to) . '.');
        }
    }

    private function applyTransition(Shipment $s, string $to, string $type, ?string $reason): void
    {
        $from = $s->status;

        $s->status = $to;
        $s->save();

        DB::table('shipment_status_logs')->insert([
            'shipment_id' => $s->id,
            'from_status' => $from,
            'to_status'   => $to,
            'type'        => $type,
            'reason'      => $reason,
            'actor'       => $this->actor(),
            'created_at'  => now(),
        ]);
    }

    /** Data tarif (berat, volume, moda) dikunci selama ada tagihan aktif / lunas agar nominal tidak berubah diam-diam. */
    private function assertTariffUnlocked(Shipment $s, array $new): void
    {
        $locked = DB::table('shipment_payments')
            ->where('shipment_id', $s->id)
            ->whereIn('status', ['PENDING', 'PAID'])
            ->exists();

        if (! $locked) {
            return;
        }

        $changed = abs((float) ($new['weight'] ?? 0) - (float) $s->weight) > 0.0001
            || abs((float) ($new['volume'] ?? 0) - (float) $s->volume) > 0.0001
            || (string) ($new['transport_type'] ?? '') !== (string) $s->transport_type;

        if ($changed) {
            $this->fail('weight', 'Berat, volume, dan moda terkunci karena sudah ada tagihan aktif/lunas. Batalkan atau refund tagihan terlebih dahulu.');
        }
    }

    /* -------------------------- PEMBAYARAN -------------------------- */

    private function doPayCreate(Request $request, Shipment $shipment): string
    {
        $data = $request->validate([
            'method' => ['required', Rule::in(array_keys(self::METHODS))],
        ]);

        if ($this->isPaid($shipment->id)) {
            $this->fail('method', 'Pengiriman ini sudah lunas.');
        }

        $pending = DB::table('shipment_payments')
            ->where('shipment_id', $shipment->id)
            ->where('status', 'PENDING')
            ->where('expires_at', '>', now())
            ->lockForUpdate()
            ->first();

        if ($pending) {
            if ($pending->method === $data['method']) {
                return 'Tagihan ' . $pending->invoice_number . ' masih aktif.';
            }

            DB::table('shipment_payments')->where('id', $pending->id)->update([
                'status' => 'CANCELLED', 'note' => 'Diganti dengan metode lain', 'updated_at' => now(),
            ]);
        }

        $quote = $this->quote($shipment);
        $m = $quote['methods'][$data['method']];

        do {
            $invoice = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (DB::table('shipment_payments')->where('invoice_number', $invoice)->exists());

        DB::table('shipment_payments')->insert([
            'shipment_id'    => $shipment->id,
            'invoice_number' => $invoice,
            'method'         => $data['method'],
            'payment_code'   => $this->makeCode($data['method'], $invoice),
            'subtotal'       => $quote['subtotal'],
            'tax'            => $quote['tax'],
            'fee'            => $m['fee'],
            'total'          => $m['total'],
            'currency'       => 'IDR',
            'status'         => 'PENDING',
            'expires_at'     => now()->addMinutes(self::PAY_TTL_MINUTES),
            'gateway_ref'    => 'GW' . strtoupper(Str::random(10)),
            'created_by'     => $this->actor(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return 'Tagihan ' . $invoice . ' dibuat. Selesaikan pembayaran sebelum batas waktu.';
    }

    /** Sandbox: meniru webhook gateway (ditandatangani HMAC lalu diverifikasi di server). */
    private function doPaySimulate(Request $request, Shipment $shipment): string
    {
        if (! $this->sandbox()) {
            abort(403, 'Mode sandbox dinonaktifkan.');
        }

        $data = $request->validate([
            'outcome' => ['required', Rule::in(['success', 'failed', 'expired'])],
        ]);

        $p = DB::table('shipment_payments')
            ->where('shipment_id', $shipment->id)
            ->where('status', 'PENDING')
            ->lockForUpdate()
            ->first();

        if (! $p) {
            $this->fail('method', 'Tidak ada tagihan yang menunggu pembayaran.');
        }

        $payload = [
            'invoice' => $p->invoice_number,
            'status'  => ['success' => 'PAID', 'failed' => 'FAILED', 'expired' => 'EXPIRED'][$data['outcome']],
            'amount'  => (int) $p->total,
        ];

        return $this->processCallback($payload, $this->signature($payload));
    }

    /**
     * Pemrosesan callback gateway: verifikasi signature, idempoten, cocokkan nominal.
     * Struktur ini sama dengan yang dipakai webhook gateway sungguhan (Midtrans/Xendit).
     */
    private function processCallback(array $payload, string $signature): string
    {
        if (! hash_equals($this->signature($payload), $signature)) {
            abort(401, 'Signature callback tidak valid.');
        }

        $p = DB::table('shipment_payments')
            ->where('invoice_number', $payload['invoice'])
            ->lockForUpdate()
            ->first();

        if (! $p) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        if ($p->status !== 'PENDING') {
            return 'Callback diabaikan (idempoten): tagihan sudah berstatus ' . $p->status . '.';
        }

        if ((int) $p->total !== (int) $payload['amount']) {
            abort(422, 'Nominal callback tidak sama dengan tagihan.');
        }

        $new = $payload['status'];

        if ($new === 'PAID' && $p->expires_at && Carbon::parse($p->expires_at)->isPast()) {
            $new = 'EXPIRED';
        }

        DB::table('shipment_payments')->where('id', $p->id)->update([
            'status'     => $new,
            'paid_at'    => $new === 'PAID' ? now() : null,
            'note'       => $new === 'FAILED' ? 'Pembayaran ditolak oleh gateway' : ($new === 'EXPIRED' ? 'Melewati batas waktu' : null),
            'updated_at' => now(),
        ]);

        return match ($new) {
            'PAID'    => 'Pembayaran berhasil. Invoice ' . $p->invoice_number . ' LUNAS.',
            'FAILED'  => 'Pembayaran gagal. Silakan coba lagi atau pilih metode lain.',
            default   => 'Tagihan kedaluwarsa. Silakan buat tagihan baru.',
        };
    }

    private function doPayCancel(Shipment $shipment): string
    {
        $n = DB::table('shipment_payments')
            ->where('shipment_id', $shipment->id)
            ->where('status', 'PENDING')
            ->update(['status' => 'CANCELLED', 'note' => 'Dibatalkan oleh ' . $this->actor(), 'updated_at' => now()]);

        if (! $n) {
            $this->fail('method', 'Tidak ada tagihan aktif yang dapat dibatalkan.');
        }

        return 'Tagihan dibatalkan.';
    }

    private function doPayRefund(Request $request, Shipment $shipment): string
    {
        if (! $this->isAdmin()) {
            abort(403, 'Hanya Administrator yang dapat melakukan refund.');
        }

        $data = $request->validate(['note' => 'required|string|min:5|max:255']);

        $p = DB::table('shipment_payments')
            ->where('shipment_id', $shipment->id)
            ->where('status', 'PAID')
            ->lockForUpdate()
            ->first();

        if (! $p) {
            $this->fail('note', 'Tidak ada pembayaran lunas yang dapat di-refund.');
        }

        if (in_array($shipment->status, ['IN_TRANSIT', 'DELIVERED'], true)) {
            $this->fail('note', 'Refund tidak dapat dilakukan setelah barang dalam perjalanan atau terkirim.');
        }

        DB::table('shipment_payments')->where('id', $p->id)->update([
            'status' => 'REFUNDED', 'refunded_at' => now(), 'note' => trim($data['note']), 'updated_at' => now(),
        ]);

        return 'Refund ' . $p->invoice_number . ' berhasil diproses.';
    }

    private function isPaid(int $shipmentId): bool
    {
        return DB::table('shipment_payments')->where('shipment_id', $shipmentId)->where('status', 'PAID')->exists();
    }

    private function signature(array $payload): string
    {
        return hash_hmac('sha256', $payload['invoice'] . '|' . $payload['status'] . '|' . $payload['amount'], (string) config('app.key'));
    }

    private function sandbox(): bool
    {
        return (bool) config('services.cargopay.sandbox', true);
    }

    private function makeCode(string $method, string $invoice): string
    {
        $m = self::METHODS[$method];

        return match ($m['group']) {
            'va'      => $m['prefix'] . str_pad((string) random_int(1, 99999999999), 11, '0', STR_PAD_LEFT),
            'qris'    => 'CARGOPAY.QRIS.' . $invoice,
            'ewallet' => 'CARGOPAY.' . $method . '.' . $invoice,
            default   => 'CARGOPAY.CARD.' . $invoice,
        };
    }

    /** Hitung tarif dari berat/volume/moda. Server selalu menghitung ulang; angka dari browser tidak dipercaya. */
    private function quote(Shipment $s): array
    {
        $cfg = self::TARIFF[$s->transport_type] ?? self::TARIFF['darat'];
        $chargeable = max((float) $s->weight, (float) $s->volume * $cfg['kg_per_m3']);
        $raw = $chargeable * $cfg['per_kg'];
        $subtotal = (int) max(self::MIN_FARE, ceil($raw / 100) * 100);
        $tax = (int) round($subtotal * self::TAX_PCT / 100);
        $base = $subtotal + $tax;

        $methods = [];
        foreach (self::METHODS as $code => $m) {
            $fee = (int) round($base * $m['pct'] / 100) + $m['fixed'];
            $methods[$code] = ['label' => $m['label'], 'group' => $m['group'], 'fee' => $fee, 'total' => $base + $fee];
        }

        return [
            'chargeable'  => round($chargeable, 2),
            'per_kg'      => $cfg['per_kg'],
            'min_applied' => $raw < self::MIN_FARE,
            'tax_pct'     => self::TAX_PCT,
            'subtotal'    => $subtotal,
            'tax'         => $tax,
            'base'        => $base,
            'methods'     => $methods,
        ];
    }

    /* ------------------- STATE UNTUK VIEW / JSON ------------------- */

    /** Kumpulkan state pembayaran + log status + quote untuk banyak shipment sekaligus (2 query). */
    private function statesFor($shipments): array
    {
        $shipments = collect($shipments);
        $ids = $shipments->pluck('id')->all();

        if (! $ids) {
            return [];
        }

        $pays = DB::table('shipment_payments')->whereIn('shipment_id', $ids)->orderByDesc('id')->get()->groupBy('shipment_id');
        $logs = DB::table('shipment_status_logs')->whereIn('shipment_id', $ids)->orderByDesc('id')->get()->groupBy('shipment_id');
        $admin = $this->isAdmin();
        $out = [];

        foreach ($shipments as $s) {
            $rawPays = $pays[$s->id] ?? collect();
            $plist = $rawPays->take(10)->map(fn ($p) => $this->payArr($p))->values()->all();
            $llist = ($logs[$s->id] ?? collect())->take(10)->map(fn ($l) => [
                'from'   => $l->from_status,
                'to'     => $l->to_status,
                'type'   => $l->type,
                'reason' => $l->reason,
                'by'     => $l->actor,
                'time'   => Carbon::parse($l->created_at)->format('d M Y H:i'),
            ])->values()->all();

            $next = $this->allowedNext((string) $s->status);
            $paid = $rawPays->contains('status', 'PAID');

            $blocked = null;
            if ($next && self::PAYMENT_GATE !== null && $next[0] === self::PAYMENT_GATE && ! $paid) {
                $blocked = 'Pembayaran harus lunas sebelum barang dapat dijemput (PICKED UP). Buat tagihan dan selesaikan pembayaran terlebih dahulu.';
            }

            $out[$s->id] = [
                'status'     => $s->status,
                'next'       => $next,
                'blocked'    => $blocked,
                'pay'        => $this->pickPay($plist),
                'payments'   => $plist,
                'logs'       => $llist,
                'quote'      => $this->quote($s),
                'can_refund' => $admin && ! in_array($s->status, ['IN_TRANSIT', 'DELIVERED'], true),
            ];
        }

        return $out;
    }

    /** Pembayaran yang mewakili shipment: PAID > PENDING > terbaru. */
    private function pickPay(array $list): ?array
    {
        foreach (['PAID', 'PENDING'] as $st) {
            foreach ($list as $p) {
                if ($p['status'] === $st) {
                    return $p;
                }
            }
        }

        return $list[0] ?? null;
    }

    private function payArr($p): array
    {
        $m = self::METHODS[$p->method] ?? ['label' => $p->method, 'group' => 'va'];
        $dt = fn ($v, $f = 'd M Y H:i') => $v ? Carbon::parse($v)->format($f) : null;

        return [
            'id'           => $p->id,
            'invoice'      => $p->invoice_number,
            'method'       => $p->method,
            'method_label' => $m['label'],
            'group'        => $m['group'],
            'code'         => $p->payment_code,
            'subtotal'     => (int) $p->subtotal,
            'tax'          => (int) $p->tax,
            'fee'          => (int) $p->fee,
            'total'        => (int) $p->total,
            'status'       => $p->status,
            'expires_at'   => $p->expires_at ? Carbon::parse($p->expires_at)->toIso8601String() : null,
            'paid_at'      => $dt($p->paid_at),
            'refunded_at'  => $dt($p->refunded_at),
            'gateway_ref'  => $p->gateway_ref,
            'note'         => $p->note,
            'by'           => $p->created_by,
            'created'      => $dt($p->created_at),
        ];
    }

    /* ------------------------------ UTIL ------------------------------ */

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => [$message]]);
    }

    private function nice(?string $v): string
    {
        return str_replace('_', ' ', (string) $v);
    }

    private function actor(): string
    {
        return (string) (auth()->user()->name ?? 'system');
    }

    /** Cek Administrator. Sesuaikan dengan skema role di proyek kamu bila berbeda. */
    private function isAdmin(): bool
    {
        $u = auth()->user();

        if (! $u) {
            return false;
        }

        if (method_exists($u, 'hasRole')) {
            try {
                if ($u->hasRole('admin') || $u->hasRole('administrator')) {
                    return true;
                }
            } catch (\Throwable $e) {
                // lanjut ke pengecekan kolom
            }
        }

        $role = $u->role ?? $u->role_name ?? '';
        if (is_object($role)) {
            $role = $role->name ?? $role->slug ?? '';
        }

        return in_array(strtolower((string) $role), ['admin', 'administrator'], true) || (bool) ($u->is_admin ?? false);
    }

    /**
     * Buat tabel pembayaran & log status bila belum ada, lalu kedaluwarsakan tagihan lama.
     * Ini membuat fitur jalan hanya dengan 2 file. Untuk produksi/UAS, pindahkan ke migration
     * (lihat catatan di jawaban) supaya skema terdokumentasi dan bisa di-rollback.
     */
    private function bootstrapPayments(): void
    {
        if (! Cache::get('ct_pay_schema_v1')) {
            if (! Schema::hasTable('shipment_payments')) {
                Schema::create('shipment_payments', function (Blueprint $t) {
                    $t->id();
                    $t->unsignedBigInteger('shipment_id')->index();
                    $t->string('invoice_number', 40)->unique();
                    $t->string('method', 20);
                    $t->string('payment_code', 120)->nullable();
                    $t->unsignedBigInteger('subtotal');
                    $t->unsignedBigInteger('tax');
                    $t->unsignedBigInteger('fee');
                    $t->unsignedBigInteger('total');
                    $t->string('currency', 3)->default('IDR');
                    $t->string('status', 12)->index();
                    $t->timestamp('expires_at')->nullable();
                    $t->timestamp('paid_at')->nullable();
                    $t->timestamp('refunded_at')->nullable();
                    $t->string('gateway_ref', 40)->nullable();
                    $t->string('note')->nullable();
                    $t->string('created_by')->nullable();
                    $t->timestamps();
                });
            }

            if (! Schema::hasTable('shipment_status_logs')) {
                Schema::create('shipment_status_logs', function (Blueprint $t) {
                    $t->id();
                    $t->unsignedBigInteger('shipment_id')->index();
                    $t->string('from_status', 20)->nullable();
                    $t->string('to_status', 20);
                    $t->string('type', 12)->default('TRANSITION');
                    $t->text('reason')->nullable();
                    $t->string('actor')->nullable();
                    $t->timestamp('created_at')->nullable();
                });
            }

            Cache::forever('ct_pay_schema_v1', true);
        }

        DB::table('shipment_payments')
            ->where('status', 'PENDING')
            ->where('expires_at', '<', now())
            ->update(['status' => 'EXPIRED', 'note' => 'Melewati batas waktu', 'updated_at' => now()]);
    }

    /* ------------------------------------------------------------------ */

    private function rules(?int $ignoreId = null): array
    {
        return [
            'shipment_number' => 'required|string|max:50|unique:shipments,shipment_number' . ($ignoreId ? ',' . $ignoreId : ''),
            'customer_name' => 'required|string|max:255',
            'shipment_name' => 'required|string|max:255',
            'cargo_type' => 'required|string|max:100',
            'weight' => 'required|numeric|min:0.01',
            'volume' => 'nullable|numeric|min:0',
            'carrier' => 'required|string|max:255',
            'transport_type' => 'required|in:udara,laut,darat',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'estimated_departure' => 'required|date',
            'estimated_arrival' => 'required|date|after_or_equal:estimated_departure',
            'status' => 'sometimes|in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Kurs seluruh negara (basis USD), di-cache 1 jam.
     * Sumber utama open.er-api.com (±160 mata uang, tanpa API key),
     * cadangan currency-api via jsDelivr.
     */
    private function kurs()
    {
        $data = Cache::get('kurs_usd_v1');

        if (! $data) {
            $data = $this->ambilKurs();

            if ($data) {
                Cache::put('kurs_usd_v1', $data, now()->addHour());
                Cache::forever('kurs_usd_last', $data); // cadangan kalau sumber sedang down
            } else {
                $data = Cache::get('kurs_usd_last');
            }
        }

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Server kurs tidak dapat dihubungi.',
            ], 503);
        }

        return response()->json(['success' => true, 'base' => 'USD'] + $data);
    }

    private function ambilKurs(): ?array
    {
        try {
            $r = Http::timeout(10)->acceptJson()->get('https://open.er-api.com/v6/latest/USD');

            if ($r->ok() && $r->json('result') === 'success' && is_array($r->json('rates'))) {
                return [
                    'rates' => $r->json('rates'),
                    'updated_at' => $r->json('time_last_update_utc'),
                    'source' => 'open.er-api.com',
                ];
            }
        } catch (\Throwable $e) {
            // lanjut ke sumber cadangan
        }

        try {
            $r = Http::timeout(10)->acceptJson()
                ->get('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json');

            if ($r->ok() && is_array($r->json('usd'))) {
                $rates = [];
                foreach ($r->json('usd') as $code => $v) {
                    if (is_numeric($v) && $v > 0) {
                        $rates[strtoupper($code)] = (float) $v;
                    }
                }

                return [
                    'rates' => $rates,
                    'updated_at' => $r->json('date'),
                    'source' => 'currency-api',
                ];
            }
        } catch (\Throwable $e) {
            // gagal semua
        }

        return null;
    }
}
