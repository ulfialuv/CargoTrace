<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\ExceptionHistory;
use App\Models\Facility;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentTracking;
use App\Models\User;
use App\Services\ExceptionService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Data demo CargoTrace (sintetis tapi konsisten).
 *
 *  - Master data (carrier, lokasi) memakai firstOrCreate: data milikmu tidak ditimpa.
 *  - Shipment demo bernomor CT-DEMO-xxx dihapus lalu dibuat ulang tiap seeding,
 *    jadi seeder ini idempotent dan tidak menyentuh shipment lain.
 *  - Tanggal dibuat relatif terhadap "sekarang", jadi data selalu terlihat segar.
 *  - Tidak berjalan di environment production, kecuali DEMO_ALLOW_PRODUCTION=true (server demo).
 */
class DemoSeeder extends Seeder
{
    private const PREFIX = 'CT-DEMO-';

    public function run(): void
    {
        if (app()->environment('production') && ! config('cargotrace.demo.allow_in_production')) {
            $this->command?->warn('DemoSeeder dilewati: tidak dijalankan di environment production (set DEMO_ALLOW_PRODUCTION=true hanya untuk server demo).');

            return;
        }

        $users      = $this->seedUsers();
        $facilities = $this->seedFacilities();
        $this->seedCarriers();

        Shipment::where('shipment_number', 'like', self::PREFIX . '%')->get()->each->delete();

        $shipments = [];

        foreach ($this->scenarios() as $i => $spec) {
            $shipments[] = $this->seedShipment($i + 1, $spec, $facilities, $users);
        }

        $this->seedAuditLogs($users, $shipments);

        $this->command?->info('Data demo siap. Login: admin@cargotrace.com / operator1@cargotrace.test / customer1@cargotrace.test (password: lihat DEMO_PASSWORD di .env, bawaan "password").');
    }

    /* ------------------------------------------------------------------ users */

    private function seedUsers(): array
    {
        $password = (string) config('cargotrace.demo.password', 'password');

        $accounts = [
            'admin' => ['Admin CargoTrace', 'admin@cargotrace.com',        'admin',    '081200000001', 'Medan',    'Sumatera Utara'],
            'op1'   => ['Rina Operator',    'operator1@cargotrace.test',   'operator', '081200000002', 'Medan',    'Sumatera Utara'],
            'op2'   => ['Dedi Operator',    'operator2@cargotrace.test',   'operator', '081200000003', 'Surabaya', 'Jawa Timur'],
            'c1'    => ['Budi Santoso',     'customer1@cargotrace.test',   'customer', '081200000011', 'Jakarta',  'DKI Jakarta'],
            'c2'    => ['Siti Rahmawati',   'customer2@cargotrace.test',   'customer', '081200000012', 'Medan',    'Sumatera Utara'],
            'c3'    => ['Andi Wijaya',      'customer3@cargotrace.test',   'customer', '081200000013', 'Makassar', 'Sulawesi Selatan'],
        ];

        $users = [];

        foreach ($accounts as $key => [$name, $email, $role, $phone, $city, $province]) {
            $user = User::firstOrNew(['email' => $email]);

            if (! $user->exists) {
                $user->fill([
                    'name'     => $name,
                    'password' => $password,
                    'phone'    => $phone,
                    'city'     => $city,
                    'province' => $province,
                    'country'  => 'Indonesia',
                ]);
            }

            if (! $user->exists || blank($user->role)) {
                $user->role = $role;
            }

            $user->save();
            $users[$key] = $user;
        }

        return $users;
    }

    /* ------------------------------------------------------- facilities/carriers */

    private function seedFacilities(): array
    {
        $rows = [
            // code, name, type, city, province, lat, lng, capacity
            ['IDBLW', 'Pelabuhan Belawan',            'PORT',      'Medan',    'Sumatera Utara',   3.7826,  98.6932, 900000],
            ['IDTPP', 'Pelabuhan Tanjung Priok',      'PORT',      'Jakarta',  'DKI Jakarta',     -6.1049, 106.8803, 2500000],
            ['IDSUB', 'Pelabuhan Tanjung Perak',      'PORT',      'Surabaya', 'Jawa Timur',      -7.2117, 112.7313, 1200000],
            ['IDMAK', 'Pelabuhan Makassar',           'PORT',      'Makassar', 'Sulawesi Selatan',-5.1366, 119.4108, 800000],
            ['KNO',   'Bandara Kualanamu',            'AIRPORT',   'Deli Serdang', 'Sumatera Utara', 3.6422,  98.8853, 60000],
            ['CGK',   'Bandara Soekarno-Hatta',       'AIRPORT',   'Tangerang','Banten',          -6.1256, 106.6559, 150000],
            ['SUB',   'Bandara Juanda',               'AIRPORT',   'Sidoarjo', 'Jawa Timur',      -7.3798, 112.7869, 70000],
            ['WHCKR', 'Gudang Cikarang',              'WAREHOUSE', 'Bekasi',   'Jawa Barat',      -6.3060, 107.1700, 30000],
            ['WHSBY', 'Gudang Rungkut Surabaya',      'WAREHOUSE', 'Surabaya', 'Jawa Timur',      -7.3350, 112.7800, 25000],
            ['DPMDN', 'Depo Kontainer Medan',         'DEPOT',     'Medan',    'Sumatera Utara',   3.6000,  98.6800, 15000],
        ];

        $out = [];

        foreach ($rows as [$code, $name, $type, $city, $province, $lat, $lng, $capacity]) {
            $out[$code] = Facility::firstOrCreate(['facility_code' => $code], [
                'name'        => $name,
                'type'        => $type,
                'address'     => $name . ', ' . $city,
                'city'        => $city,
                'province'    => $province,
                'country'     => 'Indonesia',
                'latitude'    => $lat,
                'longitude'   => $lng,
                'capacity'    => $capacity,
                'status'      => 'ACTIVE',
                'description' => 'Data demo (sintetis).',
            ]);
        }

        return $out;
    }

    private function seedCarriers(): void
    {
        $rows = [
            ['NSL', 'Nusantara Sea Line',       'Sea Freight', '061-5550101'],
            ['AAC', 'Angkasa Air Cargo',        'Air Cargo',   '021-5550102'],
            ['TNT', 'Trans Nusantara Trucking', 'Land Freight','031-5550103'],
        ];

        foreach ($rows as [$code, $name, $service, $phone]) {
            Carrier::firstOrCreate(['code' => $code], [
                'name'         => $name,
                'service_type' => $service,
                'phone'        => $phone,
                'email'        => strtolower($code) . '@carrier.test',
                'address'      => 'Alamat demo ' . $name,
                'description'  => 'Carrier demo (sintetis).',
                'status'       => 'ACTIVE',
            ]);
        }
    }

    /* ---------------------------------------------------------------- scenarios */

    /**
     * departed_h : jam yang lalu keberangkatan (negatif = berangkat nanti)
     * duration_h : durasi perjalanan yang dijanjikan (expected_arrival)
     * shift_h    : ETA terkini dikurangi expected (jam). > 0 = terlambat
     */
    private function scenarios(): array
    {
        return [
            ['name' => 'Peralatan Dapur',       'cargo' => 'General Cargo', 'w' => 2400,  'v' => 14, 'carrier' => 'Nusantara Sea Line',       'mode' => 'laut',  'from' => 'IDBLW', 'to' => 'IDTPP', 'status' => 'CREATED',    'departed_h' => -48, 'duration_h' => 96,  'shift_h' => 0,  'cust' => 'c1'],
            ['name' => 'Suku Cadang Elektronik','cargo' => 'Electronics',   'w' => 180,   'v' => 1.2,'carrier' => 'Angkasa Air Cargo',        'mode' => 'udara', 'from' => 'KNO',   'to' => 'CGK',   'status' => 'BOOKED',     'departed_h' => -20, 'duration_h' => 4,   'shift_h' => 0,  'cust' => 'c2'],
            ['name' => 'Furnitur Rotan',        'cargo' => 'Furniture',     'w' => 950,   'v' => 22, 'carrier' => 'Trans Nusantara Trucking', 'mode' => 'darat', 'from' => 'WHCKR', 'to' => 'WHSBY', 'status' => 'PICKED_UP',  'departed_h' => -3,  'duration_h' => 36,  'shift_h' => 0,  'cust' => 'c3'],
            ['name' => 'Bahan Tekstil',         'cargo' => 'Textile',       'w' => 5200,  'v' => 40, 'carrier' => 'Nusantara Sea Line',       'mode' => 'laut',  'from' => 'IDBLW', 'to' => 'IDSUB', 'status' => 'IN_TRANSIT', 'departed_h' => 30,  'duration_h' => 120, 'shift_h' => 0,  'cust' => 'c1'],
            ['name' => 'Mesin Pengemas',        'cargo' => 'Machinery',     'w' => 8800,  'v' => 55, 'carrier' => 'Nusantara Sea Line',       'mode' => 'laut',  'from' => 'IDTPP', 'to' => 'IDMAK', 'status' => 'IN_TRANSIT', 'departed_h' => 60,  'duration_h' => 100, 'shift_h' => 4,  'cust' => 'c3'],
            ['name' => 'Komponen Otomotif',     'cargo' => 'Automotive',    'w' => 6400,  'v' => 48, 'carrier' => 'Nusantara Sea Line',       'mode' => 'laut',  'from' => 'IDBLW', 'to' => 'IDTPP', 'status' => 'IN_TRANSIT', 'departed_h' => 50,  'duration_h' => 90,  'shift_h' => 15, 'cust' => 'c2',
                'exception' => ['type' => 'CUSTOMS_HOLD', 'severity' => 'MEDIUM', 'status' => 'INVESTIGATING', 'ago_h' => 12, 'location' => 'Pelabuhan Tanjung Priok', 'desc' => 'Dokumen impor menunggu verifikasi Bea Cukai.', 'cause' => 'Dokumen PIB belum lengkap.', 'impact' => 'Bongkar muat tertunda.']],
            ['name' => 'Produk Makanan Kemasan','cargo' => 'Food',          'w' => 3100,  'v' => 30, 'carrier' => 'Nusantara Sea Line',       'mode' => 'laut',  'from' => 'IDSUB', 'to' => 'IDMAK', 'status' => 'IN_TRANSIT', 'departed_h' => 70,  'duration_h' => 72,  'shift_h' => 30, 'cust' => 'c3',
                'exception' => ['type' => 'WEATHER_DELAY', 'severity' => 'HIGH', 'status' => 'OPEN', 'ago_h' => 20, 'location' => 'Selat Makassar', 'desc' => 'Cuaca buruk memaksa kapal memperlambat laju.', 'cause' => 'Gelombang tinggi.', 'impact' => 'Estimasi tiba mundur lebih dari satu hari.']],
            ['name' => 'Alat Kesehatan',        'cargo' => 'Medical',       'w' => 640,   'v' => 6,  'carrier' => 'Nusantara Sea Line',       'mode' => 'laut',  'from' => 'IDBLW', 'to' => 'IDTPP', 'status' => 'DELIVERED',  'departed_h' => 200, 'duration_h' => 96,  'shift_h' => 0,  'cust' => 'c1'],
            ['name' => 'Perangkat Jaringan',    'cargo' => 'Electronics',   'w' => 210,   'v' => 1.8,'carrier' => 'Angkasa Air Cargo',        'mode' => 'udara', 'from' => 'SUB',   'to' => 'KNO',   'status' => 'DELIVERED',  'departed_h' => 70,  'duration_h' => 6,   'shift_h' => 0,  'cust' => 'c2',
                'exception' => ['type' => 'DAMAGED', 'severity' => 'HIGH', 'status' => 'RESOLVED', 'ago_h' => 66, 'resolved_after_h' => 20, 'location' => 'Bandara Kualanamu', 'desc' => 'Satu kardus penyok saat bongkar.', 'cause' => 'Penanganan kasar di area kargo.', 'impact' => 'Klaim asuransi diproses.']],
            ['name' => 'Hasil Bumi',            'cargo' => 'Agriculture',   'w' => 7200,  'v' => 60, 'carrier' => 'Nusantara Sea Line',       'mode' => 'laut',  'from' => 'IDMAK', 'to' => 'IDSUB', 'status' => 'DELIVERED',  'departed_h' => 150, 'duration_h' => 80,  'shift_h' => 8,  'cust' => 'c3',
                'exception' => ['type' => 'PORT_CONGESTION', 'severity' => 'MEDIUM', 'status' => 'RESOLVED', 'ago_h' => 100, 'resolved_after_h' => 30, 'location' => 'Pelabuhan Tanjung Perak', 'desc' => 'Antrean sandar kapal panjang.', 'cause' => 'Kepadatan dermaga.', 'impact' => 'Tiba 8 jam lebih lambat.']],
            ['name' => 'Perlengkapan Kantor',   'cargo' => 'Office Supply', 'w' => 320,   'v' => 4,  'carrier' => 'Angkasa Air Cargo',        'mode' => 'udara', 'from' => 'CGK',   'to' => 'SUB',   'status' => 'IN_TRANSIT', 'departed_h' => 2,   'duration_h' => 3,   'shift_h' => 0,  'cust' => 'c1'],
            ['name' => 'Bahan Bangunan',        'cargo' => 'Construction',  'w' => 12000, 'v' => 70, 'carrier' => 'Trans Nusantara Trucking', 'mode' => 'darat', 'from' => 'WHCKR', 'to' => 'WHSBY', 'status' => 'IN_TRANSIT', 'departed_h' => 20,  'duration_h' => 40,  'shift_h' => 2,  'cust' => 'c2',
                'exception' => ['type' => 'DOCUMENTATION_ISSUE', 'severity' => 'LOW', 'status' => 'OPEN', 'ago_h' => 6, 'location' => 'Pos timbang Cirebon', 'desc' => 'Surat jalan perlu dilengkapi ulang.', 'cause' => 'Nomor kontrak belum tercantum.', 'impact' => 'Berhenti sementara di pos timbang.']],
        ];
    }

    /* --------------------------------------------------------------- audit log */

    /** Penanda baris audit log milik seeder ini (supaya bisa dibuat ulang tanpa duplikat). */
    private const AUDIT_MARK = 'DemoSeeder';

    /**
     * Riwayat aktivitas contoh untuk halaman Audit Log (login, tambah data,
     * perubahan status, update exception, manajemen pengguna) dalam ~9 hari terakhir.
     */
    private function seedAuditLogs(array $users, array $shipments): void
    {
        AuditLog::where('user_agent', self::AUDIT_MARK)->delete();

        $now = now();

        // Login / logout harian
        $staffDays = [0, 1, 2, 3, 4];
        $custDays  = [0, 1, 2];

        foreach ($users as $key => $user) {
            $isStaff = in_array($key, ['admin', 'op1', 'op2'], true);

            foreach ($isStaff ? $staffDays : $custDays as $d) {
                $login = $now->copy()->subDays($d)->setTime(8 + ($d % 3), 5 + ($d * 7) % 50);
                $this->audit($user, 'LOGIN', 'Autentikasi', 'Login ke sistem', $login->min($now->copy()->subMinutes(5)));

                if ($isStaff && $d > 0) {
                    $this->audit($user, 'LOGOUT', 'Autentikasi', 'Logout dari sistem', $login->copy()->addHours(8));
                }
            }
        }

        // Manajemen pengguna (oleh admin)
        foreach ($users as $key => $user) {
            if ($key !== 'admin') {
                $this->audit($users['admin'], 'USER MANAGEMENT', 'Pengguna & Hak Akses',
                    'Menambah pengguna ' . $user->name . ' (' . $user->role . ')', $now->copy()->subDays(9), null, $user);
            }
        }

        // Master data: carrier
        foreach (Carrier::whereIn('code', ['NSL', 'AAC', 'TNT'])->get() as $carrier) {
            $this->audit($users['admin'], 'CREATE', 'Perusahaan Pengiriman', 'Menambah perusahaan pengiriman ' . $carrier->name,
                $now->copy()->subDays(9)->addMinutes(10), $carrier->code, $carrier);
        }

        // Shipment: dibuat -> tiap perubahan status
        foreach ($shipments as $shipment) {
            $this->audit($users['op1'], 'CREATE', 'Pengiriman', 'Menambah pengiriman ' . $shipment->shipment_number,
                $shipment->created_at, $shipment->shipment_number, $shipment,
                null, ['customer_name' => $shipment->customer_name, 'origin' => $shipment->origin, 'destination' => $shipment->destination, 'carrier' => $shipment->carrier]);

            $previous = 'CREATED';

            foreach ($shipment->trackingEvents()->get() as $event) {
                if ($event->status === $previous) {
                    continue;
                }

                $this->audit($users['op1'], 'STATUS CHANGE', 'Pengiriman',
                    'Mengubah status pengiriman ' . $shipment->shipment_number . ': ' . $previous . ' → ' . $event->status,
                    $event->event_time, $shipment->shipment_number, $shipment, ['status' => $previous], ['status' => $event->status]);

                $previous = $event->status;
            }
        }

        // Exception manual (bukan yang dibuat otomatis oleh sistem)
        $ids = collect($shipments)->pluck('id');

        foreach (ShipmentException::whereIn('shipment_id', $ids)->where('description', 'not like', ExceptionService::AUTO_DELAY . '%')->get() as $e) {
            $ref = collect($shipments)->firstWhere('id', $e->shipment_id)?->shipment_number;

            $this->audit($users['admin'], 'CREATE', 'Pengecualian', 'Menambah pengecualian ' . $e->type . ' #' . $e->id, $e->occurred_at, $ref, $e);

            if ($e->status !== 'OPEN') {
                $this->audit($users['op1'], 'EXCEPTION UPDATE', 'Pengecualian',
                    'Mengubah pengecualian ' . $e->type . ' #' . $e->id . ': OPEN → ' . $e->status,
                    $e->resolved_at ?? $e->occurred_at->copy()->addHours(3), $ref, $e, ['status' => 'OPEN'], ['status' => $e->status]);
            }
        }
    }

    private function audit(User $user, string $action, string $module, string $description, $when, ?string $reference = null, $subject = null, ?array $old = null, ?array $new = null): void
    {
        $log = new AuditLog([
            'user_id'          => $user->id,
            'user_name'        => $user->name,
            'user_email'       => $user->email,
            'user_role'        => $user->role,
            'action'           => $action,
            'module'           => $module,
            'description'      => $description,
            'subject_type'     => $subject ? class_basename($subject) : null,
            'subject_id'       => $subject?->getKey(),
            'reference_number' => $reference,
            'old_values'       => $old,
            'new_values'       => $new,
            'ip_address'       => '127.0.0.1',
            'user_agent'       => self::AUDIT_MARK,
        ]);

        $log->created_at = $when;
        $log->updated_at = $when;
        $log->save();
    }

    /* ----------------------------------------------------------------- shipment */

    private function seedShipment(int $n, array $spec, array $fac, array $users): Shipment
    {
        $now       = now();
        $from      = $fac[$spec['from']];
        $to        = $fac[$spec['to']];
        $customer  = $users[$spec['cust']];
        $departure = $now->copy()->subHours($spec['departed_h']);
        $expected  = $departure->copy()->addHours($spec['duration_h']);
        $estimated = $expected->copy()->addHours($spec['shift_h']);

        $shipment = Shipment::create([
            'shipment_number'     => self::PREFIX . str_pad((string) $n, 3, '0', STR_PAD_LEFT),
            'user_id'             => $customer->id,
            'customer_name'       => $customer->name,
            'shipment_name'       => $spec['name'],
            'cargo_type'          => $spec['cargo'],
            'weight'              => $spec['w'],
            'volume'              => $spec['v'],
            'carrier'             => $spec['carrier'],
            'transport_type'      => $spec['mode'],
            'origin'              => $from->name . ', ' . $from->city,
            'destination'         => $to->name . ', ' . $to->city,
            'estimated_departure' => $departure,
            'estimated_arrival'   => $estimated,
            'expected_arrival'    => $expected,
            'status'              => $spec['status'],
            'notes'               => 'Data demo (sintetis).',
        ]);

        $created = $now->copy()->subHours(40)->min($departure->copy()->subHours(30));
        $shipment->forceFill(['created_at' => $created, 'updated_at' => $now])->saveQuietly();

        $this->seedTracking($shipment, $spec, $from, $to, $users, $now, $created, $departure, $estimated, $expected);

        if (isset($spec['exception'])) {
            $this->seedException($shipment, $spec['exception'], $users, $now);
        }

        // Exception delay otomatis (severity dihitung EtaService dari ETA vs expected).
        app(ExceptionService::class)->syncDelayFor($shipment->fresh(), $users['admin']->id);

        return $shipment->fresh();
    }

    private function seedTracking(Shipment $s, array $spec, Facility $from, Facility $to, array $users, Carbon $now, Carbon $created, Carbon $departure, Carbon $estimated, Carbon $expected): void
    {
        $flow    = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];
        $reached = array_slice($flow, 0, array_search($spec['status'], $flow, true) + 1);
        $by      = $users['op1']->name;

        $booked = $created->copy()->addHours(8);
        $picked = $departure->copy()->subHours(2)->min($now->copy()->subHour())->max($booked->copy()->addHour());

        $times = [
            'CREATED'    => [$created,   $from, 'Pengiriman dibuat.'],
            'BOOKED'     => [$booked,    $from, 'Booking dikonfirmasi oleh carrier ' . $spec['carrier'] . '.'],
            'PICKED_UP'  => [$picked,    $from, 'Barang dijemput dan diterima di ' . $from->name . '.'],
            'IN_TRANSIT' => [$departure, $from, 'Berangkat dari ' . $from->name . ' menuju ' . $to->name . '.'],
            'DELIVERED'  => [$estimated, $to,   'Barang diterima di ' . $to->name . '.'],
        ];

        foreach ($reached as $status) {
            [$time, $place, $text] = $times[$status];

            $this->event($s, $status, $place->name, (float) $place->latitude, (float) $place->longitude, $time, $text, $by);
        }

        if ($spec['status'] !== 'IN_TRANSIT') {
            return;
        }

        // Posisi di tengah perjalanan (diinterpolasi antara asal dan tujuan) untuk garis rute di peta.
        $total    = max(1, $expected->getTimestamp() - $departure->getTimestamp());
        $progress = min(0.95, max(0.05, ($now->getTimestamp() - $departure->getTimestamp()) / $total));

        foreach (($progress > 0.3 ? [$progress / 2, $progress] : [$progress]) as $f) {
            $lat = (float) $from->latitude + ((float) $to->latitude - (float) $from->latitude) * $f;
            $lng = (float) $from->longitude + ((float) $to->longitude - (float) $from->longitude) * $f;
            $at  = $departure->copy()->addSeconds((int) (($now->getTimestamp() - $departure->getTimestamp()) * ($f / $progress)));

            $this->event($s, 'IN_TRANSIT', 'Dalam perjalanan menuju ' . $to->city, $lat, $lng, $at->min($now->copy()->subMinutes(30)),
                'Posisi terkini: ' . round($f * 100) . '% dari rute.', $by);
        }
    }

    private function event(Shipment $s, string $status, string $location, float $lat, float $lng, Carbon $time, string $text, string $by): void
    {
        ShipmentTracking::create([
            'shipment_id' => $s->id,
            'status'      => $status,
            'location'    => $location,
            'event_time'  => $time,
            'latitude'    => $lat,
            'longitude'   => $lng,
            'description' => $text,
            'updated_by'  => $by,
        ]);
    }

    private function seedException(Shipment $s, array $e, array $users, Carbon $now): void
    {
        $occurred = $now->copy()->subHours($e['ago_h']);
        $resolved = $e['status'] === 'RESOLVED' ? $occurred->copy()->addHours($e['resolved_after_h'] ?? 12) : null;

        $exception = ShipmentException::create([
            'shipment_id' => $s->id,
            'type'        => $e['type'],
            'location'    => $e['location'],
            'occurred_at' => $occurred,
            'severity'    => $e['severity'],
            'status'      => $e['status'],
            'description' => $e['desc'],
            'cause'       => $e['cause'] ?? null,
            'impact'      => $e['impact'] ?? null,
            'assigned_to' => $users['op1']->id,
            'reported_by' => $users['admin']->id,
            'resolved_at' => $resolved,
        ]);

        $this->history($exception, $users['admin']->id, 'CREATED', null, 'OPEN', 'Pengecualian dilaporkan.');

        if ($e['status'] !== 'OPEN') {
            $this->history($exception, $users['op1']->id, 'STATUS_CHANGED', 'OPEN', $e['status'],
                $e['status'] === 'RESOLVED' ? 'Masalah diselesaikan.' : 'Sedang diselidiki oleh operator.');
        }
    }

    private function history(ShipmentException $exception, int $userId, string $action, ?string $old, string $new, string $note): void
    {
        ExceptionHistory::create([
            'exception_id' => $exception->id,
            'user_id'      => $userId,
            'action'       => $action,
            'old_status'   => $old,
            'new_status'   => $new,
            'note'         => $note,
        ]);
    }
}
