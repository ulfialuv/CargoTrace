<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Manajemen Pengecualian - CargoTrace</title>
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
.alert-error{background:#ffe9e9;color:#c92f3a}
.alert ul{margin:6px 0 0 18px}

/* HERO FOTO */
.hero{position:relative;overflow:hidden;min-height:210px;padding:34px 40px;margin-bottom:22px;border-radius:28px;color:#fff;display:flex;justify-content:space-between;align-items:center;gap:24px;
 background:linear-gradient(100deg,#061533f2 0%,#0b2a6bd6 50%,#1d6fe86e 100%),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1800&q=80') center/cover,linear-gradient(135deg,#061533,#1d4ed8);box-shadow:0 24px 50px #0b2a6b40}
.hero::after{content:"";position:absolute;width:380px;height:380px;right:-110px;top:-190px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.hero h2{font:800 27px/1.3 var(--h);letter-spacing:-.8px;max-width:520px}
.hero p{margin-top:10px;max-width:470px;line-height:1.8;color:#cfe0ff}
.live{display:inline-flex;align-items:center;gap:8px;margin-bottom:14px;padding:7px 13px;border-radius:20px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11px;font-weight:600}
.live i{width:8px;height:8px;border-radius:50%;background:#fbbf24;animation:ping 1.8s infinite}
@keyframes ping{0%{box-shadow:0 0 0 0 #fbbf24aa}70%{box-shadow:0 0 0 9px #fbbf2400}100%{box-shadow:0 0 0 0 #fbbf2400}}
.hero-side{display:flex;flex-direction:column;gap:10px}
.hchip{display:flex;align-items:center;gap:12px;min-width:210px;padding:12px 17px;background:#ffffff1c;border:1px solid #ffffff35;border-radius:16px;backdrop-filter:blur(10px);font-size:11px;color:#dbe9ff}
.hchip b{font:800 22px var(--h);color:#fff;min-width:34px}

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

/* KEPARAHAN BERFOTO */
.sev-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
.sev{position:relative;overflow:hidden;min-height:112px;padding:18px;border-radius:20px;color:#fff;display:flex;flex-direction:column;justify-content:flex-end;
 background-size:cover;background-position:center}
.sev span{font-size:11px;font-weight:600;opacity:.9}
.sev b{font:800 30px var(--h);line-height:1.1}
.sev i{position:absolute;right:16px;top:14px;font-size:20px;opacity:.9}
.sev.low{background:linear-gradient(160deg,#16a34ae6,#065f46f2),url('https://images.unsplash.com/photo-1494412651409-8963ce7935a7?auto=format&fit=crop&w=800&q=70') center/cover,#16a34a}
.sev.medium{background:linear-gradient(160deg,#f59e0be6,#b45309f2),url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=70') center/cover,#f59e0b}
.sev.high{background:linear-gradient(160deg,#ef4444e6,#991b1bf2),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=800&q=70') center/cover,#ef4444}

/* FILTER + TABEL */
.filters{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.filters input,.filters select{padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:var(--ink);font:12px 'Plus Jakarta Sans',sans-serif}
.filters input.grow{flex:1;min-width:200px}
.filters input:focus,.filters select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.date-filter{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;margin:14px 0 4px}
.date-filter label{display:block;font-size:11px;font-weight:700;color:#5d7aa5;margin-bottom:6px}
.date-filter input{padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:10px;cursor:pointer;font:700 11px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;transition:.2s}
.btn:hover{filter:brightness(1.1)}
.btn-light{background:#e8f1ff;color:#1d4ed8}
.btn-block{padding:14px;font-size:12px}
.table-wrap{overflow-x:auto;margin-top:16px}
table{width:100%;border-collapse:collapse;min-width:850px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:11px;font-weight:700;padding:13px 14px}
th:first-child{border-radius:12px 0 0 12px}th:last-child{border-radius:0 12px 12px 0}
td{padding:14px;border-bottom:1px solid #edf3fa;color:#41608a;white-space:nowrap;vertical-align:middle}
td small{display:block;margin-top:4px;font-size:10.5px;color:#8ca6c4}
tbody tr:hover{background:#f6faff}
td.num{font-weight:800;color:var(--navy)}
.loc{display:flex;align-items:center;gap:7px}.loc i{color:#9db6dc}
.badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10px;font-weight:700}
.badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.low{background:#e8f7ef;color:#26734d}.medium{background:#fff4dc;color:#b77900}.high{background:#ffe9e9;color:#c92f3a}
.open{background:#fff4dc;color:#b77900}.investigating{background:#e5f1ff;color:#2876c5}.resolved{background:#e8f7ef;color:#26734d}
.btn-detail{background:#e8f1ff;color:#1d4ed8}
.empty{text-align:center;padding:38px 10px!important;color:#8ca6c4}
.empty i{display:block;font-size:28px;margin-bottom:10px}
.empty strong{display:block;color:var(--navy);margin-bottom:5px}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top:16px;font-size:11px;color:#8ca6c4;flex-wrap:wrap}
.pager nav svg{width:16px;height:16px}

/* FORM + FOTO SAMPING */
.form-layout{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:24px;align-items:start}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.f label{display:block;font-size:11px;font-weight:700;color:#5d7aa5;margin-bottom:7px}
.f label i{color:#e5484d;font-style:normal}
.f input,.f select,.f textarea{width:100%;padding:11px 12px;border:1px solid #dce8f8;border-radius:10px;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink);background:#fbfdff}
.f input:focus,.f select:focus,.f textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.f textarea{min-height:85px;resize:vertical}
.f small{display:block;margin-top:6px;font-size:10.5px;color:#8ca6c4}
.f.full{grid-column:1/-1}
.actions{display:flex;justify-content:flex-end;gap:10px;margin-top:20px}
.photo-card{position:relative;overflow:hidden;min-height:430px;border-radius:22px;color:#fff;padding:24px;display:flex;flex-direction:column;justify-content:flex-end;
 background:linear-gradient(180deg,#06153300 15%,#061533f0 90%),url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=75') center/cover,linear-gradient(135deg,#0a2257,#1d4ed8);box-shadow:0 18px 36px #0b2a6b30}
.photo-card h3{font:800 18px/1.35 var(--h);margin-bottom:10px}
.photo-card ul{list-style:none;display:flex;flex-direction:column;gap:9px;font-size:11.5px;color:#d6e4ff;line-height:1.5}
.photo-card li{display:flex;gap:9px}.photo-card li i{color:#7dd3fc;margin-top:2px}

/* DETAIL */
.overview{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;padding:18px;background:#f4f8ff;border:1px solid #e3ecfb;border-radius:18px}
.overview label{display:block;color:#8ca6c4;font-size:10px;margin-bottom:6px}
.overview strong{font-size:12.5px;color:var(--navy);overflow-wrap:anywhere}
.box{margin-top:16px;padding:18px;border:1px solid #e3ecfb;border-radius:16px}
.box h4,.sub{font:700 14px var(--h);color:var(--navy);margin-bottom:12px}
.box p{color:var(--mut);line-height:1.7;white-space:pre-line}
.attach{display:flex;gap:16px;align-items:center;margin-top:16px;padding:14px;border-radius:16px;background:#f4f8ff;border:1px solid #e3ecfb}
.attach img{width:120px;height:84px;object-fit:cover;border-radius:12px;flex:none}
.attach .ic{display:grid;place-items:center;width:52px;height:52px;border-radius:14px;background:#e8f1ff;color:var(--blue);font-size:20px;flex:none}
.attach a{margin-left:auto}
.handling{margin-top:22px;padding:20px;border-radius:18px;background:#f9fbff;border:1px solid #e3ecfb}
.resolved-box{margin-top:20px;padding:15px;border-radius:14px;background:#e5f7ed;color:#187344}
.timeline{display:flex;flex-direction:column;margin-top:6px}
.t-item{display:flex;gap:14px;padding:0 0 20px}
.t-dot{flex:none;width:12px;height:12px;margin-top:4px;border-radius:50%;background:var(--blue);border:3px solid #e0ecff}
.t-item strong{display:block;font-size:12.5px;color:var(--navy);margin-bottom:4px}
.t-item p{margin-top:4px;font-size:11.5px;color:var(--mut);line-height:1.6}
.t-item small{display:block;margin-top:4px;font-size:10.5px;color:#8ca6c4}
.who{font-size:10.5px;color:#8ca6c4;margin-left:6px;font-weight:500}

/* RESPONSIVE */
@media(max-width:1200px){.form-layout{grid-template-columns:1fr}.photo-card{min-height:240px}.hero-side{display:none}}
@media(max-width:1100px){.mstats{grid-template-columns:repeat(2,minmax(0,1fr))}.overview{grid-template-columns:repeat(2,1fr)}.content{padding:20px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .topline{flex-direction:column;align-items:flex-start}.hero{padding:26px 22px}.hero h2{font-size:21px}.panel{padding:18px}
}
@media(max-width:600px){.mstats,.sev-grid,.form-grid,.overview{grid-template-columns:1fr}.f.full{grid-column:auto}.attach{flex-wrap:wrap}}
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
        'CUSTOMS_HOLD' => 'Penahanan Bea Cukai',
        'WEATHER_DELAY' => 'Keterlambatan Cuaca',
        'PORT_CONGESTION' => 'Kepadatan Pelabuhan',
        'DAMAGED' => 'Barang Rusak',
        'MISROUTED' => 'Salah Rute',
        'DOCUMENTATION_ISSUE' => 'Masalah Dokumen',
    ];

    $severityLabels = [
        'LOW' => 'Rendah',
        'MEDIUM' => 'Sedang',
        'HIGH' => 'Tinggi',
    ];

    $statusLabels = [
        'OPEN' => 'Terbuka',
        'INVESTIGATING' => 'Dalam Investigasi',
        'RESOLVED' => 'Selesai',
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

    {{-- HEADER --}}
    <div class="topline">
        <div>
            <h1>Manajemen Pengecualian</h1>
            <p>Kelola kendala pengiriman, pantau tingkat keparahan, dan lakukan penanganan pengecualian.</p>
        </div>
        <a href="#form-pengecualian" class="btn"><i class="fa-solid fa-plus"></i> Laporkan Pengecualian</a>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- HERO FOTO --}}
    <div class="hero">
        <div>
            <div class="live"><i></i> Pemantauan kendala aktif</div>
            <h2>Tangani kendala pengiriman dengan cepat dan terukur.</h2>
            <p>Laporkan hambatan di lapangan, pantau tingkat risikonya, dan catat setiap langkah penanganan dalam satu layar.</p>
        </div>
        <div class="hero-side">
            <div class="hchip"><b>{{ $stats['open'] ?? 0 }}</b> perlu ditangani</div>
            <div class="hchip"><b>{{ $stats['high'] ?? 0 }}</b> risiko tinggi</div>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="mstats">
        <div class="mstat">
            <div class="ico" style="background:#e8f1ff;color:#2563eb"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div><small>Total Pengecualian</small><b>{{ $stats['total'] ?? 0 }}</b><em>Seluruh laporan pengecualian</em></div>
        </div>
        <div class="mstat">
            <div class="ico" style="background:#fff4dc;color:#f59e0b"><i class="fa-solid fa-folder-open"></i></div>
            <div><small>Terbuka</small><b>{{ $stats['open'] ?? 0 }}</b><em>Menunggu penanganan</em></div>
        </div>
        <div class="mstat">
            <div class="ico" style="background:#e0f4ff;color:#0ea5e9"><i class="fa-solid fa-magnifying-glass-chart"></i></div>
            <div><small>Dalam Investigasi</small><b>{{ $stats['investigating'] ?? 0 }}</b><em>Sedang ditangani operator</em></div>
        </div>
        <div class="mstat">
            <div class="ico" style="background:#e8f7ef;color:#16a34a"><i class="fa-solid fa-circle-check"></i></div>
            <div><small>Selesai</small><b>{{ $stats['resolved'] ?? 0 }}</b><em>Pengecualian terselesaikan</em></div>
        </div>
    </div>

    {{-- TINGKAT KEPARAHAN --}}
    <section class="panel sect">
        <div class="ph"><div><h2>Tingkat Keparahan</h2><p>Pantau tingkat risiko dari setiap pengecualian pengiriman.</p></div></div>
        <div class="sev-grid">
            <div class="sev low"><i class="fa-solid fa-shield-halved"></i><span>Rendah</span><b>{{ $stats['low'] ?? 0 }}</b></div>
            <div class="sev medium"><i class="fa-solid fa-triangle-exclamation"></i><span>Sedang</span><b>{{ $stats['medium'] ?? 0 }}</b></div>
            <div class="sev high"><i class="fa-solid fa-fire"></i><span>Tinggi</span><b>{{ $stats['high'] ?? 0 }}</b></div>
        </div>
    </section>

    {{-- DAFTAR PENGECUALIAN --}}
    <section class="panel sect">
        <div class="ph">
            <div><h2>Daftar Pengecualian</h2><p>Daftar kendala yang terjadi selama proses pengiriman.</p></div>
            <span class="count">{{ $exceptions->total() }} Data</span>
        </div>

        <form action="{{ route('operator.exceptions.index') }}" method="GET" class="filters">
            <input class="grow" type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pengiriman, pelanggan, atau lokasi...">

            <select name="type">
                <option value="">Semua Jenis</option>
                @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') == $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="severity">
                <option value="">Semua Keparahan</option>
                @foreach($severityLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('severity') == $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status">
                <option value="">Semua Status</option>
                @foreach($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') == $value)>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn"><i class="fa-solid fa-filter"></i> Filter</button>
            <a href="{{ route('operator.exceptions.index') }}" class="btn btn-light">Reset</a>
        </form>

        <form action="{{ route('operator.exceptions.index') }}" method="GET" class="date-filter">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="type" value="{{ request('type') }}">
            <input type="hidden" name="severity" value="{{ request('severity') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">

            <div><label>Dari Tanggal</label><input type="date" name="date_from" value="{{ request('date_from') }}"></div>
            <div><label>Sampai Tanggal</label><input type="date" name="date_to" value="{{ request('date_to') }}"></div>
            <button type="submit" class="btn btn-light"><i class="fa-regular fa-calendar"></i> Terapkan Tanggal</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pengiriman</th>
                        <th>Jenis Pengecualian</th>
                        <th>Lokasi</th>
                        <th>Waktu Kejadian</th>
                        <th>Keparahan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exceptions as $exception)
                        <tr>
                            <td>
                                <span class="num">{{ $exception->shipment->shipment_number ?? '-' }}</span>
                                <small>{{ $exception->shipment->customer_name ?? 'Pelanggan tidak tersedia' }}</small>
                            </td>
                            <td>{{ $typeLabels[$exception->type] ?? $exception->type }}</td>
                            <td><div class="loc"><i class="fa-solid fa-location-dot"></i>{{ $exception->location }}</div></td>
                            <td>
                                {{ $exception->occurred_at?->format('d M Y') ?? '-' }}
                                <small>{{ $exception->occurred_at?->format('H:i') ?? '' }}</small>
                            </td>
                            <td><span class="badge {{ strtolower($exception->severity) }}">{{ $severityLabels[$exception->severity] ?? $exception->severity }}</span></td>
                            <td><span class="badge {{ strtolower($exception->status) }}">{{ $statusLabels[$exception->status] ?? $exception->status }}</span></td>
                            <td>
                                <a href="{{ route('operator.exceptions.show', $exception->id) }}" class="btn btn-detail">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">
                                <i class="fa-solid fa-folder-open"></i>
                                <strong>Belum ada data pengecualian</strong>
                                Data pengecualian akan muncul setelah laporan dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            <span>Menampilkan {{ $exceptions->firstItem() ?? 0 }}–{{ $exceptions->lastItem() ?? 0 }} dari {{ $exceptions->total() }} data</span>
            @if($exceptions->hasPages())<div>{{ $exceptions->links() }}</div>@endif
        </div>
    </section>

    {{-- FORM LAPORAN BARU --}}
    <section class="panel sect" id="form-pengecualian" style="scroll-margin-top:25px">
        <div class="ph"><div><h2>Laporkan Pengecualian Baru</h2><p>Masukkan informasi kendala yang ditemukan pada proses pengiriman.</p></div></div>

        <div class="form-layout">
            <form action="{{ route('operator.exceptions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-grid">
                    <div class="f">
                        <label>Nomor Pengiriman <i>*</i></label>
                        <select name="shipment_id" required>
                            <option value="">Pilih Pengiriman</option>
                            @foreach($shipments as $shipment)
                                <option value="{{ $shipment->id }}" @selected(old('shipment_id') == $shipment->id)>
                                    {{ $shipment->shipment_number }} - {{ $shipment->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="f">
                        <label>Jenis Pengecualian <i>*</i></label>
                        <select name="type" required>
                            <option value="">Pilih Jenis</option>
                            @foreach($typeLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('type') == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="f">
                        <label>Lokasi Kejadian <i>*</i></label>
                        <input type="text" name="location" value="{{ old('location') }}" placeholder="Contoh: Pelabuhan Tanjung Priok" required>
                    </div>

                    <div class="f">
                        <label>Waktu Kejadian <i>*</i></label>
                        <input type="datetime-local" name="occurred_at" value="{{ old('occurred_at') }}" required>
                    </div>

                    <div class="f">
                        <label>Tingkat Keparahan <i>*</i></label>
                        <select name="severity" required>
                            <option value="">Pilih Tingkat</option>
                            @foreach($severityLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('severity') == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="f full">
                        <label>Deskripsi Pengecualian <i>*</i></label>
                        <textarea name="description" rows="4" placeholder="Jelaskan kendala yang terjadi pada pengiriman..." required>{{ old('description') }}</textarea>
                    </div>

                    <div class="f full">
                        <label>Unggah Bukti Pendukung</label>
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <small>Format JPG, PNG, PDF, DOC, DOCX. Maksimal 5 MB.</small>
                    </div>
                </div>

                <div class="actions">
                    <button type="reset" class="btn btn-light">Reset Form</button>
                    <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> Kirim Laporan</button>
                </div>
            </form>

            <aside class="photo-card">
                <h3>Laporkan kendala lebih cepat, tangani lebih tepat.</h3>
                <ul>
                    <li><i class="fa-solid fa-circle-check"></i> Pilih pengiriman dan jenis kendala yang sesuai.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Tulis lokasi dan waktu kejadian dengan jelas.</li>
                    <li><i class="fa-solid fa-circle-check"></i> Lampirkan foto atau dokumen sebagai bukti.</li>
                </ul>
            </aside>
        </div>
    </section>

    {{-- DETAIL PENGECUALIAN --}}
    @if($focus)
        <section class="panel sect" id="detail-pengecualian" style="scroll-margin-top:25px">
            <div class="ph">
                <div><h2>Detail Pengecualian</h2><p>Informasi lengkap dan riwayat penanganan pengecualian.</p></div>
                <a href="{{ route('operator.exceptions.index') }}" class="btn btn-light"><i class="fa-solid fa-xmark"></i> Tutup Detail</a>
            </div>

            <div class="overview">
                <div><label>Nomor Pengiriman</label><strong>{{ $focus->shipment->shipment_number ?? '-' }}</strong></div>
                <div><label>Pelanggan</label><strong>{{ $focus->shipment->customer_name ?? '-' }}</strong></div>
                <div><label>Perusahaan Pengiriman</label><strong>{{ $focus->shipment->carrier ?? '-' }}</strong></div>
                <div><label>Jenis Pengecualian</label><strong>{{ $typeLabels[$focus->type] ?? $focus->type }}</strong></div>
                <div><label>Lokasi Kejadian</label><strong>{{ $focus->location }}</strong></div>
                <div><label>Waktu Kejadian</label><strong>{{ $focus->occurred_at?->format('d M Y, H:i') ?? '-' }}</strong></div>
                <div>
                    <label>Tingkat Keparahan</label>
                    <span class="badge {{ strtolower($focus->severity) }}">{{ $severityLabels[$focus->severity] ?? $focus->severity }}</span>
                </div>
                <div>
                    <label>Status Saat Ini</label>
                    <span class="badge {{ strtolower($focus->status) }}">{{ $statusLabels[$focus->status] ?? $focus->status }}</span>
                </div>
            </div>

            <div class="box">
                <h4>Deskripsi Kendala</h4>
                <p>{{ $focus->description }}</p>
            </div>

            @if($focus->attachment_path)
                @php
                    $attachUrl = \Illuminate\Support\Facades\Storage::url($focus->attachment_path);
                    $isImage = in_array(strtolower(pathinfo($focus->attachment_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']);
                @endphp
                <div class="attach">
                    @if($isImage)
                        <img src="{{ $attachUrl }}" alt="Bukti pendukung">
                    @else
                        <div class="ic"><i class="fa-solid fa-paperclip"></i></div>
                    @endif
                    <div>
                        <strong style="color:var(--navy)">Bukti pendukung tersedia</strong>
                        <small style="display:block;margin-top:4px;color:#8ca6c4">Lampiran dari laporan pengecualian ini.</small>
                    </div>
                    <a href="{{ $attachUrl }}" target="_blank" class="btn btn-light">Lihat Lampiran</a>
                </div>
            @endif

            {{-- FORM PENANGANAN --}}
            @if($focus->status !== 'RESOLVED')
                <div class="handling">
                    <h4 class="sub"><i class="fa-solid fa-screwdriver-wrench"></i> Pembaruan Penanganan</h4>

                    <form action="{{ route('operator.exceptions.update', $focus->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="f">
                                <label>Perbarui Status</label>
                                <select name="status" required>
                                    @if($focus->status === 'OPEN')
                                        <option value="OPEN">Terbuka</option>
                                        <option value="INVESTIGATING">Dalam Investigasi</option>
                                    @elseif($focus->status === 'INVESTIGATING')
                                        <option value="INVESTIGATING">Dalam Investigasi</option>
                                        <option value="RESOLVED">Selesai</option>
                                    @endif
                                </select>
                            </div>

                            <div class="f">
                                <label>Tingkat Keparahan</label>
                                <select name="severity" required>
                                    @foreach($severityLabels as $value => $label)
                                        <option value="{{ $value }}" @selected($focus->severity == $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="f full">
                                <label>Penyebab Pengecualian</label>
                                <textarea name="cause" rows="3" placeholder="Tuliskan penyebab terjadinya kendala...">{{ old('cause', $focus->cause) }}</textarea>
                            </div>

                            <div class="f full">
                                <label>Dampak terhadap ETA</label>
                                <textarea name="impact" rows="3" placeholder="Jelaskan dampak kendala terhadap estimasi waktu kedatangan...">{{ old('impact', $focus->impact) }}</textarea>
                            </div>

                            <div class="f full">
                                <label>Catatan Tindakan / Progres <i>*</i></label>
                                <textarea name="note" rows="4" placeholder="Tuliskan tindakan yang telah dilakukan atau progres penanganan..." required></textarea>
                            </div>

                            <div class="f full">
                                <label>Lampiran Tambahan</label>
                                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                <small>Opsional, maksimal 5 MB.</small>
                            </div>
                        </div>

                        <div class="actions">
                            <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan Penanganan</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="resolved-box">
                    <i class="fa-solid fa-circle-check"></i>
                    Pengecualian ini telah diselesaikan.
                    @if($focus->resolved_at)
                        <strong>{{ $focus->resolved_at->format('d M Y, H:i') }}</strong>
                    @endif
                </div>
            @endif

            {{-- RIWAYAT --}}
            <h4 class="sub" style="margin-top:26px"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Penanganan</h4>

            <div class="timeline">
                @forelse($focus->histories as $history)
                    <div class="t-item">
                        <div class="t-dot"></div>
                        <div>
                            <strong>
                                @if($history->action === 'CREATED')
                                    Pengecualian Dilaporkan
                                @elseif($history->action === 'STATUS_CHANGED')
                                    Perubahan Status
                                @elseif($history->action === 'HANDLING_NOTE')
                                    Catatan Penanganan
                                @else
                                    {{ str_replace('_', ' ', $history->action) }}
                                @endif
                                <span class="who">{{ $history->user->name ?? 'Pengguna tidak tersedia' }}</span>
                            </strong>

                            @if($history->old_status || $history->new_status)
                                <p>
                                    {{ $statusLabels[$history->old_status] ?? $history->old_status ?? '—' }}
                                    <i class="fa-solid fa-arrow-right" style="font-size:9px;margin:0 5px"></i>
                                    {{ $statusLabels[$history->new_status] ?? $history->new_status ?? '—' }}
                                </p>
                            @endif

                            @if($history->note)
                                <p>{{ $history->note }}</p>
                            @endif

                            <small>{{ $history->created_at?->format('d M Y, H:i') }}</small>
                        </div>
                    </div>
                @empty
                    <p style="color:#8ca6c4">Belum ada riwayat penanganan.</p>
                @endforelse
            </div>
        </section>
    @endif

</section>
</main>

</body>
</html>
