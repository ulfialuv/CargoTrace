<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CarrierController extends Controller
{
    /**
     * Aturan validasi bersama untuk tambah & edit.
     */
    private function rules(?Carrier $carrier = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                $carrier
                    ? Rule::unique('carriers', 'code')->ignore($carrier->id)
                    : Rule::unique('carriers', 'code'),
            ],
            'logo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'service_type' => ['required', 'string', 'max:100'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'email'        => ['nullable', 'email', 'max:255'],
            'address'      => ['nullable', 'string'],
            'description'  => ['nullable', 'string'],
            'status'       => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }

    /**
     * Jumlah pengiriman per perusahaan.
     * Tabel shipments menyimpan perusahaan sebagai teks di kolom "carrier"
     * (bukan carrier_id), jadi dicocokkan lewat nama atau kode.
     */
    private function shipmentCounts()
    {
        return DB::table('shipments')
            ->select('carrier', DB::raw('COUNT(*) as total'))
            ->whereNotNull('carrier')
            ->groupBy('carrier')
            ->pluck('total', 'carrier');
    }

    private function countFor(Carrier $carrier, $counts): int
    {
        return (int) (($counts[$carrier->name] ?? 0) + (
            strcasecmp($carrier->code, $carrier->name) === 0 ? 0 : ($counts[$carrier->code] ?? 0)
        ));
    }

    private function isUsed(Carrier $carrier): bool
    {
        return DB::table('shipments')
            ->whereIn('carrier', array_unique([$carrier->name, $carrier->code]))
            ->exists();
    }

    /**
     * Halaman daftar perusahaan pengiriman.
     */
    public function index(Request $request)
    {
        $query = Carrier::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('service_type', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $carriers = $query->latest()->paginate(10)->withQueryString();

        // Tempelkan jumlah pengiriman ke tiap perusahaan
        $counts = $this->shipmentCounts();
        $carriers->getCollection()->each(function ($carrier) use ($counts) {
            $carrier->shipments_count = $this->countFor($carrier, $counts);
        });

        // Statistik
        $totalCarriers    = Carrier::count();
        $activeCarriers   = Carrier::where('status', 'ACTIVE')->count();
        $inactiveCarriers = Carrier::where('status', 'INACTIVE')->count();

        $usedNames = $counts->keys()->all();
        $carriersInUse = Carrier::where(function ($q) use ($usedNames) {
            $q->whereIn('name', $usedNames)->orWhereIn('code', $usedNames);
        })->count();

        $serviceTypes = Carrier::whereNotNull('service_type')
            ->where('service_type', '!=', '')
            ->distinct()
            ->orderBy('service_type')
            ->pluck('service_type');

        return view('carriers.index', compact(
            'carriers',
            'totalCarriers',
            'activeCarriers',
            'inactiveCarriers',
            'carriersInUse',
            'serviceTypes'
        ));
    }

    public function create()
    {
        return redirect()->route('carriers.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('carriers', 'public');
        }

        Carrier::create($validated);

        return redirect()
            ->route('carriers.index')
            ->with('success', 'Perusahaan pengiriman berhasil ditambahkan.');
    }

    public function show($id)
    {
        $carrier = Carrier::findOrFail($id);

        return response()->json([
            'success'         => true,
            'carrier'         => $carrier,
            'total_shipments' => $this->countFor($carrier, $this->shipmentCounts()),
        ]);
    }

    public function edit($id)
    {
        return redirect()->route('carriers.index');
    }

    public function update(Request $request, $id)
    {
        $carrier = Carrier::findOrFail($id);

        $validated = $request->validate($this->rules($carrier));

        if ($request->hasFile('logo')) {
            if ($carrier->logo) {
                Storage::disk('public')->delete($carrier->logo);
            }

            $validated['logo'] = $request->file('logo')->store('carriers', 'public');
        } else {
            unset($validated['logo']);
        }

        $carrier->update($validated);

        return redirect()
            ->route('carriers.index')
            ->with('success', 'Data perusahaan pengiriman berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $carrier = Carrier::findOrFail($id);

        if ($this->isUsed($carrier)) {
            return redirect()
                ->route('carriers.index')
                ->with('error', 'Perusahaan tidak dapat dihapus karena masih digunakan oleh data pengiriman.');
        }

        if ($carrier->logo) {
            Storage::disk('public')->delete($carrier->logo);
        }

        $carrier->delete();

        return redirect()
            ->route('carriers.index')
            ->with('success', 'Perusahaan pengiriman berhasil dihapus.');
    }
}
