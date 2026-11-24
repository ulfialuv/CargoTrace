<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Lokasi &amp; Fasilitas | CargoTrace</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="preconnect" href="https://tile.openstreetmap.org" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
:root{--navy:#061533;--blue:#2563eb;--sky:#38bdf8;--ink:#12306a;--mut:#6c83ab;--line:#e0e9fa;--bg:#edf3ff;--h:'Sora',sans-serif}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--ink);font-size:13px}
a{color:inherit;text-decoration:none}
button,input,select,textarea{font-family:inherit}
:focus-visible{outline:2px solid var(--blue);outline-offset:2px}

/* ===== SIDEBAR ===== */
.sidebar{position:fixed;inset:0 auto 0 0;width:272px;padding:26px 18px 18px;display:flex;flex-direction:column;background:linear-gradient(180deg,#061533 0%,#0a2257 65%,#0f3a8f 100%);color:#fff;z-index:1000;overflow-y:auto}
.sidebar::before{content:"";position:absolute;width:300px;height:300px;top:-140px;right:-150px;border-radius:50%;background:radial-gradient(#38bdf860,#0000 70%);pointer-events:none}
.brand{display:flex;align-items:center;gap:11px;padding:0 8px;position:relative}
.brand-logo{display:grid;place-items:center;width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-size:20px;box-shadow:0 8px 22px #2563eb77}
.brand-name{font:800 22px var(--h);letter-spacing:-.5px}
.brand-sub{margin:8px 8px 26px;font-size:9.5px;letter-spacing:.8px;color:#9fbcea;position:relative}
.s-title{margin:0 12px 10px;font-size:10px;font-weight:700;letter-spacing:1.3px;color:#6f95d6}
.s-wrap{position:relative;margin-bottom:22px}
.s-menu{display:flex;flex-direction:column;gap:4px;position:relative}
.s-link{display:flex;align-items:center;gap:13px;min-height:44px;padding:10px 14px;border-radius:13px;color:#dce8ff;font-weight:500;transition:.2s}
.s-link i{width:18px;text-align:center;font-size:14px;color:#8fb3ec}
.s-link:hover{background:#ffffff18}
.s-link.active{background:linear-gradient(90deg,#2563eb,#3b8bf5);color:#fff;font-weight:700;box-shadow:0 10px 24px #2563eb55}
.s-link.active i{color:#fff}
.s-user{margin-top:auto;position:relative;padding:14px;border-radius:16px;background:#ffffff14;border:1px solid #ffffff2b}
.s-user .who{display:flex;align-items:center;gap:11px}
.avatar{display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-weight:800}
.s-user b{display:block;font-size:12.5px}.s-user small{font-size:10px;color:#a9c4f0}
.s-user button{width:100%;margin-top:12px;padding:9px;border:1px solid #ffffff33;border-radius:10px;background:transparent;color:#dce8ff;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.s-user button:hover{background:#ffffff1c}

/* ===== MAIN ===== */
.main{margin-left:272px;min-height:100vh;background:radial-gradient(circle at 100% 0,#cfe3ff 0,#0000 380px),radial-gradient(circle at 0 100%,#d7e8ff 0,#0000 340px),var(--bg)}
.content{padding:26px 32px 44px}
.alert{padding:13px 17px;margin-bottom:16px;border-radius:12px;font-size:12px}
.alert-success{background:#e5f7ed;color:#187344}
.alert-error{background:#ffebeb;color:#c92f3a}

/* HERO */
.hero{position:relative;overflow:hidden;padding:36px 40px;border-radius:30px;color:#fff;margin-bottom:22px;
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 55%,#1d6fe870 100%),url('https://images.unsplash.com/photo-1494412519320-aa613dfb7738?auto=format&fit=crop&w=1800&q=80') center/cover,#0b2a6b;box-shadow:0 26px 54px #0b2a6b46}
.hero::after{content:"";position:absolute;width:380px;height:380px;right:-110px;top:-190px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%);pointer-events:none}
.hero>*{position:relative;z-index:2}
.hero-row{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap}
.crumb{font-size:12px;color:#cfe0ff;margin-bottom:10px}
.hero h1{font:800 34px/1.2 var(--h);letter-spacing:-1px}
.hero p{margin-top:12px;max-width:580px;line-height:1.8;color:#dbe9ff;font-size:14px}
.hbtn{display:inline-flex;align-items:center;gap:9px;padding:15px 22px;border:0;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;color:#fff;background:linear-gradient(135deg,#3b8bf5,#1d6fe8);box-shadow:0 10px 24px #1d6fe855}
.hbtn:hover{filter:brightness(1.08)}
.hmini{display:flex;gap:10px;flex-wrap:wrap;margin-top:22px}
.hmini span{display:inline-flex;align-items:center;gap:8px;padding:9px 14px;border-radius:12px;background:#ffffff1c;border:1px solid #ffffff35;font-size:11.5px;font-weight:600;color:#e4efff}
.hmini b{font:800 14px var(--h);color:#fff}

/* STAT */
.cx-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:22px}
.cx-stat{position:relative;display:block;padding:24px 22px;background:#fff;border:1px solid #e9f0fb;border-radius:24px;box-shadow:0 10px 28px #2563eb0f;transition:.25s;overflow:hidden}
.cx-stat::after{content:"";position:absolute;right:-30px;top:-30px;width:100px;height:100px;border-radius:50%;background:radial-gradient(#2563eb14,#0000 70%)}
a.cx-stat:hover{transform:translateY(-4px);border-color:#b9d2f7}
.cx-ico{display:grid;place-items:center;width:56px;height:56px;border-radius:16px;font-size:22px;margin-bottom:18px}
.cx-stat b{display:block;font:800 30px var(--h);color:var(--navy);line-height:1}
.cx-stat strong{display:block;margin-top:9px;font-size:14px;font-weight:700;color:var(--navy)}
.cx-stat small{display:block;margin-top:4px;font-size:12px;color:#8ca6c4}
.cx-go{position:absolute;right:14px;top:50%;transform:translateY(-50%);display:grid;place-items:center;width:26px;height:26px;border-radius:50%;background:#fff;border:1px solid #e0e9fa;color:#8ca6c4;font-size:10px;z-index:2}

/* PANEL */
.panel{padding:28px;background:#fff;border:1px solid var(--line);border-radius:28px;box-shadow:0 14px 36px #2563eb0f;margin-bottom:22px;min-width:0}
.panel.flush{padding:0;overflow:visible}
.ph{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.ph-left{display:flex;align-items:center;gap:14px}
.ph-ico{display:grid;place-items:center;width:46px;height:46px;border-radius:14px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font-size:17px;box-shadow:0 8px 18px #2563eb44;flex:none}
.ph h2{font:700 19px var(--h);color:var(--navy);letter-spacing:-.4px}
.ph-sub{margin:10px 0 20px;font-size:13px;color:var(--mut)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:14px 20px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;border:0;border-radius:14px;cursor:pointer;font-weight:700;font-size:13px;white-space:nowrap;box-shadow:0 10px 22px #2563eb40;transition:.2s}
.btn:hover{filter:brightness(1.08)}
.btn-light{background:#e8f1ff;color:#1d4ed8;box-shadow:none}
.btn-sm{padding:10px 14px;font-size:12px;border-radius:11px}
.filters{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:22px}
.filters input,.filters select{padding:14px 16px;border:1px solid #dce8f8;border-radius:14px;background:#fff;color:var(--ink);font-size:13px;outline:none}
.filters input{flex:1;min-width:240px}
.filters input:focus,.filters select:focus,.f input:focus,.f select:focus,.f textarea:focus{border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}

/* TABEL */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:separate;border-spacing:0;min-width:880px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:12px;font-weight:700;padding:15px 16px;white-space:nowrap}
th:first-child{border-radius:14px 0 0 14px}th:last-child{border-radius:0 14px 14px 0}
td{padding:16px;border-bottom:1px solid #edf3fa;color:#41608a;vertical-align:middle;font-size:13px}
tbody tr.row{cursor:pointer;transition:.15s}
tbody tr.row:hover{background:#f6faff}
.f-info{display:flex;align-items:center;gap:13px;min-width:220px}
.f-ico{display:grid;place-items:center;flex:none;width:44px;height:44px;border-radius:12px;font-size:17px}
.f-name{font-weight:700;color:var(--navy)}
.f-sub{margin-top:4px;font-size:11.5px;color:#8ca6c4}
.tag{display:inline-block;padding:6px 12px;border-radius:9px;font-size:11.5px;font-weight:700;white-space:nowrap}
.t-PORT,.ic-PORT{background:#e8f0ff;color:#315bba}.t-AIRPORT,.ic-AIRPORT{background:#f0eaff;color:#8054c7}.t-DEPOT,.ic-DEPOT{background:#fff3df;color:#b5791b}.t-WAREHOUSE,.ic-WAREHOUSE{background:#e5f8ef;color:#168354}
.status{display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.st-ACTIVE{background:#e8f7ef;color:#26734d}.st-INACTIVE{background:#eef1f6;color:#7b879a}
.acts{display:flex;gap:7px}.acts form{margin:0}
.ab{display:grid;place-items:center;width:36px;height:36px;border:0;border-radius:11px;cursor:pointer;font-size:13px;transition:.15s}
.ab:hover{filter:brightness(.95);transform:translateY(-1px)}
.ab-d{background:#e8f1ff;color:#1d4ed8}.ab-m{background:#e5f8ef;color:#168354}.ab-e{background:#fff4dc;color:#b77900}.ab-x{background:#ffe9e9;color:#c92f3a}
td.empty{text-align:center;padding:42px;color:#8ca6c4}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-top:20px;font-size:12px;color:#8ca6c4}
.pager nav svg{width:16px;height:16px}

/* ===== PETA ===== */
.gmap{display:grid;grid-template-columns:minmax(0,1.75fr) minmax(320px,1fr);gap:20px;align-items:start}
.gmap-head{padding:28px 28px 0}
.gmap-head .ph-sub{margin-bottom:18px}
.tools{position:relative;z-index:20;display:flex;flex-wrap:wrap;align-items:center;gap:10px 20px;padding:14px 22px;border-top:1px solid #edf3fa;border-bottom:1px solid #edf3fa;background:#fff}
.tg{display:flex;align-items:center;flex-wrap:wrap;gap:6px}
.tgl{font-size:10px;font-weight:700;letter-spacing:.9px;color:#8ca6c4;margin-right:3px}
.chip{display:inline-flex;align-items:center;gap:7px;padding:8px 13px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:#3d68a8;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.15s}
.chip:hover{background:#eef6ff}
.chip.on{background:var(--blue);border-color:var(--blue);color:#fff;box-shadow:0 6px 16px #2563eb44}
.chip .dot{width:8px;height:8px;border-radius:50%}
.chip.off{opacity:.55;text-decoration:line-through}
.msearch{position:relative;margin-left:auto;flex:1;min-width:230px;max-width:380px}
.msearch>i{position:absolute;top:12px;left:13px;font-size:12px;color:#8ca6c4}
.msearch input{width:100%;padding:10px 14px 10px 36px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.msearch input:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.sres{display:none;position:absolute;left:0;right:0;top:44px;max-height:300px;overflow-y:auto;background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 16px 40px #1e3a8a2b;z-index:30}
.sres.open{display:block}
.sres button{display:flex;gap:10px;align-items:flex-start;width:100%;padding:10px 14px;border:0;border-bottom:1px solid #f1f6fc;background:transparent;text-align:left;cursor:pointer;font:12px/1.5 'Plus Jakarta Sans',sans-serif;color:#365775}
.sres button:hover{background:#f3f8ff}
.sres button i{margin-top:3px;color:var(--blue)}
.sres button b{display:block;color:var(--navy);font-size:12px}
.sres .none{padding:14px;color:#8ca6c4;text-align:center}
.tmsg{font-size:11px;color:#c92f3a;font-weight:600}
.mapw{position:relative;isolation:isolate;border-radius:0 0 28px 28px;overflow:hidden}
#facilityMap{height:640px;width:100%;background:#dfeafb}
.basebox{position:absolute;top:12px;right:12px;z-index:700;display:flex;gap:4px;padding:5px;background:#ffffffee;border:1px solid var(--line);border-radius:14px;box-shadow:0 8px 22px #0b2a6b2a}
.bb{display:flex;flex-direction:column;align-items:center;gap:3px;min-width:62px;padding:7px 8px;border:0;border-radius:10px;background:transparent;color:#3d68a8;font:600 10px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.15s}
.bb i{font-size:15px}.bb:hover{background:#eef6ff}
.bb.on{background:var(--blue);color:#fff;box-shadow:0 6px 14px #2563eb55}
.xhair{display:none;position:absolute;left:50%;top:50%;width:30px;height:30px;margin:-15px 0 0 -15px;z-index:650;pointer-events:none}
.xhair.show{display:block}
.xhair::before,.xhair::after{content:"";position:absolute;background:#e5484d;box-shadow:0 0 0 1px #fff}
.xhair::before{left:14px;top:0;width:2px;height:30px}.xhair::after{top:14px;left:0;height:2px;width:30px}
.ctr{display:none;position:absolute;left:12px;top:12px;z-index:700;width:290px;padding:14px 16px;background:#ffffffef;border:1px solid var(--line);border-radius:16px;box-shadow:0 10px 26px #0b2a6b2a}
.ctr.show{display:block}
.ctr .cn{font:700 12.5px var(--h);color:var(--navy);line-height:1.4}
.ctr .cc{margin-top:3px;font-size:10px;color:#8ca6c4}
.ctr .cw{display:flex;align-items:center;gap:12px;margin-top:10px}
.ctr .cw .e{font-size:34px}.ctr .cw b{font:800 26px var(--h);color:var(--navy);line-height:1}
.ctr .cw small{display:block;font-size:11px;color:var(--mut);margin-top:3px}
.ctr .ct{font:800 26px var(--h);color:var(--navy);margin-top:8px;letter-spacing:.5px}
.ctr .cs{margin-top:4px;font-size:11px;color:var(--mut);line-height:1.5}
.ctr .go{margin-top:10px;width:100%;padding:8px;border:0;border-radius:10px;background:#e8f1ff;color:#1d4ed8;font:700 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.map-info{position:absolute;left:12px;bottom:22px;z-index:500;display:flex;flex-wrap:wrap;gap:10px;padding:8px 12px;background:#ffffffee;border:1px solid var(--line);border-radius:12px;font-size:10.5px;color:#3d68a8;font-weight:600;max-width:calc(100% - 24px)}
.map-info span{display:flex;align-items:center;gap:6px}.map-info i.d{display:inline-block;width:9px;height:9px;border-radius:50%}
.pw{position:relative;width:34px;height:34px;display:grid;place-items:center}
.pin{display:grid;place-items:center;width:34px;height:34px;border-radius:50%;border:3px solid #fff;color:#fff;font-size:13px;box-shadow:0 4px 14px #0005;transition:transform .15s}
.pin.big{width:42px;height:42px;font-size:17px}
.pin.sel{transform:scale(1.25);box-shadow:0 0 0 6px #2563eb44,0 4px 14px #0005}
.pin.off{filter:grayscale(1);opacity:.7}
.pl{position:absolute;top:38px;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:3px;pointer-events:none}
.vl{padding:2px 8px;border-radius:8px;background:#061533e6;color:#fff;font:700 9.5px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;box-shadow:0 3px 8px #0004}
.bd{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:9px;background:#fff;border:1px solid #cfe0f7;color:#1f4a8a;font:700 10px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;box-shadow:0 3px 10px #0b2a6b33}
.bd i{font-size:11px}.bd em{font-style:normal;font-weight:600;color:#8ca6c4;font-size:9px}
.bd.bad{background:#fff1f1;border-color:#f5b5b5;color:#c92f3a}.bd.dim{color:#8ca6c4}
.leaflet-popup-content-wrapper{border-radius:14px}
.pop{min-width:200px;font:12px/1.7 'Plus Jakarta Sans',sans-serif;color:#365775}.pop strong{display:block;font-size:13px;color:var(--navy)}.pop small{color:#8ca6c4}
.pop .pt{display:inline-block;margin:3px 0;padding:2px 9px;border-radius:12px;font-weight:700;font-size:11px}
.pop button{margin-top:6px;margin-right:10px;border:0;background:none;color:var(--blue);font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer;padding:0}

.sidecol{display:flex;flex-direction:column;gap:20px;min-width:0}
.sidecol .panel{margin-bottom:0;padding:24px}
.sh{display:flex;align-items:flex-start;gap:10px;margin-bottom:14px}
.sh h3{font:700 16px var(--h);color:var(--navy);display:flex;align-items:center;gap:10px}
.sh h3 .hi{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font-size:14px;flex:none}
.sh p{margin-top:6px;font-size:12px;color:var(--mut)}
.nodata{padding:28px 12px;text-align:center;color:#8ca6c4;line-height:1.7}
.nodata i{display:block;margin-bottom:10px;font-size:30px;color:#9db6dc}
.pnl-stack{display:flex;flex-direction:column;gap:14px}
.lastloc{display:flex;gap:12px;align-items:center;padding:14px;border-radius:16px;background:linear-gradient(135deg,#eaf3ff,#dbeaff)}
.lastloc>i{display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:var(--blue);color:#fff;flex:none}
.lastloc b{display:block;font-size:13px;color:var(--navy);line-height:1.4}.lastloc small{font-size:11px;color:var(--mut);line-height:1.5;display:block;margin-top:2px}
.pbtns{display:flex;gap:8px;flex-wrap:wrap}
.wc{padding:18px;border-radius:20px;border:1px solid var(--line);background:linear-gradient(165deg,#fff,#f1f7ff)}
.wc.dark{background:linear-gradient(160deg,#061533,#0b2a6b 65%,#1747b8);color:#fff;border:0}
.wt{display:flex;align-items:center;gap:10px;font-size:10.5px;font-weight:700;letter-spacing:.8px;color:#8ca6c4;margin-bottom:6px}
.wc.dark .wt{color:#9fbcea}
.wt .wi{display:grid;place-items:center;width:26px;height:26px;border-radius:8px;color:#fff;font-size:11px}
.wcur{display:flex;align-items:center;gap:14px;margin:12px 0}
.wcur .tmp{font:800 36px var(--h);color:var(--navy);line-height:1}.wcur .ic{font-size:38px}
.wcur small{display:block;margin-top:5px;font-size:12px;color:var(--mut);font-weight:600}
.wm{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px}
.wm div{padding:9px 6px;border-radius:12px;background:#eaf3ff;text-align:center}
.wm b{display:block;font-size:12.5px;color:var(--navy)}.wm small{font-size:9.5px;color:#8ca6c4;font-weight:600}
.fc{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.fc div{padding:9px 4px;border-radius:12px;border:1px solid var(--line);text-align:center;background:#fff}
.fc small{display:block;font-size:10px;color:#8ca6c4;font-weight:600}.fc span{display:block;font-size:19px;margin:4px 0}.fc b{font-size:11px;color:var(--navy)}
.ck{font:800 32px var(--h);letter-spacing:1px;margin:12px 0 6px}
.cd{font-size:12px;color:#bcd3f7}
.cz{display:inline-block;margin-top:12px;padding:5px 11px;border-radius:20px;background:#ffffff1f;font-size:10.5px;font-weight:700}
.cdiff{margin-top:12px;font-size:11.5px;color:#9fbcea;line-height:1.6}
.wph{padding:26px 10px;text-align:center;color:#8ca6c4;font-size:12px;line-height:1.7}
.skl{height:120px;border-radius:16px;background:linear-gradient(90deg,#edf3fc,#f8fbff,#edf3fc);background-size:200% 100%;animation:sk 1.3s infinite}
@keyframes sk{to{background-position:-200% 0}}
.mlist{max-height:360px;overflow-y:auto;display:flex;flex-direction:column;gap:6px;padding-right:4px}
.mi{display:flex;align-items:center;gap:12px;width:100%;padding:10px 12px;border:1px solid #edf3fa;border-radius:14px;background:#fff;cursor:pointer;text-align:left;transition:.15s}
.mi:hover{background:#f6faff;border-color:#b9d2f7}
.mi.on{border-color:var(--blue);background:#f0f6ff}
.mi .f-ico{width:38px;height:38px;font-size:14px;border-radius:11px}
.mi b{display:block;font-size:12.5px;color:var(--navy)}.mi small{display:block;margin-top:2px;font-size:11px;color:#8ca6c4}
.mi.hidden{display:none}
.map-note{margin-top:12px;font-size:12px;color:#8ca6c4}

/* MODAL */
.modal{display:none;position:fixed;inset:0;background:rgba(6,21,51,.6);backdrop-filter:blur(3px);z-index:9999;align-items:center;justify-content:center;padding:18px}
.modal.show{display:flex}
.mbox{background:#fff;width:100%;max-width:820px;max-height:92vh;overflow-y:auto;border-radius:26px;box-shadow:0 24px 70px rgba(6,21,51,.35)}
.mhead{position:sticky;top:0;z-index:2000;display:flex;justify-content:space-between;align-items:center;gap:12px;padding:22px 28px;background:linear-gradient(100deg,#061533,#0b2a6b 70%,#1747b8);color:#fff}
.mhead h3{font:700 18px var(--h)}
.mclose{border:0;background:#ffffff26;color:#fff;border-radius:10px;width:34px;height:34px;font-size:20px;cursor:pointer}
.mbody{padding:26px 28px}
.mfoot{display:flex;justify-content:flex-end;gap:10px;padding:18px 28px;border-top:1px solid #edf3fa;flex-wrap:wrap}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.f label{display:block;font-size:12px;font-weight:700;color:#4a6694;margin-bottom:8px}
.f input,.f select,.f textarea{width:100%;padding:13px 14px;border:1px solid #dce8f8;border-radius:12px;font-size:13px;color:var(--ink);background:#fff;outline:none}
.f textarea{min-height:90px;resize:vertical}
.f.full{grid-column:1/-1}
.f small{display:block;margin-top:7px;font-size:11px;color:#8ca6c4}
.pickbox{border:1px solid var(--line);border-radius:16px;overflow:hidden}
.pickbar{display:flex;gap:8px;padding:10px;background:#f4f8ff;position:relative}
.pickbar input{flex:1;padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;font-size:12px;outline:none}
#pickMap{height:250px;width:100%;background:#dfeafb;position:relative;z-index:1}
.d-head{display:flex;align-items:center;gap:16px;padding-bottom:20px;margin-bottom:20px;border-bottom:1px solid #edf3fa}
.d-head .f-ico{width:64px;height:64px;border-radius:16px;font-size:24px}
.d-head h4{font:700 19px var(--h);color:var(--navy)}
.d-head p{margin-top:5px;font-size:12px;color:#8ca6c4}
.d-item label{display:block;font-size:11px;font-weight:700;color:#8ca6c4;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.d-item strong{font-size:13.5px;color:var(--navy);overflow-wrap:anywhere;font-weight:600;line-height:1.6}
.d-item.full{grid-column:1/-1}

@media(max-width:1350px){.cx-stats{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:1150px){.gmap{grid-template-columns:1fr}}
@media(max-width:1000px){.content{padding:20px}.cx-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.hero{padding:30px 26px}.hero h1{font-size:28px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}.panel{padding:20px}.gmap-head{padding:20px 20px 0}
 #facilityMap{height:500px}.map-info{display:none}.basebox{left:12px;right:12px;justify-content:space-between;top:auto;bottom:12px}.bb{min-width:0;flex:1}.bb span{display:none}.ctr{width:calc(100% - 24px)}.msearch{max-width:none;margin-left:0}.tools{padding:14px}
}
@media(max-width:600px){.cx-stats{grid-template-columns:1fr}.grid2{grid-template-columns:1fr}.f.full,.d-item.full{grid-column:auto}.mbody,.mhead,.mfoot{padding-left:18px;padding-right:18px}.hero h1{font-size:24px}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>
<body>

@php
    $typeLabels = ['PORT' => 'Pelabuhan', 'AIRPORT' => 'Bandara', 'DEPOT' => 'Depo', 'WAREHOUSE' => 'Gudang'];
    $typeIcons  = ['PORT' => 'fa-anchor', 'AIRPORT' => 'fa-plane', 'DEPOT' => 'fa-boxes-stacked', 'WAREHOUSE' => 'fa-warehouse'];
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
            <a href="{{ route('dashboard') }}" class="s-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-border-all"></i><span>Dashboard</span></a>
            <a href="{{ route('pengiriman.index') }}" class="s-link {{ request()->routeIs('pengiriman.*') ? 'active' : '' }}"><i class="fa-solid fa-box"></i><span>Pengiriman</span></a>
            <a href="{{ route('pelacakan.index') }}" class="s-link {{ request()->routeIs('pelacakan.*') ? 'active' : '' }}"><i class="fa-solid fa-location-dot"></i><span>Pelacakan</span></a>
            <a href="{{ route('exceptions.index') }}" class="s-link {{ request()->routeIs('exceptions.*') ? 'active' : '' }}"><i class="fa-solid fa-triangle-exclamation"></i><span>Pengecualian</span></a>
            <a href="{{ route('carriers.index') }}" class="s-link {{ request()->routeIs('carriers.*') ? 'active' : '' }}"><i class="fa-solid fa-building"></i><span>Perusahaan Pengiriman</span></a>
            <a href="{{ route('fasilitas.index') }}" class="s-link {{ request()->routeIs('fasilitas.*') ? 'active' : '' }}"><i class="fa-solid fa-warehouse"></i><span>Lokasi &amp; Fasilitas</span></a>
            <a href="{{ route('users.index') }}" class="s-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="fa-solid fa-users-gear"></i><span>Pengguna &amp; Hak Akses</span></a>
            <a href="{{ route('notifications.index') }}" class="s-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"><i class="fa-solid fa-bell"></i><span>Notifikasi</span></a>
            <a href="{{ route('audit-logs.index') }}" class="s-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-list"></i><span>Audit Log</span></a>
        </nav>
    </div>

    <div class="s-user">
        <div class="who">
            <div class="avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}</div>
            <div><b>{{ auth()->user()?->name ?? 'Admin CargoTrace' }}</b><small>Shipment Tracking System</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </form>
    </div>
</aside>

<main class="main">
<section class="content">

    @if(session('success'))<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-error">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul style="margin:7px 0 0 18px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <!-- HERO -->
    <section class="hero">
        <div class="hero-row">
            <div>
                <div class="crumb">Dashboard / Lokasi &amp; Fasilitas</div>
                <h1>Pengelolaan Lokasi &amp; Fasilitas</h1>
                <p>Kelola pelabuhan, bandara, depo, dan gudang operasional CargoTrace, lengkap dengan peta interaktif, cuaca, dan waktu setempat di tiap lokasi.</p>
            </div>
            <button type="button" class="hbtn" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> Tambah Fasilitas</button>
        </div>
        <div class="hmini">
            <span><i class="fa-solid fa-map-pin"></i> <b>{{ count($mapFacilities) }}</b> tampil di peta</span>
            <span><i class="fa-solid fa-location-crosshairs"></i> <b>{{ $tanpaKoordinat }}</b> belum berkoordinat</span>
            <span><i class="fa-solid fa-filter"></i> Peta mengikuti filter daftar</span>
        </div>
    </section>

    <!-- STATISTIK -->
    <section class="cx-stats">
        <a href="{{ route('fasilitas.index') }}" class="cx-stat">
            <div class="cx-ico" style="background:#eef2f8;color:#64748b"><i class="fa-solid fa-building"></i></div>
            <b>{{ number_format($totalFasilitas) }}</b><strong>Total</strong><small>Seluruh fasilitas terdaftar.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('fasilitas.index', ['type' => 'PORT']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#e8f0ff;color:#315bba"><i class="fa-solid fa-anchor"></i></div>
            <b>{{ number_format($totalPelabuhan) }}</b><strong>Pelabuhan</strong><small>Fasilitas transportasi laut.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('fasilitas.index', ['type' => 'AIRPORT']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#f0eaff;color:#8054c7"><i class="fa-solid fa-plane"></i></div>
            <b>{{ number_format($totalBandara) }}</b><strong>Bandara</strong><small>Fasilitas transportasi udara.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <div class="cx-stat">
            <div class="cx-ico" style="background:#fff3dc;color:#f59e0b"><i class="fa-solid fa-truck-fast"></i></div>
            <b>{{ number_format($totalDarat) }}</b><strong>Darat</strong><small>Depo dan gudang logistik.</small>
        </div>
        <a href="{{ route('fasilitas.index', ['status' => 'INACTIVE']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#ffebeb;color:#e5484d"><i class="fa-solid fa-circle-pause"></i></div>
            <b>{{ number_format($totalNonaktif) }}</b><strong>Tidak Aktif</strong><small>Fasilitas yang dinonaktifkan.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
    </section>

    <!-- DAFTAR -->
    <section class="panel">
        <div class="ph">
            <div class="ph-left">
                <div class="ph-ico"><i class="fa-solid fa-list"></i></div>
                <h2>Daftar Fasilitas</h2>
            </div>
            <button type="button" class="btn" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> Tambah</button>
        </div>
        <p class="ph-sub">{{ $facilities->total() }} fasilitas tersimpan. Klik baris untuk melihat detail lengkap.</p>

        <form action="{{ route('fasilitas.index') }}" method="GET" class="filters">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, kode, kota, atau alamat..." aria-label="Pencarian">
            <select name="type" aria-label="Jenis">
                <option value="">Semua jenis</option>
                @foreach($typeLabels as $val => $label)
                    <option value="{{ $val }}" @selected($type === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="city" aria-label="Kota">
                <option value="">Semua kota</option>
                @foreach($cities as $cityName)
                    <option value="{{ $cityName }}" @selected($city === $cityName)>{{ $cityName }}</option>
                @endforeach
            </select>
            <select name="status" aria-label="Status">
                <option value="">Semua status</option>
                <option value="ACTIVE" @selected($status === 'ACTIVE')>Aktif</option>
                <option value="INACTIVE" @selected($status === 'INACTIVE')>Tidak Aktif</option>
            </select>
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a href="{{ route('fasilitas.index') }}" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fasilitas</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($facilities as $facility)
                    @php
                        $json = json_encode($facility->toArray(), JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_TAG);
                        $hasCoord = is_numeric($facility->latitude) && is_numeric($facility->longitude);
                    @endphp
                    <tr class="row" data-facility="{{ $json }}" onclick="showDetail(this)">
                        <td>
                            <div class="f-info">
                                <div class="f-ico ic-{{ $facility->type }}"><i class="fa-solid {{ $typeIcons[$facility->type] ?? 'fa-location-dot' }}"></i></div>
                                <div>
                                    <div class="f-name">{{ $facility->name }}</div>
                                    <div class="f-sub">{{ $facility->facility_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="tag t-{{ $facility->type }}">{{ $typeLabels[$facility->type] ?? $facility->type }}</span></td>
                        <td>
                            <div>{{ $facility->city }}, {{ $facility->province }}</div>
                            <div class="f-sub">{{ $facility->country }}</div>
                        </td>
                        <td>
                            @if($facility->capacity !== null)
                                <span class="f-name">{{ number_format((float) $facility->capacity, 2, ',', '.') }}</span>
                            @else
                                <span class="f-sub">Belum diisi</span>
                            @endif
                        </td>
                        <td><span class="status st-{{ $facility->status }}">{{ $facility->status === 'ACTIVE' ? 'AKTIF' : 'TIDAK AKTIF' }}</span></td>
                        <td onclick="event.stopPropagation()">
                            <div class="acts">
                                <button type="button" class="ab ab-d" title="Detail" data-facility="{{ $json }}" onclick="showDetail(this)"><i class="fa-solid fa-eye"></i></button>
                                @if($hasCoord)
                                    <button type="button" class="ab ab-m" title="Lihat di peta" onclick="focusOnMap({{ (int) $facility->id }})"><i class="fa-solid fa-map-pin"></i></button>
                                @endif
                                <button type="button" class="ab ab-e" title="Edit" data-facility="{{ $json }}" onclick="editFacility(this)"><i class="fa-solid fa-pen"></i></button>
                                @if($facility->status === 'ACTIVE')
                                    <form action="{{ route('fasilitas.destroy', $facility->id) }}" method="POST" data-name="{{ $facility->name }}" onsubmit="return confirmDisable(this)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ab ab-x" title="Nonaktifkan"><i class="fa-solid fa-power-off"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada fasilitas yang cocok. Ubah filter atau tambah fasilitas baru.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            <span>Menampilkan {{ $facilities->firstItem() ?? 0 }}–{{ $facilities->lastItem() ?? 0 }} dari {{ $facilities->total() }} fasilitas</span>
            @if($facilities->hasPages())<div>{{ $facilities->links() }}</div>@endif
        </div>
    </section>

    <!-- PETA -->
    <section class="gmap" id="peta">
        <div class="panel flush">
            <div class="gmap-head">
                <div class="ph">
                    <div class="ph-left">
                        <div class="ph-ico"><i class="fa-solid fa-map-location-dot"></i></div>
                        <h2>Peta Lokasi Fasilitas</h2>
                    </div>
                    <button type="button" class="btn btn-light btn-sm" id="fitAll"><i class="fa-solid fa-expand"></i> Tampilkan semua</button>
                </div>
                <p class="ph-sub">Peta mengikuti filter di atas. <span id="mapCount"></span> Klik penanda untuk detail, atau klik titik mana pun untuk cek cuaca &amp; waktunya.</p>
            </div>

            <div class="tools">
                <div class="tg" id="viewGroup">
                    <span class="tgl">TAMPILAN</span>
                    <button type="button" class="chip on" data-view="loc"><i class="fa-solid fa-location-dot"></i> Lokasi</button>
                    <button type="button" class="chip" data-view="wx"><i class="fa-solid fa-cloud-sun-rain"></i> Cuaca</button>
                    <button type="button" class="chip" data-view="time"><i class="fa-regular fa-clock"></i> Waktu</button>
                </div>
                <div class="tg" id="radarGroup" style="display:none">
                    <button type="button" class="chip on" id="radarBtn"><i class="fa-solid fa-satellite-dish"></i> Radar Hujan</button>
                </div>
                <div class="tg" id="typeGroup">
                    <span class="tgl">JENIS</span>
                    <button type="button" class="chip on" data-t="PORT"><span class="dot" style="background:#315bba"></span> Pelabuhan</button>
                    <button type="button" class="chip on" data-t="AIRPORT"><span class="dot" style="background:#8054c7"></span> Bandara</button>
                    <button type="button" class="chip on" data-t="DEPOT"><span class="dot" style="background:#d99b22"></span> Depo</button>
                    <button type="button" class="chip on" data-t="WAREHOUSE"><span class="dot" style="background:#16a36a"></span> Gudang</button>
                </div>
                <div class="msearch">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="mapQ" placeholder="Cari negara, kota, desa, jalan… lalu Enter" aria-label="Cari lokasi di peta" autocomplete="off">
                    <div class="sres" id="sres"></div>
                </div>
                <span class="tmsg" id="tmsg"></span>
            </div>

            <div class="mapw">
                <div id="facilityMap"></div>
                <div class="basebox" id="baseBox" role="group" aria-label="Jenis peta">
                    <button class="bb on" data-base="default" type="button" title="Peta standar"><i class="fa-solid fa-map"></i><span>Default</span></button>
                    <button class="bb" data-base="sat" type="button" title="Citra satelit"><i class="fa-solid fa-satellite"></i><span>Satelit</span></button>
                    <button class="bb" data-base="terrain" type="button" title="Kontur medan"><i class="fa-solid fa-mountain"></i><span>Medan</span></button>
                    <button class="bb" data-base="transit" type="button" title="Jalur transportasi umum"><i class="fa-solid fa-train-subway"></i><span>Transportasi</span></button>
                    <button class="bb" data-base="traffic" type="button" title="Kondisi lalu lintas"><i class="fa-solid fa-traffic-light"></i><span>Lalu lintas</span></button>
                </div>
                <div class="xhair" id="xhair"></div>
                <div class="ctr" id="ctr"></div>
                <div class="map-info">
                    <span><i class="d" style="background:#315bba"></i> Pelabuhan</span>
                    <span><i class="d" style="background:#8054c7"></i> Bandara</span>
                    <span><i class="d" style="background:#d99b22"></i> Depo</span>
                    <span><i class="d" style="background:#16a36a"></i> Gudang</span>
                    <span><i class="d" style="background:#e5484d"></i> Titik pilihan</span>
                </div>
            </div>
        </div>

        <div class="sidecol">
            <div class="panel">
                <div class="sh"><h3><span class="hi"><i class="fa-solid fa-location-pin"></i></span> Detail Lokasi</h3></div>
                <div id="pickBox"><div class="nodata"><i class="fa-solid fa-earth-asia"></i>Pilih fasilitas di peta atau daftar, cari nama tempat, atau klik titik mana pun untuk melihat cuaca dan waktu setempat.</div></div>
            </div>
            <div class="panel">
                <div class="sh"><h3><span class="hi"><i class="fa-solid fa-list-ul"></i></span> Fasilitas di Peta</h3></div>
                <div class="mlist" id="mapList"></div>
                @if($tanpaKoordinat > 0)
                    <p class="map-note"><i class="fa-solid fa-circle-info"></i> {{ $tanpaKoordinat }} fasilitas hasil filter belum punya latitude &amp; longitude, jadi tidak muncul di peta. Edit fasilitasnya lalu pilih titik di peta.</p>
                @endif
            </div>
        </div>
    </section>

</section>
</main>

<!-- MODAL TAMBAH / EDIT -->
<div class="modal" id="facilityModal">
    <div class="mbox">
        <div class="mhead">
            <h3 id="modalTitle">Tambah Fasilitas</h3>
            <button type="button" class="mclose" onclick="closeModal('facilityModal')">&times;</button>
        </div>
        <form method="POST" id="facilityForm" action="{{ route('fasilitas.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="_mode" id="formMode" value="create">
            <input type="hidden" name="_id" id="formId" value="">
            <div class="mbody">
                <div class="grid2">
                    <div class="f"><label>Kode Fasilitas *</label><input name="facility_code" id="f_facility_code" placeholder="Contoh: PRT-001" required></div>
                    <div class="f"><label>Nama Fasilitas *</label><input name="name" id="f_name" placeholder="Contoh: Pelabuhan Belawan" required></div>
                    <div class="f"><label>Jenis Fasilitas *</label>
                        <select name="type" id="f_type" required>
                            <option value="">Pilih jenis</option>
                            <option value="PORT">Pelabuhan</option>
                            <option value="AIRPORT">Bandara</option>
                            <option value="DEPOT">Depo</option>
                            <option value="WAREHOUSE">Gudang</option>
                        </select>
                    </div>
                    <div class="f"><label>Status *</label>
                        <select name="status" id="f_status" required>
                            <option value="ACTIVE">Aktif</option>
                            <option value="INACTIVE">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="f"><label>Kota *</label><input name="city" id="f_city" required></div>
                    <div class="f"><label>Provinsi *</label><input name="province" id="f_province" required></div>
                    <div class="f"><label>Negara *</label><input name="country" id="f_country" required></div>
                    <div class="f"><label>Kapasitas</label><input type="number" step="0.01" min="0" name="capacity" id="f_capacity"></div>
                    <div class="f full">
                        <label>Titik Lokasi (klik peta untuk mengisi latitude &amp; longitude)</label>
                        <div class="pickbox">
                            <div class="pickbar">
                                <input type="text" id="pickQ" placeholder="Cari tempat lalu Enter, contoh: Pelabuhan Belawan" autocomplete="off">
                                <button type="button" class="btn btn-sm" id="pickGo"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                            </div>
                            <div id="pickMap"></div>
                        </div>
                        <small>Marker bisa digeser, atau isi koordinat manual di bawah.</small>
                    </div>
                    <div class="f"><label>Latitude</label><input type="number" step="any" name="latitude" id="f_latitude" placeholder="3.7826"></div>
                    <div class="f"><label>Longitude</label><input type="number" step="any" name="longitude" id="f_longitude" placeholder="98.6930"></div>
                    <div class="f full"><label>Alamat *</label><textarea name="address" id="f_address" required></textarea></div>
                    <div class="f full"><label>Deskripsi</label><textarea name="description" id="f_description" placeholder="Informasi tambahan tentang fasilitas"></textarea></div>
                </div>
            </div>
            <div class="mfoot">
                <button type="button" class="btn btn-light" onclick="closeModal('facilityModal')">Batal</button>
                <button type="submit" class="btn" id="submitButton">Simpan Fasilitas</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal" id="detailModal">
    <div class="mbox">
        <div class="mhead">
            <h3>Detail Fasilitas</h3>
            <button type="button" class="mclose" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="mbody">
            <div class="d-head">
                <div id="detailIcon"></div>
                <div><h4 id="detailName">-</h4><p id="detailCode">Kode: -</p></div>
            </div>
            <div class="grid2" id="detailContent"></div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-light" id="detailMapBtn" style="display:none"><i class="fa-solid fa-map-pin"></i> Lihat di Peta</button>
            <button type="button" class="btn btn-light" onclick="closeModal('detailModal')">Tutup</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const facilityBase = @json(url('/fasilitas'));
const storeUrl = @json(route('fasilitas.store'));
const fieldIds = ['facility_code','name','type','status','city','province','country','capacity','latitude','longitude','address','description'];

const TYPE_LABEL = { PORT:'Pelabuhan', AIRPORT:'Bandara', DEPOT:'Depo', WAREHOUSE:'Gudang' };
const TYPE_ICON  = { PORT:'fa-anchor', AIRPORT:'fa-plane', DEPOT:'fa-boxes-stacked', WAREHOUSE:'fa-warehouse' };
const TYPE_COLOR = { PORT:'#315bba', AIRPORT:'#8054c7', DEPOT:'#d99b22', WAREHOUSE:'#16a36a' };

const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
const p2 = n => String(n).padStart(2, '0');
const sleep = ms => new Promise(r => setTimeout(r, ms));
const fetchT = (u, ms = 7000) => { const c = new AbortController(), t = setTimeout(() => c.abort(), ms); return fetch(u, { signal:c.signal }).finally(() => clearTimeout(t)); };
const shortName = n => String(n || '').split(',').slice(0, 2).map(x => x.trim()).join(', ');
const hasVal = v => v !== null && v !== undefined && v !== '';

/* ===================== MODAL & FORM ===================== */
function openModal(id){ $(id).classList.add('show'); }
function closeModal(id){ $(id).classList.remove('show'); }

document.querySelectorAll('.modal').forEach(function(m){
    m.addEventListener('click',function(e){ if(e.target===m) m.classList.remove('show'); });
});
document.addEventListener('keydown',function(e){
    if(e.key==='Escape') document.querySelectorAll('.modal.show').forEach(function(m){m.classList.remove('show')});
});

function readFacility(el){
    try{return JSON.parse(el.dataset.facility)}
    catch(e){alert('Data fasilitas tidak dapat dibaca.');return null}
}

function setMode(mode,id){
    const form=$('facilityForm');
    $('formMode').value=mode;
    $('formId').value=id||'';
    if(mode==='edit'){
        form.action=facilityBase+'/'+id;
        $('formMethod').value='PUT';
        $('modalTitle').textContent='Edit Fasilitas';
        $('submitButton').textContent='Simpan Perubahan';
    }else{
        form.action=storeUrl;
        $('formMethod').value='POST';
        $('modalTitle').textContent='Tambah Fasilitas';
        $('submitButton').textContent='Simpan Fasilitas';
    }
}

function fillForm(data){
    fieldIds.forEach(function(f){ $('f_'+f).value=(data&&data[f])??''; });
}

function openFacilityModal(){
    openModal('facilityModal');
    initPickMap();
    setTimeout(function(){ pickMap.invalidateSize(); syncPickMarker(true); }, 200);
}
function openCreateModal(){
    $('facilityForm').reset();
    setMode('create');
    fillForm({status:'ACTIVE',country:'Indonesia'});
    $('pickQ').value='';
    openFacilityModal();
}
function editFacility(btn){
    const f=readFacility(btn); if(!f)return;
    $('facilityForm').reset();
    setMode('edit',f.id);
    fillForm(f);
    $('pickQ').value='';
    openFacilityModal();
}

function confirmDisable(form){
    return confirm('Yakin ingin menonaktifkan fasilitas '+(form.dataset.name||'ini')+'?');
}

/* ===================== MINI PETA PEMILIH KOORDINAT ===================== */
let pickMap=null, pickMarker=null;
function initPickMap(){
    if(pickMap) return;
    pickMap=L.map('pickMap',{worldCopyJump:true,minZoom:2}).setView([2,110],4);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}).addTo(pickMap);
    pickMap.on('click',function(e){
        const c=e.latlng.wrap();
        $('f_latitude').value=c.lat.toFixed(6);
        $('f_longitude').value=c.lng.toFixed(6);
        syncPickMarker(false);
    });
    $('f_latitude').addEventListener('input',function(){syncPickMarker(false)});
    $('f_longitude').addEventListener('input',function(){syncPickMarker(false)});
    $('pickGo').addEventListener('click',function(){pickSearch($('pickQ').value)});
    $('pickQ').addEventListener('keydown',function(e){ if(e.key==='Enter'){ e.preventDefault(); pickSearch(e.target.value); } });
}
function syncPickMarker(fit){
    if(!pickMap) return;
    const lat=parseFloat($('f_latitude').value), lng=parseFloat($('f_longitude').value);
    if(!Number.isFinite(lat)||!Number.isFinite(lng)||Math.abs(lat)>90||Math.abs(lng)>180){
        if(pickMarker){ pickMap.removeLayer(pickMarker); pickMarker=null; }
        return;
    }
    if(!pickMarker){
        pickMarker=L.marker([lat,lng],{draggable:true}).addTo(pickMap);
        pickMarker.on('dragend',function(){
            const c=pickMarker.getLatLng().wrap();
            $('f_latitude').value=c.lat.toFixed(6);
            $('f_longitude').value=c.lng.toFixed(6);
        });
    } else pickMarker.setLatLng([lat,lng]);
    if(fit) pickMap.setView([lat,lng],Math.max(pickMap.getZoom(),13));
}
async function pickSearch(q){
    q=String(q||'').trim(); if(!q) return;
    try{
        await gate();
        const r=await fetchT('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&accept-language=id&q='+encodeURIComponent(q));
        const j=await r.json();
        if(!j.length){ alert('Lokasi tidak ditemukan. Coba ejaan lain.'); return; }
        $('f_latitude').value=(+j[0].lat).toFixed(6);
        $('f_longitude').value=(+j[0].lon).toFixed(6);
        syncPickMarker(false);
        pickMap.setView([+j[0].lat,+j[0].lon],15);
    }catch(_){ alert('Pencarian gagal, coba lagi.'); }
}

/* ===================== DETAIL ===================== */
let currentDetail=null;
function renderDetail(f){
    currentDetail=f;
    $('detailName').textContent=f.name||'-';
    $('detailCode').textContent='Kode: '+(f.facility_code||'-');

    const icon=$('detailIcon');
    icon.replaceChildren();
    const box=document.createElement('div');
    box.className='f-ico ic-'+f.type;
    const i=document.createElement('i');
    i.className='fa-solid '+(TYPE_ICON[f.type]||'fa-location-dot');
    box.appendChild(i); icon.appendChild(box);

    const hasCoord=hasVal(f.latitude)&&hasVal(f.longitude);
    const cap=hasVal(f.capacity)?Number(f.capacity).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}):'-';
    const items=[
        ['Jenis Fasilitas',TYPE_LABEL[f.type]||f.type],
        ['Status',f.status==='ACTIVE'?'AKTIF':'TIDAK AKTIF'],
        ['Kota',f.city],
        ['Provinsi',f.province],
        ['Negara',f.country],
        ['Kapasitas',cap],
        ['Koordinat',hasCoord?(f.latitude+', '+f.longitude):'Belum diisi'],
        ['Alamat',f.address,true],
        ['Deskripsi',f.description,true]
    ];
    const wrap=$('detailContent');
    wrap.replaceChildren();
    items.forEach(function(it){
        const d=document.createElement('div');
        d.className='d-item'+(it[2]?' full':'');
        const l=document.createElement('label'); l.textContent=it[0];
        const s=document.createElement('strong');
        s.textContent=(it[1]===null||it[1]===undefined||it[1]==='')?'-':it[1];
        d.append(l,s); wrap.appendChild(d);
    });

    $('detailMapBtn').style.display=(hasCoord && byId[f.id])?'inline-flex':'none';
    openModal('detailModal');
}
function showDetail(el){ const f=readFacility(el); if(f) renderDetail(f); }
function showDetailById(id){ if(byId[id]) renderDetail(byId[id]); }

/* ===================== WAKTU ===================== */
const fmtClock = off => { const d = new Date(Date.now() + off * 1000); return p2(d.getUTCHours()) + ':' + p2(d.getUTCMinutes()) + ':' + p2(d.getUTCSeconds()); };
const fmtDate = off => new Intl.DateTimeFormat('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric', timeZone:'UTC' }).format(new Date(Date.now() + off * 1000));
const utcLabel = off => { const a = Math.abs(off), h = Math.floor(a / 3600), m = Math.round((a % 3600) / 60); return 'UTC' + (off < 0 ? '\u2212' : '+') + p2(h) + ':' + p2(m); };
const myOff = () => -new Date().getTimezoneOffset() * 60;
function diffText(off) {
    const d = (off - myOff()) / 3600;
    if (Math.abs(d) < 0.01) return 'Sama dengan waktu di perangkatmu';
    const a = Math.abs(d), h = Number.isInteger(a) ? a : a.toFixed(1).replace('.', ',');
    return h + ' jam ' + (d > 0 ? 'lebih cepat' : 'lebih lambat') + ' dari waktu perangkatmu';
}
setInterval(() => {
    if (document.hidden) return;
    document.querySelectorAll('.tmk').forEach(e => { e.textContent = fmtClock(+e.dataset.off); });
    document.querySelectorAll('.tmd').forEach(e => { e.textContent = fmtDate(+e.dataset.off); });
}, 1000);

/* ===================== CUACA (Open-Meteo) ===================== */
const cond = (c, day = true) => {
    if (c === 0) return ['Cerah', day ? '☀️' : '🌙', day ? 'fa-sun' : 'fa-moon'];
    if (c === 1) return ['Cerah berawan', day ? '🌤️' : '🌙', day ? 'fa-cloud-sun' : 'fa-cloud-moon'];
    if (c === 2) return ['Berawan sebagian', '⛅', 'fa-cloud-sun'];
    if (c === 3) return ['Berawan', '☁️', 'fa-cloud'];
    if (c === 45 || c === 48) return ['Berkabut', '🌫️', 'fa-smog'];
    if (c >= 51 && c <= 57) return ['Gerimis', '🌦️', 'fa-cloud-rain'];
    if (c >= 61 && c <= 67) return ['Hujan', '🌧️', 'fa-cloud-showers-heavy'];
    if ((c >= 71 && c <= 77) || c === 85 || c === 86) return ['Salju', '❄️', 'fa-snowflake'];
    if (c >= 80 && c <= 82) return ['Hujan lokal', '🌧️', 'fa-cloud-showers-heavy'];
    if (c >= 95) return ['Badai petir', '⛈️', 'fa-cloud-bolt'];
    return ['Berawan', '☁️', 'fa-cloud'];
};
const WXV = {}, WXP = {};
const wkey = (lat, lng) => (+lat).toFixed(2) + ',' + (+lng).toFixed(2);
function infoAt(lat, lng) {
    const k = wkey(lat, lng);
    if (WXP[k]) return WXP[k];
    WXP[k] = (async () => {
        try {
            const r = await fetchT('https://api.open-meteo.com/v1/forecast?latitude=' + lat + '&longitude=' + lng +
                '&current=temperature_2m,relative_humidity_2m,rain,weather_code,wind_speed_10m,is_day&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=auto&forecast_days=3');
            if (!r.ok) throw new Error('HTTP ' + r.status);
            const j = await r.json(), c = j.current, d = j.daily;
            return (WXV[k] = { ok:true, t:c.temperature_2m, h:c.relative_humidity_2m, w:c.wind_speed_10m, r:c.rain, c:c.weather_code, day:c.is_day !== 0, off:j.utc_offset_seconds, tz:j.timezone,
                daily:d.time.map((t, i) => ({ date:t, c:d.weather_code[i], max:d.temperature_2m_max[i], min:d.temperature_2m_min[i] })) });
        } catch (_) {
            delete WXP[k];
            return (WXV[k] = { ok:false, off:Math.round(lng / 15) * 3600, tz:null, approx:true });
        }
    })();
    return WXP[k];
}
function weatherHtml(i) {
    if (!i || !i.ok) return '<div class="wph">Data cuaca belum tersedia untuk titik ini.</div>';
    const c = cond(i.c, i.day);
    return '<div class="wcur"><span class="ic">' + c[1] + '</span><div><div class="tmp">' + Math.round(i.t) + '°C</div><small>' + c[0] + '</small></div></div>' +
        '<div class="wm"><div><b>' + i.h + '%</b><small>Lembap</small></div><div><b>' + i.w + '</b><small>km/j angin</small></div><div><b>' + i.r + '</b><small>mm hujan</small></div></div>' +
        '<div class="fc">' + i.daily.map(d => '<div><small>' + new Date(d.date + 'T00:00:00').toLocaleDateString('id-ID', { weekday:'short', day:'numeric' }) + '</small><span>' + cond(d.c)[1] + '</span><b>' + Math.round(d.max) + '°/' + Math.round(d.min) + '°</b></div>').join('') + '</div>';
}
function timeHtml(i) {
    if (!i) return '<div class="wph">Zona waktu belum dapat ditentukan.</div>';
    return '<div class="ck tmk" data-off="' + i.off + '">' + fmtClock(i.off) + '</div><div class="cd tmd" data-off="' + i.off + '">' + fmtDate(i.off) + '</div>' +
        '<span class="cz">' + (i.tz ? esc(i.tz) + ' · ' : '') + utcLabel(i.off) + (i.approx ? ' (perkiraan)' : '') + '</span><div class="cdiff">' + diffText(i.off) + '</div>';
}

/* ===================== GEOKODING (Nominatim) ===================== */
let lastNet = 0;
let gateP = Promise.resolve();
const gate = () => (gateP = gateP.then(async () => { const w = 1100 - (Date.now() - lastNet); if (w > 0) await sleep(w); lastNet = Date.now(); }));
const REV = {};
async function reverse(lat, lng, zoom) {
    const k = lat.toFixed(3) + ',' + lng.toFixed(3) + ',' + zoom;
    if (k in REV) return REV[k];
    await gate();
    try {
        const r = await fetchT('https://nominatim.openstreetmap.org/reverse?format=jsonv2&zoom=' + zoom + '&accept-language=id&lat=' + lat + '&lon=' + lng);
        if (!r.ok) return null;
        const j = await r.json();
        return (REV[k] = j && j.display_name ? j.display_name : null);
    } catch (_) { return null; }
}

/* ===================== PETA UTAMA ===================== */
const facilities = @json($mapFacilities);
const byId = {};
const markerById = {};
const itemById = {};

const osm = () => L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap contributors', maxNativeZoom:19, maxZoom:19, keepBuffer:2 });
const TOMTOM_KEY = '';   // opsional: API key TomTom (gratis) untuk lapisan lalu lintas resmi
const BASES = {
    default: osm(),
    sat: L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { subdomains:'0123', maxNativeZoom:20, maxZoom:19, keepBuffer:2, attribution:'Imagery &copy; Google' }),
    terrain: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', { subdomains:'abc', maxNativeZoom:17, maxZoom:19, keepBuffer:2, attribution:'&copy; OpenStreetMap, SRTM | Style &copy; OpenTopoMap (CC-BY-SA)' }),
    transit: L.layerGroup([
        osm(),
        L.tileLayer('https://tile.memomaps.de/tilegen/{z}/{x}/{y}.png', { maxNativeZoom:18, maxZoom:19, keepBuffer:2, attribution:'Map &copy; OpenStreetMap contributors, &Ouml;PNVKarte memomaps.de' })
    ]),
    traffic: TOMTOM_KEY
        ? L.layerGroup([osm(), L.tileLayer('https://api.tomtom.com/traffic/map/4/tile/flow/relative0/{z}/{x}/{y}.png?key=' + encodeURIComponent(TOMTOM_KEY), { opacity:.85, maxZoom:19, attribution:'Traffic &copy; TomTom' })])
        : L.tileLayer('https://mt{s}.google.com/vt/lyrs=m,traffic&x={x}&y={y}&z={z}', { subdomains:'0123', maxNativeZoom:19, maxZoom:19, keepBuffer:2, attribution:'Map data &copy; Google' })
};
const map = L.map('facilityMap', { worldCopyJump:true, maxZoom:19, minZoom:2, preferCanvas:true, wheelDebounceTime:80 }).setView([2.5, 98.5], 5);
let curBase = BASES.default.addTo(map);
const facLayer = L.layerGroup().addTo(map), pickLayer = L.layerGroup().addTo(map);
setTimeout(() => map.invalidateSize(), 250);

const st = { view:'loc', radar:true, types:{ PORT:true, AIRPORT:true, DEPOT:true, WAREHOUSE:true }, sel:null };
let radarLayer = null, PICK = null, pickTok = 0, ctrTok = 0, ctrTimer = null, badgeTimer = null;

/* ganti jenis peta */
document.querySelectorAll('#baseBox .bb').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('#baseBox .bb').forEach(x => x.classList.toggle('on', x === b));
    map.removeLayer(curBase); curBase = BASES[b.dataset.base].addTo(map);
    if (curBase.bringToBack) curBase.bringToBack(); else if (curBase.eachLayer) curBase.eachLayer(l => l.bringToBack && l.bringToBack());
}));

/* radar hujan (RainViewer) */
async function setRadar(on) {
    if (!on) { if (radarLayer && map.hasLayer(radarLayer)) map.removeLayer(radarLayer); return; }
    if (!radarLayer) {
        try {
            const j = await (await fetchT('https://api.rainviewer.com/public/weather-maps.json')).json();
            const f = ((j.radar && j.radar.past) || []).slice(-1)[0]; if (!f) return;
            radarLayer = L.tileLayer(j.host + f.path + '/256/{z}/{x}/{y}/2/1_1.png', { opacity:.6, maxNativeZoom:7, maxZoom:19, zIndex:5, attribution:'Radar &copy; RainViewer' });
        } catch (_) { return; }
    }
    if (st.view === 'wx' && st.radar) radarLayer.addTo(map);
}

/* ikon marker */
function badge(lat, lng) {
    if (st.view === 'loc') return '';
    const w = WXV[wkey(lat, lng)];
    if (st.view === 'wx') {
        if (!w) return '<span class="bd dim"><i class="fa-solid fa-spinner fa-spin"></i> cuaca…</span>';
        if (!w.ok) return '<span class="bd dim">cuaca n/a</span>';
        const x = cond(w.c, w.day), bad = w.c >= 61 || w.w >= 40;
        return '<span class="bd' + (bad ? ' bad' : '') + '"><i class="fa-solid ' + x[2] + '"></i>' + Math.round(w.t) + '°C <em>' + x[0] + '</em></span>';
    }
    const off = w ? w.off : Math.round(lng / 15) * 3600;
    return '<span class="bd"><i class="fa-solid ' + (w && w.ok ? (w.day ? 'fa-sun' : 'fa-moon') : 'fa-clock') + '"></i><span class="tmk" data-off="' + off + '">' + fmtClock(off) + '</span><em>' + utcLabel(off) + '</em></span>';
}
function pin(color, icon, cls, label, bdg) {
    return L.divIcon({ className:'',
        html:'<div class="pw"><div class="pin ' + (cls || '') + '" style="background:' + color + '"><i class="fa-solid ' + icon + '"></i></div>' +
            ((label || bdg) ? '<div class="pl">' + (label ? '<span class="vl">' + esc(label) + '</span>' : '') + (bdg || '') + '</div>' : '') + '</div>',
        iconSize:[34,34], iconAnchor:[17,17], popupAnchor:[0,-20] });
}
function facIcon(f) {
    const showLabel = st.view !== 'loc' || map.getZoom() >= 8 || st.sel === f.id;
    const inView = map.getBounds().contains([f.latitude, f.longitude]);
    const bdg = (st.view !== 'loc' && inView) ? badge(f.latitude, f.longitude) : '';
    const cls = (st.sel === f.id ? 'sel ' : '') + (f.status === 'INACTIVE' ? 'off' : '');
    return pin(TYPE_COLOR[f.type] || '#315bba', TYPE_ICON[f.type] || 'fa-location-dot', cls, showLabel ? f.name : '', bdg);
}
function ctxHtml(lat, lng) {
    const w = WXV[wkey(lat, lng)]; if (!w) return '';
    const c = w.ok ? cond(w.c, w.day) : null;
    return '<span style="display:block;margin-top:4px;padding-top:4px;border-top:1px dashed #dce8f8">' + (c ? c[1] + ' ' + c[0] + ', ' + Math.round(w.t) + '°C · angin ' + Math.round(w.w) + ' km/j<br>' : '') +
        '<i class="fa-regular fa-clock"></i> Waktu lokal <b class="tmk" data-off="' + w.off + '">' + fmtClock(w.off) + '</b> (' + utcLabel(w.off) + ')</span>';
}

/* marker fasilitas */
facilities.forEach(function(f){
    const lat=Number(f.latitude), lng=Number(f.longitude);
    if(!Number.isFinite(lat)||!Number.isFinite(lng)) return;
    f.latitude=lat; f.longitude=lng;
    byId[f.id]=f;
    const popup=function(){
        return '<div class="pop"><strong>'+esc(f.name)+'</strong><span class="pt t-'+esc(f.type)+'">'+esc(TYPE_LABEL[f.type]||f.type)+'</span> <small>'+esc(f.facility_code)+'</small><br>'+
            esc([f.address,f.city,f.province].filter(Boolean).join(', '))+ctxHtml(lat,lng)+
            '<br><button type="button" onclick="showDetailById('+Number(f.id)+')">Lihat detail →</button></div>';
    };
    const m=L.marker([lat,lng],{icon:facIcon(f)}).bindPopup(popup,{maxWidth:300}).addTo(facLayer);
    m.on('click',function(){ selectFacility(f.id,false); });
    markerById[f.id]=m;
});

/* daftar samping */
(function buildList(){
    const box=$('mapList');
    if(!Object.keys(byId).length){ box.innerHTML='<div class="nodata"><i class="fa-solid fa-map-pin"></i>Belum ada fasilitas berkoordinat pada filter ini.</div>'; return; }
    Object.values(byId).forEach(function(f){
        const b=document.createElement('button');
        b.type='button'; b.className='mi'; b.dataset.id=f.id; b.dataset.type=f.type;
        b.innerHTML='<div class="f-ico ic-'+esc(f.type)+'"><i class="fa-solid '+(TYPE_ICON[f.type]||'fa-location-dot')+'"></i></div><div><b>'+esc(f.name)+'</b><small>'+esc(f.city||'')+(f.province?', '+esc(f.province):'')+'</small></div>';
        b.addEventListener('click',function(){ selectFacility(f.id,true); });
        box.appendChild(b); itemById[f.id]=b;
    });
})();

function visibleFacilities(){ return Object.values(byId).filter(f => st.types[f.type]); }
function updateCount(){
    const n=visibleFacilities().length;
    $('mapCount').textContent=n+' fasilitas berkoordinat ditampilkan.';
}
function applyTypeFilter(){
    Object.values(byId).forEach(function(f){
        const on=st.types[f.type], m=markerById[f.id];
        if(on && !facLayer.hasLayer(m)) facLayer.addLayer(m);
        if(!on && facLayer.hasLayer(m)) facLayer.removeLayer(m);
        itemById[f.id].classList.toggle('hidden',!on);
    });
    updateCount();
}
function fitAll(){
    const vis=visibleFacilities().map(f=>[f.latitude,f.longitude]);
    if(!vis.length) return;
    if(vis.length===1) map.setView(vis[0],12); else map.fitBounds(L.latLngBounds(vis).pad(0.15),{maxZoom:12});
}
function refreshIcons(){
    Object.values(byId).forEach(f => { if(st.types[f.type]) markerById[f.id].setIcon(facIcon(f)); });
}
function loadBadges(){
    clearTimeout(badgeTimer);
    if(st.view==='loc') return;
    badgeTimer=setTimeout(function(){
        const b=map.getBounds();
        const need=visibleFacilities().filter(f=>b.contains([f.latitude,f.longitude])).slice(0,20).filter(f=>!WXV[wkey(f.latitude,f.longitude)]);
        refreshIcons();
        if(!need.length) return;
        Promise.all(need.map(f=>infoAt(f.latitude,f.longitude))).then(function(){ if(st.view!=='loc') refreshIcons(); });
    },400);
}

document.querySelectorAll('#typeGroup .chip').forEach(b => b.addEventListener('click', () => {
    const t=b.dataset.t; st.types[t]=!st.types[t];
    b.classList.toggle('on',st.types[t]); b.classList.toggle('off',!st.types[t]);
    applyTypeFilter();
}));
$('fitAll').addEventListener('click',fitAll);
map.on('zoomend',refreshIcons);

/* pilih fasilitas */
function selectFacility(id, fly){
    const f=byId[id]; if(!f) return;
    const prev=st.sel; st.sel=id;
    if(prev && markerById[prev]) markerById[prev].setIcon(facIcon(byId[prev]));
    markerById[id].setIcon(facIcon(f));
    Object.keys(itemById).forEach(k=>itemById[k].classList.toggle('on',+k===+id));
    if(itemById[id]) itemById[id].scrollIntoView({block:'nearest',behavior:'smooth'});
    pickLayer.clearLayers(); PICK=null;
    if(fly){
        map.flyTo([f.latitude,f.longitude],Math.max(map.getZoom(),13),{duration:1.1});
        setTimeout(function(){ markerById[id].openPopup(); },1150);
    }
    const tok=++pickTok;
    $('pickBox').innerHTML='<div class="skl"></div>';
    infoAt(f.latitude,f.longitude).then(function(info){
        if(tok!==pickTok) return;
        $('pickBox').innerHTML=renderFacilityPanel(f,info);
        markerById[id].setIcon(facIcon(f));
    });
}
function renderFacilityPanel(f,info){
    return '<div class="pnl-stack"><div class="lastloc"><i class="fa-solid '+(TYPE_ICON[f.type]||'fa-location-dot')+'" style="background:'+(TYPE_COLOR[f.type]||'#2563eb')+'"></i><div><b>'+esc(f.name)+'</b><small>'+esc(TYPE_LABEL[f.type]||f.type)+' · '+esc(f.facility_code)+' · '+(f.status==='ACTIVE'?'Aktif':'Tidak aktif')+'<br>'+esc([f.address,f.city,f.province,f.country].filter(Boolean).join(', '))+'<br>'+f.latitude.toFixed(5)+', '+f.longitude.toFixed(5)+'</small></div></div>'+
        '<div class="pbtns"><button type="button" class="btn btn-light btn-sm" onclick="showDetailById('+Number(f.id)+')"><i class="fa-solid fa-eye"></i> Detail lengkap</button></div>'+
        '<div class="wc"><div class="wt"><span class="wi" style="background:#2563eb"><i class="fa-solid fa-cloud-sun-rain"></i></span>CUACA SAAT INI</div>'+weatherHtml(info)+'</div>'+
        '<div class="wc dark"><div class="wt"><span class="wi" style="background:#38bdf8"><i class="fa-regular fa-clock"></i></span>WAKTU SETEMPAT</div>'+timeHtml(info)+'</div></div>';
}
function focusOnMap(id){
    if(!byId[id]) return;
    $('peta').scrollIntoView({behavior:'smooth',block:'start'});
    setTimeout(function(){ selectFacility(id,true); },450);
}
$('detailMapBtn').addEventListener('click',function(){
    if(!currentDetail) return;
    closeModal('detailModal');
    focusOnMap(currentDetail.id);
});

/* titik pilihan bebas (klik peta / hasil pencarian) */
function drawPick(){
    pickLayer.clearLayers();
    if(!PICK) return;
    const {lat,lng,name}=PICK;
    L.marker([lat,lng],{icon:pin('#e5484d','fa-location-dot','big',shortName(name),badge(lat,lng)),zIndexOffset:1000})
        .bindPopup('<div class="pop"><strong>'+esc(shortName(name)||'Titik terpilih')+'</strong><small>'+lat.toFixed(5)+', '+lng.toFixed(5)+'</small>'+ctxHtml(lat,lng)+'</div>').addTo(pickLayer);
}
async function selectPoint(lat,lng,name){
    const tok=++pickTok;
    const prev=st.sel; st.sel=null;
    if(prev && markerById[prev]) markerById[prev].setIcon(facIcon(byId[prev]));
    Object.keys(itemById).forEach(k=>itemById[k].classList.remove('on'));
    PICK={lat,lng,name:name||null};
    drawPick();
    $('pickBox').innerHTML='<div class="skl"></div>';
    const infoP=infoAt(lat,lng);
    const nameP=name?Promise.resolve(name):reverse(lat,lng,Math.max(3,Math.min(18,Math.round(map.getZoom())+1)));
    const info=await infoP;
    if(tok!==pickTok) return;
    if(st.view!=='loc') drawPick();
    $('pickBox').innerHTML=renderPick(PICK,info);
    const nm=await nameP;
    if(tok!==pickTok) return;
    PICK.name=nm||null;
    drawPick();
    $('pickBox').innerHTML=renderPick(PICK,info);
}
function renderPick(p,info){
    const title=p.name?shortName(p.name):'Titik di peta';
    return '<div class="pnl-stack"><div class="lastloc"><i class="fa-solid fa-location-dot"></i><div><b>'+esc(title)+'</b><small>'+(p.name?esc(p.name)+'<br>':'')+p.lat.toFixed(5)+', '+p.lng.toFixed(5)+'</small></div></div>'+
        '<div class="wc"><div class="wt"><span class="wi" style="background:#2563eb"><i class="fa-solid fa-cloud-sun-rain"></i></span>CUACA SAAT INI</div>'+weatherHtml(info)+'</div>'+
        '<div class="wc dark"><div class="wt"><span class="wi" style="background:#38bdf8"><i class="fa-regular fa-clock"></i></span>WAKTU SETEMPAT</div>'+timeHtml(info)+'</div></div>';
}
map.on('click',e=>{ const c=e.latlng.wrap(); selectPoint(c.lat,c.lng,null); });

/* info di pusat peta (mode Cuaca / Waktu) */
function scheduleCenter(){
    clearTimeout(ctrTimer);
    const on=st.view!=='loc';
    $('xhair').classList.toggle('show',on); $('ctr').classList.toggle('show',on);
    if(on) ctrTimer=setTimeout(updateCenter,700);
}
async function updateCenter(){
    if(st.view==='loc') return;
    const c=map.getCenter().wrap(), tok=++ctrTok, box=$('ctr');
    box.innerHTML='<div class="skl" style="height:70px"></div>';
    const [info,nm]=await Promise.all([infoAt(c.lat,c.lng),reverse(c.lat,c.lng,Math.max(3,Math.min(18,Math.round(map.getZoom())+1)))]);
    if(tok!==ctrTok||st.view==='loc') return;
    const head='<div class="cn">'+esc(nm?shortName(nm):'Pusat peta')+'</div><div class="cc">'+c.lat.toFixed(4)+', '+c.lng.toFixed(4)+'</div>';
    let body;
    if(st.view==='wx'){
        if(info.ok){ const x=cond(info.c,info.day); body='<div class="cw"><span class="e">'+x[1]+'</span><div><b>'+Math.round(info.t)+'°C</b><small>'+x[0]+' · lembap '+info.h+'% · angin '+info.w+' km/j</small></div></div>'; }
        else body='<div class="cs">Data cuaca belum tersedia.</div>';
    }else{
        body='<div class="ct tmk" data-off="'+info.off+'">'+fmtClock(info.off)+'</div><div class="cs"><span class="tmd" data-off="'+info.off+'">'+fmtDate(info.off)+'</span><br>'+(info.tz?esc(info.tz)+' · ':'')+utcLabel(info.off)+'<br>'+diffText(info.off)+'</div>';
    }
    box.innerHTML=head+body+'<button type="button" class="go" id="ctrGo"><i class="fa-solid fa-circle-info"></i> Detail lokasi ini</button>';
    $('ctrGo').addEventListener('click',()=>selectPoint(c.lat,c.lng,nm||null));
}
map.on('moveend',function(){ scheduleCenter(); loadBadges(); });

/* pencarian tempat apa pun */
async function placeSearch(q){
    q=String(q||'').trim(); if(!q) return;
    $('mapQ').value=q; $('tmsg').textContent='Mencari…';
    const box=$('sres');
    try{
        await gate();
        const r=await fetchT('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=6&accept-language=id&q='+encodeURIComponent(q));
        const j=await r.json();
        $('tmsg').textContent='';
        if(!j.length){ box.innerHTML='<div class="none">Lokasi tidak ditemukan. Coba ejaan lain atau tambahkan nama daerah.</div>'; box.classList.add('open'); return; }
        box.innerHTML=j.map((x,n)=>'<button type="button" data-n="'+n+'"><i class="fa-solid fa-location-dot"></i><span><b>'+esc(shortName(x.display_name))+'</b>'+esc(x.display_name)+'</span></button>').join('');
        box.classList.add('open');
        box.querySelectorAll('button').forEach(b=>b.addEventListener('click',()=>{ goResult(j[+b.dataset.n]); box.classList.remove('open'); }));
        goResult(j[0]);
    }catch(_){ $('tmsg').textContent='Pencarian gagal, coba lagi.'; }
}
function goResult(x){
    const lat=+x.lat, lng=+x.lon, bb=x.boundingbox?x.boundingbox.map(Number):null;
    if(bb&&(Math.abs(bb[1]-bb[0])>0.02||Math.abs(bb[3]-bb[2])>0.02)) map.fitBounds([[bb[0],bb[2]],[bb[1],bb[3]]],{padding:[30,30],maxZoom:17});
    else map.flyTo([lat,lng],16,{duration:1.2});
    selectPoint(lat,lng,x.display_name);
}
$('mapQ').addEventListener('keydown',e=>{ if(e.key==='Enter'){ e.preventDefault(); placeSearch(e.target.value); } });
document.addEventListener('click',e=>{ if(!e.target.closest('.msearch')) $('sres').classList.remove('open'); });

/* mode tampilan */
document.querySelectorAll('#viewGroup .chip').forEach(b=>b.addEventListener('click',()=>{
    document.querySelectorAll('#viewGroup .chip').forEach(x=>x.classList.toggle('on',x===b));
    st.view=b.dataset.view;
    $('radarGroup').style.display=st.view==='wx'?'flex':'none';
    setRadar(st.view==='wx'&&st.radar);
    scheduleCenter();
    drawPick();
    refreshIcons();
    loadBadges();
}));
$('radarBtn').addEventListener('click',()=>{ st.radar=!st.radar; $('radarBtn').classList.toggle('on',st.radar); setRadar(st.view==='wx'&&st.radar); });

/* inisialisasi */
updateCount();
fitAll();

/* Buka kembali modal jika validasi gagal */
@if($errors->any() && old('_mode'))
(function(){
    const old=@json(old());
    $('facilityForm').reset();
    setMode(old._mode==='edit'?'edit':'create',old._id);
    fillForm(old);
    openFacilityModal();
})();
@endif
</script>
</body>
</html>
