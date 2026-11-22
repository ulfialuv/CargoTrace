<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CargoTrace Dashboard</title>
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
.tr{display:flex;align-items:center;gap:12px}
.date-box{display:flex;align-items:center;gap:9px;padding:13px 17px;background:#fff;border:1px solid var(--line);border-radius:14px;color:#3d68a8;font-weight:600;white-space:nowrap}
.date-box i{color:var(--blue)}
.alert{padding:13px 17px;margin-bottom:16px;border-radius:12px;background:#e5f7ed;color:#187344}
.notif{position:relative}
.notif-btn{position:relative;display:grid;place-items:center;width:46px;height:46px;background:#fff;border:1px solid var(--line);border-radius:14px;color:#3d68a8;font-size:16px;cursor:pointer}
.notif-count{position:absolute;top:-6px;right:-6px;min-width:20px;height:20px;padding:0 5px;border-radius:10px;background:#e5484d;color:#fff;font-size:10px;font-weight:700;display:grid;place-items:center}
.notif-panel{display:none;position:absolute;right:0;top:56px;width:330px;max-height:400px;overflow:auto;background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:0 16px 40px #1e3a8a2b;z-index:1200}
.notif-panel.open{display:block}
.notif-head{padding:14px 18px;border-bottom:1px solid #edf3fa;font-weight:700}
.notif-item{display:flex;gap:12px;padding:12px 18px;border-bottom:1px solid #f1f6fc}
.notif-ico{flex:none;display:grid;place-items:center;width:34px;height:34px;border-radius:10px}
.n-delay{background:#fff4dc;color:#b77900}.n-exception{background:#ffe9e9;color:#d33b3b}.n-status{background:#e5f1ff;color:#2876c5}
.notif-item small{font-size:10px;color:#8ca6c4}.notif-empty{padding:24px;text-align:center;color:#8ca6c4}

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
.panel.flush{padding:0;overflow:hidden}
.ph{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px}
.ph h2{font:700 17px var(--h);color:var(--navy);letter-spacing:-.3px}
.ph p{margin-top:6px;font-size:12px;color:var(--mut)}
.row{display:grid;gap:20px;margin-bottom:24px}
.r-a{grid-template-columns:1.7fr 1fr}.r-b{grid-template-columns:1.4fr 1fr}.r-c{grid-template-columns:minmax(0,.85fr) minmax(0,1.5fr);align-items:start}
.sect{margin-bottom:24px}
.chart-box{position:relative;height:250px}
.empty-box{display:grid;place-items:center;height:250px;color:#8ca6c4;text-align:center}
.dark{background:linear-gradient(160deg,#061533,#0b2a6b 70%,#1747b8);color:#fff;border:0;box-shadow:0 18px 40px #0b2a6b44}
.dark .ph h2{color:#fff}.dark .ph p{color:#9fbcea}
.gauge-foot{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px}
.gauge-foot div{padding:11px 13px;border-radius:13px;background:#ffffff14;border:1px solid #ffffff22}
.gauge-foot b{display:block;font:700 18px var(--h)}.gauge-foot small{font-size:10.5px;color:#a9c4f0}

/* PETA */
.banner{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;padding:26px 28px;color:#fff;background:linear-gradient(100deg,#061533ee,#0b2a6bcc 60%,#1d6fe855),url('https://images.unsplash.com/photo-1494412519320-aa613dfb7738?auto=format&fit=crop&w=1600&q=80') center/cover}
.banner h2{font:800 22px var(--h);letter-spacing:-.4px}.banner p{margin-top:6px;font-size:12px;color:#cfe0ff}
.kpis{display:flex;gap:10px;flex-wrap:wrap}
.kpi{padding:9px 14px;border-radius:12px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11px;color:#dbe9ff}
.kpi b{display:block;font:800 17px var(--h);color:#fff}
.toolbar{display:flex;flex-wrap:wrap;align-items:center;gap:10px 22px;padding:16px 26px;border-bottom:1px solid #edf3fa;background:#fff}
.tg{display:flex;align-items:center;flex-wrap:wrap;gap:6px}
.tl{font-size:10px;font-weight:700;letter-spacing:.9px;color:#8ca6c4;margin-right:3px}
.chip{display:inline-flex;align-items:center;gap:7px;padding:8px 13px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:#3d68a8;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.15s}
.chip:hover{background:#eef6ff}
.chip.on{background:var(--blue);border-color:var(--blue);color:#fff;box-shadow:0 6px 16px #2563eb44}
.chip .sw{width:8px;height:8px;border-radius:50%}
.search{position:relative;margin-left:auto;min-width:230px}
.search i{position:absolute;top:12px;left:13px;font-size:12px;color:#8ca6c4}
.search input{width:100%;padding:10px 14px 10px 36px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.search input:focus,.filters input:focus,.filters select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.map-wrap{position:relative;z-index:1}
#mainMap{height:620px;width:100%;background:#dfeafb}
.map-info{position:absolute;left:16px;bottom:16px;z-index:500;display:flex;flex-wrap:wrap;gap:12px;padding:9px 14px;background:#ffffffee;border:1px solid var(--line);border-radius:12px;font-size:11px;color:#3d68a8;font-weight:600;box-shadow:0 8px 20px #0b2a6b22}
.map-info span{display:flex;align-items:center;gap:6px}.map-info i{display:inline-block;width:10px;height:10px;border-radius:50%}
.map-empty{position:absolute;inset:0;z-index:600;display:none;place-items:center;background:#ffffffb8;backdrop-filter:blur(2px);text-align:center;padding:20px}
.map-empty.show{display:grid}
.map-empty div{max-width:380px;padding:26px;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 16px 40px #0b2a6b22;line-height:1.7;color:var(--mut)}
.map-empty i{display:grid;place-items:center;width:54px;height:54px;margin:0 auto 10px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:22px}
.map-empty strong{display:block;color:var(--navy);font-size:14px}
.pin{display:grid;place-items:center;width:34px;height:34px;border-radius:50%;border:3px solid #fff;color:#fff;font-size:14px;box-shadow:0 4px 14px #0005}
.pin.sq{border-radius:11px;width:32px;height:32px;font-size:13px}
.pin.fleet{animation:pulse 2s infinite}
@keyframes pulse{0%{box-shadow:0 0 0 0 #f59e0bb0}70%{box-shadow:0 0 0 14px #f59e0b00}100%{box-shadow:0 0 0 0 #f59e0b00}}
.tp{width:10px;height:10px;border-radius:50%;background:#fff;border:3px solid var(--blue)}
.leaflet-popup-content-wrapper{border-radius:14px}
.pop{min-width:180px;font:12px/1.7 'Plus Jakarta Sans',sans-serif;color:#365775}.pop strong{display:block;font-size:13px;color:var(--navy)}.pop small{color:#8ca6c4}.pop a{color:var(--blue);font-weight:700}

/* TABEL */
.filters{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}
.filters input,.filters select{padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:var(--ink);font:12px 'Plus Jakarta Sans',sans-serif}
.filters input{flex:1;min-width:200px}
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
.s-transit{background:#fff4dc;color:#b77900}.s-delivered{background:#e8f7ef;color:#26734d}.s-picked{background:#f0eaff;color:#7c3aed}.s-delayed{background:#ffe9e9;color:#c92f3a}
.late-tag{margin-left:6px;padding:3px 8px;border-radius:12px;background:#ffe9e9;color:#c92f3a;font-size:9px;font-weight:700}
td.empty{text-align:center;padding:32px;color:#8ca6c4}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top:16px;font-size:11px;color:#8ca6c4}
.pager nav svg{width:16px;height:16px}

/* PENGECUALIAN */
.dup{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px}
.dup div{padding:14px;border-radius:16px}
.dup .a{background:linear-gradient(135deg,#fff0f0,#ffe3e3);color:#c92f3a}.dup .b{background:linear-gradient(135deg,#fff7e3,#ffefc2);color:#b77900}
.dup b{display:block;font:800 26px var(--h);line-height:1}.dup small{font-size:11px;font-weight:600}
.d-item{display:flex;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid #edf3fa}
.d-item:last-child{border:0;padding-bottom:0}
.d-item strong{display:block;font-size:12.5px;color:var(--navy)}.d-item small{display:block;margin-top:3px;font-size:11px;color:#8ca6c4;line-height:1.5}
.dur{flex:none;align-self:flex-start;padding:5px 10px;border-radius:20px;background:#ffe9e9;color:#c92f3a;font-size:10px;font-weight:700}
.h-open{background:#ffe9e9;color:#c92f3a}.h-progress{background:#fff4dc;color:#b77900}.h-resolved{background:#e8f7ef;color:#26734d}
.safe{padding:22px 10px;text-align:center;color:#6c83ab}.safe i{display:grid;place-items:center;width:52px;height:52px;margin:0 auto 10px;border-radius:50%;background:#e5f7ed;color:#16a34a;font-size:22px}

/* BERITA */
.cats{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px}
.cat{border:1px solid var(--line);background:#fff;color:#5d7aa5;border-radius:999px;padding:7px 13px;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.cat.on,.cat:hover{background:var(--blue);border-color:var(--blue);color:#fff}
.news-grid{display:grid;grid-template-columns:1.15fr 1fr;gap:16px}
.n-lead{position:relative;display:block;min-height:370px;border-radius:22px;overflow:hidden;text-decoration:none;color:#fff;background:#0b2a6b}
.n-lead img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .5s}
.n-lead:hover img{transform:scale(1.05)}
.n-lead::after{content:"";position:absolute;inset:0;background:linear-gradient(to top,#061533f2 0%,#06153300 70%)}
.n-lead div{position:absolute;z-index:2;left:20px;right:20px;bottom:18px}
.n-lead h3{font:700 18px/1.4 var(--h);display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.n-lead small{display:block;margin-top:8px;font-size:11px;color:#b9d2f7}
.tagx{display:inline-block;margin-bottom:9px;padding:4px 10px;border-radius:6px;background:var(--blue);font-size:10px;font-weight:700}
.n-list{display:flex;flex-direction:column;gap:10px}
.n-item{display:flex;gap:12px;padding:10px;border:1px solid #e9f0fb;border-radius:16px;text-decoration:none;transition:.2s;flex:1}
.n-item:hover{border-color:#9fc2f7;background:#f6faff;transform:translateX(3px)}
.n-item img{flex:none;width:92px;height:100%;min-height:74px;max-height:92px;border-radius:12px;object-fit:cover;background:#e3ecfa}
.n-item h4{font-size:12.5px;line-height:1.5;font-weight:700;color:var(--navy);display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.n-item small{display:block;margin-top:5px;font-size:10.5px;color:#8ca6c4}
.n-state{grid-column:1/-1;padding:46px 10px;text-align:center;color:#8ca6c4;line-height:1.7}.n-state .btn{margin-top:10px}
.skel{border-radius:18px;background:linear-gradient(90deg,#edf3fc,#f8fbff,#edf3fc);background-size:200% 100%;animation:sk 1.3s infinite}
@keyframes sk{to{background-position:-200% 0}}

/* RESPONSIVE */
@media(max-width:1350px){.stats{grid-template-columns:repeat(3,minmax(0,1fr))}.hero-side{display:none}}
@media(max-width:1100px){.r-a,.r-b,.r-c,.news-grid{grid-template-columns:1fr}.content{padding:20px}.search{margin-left:0;width:100%}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .topline{flex-direction:column;align-items:flex-start}.hero{padding:26px 22px 96px}.hero h2{font-size:22px}
 .stats{grid-template-columns:1fr 1fr;margin:-70px 8px 20px}
 #mainMap{height:460px}.map-info{display:none}.panel{padding:18px}.toolbar{padding:14px}.notif-panel{right:auto;left:-120px;width:280px}
}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>
<body>

@php
    use Carbon\Carbon;
    $now = now();
    $rt = fn ($name) => Route::has($name) ? route($name) : '#';
    $statusClass = fn ($s) => match (strtoupper((string) $s)) {
        'DELIVERED' => 's-delivered', 'IN_TRANSIT' => 's-transit', 'PICKED_UP' => 's-picked', 'DELAYED' => 's-delayed', default => '',
    };
    $etaOf = fn ($s) => $etaCol && !empty($s->{$etaCol}) ? Carbon::parse($s->{$etaCol}) : null;
    $isLate = function ($s) use ($etaOf, $now) {
        $st = strtoupper((string) $s->status);
        if ($st === 'DELAYED') return true;
        $eta = $etaOf($s);
        return $eta && $eta->lt($now) && !in_array($st, ['DELIVERED', 'CANCELLED']);
    };
    $fmtDelay = function ($s) use ($etaOf, $now) {
        $eta = $etaOf($s);
        if (!$eta || $eta->isFuture()) return 'Terlambat';
        $h = (int) $eta->diffInHours($now, true);
        return $h >= 24 ? floor($h / 24) . ' hari ' . ($h % 24) . ' jam' : max($h, 1) . ' jam';
    };
    $handleClass = fn ($s) => match (strtolower((string) $s)) {
        'resolved', 'selesai', 'closed' => 'h-resolved',
        'in_progress', 'progress', 'diproses', 'handling' => 'h-progress',
        default => 'h-open',
    };
    $ago = fn ($t) => $t ? Carbon::parse($t)->diffForHumans() : '';
    $total = max((int) $stats['total'], 0);
    $pct = fn ($v) => $total > 0 ? min(100, round($v / $total * 100)) : 0;
    $cards = [
        ['Total Shipment', $stats['total'], 100, '#2563eb', 'fa-boxes-stacked', 'Semua pengiriman'],
        ['Delivered', $stats['delivered'], $pct($stats['delivered']), '#16a34a', 'fa-circle-check', $pct($stats['delivered']) . '% selesai'],
        ['In Transit', $stats['in_transit'], $pct($stats['in_transit']), '#f59e0b', 'fa-truck-fast', $pct($stats['in_transit']) . '% di jalan'],
        ['Delayed', $stats['delayed'], $pct($stats['delayed']), '#e5484d', 'fa-clock', 'Delay rate ' . $stats['delay_rate'] . '%'],
        ['Open Exceptions', $stats['open_exceptions'], $pct($stats['open_exceptions']), '#7c3aed', 'fa-triangle-exclamation', 'Perlu penanganan'],
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
        <div class="s-title">MENU NAVIGASI</div>
        <nav class="s-menu">
    <a href="{{ route('dashboard') }}" class="s-link active">
        <i class="fa-solid fa-border-all"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('pengiriman.index') }}" class="s-link">
        <i class="fa-solid fa-box"></i>
        <span>Pengiriman</span>
    </a>

    <a href="{{ route('pelacakan.index') }}" class="s-link">
        <i class="fa-solid fa-location-dot"></i>
        <span>Pelacakan</span>
    </a>

    <a href="{{ route('exceptions.index') }}" class="s-link">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>Pengecualian</span>
        @if($stats['open_exceptions'] > 0)
            <em class="bdg">{{ $stats['open_exceptions'] }}</em>
        @endif
    </a>

    <a href="{{ route('carriers.index') }}" class="s-link">
        <i class="fa-solid fa-building"></i>
        <span>Perusahaan Pengiriman</span>
    </a>

    <a href="{{ route('fasilitas.index') }}" class="s-link">
        <i class="fa-solid fa-warehouse"></i>
        <span>Lokasi &amp; Fasilitas</span>
    </a>

    <a href="{{ route('users.index') }}" class="s-link">
        <i class="fa-solid fa-users-gear"></i>
        <span>Pengguna &amp; Hak Akses</span>
    </a>

    <a href="{{ route('notifications.index') }}" class="s-link">
        <i class="fa-solid fa-bell"></i>
        <span>Notifikasi</span>
        @if($notifications->count())
            <em class="bdg">{{ $notifications->count() }}</em>
        @endif
    </a>

    <a href="{{ route('audit-logs.index') }}" class="s-link">
        <i class="fa-solid fa-clipboard-list"></i>
        <span>Audit Log</span>
    </a>
</nav>
    </div>

    <div class="s-user">
        <div class="who">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div><b>{{ auth()->user()->name ?? 'Administrator' }}</b><small>Shipment Tracking System</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button></form>
    </div>
</aside>

<main class="main">
<section class="content">

    <div class="topline">
        <div>
            <h1>Dashboard</h1>
            <p>Selamat datang kembali! Berikut ringkasan pengirimanmu.</p>
        </div>
        <div class="tr">
            <div class="notif">
                <button class="notif-btn" id="notifBtn" aria-label="Notifikasi" aria-expanded="false">
                    <i class="fa-regular fa-bell"></i>
                    @if($notifications->count())<span class="notif-count">{{ $notifications->count() }}</span>@endif
                </button>
                <div class="notif-panel" id="notifPanel">
                    <div class="notif-head">Notifikasi</div>
                    @forelse($notifications as $n)
                        <div class="notif-item">
                            <div class="notif-ico n-{{ $n['type'] }}"><i class="fa-solid {{ ['delay'=>'fa-clock','exception'=>'fa-triangle-exclamation','status'=>'fa-arrows-rotate'][$n['type']] }}"></i></div>
                            <div><p>{{ $n['text'] }}</p><small>{{ $ago($n['time']) }}</small></div>
                        </div>
                    @empty
                        <div class="notif-empty">Belum ada notifikasi penting.</div>
                    @endforelse
                </div>
            </div>
            <div class="date-box"><i class="fa-regular fa-calendar"></i> {{ $now->format('d M Y') }}</div>
        </div>
    </div>

    @if(session('success'))<div class="alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif

    <!-- HERO -->
    <section class="hero">
        <div>
            <span class="live"><i></i> Pemantauan langsung aktif</span>
            <h2>Pantau seluruh kargo, dari pelabuhan sampai pintu tujuan.</h2>
            <p>Satu layar untuk status pengiriman, rute di peta, keterlambatan, dan kabar terbaru dunia logistik.</p>
        </div>
        <div class="hero-side">
            <div class="hchip"><b>{{ $stats['in_transit'] }}</b>sedang dalam perjalanan</div>
            <div class="hchip"><b>{{ $stats['delayed'] }}</b>perlu perhatian</div>
        </div>
    </section>

    <!-- STATISTIK -->
    <section class="stats">
        @foreach($cards as $c)
            <div class="stat">
                <div><small>{{ $c[0] }}</small><b class="cnt" data-v="{{ (int) $c[1] }}">{{ $c[1] }}</b><em style="color:{{ $c[3] }}">{{ $c[5] }}</em></div>
                <div class="ring">
                    <svg viewBox="0 0 54 54"><circle class="rb" cx="27" cy="27" r="22"/><circle cx="27" cy="27" r="22" stroke="{{ $c[3] }}" stroke-dasharray="{{ $c[2] * 1.382 }} 138.2"/></svg>
                    <i class="fa-solid {{ $c[4] }}" style="color:{{ $c[3] }}"></i>
                </div>
            </div>
        @endforeach
    </section>

    <!-- GRAFIK -->
    <section class="row r-a">
        <div class="panel">
            <div class="ph"><div><h2>Tren Shipment Baru</h2><p>Jumlah shipment dibuat dalam 14 hari terakhir.</p></div></div>
            @if($total > 0)<div class="chart-box"><canvas id="chartTrend"></canvas></div>@else<div class="empty-box">Belum ada data.</div>@endif
        </div>
        <div class="panel">
            <div class="ph"><div><h2>Status Pengiriman</h2><p>Sebaran seluruh shipment.</p></div></div>
            @if($byStatus->count())<div class="chart-box"><canvas id="chartStatus"></canvas></div>@else<div class="empty-box">Belum ada data.</div>@endif
        </div>
    </section>
    <section class="row r-b">
        <div class="panel">
            <div class="ph"><div><h2>Beban per Carrier</h2><p>Jumlah shipment tiap perusahaan pengiriman.</p></div></div>
            @if($byCarrier->count())<div class="chart-box" style="height:230px"><canvas id="chartCarrier"></canvas></div>@else<div class="empty-box">Belum ada data.</div>@endif
        </div>
        <div class="panel dark">
            <div class="ph"><div><h2>Ketepatan Waktu</h2><p>Shipment tanpa keterlambatan.</p></div></div>
            @if($total > 0)
                <div class="chart-box" style="height:170px"><canvas id="chartGauge"></canvas></div>
                <div class="gauge-foot">
                    <div><b>{{ $delayStats['normal_pct'] }}%</b><small>Normal</small></div>
                    <div><b>{{ $delayStats['delayed_pct'] }}%</b><small>Terlambat</small></div>
                </div>
            @else<div class="empty-box" style="color:#9fbcea">Belum ada data.</div>@endif
        </div>
    </section>

    <!-- PETA -->
    <section class="panel flush sect" id="peta">
        <div class="banner">
            <div><h2>Peta Persebaran Pengiriman</h2><p>Asal, tujuan, rute, armada bergerak, pelabuhan, bandara, dan depo kargo.</p></div>
            <div class="kpis">
                <div class="kpi"><b id="kShip">0</b>shipment di peta</div>
                <div class="kpi"><b id="kFleet">0</b>armada bergerak</div>
                <div class="kpi"><b id="kFac">0</b>fasilitas</div>
            </div>
        </div>
        <div class="toolbar">
            <div class="tg" id="modeGroup">
                <span class="tl">MODA</span>
                <button class="chip on" data-mode="all">Semua <b id="cAll">(0)</b></button>
                <button class="chip" data-mode="air"><i class="fa-solid fa-plane"></i> Udara <b id="cAir">(0)</b></button>
                <button class="chip" data-mode="sea"><i class="fa-solid fa-ship"></i> Laut <b id="cSea">(0)</b></button>
                <button class="chip" data-mode="land"><i class="fa-solid fa-truck"></i> Darat <b id="cLand">(0)</b></button>
            </div>
            <div class="tg" id="regionGroup">
                <span class="tl">FOKUS WILAYAH</span>
                <button class="chip on" data-region="all">Seluruh</button>
                <button class="chip" data-region="id">Indonesia</button>
                <button class="chip" data-region="sea">Asia Tenggara</button>
                <button class="chip" data-region="eu">Eropa</button>
            </div>
            <div class="tg" id="layerGroup">
                <span class="tl">FASILITAS</span>
                <button class="chip on" data-layer="fleet"><span class="sw" style="background:#f59e0b"></span> Armada Bergerak</button>
                <button class="chip on" data-layer="port"><span class="sw" style="background:#0f9d9a"></span> Pelabuhan</button>
                <button class="chip on" data-layer="air"><span class="sw" style="background:#2563eb"></span> Bandara</button>
                <button class="chip on" data-layer="depot"><span class="sw" style="background:#6d5bd0"></span> Depo Kargo</button>
            </div>
            <div class="search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="mapQ" placeholder="Cari fasilitas, shipment, atau lokasi..." aria-label="Cari di peta">
            </div>
        </div>
        <div class="map-wrap">
            <div id="mainMap"></div>
            <div class="map-empty" id="mapEmpty">
                <div><i class="fa-solid fa-map-location-dot"></i><strong>Belum ada titik yang bisa digambar</strong>
                Peta butuh koordinat. Isi <b>latitude/longitude</b> pada fasilitas, atau pastikan nama kota asal &amp; tujuan shipment cocok dengan nama fasilitas / kota yang dikenal.</div>
            </div>
            <div class="map-info">
                <span><i style="background:#2563eb"></i> Origin</span>
                <span><i style="background:#16a34a"></i> Destination</span>
                <span><i style="background:#f59e0b"></i> Armada darat</span>
                <span><i style="background:#0f9d9a"></i> Pelabuhan</span>
                <span><i style="background:#2563eb;border-radius:3px"></i> Bandara</span>
                <span><i style="background:#6d5bd0"></i> Depo</span>
            </div>
        </div>
    </section>

    <!-- TABEL -->
    <section class="panel sect" id="daftar">
        <div class="ph">
            <div><h2>Pengiriman Terbaru</h2><p>Cari berdasarkan nomor, customer, atau rute.</p></div>
            <a href="{{ url('/pengiriman/create') }}" class="btn"><i class="fa-solid fa-plus"></i> Tambah Shipment</a>
        </div>
        <form method="GET" action="{{ route('dashboard') }}#daftar" class="filters">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nomor, customer, origin, destination..." aria-label="Pencarian">
            <select name="status" aria-label="Status"><option value="">Semua status</option>
                @foreach($statuses as $st)<option value="{{ $st }}" @selected(request('status') === $st)>{{ str_replace('_', ' ', $st) }}</option>@endforeach
            </select>
            <select name="carrier" aria-label="Carrier"><option value="">Semua carrier</option>
                @foreach($carriers as $c)<option value="{{ $c }}" @selected(request('carrier') === $c)>{{ $c }}</option>@endforeach
            </select>
            <select name="delay" aria-label="Keterlambatan"><option value="">Semua</option>
                <option value="late" @selected(request('delay') === 'late')>Terlambat</option>
                <option value="ontime" @selected(request('delay') === 'ontime')>Tidak terlambat</option>
            </select>
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a href="{{ route('dashboard') }}#daftar" class="btn btn-light">Reset</a>
        </form>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Shipment</th><th>Customer</th><th>Carrier</th><th>Rute</th><th>Status</th><th>ETA</th><th></th></tr></thead>
                <tbody>
                @forelse($shipments as $s)
                    @php $eta = $etaOf($s); @endphp
                    <tr>
                        <td><strong>{{ $s->shipment_number }}</strong></td>
                        <td>{{ $s->customer_name }}</td>
                        <td>{{ $s->carrier }}</td>
                        <td><div class="route">{{ $s->origin }} <i class="fa-solid fa-arrow-right"></i> {{ $s->destination }}</div></td>
                        <td>
                            <span class="status {{ $statusClass($s->status) }}">{{ str_replace('_', ' ', $s->status) }}</span>
                            @if($isLate($s) && strtoupper((string) $s->status) !== 'DELAYED')<span class="late-tag">LATE</span>@endif
                        </td>
                        <td>{{ $eta ? $eta->format('d M Y') : '-' }}</td>
                        <td><a class="btn btn-light" href="{{ url('/pengiriman/' . $s->id) }}"><i class="fa-solid fa-eye"></i> Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada shipment yang cocok. Ubah filter atau tambah shipment baru.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pager">
            <span>Menampilkan {{ $shipments->firstItem() ?? 0 }}–{{ $shipments->lastItem() ?? 0 }} dari {{ $shipments->total() }} shipment</span>
            @if($shipments->hasPages())<div>{{ $shipments->fragment('daftar')->links() }}</div>@endif
        </div>
    </section>

    <!-- PENGECUALIAN + BERITA -->
    <section class="row r-c">
        <div class="panel" id="delay">
            <div class="ph"><div><h2>Ringkasan Pengecualian</h2><p>Keterlambatan dan masalah yang perlu dicek.</p></div></div>
            <div class="dup">
                <div class="a"><b>{{ $stats['open_exceptions'] }}</b><small>Exception terbuka</small></div>
                <div class="b"><b>{{ $stats['delayed'] }}</b><small>Melewati ETA</small></div>
            </div>
            @foreach($exceptionList as $e)
                @php $handling = $e->status ?? $e->handling_status ?? 'open'; $no = $exceptionShipmentNumbers[$e->shipment_id] ?? '#' . $e->shipment_id; @endphp
                <div class="d-item">
                    <div>
                        <strong>{{ $no }} · {{ $e->type ?? $e->exception_type ?? 'Exception' }}</strong>
                        <small>{{ $e->description ?? $e->message ?? 'Tidak ada keterangan.' }}</small>
                        <small>{{ $ago($e->created_at) }}</small>
                    </div>
                    <span class="dur {{ $handleClass($handling) }}">{{ strtoupper(str_replace('_', ' ', $handling)) }}</span>
                </div>
            @endforeach
            @foreach($delayedList as $s)
                <div class="d-item">
                    <div>
                        <strong>{{ $s->shipment_number }}</strong>
                        <small>{{ $s->customer_name }} · {{ $s->origin }} → {{ $s->destination }}</small>
                        <small>ETA {{ $etaOf($s) ? $etaOf($s)->format('d M Y H:i') : '-' }}</small>
                    </div>
                    <span class="dur">{{ $fmtDelay($s) }}</span>
                </div>
            @endforeach
            @if($exceptionList->isEmpty() && $delayedList->isEmpty())
                <div class="safe"><i class="fa-solid fa-shield-check"></i>Semua aman.<br>Tidak ada pengecualian maupun keterlambatan.</div>
            @endif
        </div>

        <div class="panel" id="berita">
            <div class="ph">
                <div><h2>Berita Terkini</h2><p>Kabar logistik, ekonomi, dan dunia.</p></div>
                <a href="{{ $rt('berita.index') }}" class="btn btn-light">Lihat semua</a>
            </div>
            <div class="cats" id="newsCats">
                <button class="cat on" data-c="all">Semua</button>
                <button class="cat" data-c="logistik">Logistik</button>
                <button class="cat" data-c="transportasi">Transportasi</button>
                <button class="cat" data-c="ekonomi">Ekonomi</button>
                <button class="cat" data-c="nasional">Nasional</button>
                <button class="cat" data-c="internasional">Dunia</button>
                <button class="cat" data-c="teknologi">Teknologi</button>
            </div>
            <div class="news-grid" id="newsBody"></div>
        </div>
    </section>

</section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
const $ = id => document.getElementById(id);

/* ===== NOTIFIKASI + COUNT-UP ===== */
(function () {
    const b = $('notifBtn'), p = $('notifPanel');
    b.addEventListener('click', e => { e.stopPropagation(); b.setAttribute('aria-expanded', p.classList.toggle('open')); });
    document.addEventListener('click', e => { if (!p.contains(e.target)) { p.classList.remove('open'); b.setAttribute('aria-expanded', false); } });
    document.querySelectorAll('.cnt').forEach(el => {
        const v = +el.dataset.v; if (!v) return;
        const t0 = performance.now();
        (function tick(t) { const k = Math.min(1, (t - t0) / 900); el.textContent = Math.round(v * (1 - Math.pow(1 - k, 3))); if (k < 1) requestAnimationFrame(tick); })(t0);
    });
})();

/* ===== GRAFIK ===== */
try {
    const byStatus = @json($byStatus), byCarrier = @json($byCarrier), delay = @json($delayStats), trend = @json($trend);
    const total = {{ (int) $stats['total'] }};
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#6c83ab';
    const sc = { CREATED:'#38bdf8', BOOKED:'#8b7be8', PICKED_UP:'#7c3aed', IN_TRANSIT:'#f59e0b', DELIVERED:'#16a34a', DELAYED:'#e5484d', CANCELLED:'#94a3b8' };
    const centerText = (big, small, color, sub) => ({ id:'ct', afterDraw(c) {
        const { ctx, chartArea: a } = c, x = (a.left + a.right) / 2, y = c.config.options.rotation ? a.bottom - 18 : (a.top + a.bottom) / 2;
        ctx.save(); ctx.textAlign = 'center'; ctx.fillStyle = color; ctx.font = '800 30px Sora, sans-serif'; ctx.fillText(big, x, y);
        ctx.font = '600 11px "Plus Jakarta Sans", sans-serif'; ctx.fillStyle = sub; ctx.fillText(small, x, y + 20); ctx.restore();
    }});

    if ($('chartTrend')) new Chart($('chartTrend'), { type:'line',
        data:{ labels:trend.map(t => t.label), datasets:[{ data:trend.map(t => t.value), borderColor:'#2563eb', borderWidth:3, tension:.42, fill:true, pointRadius:0, pointHoverRadius:6, pointHoverBackgroundColor:'#fff', pointHoverBorderColor:'#2563eb', pointHoverBorderWidth:3,
            backgroundColor: c => { const a = c.chart.chartArea; if (!a) return '#2563eb22'; const g = c.chart.ctx.createLinearGradient(0, a.top, 0, a.bottom); g.addColorStop(0, '#2563eb55'); g.addColorStop(1, '#2563eb00'); return g; } }] },
        options:{ maintainAspectRatio:false, interaction:{ mode:'index', intersect:false }, plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label:c => c.parsed.y + ' shipment' } } },
            scales:{ y:{ beginAtZero:true, ticks:{ precision:0 }, grid:{ color:'#edf3fa' }, border:{ display:false } }, x:{ grid:{ display:false }, ticks:{ maxTicksLimit:7 }, border:{ display:false } } } } });

    if ($('chartStatus')) { const k = Object.keys(byStatus);
        new Chart($('chartStatus'), { type:'doughnut',
            data:{ labels:k.map(x => x.replace('_',' ')), datasets:[{ data:k.map(x => byStatus[x]), backgroundColor:k.map(x => sc[x] || '#60a5fa'), borderWidth:4, borderColor:'#fff', borderRadius:10, spacing:2 }] },
            options:{ maintainAspectRatio:false, cutout:'72%', plugins:{ legend:{ position:'bottom', labels:{ boxWidth:9, usePointStyle:true, padding:14 } } } },
            plugins:[centerText(String(total), 'Total', '#061533', '#8ca6c4')] }); }

    if ($('chartCarrier')) { const k = Object.keys(byCarrier);
        new Chart($('chartCarrier'), { type:'bar',
            data:{ labels:k, datasets:[{ data:k.map(x => byCarrier[x]), borderRadius:10, maxBarThickness:22,
                backgroundColor: c => { const a = c.chart.chartArea; if (!a) return '#2563eb'; const g = c.chart.ctx.createLinearGradient(a.left, 0, a.right, 0); g.addColorStop(0, '#38bdf8'); g.addColorStop(1, '#1d4ed8'); return g; } }] },
            options:{ indexAxis:'y', maintainAspectRatio:false, plugins:{ legend:{ display:false } },
                scales:{ x:{ beginAtZero:true, ticks:{ precision:0 }, grid:{ color:'#edf3fa' }, border:{ display:false } }, y:{ grid:{ display:false }, border:{ display:false } } } } }); }

    if ($('chartGauge')) new Chart($('chartGauge'), { type:'doughnut',
        data:{ labels:['Normal','Terlambat'], datasets:[{ data:[delay.normal_pct, delay.delayed_pct + delay.exception_pct], backgroundColor:['#38bdf8','#e5484d'], borderWidth:0, borderRadius:8, spacing:2 }] },
        options:{ maintainAspectRatio:false, rotation:-90, circumference:180, cutout:'74%', plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label:c => c.label + ': ' + c.parsed + '%' } } } },
        plugins:[centerText(delay.normal_pct + '%', 'tepat waktu', '#ffffff', '#9fbcea')] });
} catch (e) { console.error('Grafik gagal dimuat:', e); }

/* ===== BERITA ===== */
(function () {
    const endpoint = @json($rt('berita.api')), body = $('newsBody');
    const FALLBACK = 'data:image/svg+xml;utf8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400"><defs><linearGradient id="g" x1="0" x2="1" y1="0" y2="1"><stop offset="0" stop-color="#0b2a6b"/><stop offset="1" stop-color="#2563eb"/></linearGradient></defs><rect width="600" height="400" fill="url(#g)"/><text x="300" y="210" fill="#ffffff99" font-family="Arial" font-size="26" text-anchor="middle">CargoTrace News</text></svg>');
    const err = 'onerror="this.onerror=null;this.src=\'' + FALLBACK + '\'"';
    let cat = 'all', seq = 0;
    const safe = v => { try { const u = new URL(v, location.origin); return ['http:','https:'].includes(u.protocol) ? u.href : ''; } catch (_) { return ''; } };
    const src = a => a.source && typeof a.source === 'object' ? a.source.name : (a.source || '');
    const dt = v => { const d = new Date(v); return isNaN(d) ? '' : new Intl.DateTimeFormat('id-ID', { day:'numeric', month:'short', year:'numeric' }).format(d); };
    const pic = a => esc(safe(a.image) || FALLBACK);
    const msg = h => { body.innerHTML = '<div class="n-state">' + h + '</div>'; };

    function renderNews(list) {
        const lead = list[0], rest = list.slice(1, 5);
        body.innerHTML = '<a class="n-lead" href="' + esc(safe(lead.url) || '#') + '" target="_blank" rel="noopener noreferrer"><img src="' + pic(lead) + '" alt="" ' + err + '>' +
            '<div><span class="tagx">' + esc(src(lead) || 'Berita utama') + '</span><h3>' + esc(lead.title) + '</h3><small>' + dt(lead.publishedAt) + '</small></div></a>' +
            '<div class="n-list">' + rest.map(a => '<a class="n-item" href="' + esc(safe(a.url) || '#') + '" target="_blank" rel="noopener noreferrer"><img src="' + pic(a) + '" alt="" loading="lazy" ' + err + '>' +
            '<div><h4>' + esc(a.title) + '</h4><small>' + esc(src(a)) + ' · ' + dt(a.publishedAt) + '</small></div></a>').join('') + '</div>';
    }
    async function load() {
        const my = ++seq;
        body.innerHTML = '<div class="skel" style="min-height:370px"></div><div style="display:grid;gap:10px"><div class="skel"></div><div class="skel"></div><div class="skel"></div></div>';
        try {
            const url = new URL(endpoint, location.origin); url.searchParams.set('category', cat);
            const res = await fetch(url, { headers:{ 'Accept':'application/json' } }), data = await res.json();
            if (my !== seq) return;
            if (!res.ok || data.success === false) throw new Error(data.message || 'HTTP ' + res.status);
            const list = Array.isArray(data.articles) ? data.articles : [];
            list.length ? renderNews(list) : msg('Belum ada berita untuk kategori ini.<br>Coba kategori lain.');
        } catch (e) {
            if (my !== seq) return;
            msg(esc(e.message || 'Berita belum dapat dimuat.') + '<br><button class="btn" id="retry" type="button">Coba lagi</button>');
            $('retry').addEventListener('click', load);
        }
    }
    $('newsCats').addEventListener('click', e => {
        const b = e.target.closest('.cat'); if (!b) return;
        document.querySelectorAll('#newsCats .cat').forEach(x => x.classList.toggle('on', x === b));
        cat = b.dataset.c; load();
    });
    load();
})();

/* ===== PETA ===== */
(function () {
    const facilities = @json($facilities), shipData = @json($mapData);

    const cities = {
        'banda aceh':[5.5483,95.3238],'aceh':[5.5483,95.3238],'sabang':[5.8897,95.3164],'medan':[3.5952,98.6722],'kabanjahe':[3.1001,98.4908],'belawan':[3.7826,98.6930],'binjai':[3.6001,98.4854],
        'padang':[-0.9471,100.4172],'pekanbaru':[0.5071,101.4478],'dumai':[1.6665,101.4478],'batam':[1.0456,104.0305],'tanjung pinang':[0.9186,104.4586],'jambi':[-1.6101,103.6131],'palembang':[-2.9761,104.7754],
        'bengkulu':[-3.8004,102.2655],'bandar lampung':[-5.4292,105.2610],'lampung':[-5.4292,105.2610],'jakarta':[-6.2088,106.8456],'tanjung priok':[-6.1050,106.8800],'bogor':[-6.5971,106.8060],
        'depok':[-6.4025,106.7942],'bekasi':[-6.2383,106.9756],'tangerang':[-6.1783,106.6319],'bandung':[-6.9175,107.6191],'cirebon':[-6.7320,108.5523],'yogyakarta':[-7.7956,110.3695],
        'jogja':[-7.7956,110.3695],'solo':[-7.5666,110.8167],'semarang':[-6.9667,110.4167],'surabaya':[-7.2575,112.7521],'malang':[-7.9666,112.6326],'banyuwangi':[-8.2192,114.3691],
        'denpasar':[-8.6705,115.2126],'bali':[-8.4095,115.1889],'mataram':[-8.5833,116.1167],'kupang':[-10.1772,123.6070],'pontianak':[-0.0263,109.3425],'banjarmasin':[-3.3186,114.5944],
        'samarinda':[-0.5022,117.1536],'balikpapan':[-1.2379,116.8529],'makassar':[-5.1477,119.4327],'manado':[1.4748,124.8421],'palu':[-0.9,119.8707],'kendari':[-3.9985,122.512],
        'ambon':[-3.6954,128.1814],'sorong':[-0.8762,131.2558],'jayapura':[-2.5916,140.669],
        'singapura':[1.3521,103.8198],'singapore':[1.3521,103.8198],'kuala lumpur':[3.139,101.6869],'malaysia':[3.139,101.6869],'penang':[5.4141,100.3288],'port klang':[3.0000,101.4000],
        'bangkok':[13.7563,100.5018],'thailand':[13.7563,100.5018],'ho chi minh':[10.8231,106.6297],'hanoi':[21.0278,105.8342],'vietnam':[14.0583,108.2772],'manila':[14.5995,120.9842],
        'colombo':[6.9271,79.8612],'sri lanka':[7.8731,80.7718],'chennai':[13.0827,80.2707],'mumbai':[19.076,72.8777],'india':[20.5937,78.9629],'dubai':[25.2048,55.2708],
        'hong kong':[22.3193,114.1694],'shanghai':[31.2304,121.4737],'tiongkok':[35.8617,104.1954],'china':[35.8617,104.1954],'tokyo':[35.6762,139.6503],'jepang':[36.2048,138.2529],
        'seoul':[37.5665,126.978],'busan':[35.1796,129.0756],'sydney':[-33.8688,151.2093],'australia':[-25.2744,133.7751],
        'berlin':[52.52,13.405],'jerman':[52.52,13.405],'germany':[52.52,13.405],'hamburg':[53.5511,9.9937],'rotterdam':[51.9244,4.4777],'amsterdam':[52.3676,4.9041],'london':[51.5072,-0.1276],'paris':[48.8566,2.3522],
        'new york':[40.7128,-74.006],'los angeles':[34.0522,-118.2437]
    };
    const cityCoord = name => {
        if (!name) return null;
        const k = String(name).toLowerCase().trim();
        if (cities[k]) return cities[k];
        for (const c in cities) if (k.includes(c)) return cities[c];
        return null;
    };
    const num = v => (v === null || v === '' || v === undefined) ? NaN : parseFloat(v);
    const modeOf = t => { t = String(t || '').toLowerCase(); if (/air|udara|plane|pesawat|flight/.test(t)) return 'air'; if (/sea|laut|ship|kapal|ocean|vessel|maritim/.test(t)) return 'sea'; return 'land'; };
    const kindOf = f => { const t = String((f.type || '') + ' ' + (f.name || '')).toLowerCase(); return /port|pelabuhan|seaport|dermaga/.test(t) ? 'port' : (/air|bandara|apt|airport/.test(t) ? 'air' : 'depot'); };

    const META = {
        port:{ label:'Pelabuhan', icon:'fa-anchor', color:'#0f9d9a', mode:'sea' },
        air:{ label:'Bandara', icon:'fa-plane', color:'#2563eb', mode:'air' },
        depot:{ label:'Depo Kargo', icon:'fa-warehouse', color:'#6d5bd0', mode:'land' }
    };
    const FLEET = { land:{ icon:'fa-truck-fast', color:'#f59e0b' }, air:{ icon:'fa-plane', color:'#2563eb' }, sea:{ icon:'fa-ship', color:'#0f9d9a' } };
    const REGIONS = { id:[[-11.5,94],[6.5,141.5]], sea:[[-11.5,92],[24,126]], eu:[[34,-11],[62,32]] };

    function arc(a, b, n) {
        const mx = (a[0] + b[0]) / 2, my = (a[1] + b[1]) / 2, dx = b[0] - a[0], dy = b[1] - a[1], cx = mx - dy * .18, cy = my + dx * .18, pts = [];
        for (let i = 0; i <= n; i++) { const t = i / n, u = 1 - t; pts.push([u*u*a[0] + 2*u*t*cx + t*t*b[0], u*u*a[1] + 2*u*t*cy + t*t*b[1]]); }
        return pts;
    }

    /* data fasilitas */
    const facs = [];
    facilities.forEach(f => {
        let lat = num(f.latitude ?? f.lat), lng = num(f.longitude ?? f.lng ?? f.lon);
        if (isNaN(lat) || isNaN(lng)) { const c = cityCoord(f.city) || cityCoord(f.name) || cityCoord(f.address); if (!c) return; lat = c[0]; lng = c[1]; }
        const k = kindOf(f);
        facs.push({ f, k, lat, lng, mode:META[k].mode, text:[f.name, f.facility_code, f.city, f.province, f.address, f.country, f.type].join(' ').toLowerCase() });
    });

    /* data shipment */
    const ships = [];
    shipData.forEach(s => {
        const o = s.origin_pos || cityCoord(s.origin), d = s.dest_pos || cityCoord(s.destination), pts = s.points || [];
        if (!o && !d && !pts.length) return;
        const mode = modeOf(s.transport_type), inTransit = String(s.status).toUpperCase() === 'IN_TRANSIT';
        let line = null, pos = null;
        if (pts.length > 1) line = pts.map(p => [p.lat, p.lng]);
        else if (o && d) line = arc(o, d, 40);
        if (inTransit) {
            if (pts.length) pos = [pts[pts.length-1].lat, pts[pts.length-1].lng];
            else if (line) pos = line[Math.floor(line.length * (.3 + ((Number(s.id) * 37) % 40) / 100))];
            else pos = o || d;
        }
        ships.push({ s, o, d, pts, line, pos, mode, exact:pts.length > 0, text:[s.number, s.customer, s.carrier, s.origin, s.destination, s.status].join(' ').toLowerCase() });
    });

    /* peta */
    const map = L.map('mainMap', { worldCopyJump:true }).setView([2, 105], 4);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap contributors', maxZoom:18 }).addTo(map);
    const layer = L.layerGroup().addTo(map);
    setTimeout(() => map.invalidateSize(), 250);

    const pin = (color, icon, cls) => L.divIcon({ className:'', html:'<div class="pin ' + (cls || '') + '" style="background:' + color + '"><i class="fa-solid ' + icon + '"></i></div>', iconSize:[34,34], iconAnchor:[17,17], popupAnchor:[0,-18] });
    const dot = L.divIcon({ className:'', html:'<div class="tp"></div>', iconSize:[10,10], iconAnchor:[5,5] });
    const lineColor = s => ({ DELIVERED:'#16a34a', IN_TRANSIT:'#f59e0b', DELAYED:'#e5484d' }[String(s).toUpperCase()] || '#2f8cf5');

    const st = { mode:'all', q:'', layers:{ fleet:true, port:true, air:true, depot:true } };
    const modeOk = m => st.mode === 'all' || m === st.mode;
    const visFac = () => facs.filter(i => st.layers[i.k] && modeOk(i.mode) && i.text.includes(st.q));
    const visShip = () => ships.filter(i => modeOk(i.mode) && i.text.includes(st.q));

    const info = (s, extra) => '<div class="pop"><strong>' + esc(s.number) + '</strong>Customer: ' + esc(s.customer) + '<br>Carrier: ' + esc(s.carrier || '-') + '<br>Rute: ' + esc(s.origin) + ' → ' + esc(s.destination) +
        '<br>Status: ' + esc(String(s.status).replace('_',' ')) + '<br><a href="/pelacakan/' + encodeURIComponent(s.id) + '">Lihat tracking →</a>' + (extra || '') + '</div>';

    function render(fit) {
        layer.clearLayers();
        const bounds = [], vf = visFac(), vs = visShip();
        let fleet = 0;

        vs.forEach(i => {
            const s = i.s, color = lineColor(s.status);
            if (i.line) {
                L.polyline(i.line, { color, weight:i.exact ? 3 : 2, opacity:i.exact ? .85 : .7, dashArray:i.exact ? null : '7,8' }).bindPopup(info(s, i.exact ? '' : '<br><small>Garis penghubung, bukan rute aktual.</small>')).addTo(layer);
            }
            if (i.o) { L.marker(i.o, { icon:pin('#2563eb','fa-box') }).bindPopup(info(s)).addTo(layer); bounds.push(i.o); }
            if (i.d) { L.marker(i.d, { icon:pin('#16a34a','fa-flag-checkered') }).bindPopup(info(s)).addTo(layer); bounds.push(i.d); }
            if (i.exact) i.pts.forEach(p => { L.marker([p.lat, p.lng], { icon:dot }).bindPopup('<div class="pop"><strong>' + esc(s.number) + '</strong>Lokasi: ' + esc(p.location || '-') + '<br>Status: ' + esc(String(p.status || s.status).replace('_',' ')) + '<br><small>' + esc(p.time || '') + '</small></div>').addTo(layer); bounds.push([p.lat, p.lng]); });
            if (st.layers.fleet && i.pos) {
                const m = FLEET[i.mode];
                L.marker(i.pos, { icon:pin(m.color, m.icon, 'fleet'), zIndexOffset:500 }).bindPopup(info(s, i.exact ? '' : '<br><small>Posisi diperkirakan di sepanjang rute.</small>')).addTo(layer);
                bounds.push(i.pos); fleet++;
            }
        });

        vf.forEach(i => {
            const f = i.f, m = META[i.k], code = f.facility_code || f.code;
            L.marker([i.lat, i.lng], { icon:pin(m.color, m.icon, 'sq') }).bindPopup('<div class="pop"><strong>' + esc(f.name || 'Tanpa nama') + '</strong><small>' + m.label.toUpperCase() + (code ? ' · ' + esc(String(code).toUpperCase()) : '') + '</small><br>' + esc([f.city, f.province, f.country].filter(Boolean).join(', ')) + (f.address ? '<br>' + esc(f.address) : '') + '</div>').addTo(layer);
            bounds.push([i.lat, i.lng]);
        });

        $('kShip').textContent = vs.length; $('kFleet').textContent = fleet; $('kFac').textContent = vf.length;
        $('mapEmpty').classList.toggle('show', !bounds.length && !st.q && st.mode === 'all');
        if (fit && bounds.length) map.fitBounds(bounds, { padding:[60,60], maxZoom:7 });
    }

    /* hitungan chip moda */
    const cnt = { air:0, sea:0, land:0 };
    facs.forEach(i => cnt[i.mode]++); ships.forEach(i => cnt[i.mode]++);
    $('cAll').textContent = '(' + (facs.length + ships.length) + ')';
    $('cAir').textContent = '(' + cnt.air + ')'; $('cSea').textContent = '(' + cnt.sea + ')'; $('cLand').textContent = '(' + cnt.land + ')';

    function single(groupId, attr, fn) {
        const g = $(groupId);
        g.querySelectorAll('.chip').forEach(b => b.addEventListener('click', () => {
            g.querySelectorAll('.chip').forEach(x => x.classList.remove('on')); b.classList.add('on'); fn(b.dataset[attr]);
        }));
    }
    single('modeGroup', 'mode', v => { st.mode = v; render(true); });
    single('regionGroup', 'region', v => { if (v === 'all') render(true); else map.flyToBounds(REGIONS[v], { duration:1.1 }); });
    document.querySelectorAll('#layerGroup .chip').forEach(b => b.addEventListener('click', () => {
        const k = b.dataset.layer; st.layers[k] = !st.layers[k]; b.classList.toggle('on', st.layers[k]); render(false);
    }));
    $('mapQ').addEventListener('input', e => { st.q = e.target.value.toLowerCase().trim(); render(true); });

    render(true);
})();
</script>
</body>
</html>
