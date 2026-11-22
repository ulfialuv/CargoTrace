<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Manajemen Pengiriman - CargoTrace</title>
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
.mstats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:22px}
.mstat{display:flex;align-items:center;gap:14px;padding:18px;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 10px 30px #2563eb0d;transition:.25s}
.mstat:hover{transform:translateY(-4px)}
.mstat .ico{display:grid;place-items:center;flex:none;width:48px;height:48px;border-radius:15px;font-size:19px}
.mstat small{display:block;font-size:11.5px;color:var(--mut);font-weight:600}
.mstat b{display:block;margin-top:5px;font:800 26px var(--h);color:var(--navy);line-height:1}
.mstat em{display:block;margin-top:5px;font-style:normal;font-size:10.5px;color:#8ca6c4}
.acts{display:flex;gap:6px}
.btn-edit{background:#fff4dc;color:#9a6814}
.btn-detail{background:#e8f1ff;color:#1d4ed8}
.btn-stat{background:#e3f6ff;color:#0e7490}
td.no{color:#8ca6c4}
td.num{font-weight:800;color:var(--navy)}

/* MODAL */
.op-modal{display:none;position:fixed;inset:0;background:rgba(6,21,51,.6);backdrop-filter:blur(3px);z-index:9999;align-items:center;justify-content:center;padding:18px}
.op-modal.show{display:flex}
.op-modal-box{background:#fff;width:100%;max-width:760px;max-height:92vh;overflow-y:auto;border-radius:24px;box-shadow:0 24px 60px rgba(6,21,51,.3)}
.op-modal-head{position:sticky;top:0;background:#fff;z-index:2;display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1px solid #edf3fa}
.op-modal-head h3{font:700 17px var(--h);color:var(--navy);margin:0}
.op-modal-close{border:0;background:#e8f1ff;color:#1d4ed8;border-radius:10px;width:34px;height:34px;font-size:20px;cursor:pointer}
.op-modal-body{padding:24px}
.op-modal-footer{display:flex;justify-content:flex-end;gap:9px;padding:16px 24px;border-top:1px solid #edf3fa}
.op-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.op-field label{display:block;font-size:11px;font-weight:700;color:#5d7aa5;margin-bottom:7px}
.op-field input,.op-field select,.op-field textarea{width:100%;padding:11px 12px;border:1px solid #dce8f8;border-radius:10px;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink);background:#fbfdff}
.op-field input:focus,.op-field select:focus,.op-field textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.op-field textarea{min-height:85px;resize:vertical}
.op-field.full{grid-column:1/-1}
.op-detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.op-detail-item label{display:block;color:#8ca6c4;font-size:10px;margin-bottom:5px}
.op-detail-item strong{font-size:12.5px;color:var(--navy);overflow-wrap:anywhere}
.op-sub{font:700 15px var(--h);color:var(--navy);margin-top:26px}
.op-timeline{border-left:2px solid #dce8f8;margin:18px 0 0 7px;padding-left:22px}
.op-event{position:relative;padding-bottom:18px}
.op-event:before{content:'';position:absolute;left:-29px;top:3px;width:10px;height:10px;border-radius:50%;background:var(--blue);border:3px solid #e0ecff}
.op-event strong{font-size:12.5px;color:var(--navy)}
.op-event p{font-size:11.5px;color:var(--mut);margin-top:5px;line-height:1.6}

@media(max-width:1100px){.mstats{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:600px){.op-form-grid,.op-detail-grid{grid-template-columns:1fr}.op-field.full{grid-column:auto}.op-modal{padding:8px}.op-modal-body{padding:16px}.mstats{grid-template-columns:1fr}}
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
    $statusClass = fn ($s) => match (strtoupper((string) $s)) {
        'BOOKED'     => 's-booked',
        'PICKED_UP'  => 's-picked',
        'IN_TRANSIT' => 's-transit',
        'DELIVERED'  => 's-delivered',
        default      => '',
    };
    $jsonFlags = JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG;
@endphp

<div class="topline">
    <div>
        <h1>Manajemen Pengiriman</h1>
        <p>Kelola shipment, informasi barang, dan aktivitas operasional CargoTrace.</p>
    </div>
    <button type="button" class="btn" onclick="openModal('addModal')">
        <i class="fa-solid fa-plus"></i> Tambah Pengiriman
    </button>
</div>

@if(session('success'))
    <div class="alert"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        <strong><i class="fa-solid fa-triangle-exclamation"></i>Terjadi kesalahan:</strong>
        <ul style="margin:7px 0 0 18px">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mstats">
    <div class="mstat">
        <div class="ico" style="background:#e8f1ff;color:#2563eb"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div><small>Total Pengiriman</small><b>{{ number_format($totalShipments) }}</b><em>Seluruh data shipment</em></div>
    </div>
    <div class="mstat">
        <div class="ico" style="background:#e0f4ff;color:#0ea5e9"><i class="fa-solid fa-file-circle-plus"></i></div>
        <div><small>Pengiriman Baru</small><b>{{ number_format($createdShipments) }}</b><em>Status CREATED</em></div>
    </div>
    <div class="mstat">
        <div class="ico" style="background:#fff4dc;color:#f59e0b"><i class="fa-solid fa-truck-fast"></i></div>
        <div><small>Dalam Perjalanan</small><b>{{ number_format($inTransitShipments) }}</b><em>Status IN TRANSIT</em></div>
    </div>
    <div class="mstat">
        <div class="ico" style="background:#e8f7ef;color:#16a34a"><i class="fa-solid fa-circle-check"></i></div>
        <div><small>Terkirim</small><b>{{ number_format($deliveredShipments) }}</b><em>Status DELIVERED</em></div>
    </div>
</div>

<section class="panel sect">
    <div class="ph"><div><h2>Pencarian dan Filter</h2><p>Cari berdasarkan nomor, customer, asal, atau tujuan.</p></div></div>
    <form action="{{ route('operator.pengiriman.index') }}" method="GET" class="filters" style="margin-bottom:0">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nomor, customer, asal, tujuan..." aria-label="Pencarian">
        <select name="status" aria-label="Status">
            <option value="">Semua Status</option>
            @foreach(['CREATED','BOOKED','PICKED_UP','IN_TRANSIT','DELIVERED'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_',' ',$status) }}</option>
            @endforeach
        </select>
        <select name="carrier" aria-label="Perusahaan">
            <option value="">Semua Perusahaan</option>
            @foreach($carriers as $carrier)
                <option value="{{ $carrier }}" @selected(request('carrier') === $carrier)>{{ $carrier }}</option>
            @endforeach
        </select>
        <button class="btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        <a href="{{ route('operator.pengiriman.index') }}" class="btn btn-light">Reset</a>
    </form>
</section>

<section class="panel sect">
    <div class="ph"><div><h2>Daftar Seluruh Pengiriman</h2><p>Detail, edit informasi, dan perbarui status pengiriman.</p></div></div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Pengiriman</th>
                    <th>Nama Pengiriman</th>
                    <th>Customer</th>
                    <th>Rute</th>
                    <th>Kurir</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shipments as $index => $shipment)
                    @php $payload = json_encode($shipment->toArray(), $jsonFlags); @endphp
                    <tr>
                        <td class="no">{{ $shipments->firstItem() + $index }}</td>
                        <td class="num">{{ $shipment->shipment_number }}</td>
                        <td>{{ $shipment->shipment_name }}</td>
                        <td>{{ $shipment->customer_name }}</td>
                        <td><div class="route">{{ $shipment->origin }} <i class="fa-solid fa-arrow-right"></i> {{ $shipment->destination }}</div></td>
                        <td>{{ $shipment->carrier }}</td>
                        <td><span class="status {{ $statusClass($shipment->status) }}">{{ str_replace('_',' ',$shipment->status) }}</span></td>
                        <td>
                            <div class="acts">
                                <button type="button" class="btn btn-detail" data-shipment="{{ $payload }}" onclick="showDetail(this)"><i class="fa-solid fa-eye"></i> Detail</button>
                                <button type="button" class="btn btn-edit" data-shipment="{{ $payload }}" onclick="editShipment(this)"><i class="fa-solid fa-pen"></i> Edit</button>
                                <button type="button" class="btn btn-stat"
                                    data-id="{{ $shipment->id }}"
                                    data-number="{{ $shipment->shipment_number }}"
                                    data-status="{{ $shipment->status }}"
                                    data-origin="{{ $shipment->origin }}"
                                    data-destination="{{ $shipment->destination }}"
                                    onclick="updateTracking(this)"><i class="fa-solid fa-arrows-rotate"></i> Status</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">Belum ada data pengiriman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pager">
        <span>Menampilkan {{ $shipments->firstItem() ?? 0 }}–{{ $shipments->lastItem() ?? 0 }} dari {{ $shipments->total() }} pengiriman</span>
        @if($shipments->hasPages())<div>{{ $shipments->links() }}</div>@endif
    </div>
</section>

{{-- MODAL TAMBAH --}}
<div class="op-modal" id="addModal">
    <div class="op-modal-box">
        <div class="op-modal-head">
            <h3>Tambah Pengiriman Baru</h3>
            <button type="button" class="op-modal-close" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form method="POST" action="{{ route('operator.pengiriman.store') }}">
            @csrf
            <div class="op-modal-body">
                <div class="op-form-grid">
                    <div class="op-field"><label>Nomor Pengiriman *</label><input name="shipment_number" value="{{ old('shipment_number') }}" placeholder="CT-2026-005" required></div>
                    <div class="op-field"><label>Nama Customer *</label><input name="customer_name" value="{{ old('customer_name') }}" required></div>
                    <div class="op-field"><label>Nama Pengiriman *</label><input name="shipment_name" value="{{ old('shipment_name') }}" required></div>
                    <div class="op-field"><label>Jenis Barang *</label><input name="cargo_type" value="{{ old('cargo_type') }}" required></div>
                    <div class="op-field"><label>Berat (kg) *</label><input type="number" step="0.01" min="0.01" name="weight" value="{{ old('weight') }}" required></div>
                    <div class="op-field"><label>Volume</label><input type="number" step="0.01" min="0" name="volume" value="{{ old('volume') }}"></div>
                    <div class="op-field"><label>Perusahaan Pengiriman *</label><input name="carrier" value="{{ old('carrier') }}" required></div>
                    <div class="op-field">
                        <label>Jenis Transportasi *</label>
                        <select name="transport_type" required>
                            <option value="">Pilih Transportasi</option>
                            <option value="LAND">Darat</option>
                            <option value="SEA">Laut</option>
                            <option value="AIR">Udara</option>
                        </select>
                    </div>
                    <div class="op-field"><label>Lokasi Asal *</label><input name="origin" value="{{ old('origin') }}" required></div>
                    <div class="op-field"><label>Lokasi Tujuan *</label><input name="destination" value="{{ old('destination') }}" required></div>
                    <div class="op-field"><label>Estimasi Keberangkatan *</label><input type="datetime-local" name="estimated_departure" required></div>
                    <div class="op-field"><label>Estimasi Kedatangan *</label><input type="datetime-local" name="estimated_arrival" required></div>
                    <div class="op-field full"><label>Catatan Tambahan</label><textarea name="notes">{{ old('notes') }}</textarea></div>
                </div>
            </div>
            <div class="op-modal-footer">
                <button type="button" class="btn btn-light" onclick="closeModal('addModal')">Batal</button>
                <button type="submit" class="btn">Simpan Pengiriman</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="op-modal" id="editModal">
    <div class="op-modal-box">
        <div class="op-modal-head">
            <h3>Edit Informasi Pengiriman</h3>
            <button type="button" class="op-modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="op-modal-body">
                <div class="op-form-grid">
                    <div class="op-field"><label>Nomor Pengiriman *</label><input name="shipment_number" id="edit_shipment_number" required></div>
                    <div class="op-field"><label>Nama Customer *</label><input name="customer_name" id="edit_customer_name" required></div>
                    <div class="op-field"><label>Nama Pengiriman *</label><input name="shipment_name" id="edit_shipment_name" required></div>
                    <div class="op-field"><label>Jenis Barang *</label><input name="cargo_type" id="edit_cargo_type" required></div>
                    <div class="op-field"><label>Berat (kg) *</label><input type="number" step="0.01" min="0.01" name="weight" id="edit_weight" required></div>
                    <div class="op-field"><label>Volume</label><input type="number" step="0.01" min="0" name="volume" id="edit_volume"></div>
                    <div class="op-field"><label>Perusahaan Pengiriman *</label><input name="carrier" id="edit_carrier" required></div>
                    <div class="op-field"><label>Jenis Transportasi *</label><select name="transport_type" id="edit_transport_type" required><option value="LAND">Darat</option><option value="SEA">Laut</option><option value="AIR">Udara</option></select></div>
                    <div class="op-field"><label>Asal *</label><input name="origin" id="edit_origin" required></div>
                    <div class="op-field"><label>Tujuan *</label><input name="destination" id="edit_destination" required></div>
                    <div class="op-field"><label>Estimasi Keberangkatan *</label><input type="datetime-local" name="estimated_departure" id="edit_estimated_departure" required></div>
                    <div class="op-field"><label>Estimasi Kedatangan *</label><input type="datetime-local" name="estimated_arrival" id="edit_estimated_arrival" required></div>
                    <div class="op-field full"><label>Catatan</label><textarea name="notes" id="edit_notes"></textarea></div>
                </div>
            </div>
            <div class="op-modal-footer">
                <button type="button" class="btn btn-light" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DETAIL --}}
<div class="op-modal" id="detailModal">
    <div class="op-modal-box">
        <div class="op-modal-head">
            <h3>Detail Pengiriman</h3>
            <button type="button" class="op-modal-close" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="op-modal-body">
            <div class="op-detail-grid" id="detailContent"></div>
            <h4 class="op-sub">Riwayat Tracking</h4>
            <div class="op-timeline" id="detailTimeline"></div>
        </div>
        <div class="op-modal-footer">
            <button type="button" class="btn btn-light" onclick="closeModal('detailModal')">Tutup</button>
        </div>
    </div>
</div>

{{-- MODAL PEMBARUAN STATUS --}}
<div class="op-modal" id="trackingModal">
    <div class="op-modal-box">
        <div class="op-modal-head">
            <h3>Pembaruan Status dan Tracking</h3>
            <button type="button" class="op-modal-close" onclick="closeModal('trackingModal')">&times;</button>
        </div>
        <form method="POST" id="trackingForm">
            @csrf
            @method('PUT')
            <div class="op-modal-body">
                <p id="trackingShipmentNumber" style="font-size:12px;color:#6c83ab;margin-bottom:17px"></p>
                <div class="op-form-grid">
                    <div class="op-field">
                        <label>Status Terbaru *</label>
                        <select name="status" id="tracking_status" required>
                            <option value="CREATED">CREATED</option>
                            <option value="BOOKED">BOOKED</option>
                            <option value="PICKED_UP">PICKED UP</option>
                            <option value="IN_TRANSIT">IN TRANSIT</option>
                            <option value="DELIVERED">DELIVERED</option>
                        </select>
                    </div>
                    <div class="op-field"><label>Lokasi Terbaru *</label><input name="location" id="tracking_location" required></div>
                    <div class="op-field"><label>Latitude</label><input type="number" step="any" name="latitude" placeholder="5.1801"></div>
                    <div class="op-field"><label>Longitude</label><input type="number" step="any" name="longitude" placeholder="97.1507"></div>
                    <div class="op-field full"><label>Keterangan Aktivitas</label><textarea name="description" placeholder="Jelaskan kondisi dan aktivitas pengiriman..."></textarea></div>
                </div>
            </div>
            <div class="op-modal-footer">
                <button type="button" class="btn btn-light" onclick="closeModal('trackingModal')">Batal</button>
                <button type="submit" class="btn">Simpan Pembaruan</button>
            </div>
        </form>
    </div>
</div>
</section>
</main>

<script>
function openModal(id){document.getElementById(id).classList.add('show')}
function closeModal(id){document.getElementById(id).classList.remove('show')}

document.querySelectorAll('.op-modal').forEach(function(modal){
    modal.addEventListener('click',function(e){
        if(e.target===modal) modal.classList.remove('show');
    });
});

function readShipment(button){
    try{return JSON.parse(button.dataset.shipment)}
    catch(e){alert('Data pengiriman tidak dapat dibaca.');return null}
}

function editShipment(button){
    const s=readShipment(button);
    if(!s)return;

    document.getElementById('editForm').action="{{ url('/operator/pengiriman') }}/"+s.id;

    ['shipment_number','customer_name','shipment_name','cargo_type','weight','volume','carrier','transport_type','origin','destination','notes']
    .forEach(function(field){
        document.getElementById('edit_'+field).value=s[field]??'';
    });

    ['estimated_departure','estimated_arrival'].forEach(function(field){
        let value=s[field]??'';
        if(value)value=value.replace(' ','T').substring(0,16);
        document.getElementById('edit_'+field).value=value;
    });

    openModal('editModal');
}

function showDetail(button){
    const s=readShipment(button);
    if(!s)return;

    const labels={
        shipment_number:'Nomor Pengiriman',shipment_name:'Nama Pengiriman',customer_name:'Customer',
        cargo_type:'Jenis Barang',weight:'Berat (kg)',volume:'Volume',carrier:'Perusahaan Pengiriman',
        transport_type:'Transportasi',origin:'Asal',destination:'Tujuan',status:'Status',
        estimated_departure:'Estimasi Keberangkatan',estimated_arrival:'Estimasi Kedatangan',notes:'Catatan'
    };

    const content=document.getElementById('detailContent');
    content.replaceChildren();

    Object.keys(labels).forEach(function(key){
        const item=document.createElement('div');
        item.className='op-detail-item';

        const label=document.createElement('label');
        label.textContent=labels[key];

        const strong=document.createElement('strong');
        let value=s[key]??'-';
        if(key==='status')value=String(value).replaceAll('_',' ');
        if(key.includes('estimated_')&&value!=='-')value=String(value).replace('T',' ').substring(0,16);
        strong.textContent=value;

        item.append(label,strong);
        content.appendChild(item);
    });

    const timeline=document.getElementById('detailTimeline');
    timeline.replaceChildren();

    const events=(s.tracking_events||[]).slice().sort(function(a,b){
        return new Date(b.event_time)-new Date(a.event_time);
    });

    if(events.length===0){
        timeline.textContent='Belum ada riwayat tracking.';
    }else{
        events.forEach(function(event){
            const item=document.createElement('div');
            item.className='op-event';

            const title=document.createElement('strong');
            title.textContent=String(event.status||'').replaceAll('_',' ');

            const p=document.createElement('p');
            p.textContent='Lokasi: '+(event.location||'-')+
                ' | Waktu: '+(event.event_time||'-')+
                ' | Oleh: '+(event.updated_by||'-')+
                ' | Keterangan: '+(event.description||'-');

            item.append(title,p);
            timeline.appendChild(item);
        });
    }

    openModal('detailModal');
}

function updateTracking(button){
    document.getElementById('trackingForm').action=
        "{{ url('/operator/pengiriman') }}/"+button.dataset.id+"/status";

    document.getElementById('trackingShipmentNumber').textContent='Nomor pengiriman: '+button.dataset.number;
    document.getElementById('tracking_status').value=button.dataset.status;
    document.getElementById('tracking_location').value=button.dataset.destination||button.dataset.origin||'';

    openModal('trackingModal');
}

document.addEventListener('keydown',function(e){
    if(e.key==='Escape'){
        document.querySelectorAll('.op-modal.show').forEach(function(modal){
            modal.classList.remove('show');
        });
    }
});
</script>
</body>
</html>
