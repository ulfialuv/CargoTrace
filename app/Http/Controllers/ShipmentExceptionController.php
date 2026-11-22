<?php

namespace App\Http\Controllers;

use App\Models\ExceptionHistory;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

    private function relations(): array
    {
        return ['shipment', 'assignedStaff', 'reporter', 'histories.user'];
    }

    /**
     * Halaman utama: daftar, tambah, edit, detail, dan hapus ada di satu view.
     */
    public function index(Request $request)
    {
        $query = ShipmentException::with($this->relations());

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
        ];

        $typeStats = ShipmentException::query()
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        // Data untuk form tambah dan edit.
        $shipments = Shipment::orderBy('shipment_number')
            ->get(['id', 'shipment_number', 'customer_name']);

        $staff = User::orderBy('name')->get(['id', 'name']);

        // Pengecualian yang diminta lewat link lama (?id=...), meski tidak ada di halaman ini.
        $focus = $request->filled('id')
            ? ShipmentException::with($this->relations())->find($request->input('id'))
            : null;

        return view('exceptions.index', compact(
            'exceptions',
            'stats',
            'typeStats',
            'shipments',
            'staff',
            'focus'
        ));
    }

    // Link lama tetap berfungsi dan membuka form/detail di halaman index.
    public function create()
    {
        return redirect()->route('exceptions.index', ['buka' => 'tambah']);
    }

    public function show(string $id)
    {
        ShipmentException::findOrFail($id);

        return redirect()->route('exceptions.index', ['buka' => 'detail', 'id' => $id]);
    }

    public function edit(string $id)
    {
        ShipmentException::findOrFail($id);

        return redirect()->route('exceptions.index', ['buka' => 'edit', 'id' => $id]);
    }

    /**
     * Menyimpan pengecualian baru.
     */
    public function store(Request $request)
    {
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
            'attachment' => 'nullable|file|max:5120',
        ]);

        DB::transaction(function () use ($request, $validated) {

            if ($request->hasFile('attachment')) {
                $validated['attachment_path'] =
                    $request->file('attachment')->store('exceptions', 'public');
            }

            unset($validated['attachment']);

            $validated['status'] = 'OPEN';
            $validated['reported_by'] = auth()->id();

            $exception = ShipmentException::create($validated);

            ExceptionHistory::create([
                'exception_id' => $exception->id,
                'user_id' => auth()->id(),
                'action' => 'CREATED',
                'old_status' => null,
                'new_status' => 'OPEN',
                'note' => 'Pengecualian baru dilaporkan.',
            ]);
        });

        return redirect()
            ->route('exceptions.index')
            ->with('success', 'Pengecualian berhasil ditambahkan.');
    }

    /**
     * Memperbarui pengecualian.
     */
    public function update(Request $request, string $id)
    {
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
            'attachment' => 'nullable|file|max:5120',
        ]);

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
                ExceptionHistory::create([
                    'exception_id' => $exception->id,
                    'user_id' => auth()->id(),
                    'action' => 'STATUS_CHANGED',
                    'old_status' => $oldStatus,
                    'new_status' => $exception->status,
                    'note' => 'Status pengecualian diperbarui.',
                ]);
            }

            ExceptionHistory::create([
                'exception_id' => $exception->id,
                'user_id' => auth()->id(),
                'action' => 'UPDATED',
                'old_status' => $oldStatus,
                'new_status' => $exception->status,
                'note' => 'Data pengecualian diperbarui.',
            ]);
        });

        return redirect()
            ->route('exceptions.index')
            ->with('success', 'Pengecualian berhasil diperbarui.');
    }

    /**
     * Menghapus pengecualian.
     */
    public function destroy(string $id)
    {
        $exception = ShipmentException::findOrFail($id);

        if ($exception->attachment_path) {
            Storage::disk('public')->delete($exception->attachment_path);
        }

        $exception->delete();

        return redirect()
            ->route('exceptions.index')
            ->with('success', 'Pengecualian berhasil dihapus.');
    }
}
