<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Pembaruan Status - CargoTrace</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
:root{--navy:#061533;--blue:#2563eb;--sky:#38bdf8;--ink:#12306a;--mut:#6c83ab;--line:#e0e9fa;--bg:#edf3ff;--h:'Sora',sans-serif}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--ink);font-size:13px}
a{color:inherit}
:focus-visible{outline:2px solid var(--blue);outline-offset:2px}

/* SIDEBAR */
.sidebar{position:fixed;inset:0 auto 0 0;width:272px;padding:26px 18px 18px;display:flex;flex-direction:column;background:linear-gradient(180deg,#061533 0%,#0a2257 65%,#0f3a8f 100%);color:#fff;z-index:1000;overflow-y:auto}
.sidebar::before{content:"";position:absolute;width:300px;height:300px;top:-140px;right:-150px;border-radius:50%;background:radial-gradient(#38bdf860,#0000 70%);pointer-events:none}
.brand{display:flex;align-items:center;gap:11px;padding:0 8px;position:relative}
.brand-logo{display:grid;place-items:center;width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-size:20px;box-shadow:0 8px 22px #2563eb77}
.brand-name{font:800 22px var(--h);letter-spacing:-.5px}
.brand-sub{margin:8px 8px 26px;font-size:9.5px;letter-spacing:.8px;color:#9fbcea;position:relative}
.s-title{margin:0 12px 10px;font-size:10px;font-weight:700;letter-spacing:1.3px;color:#6f95d6}
.s-wrap{position:relative;margin-bottom:22px}
.s-menu{display:flex;flex-direction:column;gap:4px;position:relative}
.s-link{display:flex;align-items:center;gap:13px;min-height:44px;padding:10px 14px;border-radius:13px;color:#dce8ff;text-decoration:none;font-weight:500;transition:.2s}
.s-link i{width:18px;text-align:center;font-size:14px;color:#8fb3ec}
.s-link:hover{background:#ffffff18}
.s-link.active{background:linear-gradient(90deg,#2563eb,#3b8bf5);color:#fff;font-weight:700;box-shadow:0 10px 24px #2563eb55}
.s-link.active i{color:#fff}
.bdg{margin-left:auto;min-width:20px;padding:2px 6px;border-radius:10px;background:#e5484d;color:#fff;font-size:10px;font-weight:700;text-align:center;font-style:normal}
.s-user{margin-top:auto;position:relative;padding:14px;border-radius:16px;background:#ffffff14;border:1px solid #ffffff2b}
.s-user .who{display:flex;align-items:center;gap:11px}
.avatar{display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-weight:800}
.s-user b{display:block;font-size:12.5px}.s-user small{font-size:10px;color:#a9c4f0}
.s-user button{width:100%;margin-top:12px;padding:9px;border:1px solid #ffffff33;border-radius:10px;background:transparent;color:#dce8ff;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.s-user button:hover{background:#ffffff1c}

/* MAIN */
.main{margin-left:272px;min-height:100vh;background:radial-gradient(circle at 100% 0,#cfe3ff 0,#0000 380px),radial-gradient(circle at 0 100%,#d7e8ff 0,#0000 340px),var(--bg)}
.content{padding:26px 32px 40px}
.topline{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:18px}
.topline h1{font:800 26px var(--h);color:var(--navy);letter-spacing:-.6px}
.topline p{margin-top:5px;color:var(--mut)}
.date-box{display:flex;align-items:center;gap:9px;padding:13px 17px;background:#fff;border:1px solid var(--line);border-radius:14px;color:#3d68a8;font-weight:600;white-space:nowrap}
.date-box i{color:var(--blue)}
.alert{padding:13px 17px;margin-bottom:16px;border-radius:12px;background:#e5f7ed;color:#187344}
.alert i{margin-right:6px}
.alert-error{background:#ffe9e9;color:#c92f3a}

/* STAT */
.mstats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:22px}
.mstat{display:flex;align-items:center;gap:14px;padding:18px;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 10px 30px #2563eb0d;transition:.25s}
.mstat:hover{transform:translateY(-4px)}
.mstat .ico{display:grid;place-items:center;flex:none;width:48px;height:48px;border-radius:15px;font-size:19px}
.mstat small{display:block;font-size:11.5px;color:var(--mut);font-weight:600}
.mstat b{display:block;margin-top:5px;font:800 26px var(--h);color:var(--navy);line-height:1}
.mstat em{display:block;margin-top:5px;font-style:normal;font-size:10.5px;color:#8ca6c4}

/* PANEL */
.panel{padding:24px;background:#fff;border:1px solid var(--line);border-radius:26px;box-shadow:0 10px 30px #2563eb0d}
.ph{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px}
.ph h2{font:700 17px var(--h);color:var(--navy);letter-spacing:-.3px}
.ph p{margin-top:6px;font-size:12px;color:var(--mut)}
.sect{margin-bottom:24px}
.row{display:grid;gap:20px;margin-bottom:24px}
.r-c{grid-template-columns:minmax(0,1fr) minmax(0,1fr);align-items:start}

/* FILTER + TABEL */
.filters{display:flex;gap:10px;flex-wrap:wrap}
.filters input,.filters select{padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:var(--ink);font:12px 'Plus Jakarta Sans',sans-serif}
.filters input{flex:1;min-width:150px}
.filters input:focus,.filters select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:10px;cursor:pointer;font:700 11px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;transition:.2s}
.btn:hover{filter:brightness(1.1)}
.btn-light{background:#e8f1ff;color:#1d4ed8}
.btn-block{width:100%;padding:14px;font-size:12px}
.table-wrap{overflow-x:auto;margin-top:16px}
table{width:100%;border-collapse:collapse;min-width:820px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:11px;font-weight:700;padding:13px 14px}
th:first-child{border-radius:12px 0 0 12px}th:last-child{border-radius:0 12px 12px 0}
td{padding:14px;border-bottom:1px solid #edf3fa;color:#41608a;white-space:nowrap}
tbody tr:hover{background:#f6faff}
td.num{font-weight:800;color:var(--navy)}
td.empty{text-align:center;padding:32px;color:#8ca6c4}
.route{display:flex;align-items:center;gap:8px}.route i{color:#9db6dc;font-size:10px}
.status{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10px;font-weight:700;background:#e5f1ff;color:#2876c5}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.s-booked{background:#f1edff;color:#7552ae}.s-picked{background:#f0eaff;color:#7c3aed}.s-transit{background:#fff4dc;color:#b77900}.s-delivered{background:#e8f7ef;color:#26734d}
.btn-detail{background:#e8f1ff;color:#1d4ed8}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top:16px;font-size:11px;color:#8ca6c4}
.pager nav svg{width:16px;height:16px}

/* DETAIL */
.resi{padding:16px 18px;margin-bottom:16px;border-radius:16px;background:linear-gradient(135deg,#061533,#1d4ed8);color:#fff;box-shadow:0 12px 26px #1d4ed84d}
.resi small{display:block;font-size:11px;color:#b9d0f5;margin-bottom:6px}
.resi b{font:800 19px var(--h);letter-spacing:.4px}
.d-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.d-item label{display:block;color:#8ca6c4;font-size:10px;margin-bottom:5px}
.d-item strong{font-size:12.5px;color:var(--navy);overflow-wrap:anywhere}
.now-box{margin-top:20px;padding:15px 17px;border-radius:14px;background:#f1f6ff}
.now-box small{display:block;font-size:11px;color:var(--mut);margin-bottom:6px}
.now-box strong{font:700 16px var(--h)}
.op-sub{font:700 14px var(--h);color:var(--navy);margin:24px 0 16px}
.steps{display:flex;justify-content:space-between;gap:6px}
.step{flex:1;text-align:center}
.step .c{display:grid;place-items:center;width:32px;height:32px;margin:0 auto 8px;border-radius:50%;background:#e8eef9;color:#8ca6c4;font-size:11px;font-weight:800}
.step.done .c{background:var(--blue);color:#fff;box-shadow:0 6px 14px #2563eb55}
.step span{display:block;font-size:10px;color:#8ca6c4}
.step.done span{color:var(--blue);font-weight:700}

/* FORM */
.op-form{display:flex;flex-direction:column;gap:14px}
.op-field label{display:block;font-size:11px;font-weight:700;color:#5d7aa5;margin-bottom:7px}
.op-field label i{color:#e5484d;font-style:normal}
.op-field input,.op-field select,.op-field textarea{width:100%;padding:11px 12px;border:1px solid #dce8f8;border-radius:10px;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink);background:#fbfdff}
.op-field input[readonly]{background:#eef3fc;color:#7385a6}
.op-field input:focus,.op-field select:focus,.op-field textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.op-field textarea{min-height:85px;resize:vertical}
.op-field small{display:block;margin-top:6px;font-size:10.5px;color:#8ca6c4}
.two{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.done-box,.empty-box{padding:34px 16px;text-align:center;color:var(--mut)}
.done-box i,.empty-box i{display:grid;place-items:center;width:58px;height:58px;margin:0 auto 14px;border-radius:50%;font-size:24px;background:#e5f7ed;color:#16a34a}
.empty-box i{background:#e8f1ff;color:var(--blue)}
.done-box h3,.empty-box h3{font:700 16px var(--h);color:var(--navy);margin-bottom:7px}

/* RESPONSIVE */
@media(max-width:1100px){.r-c{grid-template-columns:1fr}.mstats{grid-template-columns:repeat(2,minmax(0,1fr))}.content{padding:20px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .topline{flex-direction:column;align-items:flex-start}.panel{padding:18px}
}
@media(max-width:600px){.mstats,.d-grid,.two{grid-template-columns:1fr}.steps{flex-wrap:wrap}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>
<body>

@php
    // Ambil URL dari route pertama yang tersedia, kalau tidak ada pakai '#'
    $rt = function (array $names) {
        foreach ($names as $n) {
            if (Route::has($n)) return route($n);
        }
        return '#';
    };

    $pendingCount = \App\Models\Shipment::whereIn('status', ['CREATED', 'BOOKED'])->count();

    // [label, ikon, daftar nama route, pola URL aktif, badge]
    $menu = [
        ['Dashboard', 'fa-border-all', ['operator.dashboard', 'operator.index'], ['operator/dashboard'], 0],
        ['Daftar Pengiriman', 'fa-box', ['operator.pengiriman.index'], ['operator/pengiriman*'], $pendingCount],
        ['Pelacakan', 'fa-location-dot', ['operator.pelacakan.index'], ['operator/pelacakan*'], 0],
        ['Pembaruan Status', 'fa-arrows-rotate', ['operator.pembaruan-status.index', 'operator.status.index', 'operator.status-update.index', 'operator.update-status.index'], ['operator/pembaruan-status*', 'operator/status*'], 0],
        ['Pengecualian', 'fa-triangle-exclamation', ['operator.exceptions.index', 'operator.pengecualian.index'], ['operator/exceptions*', 'operator/pengecualian*'], 0],
        ['Notifikasi', 'fa-bell', ['operator.notifications.index', 'operator.notifikasi.index'], ['operator/notifications*', 'operator/notifikasi*'], 0],
    ];

    $statusLabels = [
        'CREATED' => 'Dibuat',
        'BOOKED' => 'Dipesan',
        'PICKED_UP' => 'Diambil Kurir',
        'IN_TRANSIT' => 'Dalam Perjalanan',
        'DELIVERED' => 'Terkirim',
    ];

    $statusColors = [
        'CREATED' => '#2876c5',
        'BOOKED' => '#7552ae',
        'PICKED_UP' => '#7c3aed',
        'IN_TRANSIT' => '#b77900',
        'DELIVERED' => '#26734d',
    ];

    $statusClass = fn ($s) => match (strtoupper((string) $s)) {
        'BOOKED'     => 's-booked',
        'PICKED_UP'  => 's-picked',
        'IN_TRANSIT' => 's-transit',
        'DELIVERED'  => 's-delivered',
        default      => '',
    };

    $statusOrder = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];

    $nextStatus = null;

    if ($selected && $selected->status !== 'DELIVERED') {
        $currentIndex = array_search($selected->status, $statusOrder);
        $nextStatus = $statusOrder[$currentIndex + 1] ?? null;
    }
@endphp

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="brand">
        <div class="brand-logo"><i class="fa-solid fa-route"></i></div>
        <div class="brand-name">CargoTrace</div>
    </div>
    <div class="brand-sub">LOGISTICS &amp; SHIPMENT TRACKING</div>

    <div class="s-wrap">
        <div class="s-title">MENU OPERATOR</div>
        <nav class="s-menu">
            @foreach($menu as $m)
                <a href="{{ $rt($m[2]) }}" class="s-link {{ request()->is(...$m[3]) ? 'active' : '' }}">
                    <i class="fa-solid {{ $m[1] }}"></i>
                    <span>{{ $m[0] }}</span>
                    @if($m[4] > 0)<em class="bdg">{{ $m[4] }}</em>@endif
                </a>
            @endforeach
        </nav>
    </div>

    <div class="s-user">
        <div class="who">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'O', 0, 1)) }}</div>
            <div><b>{{ auth()->user()->name ?? 'Operator' }}</b><small>Petugas / Operator</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </form>
    </div>
</aside>

<main class="main">
<section class="content">

    {{-- HEADER --}}
    <div class="topline">
        <div>
            <h1>Pembaruan Status</h1>
            <p>Kelola perubahan status dan riwayat perjalanan pengiriman secara real-time.</p>
        </div>
        <div class="date-box"><i class="fa-regular fa-calendar"></i> {{ now()->translatedFormat('d M Y') }}</div>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <strong><i class="fa-solid fa-triangle-exclamation"></i>Pembaruan gagal!</strong>
            <ul style="margin:7px 0 0 18px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- STATISTIK --}}
    <div class="mstats">
        <div class="mstat">
            <div class="ico" style="background:#e8f1ff;color:#2563eb"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div><small>Total Pengiriman</small><b>{{ number_format($totalShipments) }}</b><em>Seluruh data pengiriman</em></div>
        </div>
        <div class="mstat">
            <div class="ico" style="background:#fff4dc;color:#f59e0b"><i class="fa-solid fa-truck-fast"></i></div>
            <div><small>Belum Terkirim</small><b>{{ number_format($pendingShipments) }}</b><em>Masih dalam proses</em></div>
        </div>
        <div class="mstat">
            <div class="ico" style="background:#e8f7ef;color:#16a34a"><i class="fa-solid fa-circle-check"></i></div>
            <div><small>Berhasil Terkirim</small><b>{{ number_format($deliveredShipments) }}</b><em>Status DELIVERED</em></div>
        </div>
        <div class="mstat">
            <div class="ico" style="background:#f0eaff;color:#7c3aed"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div><small>Total Pembaruan</small><b>{{ number_format($totalUpdates) }}</b><em>Riwayat tracking tersimpan</em></div>
        </div>
    </div>

    {{-- DAFTAR PENGIRIMAN --}}
    <section class="panel sect">
        <div class="ph"><div><h2>Daftar Pengiriman</h2><p>Cari dan pilih pengiriman yang ingin diperbarui.</p></div></div>

        <form action="{{ route('operator.pembaruan-status.index') }}" method="GET" class="filters">
            <input name="search" value="{{ request('search') }}" placeholder="Cari nomor resi, customer, kurir, atau status..." aria-label="Pencarian">
            <select name="status" aria-label="Status">
                <option value="">Semua Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $statusLabels[$status] ?? $status }}</option>
                @endforeach
            </select>
            <button class="btn" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
            <a href="{{ route('operator.pembaruan-status.index') }}" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nomor Resi</th>
                        <th>Customer</th>
                        <th>Perusahaan</th>
                        <th>Rute</th>
                        <th>Status Saat Ini</th>
                        <th>Estimasi Tiba</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $shipment)
                        <tr>
                            <td class="num">{{ $shipment->shipment_number }}</td>
                            <td>{{ $shipment->customer_name }}</td>
                            <td>{{ $shipment->carrier }}</td>
                            <td><div class="route">{{ $shipment->origin }} <i class="fa-solid fa-arrow-right"></i> {{ $shipment->destination }}</div></td>
                            <td><span class="status {{ $statusClass($shipment->status) }}">{{ $statusLabels[$shipment->status] ?? $shipment->status }}</span></td>
                            <td>{{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <a class="btn btn-detail" href="{{ route('operator.pembaruan-status.index', ['id' => $shipment->id]) }}">
                                    <i class="fa-solid fa-pen-to-square"></i> Pilih
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty">Belum ada data pengiriman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            <span>Menampilkan {{ $shipments->firstItem() ?? 0 }}–{{ $shipments->lastItem() ?? 0 }} dari {{ $shipments->total() }} pengiriman</span>
            @if($shipments->hasPages())<div>{{ $shipments->links() }}</div>@endif
        </div>
    </section>

    {{-- DETAIL DAN FORM PEMBARUAN --}}
    @if($selected)

        <div class="row r-c">

            {{-- DETAIL PENGIRIMAN --}}
            <section class="panel">
                <div class="ph"><div><h2>Detail Pengiriman</h2><p>Informasi pengiriman yang dipilih.</p></div></div>

                <div class="resi">
                    <small>Nomor Resi</small>
                    <b>{{ $selected->shipment_number }}</b>
                </div>

                <div class="d-grid">
                    <div class="d-item"><label>Nama Customer</label><strong>{{ $selected->customer_name }}</strong></div>
                    <div class="d-item"><label>Nama Pengiriman</label><strong>{{ $selected->shipment_name }}</strong></div>
                    <div class="d-item"><label>Perusahaan Pengiriman</label><strong>{{ $selected->carrier }}</strong></div>
                    <div class="d-item"><label>Jenis Transportasi</label><strong>{{ $selected->transport_type }}</strong></div>
                    <div class="d-item"><label>Asal Pengiriman</label><strong>{{ $selected->origin }}</strong></div>
                    <div class="d-item"><label>Tujuan Pengiriman</label><strong>{{ $selected->destination }}</strong></div>
                    <div class="d-item">
                        <label>Estimasi Keberangkatan</label>
                        <strong>{{ $selected->estimated_departure ? $selected->estimated_departure->format('d M Y H:i') : '-' }}</strong>
                    </div>
                    <div class="d-item">
                        <label>Estimasi Kedatangan</label>
                        <strong>{{ $selected->estimated_arrival ? $selected->estimated_arrival->format('d M Y H:i') : '-' }}</strong>
                    </div>
                    <div class="d-item"><label>Berat Barang</label><strong>{{ $selected->weight }} kg</strong></div>
                </div>

                <div class="now-box">
                    <small>Status Sekarang</small>
                    <strong style="color: {{ $statusColors[$selected->status] ?? '#2876c5' }}">
                        {{ $statusLabels[$selected->status] ?? $selected->status }}
                    </strong>
                </div>

                <h4 class="op-sub">Tahapan Pengiriman</h4>
                <div class="steps">
                    @foreach($statusOrder as $index => $status)
                        @php
                            $currentPosition = array_search($selected->status, $statusOrder);
                            $completed = $index <= $currentPosition;
                        @endphp
                        <div class="step {{ $completed ? 'done' : '' }}">
                            <div class="c">
                                @if($completed)
                                    <i class="fa-solid fa-check"></i>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <span>{{ $statusLabels[$status] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- FORM PEMBARUAN --}}
            <section class="panel">
                <div class="ph"><div><h2>Form Pembaruan Status</h2><p>Masukkan informasi status terbaru pengiriman.</p></div></div>

                @if($selected->status === 'DELIVERED')

                    <div class="done-box">
                        <i class="fa-solid fa-circle-check"></i>
                        <h3>Pengiriman Telah Selesai</h3>
                        <p>Pengiriman ini sudah berstatus DELIVERED dan tidak dapat diperbarui lagi.</p>
                    </div>

                @else

                    <form action="{{ route('operator.pengiriman.update-status', $selected->id) }}" method="POST" class="op-form">
                        @csrf
                        @method('PUT')

                        <div class="op-field">
                            <label>Nomor Resi</label>
                            <input type="text" value="{{ $selected->shipment_number }}" readonly>
                        </div>

                        <div class="op-field">
                            <label>Status Saat Ini</label>
                            <input type="text" value="{{ $statusLabels[$selected->status] ?? $selected->status }}" readonly>
                        </div>

                        <div class="op-field">
                            <label>Status Baru <i>*</i></label>
                            <select name="status" required>
                                <option value="">Pilih Status Berikutnya</option>
                                @if($nextStatus)
                                    <option value="{{ $nextStatus }}" @selected(old('status') === $nextStatus)>
                                        {{ $statusLabels[$nextStatus] }} ({{ $nextStatus }})
                                    </option>
                                @endif
                            </select>
                            <small>Status hanya dapat dilanjutkan ke tahapan berikutnya.</small>
                        </div>

                        <div class="op-field">
                            <label>Waktu Kejadian <i>*</i></label>
                            <input type="datetime-local" name="event_time" value="{{ old('event_time', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="op-field">
                            <label>Lokasi Terbaru <i>*</i></label>
                            <input type="text" name="location" value="{{ old('location') }}" placeholder="Contoh: Medan, Sumatera Utara" required>
                        </div>

                        <div class="two">
                            <div class="op-field">
                                <label>Latitude</label>
                                <input type="number" name="latitude" step="any" min="-90" max="90" value="{{ old('latitude') }}" placeholder="Contoh: 3.5952">
                            </div>
                            <div class="op-field">
                                <label>Longitude</label>
                                <input type="number" name="longitude" step="any" min="-180" max="180" value="{{ old('longitude') }}" placeholder="Contoh: 98.6722">
                            </div>
                        </div>

                        <div class="op-field">
                            <label>Deskripsi Pembaruan</label>
                            <textarea name="description" rows="4" placeholder="Masukkan keterangan perjalanan pengiriman...">{{ old('description') }}</textarea>
                        </div>

                        <div class="op-field">
                            <label>Nama Operator</label>
                            <input type="text" value="{{ auth()->user()->name }}" readonly>
                        </div>

                        <button type="submit" class="btn btn-block"
                                onclick="return confirm('Apakah kamu yakin ingin memperbarui status pengiriman ini?')">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Pembaruan Status
                        </button>
                    </form>

                @endif
            </section>

        </div>

    @else

        <section class="panel sect">
            <div class="empty-box">
                <i class="fa-solid fa-box-open"></i>
                <h3>Belum Ada Pengiriman Dipilih</h3>
                <p>Pilih salah satu pengiriman dari tabel untuk melihat detail dan memperbarui status.</p>
            </div>
        </section>

    @endif

    {{-- RIWAYAT PEMBARUAN --}}
    <section class="panel sect">
        <div class="ph"><div><h2>Riwayat Pembaruan Status</h2><p>Daftar seluruh aktivitas perubahan status pengiriman.</p></div></div>

        <form action="{{ route('operator.pembaruan-status.index') }}" method="GET" class="filters">
            @if(request('id'))
                <input type="hidden" name="id" value="{{ request('id') }}">
            @endif

            <input name="history_search" value="{{ request('history_search') }}" placeholder="Cari nomor resi, customer, lokasi...">

            <select name="history_status">
                <option value="">Semua Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('history_status') === $status)>{{ $statusLabels[$status] }}</option>
                @endforeach
            </select>

            <input name="operator" value="{{ request('operator') }}" placeholder="Nama operator">
            <input name="location" value="{{ request('location') }}" placeholder="Lokasi">
            <input type="date" name="date_from" value="{{ request('date_from') }}" title="Tanggal awal">
            <input type="date" name="date_to" value="{{ request('date_to') }}" title="Tanggal akhir">

            <button class="btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a href="{{ route('operator.pembaruan-status.index', ['id' => request('id')]) }}" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nomor Resi</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Lokasi</th>
                        <th>Deskripsi</th>
                        <th>Operator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $event)
                        <tr>
                            <td class="num">{{ $event->shipment?->shipment_number ?? '-' }}</td>
                            <td><span class="status {{ $statusClass($event->status) }}">{{ $statusLabels[$event->status] ?? $event->status }}</span></td>
                            <td>{{ $event->event_time ? $event->event_time->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $event->location }}</td>
                            <td>{{ $event->description ?: '-' }}</td>
                            <td>{{ $event->updated_by ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Belum ada riwayat pembaruan status.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            <span>Menampilkan {{ $history->firstItem() ?? 0 }}–{{ $history->lastItem() ?? 0 }} dari {{ $history->total() }} riwayat</span>
            @if($history->hasPages())<div>{{ $history->links() }}</div>@endif
        </div>
    </section>

</section>
</main>

</body>
</html>
