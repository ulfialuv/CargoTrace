<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerTrackingController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Hanya pengiriman milik Customer yang sedang login.
        $myShipments = Shipment::where('user_id', $userId)
            ->latest()
            ->get();

        // Pintasan: 6 resi terbaru, ditampilkan sebagai chip di bawah kolom pencarian.
        $recentShipments = $myShipments->take(6);

        $trackingNumber = trim((string) $request->query('tracking', ''));

        $shipment = null;
        $trackingEvents = collect();
        $exceptions = collect();
        $mapPoints = [];

        $latestTracking = null;
        $lastLocation = null;
        $lastUpdated = null;

        $delayHours = 0;
        $delaySeverity = null;
        $isDelayed = false;

        // Sisa waktu menuju ETA (jam). Null jika tidak relevan.
        $remainingHours = null;
        $progress = 0;

        if ($trackingNumber !== '') {

            $shipment = Shipment::with(['trackingEvents', 'exceptions'])
                ->where('user_id', $userId)
                ->where('shipment_number', $trackingNumber)
                ->first();

            if ($shipment) {

                // Riwayat dari yang paling lama ke terbaru.
                $trackingEvents = $shipment->trackingEvents
                    ->sortBy('event_time')
                    ->values();

                $latestTracking = $trackingEvents->last();
                $lastLocation = $latestTracking?->location;
                $lastUpdated = $latestTracking?->event_time;

                // Peta hanya memakai koordinat aktual dari database.
                foreach ($trackingEvents as $event) {
                    if (is_numeric($event->latitude) && is_numeric($event->longitude)) {
                        $mapPoints[] = [
                            'latitude' => (float) $event->latitude,
                            'longitude' => (float) $event->longitude,
                            'status' => $event->status,
                            'location' => $event->location,
                            'description' => $event->description,
                            'event_time' => $event->event_time
                                ? Carbon::parse($event->event_time)->format('d M Y H:i')
                                : '-',
                        ];
                    }
                }

                $exceptions = $shipment->exceptions
                    ->sortByDesc(fn ($e) => $e->occurred_at ?? $e->created_at)
                    ->values();

                $currentStatus = strtoupper($shipment->status ?? '');

                // Progres untuk garis perjalanan.
                $progress = match ($currentStatus) {
                    'CREATED' => 8,
                    'BOOKED' => 28,
                    'PICKED_UP' => 52,
                    'IN_TRANSIT' => 76,
                    'DELIVERED' => 100,
                    'DELAYED' => 60,
                    default => 4,
                };

                if ($shipment->estimated_arrival && $currentStatus !== 'DELIVERED') {
                    $eta = Carbon::parse($shipment->estimated_arrival);

                    if (now()->greaterThan($eta)) {
                        $isDelayed = true;
                        $delayHours = (int) floor(abs($eta->diffInMinutes(now())) / 60);

                        $delaySeverity = $delayHours <= 6
                            ? 'LOW'
                            : ($delayHours <= 24 ? 'MEDIUM' : 'HIGH');
                    } else {
                        $remainingHours = (int) ceil(abs(now()->diffInMinutes($eta)) / 60);
                    }
                } elseif ($currentStatus === 'DELAYED') {
                    // Status tertulis terlambat walau ETA belum dihitung terlewat.
                    $isDelayed = true;
                    $delaySeverity = 'LOW';
                }
            }
        }

        return view('customer.pelacakan.index', compact(
            'myShipments',
            'recentShipments',
            'trackingNumber',
            'shipment',
            'trackingEvents',
            'exceptions',
            'mapPoints',
            'latestTracking',
            'lastLocation',
            'lastUpdated',
            'delayHours',
            'delaySeverity',
            'isDelayed',
            'remainingHours',
            'progress'
        ));
    }
}
