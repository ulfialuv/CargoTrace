<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CargoTrace Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
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
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 50%,#1d6fe870 100%),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1200&q=60') center/cover;box-shadow:0 26px 54px #0b2a6b46}
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
.banner{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap;padding:26px 28px;color:#fff;background:linear-gradient(100deg,#061533ee,#0b2a6bcc 60%,#1d6fe855),url('https://images.unsplash.com/photo-1494412519320-aa613dfb7738?auto=format&fit=crop&w=1200&q=60') center/cover}
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
.search input:focus,.filters input:focus,.filters select:focus,.tool input:focus,.inc-filter input:focus,.inc-filter select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.map-wrap{position:relative;z-index:1}
#mainMap{height:640px;width:100%;background:#dfeafb}
#mainMap.pick{cursor:crosshair}
.map-info{position:absolute;left:16px;bottom:16px;z-index:500;display:flex;flex-wrap:wrap;gap:12px;max-width:calc(100% - 200px);padding:9px 14px;background:#ffffffee;border:1px solid var(--line);border-radius:12px;font-size:11px;color:#3d68a8;font-weight:600;box-shadow:0 8px 20px #0b2a6b22}
.map-info span{display:flex;align-items:center;gap:6px}.map-info i{display:inline-block;width:10px;height:10px;border-radius:50%}
.map-info .vi{display:grid;place-items:center;width:20px;height:20px;border-radius:50%;color:#fff;font-size:10px}
.map-info .sep{width:1px;height:16px;background:#dce8f8}
.map-empty{position:absolute;inset:0;z-index:600;display:none;place-items:center;background:#ffffffb8;backdrop-filter:blur(2px);text-align:center;padding:20px}
.map-empty.show{display:grid}
.map-empty div{max-width:380px;padding:26px;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 16px 40px #0b2a6b22;line-height:1.7;color:var(--mut)}
.map-empty i{display:grid;place-items:center;width:54px;height:54px;margin:0 auto 10px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:22px}
.map-empty strong{display:block;color:var(--navy);font-size:14px}

/* PIN + LABEL */
.pw{position:relative;width:34px;height:34px;display:grid;place-items:center}
.pin{display:grid;place-items:center;width:34px;height:34px;border-radius:50%;border:3px solid #fff;color:#fff;font-size:14px;box-shadow:0 4px 14px #0005}
.pin.sq{border-radius:11px;width:32px;height:32px;font-size:13px}
.pin.fleet{animation:pulse 2s infinite}
@keyframes pulse{0%{box-shadow:0 0 0 0 #f59e0bb0}70%{box-shadow:0 0 0 14px #f59e0b00}100%{box-shadow:0 0 0 0 #f59e0b00}}
.pl{position:absolute;top:38px;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:3px;pointer-events:none}
.vl{padding:2px 8px;border-radius:8px;background:#061533e6;color:#fff;font:700 9.5px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;box-shadow:0 3px 8px #0004}
.bd{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:9px;background:#fff;border:1px solid #cfe0f7;color:#1f4a8a;font:700 10px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;box-shadow:0 3px 10px #0b2a6b33}
.bd i{font-size:11px}
.bd em{font-style:normal;font-weight:600;color:#8ca6c4;font-size:9px}
.bd.bad{background:#fff1f1;border-color:#f5b5b5;color:#c92f3a}
.bd.storm{background:#c92f3a;border-color:#c92f3a;color:#fff}.bd.storm em{color:#ffd9dc}
.bd.dim{color:#8ca6c4}
.tp{width:10px;height:10px;border-radius:50%;background:#fff;border:3px solid var(--blue)}
.wxm{display:flex;align-items:center;gap:4px;padding:3px 7px;border-radius:10px;background:#fff;border:2px solid #94a3b8;font:700 10px 'Plus Jakarta Sans',sans-serif;color:#1f3b6b;white-space:nowrap;box-shadow:0 3px 8px #0b2a6b30}
.wpm{display:grid;place-items:center;width:26px;height:26px;border-radius:50%;background:#061533;color:#fff;border:2px solid #fff;font:800 11px 'Plus Jakarta Sans',sans-serif;box-shadow:0 3px 10px #0006}
.obm{display:grid;place-items:center;width:26px;height:26px;border-radius:50%;color:#fff;border:2px solid #fff;font-size:12px;box-shadow:0 3px 10px #0006}
.leaflet-popup-content-wrapper{border-radius:14px}
.pop{min-width:190px;font:12px/1.7 'Plus Jakarta Sans',sans-serif;color:#365775}.pop strong{display:block;font-size:13px;color:var(--navy)}.pop small{color:#8ca6c4}.pop a{color:var(--blue);font-weight:700}
.pop .veh{display:inline-flex;align-items:center;gap:6px;margin:2px 0;padding:2px 9px;border-radius:12px;background:#eef4ff;color:#1d4ed8;font-weight:700}
.pop .ctx{display:block;margin-top:4px;padding-top:4px;border-top:1px dashed #dce8f8}

/* KONTROL PETA */
.basebox{position:absolute;top:12px;right:12px;z-index:700;display:flex;gap:4px;padding:5px;background:#ffffffee;border:1px solid var(--line);border-radius:14px;box-shadow:0 8px 22px #0b2a6b2a;backdrop-filter:blur(8px)}
.bb{display:flex;flex-direction:column;align-items:center;gap:3px;min-width:64px;padding:7px 8px;border:0;border-radius:10px;background:transparent;color:#3d68a8;font:600 10px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.15s}
.bb i{font-size:15px}
.bb:hover{background:#eef6ff}
.bb.on{background:var(--blue);color:#fff;box-shadow:0 6px 14px #2563eb55}
.fleetbox{position:absolute;top:12px;left:60px;z-index:650;width:260px;background:#ffffffee;border:1px solid var(--line);border-radius:14px;box-shadow:0 8px 22px #0b2a6b2a;backdrop-filter:blur(8px);overflow:hidden}
.fleet-h{display:flex;align-items:center;justify-content:space-between;gap:8px;width:100%;padding:10px 13px;border:0;background:transparent;color:var(--navy);font:700 11.5px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.fleet-h b{display:inline-block;min-width:20px;padding:1px 7px;border-radius:10px;background:#f59e0b;color:#fff;font-size:10px;text-align:center}
.fleet-h i.ch{transition:transform .2s;color:#8ca6c4}
.fleetbox.shut .fleet-h i.ch{transform:rotate(-90deg)}
.fleetbox.shut #fleetList{display:none}
#fleetList{max-height:230px;overflow-y:auto;border-top:1px solid #edf3fa}
.fl{display:flex;align-items:center;gap:10px;width:100%;padding:9px 13px;border:0;border-bottom:1px solid #f1f6fc;background:transparent;text-align:left;cursor:pointer;font-family:inherit}
.fl:hover{background:#f3f8ff}
.fl .ic{flex:none;display:grid;place-items:center;width:28px;height:28px;border-radius:50%;color:#fff;font-size:12px}
.fl strong{display:block;font-size:11.5px;color:var(--navy)}
.fl small{display:block;font-size:10px;color:#8ca6c4;max-width:170px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.fl-empty{padding:14px;text-align:center;font-size:11px;color:#8ca6c4}
.clockbox{display:none;position:absolute;top:12px;left:50%;transform:translateX(-50%);z-index:640;gap:16px;padding:9px 18px;background:#061533ee;color:#fff;border-radius:14px;box-shadow:0 8px 22px #0b2a6b55}
.clockbox.show{display:flex}
.clockbox div{text-align:center}
.clockbox b{display:block;font:800 17px var(--h);letter-spacing:.5px}
.clockbox small{font-size:9.5px;color:#9fbcea}

/* PANEL ALAT DI PETA (cuaca + uji rute) */
.rstack{position:absolute;top:84px;right:12px;z-index:660;display:flex;flex-direction:column;gap:10px;width:310px;max-height:calc(100% - 100px);overflow-y:auto;pointer-events:none}
.rstack>*{pointer-events:auto}
.tool{background:#fffffff2;border:1px solid var(--line);border-radius:14px;box-shadow:0 8px 22px #0b2a6b2a;backdrop-filter:blur(8px);overflow:hidden}
.tool-h{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:11px 13px;border-bottom:1px solid #edf3fa;color:var(--navy);font-size:12px}
.tool-h b{display:flex;align-items:center;gap:7px}
.tool-h .x{border:0;background:transparent;color:#8ca6c4;font-size:15px;cursor:pointer}
.tsum{font-size:10px;font-weight:600;color:#8ca6c4}
.tool-b{padding:12px 13px}
.tool-row{display:flex;gap:6px;flex-wrap:wrap;margin:8px 0}
.tool input,.tool select{width:100%;padding:8px 10px;border:1px solid #dce8f8;border-radius:9px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.wp{display:flex;align-items:center;gap:6px;margin-bottom:6px}
.wp .n{flex:none;display:grid;place-items:center;width:22px;height:22px;border-radius:50%;background:#061533;color:#fff;font-size:10px;font-weight:800}
.wp button{flex:none;border:0;background:transparent;color:#c0cfe6;cursor:pointer;font-size:13px}
.wp button:hover{color:#e5484d}
.res{margin-top:10px;padding:10px 12px;border-radius:12px;font-size:11.5px;line-height:1.6}
.res.ok{background:#e8f7ef;color:#1f7a4d}.res.warn{background:#fff4dc;color:#946000}.res.bad{background:#ffe9e9;color:#b02a35}
.res b{display:block;font-size:12.5px}
.kv{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin:10px 0}
.kv div{padding:8px 10px;border-radius:10px;background:#f3f8ff;color:#6c83ab;font-size:10px}
.kv b{display:block;font:700 14px var(--h);color:var(--navy);margin-top:2px}
.obs{list-style:none;margin-top:6px;max-height:190px;overflow-y:auto}
.obs li{display:flex;gap:8px;padding:7px 0;border-bottom:1px solid #f1f6fc;font-size:11px;color:#41608a;line-height:1.5}
.obs li i{flex:none;margin-top:3px}
.hint{font-size:10.5px;color:#8ca6c4;line-height:1.6}
.wxhead{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;margin-bottom:8px;font-size:11.5px;font-weight:700}
.wxhead.ok{background:#e8f7ef;color:#1f7a4d}.wxhead.bad{background:#ffe9e9;color:#b02a35}.wxhead.warn{background:#fff4dc;color:#946000}
.wxhead b{font:800 20px var(--h)}
#wxList{max-height:260px;overflow-y:auto}
.wxi{display:flex;align-items:center;gap:9px;width:100%;padding:8px 4px;border:0;border-bottom:1px solid #f1f6fc;background:transparent;text-align:left;cursor:pointer;font-family:inherit}
.wxi:hover{background:#f3f8ff}
.wxi .dl{flex:none;width:10px;height:10px;border-radius:50%}
.wxi strong{display:block;font-size:11.5px;color:var(--navy)}
.wxi small{display:block;font-size:10px;color:#8ca6c4}

/* TABEL */
.filters{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}
.filters input,.filters select{padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:var(--ink);font:12px 'Plus Jakarta Sans',sans-serif}
.filters input{flex:1;min-width:200px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 16px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:10px;cursor:pointer;font:700 11px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;transition:.2s}
.btn:hover{filter:brightness(1.1)}.btn-light{background:#e8f1ff;color:#1d4ed8}
.btn-green{background:linear-gradient(135deg,#22c55e,#15803d)}
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

/* SIDEBAR KANAN: PEMASUKAN */
.inc-tab{position:fixed;right:0;top:46%;transform:translateY(-50%);z-index:1100;display:flex;align-items:center;gap:8px;padding:16px 9px;border:0;border-radius:14px 0 0 14px;background:linear-gradient(180deg,#22c55e,#15803d);color:#fff;font:700 11px 'Plus Jakarta Sans',sans-serif;letter-spacing:.5px;writing-mode:vertical-rl;cursor:pointer;box-shadow:-6px 8px 22px #15803d55}
.inc-tab i{writing-mode:horizontal-tb}
.inc-back{position:fixed;inset:0;background:#06153366;z-index:1390;display:none}
.inc-back.show{display:block}
.inc-drawer{position:fixed;top:0;right:0;bottom:0;width:440px;max-width:100%;display:flex;flex-direction:column;background:#fff;z-index:1400;transform:translateX(104%);transition:transform .3s;box-shadow:-20px 0 50px #0b2a6b33}
.inc-drawer.open{transform:none}
.inc-head{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:20px 22px;background:linear-gradient(135deg,#061533,#0b2a6b 70%,#15803d);color:#fff}
.inc-head h2{font:800 17px var(--h)}.inc-head p{margin-top:3px;font-size:11px;color:#b9d2f7}
.inc-head .x{display:grid;place-items:center;width:34px;height:34px;border:0;border-radius:10px;background:#ffffff22;color:#fff;font-size:14px;cursor:pointer}
.inc-body{flex:1;overflow-y:auto;padding:18px 22px}
.seg{display:grid;grid-template-columns:repeat(3,1fr);gap:4px;padding:4px;border-radius:12px;background:#eef3fc}
.seg button{padding:9px;border:0;border-radius:9px;background:transparent;color:#5d7aa5;font:700 11.5px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.seg button.on{background:#fff;color:var(--blue);box-shadow:0 3px 10px #2563eb22}
.inc-filter{margin:14px 0}
.inc-filter input,.inc-filter select{width:100%;padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.inc-sum{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px}
.inc-sum div{padding:13px 14px;border-radius:14px;background:#f3f8ff;color:#6c83ab;font-size:10.5px;font-weight:600}
.inc-sum div.big{grid-column:1/-1;background:linear-gradient(135deg,#e8f7ef,#d4f1e0);color:#1f7a4d}
.inc-sum b{display:block;margin-top:4px;font:800 16px var(--h);color:var(--navy)}
.inc-sum .big b{font-size:24px;color:#15803d}
.inc-chart{position:relative;height:170px;margin-bottom:14px}
.inc-warn{margin-bottom:14px;padding:11px 13px;border-radius:12px;background:#fff4dc;color:#946000;font-size:11px;line-height:1.6}
.inc-table{width:100%;min-width:0;font-size:11.5px}
.inc-table th{padding:10px;font-size:10.5px}
.inc-table td{padding:10px;white-space:normal}
.inc-table td.r,.inc-table th.r{text-align:right}
.inc-table tr.zero td{color:#b3c4dd}
.inc-foot{padding:14px 22px;border-top:1px solid #edf3fa;display:flex;gap:10px}
.inc-foot .btn{flex:1}

/* CETAK */
#printReport{display:none}
#printReport h1{font:800 20px 'Sora',sans-serif;color:#061533}
#printReport .sub{margin:4px 0 14px;color:#555;font-size:12px}
#printReport table{min-width:0;width:100%;border-collapse:collapse;font-size:12px}
#printReport th{background:#eef3fc;color:#222;border:1px solid #cfd8e8;padding:8px 10px}
#printReport td{border:1px solid #cfd8e8;padding:7px 10px;color:#222}
#printReport td.r,#printReport th.r{text-align:right}
#printReport tfoot td{font-weight:800;background:#f3f8ff}
#printReport .sum{display:flex;gap:24px;margin:0 0 14px;font-size:12px}
#printReport .sum b{display:block;font-size:16px}
#printReport .foot{margin-top:18px;font-size:10.5px;color:#777}
@media print{
 @page{margin:14mm}
 body.printing{background:#fff}
 body.printing>*:not(#printReport){display:none!important}
 body.printing #printReport{display:block}
}

/* RESPONSIVE */
@media(max-width:1350px){.stats{grid-template-columns:repeat(3,minmax(0,1fr))}.hero-side{display:none}}
@media(max-width:1100px){.r-a,.r-b,.r-c,.news-grid{grid-template-columns:1fr}.content{padding:20px}.search{margin-left:0;width:100%}.fleetbox{display:none}.map-info{max-width:calc(100% - 32px)}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .topline{flex-direction:column;align-items:flex-start}.hero{padding:26px 22px 96px}.hero h2{font-size:22px}
 .stats{grid-template-columns:1fr 1fr;margin:-70px 8px 20px}
 #mainMap{height:480px}.map-info{display:none}.panel{padding:18px}.toolbar{padding:14px}.notif-panel{right:auto;left:-120px;width:280px}
 .bb{min-width:0;padding:8px 9px}.bb span{display:none}.clockbox{top:64px}
 .rstack{width:calc(100% - 24px);top:70px}
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
            <button type="button" class="btn btn-green" id="incomeOpen"><i class="fa-solid fa-sack-dollar"></i> Pemasukan</button>
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
            <div><h2>Peta Persebaran Pengiriman</h2><p>Asal, tujuan, rute jalan, armada bergerak, cuaca, pelabuhan, bandara, dan depo kargo. Zoom sampai level jalan dan bangunan.</p></div>
            <div class="kpis">
                <div class="kpi"><b id="kShip">0</b>shipment di peta</div>
                <div class="kpi"><b id="kFleet">0</b>armada bergerak</div>
                <div class="kpi"><b id="kFac">0</b>fasilitas</div>
            </div>
        </div>
        <div class="toolbar">
            <div class="tg" id="viewGroup">
                <span class="tl">TAMPILAN</span>
                <button class="chip on" data-view="loc"><i class="fa-solid fa-location-dot"></i> Lokasi</button>
                <button class="chip" data-view="wx"><i class="fa-solid fa-cloud-sun-rain"></i> Cuaca</button>
                <button class="chip" data-view="time"><i class="fa-regular fa-clock"></i> Waktu</button>
            </div>
            <div class="tg" id="radarGroup" style="display:none">
                <span class="tl">CUACA</span>
                <button class="chip on" id="radarBtn" type="button"><i class="fa-solid fa-satellite-dish"></i> Radar Hujan</button>
            </div>
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
            <div class="tg">
                <span class="tl">ALAT</span>
                <button class="chip" id="toolBtn" type="button"><i class="fa-solid fa-route"></i> Uji Rute &amp; Hambatan</button>
            </div>
            <div class="search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="mapQ" placeholder="Cari fasilitas, shipment, atau lokasi..." aria-label="Cari di peta">
            </div>
        </div>
        <div class="map-wrap">
            <div id="mainMap"></div>

            <!-- Ganti jenis peta -->
            <div class="basebox" id="baseBox" role="group" aria-label="Jenis peta">
                <button class="bb on" data-base="default" type="button" title="Peta standar"><i class="fa-solid fa-map"></i><span>Default</span></button>
                <button class="bb" data-base="sat" type="button" title="Citra satelit"><i class="fa-solid fa-satellite"></i><span>Satelit</span></button>
                <button class="bb" data-base="terrain" type="button" title="Kontur medan"><i class="fa-solid fa-mountain"></i><span>Medan</span></button>
                <button class="bb" data-base="transit" type="button" title="Jalur transportasi umum"><i class="fa-solid fa-train-subway"></i><span>Transportasi</span></button>
                <button class="bb" data-base="traffic" type="button" title="Kondisi lalu lintas + rute jalan pengiriman darat"><i class="fa-solid fa-traffic-light"></i><span>Lalu lintas</span></button>
            </div>

            <!-- Daftar armada -->
            <div class="fleetbox" id="fleetBox">
                <button class="fleet-h" id="fleetToggle" type="button" aria-expanded="true">
                    <span><i class="fa-solid fa-truck-fast" style="color:#f59e0b"></i> Armada bergerak <b id="fleetCount">0</b></span>
                    <i class="fa-solid fa-chevron-down ch"></i>
                </button>
                <div id="fleetList"></div>
            </div>

            <!-- Jam (mode Waktu) -->
            <div class="clockbox" id="clockBox">
                <div><b id="ckLocal">--:--:--</b><small id="ckZone">Waktu Anda</small></div>
                <div><b id="ckUtc">--:--:--</b><small>UTC</small></div>
            </div>

            <!-- Panel kanan di dalam peta: cuaca + uji rute -->
            <div class="rstack">
                <div class="tool" id="wxPanel" style="display:none">
                    <div class="tool-h"><b><i class="fa-solid fa-cloud-bolt" style="color:#f59e0b"></i> Pantauan Cuaca</b><span class="tsum" id="wxSum"></span></div>
                    <div class="tool-b">
                        <div id="wxHead" class="wxhead ok">Memuat cuaca…</div>
                        <div class="tool-row">
                            <button class="chip" id="wxScan" type="button"><i class="fa-solid fa-satellite-dish"></i> Pindai area peta</button>
                            <button class="chip" id="wxClear" type="button" style="display:none"><i class="fa-solid fa-xmark"></i> Hapus pindaian</button>
                        </div>
                        <div id="wxList"></div>
                        <p class="hint" style="margin-top:8px">Data cuaca: Open-Meteo. “Pindai area” mengecek 35 titik di area peta yang sedang terlihat, jadi zoom ke wilayah yang ingin dicek dulu.</p>
                    </div>
                </div>

                <div class="tool" id="routeTool" style="display:none">
                    <div class="tool-h"><b><i class="fa-solid fa-route" style="color:#2563eb"></i> Uji Rute &amp; Hambatan</b><button class="x" id="toolClose" type="button" aria-label="Tutup">&times;</button></div>
                    <div class="tool-b">
                        <div id="wpList"></div>
                        <div class="tool-row">
                            <button class="chip" id="wpAdd" type="button"><i class="fa-solid fa-plus"></i> Titik</button>
                            <button class="chip" id="wpPick" type="button"><i class="fa-solid fa-hand-pointer"></i> Klik di peta</button>
                            <button class="chip" id="wpReset" type="button"><i class="fa-solid fa-rotate-left"></i> Reset</button>
                        </div>
                        <button class="btn" id="wpRun" type="button" style="width:100%"><i class="fa-solid fa-magnifying-glass-location"></i> Cek rute &amp; hambatan</button>
                        <div id="wpOut"></div>
                        <p class="hint" style="margin-top:8px">Isi nama kota/fasilitas, alamat, atau koordinat (<i>-6.2,106.8</i>). Boleh lebih dari 2 titik (A → B → C).</p>
                    </div>
                </div>
            </div>
            <datalist id="placeList"></datalist>

            <div class="map-empty" id="mapEmpty">
                <div><i class="fa-solid fa-map-location-dot"></i><strong>Belum ada titik yang bisa digambar</strong>
                Peta butuh koordinat. Isi <b>latitude/longitude</b> pada fasilitas, atau pastikan nama kota asal &amp; tujuan shipment cocok dengan nama fasilitas / kota yang dikenal.</div>
            </div>
            <div class="map-info">
                <span><i style="background:#2563eb"></i> Asal</span>
                <span><i style="background:#16a34a"></i> Tujuan</span>
                <span class="sep"></span>
                <span><b class="vi" style="background:#2563eb"><i class="fa-solid fa-plane" style="background:none;width:auto;height:auto"></i></b> Pesawat</span>
                <span><b class="vi" style="background:#0f9d9a"><i class="fa-solid fa-ship" style="background:none;width:auto;height:auto"></i></b> Kapal</span>
                <span><b class="vi" style="background:#f59e0b"><i class="fa-solid fa-truck-fast" style="background:none;width:auto;height:auto"></i></b> Truk / darat</span>
                <span class="sep"></span>
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

<!-- SIDEBAR KANAN: PEMASUKAN -->
<button type="button" class="inc-tab" id="incomeTab" aria-label="Buka pemasukan"><i class="fa-solid fa-sack-dollar"></i> PEMASUKAN</button>
<div class="inc-back" id="incBack"></div>
<aside class="inc-drawer" id="incDrawer" aria-hidden="true" aria-label="Laporan pemasukan">
    <div class="inc-head">
        <div><h2>Pemasukan</h2><p>Harian, bulanan, dan tahunan. Bisa dicetak.</p></div>
        <button type="button" class="x" id="incClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="inc-body">
        <div class="seg" id="incSeg">
            <button type="button" class="on" data-t="day">Harian</button>
            <button type="button" data-t="month">Bulanan</button>
            <button type="button" data-t="year">Tahunan</button>
        </div>
        <div class="inc-filter" id="incFilter"></div>
        <div id="incWarn"></div>
        <div class="inc-sum">
            <div class="big">Total pemasukan<b id="incTotal">Rp 0</b></div>
            <div>Jumlah shipment<b id="incCount">0</b></div>
            <div>Rata-rata / <span id="incUnit">hari</span><b id="incAvg">Rp 0</b></div>
            <div style="grid-column:1/-1">Puncak tertinggi<b id="incPeak" style="font-size:13px">-</b></div>
        </div>
        <div class="inc-chart"><canvas id="incChart"></canvas></div>
        <div class="table-wrap">
            <table class="inc-table">
                <thead><tr><th>Periode</th><th class="r">Shipment</th><th class="r">Pemasukan</th></tr></thead>
                <tbody id="incRows"></tbody>
            </table>
        </div>
    </div>
    <div class="inc-foot">
        <button type="button" class="btn" id="incPrint"><i class="fa-solid fa-print"></i> Cetak laporan</button>
    </div>
</aside>

<!-- Area cetak (hanya tampil saat print) -->
<div id="printReport"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
const $ = id => document.getElementById(id);
const fetchT = (url, ms, opt) => {
    const c = new AbortController(), t = setTimeout(() => c.abort(), ms || 12000);
    return fetch(url, Object.assign({ signal:c.signal }, opt || {})).finally(() => clearTimeout(t));
};

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

/* ===== PEMASUKAN (sidebar kanan + cetak) ===== */
(function () {
    const D = @json($income), USER = @json(auth()->user()->name ?? 'Administrator');
    const idr = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
    const MN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const pad = n => String(n).padStart(2, '0');
    const now = new Date(), CY = now.getFullYear(), CM = now.getMonth() + 1;
    let tab = 'day', mVal = CY + '-' + pad(CM), yVal = String(CY), chart = null, opened = false;

    const dr = $('incDrawer'), back = $('incBack');
    function open() { dr.classList.add('open'); back.classList.add('show'); dr.setAttribute('aria-hidden', 'false'); if (!opened) { opened = true; } update(); }
    function close() { dr.classList.remove('open'); back.classList.remove('show'); dr.setAttribute('aria-hidden', 'true'); }
    $('incomeOpen').addEventListener('click', open); $('incomeTab').addEventListener('click', open);
    $('incClose').addEventListener('click', close); back.addEventListener('click', close);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

    function rows() {
        if (tab === 'day') {
            const [y, m] = mVal.split('-').map(Number), isCur = (y === CY && m === CM), n = isCur ? now.getDate() : new Date(y, m, 0).getDate(), map = {};
            D.daily.forEach(r => { if (r.k.startsWith(mVal)) map[r.k] = r; });
            return Array.from({ length:n }, (_, i) => { const k = mVal + '-' + pad(i + 1), r = map[k] || { c:0, t:0 };
                return { label:pad(i + 1) + ' ' + MN[m - 1].slice(0, 3) + ' ' + y, short:String(i + 1), c:r.c, t:r.t }; });
        }
        if (tab === 'month') {
            const map = {}; D.months.forEach(r => map[r.k] = r);
            const n = String(CY) === yVal ? CM : 12;
            return MN.slice(0, n).map((nm, i) => { const r = map[yVal + '-' + pad(i + 1)] || { c:0, t:0 }; return { label:nm + ' ' + yVal, short:nm.slice(0, 3), c:r.c, t:r.t }; });
        }
        const map = {}; D.years.forEach(r => map[r.k] = r);
        const first = Math.min(CY - 4, ...D.years.map(r => +r.k)), out = [];
        for (let y = first; y <= CY; y++) { const r = map[y] || { c:0, t:0 }; out.push({ label:String(y), short:String(y), c:r.c, t:r.t }); }
        return out;
    }
    const periodText = () => tab === 'day' ? 'Bulan ' + MN[+mVal.split('-')[1] - 1] + ' ' + mVal.split('-')[0] : tab === 'month' ? 'Tahun ' + yVal : 'Semua tahun';
    const tabName = () => ({ day:'Harian', month:'Bulanan', year:'Tahunan' })[tab];

    function filterUI() {
        const f = $('incFilter');
        if (tab === 'day') f.innerHTML = '<input type="month" id="fMonth" value="' + mVal + '" max="' + CY + '-' + pad(CM) + '" aria-label="Pilih bulan">';
        else if (tab === 'month') {
            const ys = new Set([CY, CY - 1, CY - 2, CY - 3, CY - 4]); D.years.forEach(r => ys.add(+r.k));
            f.innerHTML = '<select id="fYear" aria-label="Pilih tahun">' + [...ys].sort((a, b) => b - a).map(y => '<option value="' + y + '"' + (String(y) === yVal ? ' selected' : '') + '>Tahun ' + y + '</option>').join('') + '</select>';
        } else f.innerHTML = '';
        if ($('fMonth')) $('fMonth').addEventListener('change', e => { if (e.target.value) { mVal = e.target.value; update(true); } });
        if ($('fYear')) $('fYear').addEventListener('change', e => { yVal = e.target.value; update(true); });
    }

    function update(keepFilter) {
        if (!keepFilter) filterUI();
        const r = rows(), total = r.reduce((a, x) => a + x.t, 0), cnt = r.reduce((a, x) => a + x.c, 0), peak = r.reduce((a, x) => x.t > a.t ? x : a, { t:-1 });
        $('incWarn').innerHTML = D.available ? '' : '<div class="inc-warn"><b>Kolom biaya belum ditemukan.</b><br>Tambahkan kolom nominal (mis. <code>total_price</code>) di tabel <code>shipments</code>, atau daftarkan nama kolommu di <code>PRICE_COLUMNS</code> pada DashboardController. Sementara ini hanya jumlah shipment yang tampil.</div>';
        $('incTotal').textContent = idr(total); $('incCount').textContent = new Intl.NumberFormat('id-ID').format(cnt);
        $('incUnit').textContent = ({ day:'hari', month:'bulan', year:'tahun' })[tab];
        $('incAvg').textContent = idr(r.length ? total / r.length : 0);
        $('incPeak').textContent = peak.t > 0 ? peak.label + ' · ' + idr(peak.t) : '-';
        $('incRows').innerHTML = r.map(x => '<tr class="' + (x.c ? '' : 'zero') + '"><td>' + esc(x.label) + '</td><td class="r">' + x.c + '</td><td class="r">' + idr(x.t) + '</td></tr>').join('');
        document.querySelectorAll('#incSeg button').forEach(b => b.classList.toggle('on', b.dataset.t === tab));

        if (chart) chart.destroy();
        chart = new Chart($('incChart'), { type:'bar',
            data:{ labels:r.map(x => x.short), datasets:[{ data:r.map(x => D.available ? x.t : x.c), backgroundColor:'#22c55e', borderRadius:5, maxBarThickness:18 }] },
            options:{ maintainAspectRatio:false, animation:{ duration:250 }, plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ title:c => r[c[0].dataIndex].label, label:c => D.available ? idr(c.parsed.y) : c.parsed.y + ' shipment' } } },
                scales:{ y:{ beginAtZero:true, grid:{ color:'#edf3fa' }, border:{ display:false }, ticks:{ maxTicksLimit:4, callback:v => D.available ? (v >= 1e6 ? (v / 1e6) + ' jt' : v >= 1e3 ? (v / 1e3) + ' rb' : v) : v } }, x:{ grid:{ display:false }, ticks:{ maxTicksLimit:10 }, border:{ display:false } } } } });
    }
    $('incSeg').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; tab = b.dataset.t; update(); });

    $('incPrint').addEventListener('click', () => {
        const r = rows(), total = r.reduce((a, x) => a + x.t, 0), cnt = r.reduce((a, x) => a + x.c, 0), peak = r.reduce((a, x) => x.t > a.t ? x : a, { t:-1 });
        $('printReport').innerHTML =
            '<h1>CargoTrace — Laporan Pemasukan ' + tabName() + '</h1>' +
            '<p class="sub">Periode: <b>' + esc(periodText()) + '</b> · Dicetak ' + esc(new Date().toLocaleString('id-ID', { dateStyle:'long', timeStyle:'short' })) + ' oleh ' + esc(USER) + '</p>' +
            '<div class="sum"><div>Total pemasukan<b>' + idr(total) + '</b></div><div>Jumlah shipment<b>' + cnt + '</b></div><div>Puncak<b>' + (peak.t > 0 ? esc(peak.label) : '-') + '</b></div></div>' +
            '<table><thead><tr><th>Periode</th><th class="r">Shipment</th><th class="r">Pemasukan</th></tr></thead><tbody>' +
            r.map(x => '<tr><td>' + esc(x.label) + '</td><td class="r">' + x.c + '</td><td class="r">' + idr(x.t) + '</td></tr>').join('') +
            '</tbody><tfoot><tr><td>Total</td><td class="r">' + cnt + '</td><td class="r">' + idr(total) + '</td></tr></tfoot></table>' +
            '<p class="foot">Pemasukan dihitung dari shipment yang tidak dibatalkan' + (D.available ? '' : ' (kolom biaya belum tersedia)') + '. Dokumen dibuat otomatis oleh sistem CargoTrace.</p>';
        document.body.classList.add('printing');
        setTimeout(() => window.print(), 60);
    });
    window.addEventListener('afterprint', () => document.body.classList.remove('printing'));
})();

/* ===== BERITA (dimuat setelah halaman selesai agar tidak menahan dashboard) ===== */
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
            const res = await fetchT(url, 10000, { headers:{ 'Accept':'application/json' } }), data = await res.json();
            if (my !== seq) return;
            if (!res.ok || data.success === false) throw new Error(data.message || 'HTTP ' + res.status);
            const list = Array.isArray(data.articles) ? data.articles : [];
            list.length ? renderNews(list) : msg('Belum ada berita untuk kategori ini.<br>Coba kategori lain.');
        } catch (e) {
            if (my !== seq) return;
            msg(esc(e.name === 'AbortError' ? 'Berita terlalu lama dimuat.' : (e.message || 'Berita belum dapat dimuat.')) + '<br><button class="btn" id="retry" type="button">Coba lagi</button>');
            $('retry').addEventListener('click', load);
        }
    }
    $('newsCats').addEventListener('click', e => {
        const b = e.target.closest('.cat'); if (!b) return;
        document.querySelectorAll('#newsCats .cat').forEach(x => x.classList.toggle('on', x === b));
        cat = b.dataset.c; load();
    });
    body.innerHTML = '<div class="skel" style="min-height:370px"></div><div style="display:grid;gap:10px"><div class="skel"></div><div class="skel"></div><div class="skel"></div></div>';
    const go = () => setTimeout(load, 1200);
    if (document.readyState === 'complete') go(); else window.addEventListener('load', go);
})();

/* ===== PETA ===== */
(function () {
    /* Opsional: isi API key TomTom (gratis di developer.tomtom.com).
       Kalau diisi: lapisan lalu lintas jadi resmi, dan "Uji Rute" menampilkan kemacetan, penutupan jalan,
       dan tambahan waktu akibat lalu lintas secara nyata. Kalau kosong: rute pakai OSRM (tanpa data macet),
       kemacetan tetap bisa dilihat visual lewat tombol "Lalu lintas". */
    const TOMTOM_KEY = '';

    const facilities = @json($facilities), shipData = @json($mapData);

    const cities = {
        'banda aceh':[5.5483,95.3238],'aceh':[5.5483,95.3238],'sabang':[5.8897,95.3164],'medan':[3.5952,98.6722],'kabanjahe':[3.1001,98.4908],'belawan':[3.7826,98.6930],'binjai':[3.6001,98.4854],
        'rantauprapat':[2.1000,99.8333],'pematangsiantar':[2.9595,99.0687],'tebing tinggi':[3.3285,99.1625],'padang sidempuan':[1.3789,99.2720],'sibolga':[1.7427,98.7792],
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
    const cap = s => s.replace(/\b\w/g, m => m.toUpperCase());
    const num = v => (v === null || v === '' || v === undefined) ? NaN : parseFloat(v);
    const hav = (a, b) => { const R = 6371000, r = Math.PI / 180, dLa = (b[0] - a[0]) * r, dLo = (b[1] - a[1]) * r,
        x = Math.sin(dLa / 2) ** 2 + Math.cos(a[0] * r) * Math.cos(b[0] * r) * Math.sin(dLo / 2) ** 2; return 2 * R * Math.asin(Math.sqrt(x)); };
    const nearestCity = ll => { let best = null, bd = 1e12; for (const c in cities) { const d = hav(ll, cities[c]); if (d < bd) { bd = d; best = c; } } return best && bd < 250000 ? cap(best) : null; };
    const decimate = (c, max) => { if (c.length <= max) return c; const k = Math.ceil(c.length / max), o = []; for (let i = 0; i < c.length; i += k) o.push(c[i]); o.push(c[c.length - 1]); return o; };

    const vehicleOf = t => {
        t = String(t || '').toLowerCase();
        if (/air|udara|plane|pesawat|flight/.test(t)) return { mode:'air', name:'Pesawat', icon:'fa-plane', color:'#2563eb' };
        if (/sea|laut|ship|kapal|ocean|vessel|maritim/.test(t)) return { mode:'sea', name:'Kapal', icon:'fa-ship', color:'#0f9d9a' };
        if (/rail|train|kereta/.test(t)) return { mode:'land', name:'Kereta', icon:'fa-train', color:'#7c3aed' };
        if (/motor|bike|ojek/.test(t)) return { mode:'land', name:'Motor', icon:'fa-motorcycle', color:'#e5484d' };
        if (/\bvan\b|mobil|\bcar\b|pickup/.test(t)) return { mode:'land', name:'Mobil boks', icon:'fa-van-shuttle', color:'#f59e0b' };
        return { mode:'land', name:'Truk', icon:'fa-truck-fast', color:'#f59e0b' };
    };
    const kindOf = f => { const t = String((f.type || '') + ' ' + (f.name || '')).toLowerCase(); return /port|pelabuhan|seaport|dermaga/.test(t) ? 'port' : (/air|bandara|apt|airport/.test(t) ? 'air' : 'depot'); };

    const META = {
        port:{ label:'Pelabuhan', icon:'fa-anchor', color:'#0f9d9a', mode:'sea' },
        air:{ label:'Bandara', icon:'fa-plane', color:'#2563eb', mode:'air' },
        depot:{ label:'Depo Kargo', icon:'fa-warehouse', color:'#6d5bd0', mode:'land' }
    };
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
        const veh = vehicleOf(s.transport_type), inTransit = String(s.status).toUpperCase() === 'IN_TRANSIT';
        const vnum = s.vehicle_number || '';
        const frac = .3 + ((Number(s.id) * 37) % 40) / 100;
        let line = null, pos = null;
        if (pts.length > 1) line = pts.map(p => [p.lat, p.lng]);
        else if (o && d) line = arc(o, d, 40);
        if (inTransit) {
            if (pts.length) pos = [pts[pts.length-1].lat, pts[pts.length-1].lng];
            else if (line) pos = line[Math.floor(line.length * frac)];
            else pos = o || d;
        }
        ships.push({ idx:ships.length, s, o, d, pts, line, pos, frac, veh, mode:veh.mode, vnum, exact:pts.length > 0, text:[s.number, s.customer, s.carrier, s.origin, s.destination, s.status, veh.name, vnum].join(' ').toLowerCase() });
    });

    /* hitungan chip moda */
    const cnt = { air:0, sea:0, land:0 };
    facs.forEach(i => cnt[i.mode]++); ships.forEach(i => cnt[i.mode]++);
    $('cAll').textContent = '(' + (facs.length + ships.length) + ')';
    $('cAir').textContent = '(' + cnt.air + ')'; $('cSea').textContent = '(' + cnt.sea + ')'; $('cLand').textContent = '(' + cnt.land + ')';

    /* peta baru dibuat saat bagian peta mendekati layar → halaman utama tidak berat */
    let booted = false;
    const bootWhenVisible = () => {
        const el = $('peta');
        if (!('IntersectionObserver' in window)) return boot();
        const io = new IntersectionObserver(es => { if (es.some(e => e.isIntersecting) && !booted) { io.disconnect(); boot(); } }, { rootMargin:'400px' });
        io.observe(el);
        if (location.hash === '#peta') boot();
    };
    bootWhenVisible();

    function boot() {
        if (booted) return; booted = true;

        const osm = () => L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap contributors', maxNativeZoom:19, maxZoom:19, keepBuffer:2 });
        const BASES = {
            default: osm(),
            sat: L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { subdomains:'0123', maxNativeZoom:20, maxZoom:19, keepBuffer:2, attribution:'Imagery &copy; Google' }),
            terrain: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', { subdomains:'abc', maxNativeZoom:17, maxZoom:19, keepBuffer:2, attribution:'&copy; OpenStreetMap, SRTM | Style &copy; OpenTopoMap (CC-BY-SA)' }),
            transit: L.layerGroup([osm(), L.tileLayer('https://tile.memomaps.de/tilegen/{z}/{x}/{y}.png', { maxNativeZoom:18, maxZoom:19, keepBuffer:2, attribution:'Map &copy; OpenStreetMap contributors, &Ouml;PNVKarte memomaps.de' })]),
            traffic: TOMTOM_KEY
                ? L.layerGroup([osm(), L.tileLayer('https://api.tomtom.com/traffic/map/4/tile/flow/relative0/{z}/{x}/{y}.png?key=' + encodeURIComponent(TOMTOM_KEY), { opacity:.85, maxNativeZoom:18, maxZoom:19, attribution:'Traffic &copy; TomTom' })])
                : L.tileLayer('https://mt{s}.google.com/vt/lyrs=m,traffic&x={x}&y={y}&z={z}', { subdomains:'0123', maxNativeZoom:19, maxZoom:19, keepBuffer:2, attribution:'Map data &copy; Google' })
        };

        const map = L.map('mainMap', { worldCopyJump:true, maxZoom:19, minZoom:2, preferCanvas:true, wheelDebounceTime:80 }).setView([2, 105], 4);
        let curBase = BASES.default.addTo(map), baseName = 'default';
        const layer = L.layerGroup().addTo(map);       // titik, garis, armada
        const toolLayer = L.layerGroup().addTo(map);   // hasil uji rute
        setTimeout(() => map.invalidateSize(), 250);

        const st = { view:'loc', mode:'all', q:'', radar:true, layers:{ fleet:true, port:true, air:true, depot:true }, scan:[], pick:false };

        function setBase(n) {
            baseName = n;
            document.querySelectorAll('#baseBox .bb').forEach(x => x.classList.toggle('on', x.dataset.base === n));
            map.removeLayer(curBase);
            curBase = BASES[n].addTo(map);
            if (curBase.bringToBack) curBase.bringToBack(); else if (curBase.eachLayer) curBase.eachLayer(l => l.bringToBack && l.bringToBack());
            render(false);
        }
        document.querySelectorAll('#baseBox .bb').forEach(b => b.addEventListener('click', () => setBase(b.dataset.base)));

        /* ===== CUACA (Open-Meteo) ===== */
        const wx = new Map();
        const key = (lat, lng) => (+lat).toFixed(1) + ',' + (+lng).toFixed(1);
        const wxInfo = (c, day) => {
            if (c === 0) return { t:'Cerah', icon: day ? 'fa-sun' : 'fa-moon' };
            if (c <= 2) return { t:'Cerah berawan', icon: day ? 'fa-cloud-sun' : 'fa-cloud-moon' };
            if (c === 3) return { t:'Berawan', icon:'fa-cloud' };
            if (c === 45 || c === 48) return { t:'Berkabut', icon:'fa-smog' };
            if (c >= 51 && c <= 57) return { t:'Gerimis', icon:'fa-cloud-rain' };
            if (c >= 61 && c <= 67) return { t:'Hujan', icon:'fa-cloud-showers-heavy' };
            if ((c >= 71 && c <= 77) || c === 85 || c === 86) return { t:'Salju', icon:'fa-snowflake' };
            if (c >= 80 && c <= 82) return { t:'Hujan lebat', icon:'fa-cloud-showers-heavy' };
            if (c >= 95) return { t:'Badai petir', icon:'fa-cloud-bolt' };
            return { t:'Berawan', icon:'fa-cloud' };
        };
        /* tingkat bahaya: 3 = badai/ekstrem, 2 = hujan lebat/angin kencang, 1 = waspada, 0 = aman */
        const SEV_COLOR = ['#16a34a', '#eab308', '#f97316', '#dc2626'];
        function sev(w) {
            if (!w || w.fail) return { lvl:-1, label:'' };
            const c = w.code, v = w.wind || 0, r = w.rain || 0, base = wxInfo(c, w.day);
            if (c >= 95) return { lvl:3, label:'Badai petir', icon:base.icon };
            if (v >= 60) return { lvl:3, label:'Angin badai', icon:'fa-wind' };
            if (c === 82 || r >= 10) return { lvl:3, label:'Hujan sangat lebat', icon:base.icon };
            if (c === 65 || c === 67 || c === 81 || (c >= 71 && c <= 77) || c === 85 || c === 86 || r >= 4) return { lvl:2, label:base.t === 'Hujan' ? 'Hujan deras' : base.t, icon:base.icon };
            if (v >= 40) return { lvl:2, label:'Angin kencang', icon:'fa-wind' };
            if ((c >= 61 && c <= 64) || c === 66 || c === 80 || r >= 1) return { lvl:1, label:base.t, icon:base.icon };
            if (c === 45 || c === 48) return { lvl:1, label:'Berkabut', icon:base.icon };
            if (v >= 30) return { lvl:1, label:'Berangin', icon:'fa-wind' };
            return { lvl:0, label:base.t, icon:base.icon };
        }
        const p2 = n => String(n).padStart(2, '0');
        const fmt = off => { const d = new Date(Date.now() + off * 1000); return p2(d.getUTCHours()) + ':' + p2(d.getUTCMinutes()) + ':' + p2(d.getUTCSeconds()); };
        const utcLabel = off => { const a = Math.abs(off), h = Math.floor(a / 3600), m = Math.round((a % 3600) / 60); return 'UTC' + (off < 0 ? '-' : '+') + h + (m ? ':' + p2(m) : ''); };

        function ensureWx(points) {
            const seen = new Set(), miss = [];
            points.forEach(p => { const k = key(p[0], p[1]); if (!wx.has(k) && !seen.has(k)) { seen.add(k); miss.push(k); } });
            const list = miss.slice(0, 80);
            if (!list.length) return Promise.resolve(false);
            list.forEach(k => wx.set(k, null));
            const chunks = []; for (let i = 0; i < list.length; i += 40) chunks.push(list.slice(i, i + 40));
            return Promise.all(chunks.map(ch => {
                const la = ch.map(k => k.split(',')[0]).join(','), lo = ch.map(k => k.split(',')[1]).join(',');
                return fetchT('https://api.open-meteo.com/v1/forecast?latitude=' + la + '&longitude=' + lo + '&current=temperature_2m,weather_code,wind_speed_10m,precipitation,is_day&timezone=auto', 12000)
                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(j => (Array.isArray(j) ? j : [j]).forEach((o, i) => {
                        const c = o.current || {};
                        wx.set(ch[i], { temp:c.temperature_2m, code:c.weather_code, wind:c.wind_speed_10m, rain:c.precipitation, day:c.is_day !== 0, off:o.utc_offset_seconds || 0 });
                    }))
                    .catch(() => ch.forEach(k => wx.set(k, { fail:true })));
            })).then(() => true);
        }
        async function waitWx(points) {
            const ks = points.map(p => key(p[0], p[1]));
            for (let i = 0; i < 40; i++) { if (ks.every(k => wx.get(k) !== null && wx.has(k))) return; await new Promise(r => setTimeout(r, 250)); }
        }

        /* lencana kecil di bawah pin */
        const badge = (lat, lng) => {
            if (st.view === 'loc') return '';
            const w = wx.get(key(lat, lng));
            if (st.view === 'wx') {
                if (!w) return '<span class="bd dim"><i class="fa-solid fa-spinner fa-spin"></i> cuaca…</span>';
                if (w.fail) return '<span class="bd dim">cuaca n/a</span>';
                const sv = sev(w);
                return '<span class="bd' + (sv.lvl >= 3 ? ' storm' : sv.lvl >= 2 ? ' bad' : '') + '"><i class="fa-solid ' + sv.icon + '"></i>' + Math.round(w.temp) + '°C <em>' + esc(sv.label) + '</em></span>';
            }
            const ok = w && !w.fail, off = ok ? w.off : Math.round(lng / 15) * 3600;
            return '<span class="bd"><i class="fa-solid ' + (ok ? (w.day ? 'fa-sun' : 'fa-moon') : 'fa-clock') + '"></i><span class="tm" data-off="' + off + '">' + fmt(off) + '</span><em>' + utcLabel(off) + '</em></span>';
        };
        const ctx = (lat, lng) => {
            const w = wx.get(key(lat, lng)); if (!w || w.fail) return '';
            const sv = sev(w);
            return '<span class="ctx"><i class="fa-solid ' + sv.icon + '"></i> ' + esc(sv.label) + ', ' + Math.round(w.temp) + '°C · angin ' + Math.round(w.wind) + ' km/j<br><i class="fa-regular fa-clock"></i> Waktu lokal <span class="tm" data-off="' + w.off + '">' + fmt(w.off) + '</span> (' + utcLabel(w.off) + ')</span>';
        };

        /* radar hujan (RainViewer) */
        let radar = null;
        async function setRadar(on) {
            if (!on) { if (radar && map.hasLayer(radar)) map.removeLayer(radar); return; }
            if (!radar) {
                try {
                    const j = await (await fetchT('https://api.rainviewer.com/public/weather-maps.json', 10000)).json();
                    const f = ((j.radar && j.radar.past) || []).slice(-1)[0]; if (!f) return;
                    radar = L.tileLayer(j.host + f.path + '/256/{z}/{x}/{y}/2/1_1.png', { opacity:.6, maxNativeZoom:7, maxZoom:19, zIndex:5, attribution:'Radar &copy; RainViewer' });
                } catch (_) { return; }
            }
            if (st.view === 'wx' && st.radar) radar.addTo(map);
        }

        /* ===== MARKER ===== */
        const pin = (color, icon, cls, label, bdg) => L.divIcon({ className:'',
            html:'<div class="pw"><div class="pin ' + (cls || '') + '" style="background:' + color + '"><i class="fa-solid ' + icon + '"></i></div>' +
                ((label || bdg) ? '<div class="pl">' + (label ? '<span class="vl">' + esc(label) + '</span>' : '') + (bdg || '') + '</div>' : '') + '</div>',
            iconSize:[34,34], iconAnchor:[17,17], popupAnchor:[0,-18] });
        const dot = L.divIcon({ className:'', html:'<div class="tp"></div>', iconSize:[10,10], iconAnchor:[5,5] });
        const lineColor = s => ({ DELIVERED:'#16a34a', IN_TRANSIT:'#f59e0b', DELAYED:'#e5484d' }[String(s).toUpperCase()] || '#2f8cf5');

        const modeOk = m => st.mode === 'all' || m === st.mode;
        const visFac = () => facs.filter(i => st.layers[i.k] && modeOk(i.mode) && i.text.includes(st.q));
        const visShip = () => ships.filter(i => modeOk(i.mode) && i.text.includes(st.q));

        const info = (i, ll, extra) => {
            const s = i.s, v = i.veh;
            return '<div class="pop"><strong>' + esc(s.number) + '</strong>' +
                '<span class="veh"><i class="fa-solid ' + v.icon + '"></i> ' + esc(v.name) + (i.vnum ? ' · ' + esc(i.vnum) : '') + '</span><br>' +
                'Customer: ' + esc(s.customer) + '<br>Carrier: ' + esc(s.carrier || '-') + '<br>Rute: ' + esc(s.origin) + ' → ' + esc(s.destination) +
                '<br>Status: ' + esc(String(s.status).replace('_',' ')) + (s.eta ? '<br>ETA: ' + esc(s.eta) : '') +
                '<br><a href="/pelacakan/' + encodeURIComponent(s.id) + '">Lihat tracking →</a>' +
                (i.o && i.d ? '<br><a href="#" data-test="' + i.idx + '">Uji hambatan rute ini →</a>' : '') +
                (extra || '') + (ll ? ctx(ll[0], ll[1]) : '') + '</div>';
        };
        map.on('popupopen', e => {
            const el = e.popup.getElement(), a = el && el.querySelector('[data-test]');
            if (a) a.addEventListener('click', ev => { ev.preventDefault(); map.closePopup(); testShip(+a.dataset.test); });
        });

        /* ===== RUTE JALAN (OSRM) untuk mode Lalu lintas ===== */
        const roads = new Map(), rq = []; let rActive = 0, rTimer = null;
        const roadKey = (a, b) => a.map(v => v.toFixed(3)).join(',') + '>' + b.map(v => v.toFixed(3)).join(',');
        async function osrm(coords, steps) {
            const url = 'https://router.project-osrm.org/route/v1/driving/' + coords.map(c => c[1] + ',' + c[0]).join(';') + '?overview=full&geometries=geojson&steps=' + (steps ? 'true' : 'false');
            const j = await (await fetchT(url, 20000)).json();
            if (j.code !== 'Ok' || !j.routes || !j.routes[0]) throw new Error(j.message || j.code || 'Rute tidak ditemukan');
            return j.routes[0];
        }
        function wantRoad(a, b) {
            const k = roadKey(a, b);
            if (!roads.has(k)) { roads.set(k, 'pending'); rq.push({ k, a, b }); pump(); }
            return k;
        }
        function pump() {
            while (rActive < 2 && rq.length) {
                const t = rq.shift(); rActive++;
                osrm([t.a, t.b], false)
                    .then(r => roads.set(t.k, decimate(r.geometry.coordinates.map(c => [c[1], c[0]]), 700)))
                    .catch(() => roads.set(t.k, false))
                    .finally(() => { rActive--; pump(); clearTimeout(rTimer); rTimer = setTimeout(() => { if (baseName === 'traffic') render(false); }, 400); });
            }
        }
        const roadEnds = i => i.exact ? (i.pos && i.d ? [i.pos, i.d] : null) : (i.o && i.d ? [i.o, i.d] : null);

        /* ===== RENDER ===== */
        let fleetRefs = [], renderTok = 0, wxPts = [], wxHit = [];

        function render(fit) {
            const tok = ++renderTok;
            layer.clearLayers();
            const bounds = [], need = [], vf = visFac(), vs = visShip(), traffic = baseName === 'traffic', isWx = st.view === 'wx';
            wxPts = []; const seenW = new Set();
            const reg = (name, type, ll) => { if (!isWx) return; const k = type + '|' + name + '|' + key(ll[0], ll[1]); if (seenW.has(k)) return; seenW.add(k); wxPts.push({ name, type, ll }); };
            const bd = ll => {
                need.push(ll);
                if (isWx) {
                    const w = wx.get(key(ll[0], ll[1])), sv = sev(w);
                    if (sv.lvl >= 2) L.circle(ll, { radius:sv.lvl >= 3 ? 55000 : 35000, color:SEV_COLOR[sv.lvl], weight:1, fillColor:SEV_COLOR[sv.lvl], fillOpacity:.16, interactive:false }).addTo(layer);
                }
                return badge(ll[0], ll[1]);
            };
            let fleet = 0, roadBudget = 25; fleetRefs = [];

            vs.forEach(i => {
                const s = i.s, color = lineColor(s.status);
                let road = null, roadNote = '';
                if (traffic && i.mode === 'land') {
                    const ends = roadEnds(i);
                    if (ends && roadBudget > 0) {
                        roadBudget--;
                        const r = roads.get(wantRoad(ends[0], ends[1]));
                        if (Array.isArray(r)) road = r; else if (r === false) roadNote = '<br><small>Tidak ada jalan darat yang tersambung (beda pulau/negara?).</small>';
                    }
                }
                if (road) {
                    L.polyline(road, { color:'#ffffff', weight:9, opacity:.9, interactive:false }).addTo(layer);
                    L.polyline(road, { color:'#1d4ed8', weight:5, opacity:.95 }).bindPopup(info(i, null, '<br><small>Rute jalan menuju ' + esc(s.destination) + '. Warna jalan di bawah garis = kondisi lalu lintas.</small>')).addTo(layer);
                    if (i.exact && i.line) L.polyline(i.line, { color:'#94a3b8', weight:3, opacity:.8, dashArray:'4,6' }).addTo(layer);
                } else if (i.line) {
                    L.polyline(i.line, { color, weight:i.exact ? 3 : 2, opacity:i.exact ? .85 : .7, dashArray:i.exact ? null : '7,8' }).bindPopup(info(i, null, (i.exact ? '' : '<br><small>Garis penghubung, bukan rute aktual.</small>') + roadNote)).addTo(layer);
                }
                if (i.o) { L.marker(i.o, { icon:pin('#2563eb','fa-box','','',bd(i.o)) }).bindPopup(info(i, i.o)).addTo(layer); bounds.push(i.o); reg(s.origin || 'Asal', 'Asal ' + s.number, i.o); }
                if (i.d) { L.marker(i.d, { icon:pin('#16a34a','fa-flag-checkered','','',bd(i.d)) }).bindPopup(info(i, i.d)).addTo(layer); bounds.push(i.d); reg(s.destination || 'Tujuan', 'Tujuan ' + s.number, i.d); }
                if (i.exact) i.pts.forEach(p => { L.marker([p.lat, p.lng], { icon:dot }).bindPopup('<div class="pop"><strong>' + esc(s.number) + '</strong>Lokasi: ' + esc(p.location || '-') + '<br>Status: ' + esc(String(p.status || s.status).replace('_',' ')) + '<br><small>' + esc(p.time || '') + '</small></div>').addTo(layer); bounds.push([p.lat, p.lng]); });
                if (st.layers.fleet && i.pos) {
                    let pos = i.pos;
                    if (road && !i.exact) pos = road[Math.min(road.length - 1, Math.floor(road.length * i.frac))];
                    const m = L.marker(pos, { icon:pin(i.veh.color, i.veh.icon, 'fleet', i.veh.name, bd(pos)), zIndexOffset:500 })
                        .bindPopup(info(i, pos, i.exact ? '' : '<br><small>Posisi diperkirakan di sepanjang rute.</small>')).addTo(layer);
                    fleetRefs.push({ i, m, pos });
                    bounds.push(pos); fleet++;
                    reg(i.veh.name + ' ' + s.number, 'Armada', pos);
                }
            });

            vf.forEach(i => {
                const f = i.f, m = META[i.k], code = f.facility_code || f.code;
                L.marker([i.lat, i.lng], { icon:pin(m.color, m.icon, 'sq', '', bd([i.lat, i.lng])) }).bindPopup('<div class="pop"><strong>' + esc(f.name || 'Tanpa nama') + '</strong><small>' + m.label.toUpperCase() + (code ? ' · ' + esc(String(code).toUpperCase()) : '') + '</small><br>' + esc([f.city, f.province, f.country].filter(Boolean).join(', ')) + (f.address ? '<br>' + esc(f.address) : '') + ctx(i.lat, i.lng) + '</div>').addTo(layer);
                bounds.push([i.lat, i.lng]);
                reg(f.name || m.label, m.label, [i.lat, i.lng]);
            });

            /* hasil pindaian area cuaca */
            if (isWx) st.scan.forEach(p => {
                need.push(p.ll);
                const w = wx.get(key(p.ll[0], p.ll[1])); if (!w || w.fail) return;
                const sv = sev(w), col = SEV_COLOR[Math.max(0, sv.lvl)];
                if (sv.lvl >= 2) L.circle(p.ll, { radius:p.r, color:col, weight:1, fillColor:col, fillOpacity:.2, interactive:false }).addTo(layer);
                L.marker(p.ll, { icon:L.divIcon({ className:'', html:'<div class="wxm" style="border-color:' + col + '"><i class="fa-solid ' + sv.icon + '" style="color:' + col + '"></i>' + Math.round(w.temp) + '°</div>', iconSize:[50,22], iconAnchor:[25,11] }) })
                    .bindPopup('<div class="pop"><strong>' + esc(p.name) + '</strong>' + esc(sv.label) + ', ' + Math.round(w.temp) + '°C<br>Angin ' + Math.round(w.wind) + ' km/j' + (w.rain ? ' · hujan ' + w.rain + ' mm' : '') + '</div>').addTo(layer);
                reg(p.name, 'Area pindaian', p.ll);
            });

            $('kShip').textContent = vs.length; $('kFleet').textContent = fleet; $('kFac').textContent = vf.length;
            $('mapEmpty').classList.toggle('show', !bounds.length && !st.q && st.mode === 'all');
            $('fleetCount').textContent = fleet;
            $('fleetList').innerHTML = fleetRefs.length
                ? fleetRefs.slice(0, 40).map((r, n) => '<button type="button" class="fl" data-i="' + n + '"><span class="ic" style="background:' + r.i.veh.color + '"><i class="fa-solid ' + r.i.veh.icon + '"></i></span><span><strong>' + esc(r.i.veh.name) + ' · ' + esc(r.i.s.number) + '</strong><small>' + esc(r.i.s.origin) + ' → ' + esc(r.i.s.destination) + '</small></span></button>').join('')
                : '<div class="fl-empty">Tidak ada armada bergerak.</div>';
            if (fit && bounds.length) map.fitBounds(bounds, { padding:[60,60], maxZoom:7 });

            if (isWx) renderWxPanel();
            if (st.view !== 'loc') ensureWx(need).then(changed => { if (changed && tok === renderTok && st.view !== 'loc') render(false); });
        }

        /* ===== PANEL CUACA ===== */
        function renderWxPanel() {
            const items = wxPts.map(p => { const w = wx.get(key(p.ll[0], p.ll[1])); return { p, w, s:sev(w) }; });
            const loaded = items.filter(x => x.w && !x.w.fail);
            const hit = loaded.filter(x => x.s.lvl >= 1).sort((a, b) => b.s.lvl - a.s.lvl);
            const severe = hit.filter(x => x.s.lvl >= 2).length, storm = hit.filter(x => x.s.lvl >= 3).length;
            wxHit = hit.slice(0, 40);
            $('wxSum').textContent = loaded.length + '/' + items.length + ' titik dimuat';
            const head = $('wxHead');
            if (!items.length) { head.className = 'wxhead warn'; head.innerHTML = 'Tidak ada titik untuk dicek. Coba “Pindai area peta”.'; }
            else if (loaded.length < items.length && !hit.length) { head.className = 'wxhead warn'; head.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memuat cuaca…'; }
            else if (storm) { head.className = 'wxhead bad'; head.innerHTML = '<b>' + storm + '</b> lokasi terkena badai / cuaca ekstrem' + (severe > storm ? ' · +' + (severe - storm) + ' hujan lebat/angin kencang' : ''); }
            else if (severe) { head.className = 'wxhead bad'; head.innerHTML = '<b>' + severe + '</b> lokasi hujan lebat / angin kencang'; }
            else if (hit.length) { head.className = 'wxhead warn'; head.innerHTML = '<b>' + hit.length + '</b> lokasi hujan ringan / berkabut / berangin'; }
            else { head.className = 'wxhead ok'; head.innerHTML = '<i class="fa-solid fa-circle-check"></i> Tidak ada badai atau cuaca buruk di titik pantauan'; }
            $('wxList').innerHTML = wxHit.map((x, n) => '<button type="button" class="wxi" data-i="' + n + '"><span class="dl" style="background:' + SEV_COLOR[x.s.lvl] + '"></span><span><strong>' + esc(x.p.name) + '</strong><small>' + esc(x.p.type) + ' · ' + esc(x.s.label) + ' · ' + Math.round(x.w.temp) + '°C · angin ' + Math.round(x.w.wind) + ' km/j</small></span></button>').join('');
            $('wxClear').style.display = st.scan.length ? '' : 'none';
        }
        $('wxList').addEventListener('click', e => { const b = e.target.closest('.wxi'); if (!b) return; const x = wxHit[+b.dataset.i]; if (x) map.flyTo(x.p.ll, Math.max(map.getZoom(), 8), { duration:1 }); });

        $('wxScan').addEventListener('click', () => {
            const b = map.getBounds().pad(-.05), cols = 7, rows = 5, la0 = b.getSouth(), la1 = b.getNorth(), lo0 = b.getWest(), lo1 = b.getEast();
            const dLa = (la1 - la0) / rows, dLo = (lo1 - lo0) / cols, pts = [];
            const r = Math.max(15000, Math.min(hav([la0, lo0], [la0 + dLa, lo0 + dLo]) * .55, 400000));
            for (let y = 0; y < rows; y++) for (let x = 0; x < cols; x++) {
                const ll = [la0 + dLa * (y + .5), lo0 + dLo * (x + .5)], c = nearestCity(ll);
                pts.push({ ll, r, name: c ? 'Sekitar ' + c : 'Area ' + ll[0].toFixed(1) + ', ' + ll[1].toFixed(1) });
            }
            st.scan = pts; render(false);
        });
        $('wxClear').addEventListener('click', () => { st.scan = []; render(false); });

        /* daftar armada */
        $('fleetList').addEventListener('click', e => {
            const b = e.target.closest('.fl'); if (!b) return;
            const r = fleetRefs[+b.dataset.i]; if (!r) return;
            map.flyTo(r.pos, Math.max(map.getZoom(), 13), { duration:1 });
            r.m.openPopup();
        });
        $('fleetToggle').addEventListener('click', () => {
            const box = $('fleetBox'), shut = box.classList.toggle('shut');
            $('fleetToggle').setAttribute('aria-expanded', String(!shut));
        });

        /* jam berjalan */
        const zone = (Intl.DateTimeFormat().resolvedOptions().timeZone || 'Waktu Anda').replace('_', ' ');
        $('ckZone').textContent = 'Waktu Anda · ' + zone;
        setInterval(() => {
            if (st.view === 'loc' || document.hidden) return;
            document.querySelectorAll('#mainMap .tm').forEach(e => { e.textContent = fmt(+e.dataset.off); });
            if (st.view === 'time') { $('ckLocal').textContent = new Date().toLocaleTimeString('id-ID', { hour12:false }).replace(/\./g, ':'); $('ckUtc').textContent = fmt(0); }
        }, 1000);

        /* ===== UJI RUTE & HAMBATAN ===== */
        let wps = [{ text:'', ll:null }, { text:'', ll:null }];
        const LET = i => String.fromCharCode(65 + i);
        const fmtDur = s => { const h = Math.floor(s / 3600), m = Math.round((s % 3600) / 60); return h ? h + ' jam ' + m + ' mnt' : m + ' mnt'; };
        const fmtKm = m => (m / 1000 >= 100 ? Math.round(m / 1000) : (m / 1000).toFixed(1)) + ' km';

        $('placeList').innerHTML = Object.keys(cities).map(c => '<option value="' + esc(cap(c)) + '">').join('') + facs.map(f => '<option value="' + esc(f.f.name || '') + '">').join('');

        function drawWps() {
            $('wpList').innerHTML = wps.map((w, i) => '<div class="wp"><span class="n">' + LET(i) + '</span><input type="text" list="placeList" data-i="' + i + '" value="' + esc(w.text) + '" placeholder="' + (i === 0 ? 'Asal, mis. Medan' : i === wps.length - 1 ? 'Tujuan, mis. Jakarta' : 'Titik singgah') + '" aria-label="Titik ' + LET(i) + '">' +
                (wps.length > 2 ? '<button type="button" data-del="' + i + '" aria-label="Hapus titik"><i class="fa-solid fa-xmark"></i></button>' : '') + '</div>').join('');
        }
        $('wpList').addEventListener('input', e => { const i = e.target.dataset.i; if (i !== undefined) { wps[+i].text = e.target.value; wps[+i].ll = null; } });
        $('wpList').addEventListener('keydown', e => { if (e.key === 'Enter') runRoute(); });
        $('wpList').addEventListener('click', e => { const b = e.target.closest('[data-del]'); if (b) { wps.splice(+b.dataset.del, 1); drawWps(); } });
        $('wpAdd').addEventListener('click', () => { if (wps.length < 8) { wps.splice(wps.length - 1, 0, { text:'', ll:null }); drawWps(); } });
        $('wpReset').addEventListener('click', () => { wps = [{ text:'', ll:null }, { text:'', ll:null }]; drawWps(); toolLayer.clearLayers(); $('wpOut').innerHTML = ''; setPick(false); });
        function setPick(on) { st.pick = on; $('wpPick').classList.toggle('on', on); $('mainMap').classList.toggle('pick', on); }
        $('wpPick').addEventListener('click', () => setPick(!st.pick));
        map.on('click', e => {
            if (!st.pick) return;
            const ll = [+e.latlng.lat.toFixed(5), +e.latlng.lng.toFixed(5)], txt = ll.join(', ');
            const empty = wps.findIndex(w => !w.text.trim());
            if (empty >= 0) wps[empty] = { text:txt, ll };
            else if (wps.length < 8) wps.push({ text:txt, ll }); else return;
            drawWps();
        });
        function openTool(on) {
            $('routeTool').style.display = on ? '' : 'none';
            $('toolBtn').classList.toggle('on', on);
            if (on) { drawWps(); $('peta').scrollIntoView({ behavior:'smooth', block:'start' }); } else { setPick(false); toolLayer.clearLayers(); }
        }
        $('toolBtn').addEventListener('click', () => openTool($('routeTool').style.display === 'none'));
        $('toolClose').addEventListener('click', () => openTool(false));

        async function resolve(w) {
            if (w.ll) return w.ll;
            const t = w.text.trim(); if (!t) return null;
            const m = t.match(/^(-?\d+(?:\.\d+)?)\s*[, ]\s*(-?\d+(?:\.\d+)?)$/);
            if (m) return [+m[1], +m[2]];
            const c = cityCoord(t); if (c) return c;
            const f = facs.find(x => x.text.includes(t.toLowerCase())); if (f) return [f.lat, f.lng];
            try {
                const j = await (await fetchT('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' + encodeURIComponent(t), 10000)).json();
                if (j[0]) return [+j[0].lat, +j[0].lon];
            } catch (_) {}
            return null;
        }

        async function routeTomTom(coords) {
            const url = 'https://api.tomtom.com/routing/1/calculateRoute/' + coords.map(c => c[0] + ',' + c[1]).join(':') + '/json?traffic=true&travelMode=car&sectionType=traffic&sectionType=ferry&key=' + encodeURIComponent(TOMTOM_KEY);
            const j = await (await fetchT(url, 20000)).json(), r = j.routes && j.routes[0];
            if (!r) throw new Error('TomTom: rute tidak ditemukan');
            const pts = []; r.legs.forEach(l => l.points.forEach(p => pts.push([p.latitude, p.longitude])));
            const jams = [], ferries = [];
            (r.sections || []).forEach(s => {
                const seg = pts.slice(s.startPointIndex, s.endPointIndex + 1);
                if (s.sectionType === 'TRAFFIC') jams.push({ seg, cat:s.simpleCategory, delay:s.delayInSeconds || 0, speed:s.effectiveSpeedInKmh, mid:seg[Math.floor(seg.length / 2)] });
                else if (s.sectionType === 'FERRY') ferries.push({ name:'Penyeberangan feri', ll:seg[0] });
            });
            return { coords:pts, dist:r.summary.lengthInMeters, dur:r.summary.travelTimeInSeconds, delay:r.summary.trafficDelayInSeconds || 0, jams, ferries, live:true };
        }
        async function routeOsrm(coords) {
            const r = await osrm(coords, true), ferries = [];
            r.legs.forEach(l => (l.steps || []).forEach(s => { if (s.mode === 'ferry' && s.maneuver) ferries.push({ name:s.name || 'Penyeberangan feri', ll:[s.maneuver.location[1], s.maneuver.location[0]] }); }));
            return { coords:r.geometry.coordinates.map(c => [c[1], c[0]]), dist:r.distance, dur:r.duration, delay:null, jams:[], ferries, live:false };
        }
        function sampleRoute(coords, total) {
            const n = Math.min(12, Math.max(3, Math.round(total / 60000))), cum = [0];
            for (let i = 1; i < coords.length; i++) cum.push(cum[i - 1] + hav(coords[i - 1], coords[i]));
            const out = []; let j = 0;
            for (let k = 0; k < n; k++) {
                const target = (cum[cum.length - 1] || 0) * k / (n - 1);
                while (j < cum.length - 1 && cum[j] < target) j++;
                out.push({ ll:coords[j], km:Math.round(cum[j] / 1000) });
            }
            return out;
        }

        let running = false;
        async function runRoute() {
            if (running) return; running = true;
            const out = $('wpOut'), btn = $('wpRun');
            toolLayer.clearLayers(); setPick(false);
            out.innerHTML = '<div class="res warn"><i class="fa-solid fa-spinner fa-spin"></i> Mencari titik &amp; menghitung rute…</div>'; btn.disabled = true;
            try {
                const filled = wps.filter(w => w.text.trim() || w.ll);
                if (filled.length < 2) throw new Error('Isi minimal titik asal dan tujuan.');
                const coords = [];
                for (const w of filled) { const ll = await resolve(w); if (!ll) throw new Error('Lokasi “' + w.text + '” tidak ditemukan. Coba nama kota lain atau koordinat.'); w.ll = ll; coords.push(ll); }

                let rt = null, note = '';
                if (TOMTOM_KEY) { try { rt = await routeTomTom(coords); } catch (e) { note = 'TomTom gagal (' + e.message + '), memakai OSRM. '; } }
                if (!rt) {
                    try { rt = await routeOsrm(coords); }
                    catch (e) { throw new Error('Tidak ada rute jalan darat di antara titik-titik ini (mungkin beda pulau/negara tanpa jalan atau feri). ' + (e.message || '')); }
                }

                const samples = sampleRoute(rt.coords, rt.dist), sp = samples.map(s => s.ll);
                out.innerHTML = '<div class="res warn"><i class="fa-solid fa-spinner fa-spin"></i> Mengecek cuaca sepanjang rute…</div>';
                await ensureWx(sp); await waitWx(sp);

                const obs = [];
                samples.forEach(s => {
                    const w = wx.get(key(s.ll[0], s.ll[1])), sv = sev(w);
                    if (sv.lvl >= 1) obs.push({ lvl:sv.lvl, ll:s.ll, icon:sv.icon, txt:'km ~' + s.km + ' · ' + (nearestCity(s.ll) || 'di jalur') + ': ' + sv.label + ', ' + Math.round(w.temp) + '°C, angin ' + Math.round(w.wind) + ' km/j' });
                });
                rt.ferries.forEach(f => obs.push({ lvl:2, ll:f.ll, icon:'fa-ferry', txt:'Ada penyeberangan feri di rute ini (jadwal & antrean bisa menunda).' }));
                rt.jams.forEach(j => { if (j.delay >= 120) obs.push({ lvl:j.delay >= 600 ? 3 : 2, ll:j.mid || j.seg[0], icon:'fa-car-burst', txt:({ JAM:'Macet', ROAD_CLOSED:'Jalan ditutup', ROAD_WORK:'Perbaikan jalan', ACCIDENT:'Kecelakaan' }[j.cat] || 'Gangguan lalu lintas') + ': +' + Math.round(j.delay / 60) + ' mnt' + (j.speed ? ' (kecepatan ' + Math.round(j.speed) + ' km/j)' : '') }); });
                if (rt.dur > 12 * 3600) obs.push({ lvl:1, ll:null, icon:'fa-bed', txt:'Perjalanan > 12 jam: butuh pergantian pengemudi / istirahat.' });
                obs.sort((a, b) => b.lvl - a.lvl);

                const worst = obs.reduce((a, x) => Math.max(a, x.lvl), 0), col = worst >= 2 ? '#e5484d' : worst === 1 ? '#f59e0b' : '#16a34a';
                L.polyline(rt.coords, { color:'#fff', weight:9, opacity:.9, interactive:false }).addTo(toolLayer);
                L.polyline(decimate(rt.coords, 900), { color:col, weight:5, opacity:.95 }).addTo(toolLayer);
                rt.jams.forEach(j => L.polyline(j.seg, { color:'#7f1d1d', weight:6, opacity:.9, interactive:false }).addTo(toolLayer));
                wps.filter(w => w.ll).forEach((w, i) => L.marker(w.ll, { icon:L.divIcon({ className:'', html:'<div class="wpm">' + LET(i) + '</div>', iconSize:[26,26], iconAnchor:[13,13] }), zIndexOffset:900 }).bindPopup('<div class="pop"><strong>Titik ' + LET(i) + '</strong>' + esc(w.text) + '</div>').addTo(toolLayer));
                obs.forEach(o => { if (!o.ll) return; const c = SEV_COLOR[Math.min(3, o.lvl)]; L.marker(o.ll, { icon:L.divIcon({ className:'', html:'<div class="obm" style="background:' + c + '"><i class="fa-solid ' + o.icon + '"></i></div>', iconSize:[26,26], iconAnchor:[13,13] }), zIndexOffset:800 }).bindPopup('<div class="pop">' + esc(o.txt) + '</div>').addTo(toolLayer); });
                map.fitBounds(L.latLngBounds(rt.coords), { padding:[60, 340], maxZoom:11 });

                const cls = worst >= 2 ? 'bad' : worst === 1 ? 'warn' : 'ok', title = worst >= 2 ? 'Ada hambatan di rute' : worst === 1 ? 'Waspada, ada potensi hambatan' : 'Rute lancar, tidak ada hambatan terdeteksi';
                out.innerHTML = (note ? '<p class="hint">' + esc(note) + '</p>' : '') +
                    '<div class="res ' + cls + '"><b>' + title + '</b>' + (obs.length ? obs.length + ' catatan di bawah.' : 'Cuaca sepanjang rute aman' + (rt.live ? ' dan lalu lintas normal.' : '.')) + '</div>' +
                    '<div class="kv"><div>Jarak<b>' + fmtKm(rt.dist) + '</b></div><div>Waktu tempuh' + (rt.live ? ' (+macet)' : '') + '<b>' + fmtDur(rt.dur) + '</b></div></div>' +
                    (obs.length ? '<ul class="obs">' + obs.map(o => '<li><i class="fa-solid ' + o.icon + '" style="color:' + SEV_COLOR[Math.min(3, o.lvl)] + '"></i><span>' + esc(o.txt) + '</span></li>').join('') + '</ul>' : '') +
                    (rt.live ? '' : '<p class="hint" style="margin-top:8px">Data macet nyata tidak tersedia tanpa API key. Lihat warna jalan lewat <button class="chip" id="goTraffic" type="button"><i class="fa-solid fa-traffic-light"></i> Lalu lintas</button> (hijau lancar, merah macet).</p>');
                const gt = $('goTraffic'); if (gt) gt.addEventListener('click', () => setBase('traffic'));
            } catch (e) {
                out.innerHTML = '<div class="res bad"><b>Tidak bisa mengecek rute</b>' + esc(e.message || String(e)) + '</div>';
            } finally { btn.disabled = false; running = false; }
        }
        $('wpRun').addEventListener('click', runRoute);

        /* uji rute dari popup shipment */
        function testShip(idx) {
            const i = ships[idx]; if (!i || !i.o || !i.d) return;
            wps = [{ text:i.s.origin || 'Asal', ll:i.o }, { text:i.s.destination || 'Tujuan', ll:i.d }];
            openTool(true); runRoute();
        }

        /* hitungan & kontrol toolbar */
        function single(groupId, attr, fn) {
            const g = $(groupId);
            g.querySelectorAll('.chip').forEach(b => b.addEventListener('click', () => {
                g.querySelectorAll('.chip').forEach(x => x.classList.remove('on')); b.classList.add('on'); fn(b.dataset[attr]);
            }));
        }
        single('viewGroup', 'view', v => {
            st.view = v;
            $('radarGroup').style.display = v === 'wx' ? 'flex' : 'none';
            $('wxPanel').style.display = v === 'wx' ? '' : 'none';
            $('clockBox').classList.toggle('show', v === 'time');
            if (v !== 'wx') st.scan = [];
            if (v === 'time') { $('ckLocal').textContent = new Date().toLocaleTimeString('id-ID', { hour12:false }).replace(/\./g, ':'); $('ckUtc').textContent = fmt(0); }
            setRadar(v === 'wx' && st.radar);
            render(false);
        });
        $('radarBtn').addEventListener('click', () => {
            st.radar = !st.radar; $('radarBtn').classList.toggle('on', st.radar); setRadar(st.view === 'wx' && st.radar);
        });
        single('modeGroup', 'mode', v => { st.mode = v; render(true); });
        single('regionGroup', 'region', v => { if (v === 'all') render(true); else map.flyToBounds(REGIONS[v], { duration:1.1 }); });
        document.querySelectorAll('#layerGroup .chip').forEach(b => b.addEventListener('click', () => {
            const k = b.dataset.layer; st.layers[k] = !st.layers[k]; b.classList.toggle('on', st.layers[k]); render(false);
        }));
        let qT = null;
        $('mapQ').addEventListener('input', e => { const v = e.target.value.toLowerCase().trim(); clearTimeout(qT); qT = setTimeout(() => { st.q = v; render(true); }, 250); });

        drawWps();
        render(true);
    }
})();
</script>
</body>
</html>
