<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class CustomerShipmentController extends Controller
{
    /**
     * Daftar pengiriman milik Customer + detail (ditampilkan lewat panel geser di halaman yang sama).
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Seluruh pengiriman milik Customer, dipakai untuk statistik dan pilihan filter.
        $myShipments = Shipment::with(['exceptions'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $totalShipments = $myShipments->count();

        $countStatus = fn (array $list) => $myShipments->filter(
            fn ($s) => in_array(strtoupper($s->status ?? ''), $list)
        )->count();

        $processing = $countStatus(['CREATED', 'BOOKED']);
        $inTransit = $countStatus(['PICKED_UP', 'IN_TRANSIT']);
        $delivered = $countStatus(['DELIVERED']);

        $delayed = $myShipments->filter(function ($shipment) {
            $hasOpenException = $shipment->exceptions->contains(
                fn ($e) => strtoupper($e->status ?? '') !== 'RESOLVED'
            );

            return strtoupper($shipment->status ?? '') === 'DELAYED' || $hasOpenException;
        })->count();


        // Query daftar pengiriman, selalu dibatasi berdasarkan user_id.
        $query = Shipment::where('user_id', $userId);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
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
            $query->where('origin', 'like', '%' . $request->origin . '%');
        }

        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $shipments = $query
            ->with(['trackingEvents', 'exceptions'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $carriers = $myShipments
            ->pluck('carrier')
            ->filter()
            ->unique()
            ->sort()
            ->values();


        // Kumpulan pengiriman yang detailnya perlu disiapkan.
        $detailShipments = $shipments->getCollection();

        // Link langsung ke detail: /customer/pengiriman?detail=ID
        // Tetap dibatasi user_id, sehingga pengiriman milik orang lain tidak bisa dibuka.
        $openId = null;

        if ($request->filled('detail')) {
            $requested = Shipment::with(['trackingEvents', 'exceptions'])
                ->where('user_id', $userId)
                ->where('id', $request->detail)
                ->first();

            if ($requested) {
                $openId = $requested->id;

                if (! $detailShipments->contains('id', $requested->id)) {
                    $detailShipments = $detailShipments->push($requested);
                }
            }
        }

        $details = [];
        $latestTrackings = [];

        foreach ($detailShipments as $shipment) {
            $details[$shipment->id] = $this->buildDetails($shipment);
            $latestTrackings[$shipment->id] = $shipment->trackingEvents
                ->sortByDesc('event_time')
                ->first();
        }

        return view('customer.pengiriman.index', compact(
            'shipments',
            'detailShipments',
            'details',
            'latestTrackings',
            'openId',
            'totalShipments',
            'processing',
            'inTransit',
            'delivered',
            'delayed',
            'carriers'
        ));
    }


    /**
     * Alamat lama /customer/pengiriman/{id} diarahkan ke halaman daftar
     * dengan panel detail terbuka, jadi route lama tetap berfungsi.
     */
    public function show($id)
    {
        return redirect()->route('customer.pengiriman.index', ['detail' => $id]);
    }


    /**
     * Membaca beberapa kemungkinan nama kolom.
     * Jika kolom tidak tersedia, nilainya null.
     */
    private function buildDetails(Shipment $shipment): array
    {
        $read = function (array $keys) use ($shipment) {
            foreach ($keys as $key) {
                $value = $shipment->getAttribute($key);

                if ($value !== null && $value !== '') {
                    return $value;
                }
            }

            return null;
        };

        return [
            'description' => $read(['description', 'goods_description', 'notes']),
            'quantity' => $read(['quantity', 'item_quantity', 'total_quantity']),

            'sender_name' => $read(['sender_name', 'shipper_name']),
            'sender_phone' => $read(['sender_phone', 'shipper_phone']),
            'sender_address' => $read(['sender_address', 'shipper_address']),

            'receiver_name' => $read(['receiver_name', 'recipient_name']),
            'receiver_phone' => $read(['receiver_phone', 'recipient_phone']),
            'receiver_address' => $read(['receiver_address', 'recipient_address']),

            'origin_country' => $read(['origin_country', 'country_origin']),
            'origin_city' => $read(['origin_city', 'city_origin']),
            'destination_country' => $read(['destination_country', 'country_destination']),
            'destination_city' => $read(['destination_city', 'city_destination']),

            'shipping_cost' => $read(['shipping_cost', 'shipment_cost', 'total_cost']),
            'currency' => $read(['currency', 'cost_currency']),
            'service_type' => $read(['service_type', 'transport_type']),

            'receipt_document' => $read(['receipt_document', 'receipt_path']),
            'shipment_document' => $read(['shipment_document', 'document_path']),
            'invoice_document' => $read(['invoice_document', 'invoice_path']),
        ];
    }
}
