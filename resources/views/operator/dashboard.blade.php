<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard Operator - CargoTrace</title>
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

/* HERO + STAT */
.hero{position:relative;overflow:hidden;min-height:260px;padding:38px 42px 104px;border-radius:30px;color:#fff;display:flex;justify-content:space-between;gap:24px;
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 50%,#1d6fe870 100%),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1800&q=80') center/cover;box-shadow:0 26px 54px #0b2a6b46}
.hero::after{content:"";position:absolute;width:420px;height:420px;right:-120px;top:-200px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.hero h2{font:800 31px/1.25 var(--h);letter-spacing:-.9px;max-width:540px}
.hero p{margin-top:12px;max-width:470px;line-height:1.8;color:#cfe0ff}
.live{display:inline-flex;align-items:center;gap:8px;margin-bottom:16px;padding:7px 13px;border-radius:20px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11px;font-weight:600}
.live i{width:8px;height:8px;border-radius:50%;background:#4ade80;animation:ping 1.8s infinite}
@keyframes ping{0%{box-shadow:0 0 0 0 #4ade80aa}70%{box-shadow:0 0 0 9px #4ade8000}100%{box-shadow:0 0 0 0 #4ade8000}}
.hero-side{display:flex;flex-direction:column;gap:10px;align-self:flex-start}
.hchip{display:flex;align-items:center;gap:12px;min-width:210px;padding:13px 17px;background:#ffffff1c;border:1px solid #ffffff35;border-radius:16px;backdrop-filter:blur(10px);font-size:11px;color:#dbe9ff}
.hchip b{font:800 22px var(--h);color:#fff;min-width:34px}
.stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin:-70px 22px 24px;position:relative;z-index:3}
.stat{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:18px;background:#ffffffee;backdrop-filter:blur(10px);border:1px solid #fff;border-radius:22px;box-shadow:0 16px 36px #0b2a6b22;transition:.25s}
.stat:hover{transform:translateY(-5px)}
.stat small{display:block;font-size:11.5px;color:var(--mut);font-weight:600}
.stat b{display:block;margin-top:6px;font:800 28px var(--h);color:var(--navy);line-height:1}
.stat em{display:block;margin-top:7px;font-style:normal;font-size:10.5px;font-weight:700}
.ring{position:relative;flex:none;width:58px;height:58px}
.ring svg{width:58px;height:58px;transform:rotate(-90deg)}
.ring circle{fill:none;stroke-width:6;stroke-linecap:round}
.ring .rb{stroke:#e6eefc}
.ring i{position:absolute;inset:0;display:grid;place-items:center;font-size:15px}

/* PANEL & GRID */
.panel{padding:24px;background:#fff;border:1px solid var(--line);border-radius:26px;box-shadow:0 10px 30px #2563eb0d}
.ph{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px}
.ph h2{font:700 17px var(--h);color:var(--navy);letter-spacing:-.3px}
.ph p{margin-top:6px;font-size:12px;color:var(--mut)}
.row{display:grid;gap:20px;margin-bottom:24px}
.r-a{grid-template-columns:1.7fr 1fr}
.r-c{grid-template-columns:minmax(0,1.5fr) minmax(0,1fr);align-items:start}
.sect{margin-bottom:24px}
.chart-box{position:relative;height:250px}
.empty-box{display:grid;place-items:center;height:250px;color:#8ca6c4;text-align:center}

/* TABEL */
.filters{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}
.filters input,.filters select{padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:var(--ink);font:12px 'Plus Jakarta Sans',sans-serif}
.filters input{flex:1;min-width:200px}
.filters input:focus,.filters select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:10px;cursor:pointer;font:700 11px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;transition:.2s}
.btn:hover{filter:brightness(1.1)}.btn-light{background:#e8f1ff;color:#1d4ed8}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:820px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:11px;font-weight:700;padding:13px 14px}
th:first-child{border-radius:12px 0 0 12px}th:last-child{border-radius:0 12px 12px 0}
td{padding:14px;border-bottom:1px solid #edf3fa;color:#41608a;white-space:nowrap}
tbody tr:hover{background:#f6faff}
.route{display:flex;align-items:center;gap:8px}.route i{color:#9db6dc;font-size:10px}
.status{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10px;font-weight:700;background:#e5f1ff;color:#2876c5}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.s-booked{background:#f1edff;color:#7552ae}.s-picked{background:#f0eaff;color:#7c3aed}.s-transit{background:#fff4dc;color:#b77900}.s-delivered{background:#e8f7ef;color:#26734d}.s-delayed{background:#ffe9e9;color:#c92f3a}
.late-tag{margin-left:6px;padding:3px 8px;border-radius:12px;background:#ffe9e9;color:#c92f3a;font-size:9px;font-weight:700}
td.empty{text-align:center;padding:32px;color:#8ca6c4}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top:16px;font-size:11px;color:#8ca6c4}
.pager nav svg{width:16px;height:16px}

/* DELAY + AKTIVITAS */
.dup{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px}
.dup div{padding:14px;border-radius:16px}
.dup .a{background:linear-gradient(135deg,#fff0f0,#ffe3e3);color:#c92f3a}.dup .b{background:linear-gradient(135deg,#e8f1ff,#d7e8ff);color:#1d4ed8}
.dup b{display:block;font:800 26px var(--h);line-height:1}.dup small{font-size:11px;font-weight:600}
.d-item{display:flex;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid #edf3fa}
.d-item:last-child{border:0;padding-bottom:0}
.d-item strong{display:block;font-size:12.5px;color:var(--navy)}.d-item small{display:block;margin-top:3px;font-size:11px;color:#8ca6c4;line-height:1.5}
.dur{flex:none;align-self:flex-start;padding:5px 10px;border-radius:20px;background:#ffe9e9;color:#c92f3a;font-size:10px;font-weight:700}
.safe{padding:22px 10px;text-align:center;color:#6c83ab}.safe i{display:grid;place-items:center;width:52px;height:52px;margin:0 auto 10px;border-radius:50%;background:#e5f7ed;color:#16a34a;font-size:22px}
.timeline{display:flex;flex-direction:column}
.t-item{display:flex;gap:14px;padding:14px 0;border-bottom:1px solid #edf3fa}
.t-item:last-child{border:0;padding-bottom:0}
.t-dot{flex:none;display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:#e5f1ff;color:#2876c5;font-size:13px}
.t-body{flex:1;min-width:0}
.t-body strong{display:block;font-size:12.5px;color:var(--navy)}
.t-body p{margin-top:4px;font-size:11.5px;color:#6c83ab;line-height:1.6}
.t-body small{display:block;margin-top:3px;font-size:10.5px;color:#8ca6c4}
.t-time{font-size:10.5px;color:#8ca6c4;white-space:nowrap}
.empty-state{padding:30px;text-align:center;color:#8ca6c4}

/* RESPONSIVE */
@media(max-width:1350px){.stats{grid-template-columns:repeat(3,minmax(0,1fr))}.hero-side{display:none}}
@media(max-width:1100px){.r-a,.r-c{grid-template-columns:1fr}.content{padding:20px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .topline{flex-direction:column;align-items:flex-start}.hero{padding:26px 22px 96px}.hero h2{font-size:22px}
 .stats{grid-template-columns:1fr 1fr;margin:-70px 8px 20px}.panel{padding:18px}
}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
.alert-error{background:#ffe9e9;color:#c92f3a}
.alert i{margin-right:6px}
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
    @php
    use Carbon\Carbon;

    $now = now();

    // Ambil route pertama yang tersedia, kalau tidak ada pakai '#'
    $rt = function (array $names) {
        foreach ($names as $n) {
            if (Route::has($n)) return route($n);
        }
        return '#';
    };

    $statusClass = fn ($s) => match (strtoupper((string) $s)) {
        'BOOKED'     => 's-booked',
        'PICKED_UP'  => 's-picked',
        'IN_TRANSIT' => 's-transit',
        'DELIVERED'  => 's-delivered',
        'DELAYED'    => 's-delayed',
        default      => '',
    };

    $isLate = fn ($s) => $s->estimated_arrival
        && Carbon::parse($s->estimated_arrival)->lt($now)
        && strtoupper((string) $s->status) !== 'DELIVERED';

    $fmtDelay = function ($s) use ($now) {
        if (!$s->estimated_arrival) return 'Terlambat';
        $eta = Carbon::parse($s->estimated_arrival);
        if ($eta->isFuture()) return 'Terlambat';
        $h = (int) $eta->diffInHours($now, true);
        return $h >= 24 ? floor($h / 24) . ' hari ' . ($h % 24) . ' jam' : max($h, 1) . ' jam';
    };

    $pct = fn ($v) => $totalShipments > 0 ? min(100, round($v / $totalShipments * 100)) : 0;

    // [label, nilai, persen ring, warna, ikon, keterangan]
    $cards = [
        ['Total Pengiriman', $totalShipments, 100, '#2563eb', 'fa-boxes-stacked', 'Semua pengiriman'],
        ['Pengiriman Baru', $createdShipments, $pct($createdShipments), '#38bdf8', 'fa-file-circle-plus', 'Status CREATED'],
        ['Dalam Perjalanan', $inTransitShipments, $pct($inTransitShipments), '#f59e0b', 'fa-truck-fast', $pct($inTransitShipments) . '% di jalan'],
        ['Terkirim', $deliveredShipments, $pct($deliveredShipments), '#16a34a', 'fa-circle-check', $pct($deliveredShipments) . '% selesai'],
        ['Terlambat', $delayedShipments, $pct($delayedShipments), '#e5484d', 'fa-clock', 'Delay rate ' . $delayRate . '%'],
    ];
@endphp
<div class="topline">
        <div>
            <h1>Dashboard Operator</h1>
            <p>Selamat datang, {{ auth()->user()->name ?? 'Operator' }}! Berikut ringkasan operasional hari ini.</p>
        </div>
        <div class="date-box"><i class="fa-regular fa-calendar"></i> {{ $now->format('d M Y') }}</div>
    </div>

    @if(session('success'))
        <div class="alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <!-- HERO -->
    <section class="hero">
        <div>
            <span class="live"><i></i> Pemantauan langsung aktif</span>
            <h2>Kelola pengiriman dan pantau status barang dengan mudah.</h2>
            <p>Periksa pengiriman baru, pantau yang sedang dalam perjalanan, dan tangani keterlambatan dalam satu layar.</p>
        </div>
        <div class="hero-side">
            <div class="hchip"><b>{{ $needActionShipments }}</b>perlu diproses</div>
            <div class="hchip"><b>{{ $delayedShipments }}</b>perlu perhatian</div>
        </div>
    </section>

    <!-- STATISTIK -->
    <section class="stats">
        @foreach($cards as $c)
            <div class="stat">
                <div>
                    <small>{{ $c[0] }}</small>
                    <b class="cnt" data-v="{{ (int) $c[1] }}">{{ number_format($c[1]) }}</b>
                    <em style="color:{{ $c[3] }}">{{ $c[5] }}</em>
                </div>
                <div class="ring">
                    <svg viewBox="0 0 54 54">
                        <circle class="rb" cx="27" cy="27" r="22"/>
                        <circle cx="27" cy="27" r="22" stroke="{{ $c[3] }}" stroke-dasharray="{{ $c[2] * 1.382 }} 138.2"/>
                    </svg>
                    <i class="fa-solid {{ $c[4] }}" style="color:{{ $c[3] }}"></i>
                </div>
            </div>
        @endforeach
    </section>

    <!-- GRAFIK -->
    <section class="row r-a">
        <div class="panel">
            <div class="ph"><div><h2>Tren Pengiriman Baru</h2><p>Jumlah pengiriman dibuat dalam 14 hari terakhir.</p></div></div>
            @if($totalShipments > 0)
                <div class="chart-box"><canvas id="chartTrend"></canvas></div>
            @else
                <div class="empty-box">Belum ada data.</div>
            @endif
        </div>
        <div class="panel">
            <div class="ph"><div><h2>Status Pengiriman</h2><p>Sebaran seluruh pengiriman.</p></div></div>
            @if($byStatus->count())
                <div class="chart-box"><canvas id="chartStatus"></canvas></div>
            @else
                <div class="empty-box">Belum ada data.</div>
            @endif
        </div>
    </section>

    <!-- TABEL PENGIRIMAN -->
    <section class="panel sect" id="daftar">
        <div class="ph">
            <div><h2>Pengiriman Terbaru</h2><p>Cari berdasarkan nomor, nama, customer, atau rute.</p></div>
            <a href="{{ $rt(['operator.pengiriman.index']) }}" class="btn btn-light">Lihat semua</a>
        </div>

        <form method="GET" action="{{ url()->current() }}#daftar" class="filters">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nomor, customer, asal, tujuan..." aria-label="Pencarian">
            <select name="status" aria-label="Status">
                <option value="">Semua status</option>
                @foreach($statuses as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ str_replace('_', ' ', $st) }}</option>
                @endforeach
            </select>
            <select name="delay" aria-label="Keterlambatan">
                <option value="">Semua</option>
                <option value="late" @selected(request('delay') === 'late')>Terlambat</option>
            </select>
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a href="{{ url()->current() }}#daftar" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Pengiriman</th>
                        <th>Nama Pengiriman</th>
                        <th>Customer</th>
                        <th>Rute</th>
                        <th>Status</th>
                        <th>ETA</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentShipments as $s)
                    <tr>
                        <td><strong>{{ $s->shipment_number }}</strong></td>
                        <td>{{ $s->shipment_name }}</td>
                        <td>{{ $s->customer_name }}</td>
                        <td>
                            <div class="route">{{ $s->origin }} <i class="fa-solid fa-arrow-right"></i> {{ $s->destination }}</div>
                        </td>
                        <td>
                            <span class="status {{ $statusClass($s->status) }}">{{ str_replace('_', ' ', $s->status) }}</span>
                            @if($isLate($s))<span class="late-tag">LATE</span>@endif
                        </td>
                        <td>{{ $s->estimated_arrival ? Carbon::parse($s->estimated_arrival)->format('d M Y') : '-' }}</td>
                        <td>
                            <a class="btn btn-light" href="{{ url('/pengiriman/' . $s->id) }}">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada data pengiriman yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            <span>Menampilkan {{ $recentShipments->firstItem() ?? 0 }}–{{ $recentShipments->lastItem() ?? 0 }} dari {{ $recentShipments->total() }} pengiriman</span>
            @if($recentShipments->hasPages())
                <div>{{ $recentShipments->fragment('daftar')->links() }}</div>
            @endif
        </div>
    </section>

    <!-- KETERLAMBATAN + AKTIVITAS -->
    <section class="row r-c">
        <div class="panel">
            <div class="ph"><div><h2>Aktivitas Pelacakan Terbaru</h2><p>Pembaruan status dan lokasi pengiriman.</p></div></div>
            <div class="timeline">
                @forelse($recentActivities as $a)
                    <div class="t-item">
                        <div class="t-dot"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="t-body">
                            <strong>{{ $a->shipment->shipment_number ?? 'Pengiriman' }} — {{ str_replace('_', ' ', $a->status) }}</strong>
                            <p>{{ $a->description ?: 'Pembaruan status pengiriman' }}</p>
                            <small><i class="fa-solid fa-map-pin"></i> {{ $a->location ?: '-' }}</small>
                        </div>
                        <div class="t-time">{{ $a->event_time ? Carbon::parse($a->event_time)->format('d/m/Y H:i') : '' }}</div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada aktivitas pelacakan.</div>
                @endforelse
            </div>
        </div>

        <div class="panel">
            <div class="ph"><div><h2>Perlu Perhatian</h2><p>Pengiriman yang melewati ETA.</p></div></div>
            <div class="dup">
                <div class="a"><b>{{ $delayedShipments }}</b><small>Terlambat</small></div>
                <div class="b"><b>{{ $needActionShipments }}</b><small>Perlu diproses</small></div>
            </div>

            @forelse($delayedList as $s)
                <div class="d-item">
                    <div>
                        <strong>{{ $s->shipment_number }}</strong>
                        <small>{{ $s->customer_name }} · {{ $s->origin }} → {{ $s->destination }}</small>
                        <small>ETA {{ Carbon::parse($s->estimated_arrival)->format('d M Y H:i') }}</small>
                    </div>
                    <span class="dur">{{ $fmtDelay($s) }}</span>
                </div>
            @empty
                <div class="safe"><i class="fa-solid fa-shield-check"></i>Semua aman.<br>Tidak ada keterlambatan.</div>
            @endforelse
        </div>
    </section>
</section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const $ = id => document.getElementById(id);

/* Animasi angka */
document.querySelectorAll('.cnt').forEach(el => {
    const v = +el.dataset.v; if (!v) return;
    const t0 = performance.now();
    (function tick(t) {
        const k = Math.min(1, (t - t0) / 900);
        el.textContent = Math.round(v * (1 - Math.pow(1 - k, 3))).toLocaleString('id-ID');
        if (k < 1) requestAnimationFrame(tick);
    })(t0);
});

/* Grafik */
try {
    const byStatus = @json($byStatus), trend = @json($trend);
    const total = {{ (int) $totalShipments }};
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#6c83ab';
    const sc = { CREATED:'#38bdf8', BOOKED:'#8b7be8', PICKED_UP:'#7c3aed', IN_TRANSIT:'#f59e0b', DELIVERED:'#16a34a', DELAYED:'#e5484d', CANCELLED:'#94a3b8' };

    if ($('chartTrend')) new Chart($('chartTrend'), {
        type: 'line',
        data: {
            labels: trend.map(t => t.label),
            datasets: [{
                data: trend.map(t => t.value), borderColor:'#2563eb', borderWidth:3, tension:.42, fill:true,
                pointRadius:0, pointHoverRadius:6, pointHoverBackgroundColor:'#fff', pointHoverBorderColor:'#2563eb', pointHoverBorderWidth:3,
                backgroundColor: c => {
                    const a = c.chart.chartArea; if (!a) return '#2563eb22';
                    const g = c.chart.ctx.createLinearGradient(0, a.top, 0, a.bottom);
                    g.addColorStop(0, '#2563eb55'); g.addColorStop(1, '#2563eb00'); return g;
                }
            }]
        },
        options: {
            maintainAspectRatio:false, interaction:{ mode:'index', intersect:false },
            plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label:c => c.parsed.y + ' pengiriman' } } },
            scales:{
                y:{ beginAtZero:true, ticks:{ precision:0 }, grid:{ color:'#edf3fa' }, border:{ display:false } },
                x:{ grid:{ display:false }, ticks:{ maxTicksLimit:7 }, border:{ display:false } }
            }
        }
    });

    if ($('chartStatus')) {
        const k = Object.keys(byStatus);
        new Chart($('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: k.map(x => x.replace('_', ' ')),
                datasets: [{ data:k.map(x => byStatus[x]), backgroundColor:k.map(x => sc[x] || '#60a5fa'), borderWidth:4, borderColor:'#fff', borderRadius:10, spacing:2 }]
            },
            options: { maintainAspectRatio:false, cutout:'72%', plugins:{ legend:{ position:'bottom', labels:{ boxWidth:9, usePointStyle:true, padding:14 } } } },
            plugins: [{
                id:'ct',
                afterDraw(c) {
                    const { ctx, chartArea:a } = c, x = (a.left + a.right) / 2, y = (a.top + a.bottom) / 2;
                    ctx.save(); ctx.textAlign = 'center';
                    ctx.fillStyle = '#061533'; ctx.font = '800 30px Sora, sans-serif'; ctx.fillText(String(total), x, y);
                    ctx.fillStyle = '#8ca6c4'; ctx.font = '600 11px "Plus Jakarta Sans", sans-serif'; ctx.fillText('Total', x, y + 20);
                    ctx.restore();
                }
            }]
        });
    }
} catch (e) { console.error('Grafik gagal dimuat:', e); }
</script>
</body>
</html>
