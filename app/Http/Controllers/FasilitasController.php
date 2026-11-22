<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    /**
     * Aturan validasi bersama untuk tambah & edit.
     */
    private function rules(?Facility $facility = null): array
    {
        $unique = 'unique:facilities,facility_code' . ($facility ? ',' . $facility->id : '');

        return [
            'facility_code' => 'required|string|max:50|' . $unique,
            'name'          => 'required|string|max:255',
            'type'          => 'required|in:PORT,AIRPORT,DEPOT,WAREHOUSE',
            'address'       => 'required|string',
            'city'          => 'required|string|max:255',
            'province'      => 'required|string|max:255',
            'country'       => 'required|string|max:255',
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',
            'capacity'      => 'nullable|numeric|min:0',
            'status'        => 'required|in:ACTIVE,INACTIVE',
            'description'   => 'nullable|string',
        ];
    }

    /**
     * Halaman utama fasilitas.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type   = $request->input('type');
        $city   = $request->input('city');
        $status = $request->input('status');

        // Statistik
        $totalFasilitas = Facility::count();
        $totalPelabuhan = Facility::where('type', 'PORT')->count();
        $totalBandara   = Facility::where('type', 'AIRPORT')->count();
        $totalDarat     = Facility::whereIn('type', ['DEPOT', 'WAREHOUSE'])->count();
        $totalNonaktif  = Facility::where('status', 'INACTIVE')->count();

        // Query dengan pencarian dan filter
        $query = Facility::query();

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('facility_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('province', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        });

        $query->when($type, fn ($q) => $q->where('type', $type));
        $query->when($city, fn ($q) => $q->where('city', $city));
        $query->when($status, fn ($q) => $q->where('status', $status));

        // Data peta: semua fasilitas hasil filter yang punya koordinat
        // (bukan hanya halaman yang sedang tampil)
        $mapFacilities = (clone $query)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get()
            ->map(fn ($f) => $f->toArray())
            ->values()
            ->all();

        $facilities = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $cities = Facility::whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('fasilitas.index', compact(
            'facilities',
            'search',
            'type',
            'city',
            'status',
            'cities',
            'totalFasilitas',
            'totalPelabuhan',
            'totalBandara',
            'totalDarat',
            'totalNonaktif',
            'mapFacilities'
        ));
    }

    /**
     * Form tambah fasilitas.
     */
    public function create()
    {
        return view('fasilitas.create');
    }

    /**
     * Simpan fasilitas baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        Facility::create($validated);

        return redirect()
            ->route('fasilitas.index')
            ->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    /**
     * Detail fasilitas.
     */
    public function show(Facility $fasilita)
    {
        return view('fasilitas.show', [
            'facility' => $fasilita
        ]);
    }

    /**
     * Form edit fasilitas.
     */
    public function edit(Facility $fasilita)
    {
        return view('fasilitas.edit', [
            'facility' => $fasilita
        ]);
    }

    /**
     * Update fasilitas.
     */
    public function update(Request $request, Facility $fasilita)
    {
        $validated = $request->validate($this->rules($fasilita));

        $fasilita->update($validated);

        return redirect()
            ->route('fasilitas.index')
            ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    /**
     * Nonaktifkan fasilitas.
     */
    public function destroy(Facility $fasilita)
    {
        $fasilita->update(['status' => 'INACTIVE']);

        return redirect()
            ->route('fasilitas.index')
            ->with('success', 'Fasilitas berhasil dinonaktifkan.');
    }
}
