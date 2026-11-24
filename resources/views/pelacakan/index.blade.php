<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pelacakan | CargoTrace</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="preconnect" href="https://tile.openstreetmap.org" crossorigin>
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
.main{margin-left:272px;min-height:100vh;background:radial-gradient(circle at 100% 0,#cfe3ff 0,#0000 380px),radial-gradient(circle at 0 100%,#d7e8ff 0,#0000 340px),var(--bg)}
.content{padding:26px 32px 44px}

/* HERO + PENCARIAN */
.hero{position:relative;overflow:hidden;padding:34px 38px;margin-bottom:22px;border-radius:28px;color:#fff;
 background:linear-gradient(100deg,#061533 0%,#0b2a6b 60%,#1d4ed8 100%);box-shadow:0 24px 50px #0b2a6b44}
.hero::after{content:"";position:absolute;width:380px;height:380px;right:-110px;top:-190px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.crumb{font-size:11px;color:#a9c8f5;margin-bottom:10px}
.hero h1{font:800 29px/1.25 var(--h);letter-spacing:-.8px}
.hero p{margin-top:10px;max-width:600px;line-height:1.75;color:#cfe0ff}
.hero-row{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap}
.hstats{display:flex;gap:10px;flex-wrap:wrap}
.hs{min-width:112px;padding:12px 16px;border-radius:16px;background:#ffffff1c;border:1px solid #ffffff35;;font-size:11px;color:#dbe9ff}
.hs b{display:block;font:800 21px var(--h);color:#fff}
.stabs{display:flex;gap:6px;margin-top:22px}
.stab{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border:1px solid #ffffff44;border-radius:12px;background:#ffffff1c;color:#fff;font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.stab.on{background:#fff;color:var(--blue);border-color:#fff}
.sform{display:flex;gap:10px;margin-top:10px;padding:10px;border-radius:18px;background:#ffffff;box-shadow:0 14px 34px #061533444;max-width:780px}
.sform.hide{display:none}
.sform .in{position:relative;flex:1;min-width:0}
.sform .in i{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#8ca6c4}
.sform input{width:100%;padding:14px 16px 14px 44px;border:0;border-radius:12px;background:#f4f8ff;color:var(--ink);font:600 13.5px 'Plus Jakarta Sans',sans-serif}
.sform input:focus{outline:2px solid #2563eb55}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 20px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:12px;cursor:pointer;font:700 12px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;transition:.2s;box-shadow:0 8px 20px #2563eb44}
.btn:hover{filter:brightness(1.1)}
.btn-light{background:#e8f1ff;color:#1d4ed8;box-shadow:none}

/* PICKER */
.pick{display:flex;gap:10px;overflow-x:auto;padding:4px 2px 14px;margin-bottom:10px;scrollbar-width:thin}
.pk{flex:none;min-width:210px;padding:14px 16px;background:#fff;border:1px solid var(--line);border-radius:18px;text-decoration:none;transition:.2s;box-shadow:0 8px 22px #2563eb0d}
.pk:hover{transform:translateY(-3px);border-color:#9fc2f7}
.pk.on{border-color:var(--blue);box-shadow:0 12px 28px #2563eb30}
.pk b{display:block;color:var(--blue);font-size:12.5px}
.pk strong{display:block;margin-top:5px;font-size:12px;color:var(--navy);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.pk small{display:block;margin:5px 0 8px;font-size:10.5px;color:var(--mut);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

/* PANEL & KOMPONEN */
.panel{padding:24px;background:#fff;border:1px solid var(--line);border-radius:26px;box-shadow:0 10px 30px #2563eb0d;min-width:0}
.panel.flush{padding:0;overflow:visible}
.ph{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px}
.ph h2{font:700 16.5px var(--h);color:var(--navy);letter-spacing:-.3px;display:flex;align-items:center;gap:10px}
.ph h2 .hi{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font-size:14px;flex:none}
.ph p{margin-top:6px;font-size:12px;color:var(--mut)}
.sect{margin-bottom:22px}
.g3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-bottom:22px}
.gmap{display:grid;grid-template-columns:minmax(0,1.7fr) minmax(320px,1fr);gap:20px;margin-bottom:22px;align-items:start}
.sidecol{display:flex;flex-direction:column;gap:20px;min-width:0}
.status{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.st-CREATED{background:#e8eef7;color:#5b6f90}.st-BOOKED{background:#e0edff;color:#1d4ed8}.st-PICKED_UP{background:#f0eaff;color:#7c3aed}.st-IN_TRANSIT{background:#fff4dc;color:#b77900}.st-DELIVERED{background:#e8f7ef;color:#26734d}.st-DELAYED{background:#ffe9e9;color:#c92f3a}
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
.rt .ln i.f{display:block;height:100%;border-radius:6px;background:linear-gradient(90deg,#38bdf8,#1d4ed8)}
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
.mini span.warnb{background:#fff4dc;color:#b77900}

/* TOOLBAR PETA */
.tools{position:relative;z-index:20;display:flex;flex-wrap:wrap;align-items:center;gap:10px 20px;padding:16px 22px;border-bottom:1px solid #edf3fa;background:#fff;border-radius:26px 26px 0 0}
.tg{display:flex;align-items:center;flex-wrap:wrap;gap:6px}
.tgl{font-size:10px;font-weight:700;letter-spacing:.9px;color:#8ca6c4;margin-right:3px}
.chip{display:inline-flex;align-items:center;gap:7px;padding:8px 13px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:#3d68a8;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.15s}
.chip:hover{background:#eef6ff}
.chip.on{background:var(--blue);border-color:var(--blue);color:#fff;box-shadow:0 6px 16px #2563eb44}
.msearch{position:relative;margin-left:auto;flex:1;min-width:230px;max-width:380px}
.msearch>i{position:absolute;top:12px;left:13px;font-size:12px;color:#8ca6c4}
.msearch input{width:100%;padding:10px 14px 10px 36px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.msearch input:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.sres{display:none;position:absolute;left:0;right:0;top:44px;max-height:300px;overflow-y:auto;background:#fff;border:1px solid var(--line);border-radius:14px;box-shadow:0 16px 40px #1e3a8a2b}
.sres.open{display:block}
.sres button{display:flex;gap:10px;align-items:flex-start;width:100%;padding:10px 14px;border:0;border-bottom:1px solid #f1f6fc;background:transparent;text-align:left;cursor:pointer;font:12px/1.5 'Plus Jakarta Sans',sans-serif;color:#365775}
.sres button:hover{background:#f3f8ff}
.sres button i{margin-top:3px;color:var(--blue)}
.sres button b{display:block;color:var(--navy);font-size:12px}
.sres .none{padding:14px;color:#8ca6c4;text-align:center}
.tmsg{font-size:11px;color:#c92f3a;font-weight:600}

/* PETA */
.mapw{position:relative;isolation:isolate;border-radius:0 0 26px 26px;overflow:hidden}
#mapMain{height:640px;width:100%;background:#dfeafb}
.basebox{position:absolute;top:12px;right:12px;z-index:700;display:flex;gap:4px;padding:5px;background:#ffffffee;border:1px solid var(--line);border-radius:14px;box-shadow:0 8px 22px #0b2a6b2a;}
.bb{display:flex;flex-direction:column;align-items:center;gap:3px;min-width:62px;padding:7px 8px;border:0;border-radius:10px;background:transparent;color:#3d68a8;font:600 10px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.15s}
.bb i{font-size:15px}
.bb:hover{background:#eef6ff}
.bb.on{background:var(--blue);color:#fff;box-shadow:0 6px 14px #2563eb55}
.xhair{display:none;position:absolute;left:50%;top:50%;width:30px;height:30px;margin:-15px 0 0 -15px;z-index:650;pointer-events:none}
.xhair.show{display:block}
.xhair::before,.xhair::after{content:"";position:absolute;background:#e5484d;box-shadow:0 0 0 1px #fff}
.xhair::before{left:14px;top:0;width:2px;height:30px}.xhair::after{top:14px;left:0;height:2px;width:30px}
.ctr{display:none;position:absolute;left:12px;top:12px;z-index:700;width:290px;padding:14px 16px;background:#ffffffef;border:1px solid var(--line);border-radius:16px;box-shadow:0 10px 26px #0b2a6b2a;}
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
.map-info i.ln{display:inline-block;width:18px;border-top:3px solid #f59e0b}.map-info i.ln.ds{border-top:3px dashed #2f8cf5}
.pw{position:relative;width:34px;height:34px;display:grid;place-items:center}
.pin{display:grid;place-items:center;width:34px;height:34px;border-radius:50%;border:3px solid #fff;color:#fff;font-size:14px;box-shadow:0 4px 14px #0005}
.pin.big{width:42px;height:42px;font-size:17px}
.pin.fleet{box-shadow:0 0 0 6px #f59e0b55}
@keyframes pulse{0%{box-shadow:0 0 0 0 #f59e0bb0}70%{box-shadow:0 0 0 14px #f59e0b00}100%{box-shadow:0 0 0 0 #f59e0b00}}
.pl{position:absolute;top:40px;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:3px;pointer-events:none}
.vl{padding:2px 8px;border-radius:8px;background:#061533e6;color:#fff;font:700 9.5px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;box-shadow:0 3px 8px #0004}
.bd{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:9px;background:#fff;border:1px solid #cfe0f7;color:#1f4a8a;font:700 10px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;box-shadow:0 3px 10px #0b2a6b33}
.bd i{font-size:11px}
.bd em{font-style:normal;font-weight:600;color:#8ca6c4;font-size:9px}
.bd.bad{background:#fff1f1;border-color:#f5b5b5;color:#c92f3a}
.bd.dim{color:#8ca6c4}
.tp{width:10px;height:10px;border-radius:50%;background:#fff;border:3px solid var(--blue)}
.leaflet-popup-content-wrapper{border-radius:14px}
.pop{min-width:170px;font:12px/1.7 'Plus Jakarta Sans',sans-serif;color:#365775}.pop strong{display:block;font-size:13px;color:var(--navy)}.pop small{color:#8ca6c4}
.pop .veh{display:inline-flex;align-items:center;gap:6px;margin:2px 0;padding:2px 9px;border-radius:12px;background:#eef4ff;color:#1d4ed8;font-weight:700}
.pop .xtag{display:inline-block;margin:2px 0;padding:1px 8px;border-radius:10px;background:#ffe9e9;color:#c92f3a;font-weight:700;font-size:10.5px}

/* TIMELINE */
.tl-tools{display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin-bottom:10px}
.tl-tools .chip{padding:6px 11px;font-size:10.5px}
.tl-tools .sp{flex:1}
.tl-sum{margin-bottom:14px;font-size:11px;color:var(--mut);line-height:1.6}
.tl-sum b{color:var(--navy)}
.tl-wrap{max-height:420px;overflow-y:auto;padding-right:6px}
.tl{position:relative;padding-left:28px}
.tl::before{content:"";position:absolute;left:8px;top:6px;bottom:6px;width:2px;background:#dbe6f8}
.ti{position:relative;padding-bottom:20px}.ti:last-child{padding-bottom:0}
.ti::before{content:"";position:absolute;left:-26px;top:3px;width:12px;height:12px;border-radius:50%;background:#fff;border:3px solid #b9d2f7}
.ti.cur::before{border-color:var(--blue);box-shadow:0 0 0 5px #2563eb22}
.ti.exc::before{border-color:#e5484d;background:#ffe9e9}
.ti.exc.cur::before{box-shadow:0 0 0 5px #e5484d33}
.ti.inv{opacity:.62}
.ti b{display:block;font-size:12.5px;color:var(--navy)}
.ti.exc b{color:#c92f3a}
.ti span.loc{display:block;margin-top:3px;font-size:11.5px;color:#41608a}
.ti small{display:block;margin-top:3px;font-size:10.5px;color:#8ca6c4;line-height:1.5}
.ti .tags{display:flex;gap:5px;flex-wrap:wrap;margin-top:6px}
.tag{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:12px;font-size:9.5px;font-weight:700}
.tag.n{background:#e8f7ef;color:#26734d}.tag.x{background:#ffe9e9;color:#c92f3a}.tag.c{background:#e0edff;color:#1d4ed8}.tag.i{background:#fff4dc;color:#b77900}
.nodata{padding:30px 12px;text-align:center;color:#8ca6c4;line-height:1.7}
.nodata i{display:block;margin-bottom:10px;font-size:30px;color:#9db6dc}

/* CUACA + WAKTU */
.wg{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.wc{padding:20px;border-radius:22px;border:1px solid var(--line);background:linear-gradient(165deg,#fff,#f1f7ff)}
.wc.dark{background:linear-gradient(160deg,#061533,#0b2a6b 65%,#1747b8);color:#fff;border:0}
.wt{display:flex;align-items:center;gap:10px;font-size:10.5px;font-weight:700;letter-spacing:.8px;color:#8ca6c4;margin-bottom:6px}
.wc.dark .wt{color:#9fbcea}
.wt .wi{display:grid;place-items:center;width:26px;height:26px;border-radius:8px;color:#fff;font-size:11px}
.wn{font:700 15px var(--h);color:var(--navy);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.wc.dark .wn{color:#fff}
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
.cdiff{margin-top:12px;font-size:11.5px;color:#9fbcea;line-height:1.6}
.wph{padding:30px 10px;text-align:center;color:#8ca6c4;font-size:12px;line-height:1.7}
.wc.dark .wph{color:#9fbcea}
.skl{height:120px;border-radius:16px;background:linear-gradient(90deg,#edf3fc,#f8fbff,#edf3fc);background-size:200% 100%;animation:sk 1.3s infinite}
@keyframes sk{to{background-position:-200% 0}}
.lastloc{display:flex;gap:12px;align-items:center;padding:14px;border-radius:16px;background:linear-gradient(135deg,#eaf3ff,#dbeaff);margin-bottom:14px}
.lastloc>i{display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:var(--blue);color:#fff;flex:none}
.lastloc b{display:block;font-size:13px;color:var(--navy);line-height:1.4}.lastloc small{font-size:11px;color:var(--mut);line-height:1.5;display:block;margin-top:2px}
.pnl-stack{display:flex;flex-direction:column;gap:14px}

/* SIDEBAR */
.ct-sidebar{position:fixed;inset:0 auto 0 0;width:272px;padding:26px 18px 18px;display:flex;flex-direction:column;background:linear-gradient(180deg,#061533 0%,#0a2257 65%,#0f3a8f 100%);color:#fff;z-index:1000;overflow-y:auto}
.ct-sidebar::before{content:"";position:absolute;width:300px;height:300px;top:-140px;right:-150px;border-radius:50%;background:radial-gradient(#38bdf860,#0000 70%);pointer-events:none}
.ct-brand{display:flex;align-items:center;gap:11px;padding:0 8px;position:relative;text-decoration:none;color:#fff}
.ct-logo{display:grid;place-items:center;width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-size:20px;box-shadow:0 8px 22px #2563eb77}
.ct-name{font:800 22px var(--h);letter-spacing:-.5px}
.ct-sub{margin:8px 8px 26px;font-size:9.5px;letter-spacing:.8px;color:#9fbcea;position:relative}
.ct-title{margin:0 12px 10px;font-size:10px;font-weight:700;letter-spacing:1.3px;color:#6f95d6;position:relative}
.ct-menu{display:flex;flex-direction:column;gap:4px;position:relative;margin-bottom:22px}
.ct-link{display:flex;align-items:center;gap:13px;min-height:44px;padding:10px 14px;border-radius:13px;color:#dce8ff;text-decoration:none;font-weight:500;transition:.2s}
.ct-link i{width:18px;text-align:center;font-size:14px;color:#8fb3ec}
.ct-link:hover{background:#ffffff18}
.ct-link.on{background:linear-gradient(90deg,#2563eb,#3b8bf5);color:#fff;font-weight:700;box-shadow:0 10px 24px #2563eb55}
.ct-link.on i{color:#fff}
.ct-user{margin-top:auto;position:relative;padding:14px;border-radius:16px;background:#ffffff14;border:1px solid #ffffff2b}
.ct-who{display:flex;align-items:center;gap:11px}
.ct-av{display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-weight:800}
.ct-user b{display:block;font-size:12.5px}.ct-user small{font-size:10px;color:#a9c4f0}
.ct-user button{width:100%;margin-top:12px;padding:9px;border:1px solid #ffffff33;border-radius:10px;background:transparent;color:#dce8ff;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.ct-user button:hover{background:#ffffff1c}
@media(max-width:750px){
 .ct-sidebar{width:72px;padding:18px 8px}.ct-brand{justify-content:center}
 .ct-name,.ct-sub,.ct-title,.ct-link span,.ct-user b,.ct-user small,.ct-user button{display:none}
 .ct-link{justify-content:center;padding:13px 4px}.ct-user{padding:8px;background:none;border:0}.ct-who{justify-content:center}
}

/* RESPONSIVE */
@media(max-width:1250px){.g3,.wg{grid-template-columns:1fr 1fr}.kv{grid-template-columns:1fr 1fr}}
@media(max-width:1100px){.gmap{grid-template-columns:1fr}.content{padding:20px}}
@media(max-width:750px){.main{margin-left:72px}.content{padding:16px 12px 30px}.hero{padding:26px 20px}.hero h1{font-size:22px}.g3,.wg,.kv{grid-template-columns:1fr}.panel{padding:18px}.panel.flush{padding:0}.sform{flex-direction:column}.rt .pt{max-width:36%}#mapMain{height:500px}.map-info{display:none}.basebox{left:12px;right:12px;justify-content:space-between;top:auto;bottom:12px}.bb{min-width:0;flex:1}.bb span{display:none}.ctr{width:calc(100% - 24px)}.msearch{max-width:none;margin-left:0}.tools{padding:14px}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>
<body>

@php
    $rt = fn ($name) => Route::has($name) ? route($name) : '#';
    $steps = [
        ['CREATED', 'Created', 'fa-file-circle-plus'],
        ['BOOKED', 'Booked', 'fa-calendar-check'],
        ['PICKED_UP', 'Picked Up', 'fa-hand-holding-box'],
        ['IN_TRANSIT', 'In Transit', 'fa-truck-fast'],
        ['DELIVERED', 'Delivered', 'fa-circle-check'],
    ];
    $si = $selected ? array_search(strtoupper((string) $selected['status']), array_column($steps, 0), true) : false;
    $si = $si === false ? -1 : $si;
    $pct = $si < 0 ? 0 : (int) round($si / 4 * 100);
    $modeLabel = ['udara' => 'Udara', 'laut' => 'Laut', 'darat' => 'Darat'];
    $modeIcon = ['udara' => 'fa-plane', 'laut' => 'fa-ship', 'darat' => 'fa-truck-fast'];
@endphp

<!-- SIDEBAR -->
<aside class="ct-sidebar">
    <a href="{{ route('dashboard') }}" class="ct-brand">
        <div class="ct-logo"><i class="fa-solid fa-route"></i></div>
        <div class="ct-name">CargoTrace</div>
    </a>
    <div class="ct-sub">LOGISTICS &amp; SHIPMENT TRACKING</div>

    <div class="ct-title">MENU NAVIGASI</div>

<nav class="ct-menu">
    <a href="{{ route('dashboard') }}" class="ct-link">
        <i class="fa-solid fa-border-all"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('pengiriman.index') }}" class="ct-link">
        <i class="fa-solid fa-box"></i>
        <span>Pengiriman</span>
    </a>

    <a href="{{ route('pelacakan.index') }}" class="ct-link on">
        <i class="fa-solid fa-location-dot"></i>
        <span>Pelacakan</span>
    </a>

    <a href="{{ route('exceptions.index') }}" class="ct-link">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>Pengecualian</span>
    </a>

    <a href="{{ route('carriers.index') }}" class="ct-link">
        <i class="fa-solid fa-building"></i>
        <span>Perusahaan Pengiriman</span>
    </a>

    <a href="{{ route('fasilitas.index') }}" class="ct-link">
        <i class="fa-solid fa-warehouse"></i>
        <span>Lokasi &amp; Fasilitas</span>
    </a>

    <a href="{{ route('users.index') }}" class="ct-link">
        <i class="fa-solid fa-users-gear"></i>
        <span>Pengguna &amp; Hak Akses</span>
    </a>

    <a href="{{ route('notifications.index') }}" class="ct-link">
        <i class="fa-solid fa-bell"></i>
        <span>Notifikasi</span>
    </a>

    <a href="{{ route('audit-logs.index') }}" class="ct-link">
        <i class="fa-solid fa-clipboard-list"></i>
        <span>Audit Log</span>
    </a>
</nav>

    <div class="ct-user">
        <div class="ct-who">
            <div class="ct-av">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div><b>{{ auth()->user()->name ?? 'Administrator' }}</b><small>Shipment Tracking System</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button></form>
    </div>
</aside>

<main class="main">
<section class="content">

    <!-- 1. PENCARIAN: PENGIRIMAN ATAU LOKASI MANA PUN -->
    <section class="hero">
        <div class="crumb"><a href="{{ route('dashboard') }}" style="text-decoration:none">Dashboard</a> &nbsp;/&nbsp; Pelacakan</div>
        <div class="hero-row">
            <div>
                <h1>Pelacakan &amp; Peta</h1>
                <p>Lacak posisi barang berdasarkan nomor resi, atau jelajahi lokasi mana pun di dunia seperti Maps: negara, kota, sampai kampung terpencil, lengkap dengan cuaca dan waktu setempat.</p>
            </div>
            <div class="hstats">
                <div class="hs"><b>{{ number_format($stats['events']) }}</b>event tracking</div>
                <div class="hs"><b>{{ $stats['in_transit'] }}</b>dalam perjalanan</div>
                <div class="hs"><b>{{ $stats['delayed'] }}</b>terlambat</div>
                <div class="hs"><b>{{ $stats['delivered'] }}</b>terkirim</div>
            </div>
        </div>
        <div class="stabs">
            <button type="button" class="stab on" data-t="ship"><i class="fa-solid fa-box"></i> Lacak Pengiriman</button>
            <button type="button" class="stab" data-t="place"><i class="fa-solid fa-earth-asia"></i> Cari Lokasi di Peta</button>
        </div>
        <form class="sform" id="shipForm" action="{{ route('pelacakan.index') }}" method="GET">
            <div class="in">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nomor resi / pengiriman, contoh: CT-2026-001" aria-label="Nomor resi atau nomor pengiriman" autocomplete="off">
            </div>
            <button type="submit" class="btn"><i class="fa-solid fa-location-crosshairs"></i> Lacak Pengiriman</button>
            @if($search !== '')<a href="{{ route('pelacakan.index') }}" class="btn btn-light">Reset</a>@endif
        </form>
        <form class="sform hide" id="placeForm">
            <div class="in">
                <i class="fa-solid fa-location-dot"></i>
                <input type="text" id="heroQ" placeholder="Negara, kota, desa, atau jalan. Contoh: Jerman, Takengon, Desa Lampuuk" aria-label="Cari lokasi" autocomplete="off">
            </div>
            <button type="submit" class="btn"><i class="fa-solid fa-earth-asia"></i> Cari di Peta</button>
        </form>
    </section>

    @if($selected && count($picker) > 1)
    <div class="pick" aria-label="Pilih pengiriman">
        @foreach($picker as $p)
            <a href="{{ route('pelacakan.index', array_filter(['id' => $p['id'], 'search' => $search])) }}" class="pk {{ $p['id'] === $selected['id'] ? 'on' : '' }}">
                <b>{{ $p['number'] }}</b>
                <strong>{{ $p['customer'] }}</strong>
                <small>{{ $p['origin'] }} → {{ $p['destination'] }}</small>
                <span class="status st-{{ $p['status'] }}">{{ str_replace('_', ' ', $p['status']) }}</span>
            </a>
        @endforeach
    </div>
    @endif

    @if($selected)
    <!-- 2. DETAIL PENGIRIMAN -->
    <section class="panel sect">
        <div class="sum-top">
            <div>
                <h2>{{ $selected['number'] }}</h2>
                <p>{{ $selected['name'] }} · {{ $selected['customer'] }}</p>
            </div>
            <span class="status st-{{ $selected['status'] }}" style="font-size:11.5px;padding:8px 16px">{{ str_replace('_', ' ', $selected['status']) }}</span>
        </div>

        <div class="rt">
            <div class="d"></div>
            <div class="pt"><small>ASAL</small><b>{{ $selected['origin'] }}</b></div>
            <div class="ln"><i class="f" style="width:{{ $pct }}%"></i><span style="left:{{ $pct }}%"><i class="fa-solid {{ $modeIcon[$selected['mode']] ?? 'fa-truck-fast' }}"></i></span></div>
            <div class="pt" style="text-align:right"><small>TUJUAN</small><b>{{ $selected['destination'] }}</b></div>
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
            <div><small>NOMOR RESI</small><b>{{ $selected['number'] }}</b></div>
            <div><small>PELANGGAN</small><b>{{ $selected['customer'] }}</b></div>
            <div><small>PERUSAHAAN PENGIRIMAN</small><b>{{ $selected['carrier'] ?: '-' }}</b></div>
            <div><small>LOKASI ASAL</small><b>{{ $selected['origin'] }}</b></div>
            <div><small>LOKASI TUJUAN</small><b>{{ $selected['destination'] }}</b></div>
            <div><small>MODA &amp; BARANG</small><b>{{ $modeLabel[$selected['mode']] ?? '-' }} · {{ $selected['cargo'] ?: '-' }}{{ $selected['weight'] ? ' · ' . rtrim(rtrim(number_format((float) $selected['weight'], 2, ',', '.'), '0'), ',') . ' kg' : '' }}</b></div>
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
    @endif

    <!-- PETA BEBAS (SELURUH DUNIA) + PENGIRIMAN -->
    <section class="gmap" id="peta">
        <div class="panel flush">
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
                @if($selected)
                <div class="tg" id="jumpGroup">
                    <span class="tgl">PENGIRIMAN</span>
                    <button type="button" class="chip" data-j="all"><i class="fa-solid fa-route"></i> Seluruh rute</button>
                    <button type="button" class="chip" data-j="o"><i class="fa-solid fa-box"></i> Asal</button>
                    <button type="button" class="chip" data-j="p"><i class="fa-solid fa-truck-fast"></i> Posisi</button>
                    <button type="button" class="chip" data-j="d"><i class="fa-solid fa-flag-checkered"></i> Tujuan</button>
                </div>
                @endif
                <div class="msearch">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="mapQ" placeholder="Cari negara, kota, desa, jalan… lalu Enter" aria-label="Cari lokasi di peta" autocomplete="off">
                    <div class="sres" id="sres"></div>
                </div>
                <span class="tmsg" id="tmsg"></span>
            </div>
            <div class="mapw">
                <div id="mapMain"></div>
                <div class="basebox" id="baseBox" role="group" aria-label="Jenis peta">
                    <button class="bb on" data-base="default" type="button" title="Peta standar"><i class="fa-solid fa-map"></i><span>Default</span></button>
                    <button class="bb" data-base="sat" type="button" title="Citra satelit"><i class="fa-solid fa-satellite"></i><span>Satelit</span></button>
                    <button class="bb" data-base="terrain" type="button" title="Kontur medan"><i class="fa-solid fa-mountain"></i><span>Medan</span></button>
                    <button class="bb" data-base="transit" type="button" title="Jalur transportasi umum"><i class="fa-solid fa-train-subway"></i><span>Transportasi</span></button>
                    <button class="bb" data-base="traffic" type="button" title="Kondisi lalu lintas"><i class="fa-solid fa-traffic-light"></i><span>Lalu lintas</span></button>
                </div>
                <div class="xhair" id="xhair"></div>
                <div class="ctr" id="ctr"></div>
                <div class="map-info" id="mapInfo"><span><i class="fa-solid fa-hand-pointer" style="color:#2563eb"></i> Klik titik mana pun di peta untuk melihat cuaca &amp; waktunya</span></div>
            </div>
        </div>

        <div class="sidecol">
            <div class="panel">
                <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-location-pin"></i></span> Detail Lokasi</h2><p>Hasil pencarian atau titik yang kamu klik di peta.</p></div></div>
                <div id="pickBox"><div class="nodata"><i class="fa-solid fa-earth-asia"></i>Cari nama tempat, atau klik di peta. Bisa sampai kampung terpencil di seluruh dunia.</div></div>
            </div>
            @if($selected)
            <div class="panel">
                <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-clock-rotate-left"></i></span> Riwayat Perjalanan</h2><p>Timeline event tracking secara kronologis (historis, event lama tidak ditimpa).</p></div></div>
                <div id="tlTools"></div>
                <div class="tl-wrap" id="timeline"></div>
            </div>
            @endif
        </div>
    </section>

    @if($selected)
    <!-- CUACA PENGIRIMAN -->
    <section class="panel sect">
        <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-cloud-sun-rain"></i></span> Cuaca di Lokasi Pengiriman</h2><p>Kondisi terkini dan prakiraan 3 hari di lokasi asal, lokasi saat ini, dan tujuan. Sumber: Open-Meteo.</p></div></div>
        <div class="wg" id="weatherGrid"><div class="skl"></div><div class="skl"></div><div class="skl"></div></div>
    </section>

    <!-- WAKTU LOKAL PENGIRIMAN -->
    <section class="panel sect">
        <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-globe"></i></span> Waktu Lokal Pengiriman</h2><p>Jam setempat dihitung dari koordinat tiap lokasi, dibandingkan dengan waktu perangkatmu.</p></div></div>
        <div class="wg" id="clockGrid"><div class="skl"></div><div class="skl"></div><div class="skl"></div></div>
    </section>
    @endif

</section>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const S = @json($selected);
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
const nice = v => esc(String(v || '').replace(/_/g, ' '));
const p2 = n => String(n).padStart(2, '0');
const sleep = ms => new Promise(r => setTimeout(r, ms));
const fetchT = (u, ms = 7000) => { const c = new AbortController(), t = setTimeout(() => c.abort(), ms); return fetch(u, { signal:c.signal }).finally(() => clearTimeout(t)); };

/* ===== TAB PENCARIAN HERO ===== */
document.querySelectorAll('.stab').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('.stab').forEach(x => x.classList.toggle('on', x === b));
    $('shipForm').classList.toggle('hide', b.dataset.t !== 'ship');
    $('placeForm').classList.toggle('hide', b.dataset.t !== 'place');
    if (b.dataset.t === 'place') $('heroQ').focus();
}));

/* ===== KONSTANTA ===== */
const TOMTOM_KEY = '';   // opsional: API key TomTom (gratis) agar lapisan lalu lintas resmi. Kosong = tile lalu lintas Google (tidak resmi).
const VEH = { udara:{ name:'Pesawat', icon:'fa-plane', color:'#2563eb' }, laut:{ name:'Kapal', icon:'fa-ship', color:'#0f9d9a' }, darat:{ name:'Truk', icon:'fa-truck-fast', color:'#f59e0b' } };
const GEO = (() => { try { return JSON.parse(localStorage.getItem('ct_geo') || '{}'); } catch (_) { return {}; } })();
const saveGeo = () => { try { localStorage.setItem('ct_geo', JSON.stringify(GEO)); } catch (_) {} };

/* ===== WAKTU: dihitung dari offset UTC koordinat itu sendiri ===== */
const fmtClock = off => { const d = new Date(Date.now() + off * 1000); return p2(d.getUTCHours()) + ':' + p2(d.getUTCMinutes()) + ':' + p2(d.getUTCSeconds()); };
const fmtDate = off => new Intl.DateTimeFormat('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric', timeZone:'UTC' }).format(new Date(Date.now() + off * 1000));
const utcLabel = off => { const a = Math.abs(off), h = Math.floor(a / 3600), m = Math.round((a % 3600) / 60); return 'UTC' + (off < 0 ? '−' : '+') + p2(h) + ':' + p2(m); };
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

/* ===== CUACA (Open-Meteo, berlaku untuk koordinat mana pun termasuk kampung terpencil) ===== */
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

/* ===== GEOKODING (OpenStreetMap Nominatim: negara, kota, desa, jalan) ===== */
let lastNet = 0;
let gateP = Promise.resolve();
const gate = () => (gateP = gateP.then(async () => { const w = 1100 - (Date.now() - lastNet); if (w > 0) await sleep(w); lastNet = Date.now(); }));
async function geocode(name) {
    if (!name) return null;
    const k = String(name).toLowerCase().trim();
    if (k in GEO) return GEO[k];
    const parts = String(name).split(',').map(x => x.trim()).filter(Boolean);
    const tries = [name]; if (parts.length > 1) tries.push(parts.slice(-2).join(', '));
    for (const q of tries) {
        await gate();
        try {
            const r = await fetchT('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&accept-language=id&q=' + encodeURIComponent(q));
            if (!r.ok) return null;
            const j = await r.json();
            if (j && j[0]) { const v = [+j[0].lat, +j[0].lon]; GEO[k] = v; saveGeo(); return v; }
        } catch (_) { return null; }
    }
    GEO[k] = null; saveGeo();
    return null;
}
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
const shortName = n => String(n || '').split(',').slice(0, 2).map(x => x.trim()).join(', ');

/* ===== BLOK HTML CUACA & WAKTU (dipakai panel & seksi pengiriman) ===== */
function weatherHtml(i) {
    if (!i || !i.ok) return '<div class="wph">Data cuaca belum tersedia untuk titik ini.</div>';
    const c = cond(i.c, i.day);
    return '<div class="wcur"><span class="ic">' + c[1] + '</span><div><div class="tmp">' + Math.round(i.t) + '°C</div><small>' + c[0] + '</small></div></div>' +
        '<div class="wm"><div><b>' + i.h + '%</b><small>Lembap</small></div><div><b>' + i.w + '</b><small>km/j angin</small></div><div><b>' + i.r + '</b><small>mm hujan</small></div></div>' +
        '<div class="fc">' + i.daily.map(d => '<div><small>' + new Date(d.date + 'T00:00:00').toLocaleDateString('id-ID', { weekday:'short', day:'numeric' }) + '</small><span>' + cond(d.c)[1] + '</span><b>' + Math.round(d.max) + '°/' + Math.round(d.min) + '°</b></div>').join('') + '</div>';
}
function timeHtml(i, baseOff, baseLabel) {
    if (!i) return '<div class="wph">Zona waktu belum dapat ditentukan.</div>';
    let extra = '';
    if (baseOff !== undefined && baseOff !== null) {
        const h = (i.off - baseOff) / 3600;
        extra = '<div class="cdiff">' + (Math.abs(h) < 0.01 ? 'Sama dengan waktu ' + baseLabel : (Number.isInteger(Math.abs(h)) ? Math.abs(h) : Math.abs(h).toFixed(1).replace('.', ',')) + ' jam ' + (h > 0 ? 'lebih cepat' : 'lebih lambat') + ' dari ' + baseLabel) + '</div>';
    }
    return '<div class="ck tmk" data-off="' + i.off + '">' + fmtClock(i.off) + '</div><div class="cd tmd" data-off="' + i.off + '">' + fmtDate(i.off) + '</div>' +
        '<span class="cz">' + (i.tz ? esc(i.tz) + ' · ' : '') + utcLabel(i.off) + (i.approx ? ' (perkiraan)' : '') + '</span><div class="cdiff">' + diffText(i.off) + '</div>' + extra;
}

/* =====================================================================
   PETA
   ===================================================================== */
const osm = () => L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap contributors', maxNativeZoom:19, maxZoom:19, keepBuffer:2 });
const BASES = {
    default: osm(),
    sat: L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { subdomains:'0123', maxNativeZoom:20, maxZoom:19, keepBuffer:2, attribution:'Imagery &copy; Google' }),
    terrain: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', { subdomains:'abc', maxNativeZoom:17, maxZoom:19, keepBuffer:2, attribution:'&copy; OpenStreetMap, SRTM | Style &copy; OpenTopoMap (CC-BY-SA)' }),
    /* Transportasi: OpenStreetMap + OPNVKarte (bus, kereta, tram). Tanpa API key. */
    transit: L.layerGroup([
        osm(),
        L.tileLayer('https://tile.memomaps.de/tilegen/{z}/{x}/{y}.png', { maxNativeZoom:18, maxZoom:19, keepBuffer:2, attribution:'Map &copy; OpenStreetMap contributors, &Ouml;PNVKarte memomaps.de' })
    ]),
    traffic: TOMTOM_KEY
        ? L.layerGroup([osm(), L.tileLayer('https://api.tomtom.com/traffic/map/4/tile/flow/relative0/{z}/{x}/{y}.png?key=' + encodeURIComponent(TOMTOM_KEY), { opacity:.85, maxZoom:19, attribution:'Traffic &copy; TomTom' })])
        : L.tileLayer('https://mt{s}.google.com/vt/lyrs=m,traffic&x={x}&y={y}&z={z}', { subdomains:'0123', maxNativeZoom:19, maxZoom:19, keepBuffer:2, attribution:'Map data &copy; Google' })
};
const map = L.map('mapMain', { worldCopyJump:true, maxZoom:19, minZoom:2, preferCanvas:true, wheelDebounceTime:80 }).setView([2, 110], 4);
let curBase = BASES.default.addTo(map);
const shipLayer = L.layerGroup().addTo(map), pickLayer = L.layerGroup().addTo(map);
setTimeout(() => map.invalidateSize(), 250);

const st = { view:'loc', radar:true };
let radarLayer = null, T = null, PICK = null, pickTok = 0, ctrTok = 0, ctrTimer = null;

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

/* marker */
const pin = (color, icon, cls, label, bdg) => L.divIcon({ className:'',
    html:'<div class="pw"><div class="pin ' + (cls || '') + '" style="background:' + color + '"><i class="fa-solid ' + icon + '"></i></div>' +
        ((label || bdg) ? '<div class="pl">' + (label ? '<span class="vl">' + esc(label) + '</span>' : '') + (bdg || '') + '</div>' : '') + '</div>',
    iconSize:[34,34], iconAnchor:[17,17], popupAnchor:[0,-20] });
const dotIcon = L.divIcon({ className:'', html:'<div class="tp"></div>', iconSize:[10,10], iconAnchor:[5,5] });

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
function ctxHtml(lat, lng) {
    const w = WXV[wkey(lat, lng)]; if (!w) return '';
    const c = w.ok ? cond(w.c, w.day) : null;
    return '<span style="display:block;margin-top:4px;padding-top:4px;border-top:1px dashed #dce8f8">' + (c ? c[1] + ' ' + c[0] + ', ' + Math.round(w.t) + '°C · angin ' + Math.round(w.w) + ' km/j<br>' : '') +
        '<i class="fa-regular fa-clock"></i> Waktu lokal <b class="tmk" data-off="' + w.off + '">' + fmtClock(w.off) + '</b> (' + utcLabel(w.off) + ')</span>';
}

/* ----- titik pilihan (klik peta / hasil pencarian) ----- */
function drawPick() {
    pickLayer.clearLayers();
    if (!PICK) return;
    const { lat, lng, name } = PICK;
    L.marker([lat, lng], { icon:pin('#e5484d', 'fa-location-dot', 'big', shortName(name), badge(lat, lng)), zIndexOffset:1000 })
        .bindPopup('<div class="pop"><strong>' + esc(shortName(name) || 'Titik terpilih') + '</strong><small>' + lat.toFixed(5) + ', ' + lng.toFixed(5) + '</small>' + ctxHtml(lat, lng) + '</div>').addTo(pickLayer);
}
async function selectPoint(lat, lng, name) {
    const tok = ++pickTok;
    PICK = { lat, lng, name:name || null };
    drawPick();
    $('pickBox').innerHTML = '<div class="skl"></div>';
    const infoP = infoAt(lat, lng);
    const nameP = name ? Promise.resolve(name) : reverse(lat, lng, Math.max(3, Math.min(18, Math.round(map.getZoom()) + 1)));
    const info = await infoP;
    if (tok !== pickTok) return;
    if (st.view !== 'loc') drawPick();
    $('pickBox').innerHTML = renderPick(PICK, info);
    const nm = await nameP;
    if (tok !== pickTok) return;
    PICK.name = nm || null;
    drawPick();
    $('pickBox').innerHTML = renderPick(PICK, info);
}
function renderPick(p, info) {
    const title = p.name ? shortName(p.name) : 'Titik di peta';
    return '<div class="pnl-stack"><div class="lastloc"><i class="fa-solid fa-location-dot"></i><div><b>' + esc(title) + '</b><small>' + (p.name ? esc(p.name) + '<br>' : '') + p.lat.toFixed(5) + ', ' + p.lng.toFixed(5) + '</small></div></div>' +
        '<div class="wc"><div class="wt"><span class="wi" style="background:#2563eb"><i class="fa-solid fa-cloud-sun-rain"></i></span>CUACA SAAT INI</div>' + weatherHtml(info) + '</div>' +
        '<div class="wc dark"><div class="wt"><span class="wi" style="background:#38bdf8"><i class="fa-regular fa-clock"></i></span>WAKTU SETEMPAT</div>' + timeHtml(info) + '</div></div>';
}
map.on('click', e => { const c = e.latlng.wrap(); selectPoint(c.lat, c.lng, null); });

/* ----- info di pusat peta (mode Cuaca / Waktu) ----- */
function scheduleCenter() {
    clearTimeout(ctrTimer);
    const on = st.view !== 'loc';
    $('xhair').classList.toggle('show', on); $('ctr').classList.toggle('show', on);
    if (on) ctrTimer = setTimeout(updateCenter, 700);
}
async function updateCenter() {
    if (st.view === 'loc') return;
    const c = map.getCenter().wrap(), tok = ++ctrTok, box = $('ctr');
    box.innerHTML = '<div class="skl" style="height:70px"></div>';
    const [info, nm] = await Promise.all([infoAt(c.lat, c.lng), reverse(c.lat, c.lng, Math.max(3, Math.min(18, Math.round(map.getZoom()) + 1)))]);
    if (tok !== ctrTok || st.view === 'loc') return;
    const head = '<div class="cn">' + esc(nm ? shortName(nm) : 'Pusat peta') + '</div><div class="cc">' + c.lat.toFixed(4) + ', ' + c.lng.toFixed(4) + '</div>';
    let body;
    if (st.view === 'wx') {
        if (info.ok) { const x = cond(info.c, info.day); body = '<div class="cw"><span class="e">' + x[1] + '</span><div><b>' + Math.round(info.t) + '°C</b><small>' + x[0] + ' · lembap ' + info.h + '% · angin ' + info.w + ' km/j</small></div></div>'; }
        else body = '<div class="cs">Data cuaca belum tersedia.</div>';
    } else {
        body = '<div class="ct tmk" data-off="' + info.off + '">' + fmtClock(info.off) + '</div><div class="cs"><span class="tmd" data-off="' + info.off + '">' + fmtDate(info.off) + '</span><br>' + (info.tz ? esc(info.tz) + ' · ' : '') + utcLabel(info.off) + '<br>' + diffText(info.off) + '</div>';
    }
    box.innerHTML = head + body + '<button type="button" class="go" id="ctrGo"><i class="fa-solid fa-circle-info"></i> Detail lokasi ini</button>';
    $('ctrGo').addEventListener('click', () => selectPoint(c.lat, c.lng, nm || null));
}
map.on('moveend', scheduleCenter);

/* ----- pencarian tempat apa pun ----- */
async function placeSearch(q, auto) {
    q = String(q || '').trim(); if (!q) return;
    $('mapQ').value = q; $('tmsg').textContent = 'Mencari…';
    const box = $('sres');
    try {
        await gate();
        const r = await fetchT('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=6&accept-language=id&q=' + encodeURIComponent(q));
        const j = await r.json();
        $('tmsg').textContent = '';
        if (!j.length) { box.innerHTML = '<div class="none">Lokasi tidak ditemukan. Coba ejaan lain atau tambahkan nama daerah.</div>'; box.classList.add('open'); return; }
        box.innerHTML = j.map((x, n) => '<button type="button" data-n="' + n + '"><i class="fa-solid fa-location-dot"></i><span><b>' + esc(shortName(x.display_name)) + '</b>' + esc(x.display_name) + '</span></button>').join('');
        box.classList.add('open');
        box.querySelectorAll('button').forEach(b => b.addEventListener('click', () => { goResult(j[+b.dataset.n]); box.classList.remove('open'); }));
        if (auto !== false) goResult(j[0]);
    } catch (_) { $('tmsg').textContent = 'Pencarian gagal, coba lagi.'; }
}
function goResult(x) {
    const lat = +x.lat, lng = +x.lon, bb = x.boundingbox ? x.boundingbox.map(Number) : null;
    if (bb && (Math.abs(bb[1] - bb[0]) > 0.02 || Math.abs(bb[3] - bb[2]) > 0.02)) map.fitBounds([[bb[0], bb[2]], [bb[1], bb[3]]], { padding:[30,30], maxZoom:17 });
    else map.flyTo([lat, lng], 16, { duration:1.2 });
    selectPoint(lat, lng, x.display_name);
}
$('mapQ').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); placeSearch(e.target.value); } });
$('placeForm').addEventListener('submit', e => { e.preventDefault(); $('peta').scrollIntoView({ behavior:'smooth' }); placeSearch($('heroQ').value); });
document.addEventListener('click', e => { if (!e.target.closest('.msearch')) $('sres').classList.remove('open'); });

/* ----- mode tampilan ----- */
document.querySelectorAll('#viewGroup .chip').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('#viewGroup .chip').forEach(x => x.classList.toggle('on', x === b));
    st.view = b.dataset.view;
    $('radarGroup').style.display = st.view === 'wx' ? 'flex' : 'none';
    setRadar(st.view === 'wx' && st.radar);
    scheduleCenter();
    drawPick();
    if (T) drawShip(false);
}));
$('radarBtn').addEventListener('click', () => { st.radar = !st.radar; $('radarBtn').classList.toggle('on', st.radar); setRadar(st.view === 'wx' && st.radar); });

/* =====================================================================
   PENGIRIMAN (jika ada yang dilacak)
   ===================================================================== */
const kmBetween = (a, b) => { const r = x => x * Math.PI / 180, dl = r(b[0] - a[0]), dn = r(b[1] - a[1]), h = Math.sin(dl / 2) ** 2 + Math.cos(r(a[0])) * Math.cos(r(b[0])) * Math.sin(dn / 2) ** 2; return 12742 * Math.asin(Math.sqrt(h)); };
function arc(a, b, n) {
    const mx = (a[0] + b[0]) / 2, my = (a[1] + b[1]) / 2, dx = b[0] - a[0], dy = b[1] - a[1], cx = mx - dy * .18, cy = my + dx * .18, pts = [];
    for (let i = 0; i <= n; i++) { const t = i / n, u = 1 - t; pts.push([u*u*a[0] + 2*u*t*cx + t*t*b[0], u*u*a[1] + 2*u*t*cy + t*t*b[1]]); }
    return pts;
}
function timeFrac() {
    const a = Date.parse(S.etd), b = Date.parse(S.eta);
    if (isNaN(a) || isNaN(b) || b <= a) return .5;
    return Math.min(.92, Math.max(.08, (Date.now() - a) / (b - a)));
}
async function buildShip(fast) {
    const veh = VEH[S.mode] || VEH.darat;
    /* hanya event VALID (bukan waktu masa depan, punya lokasi/koordinat) yang dipakai untuk peta & posisi */
    const evs = (S.events || []).filter(e => e.valid);
    const [o, d] = await Promise.all([S.origin_pos || (fast ? null : geocode(S.origin)), S.dest_pos || (fast ? null : geocode(S.destination))]);
    const ev = evs.filter(e => e.lat !== null && e.lng !== null).map(e => ({ ll:[e.lat, e.lng], t:e }));
    const last = evs.length ? evs[evs.length - 1] : null;   // current location = event valid terbaru
    /* jika event valid terbaru tidak punya koordinat, geocode nama lokasinya */
    if (!fast && last && (last.lat === null || last.lng === null) && last.location) { const g = await geocode(last.location); if (g) ev.push({ ll:g, t:last }); }
    const planned = (o && d) ? arc(o, d, 60) : null;
    let pos = null, exact = false;
    if (ev.length) { pos = ev[ev.length - 1].ll; exact = true; }
    else if (planned) {
        const f = S.status === 'DELIVERED' ? 1 : S.status === 'IN_TRANSIT' ? timeFrac() : S.status === 'PICKED_UP' ? .05 : 0;
        pos = planned[Math.round(f * (planned.length - 1))];
    } else pos = o || d;
    return { o, d, ev, last, veh, planned, pos, exact };
}
function popShip(ll, extra) {
    const v = T.veh;
    return '<div class="pop"><strong>' + esc(S.number) + '</strong><span class="veh"><i class="fa-solid ' + v.icon + '"></i> ' + esc(v.name) + (S.vnum ? ' · ' + esc(S.vnum) : '') + '</span><br>Customer: ' + esc(S.customer) +
        '<br>Carrier: ' + esc(S.carrier || '-') + '<br>Rute: ' + esc(S.origin) + ' → ' + esc(S.destination) + '<br>Status: ' + nice(S.status) + (S.eta_text ? '<br>ETA: ' + esc(S.eta_text) : '') + (extra || '') + (ll ? ctxHtml(ll[0], ll[1]) : '') + '</div>';
}
function drawShip(fit) {
    if (!T) return;
    shipLayer.clearLayers();
    const bounds = [], need = [], delivered = S.status === 'DELIVERED';
    const bd = ll => { need.push(ll); return badge(ll[0], ll[1]); };
    if (T.planned) L.polyline(T.planned, { color:'#2f8cf5', weight:3, opacity:.7, dashArray:'7,8' }).bindPopup(popShip(null, '<br><small>Garis rencana rute (bukan jalur aktual).</small>')).addTo(shipLayer);
    if (T.ev.length > 1) L.polyline(T.ev.map(e => e.ll), { color:'#f59e0b', weight:4, opacity:.95 }).addTo(shipLayer);
    else if (!T.exact && T.planned && S.status !== 'CREATED' && S.status !== 'BOOKED') {
        const cut = T.planned.findIndex(p => p === T.pos);
        if (cut > 0) L.polyline(T.planned.slice(0, cut + 1), { color:'#f59e0b', weight:4, opacity:.95 }).addTo(shipLayer);
    }
    if (T.o) { L.marker(T.o, { icon:pin('#2563eb', 'fa-box', '', 'Asal', bd(T.o)) }).bindPopup(popShip(T.o, '<br><b>Titik asal</b>')).addTo(shipLayer); bounds.push(T.o); }
    if (T.d) { L.marker(T.d, { icon:pin('#16a34a', 'fa-flag-checkered', '', 'Tujuan', bd(T.d)) }).bindPopup(popShip(T.d, '<br><b>Titik tujuan</b>')).addTo(shipLayer); bounds.push(T.d); }
    T.ev.forEach(e => {
        const x = e.t.exception;   // titik exception diberi warna merah
        L.circleMarker(e.ll, { radius:x ? 7 : 5, weight:3, color:x ? '#e5484d' : '#2563eb', fillColor:x ? '#ffe9e9' : '#fff', fillOpacity:1 }).bindPopup('<div class="pop"><strong>' + esc(S.number) + '</strong>' + (x ? '<span class="xtag"><i class="fa-solid fa-triangle-exclamation"></i> Exception</span><br>' : '') + 'Lokasi: ' + esc(e.t.location || '-') + '<br>Status: ' + nice(e.t.status) + (e.t.desc ? '<br>' + esc(e.t.desc) : '') + '<br><small>' + esc(e.t.time_text || '') + '</small></div>').addTo(shipLayer);
        bounds.push(e.ll);
    });
    if (T.pos && !delivered) {
        L.marker(T.pos, { icon:pin(T.veh.color, T.veh.icon, 'big fleet', T.veh.name + (S.vnum ? ' ' + S.vnum : ''), bd(T.pos)), zIndexOffset:900 })
            .bindPopup(popShip(T.pos, T.exact ? '<br><b>Posisi terakhir tercatat</b>' : '<br><small>Posisi diperkirakan di sepanjang rute.</small>')).addTo(shipLayer);
        bounds.push(T.pos);
    }
    T.bounds = bounds;
    if (fit && bounds.length) { if (bounds.length === 1) map.setView(bounds[0], 11); else map.fitBounds(bounds, { padding:[70,70], maxZoom:12 }); }
    const fresh = need.filter(p => !WXV[wkey(p[0], p[1])]);
    if (st.view !== 'loc' && fresh.length) Promise.all(fresh.map(p => infoAt(p[0], p[1]))).then(() => { if (st.view !== 'loc') drawShip(false); });
}
function legend() {
    const v = T.veh;
    $('mapInfo').innerHTML = '<span><i class="d" style="background:#2563eb"></i> Asal</span><span><i class="d" style="background:#16a34a"></i> Tujuan</span>' +
        '<span><i class="fa-solid ' + v.icon + '" style="color:' + v.color + '"></i> ' + v.name + (S.vnum ? ' ' + esc(S.vnum) : '') + '</span>' +
        '<span><i class="ln"></i> Sudah dilalui</span><span><i class="ln ds"></i> Rencana rute</span>' +
        '<span><i class="d" style="background:#e5484d"></i> Titik exception</span>' +
        '<span><i class="fa-solid fa-hand-pointer" style="color:#2563eb"></i> Klik peta untuk cek lokasi lain</span>';
}

if (S) (async function () {
    const events = S.events || [], delivered = S.status === 'DELIVERED';
    const cur = S.current;   // event valid terbaru (dihitung di server)
    const dur = ms => { const m = Math.floor(Math.abs(ms) / 60000), d = Math.floor(m / 1440), h = Math.floor(m % 1440 / 60), mi = m % 60; return d > 0 ? d + ' hari ' + h + ' jam' : (h > 0 ? h + ' jam ' + mi + ' menit' : mi + ' menit'); };
    const ago = iso => { const m = Math.floor((Date.now() - new Date(iso).getTime()) / 60000); return m < 1 ? 'baru saja' : m < 60 ? m + ' menit lalu' : m < 1440 ? Math.floor(m / 60) + ' jam lalu' : Math.floor(m / 1440) + ' hari lalu'; };

    /* LOKASI TERAKHIR (dari event valid terbaru) */
    if (!events.length) $('lastLoc').innerHTML = '<div class="nodata"><i class="fa-solid fa-location-pin-lock"></i>Belum ada data pelacakan tercatat untuk pengiriman ini.</div>';
    else if (!cur) $('lastLoc').innerHTML = '<div class="nodata"><i class="fa-solid fa-circle-exclamation"></i>Belum ada event yang valid (waktu/lokasi tidak lengkap atau di masa depan). ' + S.events_invalid + ' event dilewati.</div>';
    else {
        $('lastLoc').innerHTML = '<div class="icb ' + (cur.exception ? 'bad' : 'info') + '"><i class="fa-solid ' + (cur.exception ? 'fa-triangle-exclamation' : 'fa-location-dot') + '"></i></div><div class="big">' + esc(cur.location || 'Lokasi tidak tercatat') + '</div>' +
            '<div class="sub">' + nice(cur.status) + ' · ' + esc(cur.time_text) + (cur.time ? ' (' + ago(cur.time) + ')' : '') + '</div>' + (cur.desc ? '<div class="sub">' + esc(cur.desc) + '</div>' : '') +
            '<div class="mini">' + (cur.lat !== null ? '<span>' + cur.lat.toFixed(4) + ', ' + cur.lng.toFixed(4) + '</span>' : '<span>Koordinat belum tercatat</span>') + (cur.by ? '<span>Oleh ' + esc(cur.by) + '</span>' : '') +
            (S.events_invalid ? '<span class="warnb">' + S.events_invalid + ' event dilewati (tidak valid)</span>' : '') + '</div>';
    }

    /* ETA + KETERLAMBATAN */
    function renderEta() {
        const now = Date.now(), eta = S.eta ? new Date(S.eta).getTime() : null, etd = S.etd ? new Date(S.etd).getTime() : null;
        let pct = delivered ? 100 : 0;
        if (!delivered && eta && etd && eta > etd) pct = Math.max(0, Math.min(100, (now - etd) / (eta - etd) * 100));
        const late = !delivered && eta && now > eta, flagged = S.status === 'DELAYED';
        const sub = delivered ? 'Barang sudah diterima.' : !eta ? 'Jadwal tiba belum diisi.' : (late ? 'Melewati ETA ' + dur(now - eta) + ' yang lalu.' : 'Tiba dalam ' + dur(eta - now) + '.');
        $('etaBox').innerHTML = '<div class="icb info"><i class="fa-solid fa-calendar-check"></i></div><div class="big">' + esc(S.eta_text) + '</div><div class="sub">' + sub + '</div><div class="bar"><i style="width:' + pct.toFixed(0) + '%"></i></div>' +
            '<div class="mini"><span>Berangkat ' + esc(S.etd_text) + '</span>' + (etd && eta && eta > etd ? '<span>Durasi ' + dur(eta - etd) + '</span>' : '') + '<span>' + pct.toFixed(0) + '% perjalanan</span></div>';
        let cls = 'ok', icon = 'fa-circle-check', head = 'Tepat waktu', text = eta ? 'Belum melewati estimasi tiba. Sisa waktu ' + dur(eta - now) + '.' : 'Estimasi tiba belum diisi.';
        if (delivered) { head = 'Sudah diterima'; text = 'Pengiriman telah selesai, tidak ada keterlambatan aktif.'; }
        else if (late) { cls = 'bad'; icon = 'fa-triangle-exclamation'; head = 'Terlambat ' + dur(now - eta); text = 'Barang belum tiba setelah estimasi tiba ' + S.eta_text + '.'; }
        else if (flagged) { cls = 'warn'; icon = 'fa-clock'; head = 'Pengiriman tertunda'; text = 'Status pengiriman ditandai DELAYED.'; }
        $('delayBox').innerHTML = '<div class="icb ' + cls + '"><i class="fa-solid ' + icon + '"></i></div><div class="big">' + head + '</div><div class="sub">' + text + '</div>' +
            ((late || flagged) ? '<div class="cause ' + (S.delay_cause ? '' : 'n') + '"><b>Penyebab:</b> ' + (S.delay_cause ? esc(S.delay_cause) : 'Belum dicatat pada riwayat tracking.') + '</div>' : '');
    }
    renderEta(); setInterval(renderEta, 30000);

    /* TIMELINE: kronologis (lama → baru) sebagai default, bisa dibalik; filter normal / exception */
    const tl = { sort:'asc', f:'all' };
    function renderTimeline() {
        const nExc = S.events_exception || 0, nAll = events.length;
        let list = events.filter(e => tl.f === 'all' || (tl.f === 'exc' ? e.exception : !e.exception));
        if (tl.sort === 'desc') list = list.slice().reverse();

        $('tlTools').innerHTML =
            '<div class="tl-tools">' +
                '<button type="button" class="chip' + (tl.f === 'all' ? ' on' : '') + '" data-f="all">Semua (' + nAll + ')</button>' +
                '<button type="button" class="chip' + (tl.f === 'nor' ? ' on' : '') + '" data-f="nor"><i class="fa-solid fa-circle-check"></i> Normal (' + (nAll - nExc) + ')</button>' +
                '<button type="button" class="chip' + (tl.f === 'exc' ? ' on' : '') + '" data-f="exc"><i class="fa-solid fa-triangle-exclamation"></i> Exception (' + nExc + ')</button>' +
                '<span class="sp"></span>' +
                '<button type="button" class="chip" id="tlSort"><i class="fa-solid ' + (tl.sort === 'asc' ? 'fa-arrow-down-1-9' : 'fa-arrow-up-9-1') + '"></i> ' + (tl.sort === 'asc' ? 'Lama → Baru' : 'Baru → Lama') + '</button>' +
            '</div>' +
            '<div class="tl-sum">Menampilkan <b>' + list.length + '</b> dari <b>' + nAll + '</b> event' + (S.events_total > nAll ? ' (' + nAll + ' terbaru dari total ' + S.events_total + ')' : '') + '.' +
            (S.events_invalid ? ' <b>' + S.events_invalid + '</b> event ditandai tidak valid.' : '') + '</div>';

        $('timeline').innerHTML = !nAll
            ? '<div class="nodata"><i class="fa-solid fa-route"></i>Belum ada riwayat perjalanan. Riwayat muncul setelah event tracking ditambahkan.</div>'
            : !list.length
                ? '<div class="nodata"><i class="fa-solid fa-filter-circle-xmark"></i>Tidak ada event pada filter ini.</div>'
                : '<div class="tl">' + list.map(e => {
                    const tags = [];
                    tags.push(e.exception ? '<span class="tag x"><i class="fa-solid fa-triangle-exclamation"></i> Exception</span>' : '<span class="tag n"><i class="fa-solid fa-circle-check"></i> Normal</span>');
                    if (e.is_current) tags.push('<span class="tag c"><i class="fa-solid fa-location-dot"></i> Lokasi saat ini</span>');
                    if (!e.valid) tags.push('<span class="tag i"><i class="fa-solid fa-circle-exclamation"></i> Tidak valid</span>');
                    return '<div class="ti' + (e.is_current ? ' cur' : '') + (e.exception ? ' exc' : '') + (e.valid ? '' : ' inv') + '"><b>' + nice(e.status) + '</b><span class="loc">' + esc(e.location || '-') + '</span>' +
                        (e.desc ? '<small>' + esc(e.desc) + '</small>' : '') +
                        '<small>' + esc(e.time_text) + (e.lat !== null ? ' · ' + e.lat.toFixed(4) + ', ' + e.lng.toFixed(4) : '') + (e.by ? ' · ' + esc(e.by) : '') + '</small>' +
                        '<div class="tags">' + tags.join('') + '</div></div>';
                }).join('') + '</div>';

        document.querySelectorAll('#tlTools [data-f]').forEach(b => b.addEventListener('click', () => { tl.f = b.dataset.f; renderTimeline(); }));
        $('tlSort').addEventListener('click', () => { tl.sort = tl.sort === 'asc' ? 'desc' : 'asc'; renderTimeline(); });
        const w = $('timeline');
        w.scrollTop = tl.sort === 'asc' ? w.scrollHeight : 0;   // selalu tampilkan sisi event terbaru
    }
    renderTimeline();

    /* DATA PETA: tampil instan dari koordinat yang ada; geocoding lambat jalan di latar belakang */
    let geoTok = 0;
    function renderGeoCards() {
        const tok = ++geoTok;
        const atPos = T.ev.length ? T.ev[T.ev.length - 1].ll : (delivered ? T.d : (S.status === 'CREATED' || S.status === 'BOOKED') ? T.o : T.pos);
        const places = [
            { title:'LOKASI ASAL', icon:'fa-box', color:'#2563eb', name:S.origin, ll:T.o },
            { title:delivered ? 'LOKASI TERAKHIR' : 'LOKASI SAAT INI', icon:'fa-truck-fast', color:'#f59e0b', name:T.last ? (T.last.location || 'Titik tracking terakhir') : (atPos ? (T.exact || delivered ? 'Lokasi terakhir' : 'Perkiraan posisi di sepanjang rute') : null), ll:atPos },
            { title:'LOKASI TUJUAN', icon:'fa-flag-checkered', color:'#16a34a', name:S.destination, ll:T.d }
        ];
        const hd = p => '<div class="wt"><span class="wi" style="background:' + p.color + '"><i class="fa-solid ' + p.icon + '"></i></span>' + p.title + '</div><div class="wn">' + esc(p.name || 'Belum tercatat') + '</div>';
        const miss = p => '<div class="wph">' + (p.name ? 'Koordinat lokasi tidak ditemukan.' : 'Belum ada lokasi yang tercatat.') + '</div>';
        const skel = p => p.ll ? '<div class="skl"></div>' : miss(p);
        $('weatherGrid').innerHTML = places.map((p, n) => '<div class="wc" id="wx' + n + '">' + hd(p) + skel(p) + '</div>').join('');
        $('clockGrid').innerHTML = places.map((p, n) => '<div class="wc dark" id="ck' + n + '">' + hd(p) + skel(p) + '</div>').join('');
        const get = p => p.ll ? infoAt(p.ll[0], p.ll[1]) : Promise.resolve(null);
        const baseP = get(places[0]);
        places.forEach((p, n) => {
            if (!p.ll) return;
            get(p).then(i => { if (tok === geoTok) $('wx' + n).innerHTML = hd(p) + weatherHtml(i); });
            Promise.all([get(p), baseP]).then(([i, b]) => { if (tok === geoTok) $('ck' + n).innerHTML = hd(p) + timeHtml(i, n > 0 && b ? b.off : null, 'lokasi asal'); });
        });
    }

    T = await buildShip(true);
    legend(); drawShip(true); renderGeoCards();
    document.querySelectorAll('#jumpGroup .chip').forEach(b => b.addEventListener('click', () => {
        const j = b.dataset.j;
        if (j === 'all') { if (T.bounds.length) map.fitBounds(T.bounds, { padding:[70,70], maxZoom:12 }); return; }
        const ll = j === 'o' ? T.o : j === 'd' ? T.d : T.pos;
        if (!ll) { $('tmsg').textContent = 'Titik itu belum diketahui.'; return; }
        map.flyTo(ll, 13, { duration:1.1 });
        const nm = j === 'o' ? S.origin : j === 'd' ? S.destination : (T.last && T.last.location) || 'Posisi barang';
        selectPoint(ll[0], ll[1], nm);
    }));
    const finish = () => { $('tmsg').textContent = T.bounds.length ? '' : 'Lokasi pengiriman belum ditemukan di peta, tapi kamu tetap bisa mencari tempat lain.'; };
    const missing = (!T.o && S.origin) || (!T.d && S.destination) || (T.last && (T.last.lat === null || T.last.lng === null) && T.last.location && !T.ev.some(x => x.t === T.last));
    if (missing) {
        $('tmsg').textContent = 'Mencari koordinat lokasi…';
        buildShip(false).then(t => { T = t; legend(); drawShip(true); renderGeoCards(); finish(); });
    } else finish();
})();
</script>
</body>
</html>
