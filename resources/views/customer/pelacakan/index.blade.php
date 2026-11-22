<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pelacakan Pengiriman | CargoTrace</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<style>
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
:root{--navy:#071633;--blue:#2457f5;--sky:#38bdf8;--ink:#17306a;--mut:#6b7fa3;--line:#e1e9f7;--bg:#eef3fb;--h:'Sora',sans-serif}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--ink);font-size:13px}
a{color:inherit}
button{font-family:inherit}
:focus-visible{outline:2px solid var(--blue);outline-offset:2px}

/* SIDEBAR */
.sidebar{position:fixed;inset:0 auto 0 0;width:272px;padding:26px 18px 18px;display:flex;flex-direction:column;background:linear-gradient(180deg,#071633 0%,#0a2257 65%,#0f3a8f 100%);color:#fff;z-index:1000;overflow-y:auto}
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
.topline{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:18px}
.topline h1{font:800 26px var(--h);color:var(--navy);letter-spacing:-.6px}
.topline p{margin-top:5px;color:var(--mut)}
.bell{position:relative;display:grid;place-items:center;width:46px;height:46px;background:#fff;border:1px solid var(--line);border-radius:14px;color:#3d68a8;text-decoration:none;font-size:16px}
.bell span{position:absolute;top:10px;right:11px;width:9px;height:9px;border-radius:50%;background:#ef4444;border:2px solid #fff}

/* HERO PENCARIAN */
.hero{position:relative;overflow:hidden;padding:44px 48px;border-radius:32px;color:#fff;margin-bottom:24px;
 background:linear-gradient(100deg,#071633f5 0%,#0b2a6bdd 52%,#1d6fe866 100%),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1900&q=75') center/cover,linear-gradient(135deg,#071633,#1d4ed8);box-shadow:0 28px 58px #0b2a6b46}
.hero::after{content:"";position:absolute;width:460px;height:460px;right:-130px;top:-220px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.hero h2{font:800 31px/1.22 var(--h);letter-spacing:-1px;max-width:600px}
.hero p{margin-top:12px;max-width:500px;line-height:1.85;color:#cfe0ff}
.search{display:flex;align-items:center;gap:8px;margin-top:24px;max-width:620px;padding:8px;border-radius:20px;background:#fff;box-shadow:0 18px 40px #02081c55}
.search>i{padding:0 8px 0 14px;color:#8ca6c4}
.search input{flex:1;min-width:0;height:44px;border:0;outline:0;background:transparent;font:600 14px 'Plus Jakarta Sans',sans-serif;color:var(--navy)}
.search input::placeholder{font-weight:500;color:#9db0cc}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 20px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:14px;font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.2s;white-space:nowrap}
.btn:hover{filter:brightness(1.1)}
.btn-light{background:#e8f1ff;color:#1d4ed8}
.btn-ghost{background:#ffffff1f;border:1px solid #ffffff3a;color:#fff}
.chips{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:18px}
.chips>span{font-size:11.5px;color:#a9c4f0;margin-right:4px}
.chip{display:inline-flex;align-items:center;gap:7px;padding:8px 13px;border-radius:20px;background:#ffffff1c;border:1px solid #ffffff33;color:#fff;text-decoration:none;font-size:11.5px;font-weight:600;backdrop-filter:blur(8px);transition:.2s}
.chip:hover{background:#ffffff38}
.chip i{width:7px;height:7px;border-radius:50%;background:#7dd3fc}
.chip.t-delivered i{background:#4ade80}.chip.t-delayed i{background:#fb7185}.chip.t-created i{background:#fbbf24}

.alert{display:flex;gap:12px;padding:16px 18px;border-radius:18px;margin-bottom:20px;line-height:1.7;font-size:12px}
.alert>i{margin-top:3px}
.alert-error{color:#b42332;background:#fff0f1;border:1px solid #ffd9dc}
.alert-info{color:#2c4f94;background:#edf3ff;border:1px solid #dce7ff}

/* STATUS */
.status{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10.5px;font-weight:700;white-space:nowrap}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.status-transit{background:#e5f1ff;color:#2876c5}
.status-delivered{background:#e8f7ef;color:#26734d}
.status-created{background:#fff4dc;color:#b77900}
.status-delayed{background:#ffe9e9;color:#c92f3a}
.status-default{background:#eef0f4;color:#667085}

/* KARTU HASIL */
.result{position:relative;overflow:hidden;border-radius:30px;color:#fff;padding:30px 34px 26px;margin-bottom:20px;box-shadow:0 24px 50px #0b2a6b38}
.result.darat{background:linear-gradient(180deg,#071633d8,#071633fa),url('https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?auto=format&fit=crop&w=1600&q=70') center/cover,#0a2257}
.result.laut{background:linear-gradient(180deg,#071633d8,#071633fa),url('https://images.unsplash.com/photo-1494412651409-8963ce7935a7?auto=format&fit=crop&w=1600&q=70') center/cover,#0a2257}
.result.udara{background:linear-gradient(180deg,#071633d8,#071633fa),url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1600&q=70') center/cover,#0a2257}
.r-top{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap}
.r-top small{display:block;font-size:11px;color:#9fd0ff;font-weight:600;margin-bottom:6px}
.r-num{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.r-num h2{font:800 28px var(--h);letter-spacing:-.8px}
.copy{display:inline-flex;align-items:center;gap:7px;padding:7px 12px;border:1px solid #ffffff3a;border-radius:10px;background:#ffffff1c;color:#fff;font-size:11px;font-weight:600;cursor:pointer}
.copy:hover{background:#ffffff36}
.r-sub{margin-top:8px;color:#cfe0ff;font-size:12.5px}
.result .status{background:#ffffffee}

/* garis perjalanan */
.lane{position:relative;margin:40px 8px 8px}
.lane-ends{display:flex;justify-content:space-between;gap:14px}
.lane-ends>div{max-width:44%}
.lane-ends small{display:flex;align-items:center;gap:7px;font-size:11px;color:#9fd0ff}
.lane-ends strong{display:block;margin-top:6px;font:700 17px var(--h);letter-spacing:-.3px;overflow-wrap:anywhere}
.lane-ends .r{text-align:right}.lane-ends .r small{justify-content:flex-end}
.track{position:relative;height:6px;margin:30px 14px 0;border-radius:6px;background:#ffffff26}
.track .fill{position:absolute;left:0;top:0;bottom:0;border-radius:6px;background:linear-gradient(90deg,#38bdf8,#7aa2ff);width:0;transition:width 1.4s cubic-bezier(.2,.8,.2,1)}
.track .dot{position:absolute;top:50%;width:13px;height:13px;border-radius:50%;background:#fff;border:3px solid #38bdf8;transform:translate(-50%,-50%)}
.track .dot.end{border-color:#fb7185}
.vehicle{position:absolute;top:-25px;left:0;display:grid;place-items:center;width:44px;height:44px;margin-left:-22px;border-radius:50%;background:#fff;color:var(--blue);font-size:17px;box-shadow:0 10px 24px #02081c66;transition:left 1.4s cubic-bezier(.2,.8,.2,1)}
.vehicle::after{content:"";position:absolute;inset:-6px;border-radius:50%;border:2px solid #7dd3fc99;animation:pulse 2s infinite}
@keyframes pulse{0%{transform:scale(.85);opacity:1}100%{transform:scale(1.35);opacity:0}}
.pct{position:absolute;right:0;top:20px;font-size:11px;color:#9fd0ff;font-weight:600}

.steps{display:grid;grid-template-columns:repeat(5,1fr);gap:6px;margin-top:34px;padding-top:22px;border-top:1px solid #ffffff22}
.step{text-align:center}
.step i{display:grid;place-items:center;width:30px;height:30px;margin:0 auto 8px;border-radius:50%;background:#ffffff1c;border:2px solid #ffffff33;color:#ffffff88;font-size:11px;font-style:normal}
.step.done i{background:#38bdf8;border-color:#38bdf8;color:#071633}
.step.cur i{background:#fff;border-color:#fff;color:var(--blue);box-shadow:0 0 0 5px #ffffff30}
.step small{display:block;font-size:10.5px;line-height:1.4;color:#a9c4f0}
.step.done small,.step.cur small{color:#fff;font-weight:700}

/* RINGKASAN */
.kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:20px}
.kpi{display:flex;gap:14px;align-items:center;padding:18px;background:#fff;border:1px solid var(--line);border-radius:22px;box-shadow:0 10px 30px #2457f50d;min-width:0}
.kpi>i{flex:none;display:grid;place-items:center;width:46px;height:46px;border-radius:15px;background:#e8f1ff;color:var(--blue);font-size:17px}
.kpi small{display:block;font-size:11px;color:var(--mut);font-weight:600}
.kpi b{display:block;margin-top:5px;font:700 14px var(--h);color:var(--navy);overflow-wrap:anywhere}
.kpi.warn>i{background:#ffe9e9;color:#e5484d}.kpi.good>i{background:#e8f7ef;color:#16a34a}

/* PETA + TIMELINE */
.duo{display:grid;grid-template-columns:minmax(0,1.55fr) minmax(0,1fr);gap:20px;margin-bottom:20px;align-items:stretch}
.panel{padding:24px;background:#fff;border:1px solid var(--line);border-radius:28px;box-shadow:0 10px 30px #2457f50d;min-width:0}
.ph{margin-bottom:16px}
.ph h3{font:700 16px var(--h);color:var(--navy);letter-spacing:-.3px;display:flex;align-items:center;gap:10px}
.ph h3 i{display:grid;place-items:center;width:32px;height:32px;border-radius:11px;background:#e8f1ff;color:var(--blue);font-size:13px}
.ph p{margin-top:7px;font-size:12px;color:var(--mut)}
.mapbox{position:relative;border-radius:20px;overflow:hidden}
#shipment-map{height:480px;width:100%;background:#e6edf8;z-index:1}
.map-empty{position:absolute;inset:0;z-index:500;display:grid;place-items:center;background:#eef3fbd9;backdrop-filter:blur(3px);text-align:center;padding:20px}
.map-empty div{max-width:260px;color:var(--mut);line-height:1.7}
.map-empty i{display:grid;place-items:center;width:58px;height:58px;margin:0 auto 12px;border-radius:50%;background:#fff;color:var(--blue);font-size:22px;box-shadow:0 8px 20px #2457f522}
.map-empty b{display:block;color:var(--navy);font:700 14px var(--h);margin-bottom:4px}
.map-note{position:absolute;left:12px;right:12px;bottom:12px;z-index:500;padding:10px 14px;border-radius:12px;background:#ffffffee;border:1px solid var(--line);color:#3d5c91;font-size:11.5px;line-height:1.55;box-shadow:0 8px 20px #0b2a6b22}
.vmark{display:grid;place-items:center;width:42px;height:42px;border-radius:50%;background:#fff;border:3px solid #2457f5;color:#2457f5;font-size:16px;box-shadow:0 8px 18px #0b2a6b55}
.pin{display:grid;place-items:center;width:28px;height:28px;border-radius:50%;background:#2457f5;border:3px solid #fff;color:#fff;font:700 11px 'Plus Jakarta Sans',sans-serif;box-shadow:0 6px 14px #0b2a6b55}
.pin.first{background:#19a66a}
.pin.last{background:#e74d59;width:34px;height:34px;box-shadow:0 0 0 0 #e74d5988;animation:pin 2s infinite}
@keyframes pin{0%{box-shadow:0 0 0 0 #e74d5999}100%{box-shadow:0 0 0 16px #e74d5900}}
.leaflet-container{font-family:'Plus Jakarta Sans',sans-serif}
.pop b{display:block;font:700 13px 'Sora',sans-serif;color:#071633}
.pop p{margin-top:5px;font-size:11.5px;color:#4b628a;line-height:1.5}

.tl-wrap{max-height:552px;overflow-y:auto;padding:4px 6px 4px 2px;margin-right:-6px}
.tl{position:relative}
.tl-i{position:relative;display:flex;gap:14px;padding:12px;margin-bottom:4px;border-radius:16px;transition:.2s}
.tl-i.has-geo{cursor:pointer}
.tl-i.has-geo:hover{background:#f1f6ff}
.tl-i:not(:last-child)::before{content:"";position:absolute;left:22px;top:38px;bottom:-14px;width:2px;background:#dbe6f8}
.tl-i>i{flex:none;display:grid;place-items:center;width:22px;height:22px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:9px;z-index:1}
.tl-i.latest>i{background:var(--blue);color:#fff;box-shadow:0 0 0 5px #2457f522}
.tl-i b{display:flex;align-items:center;gap:8px;flex-wrap:wrap;font-size:13px;color:var(--navy)}
.tl-i b em{font-style:normal;padding:2px 8px;border-radius:10px;background:#e8f1ff;color:var(--blue);font-size:10px;font-weight:700}
.tl-i p{margin-top:5px;font-size:12px;color:var(--mut);line-height:1.6}
.tl-i small{display:block;margin-top:6px;font-size:11px;color:#9fb0c9}
.tl-i .geo{margin-top:7px;display:inline-flex;align-items:center;gap:6px;font-size:11px;color:var(--blue);font-weight:700}

/* ETA */
.eta-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.eta-box{padding:18px;border-radius:18px;background:#f4f8ff;border:1px solid #e3ecfb}
.eta-box small{display:block;font-size:11px;color:#8ca6c4}
.eta-box strong{display:block;margin-top:8px;font:700 17px var(--h);color:var(--navy);overflow-wrap:anywhere}
.box-alert{margin-top:16px;padding:16px 18px;border-radius:18px;line-height:1.7;font-size:12px}
.box-alert.err{background:#fff0f1;border:1px solid #ffd9dc;color:#b42332}
.box-alert.ok{background:#edf3ff;border:1px solid #dce7ff;color:#2c4f94}
.sev{display:inline-block;padding:4px 10px;border-radius:20px;font-size:10px;font-weight:700}
.sev-low{background:#e6f8ef;color:#15945d}.sev-medium{background:#fff5df;color:#b7791f}.sev-high{background:#ffebed;color:#d6424d}

.sect{margin-bottom:20px}
.ex{padding:18px;border:1px solid var(--line);border-radius:18px;margin-bottom:12px;border-left:4px solid #e5484d}
.ex.resolved{border-left-color:#16a34a}
.ex:last-child{margin-bottom:0}
.ex-top{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
.ex-top strong{font:700 13px var(--h);color:var(--navy)}
.ex-top div{display:flex;gap:6px;flex-wrap:wrap}
.ex dl{display:grid;grid-template-columns:repeat(2,1fr);gap:12px 18px;margin-top:14px}
.ex dt{font-size:11px;color:#8ca6c4;margin-bottom:3px}
.ex dd{font-size:12px;color:#2c4a7c;line-height:1.55}
.ex dd.full{grid-column:1/-1}
.empty{padding:30px 14px;text-align:center;color:var(--mut)}
.empty>i{display:grid;place-items:center;width:64px;height:64px;margin:0 auto 12px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:24px}
.empty.good>i{background:#e8f7ef;color:#16a34a}
.empty strong{display:block;font:700 15px var(--h);color:var(--navy);margin-bottom:5px}

.acts{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px}
.acts .btn{padding:14px 22px}
.start{padding:50px 24px}
.footer{text-align:center;color:#8ca6c4;font-size:11px;margin-top:30px}

@media(max-width:1350px){.duo{grid-template-columns:1fr}.tl-wrap{max-height:none}.kpis{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:1100px){.content{padding:20px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .hero{padding:30px 22px}.hero h2{font-size:23px}
 .search{flex-wrap:wrap}.search input{flex-basis:calc(100% - 50px)}.search .btn{flex:1}
 .result{padding:24px 20px}.r-num h2{font-size:21px}.lane-ends strong{font-size:14px}
 .steps{gap:2px}.step small{font-size:9px}
 .kpis,.eta-grid,.ex dl{grid-template-columns:1fr}.panel{padding:18px}#shipment-map{height:340px}
}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>

<body>

@php
    $currentStatus = strtoupper($latestTracking->status ?? $shipment?->status ?? '');

    $statusLabels = [
        'CREATED' => 'Dibuat',
        'BOOKED' => 'Dijadwalkan',
        'PICKED_UP' => 'Telah Dijemput',
        'IN_TRANSIT' => 'Dalam Perjalanan',
        'DELIVERED' => 'Terkirim',
        'DELAYED' => 'Terlambat',
    ];

    $stepOrder = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];
    $stepLabels = ['Pengiriman Dibuat', 'Dijadwalkan', 'Barang Dijemput', 'Dalam Perjalanan', 'Barang Diterima'];

    $statusLabel = $statusLabels[$currentStatus] ?? ($currentStatus ?: 'Belum tersedia');

    $classOf = fn ($s) => match (strtoupper($s ?? '')) {
        'PICKED_UP', 'IN_TRANSIT' => 'status-transit',
        'DELIVERED' => 'status-delivered',
        'CREATED', 'BOOKED' => 'status-created',
        'DELAYED' => 'status-delayed',
        default => 'status-default',
    };
    $statusClass = $classOf($currentStatus);

    $transport = match (strtoupper($shipment?->transport_type ?? '')) {
        'SEA' => ['laut', 'fa-ship', 'Jalur Laut'],
        'AIR' => ['udara', 'fa-plane', 'Jalur Udara'],
        default => ['darat', 'fa-truck', 'Jalur Darat'],
    };

    $formatDate = function ($value, $format = 'd M Y, H:i') {
        if (!$value) return '-';
        try { return \Carbon\Carbon::parse($value)->format($format); }
        catch (\Throwable $e) { return '-'; }
    };

    $eta = $shipment?->estimated_arrival;
    $departure = $shipment?->estimated_departure;

    $curIdx = array_search($currentStatus, $stepOrder);
    $curIdx = $curIdx === false ? -1 : $curIdx;

    $remainingLabel = null;
    if ($remainingHours !== null) {
        $remainingLabel = $remainingHours >= 48
            ? floor($remainingHours / 24) . ' hari lagi'
            : $remainingHours . ' jam lagi';
    }

    $unread = auth()->user()->unreadNotifications()->count();
    $userName = auth()->user()->name;
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
            <a href="{{ route('customer.pelacakan.index') }}" class="s-link active"><i class="fa-solid fa-location-dot"></i><span>Pelacakan Pengiriman</span></a>
            <a href="{{ url('/customer/notifikasi') }}" class="s-link">
                <i class="fa-solid fa-bell"></i><span>Notifikasi</span>
                @if($unread > 0)<em class="bdg">{{ $unread }}</em>@endif
            </a>
            <a href="{{ url('/customer/profil') }}" class="s-link"><i class="fa-solid fa-user"></i><span>Profil Saya</span></a>
        </nav>
    </div>

    <div class="s-user">
        <div class="who">
            <div class="avatar">{{ strtoupper(substr($userName ?? 'C', 0, 1)) }}</div>
            <div><b>{{ $userName }}</b><small>Customer</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </form>
    </div>
</aside>

<main class="main">
<section class="content">

    <div class="topline">
        <div>
            <h1>Pelacakan Pengiriman</h1>
            <p>Pantau perjalanan barang Anda dari asal sampai tujuan.</p>
        </div>
        <a href="{{ url('/customer/notifikasi') }}" class="bell" aria-label="Notifikasi">
            <i class="fa-regular fa-bell"></i>
            @if($unread > 0)<span></span>@endif
        </a>
    </div>

    {{-- PENCARIAN --}}
    <div class="hero">
        <h2>Di mana barang Anda sekarang?</h2>
        <p>Masukkan nomor resi untuk melihat posisi, riwayat perjalanan, dan perkiraan waktu tiba.</p>

        <form method="GET" action="{{ route('customer.pelacakan.index') }}" class="search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="tracking" value="{{ $trackingNumber }}" placeholder="Contoh: CT-2026-001" required aria-label="Nomor resi" autocomplete="off">
            <button type="submit" class="btn"><i class="fa-solid fa-location-crosshairs"></i> Lacak</button>
        </form>

        @if($recentShipments->isNotEmpty())
            <div class="chips">
                <span>Pengiriman terbaru:</span>
                @foreach($recentShipments as $recent)
                    @php
                        $rc = strtoupper($recent->status ?? '');
                        $rt = $rc === 'DELIVERED' ? 't-delivered' : ($rc === 'DELAYED' ? 't-delayed' : (in_array($rc, ['CREATED', 'BOOKED']) ? 't-created' : ''));
                    @endphp
                    <a href="{{ route('customer.pelacakan.index', ['tracking' => $recent->shipment_number]) }}" class="chip {{ $rt }}">
                        <i></i>{{ $recent->shipment_number }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>


    @if($trackingNumber !== '' && !$shipment)
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                Nomor resi <strong>{{ $trackingNumber }}</strong> tidak ditemukan di akun Anda.
                Periksa penulisannya, atau pilih salah satu pengiriman terbaru di atas.
            </div>
        </div>
    @endif


    @if($shipment)

        {{-- KARTU HASIL + GARIS PERJALANAN --}}
        <section class="result {{ $transport[0] }}">
            <div class="r-top">
                <div>
                    <small>Nomor resi</small>
                    <div class="r-num">
                        <h2 id="resi">{{ $shipment->shipment_number }}</h2>
                        <button type="button" class="copy" id="copyBtn"><i class="fa-regular fa-copy"></i> <span>Salin</span></button>
                    </div>
                    <p class="r-sub">
                        {{ $shipment->shipment_name ?: 'Barang' }}
                        &middot; {{ $shipment->carrier ?: 'Perusahaan belum tersedia' }}
                        &middot; {{ $transport[2] }}
                    </p>
                </div>
                <span class="status {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>

            <div class="lane">
                <div class="lane-ends">
                    <div>
                        <small><i class="fa-solid fa-circle-dot" style="color:#4ade80"></i> Kota asal</small>
                        <strong>{{ $shipment->origin ?: 'Belum tersedia' }}</strong>
                    </div>
                    <div class="r">
                        <small>Kota tujuan <i class="fa-solid fa-location-dot" style="color:#fb7185"></i></small>
                        <strong>{{ $shipment->destination ?: 'Belum tersedia' }}</strong>
                    </div>
                </div>

                <div class="track" data-progress="{{ $progress }}">
                    <span class="dot" style="left:0"></span>
                    <span class="dot end" style="left:100%"></span>
                    <div class="fill" id="fill"></div>
                    <div class="vehicle" id="vehicle"><i class="fa-solid {{ $transport[1] }}"></i></div>
                    <span class="pct">{{ $progress }}% perjalanan</span>
                </div>
            </div>

            <div class="steps">
                @foreach($stepOrder as $i => $st)
                    <div class="step {{ $i < $curIdx ? 'done' : ($i === $curIdx ? 'cur' : '') }}">
                        <i>@if($i < $curIdx)<span class="fa-solid fa-check"></span>@else{{ $i + 1 }}@endif</i>
                        <small>{{ $stepLabels[$i] }}</small>
                    </div>
                @endforeach
            </div>
        </section>


        {{-- RINGKASAN --}}
        <div class="kpis">
            <div class="kpi">
                <i class="fa-solid fa-location-crosshairs"></i>
                <div><small>Lokasi terakhir</small><b>{{ $lastLocation ?: 'Belum tersedia' }}</b></div>
            </div>
            <div class="kpi">
                <i class="fa-regular fa-clock"></i>
                <div><small>Pembaruan terakhir</small><b>{{ $formatDate($lastUpdated) }}</b></div>
            </div>
            <div class="kpi">
                <i class="fa-solid fa-plane-departure"></i>
                <div><small>Keberangkatan</small><b>{{ $formatDate($departure, 'd M Y') }}</b></div>
            </div>
            <div class="kpi {{ $isDelayed ? 'warn' : ($currentStatus === 'DELIVERED' ? 'good' : '') }}">
                <i class="fa-solid {{ $isDelayed ? 'fa-triangle-exclamation' : ($currentStatus === 'DELIVERED' ? 'fa-circle-check' : 'fa-flag-checkered') }}"></i>
                <div>
                    <small>Estimasi tiba</small>
                    <b>{{ $formatDate($eta, 'd M Y') }}</b>
                </div>
            </div>
        </div>


        {{-- PETA + TIMELINE --}}
        <div class="duo">
            <section class="panel">
                <div class="ph">
                    <h3><i class="fa-solid fa-map-location-dot"></i> Peta perjalanan</h3>
                    <p>Klik titik di peta atau di riwayat untuk melihat detail lokasinya.</p>
                </div>

                <div class="mapbox">
                    <div id="shipment-map"></div>

                    <div class="map-note" id="mapNote" hidden></div>
                </div>
            </section>

            <section class="panel">
                <div class="ph">
                    <h3><i class="fa-solid fa-clock-rotate-left"></i> Riwayat perjalanan</h3>
                    <p>Dari pembaruan terbaru ke yang paling awal.</p>
                </div>

                <div class="tl-wrap">
                    <div class="tl">
                        @forelse($trackingEvents->reverse()->values() as $i => $event)
                            @php
                                $es = strtoupper($event->status ?? '');
                                $hasGeo = is_numeric($event->latitude) && is_numeric($event->longitude);
                            @endphp

                            <div class="tl-i {{ $i === 0 ? 'latest' : '' }} {{ $hasGeo ? 'has-geo' : '' }}"
                                 @if($hasGeo) data-lat="{{ (float) $event->latitude }}" data-lng="{{ (float) $event->longitude }}" tabindex="0" role="button" @endif>
                                <i class="fa-solid fa-check"></i>
                                <div>
                                    <b>
                                        {{ $statusLabels[$es] ?? ($es ?: 'Pembaruan pengiriman') }}
                                        @if($i === 0)<em>Terbaru</em>@endif
                                    </b>
                                    <p><i class="fa-solid fa-location-dot"></i> {{ $event->location ?: 'Lokasi belum tersedia' }}</p>
                                    @if($event->description)<p>{{ $event->description }}</p>@endif
                                    <small><i class="fa-regular fa-clock"></i> {{ $formatDate($event->event_time) }}</small>
                                    @if($hasGeo)<span class="geo"><i class="fa-solid fa-crosshairs"></i> Lihat di peta</span>@endif
                                </div>
                            </div>
                        @empty
                            <div class="empty">
                                <i class="fa-solid fa-clock"></i>
                                <strong>Belum ada riwayat</strong>
                                Pembaruan akan muncul setelah barang mulai diproses.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>


        {{-- ETA & KETERLAMBATAN --}}
        <section class="panel sect">
            <div class="ph">
                <h3><i class="fa-solid fa-calendar-check"></i> Estimasi tiba</h3>
                <p>Perkiraan kedatangan berdasarkan jadwal yang tercatat.</p>
            </div>

            <div class="eta-grid">
                <div class="eta-box">
                    <small>Estimasi waktu tiba</small>
                    <strong>{{ $formatDate($eta, 'd M Y') }}</strong>
                </div>
                <div class="eta-box">
                    <small>Status pengiriman</small>
                    <strong>{{ $statusLabel }}</strong>
                </div>
                <div class="eta-box">
                    <small>{{ $isDelayed ? 'Durasi keterlambatan' : 'Sisa waktu' }}</small>
                    <strong>
                        @if($isDelayed)
                            {{ $delayHours }} jam
                        @elseif($currentStatus === 'DELIVERED')
                            Sudah tiba
                        @else
                            {{ $remainingLabel ?? 'Belum dihitung' }}
                        @endif
                    </strong>
                </div>
            </div>

            @if($isDelayed)
                <div class="box-alert err">
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> Pengiriman terlambat</strong>
                    <p style="margin-top:6px">
                        @if($delayHours > 0)Estimasi tiba sudah terlewati sekitar {{ $delayHours }} jam.@else Pengiriman ini ditandai terlambat.@endif
                        Tingkat keterlambatan:
                        <span class="sev sev-{{ strtolower($delaySeverity ?? 'low') }}">{{ $delaySeverity ?? 'LOW' }}</span>
                    </p>
                    <p style="margin-top:6px">Lihat bagian Kendala pengiriman di bawah untuk mengetahui penyebabnya.</p>
                </div>
            @elseif($currentStatus === 'DELIVERED')
                <div class="box-alert ok"><i class="fa-solid fa-circle-check"></i> Pengiriman sudah terkirim ke tujuan.</div>
            @else
                <div class="box-alert ok"><i class="fa-solid fa-circle-info"></i> Belum ada keterlambatan berdasarkan estimasi yang tercatat. Estimasi bukan jaminan waktu kedatangan.</div>
            @endif
        </section>


        {{-- KENDALA --}}
        <section class="panel sect">
            <div class="ph">
                <h3><i class="fa-solid fa-triangle-exclamation"></i> Kendala pengiriman</h3>
                <p>Masalah yang tercatat pada pengiriman ini.</p>
            </div>

            @forelse($exceptions as $exception)
                @php
                    $sev = strtoupper($exception->severity ?? 'LOW');
                    $isResolved = strtoupper($exception->status ?? '') === 'RESOLVED';
                @endphp

                <div class="ex {{ $isResolved ? 'resolved' : '' }}">
                    <div class="ex-top">
                        <strong>{{ str_replace('_', ' ', $exception->type ?? 'Pengecualian') }}</strong>
                        <div>
                            <span class="sev sev-{{ strtolower($sev) }}">{{ $sev }}</span>
                            <span class="status {{ $isResolved ? 'status-delivered' : 'status-delayed' }}">{{ $isResolved ? 'Selesai' : 'Terbuka' }}</span>
                        </div>
                    </div>

                    <dl>
                        <div><dt>Lokasi</dt><dd>{{ $exception->location ?: 'Belum tersedia' }}</dd></div>
                        <div><dt>Waktu terdeteksi</dt><dd>{{ $formatDate($exception->occurred_at ?? $exception->created_at) }}</dd></div>
                        <div><dt>Deskripsi</dt><dd class="full">{{ $exception->description ?: 'Belum ada deskripsi.' }}</dd></div>
                        <div><dt>Penyebab</dt><dd class="full">{{ $exception->cause ?: 'Belum ada informasi penyebab.' }}</dd></div>
                        @if($exception->resolved_at)
                            <div><dt>Waktu penyelesaian</dt><dd>{{ $formatDate($exception->resolved_at) }}</dd></div>
                        @endif
                    </dl>
                </div>
            @empty
                <div class="empty good">
                    <i class="fa-solid fa-circle-check"></i>
                    <strong>Tidak ada kendala</strong>
                    Perjalanan barang Anda berjalan tanpa masalah yang tercatat.
                </div>
            @endforelse
        </section>


        <div class="acts">
            <a href="{{ route('customer.pengiriman.index', ['detail' => $shipment->id]) }}" class="btn"><i class="fa-solid fa-box"></i> Lihat detail pengiriman</a>
            <a href="{{ route('customer.pelacakan.index') }}" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i> Cari resi lain</a>
        </div>

    @elseif($trackingNumber === '')

        <section class="panel start">
            <div class="empty">
                <i class="fa-solid fa-truck-fast"></i>
                <strong>Mulai lacak pengiriman Anda</strong>
                Ketik nomor resi di atas, atau pilih salah satu pengiriman terbaru.
            </div>
        </section>

    @endif

    <footer class="footer">
        © {{ date('Y') }} CargoTrace. Sistem Informasi Pelacakan Pengiriman.
    </footer>

</section>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@if($shipment)
<script>
(function () {
    var points = @json($mapPoints);

    function esc(v) {
        return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
        });
    }

    // Animasi garis perjalanan
    var track = document.querySelector('.track');
    var p = parseFloat(track.dataset.progress) || 0;
    requestAnimationFrame(function () {
        setTimeout(function () {
            document.getElementById('fill').style.width = p + '%';
            document.getElementById('vehicle').style.left = p + '%';
        }, 150);
    });

    // Tombol salin resi
    var copyBtn = document.getElementById('copyBtn');
    copyBtn.addEventListener('click', function () {
        var text = document.getElementById('resi').textContent.trim();
        var done = function () {
            copyBtn.querySelector('span').textContent = 'Tersalin';
            setTimeout(function () { copyBtn.querySelector('span').textContent = 'Salin'; }, 1600);
        };
        if (navigator.clipboard) { navigator.clipboard.writeText(text).then(done); }
    });

    // Peta
    var map = L.map('shipment-map', { scrollWheelZoom: false }).setView([-2.5, 118], 4);

    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var sat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    });

    L.control.layers({ 'Peta': osm, 'Satelit': sat }, null, { position: 'topright' }).addTo(map);
    L.control.scale({ imperial: false }).addTo(map);

    map.on('click', function () { map.scrollWheelZoom.enable(); });

    var markers = {};
    setTimeout(function () { map.invalidateSize(); }, 300);

    // ---- Cadangan: jika koordinat GPS belum tersimpan, posisikan dari nama kota ----
    var geo = {
        origin: @json($shipment->origin),
        destination: @json($shipment->destination),
        last: @json($lastLocation)
    };
    var vehicleIcon = '{{ $transport[1] }}';
    var isLand = '{{ $transport[0] }}' === 'darat';

    // Rute jalan sungguhan dari OpenStreetMap (OSRM). Gagal/antar pulau -> null.
    function road(pts) {
        var s = pts.map(function (q) { return q[1] + ',' + q[0]; }).join(';');
        return fetch('https://router.project-osrm.org/route/v1/driving/' + s + '?overview=full&geometries=geojson')
            .then(function (r) { return r.json(); })
            .then(function (j) {
                if (!j.routes || !j.routes[0]) return null;
                return j.routes[0].geometry.coordinates.map(function (c) { return [c[1], c[0]]; });
            })
            .catch(function () { return null; });
    }

    function geocode(q) {
        if (!q) return Promise.resolve(null);
        var key = 'geo:' + String(q).toLowerCase();
        try {
            var cached = localStorage.getItem(key);
            if (cached) return Promise.resolve(JSON.parse(cached));
        } catch (e) {}

        return fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&accept-language=id&q=' + encodeURIComponent(q))
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res || !res.length) return null;
                var p = [parseFloat(res[0].lat), parseFloat(res[0].lon)];
                try { localStorage.setItem(key, JSON.stringify(p)); } catch (e) {}
                return p;
            })
            .catch(function () { return null; });
    }

    function pin(latlng, cls, label, popup) {
        var size = cls === 'last' ? 34 : 28;
        var icon = L.divIcon({
            className: '',
            html: '<div class="pin ' + cls + '">' + label + '</div>',
            iconSize: [size, size], iconAnchor: [size / 2, size / 2], popupAnchor: [0, -size / 2]
        });
        return L.marker(latlng, { icon: icon }).addTo(map).bindPopup('<div class="pop">' + popup + '</div>');
    }

    // Garis lengkung antara dua titik (seperti rute penerbangan)
    function arc(a, b, n) {
        var mx = (a[0] + b[0]) / 2, my = (a[1] + b[1]) / 2;
        var dx = b[1] - a[1], dy = b[0] - a[0];
        var c = [mx + dx * 0.18, my - dy * 0.18];
        var pts = [];
        for (var i = 0; i <= n; i++) {
            var t = i / n, u = 1 - t;
            pts.push([u * u * a[0] + 2 * u * t * c[0] + t * t * b[0], u * u * a[1] + 2 * u * t * c[1] + t * t * b[1]]);
        }
        return pts;
    }

    function drawFallback(a, b, c, roadCoords) {
        var note = document.getElementById('mapNote');

        if (!a && !b && !c) {
            note.hidden = false;
            note.textContent = 'Lokasi pada peta belum bisa ditemukan dari nama kota yang tersimpan. Rute akan tampil setelah koordinat tracking tersedia.';
            return;
        }

        var all = [];
        var N = 60;
        var frac = Math.max(0, Math.min(1, p / 100));
        var vehiclePos = c;

        if (a && b) {
            var curve = roadCoords || arc(a, b, N);
            var cut = Math.round(frac * (curve.length - 1));
            if (!vehiclePos) vehiclePos = curve[cut];

            L.polyline(curve, { color: '#2457f5', weight: 3, opacity: .3, dashArray: '2 9', lineCap: 'round' }).addTo(map);
            L.polyline(curve.slice(0, cut + 1), { color: '#2457f5', weight: 4, opacity: .95, lineCap: 'round' }).addTo(map);
            all = all.concat(curve);
        }

        if (a) { pin(a, 'first', '<i class="fa-solid fa-play" style="font-size:9px"></i>', '<b>Kota asal</b><p>' + esc(geo.origin) + '</p>'); all.push(a); }
        if (b) { pin(b, '', '<i class="fa-solid fa-flag-checkered" style="font-size:10px"></i>', '<b>Kota tujuan</b><p>' + esc(geo.destination) + '</p>'); all.push(b); }

        if (vehiclePos) {
            var vi = L.divIcon({
                className: '',
                html: '<div class="vmark"><i class="fa-solid ' + vehicleIcon + '"></i></div>',
                iconSize: [42, 42], iconAnchor: [21, 21], popupAnchor: [0, -22]
            });
            L.marker(vehiclePos, { icon: vi, zIndexOffset: 1000 }).addTo(map)
                .bindPopup('<div class="pop"><b>Posisi barang</b><p>' + esc(geo.last || 'Perkiraan berdasarkan progres pengiriman') + '</p></div>');
            all.push(vehiclePos);
        }

        if (all.length > 1) map.fitBounds(L.latLngBounds(all), { padding: [60, 60] });
        else map.setView(all[0], 9);

        note.hidden = false;
        note.textContent = c
            ? 'Posisi diperkirakan dari nama lokasi terakhir. Koordinat GPS belum tersimpan.'
            : 'Rute diperkirakan dari kota asal dan tujuan. Posisi barang mengikuti progres pengiriman.';
    }

    if (points.length > 0) {
        var coords = points.map(function (pt) { return [pt.latitude, pt.longitude]; });

        // Garis putus-putus untuk jalur, garis solid untuk bagian yang sudah ditempuh
        L.polyline(coords, { color: '#2457f5', weight: 4, opacity: .25 }).addTo(map);
        var line = L.polyline(coords, { color: '#2457f5', weight: 4, opacity: .9, dashArray: '2 9', lineCap: 'round' }).addTo(map);

        points.forEach(function (pt, i) {
            var cls = i === 0 ? 'first' : (i === points.length - 1 ? 'last' : '');
            var size = cls === 'last' ? 34 : 28;
            var icon = L.divIcon({
                className: '',
                html: '<div class="pin ' + cls + '">' + (i + 1) + '</div>',
                iconSize: [size, size],
                iconAnchor: [size / 2, size / 2],
                popupAnchor: [0, -size / 2]
            });

            var m = L.marker([pt.latitude, pt.longitude], { icon: icon }).addTo(map);
            m.bindPopup(
                '<div class="pop"><b>' + esc(pt.status || 'Tracking') + '</b>' +
                '<p>' + esc(pt.location || 'Lokasi tidak tersedia') + '</p>' +
                '<p>' + esc(pt.event_time || '-') + '</p>' +
                (pt.description ? '<p>' + esc(pt.description) + '</p>' : '') + '</div>'
            );

            markers[pt.latitude + ',' + pt.longitude] = m;
        });

        if (points.length === 1) {
            map.setView(coords[0], 10);
        } else {
            map.fitBounds(line.getBounds(), { padding: [50, 50] });

            // Jalur darat: ganti garis lurus dengan rute jalan sungguhan
            if (isLand && coords.length <= 25) {
                road(coords).then(function (r) {
                    if (!r) return;
                    map.removeLayer(line);
                    var real = L.polyline(r, { color: '#2457f5', weight: 5, opacity: .9, lineCap: 'round' }).addTo(map);
                    map.fitBounds(real.getBounds(), { padding: [50, 50] });
                });
            }
        }
    } else {
        // Berurutan agar tidak melanggar batas permintaan layanan geocoding
        geocode(geo.origin).then(function (a) {
            return geocode(geo.destination).then(function (b) {
                return geocode(geo.last).then(function (c) {
                    var wantRoad = isLand && a && b;
                    (wantRoad ? road([a, b]) : Promise.resolve(null)).then(function (r) {
                        drawFallback(a, b, c, r);
                    });
                });
            });
        });
    }

    // Klik riwayat -> peta terbang ke titik tersebut
    function focusEvent(el) {
        var key = parseFloat(el.dataset.lat) + ',' + parseFloat(el.dataset.lng);
        var m = markers[key];
        if (!m) return;
        document.getElementById('shipment-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
        map.flyTo(m.getLatLng(), Math.max(map.getZoom(), 9), { duration: .9 });
        setTimeout(function () { m.openPopup(); }, 950);
    }

    document.querySelectorAll('.tl-i.has-geo').forEach(function (el) {
        el.addEventListener('click', function () { focusEvent(el); });
        el.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); focusEvent(el); }
        });
    });
})();
</script>
@endif

</body>
</html>
