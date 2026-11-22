<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard Customer | CargoTrace</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
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
.avatar{display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-weight:800;flex:none}
.s-user b{display:block;font-size:12.5px}.s-user small{font-size:10px;color:#a9c4f0}
.s-user button{width:100%;margin-top:12px;padding:9px;border:1px solid #ffffff33;border-radius:10px;background:transparent;color:#dce8ff;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.s-user button:hover{background:#ffffff1c}

/* MAIN */
.main{margin-left:272px;min-height:100vh;background:radial-gradient(circle at 100% 0,#cfe3ff 0,#0000 380px),radial-gradient(circle at 0 100%,#d7e8ff 0,#0000 340px),var(--bg)}
.content{padding:26px 32px 40px}
.topline{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:18px}
.topline h1{font:800 26px var(--h);color:var(--navy);letter-spacing:-.6px}
.topline p{margin-top:5px;color:var(--mut)}
.top-right{display:flex;align-items:center;gap:12px}
.date-box{display:flex;align-items:center;gap:9px;padding:13px 17px;background:#fff;border:1px solid var(--line);border-radius:14px;color:#3d68a8;font-weight:600;white-space:nowrap}
.date-box i{color:var(--blue)}
.bell{position:relative;display:grid;place-items:center;width:46px;height:46px;background:#fff;border:1px solid var(--line);border-radius:14px;color:#3d68a8;text-decoration:none;font-size:16px}
.bell span{position:absolute;top:10px;right:11px;width:9px;height:9px;border-radius:50%;background:#ef4444;border:2px solid #fff}

/* HERO */
.hero{position:relative;overflow:hidden;min-height:310px;padding:44px 48px 118px;border-radius:32px;color:#fff;display:flex;justify-content:space-between;gap:28px;
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 48%,#1d6fe866 100%),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1900&q=80') center/cover,linear-gradient(135deg,#061533,#1d4ed8);box-shadow:0 28px 58px #0b2a6b46}
.hero::after{content:"";position:absolute;width:460px;height:460px;right:-130px;top:-220px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.live{display:inline-flex;align-items:center;gap:8px;margin-bottom:18px;padding:7px 14px;border-radius:20px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11px;font-weight:600}
.live i{width:8px;height:8px;border-radius:50%;background:#4ade80;animation:ping 1.8s infinite}
@keyframes ping{0%{box-shadow:0 0 0 0 #4ade80aa}70%{box-shadow:0 0 0 9px #4ade8000}100%{box-shadow:0 0 0 0 #4ade8000}}
.hero h2{font:800 34px/1.22 var(--h);letter-spacing:-1px;max-width:560px}
.hero h2 em{font-style:normal;background:linear-gradient(90deg,#7dd3fc,#a5b4fc);-webkit-background-clip:text;background-clip:text;color:transparent}
.hero p{margin-top:14px;max-width:480px;line-height:1.85;color:#cfe0ff}
.track-form{display:flex;align-items:center;gap:8px;margin-top:24px;max-width:500px;padding:7px;border-radius:18px;background:#ffffff;box-shadow:0 18px 40px #02081c55}
.track-form i{padding:0 8px 0 14px;color:#8ca6c4}
.track-form input{flex:1;min-width:0;border:0;outline:0;background:transparent;font:500 13px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.track-form button{padding:13px 20px;border:0;border-radius:13px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer;white-space:nowrap}
.track-form button:hover{filter:brightness(1.1)}
.hero-side{display:flex;flex-direction:column;gap:12px;align-self:flex-start}
.hchip{display:flex;align-items:center;gap:14px;min-width:230px;padding:15px 19px;background:#ffffff1c;border:1px solid #ffffff35;border-radius:18px;backdrop-filter:blur(10px);font-size:11.5px;color:#dbe9ff}
.hchip b{font:800 26px var(--h);color:#fff;min-width:38px}

/* STAT RING */
.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin:-78px 24px 28px;position:relative;z-index:3}
.stat{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:20px;background:#ffffffee;backdrop-filter:blur(10px);border:1px solid #fff;border-radius:24px;box-shadow:0 18px 40px #0b2a6b24;transition:.25s}
.stat:hover{transform:translateY(-6px)}
.stat small{display:block;font-size:12px;color:var(--mut);font-weight:600}
.stat b{display:block;margin-top:7px;font:800 32px var(--h);color:var(--navy);line-height:1}
.stat em{display:block;margin-top:8px;font-style:normal;font-size:11px;font-weight:700}
.ring{position:relative;flex:none;width:62px;height:62px}
.ring svg{width:62px;height:62px;transform:rotate(-90deg)}
.ring circle{fill:none;stroke-width:6;stroke-linecap:round}
.ring .rb{stroke:#e6eefc}
.ring i{position:absolute;inset:0;display:grid;place-items:center;font-size:16px}
.c-blue{color:#2563eb}.c-orange{color:#f59e0b}.c-green{color:#16a34a}.c-red{color:#e5484d}

/* PANEL */
.panel{padding:26px;background:#fff;border:1px solid var(--line);border-radius:28px;box-shadow:0 10px 30px #2563eb0d}
.sect{margin-bottom:24px}
.ph{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:18px}
.ph h2{font:700 18px var(--h);color:var(--navy);letter-spacing:-.3px}
.ph p{margin-top:6px;font-size:12px;color:var(--mut)}
.ph a{display:inline-flex;align-items:center;gap:7px;padding:9px 14px;border-radius:11px;background:#e8f1ff;color:#1d4ed8;text-decoration:none;font-size:11px;font-weight:700;white-space:nowrap}
.ph a:hover{background:var(--blue);color:#fff}
.row2{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:22px;align-items:start;margin-bottom:24px}
.row2 .panel{height:100%}

/* TABEL */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:760px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:11px;font-weight:700;padding:13px 14px}
th:first-child{border-radius:12px 0 0 12px}th:last-child{border-radius:0 12px 12px 0}
td{padding:15px 14px;border-bottom:1px solid #edf3fa;color:#41608a;white-space:nowrap}
tbody tr:hover{background:#f6faff}
td.num{font-weight:800;color:var(--navy)}
.route{display:flex;align-items:center;gap:8px}.route i{color:#9db6dc;font-size:10px}
.status{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10px;font-weight:700}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.status-transit{background:#e5f1ff;color:#2876c5}
.status-delivered{background:#e8f7ef;color:#26734d}
.status-created{background:#fff4dc;color:#b77900}
.status-delayed{background:#ffe9e9;color:#c92f3a}
.status-default{background:#eef0f4;color:#667085}

/* PELACAKAN BERFOTO */
.track-layout{display:grid;grid-template-columns:330px minmax(0,1fr);gap:24px;align-items:stretch}
.track-photo{position:relative;overflow:hidden;min-height:330px;border-radius:24px;color:#fff;padding:24px;display:flex;flex-direction:column;justify-content:flex-end;
 background:linear-gradient(180deg,#06153322 5%,#061533f2 88%),url('https://images.unsplash.com/photo-1494412651409-8963ce7935a7?auto=format&fit=crop&w=900&q=75') center/cover,linear-gradient(135deg,#0a2257,#1d4ed8);box-shadow:0 18px 36px #0b2a6b30}
.track-photo small{font-size:10.5px;letter-spacing:1px;color:#9fd0ff;font-weight:700}
.track-photo .tn{font:800 22px var(--h);margin:6px 0 12px;letter-spacing:-.3px}
.track-photo .ts{display:inline-flex;align-self:flex-start;align-items:center;gap:8px;padding:8px 14px;border-radius:20px;background:#ffffff24;border:1px solid #ffffff3a;backdrop-filter:blur(8px);font-size:12px;font-weight:700}
.track-photo .tl{margin-top:14px;font-size:12px;color:#d6e4ff;line-height:1.6}
.d-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.d-item{padding:14px 16px;border-radius:16px;background:#f4f8ff;border:1px solid #e3ecfb}
.d-item label{display:block;color:#8ca6c4;font-size:10.5px;margin-bottom:6px}
.d-item strong{font-size:13px;color:var(--navy)}
.steps{display:flex;justify-content:space-between;gap:6px;margin:26px 0 6px;position:relative}
.steps::before{content:"";position:absolute;left:8%;right:8%;top:16px;height:3px;border-radius:3px;background:#e3ecfb}
.step{flex:1;text-align:center;position:relative;z-index:1}
.step .c{display:grid;place-items:center;width:34px;height:34px;margin:0 auto 9px;border-radius:50%;background:#e8eef9;color:#8ca6c4;font-size:12px;border:3px solid #fff}
.step.done .c{background:var(--blue);color:#fff;box-shadow:0 8px 16px #2563eb55}
.step.now .c{box-shadow:0 0 0 6px #2563eb26,0 8px 16px #2563eb55}
.step span{display:block;font-size:10.5px;color:#8ca6c4;font-weight:600}
.step.done span{color:var(--blue);font-weight:700}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 18px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:12px;font:700 12px 'Plus Jakarta Sans',sans-serif;transition:.2s;box-shadow:0 10px 22px #2563eb40}
.btn:hover{filter:brightness(1.1);transform:translateY(-1px)}

/* LAYANAN FOTO */
.svc-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.svc{position:relative;overflow:hidden;min-height:190px;padding:20px;border-radius:24px;color:#fff;display:flex;flex-direction:column;justify-content:flex-end;transition:.3s;box-shadow:0 16px 34px #0b2a6b2e}
.svc:hover{transform:translateY(-6px)}
.svc i{position:absolute;top:18px;right:18px;display:grid;place-items:center;width:42px;height:42px;border-radius:14px;background:#ffffff26;border:1px solid #ffffff3a;backdrop-filter:blur(8px);font-size:16px}
.svc h4{font:800 18px var(--h);letter-spacing:-.3px}
.svc p{margin-top:5px;font-size:11.5px;color:#d6e4ff;line-height:1.6}
.svc.darat{background:linear-gradient(180deg,#06153314 10%,#061533ee 92%),url('https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?auto=format&fit=crop&w=800&q=70') center/cover,linear-gradient(135deg,#0a2257,#2563eb)}
.svc.laut{background:linear-gradient(180deg,#06153314 10%,#061533ee 92%),url('https://images.unsplash.com/photo-1494412651409-8963ce7935a7?auto=format&fit=crop&w=800&q=70') center/cover,linear-gradient(135deg,#0a2257,#0ea5e9)}
.svc.udara{background:linear-gradient(180deg,#06153314 10%,#061533ee 92%),url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=800&q=70') center/cover,linear-gradient(135deg,#0a2257,#6366f1)}

/* RIWAYAT */
.h-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
.h-box{display:flex;align-items:center;gap:16px;padding:20px;border-radius:20px;background:#f4f8ff;border:1px solid #e3ecfb}
.h-box .hi{display:grid;place-items:center;flex:none;width:52px;height:52px;border-radius:16px;font-size:20px}
.h-box small{display:block;font-size:11.5px;color:var(--mut);font-weight:600}
.h-box strong{display:block;margin-top:5px;font:800 28px var(--h);color:var(--navy);line-height:1}

/* LIST ITEM */
.item{display:flex;gap:14px;padding:15px 0;border-bottom:1px solid #edf3fa}
.item:last-child{border:0;padding-bottom:0}.item:first-child{padding-top:0}
.item .ii{display:grid;place-items:center;flex:none;width:42px;height:42px;border-radius:13px;background:#fff4dc;color:#f59e0b;font-size:16px}
.item .ii.blue{background:#e8f1ff;color:var(--blue)}
.item strong{display:block;font-size:13px;color:var(--navy);margin-bottom:5px}
.item p{font-size:12px;color:var(--mut);line-height:1.65}
.item small{display:block;margin-top:6px;font-size:10.5px;color:#8ca6c4}
.sev{display:inline-block;padding:2px 9px;border-radius:12px;font-size:9.5px;font-weight:700;margin-left:4px}
.sev.low{background:#e8f7ef;color:#26734d}.sev.medium{background:#fff4dc;color:#b77900}.sev.high{background:#ffe9e9;color:#c92f3a}
.dot{display:inline-block;width:7px;height:7px;border-radius:50%;background:var(--blue);margin-right:5px}
.empty{padding:34px 12px;text-align:center;color:var(--mut)}
.empty i{display:grid;place-items:center;width:62px;height:62px;margin:0 auto 12px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:24px}
.empty.ok i{background:#e8f7ef;color:#16a34a}

/* CTA */
.cta{position:relative;overflow:hidden;display:flex;justify-content:space-between;align-items:center;gap:24px;padding:38px 44px;border-radius:30px;color:#fff;margin-bottom:24px;
 background:linear-gradient(100deg,#061533f2 0%,#0b2a6bd0 55%,#2563eb88 100%),url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1800&q=75') center/cover,linear-gradient(135deg,#061533,#1d4ed8);box-shadow:0 24px 50px #0b2a6b3c}
.cta h3{font:800 24px/1.3 var(--h);letter-spacing:-.6px;max-width:480px}
.cta p{margin-top:8px;color:#cfe0ff;max-width:440px;line-height:1.75}
.cta .btn{background:#fff;color:#1d4ed8;box-shadow:0 14px 28px #02081c44;padding:15px 24px;font-size:13px}

.footer{text-align:center;color:#8ca6c4;font-size:11px;margin-top:30px}

/* ANIMASI MASUK */
.stat,.panel,.svc,.cta{animation:rise .6s both}
.stats .stat:nth-child(2){animation-delay:.06s}.stats .stat:nth-child(3){animation-delay:.12s}.stats .stat:nth-child(4){animation-delay:.18s}
@keyframes rise{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}

/* RESPONSIVE */
@media(max-width:1350px){.hero-side{display:none}.stats{grid-template-columns:repeat(2,minmax(0,1fr));margin-top:-60px}}
@media(max-width:1100px){.row2,.track-layout{grid-template-columns:1fr}.track-photo{min-height:240px}.content{padding:20px}.svc-grid,.h-grid{grid-template-columns:1fr}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .topline{flex-direction:column;align-items:flex-start}.date-box{display:none}
 .hero{padding:30px 22px 100px}.hero h2{font-size:24px}.stats{margin:-70px 6px 22px}.panel{padding:18px}
 .cta{flex-direction:column;align-items:flex-start;padding:28px 24px}.d-grid{grid-template-columns:1fr}.steps{flex-wrap:wrap;gap:14px}.steps::before{display:none}
}
@media(max-width:560px){.stats{grid-template-columns:1fr}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>

<body>

@php
    $statusLabels = [
        'CREATED' => 'Dibuat',
        'BOOKED' => 'Dipesan',
        'PICKED_UP' => 'Telah Dijemput',
        'IN_TRANSIT' => 'Dalam Perjalanan',
        'DELIVERED' => 'Terkirim',
        'DELAYED' => 'Terlambat',
    ];

    $getStatusClass = function ($status) {
        return match (strtoupper($status ?? '')) {
            'IN_TRANSIT', 'PICKED_UP' => 'status-transit',
            'DELIVERED' => 'status-delivered',
            'CREATED', 'BOOKED' => 'status-created',
            'DELAYED' => 'status-delayed',
            default => 'status-default',
        };
    };

    $getStatusLabel = function ($status) use ($statusLabels) {
        return $statusLabels[strtoupper($status ?? '')] ?? ($status ?: 'Belum ada status');
    };

    $latestTracking = $selectedShipment
        ? $selectedShipment->trackingEvents->sortBy('event_time')->last()
        : null;

    $historyCreated = $shipments->filter(fn($s) =>
        in_array(strtoupper($s->status ?? ''), ['CREATED', 'BOOKED'])
    )->count();

    $historyTransit = $inTransit;
    $historyDelivered = $delivered;

    // Persentase untuk ring pada kartu statistik
    $base = max((int) $totalShipments, 1);
    $pctTransit = round($inTransit / $base * 100);
    $pctDelivered = round($delivered / $base * 100);
    $pctDelayed = round($delayed / $base * 100);
    $circ = 150.8; // keliling lingkaran r=24

    // Tahapan pada ringkasan pelacakan
    $statusOrder = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];
    $currentStatus = $selectedShipment
        ? strtoupper($latestTracking->status ?? $selectedShipment->status ?? '')
        : '';
    $currentPos = array_search($currentStatus, $statusOrder);
    $currentPos = $currentPos === false ? -1 : $currentPos;

    // Salam sesuai waktu
    $hour = now()->hour;
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
@endphp

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="brand">
        <div class="brand-logo"><i class="fa-solid fa-route"></i></div>
        <div class="brand-name">CargoTrace</div>
    </div>
    <div class="brand-sub">LOGISTICS &amp; SHIPMENT TRACKING</div>

    <div class="s-wrap">
        <div class="s-title">MENU CUSTOMER</div>
        <nav class="s-menu">
            <a href="{{ route('customer.dashboard') }}" class="s-link {{ request()->is('customer/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-border-all"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ url('/customer/pengiriman') }}" class="s-link {{ request()->is('customer/pengiriman*') ? 'active' : '' }}">
                <i class="fa-solid fa-box"></i>
                <span>Pengiriman Saya</span>
            </a>

            <a href="{{ url('/customer/pelacakan') }}" class="s-link {{ request()->is('customer/pelacakan*') ? 'active' : '' }}">
                <i class="fa-solid fa-location-dot"></i>
                <span>Pelacakan Pengiriman</span>
            </a>

            <a href="{{ url('/customer/notifikasi') }}" class="s-link {{ request()->is('customer/notifikasi*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i>
                <span>Notifikasi</span>
                @if($notificationUnread > 0)<em class="bdg">{{ $notificationUnread }}</em>@endif
            </a>

            <a href="{{ url('/customer/profil') }}" class="s-link {{ request()->is('customer/profil*') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i>
                <span>Profil Saya</span>
            </a>
        </nav>
    </div>

    <div class="s-user">
        <div class="who">
            <div class="avatar">{{ strtoupper(substr($user->name ?? 'C', 0, 1)) }}</div>
            <div><b>{{ $user->name }}</b><small>Customer</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </form>
    </div>
</aside>

<main class="main">
<section class="content">

    {{-- TOP --}}
    <div class="topline">
        <div>
            <h1>Dashboard</h1>
            <p>Selamat datang di halaman Customer CargoTrace.</p>
        </div>

        <div class="top-right">
            <div class="date-box"><i class="fa-regular fa-calendar"></i> {{ now()->translatedFormat('d M Y') }}</div>
            <a href="{{ url('/customer/notifikasi') }}" class="bell" aria-label="Notifikasi">
                <i class="fa-regular fa-bell"></i>
                @if($notificationUnread > 0)<span></span>@endif
            </a>
        </div>
    </div>

    {{-- HERO --}}
    <div class="hero">
        <div>
            <div class="live"><i></i> Pelacakan real-time aktif</div>
            <h2>{{ $greeting }}, {{ \Illuminate\Support\Str::of($user->name)->explode(' ')->first() }}! Pantau barang Anda <em>dengan mudah.</em></h2>
            <p>Lihat posisi terakhir, estimasi tiba, dan setiap pembaruan perjalanan pengiriman Anda dalam satu layar.</p>

            <form action="{{ url('/customer/pelacakan') }}" method="GET" class="track-form">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="tracking" placeholder="Masukkan nomor resi, contoh CT-2026-001" aria-label="Nomor resi">
                <button type="submit">Lacak Sekarang</button>
            </form>
        </div>

        <div class="hero-side">
            <div class="hchip"><b>{{ $inTransit }}</b> pengiriman<br>sedang dalam perjalanan</div>
            <div class="hchip"><b>{{ $notificationUnread }}</b> notifikasi<br>belum dibaca</div>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="stats">
        <div class="stat">
            <div>
                <small>Total Pengiriman</small>
                <b>{{ $totalShipments }}</b>
                <em class="c-blue">Seluruh pengiriman Anda</em>
            </div>
            <div class="ring">
                <svg viewBox="0 0 62 62"><circle class="rb" cx="31" cy="31" r="24"/><circle cx="31" cy="31" r="24" stroke="#2563eb" stroke-dasharray="{{ $circ }} {{ $circ }}"/></svg>
                <i class="fa-solid fa-boxes-stacked c-blue"></i>
            </div>
        </div>

        <div class="stat">
            <div>
                <small>Dalam Perjalanan</small>
                <b>{{ $inTransit }}</b>
                <em class="c-orange">{{ $pctTransit }}% sedang dijalan</em>
            </div>
            <div class="ring">
                <svg viewBox="0 0 62 62"><circle class="rb" cx="31" cy="31" r="24"/><circle cx="31" cy="31" r="24" stroke="#f59e0b" stroke-dasharray="{{ $pctTransit * $circ / 100 }} {{ $circ }}"/></svg>
                <i class="fa-solid fa-truck-fast c-orange"></i>
            </div>
        </div>

        <div class="stat">
            <div>
                <small>Terkirim</small>
                <b>{{ $delivered }}</b>
                <em class="c-green">{{ $pctDelivered }}% selesai</em>
            </div>
            <div class="ring">
                <svg viewBox="0 0 62 62"><circle class="rb" cx="31" cy="31" r="24"/><circle cx="31" cy="31" r="24" stroke="#16a34a" stroke-dasharray="{{ $pctDelivered * $circ / 100 }} {{ $circ }}"/></svg>
                <i class="fa-solid fa-circle-check c-green"></i>
            </div>
        </div>

        <div class="stat">
            <div>
                <small>Terlambat / Bermasalah</small>
                <b>{{ $delayed }}</b>
                <em class="c-red">{{ $pctDelayed }}% perlu perhatian</em>
            </div>
            <div class="ring">
                <svg viewBox="0 0 62 62"><circle class="rb" cx="31" cy="31" r="24"/><circle cx="31" cy="31" r="24" stroke="#e5484d" stroke-dasharray="{{ $pctDelayed * $circ / 100 }} {{ $circ }}"/></svg>
                <i class="fa-solid fa-triangle-exclamation c-red"></i>
            </div>
        </div>
    </div>

    {{-- PENGIRIMAN TERBARU --}}
    <section class="panel sect">
        <div class="ph">
            <div><h2>Pengiriman Terbaru</h2><p>Aktivitas pengiriman terakhir pada akun Anda.</p></div>
            <a href="{{ url('/customer/pengiriman') }}">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No. Resi</th>
                        <th>Nama Barang</th>
                        <th>Kurir</th>
                        <th>Rute</th>
                        <th>ETA</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentShipments as $shipment)
                        <tr>
                            <td class="num">{{ $shipment->shipment_number }}</td>
                            <td>{{ $shipment->shipment_name }}</td>
                            <td>{{ $shipment->carrier ?: '-' }}</td>
                            <td>
                                <div class="route">
                                    {{ $shipment->origin ?: '-' }}
                                    <i class="fa-solid fa-arrow-right"></i>
                                    {{ $shipment->destination ?: '-' }}
                                </div>
                            </td>
                            <td>
                                {{ $shipment->estimated_arrival
                                    ? \Carbon\Carbon::parse($shipment->estimated_arrival)->format('d M Y')
                                    : '-' }}
                            </td>
                            <td><span class="status {{ $getStatusClass($shipment->status) }}">{{ $getStatusLabel($shipment->status) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty">
                                    <i class="fa-solid fa-box-open"></i>
                                    Belum ada data pengiriman untuk akun Anda.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- RINGKASAN PELACAKAN --}}
    <section class="panel sect">
        <div class="ph">
            <div><h2>Ringkasan Pelacakan Terakhir</h2><p>Posisi dan tahapan terbaru dari pengiriman Anda.</p></div>
            <a href="{{ url('/customer/pelacakan') }}">Buka Pelacakan <i class="fa-solid fa-arrow-right"></i></a>
        </div>

        @if($selectedShipment)
            <div class="track-layout">
                <div class="track-photo">
                    <small>NOMOR RESI</small>
                    <div class="tn">{{ $selectedShipment->shipment_number }}</div>
                    <div class="ts"><i class="fa-solid fa-circle" style="font-size:7px;color:#4ade80"></i> {{ $getStatusLabel($latestTracking->status ?? $selectedShipment->status) }}</div>
                    <div class="tl"><i class="fa-solid fa-location-dot"></i> Lokasi terakhir: {{ $latestTracking->location ?? 'Belum ada informasi lokasi' }}</div>
                </div>

                <div>
                    <div class="d-grid">
                        <div class="d-item"><label>Asal Pengiriman</label><strong>{{ $selectedShipment->origin ?: '-' }}</strong></div>
                        <div class="d-item"><label>Tujuan Pengiriman</label><strong>{{ $selectedShipment->destination ?: '-' }}</strong></div>
                        <div class="d-item">
                            <label>Waktu Pembaruan</label>
                            <strong>
                                {{ $latestTracking && $latestTracking->event_time
                                    ? \Carbon\Carbon::parse($latestTracking->event_time)->format('d M Y, H:i')
                                    : 'Belum tersedia' }}
                            </strong>
                        </div>
                        <div class="d-item">
                            <label>Estimasi Tiba</label>
                            <strong>
                                {{ $selectedShipment->estimated_arrival
                                    ? \Carbon\Carbon::parse($selectedShipment->estimated_arrival)->format('d M Y')
                                    : 'Belum tersedia' }}
                            </strong>
                        </div>
                    </div>

                    <div class="steps">
                        @foreach($statusOrder as $index => $step)
                            <div class="step {{ $index <= $currentPos ? 'done' : '' }} {{ $index === $currentPos ? 'now' : '' }}">
                                <div class="c">
                                    @if($index <= $currentPos)<i class="fa-solid fa-check"></i>@else{{ $index + 1 }}@endif
                                </div>
                                <span>{{ $statusLabels[$step] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top:20px">
                        <a href="{{ url('/customer/pelacakan?tracking=' . urlencode($selectedShipment->shipment_number)) }}" class="btn">
                            Lihat Detail Pelacakan <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="empty">
                <i class="fa-solid fa-location-crosshairs"></i>
                Belum ada pengiriman yang dapat ditampilkan.
            </div>
        @endif
    </section>

    {{-- LAYANAN --}}
    <section class="sect">
        <div class="svc-grid">
            <div class="svc darat">
                <i class="fa-solid fa-truck"></i>
                <h4>Jalur Darat</h4>
                <p>Pengiriman antarkota yang cepat dan terjangkau.</p>
            </div>
            <div class="svc laut">
                <i class="fa-solid fa-ship"></i>
                <h4>Jalur Laut</h4>
                <p>Muatan besar dengan jangkauan antarpulau.</p>
            </div>
            <div class="svc udara">
                <i class="fa-solid fa-plane"></i>
                <h4>Jalur Udara</h4>
                <p>Waktu tiba tercepat untuk barang prioritas.</p>
            </div>
        </div>
    </section>

    {{-- RIWAYAT --}}
    <section class="panel sect">
        <div class="ph"><div><h2>Riwayat Pengiriman</h2><p>Gambaran jumlah pengiriman berdasarkan tahapan.</p></div></div>

        <div class="h-grid">
            <div class="h-box">
                <div class="hi" style="background:#fff4dc;color:#f59e0b"><i class="fa-solid fa-file-circle-plus"></i></div>
                <div><small>Dibuat / Dipesan</small><strong>{{ $historyCreated }}</strong></div>
            </div>
            <div class="h-box">
                <div class="hi" style="background:#e5f1ff;color:#2563eb"><i class="fa-solid fa-truck-fast"></i></div>
                <div><small>Dalam Perjalanan</small><strong>{{ $historyTransit }}</strong></div>
            </div>
            <div class="h-box">
                <div class="hi" style="background:#e8f7ef;color:#16a34a"><i class="fa-solid fa-circle-check"></i></div>
                <div><small>Terkirim</small><strong>{{ $historyDelivered }}</strong></div>
            </div>
        </div>
    </section>

    {{-- PENGECUALIAN + NOTIFIKASI --}}
    <div class="row2">

        <section class="panel">
            <div class="ph">
                <div><h2>Pengecualian Pengiriman</h2><p>Kendala yang terjadi pada pengiriman Anda.</p></div>
                <a href="{{ url('/customer/pelacakan') }}">Lihat Detail</a>
            </div>

            @forelse($recentExceptions as $exception)
                <div class="item">
                    <div class="ii"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <strong>{{ str_replace('_', ' ', $exception->type ?? 'Pengecualian') }}</strong>
                        <p>{{ $exception->description ?: 'Terdapat pengecualian pada pengiriman.' }}</p>
                        <small>
                            Status: {{ $exception->status ?? '-' }}
                            · Tingkat:
                            <span class="sev {{ strtolower($exception->severity ?? '') }}">{{ $exception->severity ?? '-' }}</span>
                        </small>
                    </div>
                </div>
            @empty
                <div class="empty ok">
                    <i class="fa-solid fa-circle-check"></i>
                    Tidak ada pengecualian pengiriman.
                </div>
            @endforelse
        </section>

        <section class="panel">
            <div class="ph">
                <div><h2>Notifikasi Terbaru</h2><p>Kabar terbaru dari pengiriman Anda.</p></div>
                <a href="{{ url('/customer/notifikasi') }}">Lihat Semua</a>
            </div>

            @forelse($notifications as $notification)
                @php
                    $notificationData = $notification->data ?? [];
                    $notificationTitle = $notificationData['title']
                        ?? $notificationData['message']
                        ?? 'Pembaruan Pengiriman';

                    $notificationMessage = $notificationData['body']
                        ?? $notificationData['description']
                        ?? $notificationData['message']
                        ?? 'Ada pembaruan pada informasi pengiriman Anda.';
                @endphp

                <div class="item">
                    <div class="ii blue"><i class="fa-solid fa-bell"></i></div>
                    <div>
                        <strong>{{ $notificationTitle }}</strong>
                        <p>{{ $notificationMessage }}</p>
                        <small>
                            {{ $notification->created_at ? $notification->created_at->format('d M Y, H:i') : '-' }}
                            ·
                            @if(!$notification->read_at)<span class="dot"></span>Belum dibaca @else Sudah dibaca @endif
                        </small>
                    </div>
                </div>
            @empty
                <div class="empty">
                    <i class="fa-regular fa-bell-slash"></i>
                    Belum ada notifikasi.
                </div>
            @endforelse
        </section>

    </div>

    {{-- RINGKASAN NOTIFIKASI --}}
    <div class="stats" style="margin:0 0 24px">
        <div class="stat">
            <div><small>Total Notifikasi</small><b>{{ $notificationTotal }}</b></div>
            <div class="h-box" style="padding:0;border:0;background:none"><div class="hi" style="background:#e8f1ff;color:#2563eb"><i class="fa-solid fa-bell"></i></div></div>
        </div>
        <div class="stat">
            <div><small>Belum Dibaca</small><b>{{ $notificationUnread }}</b></div>
            <div class="h-box" style="padding:0;border:0;background:none"><div class="hi" style="background:#fff4dc;color:#f59e0b"><i class="fa-solid fa-envelope"></i></div></div>
        </div>
        <div class="stat">
            <div><small>Sudah Dibaca</small><b>{{ $notificationRead }}</b></div>
            <div class="h-box" style="padding:0;border:0;background:none"><div class="hi" style="background:#e8f7ef;color:#16a34a"><i class="fa-solid fa-envelope-open"></i></div></div>
        </div>
        <div class="stat">
            <div><small>Total Pengiriman</small><b>{{ $totalShipments }}</b></div>
            <div class="h-box" style="padding:0;border:0;background:none"><div class="hi" style="background:#e8f1ff;color:#2563eb"><i class="fa-solid fa-box"></i></div></div>
        </div>
    </div>

    {{-- CTA --}}
    <div class="cta">
        <div>
            <h3>Ingin tahu posisi barang Anda saat ini?</h3>
            <p>Masukkan nomor resi dan lihat perjalanan pengiriman Anda secara lengkap, dari asal sampai tujuan.</p>
        </div>
        <a href="{{ url('/customer/pelacakan') }}" class="btn"><i class="fa-solid fa-location-dot"></i> Lacak Pengiriman</a>
    </div>

    <footer class="footer">
        © {{ date('Y') }} CargoTrace. Sistem Informasi Pelacakan Pengiriman.
    </footer>

</section>
</main>

</body>
</html>
