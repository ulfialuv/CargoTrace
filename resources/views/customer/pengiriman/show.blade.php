<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengiriman | CargoTrace</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6fb;
            color: #202b42;
        }

        .sidebar {
            width: 255px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #101d3a, #172b50);
            color: white;
            padding: 27px 19px;
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 32px;
            border-bottom: 1px solid rgba(255,255,255,.12);
        }

        .brand-icon {
            width: 43px;
            height: 43px;
            border-radius: 12px;
            background: #315efb;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
        }

        .brand-name { font-size: 19px; font-weight: 800; }
        .brand-name span { color: #6f9aff; }

        .menu-title {
            color: #8998b5;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin: 29px 12px 13px;
        }

        .menu { display: flex; flex-direction: column; gap: 7px; }

        .menu a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 13px 14px;
            color: #c1cbe0;
            text-decoration: none;
            border-radius: 10px;
            font-size: 13px;
        }

        .menu a i { width: 19px; text-align: center; }
        .menu a:hover, .menu a.active { background: #315efb; color: white; }
        .sidebar-bottom { margin-top: auto; }

        .sidebar-user {
            padding: 16px 10px;
            border-top: 1px solid rgba(255,255,255,.12);
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #315efb;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
        }

        .sidebar-user strong { display: block; font-size: 12px; }
        .sidebar-user small { color: #9eacc7; font-size: 10px; }

        .main { margin-left: 255px; padding: 29px 35px 45px; }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 24px;
        }

        .heading h1 { font-size: 23px; font-weight: 800; }
        .heading p { color: #8993a7; font-size: 11px; margin-top: 7px; }

        .btn {
            padding: 11px 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 9px;
            text-decoration: none;
            font-size: 10px;
            font-weight: 700;
        }

        .btn-blue { background: #315efb; color: white; }
        .btn-light { background: white; border: 1px solid #e9edf4; color: #59657b; }

        .hero {
            background: linear-gradient(115deg, #142647, #284d91);
            color: white;
            border-radius: 16px;
            padding: 25px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .hero small { color: #c7d5f1; font-size: 10px; }
        .hero h2 { font-size: 21px; margin: 8px 0; }
        .hero p { color: #d4def3; font-size: 11px; }
        .hero-icon { font-size: 54px; color: rgba(255,255,255,.2); padding-right: 20px; }

        .status {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 800;
        }

        .status-transit { background: #eaf0ff; color: #315efb; }
        .status-delivered { background: #e6f8ef; color: #15945d; }
        .status-created { background: #fff5df; color: #b7791f; }
        .status-delayed { background: #ffebed; color: #d6424d; }
        .status-default { background: #eef0f4; color: #667085; }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 17px;
            align-items: start;
        }

        .panel {
            background: white;
            border: 1px solid #e9edf4;
            border-radius: 13px;
            padding: 20px;
            margin-bottom: 17px;
            min-width: 0;
        }

        .panel h3 {
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 17px;
        }

        .panel h3 i { color: #315efb; margin-right: 7px; }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 17px 20px;
        }

        .info-item small {
            display: block;
            color: #8993a7;
            font-size: 9px;
            margin-bottom: 6px;
        }

        .info-item strong {
            font-size: 11px;
            color: #26334d;
            overflow-wrap: anywhere;
        }

        .muted { color: #a1a9b7 !important; font-weight: 500; }

        .timeline { position: relative; padding-left: 8px; }

        .timeline-item {
            position: relative;
            display: flex;
            gap: 13px;
            padding-bottom: 21px;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 9px;
            top: 22px;
            bottom: 0;
            width: 2px;
            background: #e4e9f2;
        }

        .timeline-dot {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #eaf0ff;
            color: #315efb;
            border-radius: 50%;
            font-size: 8px;
            z-index: 1;
        }

        .timeline-content { min-width: 0; }
        .timeline-content strong { display: block; font-size: 10px; }
        .timeline-content p { color: #7f8aa0; font-size: 10px; line-height: 1.6; margin-top: 5px; }
        .timeline-content small { color: #a1a9b7; font-size: 9px; display: block; margin-top: 5px; }

        .steps {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 7px;
            margin-top: 8px;
        }

        .step { text-align: center; position: relative; }

        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #eef0f4;
            color: #929bad;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 11px;
        }

        .step.done .step-circle { background: #315efb; color: white; }
        .step.current .step-circle { background: #eaf0ff; color: #315efb; border: 2px solid #315efb; }

        .step small {
            display: block;
            font-size: 8px;
            color: #8993a7;
            line-height: 1.5;
        }

        .step.done small, .step.current small { color: #273650; font-weight: 700; }

        .document-list, .exception-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .document-row, .exception-row {
            padding: 12px;
            border: 1px solid #edf0f5;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 10px;
        }

        .document-row i { color: #315efb; margin-right: 8px; }
        .document-row a { color: #315efb; text-decoration: none; font-weight: 700; }

        .empty {
            text-align: center;
            padding: 20px 10px;
            color: #9aa3b3;
            font-size: 10px;
        }

        .empty i { display: block; font-size: 23px; margin-bottom: 9px; }

        .footer { text-align: center; color: #9aa3b3; font-size: 10px; margin-top: 15px; }

        @media(max-width: 1000px) {
            .grid { grid-template-columns: 1fr; }
        }

        @media(max-width: 700px) {
            .sidebar { width: 72px; padding: 20px 9px; }
            .brand { padding: 0 4px 25px; }
            .brand-name, .menu-title, .menu a span, .sidebar-user div:last-child { display: none; }
            .menu a { justify-content: center; padding: 14px 8px; }
            .sidebar-user { justify-content: center; padding: 14px 0; }
            .main { margin-left: 72px; padding: 20px 14px; }
            .heading h1 { font-size: 18px; }
            .hero-icon { display: none; }
            .info-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
            .steps { gap: 3px; }
            .step small { font-size: 7px; }
        }
    </style>
</head>

<body>

@php
    $statusOrder = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];

    $statusLabels = [
        'CREATED' => 'Pengiriman Dibuat',
        'BOOKED' => 'Pengiriman Dijadwalkan',
        'PICKED_UP' => 'Barang Dijemput',
        'IN_TRANSIT' => 'Dalam Perjalanan',
        'DELIVERED' => 'Barang Diterima',
    ];

    $currentStatus = strtoupper($shipment->status ?? 'CREATED');

    $statusLabelsDisplay = [
        'CREATED' => 'Dibuat',
        'BOOKED' => 'Dijadwalkan',
        'PICKED_UP' => 'Telah Dijemput',
        'IN_TRANSIT' => 'Dalam Perjalanan',
        'DELIVERED' => 'Terkirim',
        'DELAYED' => 'Terlambat',
    ];

    $statusClass = match ($currentStatus) {
        'PICKED_UP', 'IN_TRANSIT' => 'status-transit',
        'DELIVERED' => 'status-delivered',
        'CREATED', 'BOOKED' => 'status-created',
        'DELAYED' => 'status-delayed',
        default => 'status-default',
    };

    $currentIndex = array_search($currentStatus, $statusOrder);
    $currentIndex = $currentIndex === false ? -1 : $currentIndex;

    $documents = [
        'Bukti / Tanda Terima' => $details['receipt_document'],
        'Dokumen Pengiriman' => $details['shipment_document'],
        'Invoice Pengiriman' => $details['invoice_document'],
    ];

    $formatDate = function ($value, $format = 'd M Y, H:i') {
        if (!$value) return null;

        try {
            return \Carbon\Carbon::parse($value)->format($format);
        } catch (\Throwable $e) {
            return $value;
        }
    };

    $exceptions = $shipment->exceptions->sortByDesc('created_at');
@endphp


<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon"><i class="fa-solid fa-truck-fast"></i></div>
        <div class="brand-name">Cargo<span>Trace</span></div>
    </div>

    <div class="menu-title">MENU CUSTOMER</div>

    <nav class="menu">
        <a href="{{ route('customer.dashboard') }}">
            <i class="fa-solid fa-house"></i><span>Dashboard</span>
        </a>

        <a href="{{ route('customer.pengiriman.index') }}" class="active">
            <i class="fa-solid fa-box"></i><span>Pengiriman Saya</span>
        </a>

        <a href="{{ url('/customer/pelacakan') }}">
            <i class="fa-solid fa-location-dot"></i><span>Pelacakan Pengiriman</span>
        </a>

        <a href="{{ url('/customer/notifikasi') }}">
            <i class="fa-solid fa-bell"></i><span>Notifikasi</span>
        </a>

        <a href="{{ url('/customer/profil') }}">
            <i class="fa-solid fa-user"></i><span>Profil Saya</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <nav class="menu">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
            </a>
        </nav>

        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}</div>
            <div>
                <strong>{{ auth()->user()->name }}</strong>
                <small>Customer</small>
            </div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</aside>


<main class="main">

    <header class="topbar">
        <div class="heading">
            <h1>Detail Pengiriman</h1>
            <p>Informasi lengkap mengenai pengiriman barang Anda.</p>
        </div>

        <a href="{{ route('customer.pengiriman.index') }}" class="btn btn-light">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </header>


    <section class="hero">
        <div>
            <small>NOMOR RESI PENGIRIMAN</small>
            <h2>{{ $shipment->shipment_number }}</h2>

            <p>
                {{ $shipment->shipment_name }}
                &nbsp; · &nbsp;
                {{ $shipment->carrier ?: 'Perusahaan belum tersedia' }}
            </p>

            <div style="margin-top:13px;">
                <span class="status {{ $statusClass }}">
                    {{ $statusLabelsDisplay[$currentStatus] ?? $currentStatus }}
                </span>
            </div>
        </div>

        <div class="hero-icon"><i class="fa-solid fa-box-open"></i></div>
    </section>


    <section class="panel">
        <h3><i class="fa-solid fa-route"></i> Tahapan Status Pengiriman</h3>

        <div class="steps">
            @foreach($statusOrder as $index => $status)
                @php
                    $stepClass = $index < $currentIndex
                        ? 'done'
                        : ($index === $currentIndex ? 'current' : '');
                @endphp

                <div class="step {{ $stepClass }}">
                    <div class="step-circle">
                        @if($index < $currentIndex)
                            <i class="fa-solid fa-check"></i>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>

                    <small>{{ $statusLabels[$status] }}</small>
                </div>
            @endforeach
        </div>

        @if($currentStatus === 'DELAYED')
            <div style="margin-top:18px;padding:13px;background:#fff1f1;border-radius:9px;color:#c8434d;font-size:10px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Pengiriman ini berstatus terlambat. Periksa riwayat tracking dan pengecualian untuk informasi lebih lanjut.
            </div>
        @endif
    </section>


    <div class="grid">

        <div>
            <section class="panel">
                <h3><i class="fa-solid fa-truck-fast"></i> A. Informasi Pengiriman</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <small>Nomor Pengiriman</small>
                        <strong>{{ $shipment->shipment_number }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Tanggal Dibuat</small>
                        <strong>{{ $formatDate($shipment->created_at, 'd M Y') ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Tanggal Keberangkatan</small>
                        <strong>{{ $formatDate($shipment->estimated_departure, 'd M Y') ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Estimasi Tiba</small>
                        <strong>{{ $formatDate($shipment->estimated_arrival, 'd M Y') ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Status Terkini</small>
                        <strong>{{ $statusLabelsDisplay[$currentStatus] ?? $currentStatus }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Jenis Transportasi</small>
                        <strong>{{ $shipment->transport_type ?: '-' }}</strong>
                    </div>
                </div>
            </section>


            <section class="panel">
                <h3><i class="fa-solid fa-box"></i> B. Informasi Barang</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <small>Nama Barang</small>
                        <strong>{{ $shipment->shipment_name ?: '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Jenis / Kategori</small>
                        <strong>{{ $shipment->cargo_type ?: '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Berat Barang</small>
                        <strong>{{ $shipment->weight !== null ? $shipment->weight . ' kg' : '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Volume Barang</small>
                        <strong>{{ $shipment->volume !== null ? $shipment->volume . ' m³' : '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Jumlah Barang</small>
                        <strong>{{ $details['quantity'] ?? 'Belum tersedia' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Deskripsi Barang</small>
                        <strong>{{ $details['description'] ?? 'Belum tersedia' }}</strong>
                    </div>
                </div>
            </section>


            <section class="panel">
                <h3><i class="fa-solid fa-user"></i> C. Informasi Pengirim</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <small>Nama Pengirim</small>
                        <strong>{{ $details['sender_name'] ?? 'Belum tersedia' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Nomor Telepon</small>
                        <strong>{{ $details['sender_phone'] ?? 'Belum tersedia' }}</strong>
                    </div>

                    <div class="info-item" style="grid-column:1/-1;">
                        <small>Alamat Pengirim</small>
                        <strong>{{ $details['sender_address'] ?? 'Belum tersedia' }}</strong>
                    </div>
                </div>
            </section>


            <section class="panel">
                <h3><i class="fa-solid fa-user-check"></i> D. Informasi Penerima</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <small>Nama Penerima</small>
                        <strong>{{ $details['receiver_name'] ?? 'Belum tersedia' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Nomor Telepon</small>
                        <strong>{{ $details['receiver_phone'] ?? 'Belum tersedia' }}</strong>
                    </div>

                    <div class="info-item" style="grid-column:1/-1;">
                        <small>Alamat Penerima</small>
                        <strong>{{ $details['receiver_address'] ?? 'Belum tersedia' }}</strong>
                    </div>
                </div>
            </section>
        </div>


        <div>
            <section class="panel">
                <h3><i class="fa-solid fa-building"></i> E. Perusahaan Pengiriman</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <small>Nama Perusahaan Logistik</small>
                        <strong>{{ $shipment->carrier ?: 'Belum tersedia' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Jenis Layanan / Transportasi</small>
                        <strong>{{ $details['service_type'] ?? 'Belum tersedia' }}</strong>
                    </div>
                </div>

                <p style="font-size:9px;color:#9aa3b3;margin-top:15px;">
                    Logo dan kontak perusahaan akan ditampilkan apabila data tersebut tersedia pada sistem.
                </p>
            </section>


            <section class="panel">
                <h3><i class="fa-solid fa-map-location-dot"></i> F. Informasi Lokasi</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <small>Negara Asal</small>
                        <strong>{{ $details['origin_country'] ?? 'Belum tersedia' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Kota Asal</small>
                        <strong>{{ $details['origin_city'] ?? $shipment->origin ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Negara Tujuan</small>
                        <strong>{{ $details['destination_country'] ?? 'Belum tersedia' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Kota Tujuan</small>
                        <strong>{{ $details['destination_city'] ?? $shipment->destination ?? '-' }}</strong>
                    </div>
                </div>
            </section>


            <section class="panel">
                <h3><i class="fa-solid fa-calendar-days"></i> G. Jadwal Pengiriman</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <small>Tanggal Dibuat</small>
                        <strong>{{ $formatDate($shipment->created_at, 'd M Y') ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Keberangkatan</small>
                        <strong>{{ $formatDate($shipment->estimated_departure, 'd M Y') ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Estimasi Tiba</small>
                        <strong>{{ $formatDate($shipment->estimated_arrival, 'd M Y') ?? '-' }}</strong>
                    </div>

                    <div class="info-item">
                        <small>Pembaruan Tracking Terakhir</small>
                        <strong>{{ $formatDate($latestTracking?->event_time) ?? 'Belum tersedia' }}</strong>
                    </div>
                </div>
            </section>


            <section class="panel">
                <h3><i class="fa-solid fa-money-bill-wave"></i> H. Biaya Pengiriman</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <small>Biaya Pengiriman</small>
                        <strong>
                            {{ $details['shipping_cost'] !== null
                                ? number_format((float) $details['shipping_cost'], 2, ',', '.')
                                : 'Belum tersedia' }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <small>Mata Uang</small>
                        <strong>{{ $details['currency'] ?? 'Belum tersedia' }}</strong>
                    </div>
                </div>

                <p style="font-size:9px;color:#9aa3b3;margin-top:14px;">
                    Informasi biaya ditampilkan apabila dicatat pada database.
                </p>
            </section>


            <section class="panel">
                <h3><i class="fa-solid fa-file-lines"></i> I. Dokumen Pengiriman</h3>

                <div class="document-list">
                    @php $hasDocument = false; @endphp

                    @foreach($documents as $label => $path)
                        @if($path)
                            @php $hasDocument = true; @endphp

                            <div class="document-row">
                                <span><i class="fa-solid fa-file-pdf"></i>{{ $label }}</span>
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($path) }}"
                                   target="_blank" rel="noopener">
                                    Lihat <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        @endif
                    @endforeach

                    @unless($hasDocument)
                        <div class="empty">
                            <i class="fa-regular fa-folder-open"></i>
                            Belum ada dokumen pengiriman yang tersedia.
                        </div>
                    @endunless
                </div>
            </section>
        </div>

    </div>


    <section class="panel">
        <h3><i class="fa-solid fa-clock-rotate-left"></i> J. Riwayat Perjalanan Pengiriman</h3>

        <div class="timeline">
            @forelse($shipment->trackingEvents->sortByDesc('event_time') as $event)
                <div class="timeline-item">
                    <div class="timeline-dot">
                        <i class="fa-solid fa-check"></i>
                    </div>

                    <div class="timeline-content">
                        <strong>{{ $statusLabelsDisplay[strtoupper($event->status ?? '')] ?? ($event->status ?? 'Pembaruan Status') }}</strong>
                        <p>
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $event->location ?: 'Lokasi belum tersedia' }}
                        </p>
                        @if($event->description)
                            <p>{{ $event->description }}</p>
                        @endif
                        <small>{{ $formatDate($event->event_time) ?? '-' }}</small>
                    </div>
                </div>
            @empty
                <div class="empty">
                    <i class="fa-solid fa-clock"></i>
                    Belum ada riwayat perjalanan untuk pengiriman ini.
                </div>
            @endforelse
        </div>
    </section>


    <section class="panel">
        <h3><i class="fa-solid fa-triangle-exclamation"></i> K. Informasi Pengecualian</h3>

        <div class="exception-list">
            @forelse($exceptions as $exception)
                <div class="exception-row">
                    <div>
                        <strong style="font-size:10px;">
                            {{ str_replace('_', ' ', $exception->type ?? 'Pengecualian') }}
                        </strong>

                        <p style="font-size:9px;color:#8993a7;margin-top:5px;">
                            {{ $exception->description ?: 'Tidak ada deskripsi.' }}
                        </p>

                        <p style="font-size:9px;color:#8993a7;margin-top:4px;">
                            {{ $formatDate($exception->occurred_at ?? $exception->created_at) ?? '-' }}
                            · {{ $exception->location ?: 'Lokasi tidak tersedia' }}
                        </p>
                    </div>

                    <span class="status {{ strtoupper($exception->status ?? '') === 'RESOLVED' ? 'status-delivered' : 'status-delayed' }}">
                        {{ $exception->status ?? 'OPEN' }}
                    </span>
                </div>
            @empty
                <div class="empty">
                    <i class="fa-solid fa-circle-check"></i>
                    Tidak ada pengecualian pada pengiriman ini.
                </div>
            @endforelse
        </div>
    </section>


    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:5px;">
        <a href="{{ url('/customer/pelacakan?tracking=' . urlencode($shipment->shipment_number)) }}"
           class="btn btn-blue">
            <i class="fa-solid fa-location-dot"></i> Lacak Pengiriman
        </a>

        <a href="{{ route('customer.pengiriman.index') }}" class="btn btn-light">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Pengiriman Saya
        </a>
    </div>


    <footer class="footer">
        © {{ date('Y') }} CargoTrace. Sistem Informasi Pelacakan Pengiriman.
    </footer>

</main>

</body>
</html>
