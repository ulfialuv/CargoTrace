<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Basis semua controller API. Semua respons memakai format yang sama:
 *   sukses : { "success": true,  "message": "...", "data": ..., "meta": {...}? }
 *   gagal  : { "success": false, "message": "...", "errors": {...}? }
 */
abstract class ApiController extends Controller
{
    protected function ok(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    protected function item(string $resource, mixed $model, string $message = 'OK', int $status = 200): JsonResponse
    {
        return $this->ok((new $resource($model))->resolve(), $message, $status);
    }

    protected function paginated(LengthAwarePaginator $page, string $resource, string $message = 'OK'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $resource::collection($page->getCollection())->resolve(),
            'meta'    => [
                'current_page' => $page->currentPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
                'last_page'    => $page->lastPage(),
            ],
        ]);
    }

    protected function fail(string $message, int $status = 422, array $errors = []): JsonResponse
    {
        $body = ['success' => false, 'message' => $message];

        if ($errors) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $status);
    }

    protected function perPage(Request $request): int
    {
        return min(max((int) $request->query('per_page', 15), 1), 50);
    }

    protected function isCustomer(Request $request): bool
    {
        return $request->user()->role === 'customer';
    }

    /** Query shipment yang boleh dilihat user: customer hanya miliknya sendiri. */
    protected function shipmentQuery(Request $request): Builder
    {
        $query = Shipment::query();

        if ($this->isCustomer($request)) {
            $query->where('user_id', $request->user()->id);
        }

        return $query;
    }

    /** Cari shipment; 404 jika tidak ada, 403 jika customer membuka milik orang lain. */
    protected function findShipment(Request $request, int|string $id): Shipment
    {
        $shipment = Shipment::findOrFail($id);

        if ($this->isCustomer($request) && (int) $shipment->user_id !== (int) $request->user()->id) {
            abort(403, 'Anda tidak berhak mengakses pengiriman ini.');
        }

        return $shipment;
    }
}
