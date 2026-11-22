<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengirimanController extends Controller
{
    // MENAMPILKAN DAFTAR PENGIRIMAN
    public function index(Request $request)
    {
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

        $shipments = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $carriers = Shipment::query()
            ->whereNotNull('carrier')
            ->where('carrier', '!=', '')
            ->distinct()
            ->orderBy('carrier')
            ->pluck('carrier');

        // Jumlah per status (untuk alur status 7 tahap).
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
            'nextNumber'
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
        $validated = $request->validate([
            'shipment_number' => 'required|string|max:50|unique:shipments,shipment_number',
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
            'status' => 'required|in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED',
            'notes' => 'nullable|string',
        ]);

        Shipment::create($validated);

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

    // UPDATE PENGIRIMAN
    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $validated = $request->validate([
            'shipment_number' => 'required|string|max:50|unique:shipments,shipment_number,' . $shipment->id,
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
            'status' => 'required|in:CREATED,BOOKED,PICKED_UP,IN_TRANSIT,DELIVERED',
            'notes' => 'nullable|string',
        ]);

        $shipment->update($validated);

        return redirect()
            ->route('pengiriman.index')
            ->with('success', 'Pengiriman berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $shipment = Shipment::findOrFail($id);

        $shipment->delete();

        return redirect()
            ->route('pengiriman.index')
            ->with('success', 'Pengiriman berhasil dihapus!');
    }
}
