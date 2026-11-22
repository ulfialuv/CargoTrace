<?php

namespace App\Http\Controllers;

use App\Models\ExceptionHistory;
use App\Models\Shipment;
use App\Models\ShipmentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

    private const SEVERITIES = [
        'LOW',
        'MEDIUM',
        'HIGH',
    ];

    private const STATUSES = [
        'OPEN',
        'INVESTIGATING',
        'RESOLVED',
    ];

    private function relations(): array
    {
        return [
            'shipment',
            'assignedStaff',
            'reporter',
            'histories.user',
        ];
    }

    /**
     * Halaman utama Pengecualian Operator.
     */
    public function index(Request $request)
    {
        $query = ShipmentException::with($this->relations());

        // PENCARIAN
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

        // FILTER
        foreach (['type', 'severity', 'status'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
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

        // STATISTIK
        $stats = [
            'total' => ShipmentException::count(),
            'open' => ShipmentException::where('status', 'OPEN')->count(),
            'investigating' => ShipmentException::where('status', 'INVESTIGATING')->count(),
            'resolved' => ShipmentException::where('status', 'RESOLVED')->count(),
            'low' => ShipmentException::where('severity', 'LOW')->count(),
            'medium' => ShipmentException::where('severity', 'MEDIUM')->count(),
            'high' => ShipmentException::where('severity', 'HIGH')->count(),
        ];

        // STATISTIK JENIS MASALAH
        $typeStats = ShipmentException::query()
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        // DATA PENGIRIMAN UNTUK FORM
        $shipments = Shipment::orderBy('shipment_number')
            ->get([
                'id',
                'shipment_number',
                'customer_name',
                'carrier',
            ]);

        // DETAIL YANG DIPILIH
        $focus = $request->filled('id')
            ? ShipmentException::with($this->relations())
                ->find($request->id)
            : null;

        return view('operator.pengecualian.index', compact(
            'exceptions',
            'stats',
            'typeStats',
            'shipments',
            'focus'
        ));
    }

    /**
     * Menambahkan pengecualian baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipment_id' => [
                'required',
                'exists:shipments,id',
            ],
            'type' => [
                'required',
                Rule::in(self::TYPES),
            ],
            'location' => [
                'required',
                'string',
                'max:255',
            ],
            'occurred_at' => [
                'required',
                'date',
            ],
            'severity' => [
                'required',
                Rule::in(self::SEVERITIES),
            ],
            'description' => [
                'required',
                'string',
            ],
            'cause' => [
                'nullable',
                'string',
            ],
            'impact' => [
                'nullable',
                'string',
            ],
            'attachment' => [
                'nullable',
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
            ],
        ]);

        DB::transaction(function () use ($request, $validated) {

            if ($request->hasFile('attachment')) {
                $validated['attachment_path'] =
                    $request->file('attachment')
                        ->store('exceptions', 'public');
            }

            unset($validated['attachment']);

            $validated['status'] = 'OPEN';
            $validated['reported_by'] = auth()->id();
            $validated['assigned_to'] = auth()->id();

            $exception = ShipmentException::create($validated);

            ExceptionHistory::create([
                'exception_id' => $exception->id,
                'user_id' => auth()->id(),
                'action' => 'CREATED',
                'old_status' => null,
                'new_status' => 'OPEN',
                'note' => 'Pengecualian baru dilaporkan oleh Operator.',
            ]);
        });

        return redirect()
            ->route('operator.exceptions.index')
            ->with('success', 'Pengecualian berhasil dilaporkan.');
    }

    /**
     * Melihat detail pengecualian.
     */
    public function show(string $id)
    {
        $exception = ShipmentException::with($this->relations())
            ->findOrFail($id);

        return redirect()->route('operator.exceptions.index', [
            'id' => $exception->id,
        ]);
    }

    /**
     * Memperbarui penanganan pengecualian.
     */
    public function update(Request $request, string $id)
    {
        $exception = ShipmentException::findOrFail($id);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(self::STATUSES),
            ],
            'severity' => [
                'required',
                Rule::in(self::SEVERITIES),
            ],
            'cause' => [
                'nullable',
                'string',
            ],
            'impact' => [
                'nullable',
                'string',
            ],
            'note' => [
                'required',
                'string',
                'max:5000',
            ],
            'attachment' => [
                'nullable',
                'file',
                'max:5120',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
            ],
        ]);

        DB::transaction(function () use ($request, $exception, $validated) {

            $exception = ShipmentException::whereKey($exception->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldStatus = $exception->status;
            $newStatus = $validated['status'];

            // STATUS TIDAK BOLEH MUNDUR
            $statusOrder = array_flip(self::STATUSES);

            if ($statusOrder[$newStatus] < $statusOrder[$oldStatus]) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => 'Status pengecualian tidak boleh kembali ke tahap sebelumnya.',
                ]);
            }

            // STATUS RESOLVED BERSIFAT FINAL
            if ($oldStatus === 'RESOLVED' && $newStatus !== 'RESOLVED') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => 'Pengecualian yang sudah selesai tidak dapat dibuka kembali.',
                ]);
            }

            if ($request->hasFile('attachment')) {
                if ($exception->attachment_path) {
                    Storage::disk('public')->delete(
                        $exception->attachment_path
                    );
                }

                $validated['attachment_path'] =
                    $request->file('attachment')
                        ->store('exceptions', 'public');
            }

            unset($validated['attachment'], $validated['note']);

            if ($newStatus === 'RESOLVED' && $oldStatus !== 'RESOLVED') {
                $validated['resolved_at'] = now();
            }

            $exception->update($validated);

            // RIWAYAT PERUBAHAN STATUS
            if ($oldStatus !== $newStatus) {
                ExceptionHistory::create([
                    'exception_id' => $exception->id,
                    'user_id' => auth()->id(),
                    'action' => 'STATUS_CHANGED',
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'note' => $request->note,
                ]);
            } else {
                // RIWAYAT CATATAN PENANGANAN
                ExceptionHistory::create([
                    'exception_id' => $exception->id,
                    'user_id' => auth()->id(),
                    'action' => 'HANDLING_NOTE',
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'note' => $request->note,
                ]);
            }
        });

        return redirect()
            ->route('operator.exceptions.index', [
                'id' => $id,
            ])
            ->with('success', 'Penanganan pengecualian berhasil diperbarui.');
    }
}
