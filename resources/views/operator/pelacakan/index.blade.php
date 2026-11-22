<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Pelacakan | CargoTrace</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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

/* HERO + PENCARIAN */
.hero{position:relative;overflow:hidden;padding:34px 38px;margin-bottom:22px;border-radius:28px;color:#fff;
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 55%,#1d6fe870 100%),url('https://images.unsplash.com/photo-1494412519320-aa613dfb7738?auto=format&fit=crop&w=1800&q=80') center/cover;box-shadow:0 24px 50px #0b2a6b44}
.hero::after{content:"";position:absolute;width:380px;height:380px;right:-110px;top:-190px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.crumb{font-size:11px;color:#a9c8f5;margin-bottom:10px}
.hero h1{font:800 29px/1.25 var(--h);letter-spacing:-.8px}
.hero p{margin-top:10px;max-width:560px;line-height:1.75;color:#cfe0ff}
.hero-row{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap}
.hstats{display:flex;gap:10px;flex-wrap:wrap}
.hs{min-width:112px;padding:12px 16px;border-radius:16px;background:#ffffff1c;border:1px solid #ffffff35;backdrop-filter:blur(8px);font-size:11px;color:#dbe9ff}
.hs b{display:block;font:800 21px var(--h);color:#fff}
.sform{display:flex;flex-wrap:wrap;gap:10px;margin-top:24px;padding:10px;border-radius:18px;background:#fff;box-shadow:0 14px 34px #06153344;max-width:980px}
.sform .in{position:relative;flex:1;min-width:220px}
.sform .in i{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#8ca6c4}
.sform input{width:100%;padding:14px 16px 14px 44px;border:0;border-radius:12px;background:#f4f8ff;color:var(--ink);font:600 13.5px 'Plus Jakarta Sans',sans-serif}
.sform input:focus{outline:2px solid #2563eb55}
.sform select{padding:0 12px;border:0;border-radius:12px;background:#f4f8ff;color:var(--ink);font:600 12px 'Plus Jakarta Sans',sans-serif;max-width:170px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 20px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:12px;cursor:pointer;font:700 12px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;transition:.2s;box-shadow:0 8px 20px #2563eb44}
.btn:hover{filter:brightness(1.1)}
.btn-light{background:#e8f1ff;color:#1d4ed8;box-shadow:none}
.btn-warn{background:linear-gradient(135deg,#f5b73a,#d98a00);box-shadow:0 8px 20px #d98a0044}

/* PICKER */
.pick{display:flex;gap:10px;overflow-x:auto;padding:4px 2px 14px;margin-bottom:6px;scrollbar-width:thin}
.pk{flex:none;min-width:210px;max-width:260px;padding:14px 16px;background:#fff;border:1px solid var(--line);border-radius:18px;text-decoration:none;transition:.2s;box-shadow:0 8px 22px #2563eb0d}
.pk:hover{transform:translateY(-3px);border-color:#9fc2f7}
.pk.on{border-color:var(--blue);box-shadow:0 12px 28px #2563eb30}
.pk b{display:block;color:var(--blue);font-size:12.5px}
.pk strong{display:block;margin-top:5px;font-size:12px;color:var(--navy);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.pk small{display:block;margin:5px 0 8px;font-size:10.5px;color:var(--mut);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.pgn{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:22px;font-size:11px;color:#8ca6c4}
.pgn nav svg{width:16px;height:16px}

/* PANEL & KOMPONEN */
.panel{padding:24px;background:#fff;border:1px solid var(--line);border-radius:26px;box-shadow:0 10px 30px #2563eb0d;min-width:0}
.ph{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px}
.ph h2{font:700 16.5px var(--h);color:var(--navy);letter-spacing:-.3px;display:flex;align-items:center;gap:10px}
.ph h2 .hi{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font-size:14px;flex:none}
.ph p{margin-top:6px;font-size:12px;color:var(--mut)}
.sect{margin-bottom:22px}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-bottom:22px}
.g2{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:20px;margin-bottom:22px;align-items:start}
.gmap{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(0,1fr);gap:20px;margin-bottom:22px;align-items:start}
.status{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.st-CREATED{background:#e8eef7;color:#5b6f90}.st-BOOKED{background:#e0edff;color:#1d4ed8}.st-PICKED_UP{background:#f0eaff;color:#7c3aed}.st-IN_TRANSIT{background:#fff4dc;color:#b77900}.st-DELIVERED{background:#e8f7ef;color:#26734d}.st-DELAYED{background:#ffe9e9;color:#c92f3a}
.late-tag{margin-left:6px;padding:3px 8px;border-radius:12px;background:#ffe9e9;color:#c92f3a;font-size:9px;font-weight:700}
.kv{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
.kv div{padding:13px 15px;border-radius:15px;background:#f4f8ff}
.kv small{display:block;font-size:10px;color:#8ca6c4;font-weight:600}
.kv b{display:block;margin-top:5px;font-size:12.5px;color:var(--navy);overflow-wrap:anywhere}
.sum-top{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap;margin-bottom:20px}
.sum-top h2{font:800 24px var(--h);color:var(--navy);letter-spacing:-.5px}
.sum-top p{margin-top:6px;color:var(--mut)}
.rt{display:flex;align-items:center;gap:12px;margin:6px 0 22px}
.rt .d{flex:none;width:14px;height:14px;border-radius:50%;border:4px solid var(--blue);background:#fff}.rt .d.e{border-color:#16a34a}
.rt .pt{min-width:0;max-width:30%}.rt .pt b{display:block;font-size:13px;color:var(--navy);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.rt .pt small{font-size:10px;color:#8ca6c4;font-weight:600}
.rt .ln{flex:1;height:8px;border-radius:6px;background:#e6eefc;position:relative}
.rt .ln i{display:block;height:100%;border-radius:6px;background:linear-gradient(90deg,#38bdf8,#1d4ed8)}
.rt .ln span{position:absolute;top:50%;transform:translate(-50%,-50%);display:grid;place-items:center;width:30px;height:30px;border-radius:50%;background:#f59e0b;border:3px solid #fff;color:#fff;font-size:11px;box-shadow:0 4px 12px #f59e0b66}
.prog{display:flex;align-items:flex-start;margin-bottom:22px}
.pg{flex:1;text-align:center;position:relative}
.pg::before{content:"";position:absolute;top:13px;left:-50%;width:100%;height:3px;background:#e1eafa}
.pg:first-child::before{display:none}.pg.done::before{background:var(--blue)}
.pg i{position:relative;z-index:2;display:grid;place-items:center;width:28px;height:28px;margin:0 auto;border-radius:50%;background:#e1eafa;color:#fff;font-size:11px}
.pg.done i{background:var(--blue)}.pg.now i{background:#fff;border:3px solid var(--blue);box-shadow:0 0 0 5px #2563eb22;color:var(--blue)}
.pg small{display:block;margin-top:7px;font-size:10px;font-weight:600;color:#8ca6c4}.pg.now small,.pg.done small{color:var(--navy)}

/* KARTU INFO */
.big{font:800 22px/1.25 var(--h);color:var(--navy);letter-spacing:-.4px}
.sub{margin-top:6px;font-size:12px;color:var(--mut);line-height:1.6}
.bar{height:8px;border-radius:6px;background:#e9f0fc;overflow:hidden;margin-top:14px}
.bar i{display:block;height:100%;border-radius:6px;background:linear-gradient(90deg,#38bdf8,#1d4ed8)}
.icb{display:grid;place-items:center;width:46px;height:46px;border-radius:14px;color:#fff;font-size:18px;margin-bottom:14px}
.ok{background:linear-gradient(135deg,#3fc08a,#1f9a68)}.bad{background:linear-gradient(135deg,#f0626a,#c92f3a)}.warn{background:linear-gradient(135deg,#f5b73a,#d98a00)}.info{background:linear-gradient(135deg,#3b8bf5,#1d4ed8)}
.cause{margin-top:14px;padding:12px 14px;border-radius:14px;background:#fff4f4;color:#a8323b;font-size:12px;line-height:1.6}
.cause.n{background:#f4f8ff;color:var(--mut)}
.mini{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
.mini span{padding:6px 11px;border-radius:20px;background:#e8f1ff;color:#1d4ed8;font-size:10.5px;font-weight:700}

/* PETA */
#trackMap{height:480px;width:100%;border-radius:18px;overflow:hidden;background:#dfeafb;position:relative;z-index:0}
.mapw{position:relative}
.map-empty{position:absolute;inset:0;z-index:600;display:none;place-items:center;background:#ffffffc0;backdrop-filter:blur(2px);text-align:center;padding:20px;border-radius:18px}
.map-empty.show{display:grid}
.map-empty div{max-width:330px;padding:24px;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 16px 40px #0b2a6b22;line-height:1.7;color:var(--mut)}
.map-empty i{display:grid;place-items:center;width:52px;height:52px;margin:0 auto 10px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:21px}
.map-info{position:absolute;left:12px;bottom:12px;z-index:500;display:flex;flex-wrap:wrap;gap:10px;padding:8px 12px;background:#ffffffee;border:1px solid var(--line);border-radius:12px;font-size:10.5px;color:#3d68a8;font-weight:600}
.map-info span{display:flex;align-items:center;gap:6px}.map-info i{display:inline-block;width:9px;height:9px;border-radius:50%}
.pin{display:grid;place-items:center;width:34px;height:34px;border-radius:50%;border:3px solid #fff;color:#fff;font-size:14px;box-shadow:0 4px 14px #0005}
.pin.fleet{animation:pulse 2s infinite}
@keyframes pulse{0%{box-shadow:0 0 0 0 #f59e0bb0}70%{box-shadow:0 0 0 14px #f59e0b00}100%{box-shadow:0 0 0 0 #f59e0b00}}
.tp{width:10px;height:10px;border-radius:50%;background:#fff;border:3px solid var(--blue)}
.pop{min-width:170px;font:12px/1.7 'Plus Jakarta Sans',sans-serif;color:#365775}.pop strong{display:block;font-size:13px;color:var(--navy)}.pop small{color:#8ca6c4}

/* TIMELINE */
.tl-wrap{max-height:480px;overflow-y:auto;padding-right:6px}
.tl{position:relative;padding-left:28px}
.tl::before{content:"";position:absolute;left:8px;top:6px;bottom:6px;width:2px;background:#dbe6f8}
.ti{position:relative;padding-bottom:20px}.ti:last-child{padding-bottom:0}
.ti::before{content:"";position:absolute;left:-26px;top:3px;width:12px;height:12px;border-radius:50%;background:#fff;border:3px solid #b9d2f7}
.ti.last::before{border-color:var(--blue);box-shadow:0 0 0 5px #2563eb22}
.ti b{display:block;font-size:12.5px;color:var(--navy)}
.ti span{display:block;margin-top:3px;font-size:11.5px;color:#41608a}
.ti small{display:block;margin-top:3px;font-size:10.5px;color:#8ca6c4;line-height:1.5}
.nodata{padding:34px 12px;text-align:center;color:#8ca6c4;line-height:1.7}
.nodata i{display:block;margin-bottom:10px;font-size:30px;color:#9db6dc}

/* CUACA + WAKTU */
.wg{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.wc{padding:20px;border-radius:22px;border:1px solid var(--line);background:linear-gradient(165deg,#fff,#f1f7ff)}
.wc.dark{background:linear-gradient(160deg,#061533,#0b2a6b 65%,#1747b8);color:#fff;border:0}
.wt{display:flex;align-items:center;gap:10px;font-size:10.5px;font-weight:700;letter-spacing:.8px;color:#8ca6c4;margin-bottom:6px}
.wc.dark .wt{color:#9fbcea}
.wt i{display:grid;place-items:center;width:26px;height:26px;border-radius:8px;color:#fff;font-size:11px}
.wn{font:700 15px var(--h);color:var(--navy);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.wcur{display:flex;align-items:center;gap:14px;margin:14px 0}
.wcur .tmp{font:800 40px var(--h);color:var(--navy);line-height:1}
.wcur .ic{font-size:40px}
.wcur small{display:block;margin-top:5px;font-size:12px;color:var(--mut);font-weight:600}
.wm{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px}
.wm div{padding:9px 6px;border-radius:12px;background:#eaf3ff;text-align:center}
.wm b{display:block;font-size:12.5px;color:var(--navy)}.wm small{font-size:9.5px;color:#8ca6c4;font-weight:600}
.fc{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.fc div{padding:9px 4px;border-radius:12px;border:1px solid var(--line);text-align:center;background:#fff}
.fc small{display:block;font-size:10px;color:#8ca6c4;font-weight:600}.fc span{display:block;font-size:19px;margin:4px 0}.fc b{font-size:11px;color:var(--navy)}
.ck{font:800 34px var(--h);letter-spacing:1px;margin:14px 0 6px}
.cd{font-size:12px;color:#bcd3f7}
.cz{display:inline-block;margin-top:12px;padding:5px 11px;border-radius:20px;background:#ffffff1f;font-size:10.5px;font-weight:700}
.cdiff{margin-top:12px;font-size:11.5px;color:#9fbcea}
.wph{padding:30px 10px;text-align:center;color:#8ca6c4;font-size:12px;line-height:1.7}
.wc.dark .wph{color:#9fbcea}
.skl{height:120px;border-radius:16px;background:linear-gradient(90deg,#edf3fc,#f8fbff,#edf3fc);background-size:200% 100%;animation:sk 1.3s infinite}
@keyframes sk{to{background-position:-200% 0}}
.empty-all{padding:60px 20px;text-align:center;color:#8ca6c4;line-height:1.8}
.empty-all i{display:grid;place-items:center;width:70px;height:70px;margin:0 auto 14px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:28px}

/* FORM OPERATOR */
.fg{display:grid;grid-template-columns:1fr 1fr;gap:13px}
.fg .f label{display:block;font-size:11px;font-weight:700;color:#5d7aa5;margin-bottom:7px}
.fg input,.fg select,.fg textarea{width:100%;padding:11px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:var(--ink);font:12px 'Plus Jakarta Sans',sans-serif}
.fg input:focus,.fg select:focus,.fg textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.fg textarea{min-height:80px;resize:vertical}
.fg .full{grid-column:1/-1}
.alert-error{background:#ffe9e9;color:#c92f3a}
.alert i{margin-right:6px}
.note{margin-top:13px;font-size:11px;color:#8ca6c4;line-height:1.6}

/* RESPONSIVE */
@media(max-width:1250px){.g3,.wg{grid-template-columns:1fr 1fr}.kv{grid-template-columns:1fr 1fr}}
@media(max-width:1100px){.gmap,.g2{grid-template-columns:1fr}.content{padding:20px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 30px}.hero{padding:26px 20px}.hero h1{font-size:22px}
 .g3,.g2,.wg,.kv,.fg{grid-template-columns:1fr}.fg .full{grid-column:auto}.panel{padding:18px}.rt .pt{max-width:36%}#trackMap{height:380px}.map-info{display:none}
}
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

    $steps = [
        ['CREATED', 'Created', 'fa-file-circle-plus'],
        ['BOOKED', 'Booked', 'fa-calendar-check'],
        ['PICKED_UP', 'Picked Up', 'fa-hand-holding-box'],
        ['IN_TRANSIT', 'In Transit', 'fa-truck-fast'],
        ['DELIVERED', 'Delivered', 'fa-circle-check'],
    ];
    $statusLabels = ['CREATED' => 'Dibuat', 'BOOKED' => 'Dipesan', 'PICKED_UP' => 'Dijemput', 'IN_TRANSIT' => 'Dalam Perjalanan', 'DELIVERED' => 'Terkirim'];
    $statusOrder = array_column($steps, 0);
    $modeMap = ['LAND' => 'darat', 'SEA' => 'laut', 'AIR' => 'udara'];
    $modeLabel = ['udara' => 'Udara', 'laut' => 'Laut', 'darat' => 'Darat'];
    $modeIcon = ['udara' => 'fa-plane', 'laut' => 'fa-ship', 'darat' => 'fa-truck-fast'];
    $cb = fn ($v) => $v ? Carbon::parse($v) : null;
    $num = fn ($v) => is_numeric($v) ? (float) $v : null;
    $search = (string) request('search', '');
    $filterQuery = array_filter(['search' => request('search'), 'status' => request('status'), 'carrier' => request('carrier')]);

    $S = null;
    if ($selected) {
        $eta = $cb($selected->estimated_arrival);
        $etd = $cb($selected->estimated_departure);
        $events = $selected->trackingEvents->sortBy('event_time')->map(function ($e) use ($cb, $num) {
            $t = $cb($e->event_time);
            return [
                'status' => $e->status,
                'location' => $e->location,
                'lat' => $num($e->latitude),
                'lng' => $num($e->longitude),
                'time' => $t?->toIso8601String(),
                'time_text' => $t ? $t->format('d M Y H:i') : '-',
                'desc' => $e->description,
                'by' => $e->updated_by,
            ];
        })->values()->all();

        $S = [
            'id' => $selected->id,
            'number' => $selected->shipment_number,
            'name' => $selected->shipment_name,
            'customer' => $selected->customer_name,
            'carrier' => $selected->carrier,
            'origin' => $selected->origin,
            'destination' => $selected->destination,
            'status' => strtoupper((string) $selected->status),
            'mode' => $modeMap[strtoupper((string) $selected->transport_type)] ?? null,
            'cargo' => $selected->cargo_type,
            'weight' => $selected->weight,
            'eta' => $eta?->toIso8601String(),
            'etd' => $etd?->toIso8601String(),
            'eta_text' => $eta ? $eta->format('d M Y H:i') : '-',
            'etd_text' => $etd ? $etd->format('d M Y H:i') : '-',
            'delay_cause' => null,
            'origin_pos' => null,
            'dest_pos' => null,
            'events' => $events,
        ];
    }

    $si = $S ? array_search($S['status'], $statusOrder, true) : false;
    $si = $si === false ? -1 : $si;
    $pct = $si < 0 ? 0 : (int) round($si / 4 * 100);
    $nextStatuses = $si < 0 ? [] : array_slice($statusOrder, $si + 1);
    $latestLoc = $latestEvent->location ?? ($S['origin'] ?? '');
@endphp

    <!-- 1. PENCARIAN -->
    <section class="hero">
        <div class="crumb"><a href="{{ $rt(['operator.dashboard', 'operator.index']) }}" style="text-decoration:none">Dashboard</a> &nbsp;/&nbsp; Pelacakan</div>
        <div class="hero-row">
            <div>
                <h1>Pelacakan Pengiriman</h1>
                <p>Pantau posisi barang, riwayat perjalanan, estimasi tiba, cuaca, dan waktu lokal. Operator juga bisa menambah aktivitas dan memperbarui status dari halaman ini.</p>
            </div>
            <div class="hstats">
                <div class="hs"><b>{{ number_format($totalShipments) }}</b>total dipantau</div>
                <div class="hs"><b>{{ $inTransit }}</b>dalam perjalanan</div>
                <div class="hs"><b>{{ $delayed }}</b>terlambat</div>
                <div class="hs"><b>{{ $delivered }}</b>terkirim</div>
            </div>
        </div>
        <form class="sform" action="{{ route('operator.pelacakan.index') }}" method="GET">
            <div class="in">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nomor resi, pelanggan, asal, tujuan..." aria-label="Pencarian" autocomplete="off">
            </div>
            <select name="status" aria-label="Status">
                <option value="">Semua status</option>
                @foreach($statusLabels as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="carrier" aria-label="Kurir">
                <option value="">Semua kurir</option>
                @foreach($carriers as $c)
                    <option value="{{ $c }}" @selected(request('carrier') === $c)>{{ $c }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn"><i class="fa-solid fa-location-crosshairs"></i> Lacak</button>
            @if($filterQuery)<a href="{{ route('operator.pelacakan.index') }}" class="btn btn-light">Reset</a>@endif
        </form>
    </section>

    @if(session('success'))
        <div class="alert"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <strong><i class="fa-solid fa-triangle-exclamation"></i>Periksa kembali data yang dimasukkan:</strong>
            <ul style="margin:7px 0 0 18px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if(!$S)
        <section class="panel">
            <div class="empty-all">
                <i class="fa-solid fa-box-open"></i>
                <strong style="color:var(--navy);font-size:15px">{{ $filterQuery ? 'Pengiriman tidak ditemukan' : 'Belum ada pengiriman' }}</strong><br>
                {{ $filterQuery ? 'Tidak ada pengiriman yang cocok dengan pencarian. Coba ubah kata kunci atau filter.' : 'Tambahkan pengiriman terlebih dahulu melalui menu Daftar Pengiriman.' }}
                <div style="margin-top:16px"><a href="{{ $rt(['operator.pengiriman.index']) }}" class="btn"><i class="fa-solid fa-box"></i> Ke Daftar Pengiriman</a></div>
            </div>
        </section>
    @else

    <!-- DAFTAR / PEMILIH PENGIRIMAN -->
    <div class="pick" aria-label="Pilih pengiriman">
        @foreach($shipments as $p)
            @php
                $pEta = $cb($p->estimated_arrival);
                $pLate = $pEta && $pEta->isPast() && strtoupper((string) $p->status) !== 'DELIVERED';
            @endphp
            <a href="{{ route('operator.pelacakan.index', array_merge($filterQuery, ['id' => $p->id])) }}" class="pk {{ $p->id === $S['id'] ? 'on' : '' }}">
                <b>{{ $p->shipment_number }}</b>
                <strong>{{ $p->customer_name }}</strong>
                <small>{{ $p->origin }} → {{ $p->destination }}</small>
                <span class="status st-{{ strtoupper((string) $p->status) }}">{{ str_replace('_', ' ', $p->status) }}</span>
                @if($pLate)<span class="late-tag">LATE</span>@endif
            </a>
        @endforeach
    </div>
    <div class="pgn">
        <span>Menampilkan {{ $shipments->firstItem() ?? 0 }}–{{ $shipments->lastItem() ?? 0 }} dari {{ $shipments->total() }} pengiriman</span>
        @if($shipments->hasPages())<div>{{ $shipments->links() }}</div>@endif
    </div>

    <!-- 2. DETAIL PENGIRIMAN -->
    <section class="panel sect">
        <div class="sum-top">
            <div>
                <h2>{{ $S['number'] }}</h2>
                <p>{{ $S['name'] }} · {{ $S['customer'] }}</p>
            </div>
            <span class="status st-{{ $S['status'] }}" style="font-size:11.5px;padding:8px 16px">{{ str_replace('_', ' ', $S['status']) }}</span>
        </div>

        <div class="rt">
            <div class="d"></div>
            <div class="pt"><small>ASAL</small><b>{{ $S['origin'] }}</b></div>
            <div class="ln"><i style="width:{{ $pct }}%"></i><span style="left:{{ $pct }}%"><i class="fa-solid {{ $modeIcon[$S['mode']] ?? 'fa-truck-fast' }}" style="background:none;width:auto;height:auto"></i></span></div>
            <div class="pt" style="text-align:right"><small>TUJUAN</small><b>{{ $S['destination'] }}</b></div>
            <div class="d e"></div>
        </div>

        <div class="prog">
            @foreach($steps as $n => [$key, $label, $icon])
                <div class="pg {{ $n < $si ? 'done' : ($n === $si ? 'now' : '') }}">
                    <i class="fa-solid {{ $n < $si ? 'fa-check' : $icon }}"></i><small>{{ $label }}</small>
                </div>
            @endforeach
        </div>

        <div class="kv">
            <div><small>NOMOR RESI</small><b>{{ $S['number'] }}</b></div>
            <div><small>PELANGGAN</small><b>{{ $S['customer'] }}</b></div>
            <div><small>PERUSAHAAN PENGIRIMAN</small><b>{{ $S['carrier'] ?: '-' }}</b></div>
            <div><small>LOKASI ASAL</small><b>{{ $S['origin'] }}</b></div>
            <div><small>LOKASI TUJUAN</small><b>{{ $S['destination'] }}</b></div>
            <div><small>MODA &amp; BARANG</small><b>{{ $modeLabel[$S['mode']] ?? '-' }} · {{ $S['cargo'] ?: '-' }}{{ $S['weight'] ? ' · ' . rtrim(rtrim(number_format((float) $S['weight'], 2, ',', '.'), '0'), ',') . ' kg' : '' }}</b></div>
        </div>
    </section>

    <!-- LOKASI TERAKHIR, ETA, KETERLAMBATAN -->
    <section class="g3">
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-location-crosshairs"></i></span> Lokasi Terakhir</h2></div></div>
            <div id="lastLoc"></div>
        </div>
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-calendar-check"></i></span> Estimasi Tiba (ETA)</h2></div></div>
            <div id="etaBox"></div>
        </div>
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-clock"></i></span> Informasi Keterlambatan</h2></div></div>
            <div id="delayBox"></div>
        </div>
    </section>

    <!-- PETA + TIMELINE -->
    <section class="gmap">
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-map-location-dot"></i></span> Peta Pelacakan</h2><p>Perjalanan dari lokasi asal ke tujuan beserta titik yang sudah dilalui.</p></div></div>
            <div class="mapw">
                <div id="trackMap"></div>
                <div class="map-empty" id="mapEmpty"><div><i class="fa-solid fa-map-pin"></i><strong style="color:var(--navy)">Peta belum bisa digambar</strong><br>Koordinat lokasi asal, tujuan, atau titik tracking belum ditemukan.</div></div>
                <div class="map-info">
                    <span><i style="background:#2563eb"></i> Asal</span>
                    <span><i style="background:#16a34a"></i> Tujuan</span>
                    <span><i style="background:#f59e0b"></i> Posisi barang</span>
                    <span><i style="background:#fff;border:3px solid #2563eb"></i> Titik tracking</span>
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-clock-rotate-left"></i></span> Riwayat Perjalanan</h2><p>Urutan aktivitas barang berdasarkan waktu.</p></div></div>
            <div class="tl-wrap" id="timeline"></div>
        </div>
    </section>

    <!-- CUACA -->
    <section class="panel sect">
        <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-cloud-sun-rain"></i></span> Informasi Cuaca</h2><p>Kondisi cuaca terkini dan prakiraan 3 hari di lokasi asal, lokasi saat ini, dan tujuan. Sumber: Open-Meteo.</p></div></div>
        <div class="wg" id="weatherGrid"><div class="skl"></div><div class="skl"></div><div class="skl"></div></div>
    </section>

    <!-- WAKTU LOKAL -->
    <section class="panel sect">
        <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-globe"></i></span> Waktu Lokal</h2><p>Waktu dan zona waktu setempat di setiap lokasi pengiriman.</p></div></div>
        <div class="wg" id="clockGrid"><div class="skl"></div><div class="skl"></div><div class="skl"></div></div>
    </section>

    <!-- FORM OPERATOR -->
    <section class="g2">
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-plus"></i></span> Tambah Aktivitas Tracking</h2><p>Catat aktivitas atau titik lokasi baru untuk pengiriman ini.</p></div></div>
            <form action="{{ route('operator.pengiriman.tracking.store', $selected) }}" method="POST">
                @csrf
                <div class="fg">
                    <div class="f">
                        <label>Status Aktivitas *</label>
                        <select name="status" required>
                            @foreach($statusLabels as $key => $label)
                                <option value="{{ $key }}" @selected(old('status', $S['status']) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="f">
                        <label>Tanggal dan Waktu *</label>
                        <input type="datetime-local" name="event_time" value="{{ old('event_time', now()->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="f full">
                        <label>Lokasi Aktivitas *</label>
                        <input type="text" name="location" value="{{ old('location', $latestLoc) }}" placeholder="Masukkan lokasi aktivitas" required>
                    </div>
                    <div class="f"><label>Latitude</label><input type="number" step="any" name="latitude" value="{{ old('latitude') }}" placeholder="-6.200000"></div>
                    <div class="f"><label>Longitude</label><input type="number" step="any" name="longitude" value="{{ old('longitude') }}" placeholder="106.816666"></div>
                    <div class="f full"><label>Keterangan</label><textarea name="description" placeholder="Jelaskan aktivitas pengiriman...">{{ old('description') }}</textarea></div>
                </div>
                <button type="submit" class="btn" style="margin-top:16px"><i class="fa-solid fa-floppy-disk"></i> Simpan Aktivitas</button>
            </form>
        </div>

        <div class="panel">
            <div class="ph"><div><h2><span class="hi" style="background:linear-gradient(135deg,#f5b73a,#d98a00)"><i class="fa-solid fa-arrows-rotate"></i></span> Perbarui Status</h2><p>Status hanya bisa maju, tidak bisa mundur.</p></div></div>

            @if($S['status'] === 'DELIVERED')
                <div class="alert"><i class="fa-solid fa-circle-check"></i>Pengiriman sudah selesai. Status tidak dapat dimundurkan.</div>
            @elseif(count($nextStatuses) > 0)
                <form action="{{ route('operator.pengiriman.update-status', $selected) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="fg">
                        <div class="f full">
                            <label>Status Selanjutnya *</label>
                            <select name="status" required>
                                <option value="">Pilih status berikutnya</option>
                                @foreach($nextStatuses as $st)
                                    <option value="{{ $st }}">{{ $statusLabels[$st] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="f full"><label>Lokasi Saat Ini *</label><input type="text" name="location" value="{{ $latestLoc }}" required></div>
                        <div class="f"><label>Latitude</label><input type="number" step="any" name="latitude" placeholder="-6.200000"></div>
                        <div class="f"><label>Longitude</label><input type="number" step="any" name="longitude" placeholder="106.816666"></div>
                        <div class="f full"><label>Keterangan</label><textarea name="description" placeholder="Keterangan perubahan status..."></textarea></div>
                    </div>
                    <button type="submit" class="btn btn-warn" style="margin-top:16px"><i class="fa-solid fa-arrows-rotate"></i> Perbarui Status</button>
                </form>
            @else
                <div class="alert alert-error">Status pengiriman tidak dapat diperbarui.</div>
            @endif

            <p class="note">Urutan status: Dibuat → Dipesan → Dijemput → Dalam Perjalanan → Terkirim.</p>
        </div>
    </section>

    @endif

</section>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const S = @json($S);
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
const nice = v => esc(String(v || '').replace(/_/g, ' '));

if (S) (async function () {
    const events = S.events || [];
    const lastEv = events.length ? events[events.length - 1] : null;
    const lastPt = [...events].reverse().find(e => e.lat !== null && e.lng !== null) || null;
    const delivered = S.status === 'DELIVERED';
    const MODEICON = { udara:'fa-plane', laut:'fa-ship', darat:'fa-truck-fast' }[S.mode] || 'fa-truck-fast';

    const dur = ms => {
        const m = Math.floor(Math.abs(ms) / 60000), d = Math.floor(m / 1440), h = Math.floor(m % 1440 / 60), mi = m % 60;
        return d > 0 ? d + ' hari ' + h + ' jam' : (h > 0 ? h + ' jam ' + mi + ' menit' : mi + ' menit');
    };
    const ago = iso => {
        const ms = Date.now() - new Date(iso).getTime(), m = Math.floor(ms / 60000);
        if (m < 1) return 'baru saja';
        if (m < 60) return m + ' menit lalu';
        if (m < 1440) return Math.floor(m / 60) + ' jam lalu';
        return Math.floor(m / 1440) + ' hari lalu';
    };

    /* LOKASI TERAKHIR */
    (function () {
        if (!lastEv) {
            $('lastLoc').innerHTML = '<div class="nodata"><i class="fa-solid fa-location-pin-lock"></i>Belum ada data pelacakan tercatat untuk pengiriman ini.</div>';
            return;
        }
        $('lastLoc').innerHTML =
            '<div class="icb info"><i class="fa-solid fa-location-dot"></i></div>' +
            '<div class="big">' + esc(lastEv.location || 'Lokasi tidak tercatat') + '</div>' +
            '<div class="sub">' + nice(lastEv.status) + ' · ' + esc(lastEv.time_text) + (lastEv.time ? ' (' + ago(lastEv.time) + ')' : '') + '</div>' +
            (lastEv.desc ? '<div class="sub">' + esc(lastEv.desc) + '</div>' : '') +
            '<div class="mini">' + (lastPt ? '<span>' + lastPt.lat.toFixed(4) + ', ' + lastPt.lng.toFixed(4) + '</span>' : '<span>Koordinat belum tercatat</span>') + (lastEv.by ? '<span>Oleh ' + esc(lastEv.by) + '</span>' : '') + '</div>';
    })();

    /* ETA + KETERLAMBATAN */
    function renderEta() {
        const now = Date.now(), eta = S.eta ? new Date(S.eta).getTime() : null, etd = S.etd ? new Date(S.etd).getTime() : null;
        let pct = delivered ? 100 : 0;
        if (!delivered && eta && etd && eta > etd) pct = Math.max(0, Math.min(100, (now - etd) / (eta - etd) * 100));
        const late = !delivered && eta && now > eta, flagged = S.status === 'DELAYED';

        const sub = delivered ? 'Barang sudah diterima.' : !eta ? 'Jadwal tiba belum diisi.' : (late ? 'Melewati ETA ' + dur(now - eta) + ' yang lalu.' : 'Tiba dalam ' + dur(eta - now) + '.');
        $('etaBox').innerHTML =
            '<div class="icb info"><i class="fa-solid fa-calendar-check"></i></div>' +
            '<div class="big">' + esc(S.eta_text) + '</div><div class="sub">' + sub + '</div>' +
            '<div class="bar"><i style="width:' + pct.toFixed(0) + '%"></i></div>' +
            '<div class="mini"><span>Berangkat ' + esc(S.etd_text) + '</span>' + (etd && eta && eta > etd ? '<span>Durasi ' + dur(eta - etd) + '</span>' : '') + '<span>' + pct.toFixed(0) + '% perjalanan</span></div>';

        let cls = 'ok', icon = 'fa-circle-check', head = 'Tepat waktu', text = eta ? 'Belum melewati estimasi tiba. Sisa waktu ' + dur(eta - now) + '.' : 'Estimasi tiba belum diisi.';
        if (delivered) { head = 'Sudah diterima'; text = 'Pengiriman telah selesai, tidak ada keterlambatan aktif.'; }
        else if (late) { cls = 'bad'; icon = 'fa-triangle-exclamation'; head = 'Terlambat ' + dur(now - eta); text = 'Barang belum tiba setelah estimasi tiba ' + S.eta_text + '.'; }
        else if (flagged) { cls = 'warn'; icon = 'fa-clock'; head = 'Pengiriman tertunda'; text = 'Status pengiriman ditandai DELAYED.'; }
        const showCause = late || flagged;
        $('delayBox').innerHTML =
            '<div class="icb ' + cls + '"><i class="fa-solid ' + icon + '"></i></div><div class="big">' + head + '</div><div class="sub">' + text + '</div>' +
            (showCause ? '<div class="cause ' + (S.delay_cause ? '' : 'n') + '"><b>Penyebab:</b> ' + (S.delay_cause ? esc(S.delay_cause) : 'Belum dicatat pada riwayat tracking.') + '</div>' : '');
    }
    renderEta(); setInterval(renderEta, 30000);

    /* TIMELINE (terbaru di bawah, sama seperti admin) */
    $('timeline').innerHTML = events.length
        ? '<div class="tl">' + events.map((e, n) => '<div class="ti ' + (n === events.length - 1 ? 'last' : '') + '"><b>' + nice(e.status) + '</b><span>' + esc(e.location || '-') + '</span>' +
            (e.desc ? '<small>' + esc(e.desc) + '</small>' : '') + '<small>' + esc(e.time_text) + (e.by ? ' · ' + esc(e.by) : '') + '</small></div>').join('') + '</div>'
        : '<div class="nodata"><i class="fa-solid fa-route"></i>Belum ada riwayat perjalanan. Riwayat muncul setelah aktivitas tracking ditambahkan.</div>';

    /* LOKASI: koordinat, cuaca, zona waktu */
    const places = [
        { title:'LOKASI ASAL', icon:'fa-box', color:'#2563eb', name:S.origin, pos:S.origin_pos },
        { title:delivered ? 'LOKASI TERAKHIR' : 'LOKASI SAAT INI', icon:'fa-truck-fast', color:'#f59e0b', name:lastEv ? (lastEv.location || (lastPt ? 'Titik tracking terakhir' : null)) : null, pos:lastPt ? [lastPt.lat, lastPt.lng] : null },
        { title:'LOKASI TUJUAN', icon:'fa-flag-checkered', color:'#16a34a', name:S.destination, pos:S.dest_pos }
    ];
    const geoCache = {};
    async function geocode(name) {
        const key = String(name).toLowerCase();
        if (key in geoCache) return geoCache[key];
        const tries = [...new Set([String(name), String(name).split(',')[0], String(name).split(/[,\-–]/)[0]].map(x => x.trim()).filter(Boolean))];
        for (const t of tries) {
            try {
                const r = await fetch('https://geocoding-api.open-meteo.com/v1/search?name=' + encodeURIComponent(t) + '&count=1&language=id&format=json');
                if (!r.ok) continue;
                const j = await r.json();
                if (j.results && j.results[0]) return (geoCache[key] = { lat:j.results[0].latitude, lng:j.results[0].longitude });
            } catch (_) {}
        }
        return (geoCache[key] = null);
    }
    async function resolve(p) {
        if (p.pos) return (p.res = { lat:p.pos[0], lng:p.pos[1] });
        if (p.name) return (p.res = await geocode(p.name));
        return (p.res = null);
    }
    async function loadWeather(p) {
        if (!p.res) return (p.w = null);
        try {
            const u = 'https://api.open-meteo.com/v1/forecast?latitude=' + p.res.lat + '&longitude=' + p.res.lng +
                '&current=temperature_2m,relative_humidity_2m,rain,weather_code,wind_speed_10m&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max&timezone=auto&forecast_days=3';
            const r = await fetch(u);
            if (!r.ok) throw new Error('x');
            return (p.w = await r.json());
        } catch (_) { return (p.w = null); }
    }
    await Promise.all(places.map(resolve));

    /* PETA */
    (function () {
        const map = L.map('trackMap');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap contributors', maxZoom:18 }).addTo(map);
        const pin = (color, icon, cls) => L.divIcon({ className:'', html:'<div class="pin ' + (cls || '') + '" style="background:' + color + '"><i class="fa-solid ' + icon + '"></i></div>', iconSize:[34,34], iconAnchor:[17,17], popupAnchor:[0,-18] });
        const dot = L.divIcon({ className:'', html:'<div class="tp"></div>', iconSize:[10,10], iconAnchor:[5,5] });
        const O = places[0].res ? [places[0].res.lat, places[0].res.lng] : null, D = places[2].res ? [places[2].res.lat, places[2].res.lng] : null;
        const pts = events.filter(e => e.lat !== null && e.lng !== null), bounds = [];
        const color = { DELIVERED:'#16a34a', IN_TRANSIT:'#f59e0b', DELAYED:'#e5484d' }[S.status] || '#2563eb';
        const pop = (t, extra) => '<div class="pop"><strong>' + esc(S.number) + '</strong>' + t + (extra || '') + '</div>';

        function arc(a, b, n) {
            const mx = (a[0] + b[0]) / 2, my = (a[1] + b[1]) / 2, dx = b[0] - a[0], dy = b[1] - a[1], cx = mx - dy * .18, cy = my + dx * .18, out = [];
            for (let i = 0; i <= n; i++) { const t = i / n, u = 1 - t; out.push([u*u*a[0] + 2*u*t*cx + t*t*b[0], u*u*a[1] + 2*u*t*cy + t*t*b[1]]); }
            return out;
        }

        if (O) { L.marker(O, { icon:pin('#2563eb', 'fa-box') }).bindPopup(pop('Asal: ' + esc(S.origin))).addTo(map); bounds.push(O); }
        if (D) { L.marker(D, { icon:pin('#16a34a', 'fa-flag-checkered') }).bindPopup(pop('Tujuan: ' + esc(S.destination))).addTo(map); bounds.push(D); }

        if (pts.length) {
            const path = pts.map(p => [p.lat, p.lng]);
            if (O) path.unshift(O);
            L.polyline(path, { color, weight:4, opacity:.9 }).addTo(map);
            pts.forEach((p, n) => {
                bounds.push([p.lat, p.lng]);
                if (n < pts.length - 1) L.marker([p.lat, p.lng], { icon:dot }).bindPopup(pop('Lokasi: ' + esc(p.location || '-') + '<br>' + nice(p.status) + '<br><small>' + esc(p.time_text) + '</small>')).addTo(map);
            });
            const lp = pts[pts.length - 1];
            if (!delivered) {
                L.marker([lp.lat, lp.lng], { icon:pin('#f59e0b', MODEICON, 'fleet'), zIndexOffset:500 }).bindPopup(pop('Posisi terakhir: ' + esc(lp.location || '-') + '<br><small>' + esc(lp.time_text) + '</small>')).addTo(map);
                if (D) L.polyline([[lp.lat, lp.lng], D], { color:'#2f8cf5', weight:2, opacity:.7, dashArray:'7,8' }).addTo(map);
            }
        } else if (O && D) {
            const line = arc(O, D, 40);
            L.polyline(line, { color:'#2f8cf5', weight:2, opacity:.75, dashArray:'7,8' }).bindPopup(pop('Garis penghubung, bukan rute aktual.')).addTo(map);
            if (S.status === 'IN_TRANSIT') {
                const mid = line[20]; bounds.push(mid);
                L.marker(mid, { icon:pin('#f59e0b', MODEICON, 'fleet'), zIndexOffset:500 }).bindPopup(pop('Posisi diperkirakan di tengah rute.')).addTo(map);
            }
        }

        if (!bounds.length) { $('mapEmpty').classList.add('show'); map.setView([-2.5, 113], 5); return; }
        map.fitBounds(bounds, { padding:[50,50], maxZoom:9 });
        setTimeout(() => map.invalidateSize(), 250);
    })();

    /* CUACA + WAKTU LOKAL */
    const WC = { 0:['Cerah','☀️'], 1:['Cerah berawan','🌤️'], 2:['Berawan sebagian','⛅'], 3:['Berawan','☁️'], 45:['Kabut','🌫️'], 48:['Kabut tebal','🌫️'], 51:['Gerimis ringan','🌦️'], 53:['Gerimis sedang','🌦️'], 55:['Gerimis lebat','🌧️'],
        61:['Hujan ringan','🌧️'], 63:['Hujan sedang','🌧️'], 65:['Hujan lebat','🌧️'], 71:['Salju ringan','❄️'], 80:['Hujan lokal','🌦️'], 81:['Hujan lokal sedang','🌧️'], 82:['Hujan lokal lebat','⛈️'], 95:['Badai petir','⛈️'] };
    const wc = c => WC[c] || ['Kondisi tidak diketahui', '🌦️'];
    const head = p => '<div class="wt"><i style="background:' + p.color + '"><i class="fa-solid ' + p.icon + '" style="background:none;width:auto;height:auto"></i></i>' + p.title + '</div>';
    const ph = (p, msg) => '<div class="wph">' + msg + '</div>';

    await Promise.all(places.map(loadWeather));

    $('weatherGrid').innerHTML = places.map(p => {
        let body;
        if (!p.name && !p.res) body = ph(p, 'Belum ada lokasi terakhir yang tercatat.');
        else if (!p.res) body = ph(p, 'Koordinat lokasi tidak ditemukan.');
        else if (!p.w || !p.w.current) body = ph(p, 'Data cuaca belum tersedia untuk lokasi ini.');
        else {
            const c = p.w.current, d = p.w.daily, cond = wc(c.weather_code);
            body = '<div class="wcur"><span class="ic">' + cond[1] + '</span><div><div class="tmp">' + Math.round(c.temperature_2m) + '°C</div><small>' + cond[0] + '</small></div></div>' +
                '<div class="wm"><div><b>' + c.relative_humidity_2m + '%</b><small>Lembap</small></div><div><b>' + c.wind_speed_10m + '</b><small>km/j angin</small></div><div><b>' + c.rain + '</b><small>mm hujan</small></div></div>' +
                '<div class="fc">' + d.time.map((t, i) => '<div><small>' + new Date(t + 'T00:00:00').toLocaleDateString('id-ID', { weekday:'short', day:'numeric' }) + '</small><span>' + wc(d.weather_code[i])[1] + '</span><b>' + Math.round(d.temperature_2m_max[i]) + '°/' + Math.round(d.temperature_2m_min[i]) + '°</b></div>').join('') + '</div>';
        }
        return '<div class="wc">' + head(p) + '<div class="wn">' + esc(p.name || 'Belum tercatat') + '</div>' + body + '</div>';
    }).join('');

    const off = sec => { const a = Math.abs(sec), h = Math.floor(a / 3600), m = Math.floor(a % 3600 / 60); return 'UTC' + (sec < 0 ? '−' : '+') + String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0'); };
    const baseOff = places[0].w ? places[0].w.utc_offset_seconds : null;

    $('clockGrid').innerHTML = places.map((p, i) => {
        let body;
        if (!p.name && !p.res) body = ph(p, 'Belum ada lokasi terakhir yang tercatat.');
        else if (!p.w || !p.w.timezone) body = ph(p, 'Zona waktu belum dapat ditentukan untuk lokasi ini.');
        else {
            let diff = 'Zona waktu acuan (lokasi asal)';
            if (i > 0 && baseOff !== null) { const h = (p.w.utc_offset_seconds - baseOff) / 3600; diff = h === 0 ? 'Sama dengan waktu lokasi asal' : (h > 0 ? '+' : '−') + Math.abs(h) + ' jam dari waktu lokasi asal'; }
            body = '<div class="ck" data-tz="' + esc(p.w.timezone) + '">--:--:--</div><div class="cd" data-tzd="' + esc(p.w.timezone) + '"></div>' +
                '<span class="cz">' + esc(p.w.timezone) + ' · ' + off(p.w.utc_offset_seconds) + '</span><div class="cdiff">' + diff + '</div>';
        }
        return '<div class="wc dark">' + head(p) + '<div class="wn" style="color:#fff">' + esc(p.name || 'Belum tercatat') + '</div>' + body + '</div>';
    }).join('');

    function tick() {
        const now = new Date();
        document.querySelectorAll('[data-tz]').forEach(el => { try { el.textContent = new Intl.DateTimeFormat('id-ID', { timeZone:el.dataset.tz, hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:false }).format(now).replace(/\./g, ':'); } catch (_) {} });
        document.querySelectorAll('[data-tzd]').forEach(el => { try { el.textContent = new Intl.DateTimeFormat('id-ID', { timeZone:el.dataset.tzd, weekday:'long', day:'numeric', month:'long', year:'numeric' }).format(now); } catch (_) {} });
    }
    tick(); setInterval(tick, 1000);
})();
</script>
</body>
</html>
