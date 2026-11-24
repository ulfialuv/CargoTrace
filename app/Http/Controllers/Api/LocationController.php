<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** "Locations" di brief = tabel facilities (PORT, AIRPORT, DEPOT, WAREHOUSE). */
class LocationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Facility::query()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->query('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('city'), fn ($q) => $q->where('city', $request->query('city')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->query('search') . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('facility_code', 'like', $s));
            })
            ->orderBy('name');

        return $this->paginated($query->paginate($this->perPage($request)), FacilityResource::class);
    }

    public function show(string $id): JsonResponse
    {
        return $this->item(FacilityResource::class, Facility::findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        $facility = Facility::create($request->validate($this->rules()));

        return $this->item(FacilityResource::class, $facility, 'Lokasi berhasil dibuat.', 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $facility = Facility::findOrFail($id);
        $facility->update($request->validate($this->rules($facility->id)));

        return $this->item(FacilityResource::class, $facility->fresh(), 'Lokasi berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        Facility::findOrFail($id)->delete();

        return $this->ok(null, 'Lokasi berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'facility_code' => ['required', 'string', 'max:50', Rule::unique('facilities', 'facility_code')->ignore($ignoreId)],
            'name'          => ['required', 'string', 'max:255'],
            'type'          => ['required', Rule::in(['PORT', 'AIRPORT', 'DEPOT', 'WAREHOUSE'])],
            'address'       => ['required', 'string'],
            'city'          => ['required', 'string', 'max:255'],
            'province'      => ['required', 'string', 'max:255'],
            'country'       => ['nullable', 'string', 'max:255'],
            'latitude'      => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'     => ['nullable', 'numeric', 'between:-180,180'],
            'capacity'      => ['nullable', 'numeric', 'min:0'],
            'status'        => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
            'description'   => ['nullable', 'string'],
        ];
    }
}
