<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Notifikasi | CargoTrace</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
:root{--navy:#071633;--blue:#2457f5;--sky:#38bdf8;--ink:#17306a;--mut:#6b7fa3;--line:#e1e9f7;--bg:#eef3fb;--h:'Sora',sans-serif;
 --t-blue:#2457f5;--t-blue-bg:#e8f1ff;--t-warning:#e78a32;--t-warning-bg:#fff1e3;--t-danger:#e5484d;--t-danger-bg:#ffe9ea;--t-success:#16a34a;--t-success-bg:#e6f7ee}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--ink);font-size:13px}
body.lock{overflow:hidden}
a{color:inherit}
button{font-family:inherit}
:focus-visible{outline:2px solid var(--blue);outline-offset:2px}

/* SIDEBAR */
.sidebar{position:fixed;inset:0 auto 0 0;width:272px;padding:26px 18px 18px;display:flex;flex-direction:column;background:linear-gradient(180deg,#071633 0%,#0a2257 65%,#0f3a8f 100%);color:#fff;z-index:100;overflow-y:auto}
.sidebar::before{content:"";position:absolute;width:300px;height:300px;top:-140px;right:-150px;border-radius:50%;background:radial-gradient(#38bdf860,#0000 70%);pointer-events:none}
.brand{display:flex;align-items:center;gap:11px;padding:0 8px;position:relative}
.brand-logo{display:grid;place-items:center;width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#38bdf8,#2457f5);font-size:20px;box-shadow:0 8px 22px #2457f577}
.brand-name{font:800 22px var(--h);letter-spacing:-.5px}
.brand-sub{margin:8px 8px 26px;font-size:10px;color:#9fbcea;position:relative}
.s-title{margin:0 12px 10px;font-size:11px;font-weight:700;color:#6f95d6}
.s-wrap{position:relative;margin-bottom:22px}
.s-menu{display:flex;flex-direction:column;gap:4px;position:relative}
.s-link{display:flex;align-items:center;gap:13px;min-height:44px;padding:10px 14px;border-radius:13px;color:#dce8ff;text-decoration:none;font-weight:500;transition:.2s}
.s-link i{width:18px;text-align:center;font-size:14px;color:#8fb3ec}
.s-link:hover{background:#ffffff18}
.s-link.active{background:linear-gradient(90deg,#2457f5,#3b8bf5);color:#fff;font-weight:700;box-shadow:0 10px 24px #2457f555}
.s-link.active i{color:#fff}
.bdg{margin-left:auto;min-width:20px;padding:2px 6px;border-radius:10px;background:#e5484d;color:#fff;font-size:10px;font-weight:700;text-align:center;font-style:normal}
.s-user{margin-top:auto;position:relative;padding:14px;border-radius:16px;background:#ffffff14;border:1px solid #ffffff2b}
.s-user .who{display:flex;align-items:center;gap:11px}
.avatar{display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2457f5);font-weight:800;flex:none}
.s-user b{display:block;font-size:12.5px}.s-user small{font-size:10px;color:#a9c4f0}
.s-user button{width:100%;margin-top:12px;padding:9px;border:1px solid #ffffff33;border-radius:10px;background:transparent;color:#dce8ff;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.s-user button:hover{background:#ffffff1c}

/* MAIN */
.main{margin-left:272px;min-height:100vh;background:radial-gradient(circle at 100% 0,#cfe0ff 0,#0000 380px),radial-gradient(circle at 0 100%,#d7e8ff 0,#0000 340px),var(--bg)}
.content{padding:26px 32px 40px}
.topline{margin-bottom:18px}
.topline h1{font:800 26px var(--h);color:var(--navy);letter-spacing:-.6px}
.topline p{margin-top:5px;color:var(--mut)}

.toast{display:flex;align-items:center;gap:10px;padding:14px 18px;margin-bottom:18px;border-radius:16px;background:#e6f7ee;border:1px solid #c4ebd5;color:#15693f;font-weight:600;font-size:12.5px;animation:toast .4s both}
@keyframes toast{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}

/* HERO */
.hero{position:relative;overflow:hidden;min-height:250px;padding:40px 48px 104px;border-radius:32px;color:#fff;display:flex;justify-content:space-between;align-items:flex-start;gap:28px;
 background:linear-gradient(100deg,#071633f5 0%,#0b2a6bdd 52%,#1d6fe866 100%),url('https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?auto=format&fit=crop&w=1900&q=75') center/cover,linear-gradient(135deg,#071633,#1d4ed8);box-shadow:0 28px 58px #0b2a6b46}
.hero::after{content:"";position:absolute;width:460px;height:460px;right:-130px;top:-220px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.live{display:inline-flex;align-items:center;gap:8px;margin-bottom:16px;padding:7px 14px;border-radius:20px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11px;font-weight:600}
.live i{width:8px;height:8px;border-radius:50%;background:#fb7185;animation:ping 1.8s infinite}
.live.clear i{background:#4ade80;animation:none}
@keyframes ping{0%{box-shadow:0 0 0 0 #fb7185aa}70%{box-shadow:0 0 0 9px #fb718500}100%{box-shadow:0 0 0 0 #fb718500}}
.hero h2{font:800 31px/1.22 var(--h);letter-spacing:-1px;max-width:560px}
.hero p{margin-top:12px;max-width:480px;line-height:1.85;color:#cfe0ff}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 20px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:13px;font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.2s;white-space:nowrap}
.btn:hover{filter:brightness(1.1)}
.btn-light{background:#e8f1ff;color:#1d4ed8}
.btn-white{background:#fff;color:#1d4ed8;box-shadow:0 14px 28px #02081c44}
.hero .btn{margin-top:22px}
.hero-big{flex:none;text-align:center;min-width:190px;padding:20px 26px;border-radius:24px;background:#ffffff1c;border:1px solid #ffffff35;backdrop-filter:blur(10px)}
.hero-big b{display:block;font:800 52px/1 var(--h);letter-spacing:-2px}
.hero-big span{display:block;margin-top:8px;font-size:12px;color:#dbe9ff}

/* STATISTIK */
.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:-72px 22px 26px;position:relative;z-index:3}
.stat{display:flex;align-items:center;gap:14px;padding:18px;background:#ffffffee;backdrop-filter:blur(10px);border:1px solid #fff;border-radius:22px;box-shadow:0 18px 40px #0b2a6b24;min-width:0}
.stat>i{flex:none;display:grid;place-items:center;width:48px;height:48px;border-radius:16px;font-size:18px}
.stat small{display:block;font-size:11.5px;color:var(--mut);font-weight:600}
.stat b{display:block;margin-top:5px;font:800 26px var(--h);color:var(--navy);line-height:1}
.i-blue{background:var(--t-blue-bg);color:var(--t-blue)}.i-warning{background:var(--t-warning-bg);color:var(--t-warning)}
.i-danger{background:var(--t-danger-bg);color:var(--t-danger)}.i-success{background:var(--t-success-bg);color:var(--t-success)}

/* PANEL */
.panel{padding:26px;background:#fff;border:1px solid var(--line);border-radius:28px;box-shadow:0 10px 30px #2457f50d}
.sect{margin-bottom:22px}

/* TOOLBAR */
.toolbar{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.sbox{flex:1;min-width:240px;display:flex;align-items:center;gap:6px;height:48px;padding:0 6px 0 16px;border:1px solid #dce8f8;border-radius:15px;background:#fbfdff}
.sbox:focus-within{border-color:var(--blue);box-shadow:0 0 0 3px #2457f51f}
.sbox i{color:#8ca6c4}
.sbox input{flex:1;min-width:0;border:0;outline:0;background:transparent;font:500 13px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.sbox button{height:36px;padding:0 16px;border:0;border-radius:11px;background:var(--blue);color:#fff;font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.seg{display:inline-flex;padding:4px;border-radius:14px;background:#eaf1ff}
.seg a{padding:10px 15px;border-radius:11px;color:#5d7aa5;text-decoration:none;font-size:12px;font-weight:700;white-space:nowrap}
.seg a.on{background:#fff;color:var(--blue);box-shadow:0 4px 12px #2457f522}
.cats{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}
.cat{display:inline-flex;align-items:center;gap:8px;padding:9px 14px;border:1px solid #dce8f8;border-radius:30px;background:#fff;color:#5d7aa5;text-decoration:none;font-size:12px;font-weight:700;transition:.2s}
.cat:hover{border-color:#b9cff5}
.cat em{min-width:22px;padding:2px 7px;border-radius:12px;background:#eef3fb;color:#5d7aa5;font-size:10.5px;font-style:normal;text-align:center}
.cat.on{background:var(--navy);border-color:var(--navy);color:#fff}
.cat.on em{background:#ffffff26;color:#fff}

/* LIST */
.lh{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:6px}
.lh h2{font:700 18px var(--h);color:var(--navy);letter-spacing:-.3px}
.lh p{margin-top:6px;font-size:12px;color:var(--mut)}
.day{display:flex;align-items:center;gap:12px;margin:24px 0 12px;color:#6c86b3;font-size:12px;font-weight:700}
.day::after{content:"";flex:1;height:1px;background:var(--line)}
.n{position:relative;display:flex;gap:16px;padding:18px 18px 18px 20px;margin-bottom:10px;border:1px solid var(--line);border-radius:20px;background:#fff;transition:.2s;overflow:hidden}
.n:hover{border-color:#bcd0f5;box-shadow:0 12px 28px #2457f51a;transform:translateY(-2px)}
.n::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--c)}
.n.unread{background:linear-gradient(90deg,#f3f7ff,#fff 60%);border-color:#d3e1fb}
.n .go{position:absolute;inset:0;z-index:1}
.n-ic{flex:none;display:grid;place-items:center;width:48px;height:48px;border-radius:16px;font-size:18px;background:var(--bg-c);color:var(--c)}
.tone-blue{--c:var(--t-blue);--bg-c:var(--t-blue-bg)}.tone-warning{--c:var(--t-warning);--bg-c:var(--t-warning-bg)}
.tone-danger{--c:var(--t-danger);--bg-c:var(--t-danger-bg)}.tone-success{--c:var(--t-success);--bg-c:var(--t-success-bg)}
.n-body{flex:1;min-width:0}
.n-top{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
.n-title{display:flex;align-items:center;gap:9px;font:700 14px/1.45 var(--h);color:var(--navy)}
.dot{flex:none;width:9px;height:9px;border-radius:50%;background:var(--blue);box-shadow:0 0 0 4px #2457f522}
.n-time{flex:none;font-size:11px;color:#8ca6c4;white-space:nowrap}
.n-msg{margin-top:7px;font-size:12.5px;line-height:1.7;color:#506a98;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.n-foot{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:13px;flex-wrap:wrap}
.tags{display:flex;gap:8px;flex-wrap:wrap}
.tag{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:20px;background:#eef3fb;color:#4a6592;font-size:11px;font-weight:600}
.tag.cat-delay{background:var(--t-danger-bg);color:#c0343f}.tag.cat-exception{background:var(--t-warning-bg);color:#b2641a}
.acts{position:relative;z-index:2;display:flex;gap:6px}
.acts form{display:inline}
.ib{display:grid;place-items:center;width:34px;height:34px;border:0;border-radius:11px;background:#eef3fb;color:#5d7aa5;font-size:12px;cursor:pointer;text-decoration:none;transition:.2s}
.ib:hover{background:var(--blue);color:#fff}
.ib.del:hover{background:var(--t-danger);color:#fff}

.empty{padding:56px 14px;text-align:center;color:var(--mut)}
.empty>i{display:grid;place-items:center;width:78px;height:78px;margin:0 auto 16px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:30px}
.empty strong{display:block;font:700 17px var(--h);color:var(--navy);margin-bottom:7px}
.empty .btn{margin-top:18px}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top:22px;font-size:11.5px;color:#8ca6c4;flex-wrap:wrap}
.pager nav svg{width:16px;height:16px}
.footer{text-align:center;color:#8ca6c4;font-size:11px;margin-top:30px}

/* DRAWER */
.ov{position:fixed;inset:0;background:#030b1fb0;backdrop-filter:blur(3px);opacity:0;visibility:hidden;transition:.3s;z-index:900}
.ov.on{opacity:1;visibility:visible}
.drawer{position:fixed;top:0;right:0;bottom:0;width:min(520px,100%);background:var(--bg);z-index:910;transform:translateX(100%);transition:transform .35s cubic-bezier(.2,.8,.2,1);display:flex;flex-direction:column;box-shadow:-30px 0 60px #02081c44}
.drawer.on{transform:none}
.d-close{position:absolute;top:18px;right:18px;z-index:5;width:40px;height:40px;border:1px solid #ffffff44;border-radius:12px;background:#ffffff22;color:#fff;font-size:15px;cursor:pointer;backdrop-filter:blur(6px)}
.d-close:hover{background:#ffffff40}
.d-scroll{flex:1;overflow-y:auto;overscroll-behavior:contain}
.dh{padding:34px 28px 52px;color:#fff;background:linear-gradient(135deg,#071633,#12347f)}
.dh.tone-danger{background:linear-gradient(135deg,#3b0d18,#9c2a38)}
.dh.tone-warning{background:linear-gradient(135deg,#3a2308,#a8621c)}
.dh.tone-success{background:linear-gradient(135deg,#06301d,#16794a)}
.dh .big{display:grid;place-items:center;width:58px;height:58px;margin-bottom:18px;border-radius:18px;background:#ffffff26;border:1px solid #ffffff3a;font-size:24px}
.dh h2{font:800 21px/1.35 var(--h);letter-spacing:-.4px;padding-right:46px}
.dh small{display:block;margin-top:10px;color:#ffffffcc;font-size:12px}
.dbody{margin:-26px 18px 0;position:relative}
.card{padding:20px;background:#fff;border:1px solid var(--line);border-radius:22px;margin-bottom:14px;box-shadow:0 10px 26px #0b2a6b14}
.card h4{margin-bottom:12px;font:700 13px var(--h);color:var(--navy);display:flex;align-items:center;gap:9px}
.card h4 i{display:grid;place-items:center;width:28px;height:28px;border-radius:9px;background:#e8f1ff;color:var(--blue);font-size:11px}
.card p.msg{font-size:13px;line-height:1.85;color:#3f5985;overflow-wrap:anywhere}
.route{display:flex;align-items:center;gap:12px;margin-top:14px;padding:14px 16px;border-radius:16px;background:#f4f8ff;border:1px solid #e3ecfb}
.route .pt{flex:1;min-width:0}.route .pt.r{text-align:right}
.route label{display:block;font-size:10.5px;color:#8ca6c4;margin-bottom:3px}
.route strong{font-size:12.5px;color:var(--navy);overflow-wrap:anywhere}
.route .mid{flex:none;display:grid;place-items:center;width:30px;height:30px;border-radius:50%;background:var(--blue);color:#fff;font-size:11px}
.kv{display:grid;grid-template-columns:1fr 1fr;gap:15px 18px}
.kv .full{grid-column:1/-1}
.kv label{display:block;font-size:10.5px;color:#8ca6c4;margin-bottom:4px}
.kv strong{font-size:12.5px;color:#26417a;font-weight:600;overflow-wrap:anywhere}
.kv .na{color:#a9b6cc;font-weight:500}
.st{display:inline-flex;align-items:center;gap:6px;padding:5px 11px;border-radius:20px;font-size:11px;font-weight:700;background:#eef0f4;color:#667085}
.st::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.st.transit{background:#e5f1ff;color:#2876c5}.st.done{background:#e8f7ef;color:#26734d}.st.created{background:#fff4dc;color:#b77900}.st.late{background:#ffe9e9;color:#c92f3a}
.d-foot{flex:none;display:flex;flex-direction:column;gap:9px;padding:14px 18px;background:#fff;border-top:1px solid var(--line)}
.d-foot .row{display:flex;gap:9px}.d-foot .row>*{flex:1}
.btn-del{background:#fff0f1;color:#d6424d}
.btn-del:hover{background:#d6424d;color:#fff;filter:none}

/* RESPONSIVE */
@media(max-width:1200px){.stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:1100px){.content{padding:20px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .hero{padding:30px 22px 100px;flex-direction:column}.hero h2{font-size:23px}.hero-big{align-self:stretch}
 .stats{margin:-70px 6px 22px;gap:10px}.stat{padding:14px}.stat b{font-size:21px}
 .panel{padding:18px}.sbox{min-width:100%}.n{padding:16px 14px 16px 16px;gap:12px}.n-ic{width:42px;height:42px;font-size:16px}
 .n-top{flex-direction:column;gap:4px}.kv{grid-template-columns:1fr}
}
@media(max-width:480px){.stats{grid-template-columns:1fr}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>

<body>

@php
    $shipStatus = [
        'CREATED' => ['Dibuat', 'created'],
        'BOOKED' => ['Dijadwalkan', 'created'],
        'PICKED_UP' => ['Telah Dijemput', 'transit'],
        'IN_TRANSIT' => ['Dalam Perjalanan', 'transit'],
        'DELIVERED' => ['Terkirim', 'done'],
        'DELAYED' => ['Terlambat', 'late'],
    ];

    $catLabel = ['shipment' => 'Pengiriman', 'delay' => 'Keterlambatan', 'exception' => 'Kendala'];
    $catIcon = ['shipment' => 'fa-box', 'delay' => 'fa-clock', 'exception' => 'fa-circle-exclamation'];

    $dayLabel = function ($date) {
        if (!$date) return 'Tanpa tanggal';
        $d = \Carbon\Carbon::parse($date);
        if ($d->isToday()) return 'Hari ini';
        if ($d->isYesterday()) return 'Kemarin';
        return $d->translatedFormat('l, d F Y');
    };

    $fmt = function ($date, $format = 'd M Y, H:i') {
        if (!$date) return '-';
        try { return \Carbon\Carbon::parse($date)->format($format); }
        catch (\Throwable $e) { return '-'; }
    };

    $ago = function ($date) {
        if (!$date) return '-';
        $d = \Carbon\Carbon::parse($date);
        return $d->isToday() ? $d->format('H:i') : $d->format('d M, H:i');
    };

    // Link yang mempertahankan filter dan pencarian saat berpindah.
    $link = fn (array $over) => route('customer.notifikasi.index', array_filter(
        array_merge(['status' => $statusFilter, 'type' => $typeFilter, 'search' => $search], $over),
        fn ($v) => $v !== null && $v !== '' && $v !== 'all'
    ));

    $val = fn ($v) => ($v === null || $v === '' || $v === '-') ? null : $v;
    $kv = fn ($label, $v, $full = false) => '<div' . ($full ? ' class="full"' : '') . '><label>' . e($label) . '</label><strong'
        . (($val($v) === null) ? ' class="na">Belum tersedia' : '>' . e($v)) . '</strong></div>';

    $grouped = $notifications->getCollection()->groupBy(fn ($i) => $dayLabel($i['created_at']));
@endphp

<aside class="sidebar">
    <div class="brand">
        <div class="brand-logo"><i class="fa-solid fa-route"></i></div>
        <div class="brand-name">CargoTrace</div>
    </div>
    <div class="brand-sub">Logistics &amp; Shipment Tracking</div>

    <div class="s-wrap">
        <div class="s-title">Menu Customer</div>
        <nav class="s-menu">
            <a href="{{ route('customer.dashboard') }}" class="s-link"><i class="fa-solid fa-border-all"></i><span>Dashboard</span></a>
            <a href="{{ route('customer.pengiriman.index') }}" class="s-link"><i class="fa-solid fa-box"></i><span>Pengiriman Saya</span></a>
            <a href="{{ route('customer.pelacakan.index') }}" class="s-link"><i class="fa-solid fa-location-dot"></i><span>Pelacakan Pengiriman</span></a>
            <a href="{{ route('customer.notifikasi.index') }}" class="s-link active">
                <i class="fa-solid fa-bell"></i><span>Notifikasi</span>
                @if($unreadNotifications > 0)<em class="bdg">{{ $unreadNotifications }}</em>@endif
            </a>
            <a href="{{ url('/customer/profil') }}" class="s-link"><i class="fa-solid fa-user"></i><span>Profil Saya</span></a>
        </nav>
    </div>

    <div class="s-user">
        <div class="who">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}</div>
            <div><b>{{ auth()->user()->name }}</b><small>Customer</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </form>
    </div>
</aside>

<main class="main">
<section class="content">

    <div class="topline">
        <h1>Notifikasi</h1>
        <p>Semua kabar terbaru tentang pengiriman Anda.</p>
    </div>

    @if(session('success'))
        <div class="toast"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    {{-- HERO --}}
    <div class="hero">
        <div>
            <div class="live {{ $unreadNotifications > 0 ? '' : 'clear' }}">
                <i></i> {{ $unreadNotifications > 0 ? 'Ada kabar yang belum Anda baca' : 'Semua notifikasi sudah dibaca' }}
            </div>
            <h2>{{ $unreadNotifications > 0 ? 'Ada pembaruan baru untuk pengiriman Anda.' : 'Tidak ada yang terlewat.' }}</h2>
            <p>Pantau perubahan status, keterlambatan, dan kendala pengiriman di satu tempat.</p>

            @if($unreadNotifications > 0)
                <form method="POST" action="{{ route('customer.notifikasi.read-all') }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-white" onclick="return confirm('Tandai semua notifikasi sebagai sudah dibaca?')">
                        <i class="fa-solid fa-check-double"></i> Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="hero-big">
            <b>{{ $unreadNotifications }}</b>
            <span>notifikasi belum dibaca</span>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="stats">
        <div class="stat"><i class="i-blue fa-solid fa-bell"></i><div><small>Total notifikasi</small><b>{{ $totalNotifications }}</b></div></div>
        <div class="stat"><i class="i-warning fa-solid fa-envelope"></i><div><small>Belum dibaca</small><b>{{ $unreadNotifications }}</b></div></div>
        <div class="stat"><i class="i-success fa-solid fa-envelope-open"></i><div><small>Sudah dibaca</small><b>{{ $readNotifications }}</b></div></div>
        <div class="stat"><i class="i-danger fa-solid fa-triangle-exclamation"></i><div><small>Perlu perhatian</small><b>{{ $importantNotifications }}</b></div></div>
    </div>

    {{-- TOOLBAR --}}
    <section class="panel sect">
        <div class="toolbar">
            <form method="GET" action="{{ route('customer.notifikasi.index') }}" class="sbox">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul, isi pesan, atau nomor resi" aria-label="Cari notifikasi">
                @if($statusFilter !== 'all')<input type="hidden" name="status" value="{{ $statusFilter }}">@endif
                @if($typeFilter !== 'all')<input type="hidden" name="type" value="{{ $typeFilter }}">@endif
                <button type="submit">Cari</button>
            </form>

            <div class="seg" aria-label="Status baca">
                <a href="{{ $link(['status' => null]) }}" class="{{ $statusFilter === 'all' ? 'on' : '' }}">Semua</a>
                <a href="{{ $link(['status' => 'unread']) }}" class="{{ $statusFilter === 'unread' ? 'on' : '' }}">Belum dibaca</a>
                <a href="{{ $link(['status' => 'read']) }}" class="{{ $statusFilter === 'read' ? 'on' : '' }}">Sudah dibaca</a>
            </div>
        </div>

        <div class="cats">
            <a href="{{ $link(['type' => null]) }}" class="cat {{ $typeFilter === 'all' ? 'on' : '' }}"><i class="fa-solid fa-layer-group"></i> Semua <em>{{ $counts['all'] }}</em></a>
            <a href="{{ $link(['type' => 'shipment']) }}" class="cat {{ $typeFilter === 'shipment' ? 'on' : '' }}"><i class="fa-solid fa-box"></i> Pengiriman <em>{{ $counts['shipment'] }}</em></a>
            <a href="{{ $link(['type' => 'delay']) }}" class="cat {{ $typeFilter === 'delay' ? 'on' : '' }}"><i class="fa-solid fa-clock"></i> Keterlambatan <em>{{ $counts['delay'] }}</em></a>
            <a href="{{ $link(['type' => 'exception']) }}" class="cat {{ $typeFilter === 'exception' ? 'on' : '' }}"><i class="fa-solid fa-circle-exclamation"></i> Kendala <em>{{ $counts['exception'] }}</em></a>
        </div>
    </section>

    {{-- DAFTAR --}}
    <section class="panel sect">
        <div class="lh">
            <div>
                <h2>Daftar notifikasi</h2>
                <p>{{ $notifications->total() }} notifikasi ditemukan. Klik salah satu untuk membaca selengkapnya.</p>
            </div>
        </div>

        @forelse($grouped as $label => $group)
            <div class="day">{{ $label }}</div>

            @foreach($group as $n)
                <article class="n tone-{{ $n['tone'] }} {{ $n['read'] ? '' : 'unread' }}">
                    <a class="go" href="{{ $link(['detail' => $n['id'], 'page' => $notifications->currentPage() > 1 ? $notifications->currentPage() : null]) }}" aria-label="Buka {{ $n['title'] }}"></a>

                    <div class="n-ic"><i class="fa-solid {{ $n['icon'] }}"></i></div>

                    <div class="n-body">
                        <div class="n-top">
                            <div class="n-title">
                                @unless($n['read'])<span class="dot" title="Belum dibaca"></span>@endunless
                                {{ $n['title'] }}
                            </div>
                            <span class="n-time"><i class="fa-regular fa-clock"></i> {{ $ago($n['created_at']) }}</span>
                        </div>

                        <p class="n-msg">{{ $n['message'] }}</p>

                        <div class="n-foot">
                            <div class="tags">
                                <span class="tag cat-{{ $n['category'] }}"><i class="fa-solid {{ $catIcon[$n['category']] }}"></i> {{ $catLabel[$n['category']] }}</span>
                                @if($n['tracking'])<span class="tag"><i class="fa-solid fa-barcode"></i> {{ $n['tracking'] }}</span>@endif
                            </div>

                            <div class="acts">
                                @if(!$n['read'])
                                    <form method="POST" action="{{ route('customer.notifikasi.read', $n['id']) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="ib" title="Tandai dibaca" aria-label="Tandai dibaca"><i class="fa-solid fa-check"></i></button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('customer.notifikasi.destroy', $n['id']) }}" onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ib del" title="Hapus" aria-label="Hapus notifikasi"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        @empty
            <div class="empty">
                <i class="fa-regular fa-bell-slash"></i>
                @if($search !== '' || $statusFilter !== 'all' || $typeFilter !== 'all')
                    <strong>Tidak ada hasil</strong>
                    Tidak ada notifikasi yang cocok dengan pencarian atau filter Anda.
                    <div><a href="{{ route('customer.notifikasi.index') }}" class="btn"><i class="fa-solid fa-rotate-left"></i> Hapus filter</a></div>
                @else
                    <strong>Belum ada notifikasi</strong>
                    Kabar tentang pengiriman Anda akan muncul di sini.
                @endif
            </div>
        @endforelse

        <div class="pager">
            <span>Menampilkan {{ $notifications->firstItem() ?? 0 }} sampai {{ $notifications->lastItem() ?? 0 }} dari {{ $notifications->total() }} notifikasi</span>
            @if($notifications->hasPages())<div>{{ $notifications->links() }}</div>@endif
        </div>
    </section>

    <footer class="footer">
        © {{ date('Y') }} CargoTrace. Sistem Informasi Pelacakan Pengiriman.
    </footer>

</section>
</main>


{{-- PANEL DETAIL --}}
<div class="ov {{ $selected ? 'on' : '' }}" id="ov"></div>

<aside class="drawer {{ $selected ? 'on' : '' }}" id="drawer" role="dialog" aria-modal="true" aria-label="Detail notifikasi" aria-hidden="{{ $selected ? 'false' : 'true' }}">
    @if($selected)
        @php
            $d = $selected['data'];
            $ship = $selected['shipment'];

            $sName = $ship?->shipment_name ?? ($d['shipment_name'] ?? $d['item_name'] ?? null);
            $sCarrier = $ship?->carrier ?? ($d['carrier'] ?? null);
            $sStatusKey = strtoupper($ship?->status ?? ($d['status'] ?? $d['shipment_status'] ?? ''));
            $sStatus = $shipStatus[$sStatusKey] ?? null;
            $sLocation = $d['location'] ?? null;
            $sCause = $d['cause'] ?? $d['reason'] ?? null;
            $origin = $ship?->origin;
            $dest = $ship?->destination;
        @endphp

        <button type="button" class="d-close" id="dClose" aria-label="Tutup detail"><i class="fa-solid fa-xmark"></i></button>

        <div class="d-scroll">
            <header class="dh tone-{{ $selected['tone'] }}">
                <div class="big"><i class="fa-solid {{ $selected['icon'] }}"></i></div>
                <h2>{{ $selected['title'] }}</h2>
                <small><i class="fa-regular fa-clock"></i> {{ $fmt($selected['created_at']) }} &middot; {{ $catLabel[$selected['category']] }}</small>
            </header>

            <div class="dbody">
                <section class="card">
                    <h4><i class="fa-solid fa-message"></i> Isi pesan</h4>
                    <p class="msg">{{ $selected['message'] }}</p>
                </section>

                @if($selected['tracking'])
                    <section class="card">
                        <h4><i class="fa-solid fa-box"></i> Pengiriman terkait</h4>

                        <div class="kv">
                            {!! $kv('Nomor resi', $selected['tracking']) !!}
                            {!! $kv('Nama barang', $sName) !!}
                            {!! $kv('Perusahaan', $sCarrier) !!}
                            <div>
                                <label>Status terbaru</label>
                                @if($sStatus)<span class="st {{ $sStatus[1] }}">{{ $sStatus[0] }}</span>@else<strong class="na">Belum tersedia</strong>@endif
                            </div>
                        </div>

                        @if($origin || $dest)
                            <div class="route">
                                <div class="pt"><label>Asal</label><strong>{{ $origin ?: '-' }}</strong></div>
                                <div class="mid"><i class="fa-solid fa-arrow-right"></i></div>
                                <div class="pt r"><label>Tujuan</label><strong>{{ $dest ?: '-' }}</strong></div>
                            </div>
                        @endif
                    </section>
                @endif

                <section class="card">
                    <h4><i class="fa-solid fa-circle-info"></i> Keterangan kejadian</h4>
                    <div class="kv">
                        {!! $kv('Lokasi kejadian', $sLocation) !!}
                        {!! $kv('Waktu notifikasi', $fmt($selected['created_at'])) !!}
                        {!! $kv('Penyebab kendala', $sCause, true) !!}
                        {!! $kv('Status baca', $selected['read'] ? 'Sudah dibaca' : 'Belum dibaca') !!}
                    </div>
                </section>

                <div style="height:8px"></div>
            </div>
        </div>

        <div class="d-foot">
            @if($selected['tracking'])
                <a href="{{ route('customer.pelacakan.index', ['tracking' => $selected['tracking']]) }}" class="btn"><i class="fa-solid fa-location-dot"></i> Lacak pengiriman</a>
            @endif

            <div class="row">
                @if($ship)
                    <a href="{{ route('customer.pengiriman.index', ['detail' => $ship->id]) }}" class="btn btn-light"><i class="fa-solid fa-box-open"></i> Detail pengiriman</a>
                @endif

                <form method="POST" action="{{ route('customer.notifikasi.destroy', $selected['id']) }}" onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')" style="display:flex">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-del" style="flex:1"><i class="fa-solid fa-trash"></i> Hapus</button>
                </form>
            </div>
        </div>
    @endif
</aside>

<script>
(function () {
    var drawer = document.getElementById('drawer');
    var ov = document.getElementById('ov');

    if (!drawer.classList.contains('on')) return;

    document.body.classList.add('lock');

    function close() {
        drawer.classList.remove('on');
        ov.classList.remove('on');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('lock');

        // Hapus ?detail dari alamat tanpa memuat ulang halaman.
        try {
            var u = new URL(window.location);
            u.searchParams.delete('detail');
            history.replaceState(null, '', u);
        } catch (e) {}
    }

    var btn = document.getElementById('dClose');
    if (btn) { btn.addEventListener('click', close); btn.focus(); }
    ov.addEventListener('click', close);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
})();
</script>

</body>
</html>
