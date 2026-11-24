<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ExceptionResource;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExceptionController extends ApiController
{
    public function __construct(private ExceptionService $exceptions)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = ShipmentException::query()
            ->with('shipment:id,shipment_number')
            ->when($this->isCustomer($request), fn ($q) => $q->whereHas(
                'shipment', fn ($s) => $s->where('user_id', $request->user()->id)
            ))
            ->when($request->filled('shipment_id'), fn ($q) => $q->where('shipment_id', $request->query('shipment_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('severity'), fn ($q) => $q->where('severity', $request->query('severity')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->query('type')))
            ->latest('occurred_at');

        return $this->paginated($query->paginate($this->perPage($request)), ExceptionResource::class);
    }

    /** GET /shipments/{id}/exceptions */
    public function forShipment(Request $request, string $id): JsonResponse
    {
        $shipment = $this->findShipment($request, $id);

        $query = ShipmentException::query()
            ->with('shipment:id,shipment_number')
            ->where('shipment_id', $shipment->id)
            ->latest('occurred_at');

        return $this->paginated($query->paginate($this->perPage($request)), ExceptionResource::class);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $exception = ShipmentException::with('shipment:id,shipment_number,user_id')->findOrFail($id);

        if ($this->isCustomer($request) && (int) $exception->shipment?->user_id !== (int) $request->user()->id) {
            abort(403, 'Anda tidak berhak mengakses pengecualian ini.');
        }

        return $this->item(ExceptionResource::class, $exception);
    }

    /** POST /exceptions (shipment_id di body) */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->createRules() + [
            'shipment_id' => ['required', 'integer', 'exists:shipments,id'],
        ]);

        return $this->created($data, $request);
    }

    /** POST /shipments/{id}/exceptions */
    public function storeForShipment(Request $request, string $id): JsonResponse
    {
        $shipment = $this->findShipment($request, $id);

        $data = $request->validate($this->createRules());
        $data['shipment_id'] = $shipment->id;

        return $this->created($data, $request);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $exception = ShipmentException::findOrFail($id);

        $data = $request->validate([
            'type'        => ['sometimes', Rule::in(ExceptionService::TYPES)],
            'location'    => ['sometimes', 'string', 'max:255'],
            'occurred_at' => ['sometimes', 'date'],
            'severity'    => ['sometimes', Rule::in(ExceptionService::SEVERITIES)],
            'status'      => ['sometimes', Rule::in(ExceptionService::STATUSES)],
            'description' => ['sometimes', 'string'],
            'cause'       => ['nullable', 'string'],
            'impact'      => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'note'        => ['nullable', 'string', 'max:2000'],
        ]);

        $note = $data['note'] ?? null;
        unset($data['note']);

        $exception = $this->exceptions->update($exception, $data, $request->user()->id, $note);

        return $this->item(ExceptionResource::class, $exception, 'Pengecualian berhasil diperbarui.');
    }

    private function created(array $data, Request $request): JsonResponse
    {
        $exception = $this->exceptions->create($data, $request->user()->id, 'Pengecualian dilaporkan melalui API.');

        return $this->item(ExceptionResource::class, $exception, 'Pengecualian berhasil dibuat.', 201);
    }

    private function createRules(): array
    {
        return [
            'type'        => ['required', Rule::in(ExceptionService::TYPES)],
            'location'    => ['required', 'string', 'max:255'],
            'occurred_at' => ['required', 'date'],
            'severity'    => ['required', Rule::in(ExceptionService::SEVERITIES)],
            'status'      => ['nullable', Rule::in(ExceptionService::STATUSES)],
            'description' => ['required', 'string'],
            'cause'       => ['nullable', 'string'],
            'impact'      => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
