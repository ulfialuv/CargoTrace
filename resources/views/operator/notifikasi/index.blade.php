<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Notifikasi Operator - CargoTrace</title>
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
.alert{padding:13px 17px;margin-bottom:16px;border-radius:12px;background:#e5f7ed;color:#187344;display:flex;gap:10px}

/* HERO FOTO */
.hero{position:relative;overflow:hidden;min-height:200px;padding:32px 40px;margin-bottom:22px;border-radius:28px;color:#fff;display:flex;justify-content:space-between;align-items:center;gap:24px;
 background:linear-gradient(100deg,#061533f2 0%,#0b2a6bd6 50%,#1d6fe86e 100%),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1800&q=80') center/cover,linear-gradient(135deg,#061533,#1d4ed8);box-shadow:0 24px 50px #0b2a6b40}
.hero::after{content:"";position:absolute;width:380px;height:380px;right:-110px;top:-190px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.hero h2{font:800 26px/1.3 var(--h);letter-spacing:-.8px;max-width:520px}
.hero p{margin-top:10px;max-width:480px;line-height:1.8;color:#cfe0ff}
.live{display:inline-flex;align-items:center;gap:8px;margin-bottom:14px;padding:7px 13px;border-radius:20px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11px;font-weight:600}
.live i{width:8px;height:8px;border-radius:50%;background:#f87171;animation:ping 1.8s infinite}
@keyframes ping{0%{box-shadow:0 0 0 0 #f87171aa}70%{box-shadow:0 0 0 9px #f8717100}100%{box-shadow:0 0 0 0 #f8717100}}
.hchip{display:flex;align-items:center;gap:14px;min-width:200px;padding:16px 22px;background:#ffffff1c;border:1px solid #ffffff35;border-radius:18px;backdrop-filter:blur(10px);font-size:11px;color:#dbe9ff}
.hchip b{font:800 32px var(--h);color:#fff;line-height:1}

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
.sect{margin-bottom:24px}
.ph{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px}
.ph h2{font:700 17px var(--h);color:var(--navy);letter-spacing:-.3px}
.ph p{margin-top:6px;font-size:12px;color:var(--mut)}
.count{padding:7px 13px;border-radius:20px;background:#e8f1ff;color:#1d4ed8;font-size:11px;font-weight:700;white-space:nowrap}

/* FILTER */
.filters{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.filters input,.filters select{padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:var(--ink);font:12px 'Plus Jakarta Sans',sans-serif}
.filters input.grow{flex:1;min-width:200px}
.filters input:focus,.filters select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.date-filter{width:100%;display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;padding-top:12px;margin-top:2px;border-top:1px solid #edf3fa}
.date-filter label{display:block;font-size:11px;font-weight:700;color:#5d7aa5;margin-bottom:6px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:10px;cursor:pointer;font:700 11px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;transition:.2s}
.btn:hover{filter:brightness(1.1)}
.btn-light{background:#e8f1ff;color:#1d4ed8}

/* DAFTAR NOTIFIKASI */
.n-list{display:flex;flex-direction:column;gap:12px;margin-top:20px}
.n-item{display:flex;gap:16px;padding:18px;border-radius:20px;border:1px solid #e8eefb;background:#fff;transition:.2s}
.n-item:hover{box-shadow:0 10px 26px #2563eb14;transform:translateY(-2px)}
.n-item.unread{background:linear-gradient(90deg,#f1f6ff,#fff);border-color:#cfe0fb;box-shadow:inset 4px 0 0 var(--blue)}
.n-icon{display:grid;place-items:center;flex:none;width:46px;height:46px;border-radius:14px;background:#e8f1ff;color:var(--blue);font-size:18px}
.n-icon.urgent,.n-icon.new_exception,.n-icon.high_severity_exception{background:#ffe9e9;color:#c92f3a}
.n-icon.shipment_delayed{background:#fff4dc;color:#b77900}
.n-icon.exception_resolved{background:#e8f7ef;color:#26734d}
.n-body{flex:1;min-width:0}
.n-top{display:flex;justify-content:space-between;align-items:flex-start;gap:14px}
.n-title{display:flex;align-items:center;gap:9px}
.n-title h4{font:700 14px var(--h);color:var(--navy);letter-spacing:-.2px}
.dot{width:8px;height:8px;border-radius:50%;background:var(--blue);box-shadow:0 0 0 4px #2563eb22}
.n-time{font-size:11px;color:#8ca6c4;white-space:nowrap}
.n-time i{margin-right:4px}
.n-msg{margin:8px 0 12px;color:var(--mut);line-height:1.7;font-size:12.5px}
.n-meta{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
.n-meta>span{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:20px;font-size:10px;font-weight:700}
.t-label{background:#e8f1ff;color:#1d4ed8}
.s-label{background:#f1f3f8;color:#52627a}
.sev-low{background:#e8f7ef;color:#26734d}.sev-medium{background:#fff4dc;color:#b77900}.sev-high{background:#ffe9e9;color:#c92f3a}
.r-unread{background:#fff4dc;color:#b77900}.r-done{background:#e8f7ef;color:#26734d}
.n-actions{display:flex;align-items:center;flex-wrap:wrap;gap:16px;margin-top:14px}
.n-actions form{margin:0}
.a-read,.a-del{display:inline-flex;align-items:center;gap:6px;border:0;background:transparent;text-decoration:none;font:700 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer;padding:0}
.a-read{color:var(--blue)}.a-del{color:#c92f3a}
.a-read:hover,.a-del:hover{text-decoration:underline}
.empty{text-align:center;padding:46px 16px;color:var(--mut)}
.empty .ei{display:grid;place-items:center;width:66px;height:66px;margin:0 auto 14px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:26px}
.empty h3{font:700 16px var(--h);color:var(--navy);margin-bottom:8px}
.empty p{max-width:430px;margin:0 auto;line-height:1.7}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top:18px;font-size:11px;color:#8ca6c4;flex-wrap:wrap}
.pager nav svg{width:16px;height:16px}

/* KATEGORI BERFOTO */
.cat-layout{display:grid;grid-template-columns:300px minmax(0,1fr);gap:24px;align-items:stretch}
.photo-card{position:relative;overflow:hidden;min-height:280px;border-radius:22px;color:#fff;padding:24px;display:flex;flex-direction:column;justify-content:flex-end;
 background:linear-gradient(180deg,#06153300 10%,#061533f0 92%),url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=75') center/cover,linear-gradient(135deg,#0a2257,#1d4ed8);box-shadow:0 18px 36px #0b2a6b30}
.photo-card h3{font:800 18px/1.35 var(--h);margin-bottom:8px}
.photo-card p{font-size:11.5px;line-height:1.7;color:#d6e4ff}
.type-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;align-content:start}
.type-item{display:flex;align-items:center;gap:12px;padding:14px;border-radius:16px;background:#f4f8ff;border:1px solid #e3ecfb}
.type-icon{display:grid;place-items:center;flex:none;width:38px;height:38px;border-radius:12px;background:#e0ecff;color:var(--blue);font-size:15px}
.type-item span{font-size:12px;font-weight:700;color:#41608a}

/* RESPONSIVE */
@media(max-width:1200px){.cat-layout{grid-template-columns:1fr}.photo-card{min-height:200px}.hero .hchip{display:none}}
@media(max-width:1100px){.mstats{grid-template-columns:repeat(2,minmax(0,1fr))}.content{padding:20px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .topline{flex-direction:column;align-items:flex-start}.hero{padding:26px 22px}.hero h2{font-size:21px}.panel{padding:18px}
 .n-top{flex-direction:column;gap:6px}
}
@media(max-width:600px){.mstats,.type-grid{grid-template-columns:1fr}}
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

    $typeLabels = [
        'new_shipment' => 'New Shipment',
        'shipment_status_update' => 'Shipment Status Update',
        'tracking_update' => 'Tracking Update',
        'shipment_delayed' => 'Shipment Delayed',
        'new_exception' => 'New Exception',
        'high_severity_exception' => 'High Severity Exception',
        'exception_update' => 'Exception Update',
        'exception_resolved' => 'Exception Resolved',
    ];

    $typeIcons = [
        'new_shipment' => 'fa-box',
        'shipment_status_update' => 'fa-truck-fast',
        'tracking_update' => 'fa-location-dot',
        'shipment_delayed' => 'fa-clock',
        'new_exception' => 'fa-triangle-exclamation',
        'high_severity_exception' => 'fa-circle-exclamation',
        'exception_update' => 'fa-clipboard-check',
        'exception_resolved' => 'fa-circle-check',
    ];

    $getType = function ($data) {
        return $data['notification_type'] ?? $data['type'] ?? 'other';
    };

    $getTitle = function ($data) use ($typeLabels, $getType) {
        return $data['title']
            ?? $typeLabels[$getType($data)]
            ?? 'Notifikasi CargoTrace';
    };

    $getMessage = function ($data) {
        return $data['message']
            ?? $data['description']
            ?? $data['body']
            ?? 'Terdapat pembaruan aktivitas operasional.';
    };
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
            <h1>Notifikasi Operator</h1>
            <p>Pusat informasi aktivitas pengiriman dan pemberitahuan yang membutuhkan perhatian operasional.</p>
        </div>

        @if(($stats['unread'] ?? 0) > 0)
            <form action="{{ route('operator.notifications.read-all') }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn"><i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca</button>
            </form>
        @endif
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
    @endif

    {{-- HERO FOTO --}}
    <div class="hero">
        <div>
            <div class="live"><i></i> Perhatian operasional</div>
            <h2>Periksa notifikasi penting agar pengiriman tetap lancar.</h2>
            <p>Utamakan keterlambatan dan pengecualian dengan tingkat keparahan tinggi supaya bisa segera ditangani.</p>
        </div>
        <div class="hchip"><b>{{ $stats['urgent'] ?? 0 }}</b> notifikasi<br>prioritas tinggi</div>
    </div>

    {{-- STATISTIK --}}
    <div class="mstats">
        <div class="mstat">
            <div class="ico" style="background:#e8f1ff;color:#2563eb"><i class="fa-solid fa-bell"></i></div>
            <div><small>Total Notifikasi</small><b>{{ $stats['total'] ?? 0 }}</b><em>Seluruh pemberitahuan</em></div>
        </div>
        <div class="mstat">
            <div class="ico" style="background:#fff4dc;color:#f59e0b"><i class="fa-solid fa-envelope"></i></div>
            <div><small>Belum Dibaca</small><b>{{ $stats['unread'] ?? 0 }}</b><em>Menunggu perhatian</em></div>
        </div>
        <div class="mstat">
            <div class="ico" style="background:#e8f7ef;color:#16a34a"><i class="fa-solid fa-envelope-open"></i></div>
            <div><small>Sudah Dibaca</small><b>{{ $stats['read'] ?? 0 }}</b><em>Telah diperiksa</em></div>
        </div>
        <div class="mstat">
            <div class="ico" style="background:#ffe9e9;color:#e5484d"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div><small>Prioritas Tinggi</small><b>{{ $stats['urgent'] ?? 0 }}</b><em>Mendesak, belum dibaca</em></div>
        </div>
    </div>

    {{-- DAFTAR NOTIFIKASI --}}
    <section class="panel sect">
        <div class="ph">
            <div><h2>Daftar Notifikasi</h2><p>Informasi terbaru mengenai aktivitas pengiriman CargoTrace.</p></div>
            <span class="count">{{ $notifications->total() }} Notifikasi</span>
        </div>

        <form action="{{ route('operator.notifications.index') }}" method="GET" class="filters">
            <input class="grow" type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, pesan, atau nomor resi...">

            <select name="type">
                <option value="">Semua Jenis</option>
                @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') == $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status">
                <option value="">Semua Status</option>
                <option value="unread" @selected(request('status') == 'unread')>Belum Dibaca</option>
                <option value="read" @selected(request('status') == 'read')>Sudah Dibaca</option>
            </select>

            <select name="severity">
                <option value="">Semua Keparahan</option>
                <option value="LOW" @selected(request('severity') == 'LOW')>Rendah</option>
                <option value="MEDIUM" @selected(request('severity') == 'MEDIUM')>Sedang</option>
                <option value="HIGH" @selected(request('severity') == 'HIGH')>Tinggi</option>
            </select>

            <button type="submit" class="btn"><i class="fa-solid fa-filter"></i> Filter</button>
            <a href="{{ route('operator.notifications.index') }}" class="btn btn-light">Reset</a>

            <div class="date-filter">
                <div><label>Dari Tanggal</label><input type="date" name="date_from" value="{{ request('date_from') }}"></div>
                <div><label>Sampai Tanggal</label><input type="date" name="date_to" value="{{ request('date_to') }}"></div>
                <button type="submit" class="btn btn-light"><i class="fa-regular fa-calendar"></i> Terapkan Tanggal</button>
            </div>
        </form>

        <div class="n-list">

            @forelse($notifications as $notification)

                @php
                    $data = $notification->data ?? [];
                    $type = $getType($data);
                    $title = $getTitle($data);
                    $message = $getMessage($data);

                    $shipmentNumber = $data['shipment_number']
                        ?? $data['tracking_number']
                        ?? $data['reference_number']
                        ?? null;

                    $severity = strtoupper($data['severity'] ?? '');
                    $isUnread = is_null($notification->read_at);

                    $icon = $typeIcons[$type] ?? 'fa-bell';

                    $isUrgent = $severity === 'HIGH'
                        || in_array(strtoupper($data['priority'] ?? ''), ['HIGH', 'URGENT']);

                    $typeLabel = $typeLabels[$type] ?? 'Informasi Operasional';
                @endphp

                <div class="n-item {{ $isUnread ? 'unread' : 'read' }}">

                    <div class="n-icon {{ $isUrgent ? 'urgent' : strtolower($type) }}">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>

                    <div class="n-body">

                        <div class="n-top">
                            <div class="n-title">
                                <h4>{{ $title }}</h4>
                                @if($isUnread)<span class="dot"></span>@endif
                            </div>
                            <span class="n-time"><i class="fa-regular fa-clock"></i>{{ $notification->created_at?->format('d M Y, H:i') }}</span>
                        </div>

                        <p class="n-msg">{{ $message }}</p>

                        <div class="n-meta">
                            <span class="t-label"><i class="fa-solid {{ $icon }}"></i> {{ $typeLabel }}</span>

                            @if($shipmentNumber)
                                <span class="s-label"><i class="fa-solid fa-box"></i> {{ $shipmentNumber }}</span>
                            @endif

                            @if($severity)
                                <span class="sev-{{ strtolower($severity) }}">{{ $severity }} PRIORITY</span>
                            @endif

                            @if($isUnread)
                                <span class="r-unread"><i class="fa-solid fa-envelope"></i> Belum Dibaca</span>
                            @else
                                <span class="r-done"><i class="fa-solid fa-envelope-open"></i> Sudah Dibaca</span>
                            @endif
                        </div>

                        <div class="n-actions">

                            @if($isUnread)
                                <form action="{{ route('operator.notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="a-read"><i class="fa-solid fa-check"></i> Baca / Buka Detail</button>
                                </form>
                            @else
                                @if(!empty($data['url']))
                                    <a href="{{ $data['url'] }}" class="a-read">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Detail
                                    </a>
                                @elseif(!empty($data['exception_id']))
                                    <a href="{{ route('operator.exceptions.show', $data['exception_id']) }}" class="a-read">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Pengecualian
                                    </a>
                                @endif
                            @endif

                            <form action="{{ route('operator.notifications.destroy', $notification->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="a-del"><i class="fa-solid fa-trash"></i> Hapus</button>
                            </form>

                        </div>

                    </div>
                </div>

            @empty

                <div class="empty">
                    <div class="ei"><i class="fa-regular fa-bell-slash"></i></div>
                    <h3>Belum Ada Notifikasi</h3>
                    <p>Belum ada pemberitahuan yang tersedia untuk akun Operator ini. Notifikasi akan muncul ketika terdapat aktivitas operasional.</p>
                </div>

            @endforelse

        </div>

        <div class="pager">
            <span>Menampilkan {{ $notifications->firstItem() ?? 0 }}–{{ $notifications->lastItem() ?? 0 }} dari {{ $notifications->total() }} notifikasi</span>
            @if($notifications->hasPages())<div>{{ $notifications->links() }}</div>@endif
        </div>
    </section>

    {{-- KATEGORI NOTIFIKASI --}}
    <section class="panel sect">
        <div class="ph"><div><h2>Kategori Notifikasi</h2><p>Jenis pemberitahuan yang digunakan dalam operasional pengiriman.</p></div></div>

        <div class="cat-layout">
            <aside class="photo-card">
                <h3>Setiap kabar, terpantau dalam satu tempat.</h3>
                <p>Dari pengiriman baru sampai pengecualian selesai, semuanya tercatat agar operasional tetap terkendali.</p>
            </aside>

            <div class="type-grid">
                @foreach($typeLabels as $value => $label)
                    <div class="type-item">
                        <div class="type-icon"><i class="fa-solid {{ $typeIcons[$value] }}"></i></div>
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</section>
</main>

</body>
</html>
