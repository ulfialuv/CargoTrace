<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarrierController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Carrier::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->query('search') . '%';
                $q->where(fn ($w) => $w->where('name', 'like', $s)->orWhere('code', 'like', $s));
            })
            ->orderBy('name');

        return $this->paginated($query->paginate($this->perPage($request)), CarrierResource::class);
    }

    public function show(string $id): JsonResponse
    {
        return $this->item(CarrierResource::class, Carrier::findOrFail($id));
    }

    public function store(Request $request): JsonResponse
    {
        $carrier = Carrier::create($request->validate($this->rules()));

        return $this->item(CarrierResource::class, $carrier, 'Carrier berhasil dibuat.', 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $carrier = Carrier::findOrFail($id);
        $carrier->update($request->validate($this->rules($carrier->id)));

        return $this->item(CarrierResource::class, $carrier->fresh(), 'Carrier berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $carrier = Carrier::findOrFail($id);

        if (Shipment::where('carrier', $carrier->name)->exists()) {
            return $this->fail('Carrier masih dipakai oleh pengiriman, tidak dapat dihapus.', 409);
        }

        $carrier->delete();

        return $this->ok(null, 'Carrier berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'code'         => ['required', 'string', 'max:50', Rule::unique('carriers', 'code')->ignore($ignoreId)],
            'service_type' => ['required', 'string', 'max:100'],
            'phone'        => ['nullable', 'string', 'max:50'],
            'email'        => ['nullable', 'email', 'max:255'],
            'address'      => ['nullable', 'string'],
            'description'  => ['nullable', 'string'],
            'status'       => ['required', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }
}
