<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pengiriman | CargoTrace</title>
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

/* HERO */
.hero{position:relative;overflow:hidden;display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;padding:34px 38px;margin-bottom:22px;border-radius:28px;color:#fff;
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 55%,#1d6fe870 100%),url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1800&q=80') center/cover;box-shadow:0 24px 50px #0b2a6b44}
.hero::after{content:"";position:absolute;width:380px;height:380px;right:-110px;top:-190px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.crumb{font-size:11px;color:#a9c8f5;margin-bottom:10px}
.hero h1{font:800 29px/1.25 var(--h);letter-spacing:-.8px}
.hero p{margin-top:10px;max-width:520px;line-height:1.75;color:#cfe0ff}
.hero-actions{display:flex;gap:10px;flex-wrap:wrap}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 18px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:12px;cursor:pointer;font:700 12px 'Plus Jakarta Sans',sans-serif;white-space:nowrap;transition:.2s;box-shadow:0 8px 20px #2563eb44}
.btn:hover{filter:brightness(1.1);transform:translateY(-1px)}
.btn-light{background:#e8f1ff;color:#1d4ed8;box-shadow:none}
.btn-glass{background:#ffffff22;border:1px solid #ffffff44;box-shadow:none;backdrop-filter:blur(8px)}
.btn-danger{background:#ffe9e9;color:#c92f3a;box-shadow:none}
.alert{display:flex;gap:10px;align-items:center;padding:13px 17px;margin-bottom:18px;border-radius:14px;background:#e5f7ed;color:#187344;font-weight:600}
.alert.err{background:#ffe9e9;color:#b9302d}

/* ALUR STATUS */
.flow{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:22px}
.step{position:relative;display:block;padding:18px;background:#fff;border:1px solid var(--line);border-radius:20px;text-decoration:none;box-shadow:0 10px 26px #2563eb0d;transition:.25s}
.step:hover{transform:translateY(-4px);border-color:#9fc2f7}
.step.on{border-color:var(--c);box-shadow:0 14px 30px color-mix(in srgb,var(--c) 28%,transparent)}
.step:not(:last-child)::after{content:"\f054";font-family:'Font Awesome 6 Free';font-weight:900;position:absolute;right:-13px;top:50%;transform:translateY(-50%);z-index:2;display:grid;place-items:center;width:22px;height:22px;border-radius:50%;background:#fff;border:1px solid var(--line);color:#9db6dc;font-size:8px}
.step .ic{display:grid;place-items:center;width:42px;height:42px;border-radius:13px;background:color-mix(in srgb,var(--c) 14%,#fff);color:var(--c);font-size:17px;margin-bottom:12px}
.step b{display:block;font:800 26px var(--h);color:var(--navy);line-height:1}
.step strong{display:block;margin-top:8px;font-size:12px;color:var(--navy)}
.step small{display:block;margin-top:4px;font-size:10.5px;color:var(--mut);line-height:1.5}

/* PANEL */
.panel{padding:24px;background:#fff;border:1px solid var(--line);border-radius:26px;box-shadow:0 10px 30px #2563eb0d}
.ph{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:16px}
.ph h2{font:700 17px var(--h);color:var(--navy);letter-spacing:-.3px;display:flex;align-items:center;gap:10px}
.ph h2 .hi{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font-size:14px}
.ph p{margin-top:6px;font-size:12px;color:var(--mut)}
.sect{margin-bottom:24px}
.grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin-bottom:24px}

/* FILTER + TABEL */
.filters{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px}
.filters input,.filters select,.fgrid input,.fgrid select{padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:var(--ink);font:12px 'Plus Jakarta Sans',sans-serif}
.filters input:focus,.filters select:focus,.fgrid input:focus,.fgrid select:focus,.fgrid textarea:focus,.fx input:focus,.fx select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.filters .grow{flex:1;min-width:210px}
details.adv{margin-bottom:16px}
details.adv summary{cursor:pointer;font-weight:700;font-size:11.5px;color:var(--blue);list-style:none;display:inline-flex;gap:7px;align-items:center}
details.adv summary::-webkit-details-marker{display:none}
details.adv .row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-top:12px}
.row label,.fgrid label{display:block;margin-bottom:5px;font-size:10px;font-weight:700;color:#8ca6c4;letter-spacing:.5px}
.row input{width:100%;padding:10px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif}
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:980px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:11px;font-weight:700;padding:13px 14px;white-space:nowrap}
th:first-child{border-radius:12px 0 0 12px}th:last-child{border-radius:0 12px 12px 0}
td{padding:14px;border-bottom:1px solid #edf3fa;color:#41608a;vertical-align:middle}
tbody tr{cursor:pointer;transition:.15s}
tbody tr:hover{background:#f4f9ff}
td small{display:block;margin-top:3px;font-size:10.5px;color:#8ca6c4}
.num{color:var(--blue);font-weight:800}
.route{display:flex;align-items:center;gap:8px;white-space:nowrap}.route i{color:#9db6dc;font-size:10px}
.tmode{display:inline-grid;place-items:center;width:26px;height:26px;border-radius:8px;background:#e8f1ff;color:var(--blue);font-size:11px;margin-right:8px}
.status{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10px;font-weight:700;background:#e5f1ff;color:#2876c5;white-space:nowrap}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.st-CREATED{background:#e8eef7;color:#5b6f90}.st-BOOKED{background:#e0edff;color:#1d4ed8}.st-PICKED_UP{background:#f0eaff;color:#7c3aed}.st-IN_TRANSIT{background:#fff4dc;color:#b77900}.st-DELIVERED{background:#e8f7ef;color:#26734d}
td.empty{text-align:center;padding:50px 20px;color:#8ca6c4}
td.empty i{display:grid;place-items:center;width:58px;height:58px;margin:0 auto 12px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:24px}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-top:16px;font-size:11px;color:#8ca6c4}
.pager nav svg{width:16px;height:16px}.pager nav a,.pager nav span{font-size:12px}

/* PANEL INFO */
.bar{height:8px;border-radius:6px;background:#e9f0fc;overflow:hidden;margin-top:8px}
.bar i{display:block;height:100%;border-radius:6px;background:linear-gradient(90deg,#38bdf8,#1d4ed8)}
.li{padding:13px 0;border-bottom:1px solid #edf3fa}.li:last-child{border:0;padding-bottom:0}.li:first-child{padding-top:0}
.li .t{display:flex;justify-content:space-between;gap:10px;align-items:center}
.li strong{font-size:12.5px;color:var(--navy)}
.li .meta{font-size:11px;color:var(--mut);margin-top:4px}
.pill{padding:4px 10px;border-radius:20px;background:#e8f1ff;color:#1d4ed8;font-size:10px;font-weight:700;white-space:nowrap}
.rt{display:flex;align-items:center;gap:8px;margin-top:9px}
.rt .d{width:10px;height:10px;border-radius:50%;border:3px solid var(--blue);background:#fff;flex:none}.rt .d.e{border-color:#16a34a}
.rt .ln{flex:1;border-top:2px dashed #b9d2f7;position:relative}.rt .ln i{position:absolute;left:50%;top:-9px;transform:translateX(-50%);background:#fff;padding:0 6px;color:var(--blue);font-size:10px}
.rt span{font-size:11.5px;font-weight:600;color:var(--navy);max-width:34%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.tot{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px}
.tot div{padding:14px;border-radius:16px;background:linear-gradient(135deg,#eaf3ff,#dbeaff)}
.tot b{display:block;font:800 21px var(--h);color:var(--navy)}.tot small{font-size:11px;color:var(--mut);font-weight:600}
.sch{display:flex;gap:14px;padding:12px 0;border-bottom:1px solid #edf3fa}.sch:last-child{border:0;padding-bottom:0}.sch:first-child{padding-top:0}
.cal{flex:none;width:52px;text-align:center;border-radius:14px;overflow:hidden;border:1px solid var(--line)}
.cal em{display:block;background:var(--blue);color:#fff;font-style:normal;font-size:9px;font-weight:700;padding:3px 0;letter-spacing:.6px}
.cal b{display:block;font:800 19px var(--h);padding:5px 0;color:var(--navy)}
.nodata{padding:26px 10px;text-align:center;color:#8ca6c4;line-height:1.7}

/* KURS */
.fx-wrap{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.3fr);overflow:hidden;padding:0}
.fx-calc{padding:28px;color:#fff;background:linear-gradient(160deg,#061533,#0b2a6b 65%,#1747b8);position:relative}
.fx-calc::after{content:"";position:absolute;width:260px;height:260px;right:-90px;top:-100px;border-radius:50%;background:radial-gradient(#7cc8ff44,#0000 70%)}
.fx-calc>*{position:relative;z-index:2}
.fx-calc h2{font:700 18px var(--h);display:flex;align-items:center;gap:10px}
.fx-calc p.s{margin:6px 0 20px;font-size:12px;color:#9fbcea}
.fx label{display:block;margin:12px 0 6px;font-size:10px;font-weight:700;letter-spacing:.6px;color:#9fbcea}
.fx input,.fx select{width:100%;padding:13px 14px;border:1px solid #ffffff33;border-radius:12px;background:#ffffff14;color:#fff;font:600 14px 'Plus Jakarta Sans',sans-serif}
.fx select option{color:#12306a}
.fx .pair{display:grid;grid-template-columns:1fr auto 1fr;gap:10px;align-items:end}
.swap{display:grid;place-items:center;width:42px;height:46px;border:1px solid #ffffff33;border-radius:12px;background:#ffffff1c;color:#fff;cursor:pointer;font-size:14px}
.swap:hover{background:#ffffff33}
.fx-res{margin-top:20px;padding:18px;border-radius:18px;background:#ffffff16;border:1px solid #ffffff2b}
.fx-res small{font-size:11px;color:#a9c4f0}
.fx-res b{display:block;margin-top:6px;font:800 27px/1.2 var(--h);overflow-wrap:anywhere}
.fx-res em{display:block;margin-top:8px;font-style:normal;font-size:11.5px;color:#cfe0ff}
.fx-list{padding:28px;min-width:0}
.fx-top{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px}
.fx-top h3{font:700 16px var(--h);color:var(--navy)}
.fx-top small{display:block;margin-top:5px;color:var(--mut);font-size:11px}
.fx-search{position:relative;flex:1;min-width:190px;max-width:290px}
.fx-search i{position:absolute;left:13px;top:12px;font-size:12px;color:#8ca6c4}
.fx-search input{width:100%;padding:10px 12px 10px 36px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif}
.fx-search input:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.fx-feat{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin-bottom:14px}
.fx-card{padding:13px;border-radius:16px;border:1px solid var(--line);background:linear-gradient(160deg,#fff,#f2f7ff);cursor:pointer;transition:.2s;text-align:left;font-family:inherit}
.fx-card:hover{border-color:#9fc2f7;transform:translateY(-2px)}
.fx-card b{display:block;font:800 13px var(--h);color:var(--navy)}
.fx-card span{display:block;margin-top:6px;font-size:12px;font-weight:700;color:var(--blue)}
.fx-rows{max-height:318px;overflow-y:auto;padding-right:4px}
.fx-row{display:flex;align-items:center;gap:12px;padding:11px 8px;border-bottom:1px solid #edf3fa;cursor:pointer;border-radius:10px}
.fx-row:hover{background:#f4f9ff}
.fx-code{flex:none;display:grid;place-items:center;width:42px;height:34px;border-radius:10px;background:#e8f1ff;color:#1d4ed8;font:800 11px var(--h)}
.fx-n{flex:1;min-width:0}.fx-n strong{display:block;font-size:12.5px;color:var(--navy)}.fx-n small{display:block;margin-top:2px;font-size:10.5px;color:#8ca6c4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.fx-v{text-align:right;flex:none}.fx-v b{display:block;font-size:12.5px;color:var(--navy)}
.chg{display:inline-block;margin-top:3px;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px}
.chg.up{background:#e8f7ef;color:#26734d}.chg.dn{background:#ffe9e9;color:#c92f3a}.chg.na{background:#eef2f9;color:#8ca6c4}
.fx-state{padding:34px 10px;text-align:center;color:#8ca6c4;line-height:1.7}.fx-state .btn{margin-top:10px}
.skel{height:44px;margin-bottom:8px;border-radius:12px;background:linear-gradient(90deg,#edf3fc,#f8fbff,#edf3fc);background-size:200% 100%;animation:sk 1.3s infinite}
@keyframes sk{to{background-position:-200% 0}}

/* DRAWER */
.veil{position:fixed;inset:0;background:#061533a0;backdrop-filter:blur(3px);opacity:0;pointer-events:none;transition:.25s;z-index:1900}
.veil.open{opacity:1;pointer-events:auto}
.drawer{position:fixed;top:0;right:0;bottom:0;width:min(480px,100%);background:#fff;z-index:2000;transform:translateX(105%);transition:.3s;display:flex;flex-direction:column;box-shadow:-20px 0 50px #0b2a6b33}
.drawer.open{transform:none}
.dr-head{padding:24px 26px;color:#fff;background:linear-gradient(120deg,#061533,#1747b8)}
.dr-head .x{float:right;display:grid;place-items:center;width:34px;height:34px;border:1px solid #ffffff44;border-radius:10px;background:#ffffff1c;color:#fff;cursor:pointer}
.dr-head h3{font:800 20px var(--h);letter-spacing:-.4px}.dr-head p{margin-top:6px;font-size:12px;color:#bcd3f7}
.dr-body{flex:1;overflow-y:auto;padding:22px 26px}
.dr-sec{margin-bottom:22px}
.dr-sec h4{font-size:10.5px;letter-spacing:1px;color:#8ca6c4;margin-bottom:10px;display:flex;align-items:center;gap:8px}
.dr-sec h4 i{color:var(--blue)}
.kv{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.kv div{padding:12px 14px;border-radius:14px;background:#f4f8ff}.kv small{display:block;font-size:10px;color:#8ca6c4;font-weight:600}.kv b{display:block;margin-top:4px;font-size:12.5px;color:var(--navy);overflow-wrap:anywhere}
.kv .full{grid-column:1/-1}
.prog{display:flex;align-items:flex-start}
.pg{flex:1;text-align:center;position:relative}
.pg::before{content:"";position:absolute;top:13px;left:-50%;width:100%;height:3px;background:#e1eafa}
.pg:first-child::before{display:none}
.pg.done::before{background:var(--blue)}
.pg i{position:relative;z-index:2;display:grid;place-items:center;width:28px;height:28px;margin:0 auto;border-radius:50%;background:#e1eafa;color:#fff;font-size:11px}
.pg.done i{background:var(--blue)}.pg.now i{background:#fff;border:3px solid var(--blue);box-shadow:0 0 0 5px #2563eb22}
.pg small{display:block;margin-top:7px;font-size:9.5px;font-weight:600;color:#8ca6c4}.pg.now small,.pg.done small{color:var(--navy)}
.dr-foot{display:flex;gap:8px;flex-wrap:wrap;padding:16px 26px;border-top:1px solid var(--line)}
.dr-foot .btn{flex:1;padding:11px 10px}

/* MODAL */
.modal{position:fixed;inset:0;z-index:2100;display:none;place-items:center;padding:20px;background:#061533a8;backdrop-filter:blur(4px);overflow-y:auto}
.modal.open{display:grid}
.mbox{width:min(760px,100%);background:#fff;border-radius:26px;overflow:hidden;box-shadow:0 30px 70px #0b2a6b55;margin:auto}
.mhead{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:22px 28px;color:#fff;background:linear-gradient(120deg,#061533,#1747b8)}
.mhead h3{font:800 19px var(--h)}.mhead p{margin-top:5px;font-size:12px;color:#bcd3f7}
.mhead .x{display:grid;place-items:center;width:36px;height:36px;border:1px solid #ffffff44;border-radius:10px;background:#ffffff1c;color:#fff;cursor:pointer}
.fgrid{display:grid;grid-template-columns:1fr 1fr;gap:14px;padding:26px 28px 8px}
.fgrid .full{grid-column:1/-1}
.fgrid input,.fgrid select,.fgrid textarea{width:100%;padding:12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:var(--ink);font:13px 'Plus Jakarta Sans',sans-serif}
.fgrid textarea{min-height:80px;resize:vertical}
.fsec{grid-column:1/-1;font-size:11px;font-weight:800;letter-spacing:1px;color:var(--blue);padding-top:6px;display:flex;align-items:center;gap:8px}
.ferr{color:#c92f3a;font-size:11px;margin-top:4px}
.mfoot{display:flex;justify-content:flex-end;gap:10px;padding:18px 28px 26px}

/* AKSI BARIS, TAB, TRACKING */
.acts{display:flex;gap:6px}
.ab{display:grid;place-items:center;width:32px;height:32px;border:0;border-radius:10px;background:#e8f1ff;color:#1d4ed8;cursor:pointer;font-size:12px;transition:.15s}
.ab:hover{background:#1d4ed8;color:#fff}.ab.del{background:#ffe9e9;color:#c92f3a}.ab.del:hover{background:#c92f3a;color:#fff}
.tabs{display:flex;gap:6px;margin:16px -26px -24px;padding:0 26px}
.tab{padding:11px 18px;border:0;border-radius:12px 12px 0 0;background:transparent;color:#bcd3f7;font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.tab.on{background:#fff;color:var(--navy)}
.tmap{height:210px;border-radius:16px;overflow:hidden;background:#dfeafb;margin-bottom:18px;position:relative;z-index:0}
.tl{position:relative;padding-left:26px}
.tl::before{content:"";position:absolute;left:8px;top:6px;bottom:6px;width:2px;background:#dbe6f8}
.ti{position:relative;padding-bottom:18px}.ti:last-child{padding-bottom:0}
.ti::before{content:"";position:absolute;left:-23px;top:3px;width:12px;height:12px;border-radius:50%;background:#fff;border:3px solid #b9d2f7}
.ti.last::before{border-color:var(--blue);box-shadow:0 0 0 5px #2563eb22}
.ti b{display:block;font-size:12.5px;color:var(--navy)}.ti span{display:block;margin-top:3px;font-size:11.5px;color:#41608a}.ti small{display:block;margin-top:3px;font-size:10.5px;color:#8ca6c4;line-height:1.5}
.lastloc{display:flex;gap:12px;align-items:center;padding:14px;border-radius:16px;background:linear-gradient(135deg,#eaf3ff,#dbeaff);margin-bottom:16px}
.lastloc i{display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:var(--blue);color:#fff;flex:none}
.lastloc b{display:block;font-size:13px;color:var(--navy)}.lastloc small{font-size:11px;color:var(--mut)}

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
@media(max-width:1250px){.flow{grid-template-columns:repeat(3,minmax(0,1fr))}.step::after{display:none!important}.fx-wrap{grid-template-columns:1fr}}
@media(max-width:1100px){.grid-2{grid-template-columns:1fr}.content{padding:20px}details.adv .row{grid-template-columns:1fr 1fr}.fx-feat{grid-template-columns:1fr 1fr}}
@media(max-width:750px){.main{margin-left:72px}.content{padding:16px 12px 30px}.hero{padding:26px 22px}.hero h1{font-size:22px}.flow{grid-template-columns:1fr 1fr}.panel{padding:18px}.fgrid{grid-template-columns:1fr}.fx-calc,.fx-list{padding:20px}.fx .pair{grid-template-columns:1fr}.swap{width:100%;height:38px}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>
<body>

@php
    use Carbon\Carbon;
    $rt = fn ($name) => Route::has($name) ? route($name) : '#';
    $fmt = fn ($v, $f = 'd M Y H:i') => $v ? Carbon::parse($v)->format($f) : '-';
    $modeIcon = ['udara' => 'fa-plane', 'laut' => 'fa-ship', 'darat' => 'fa-truck'];
    $flow = [
        ['CREATED',    'Created',    'fa-file-circle-plus', '#64748b', 'Pengiriman baru dibuat.'],
        ['BOOKED',     'Booked',     'fa-calendar-check',   '#2563eb', 'Pengiriman sudah dijadwalkan.'],
        ['PICKED_UP',  'Picked Up',  'fa-hand-holding-box', '#7c3aed', 'Barang sudah dijemput.'],
        ['IN_TRANSIT', 'In Transit', 'fa-truck-fast',       '#f59e0b', 'Barang sedang dalam perjalanan.'],
        ['DELIVERED',  'Delivered',  'fa-circle-check',     '#16a34a', 'Barang sudah diterima.'],
    ];
    $totalAll = max((int) $statusCounts->sum(), 0);
    $rows = $shipments->getCollection()->map(fn ($s) => [
        'id' => $s->id, 'number' => $s->shipment_number, 'name' => $s->shipment_name, 'customer' => $s->customer_name,
        'cargo' => $s->cargo_type, 'weight' => $s->weight, 'volume' => $s->volume, 'carrier' => $s->carrier,
        'mode' => $s->transport_type, 'origin' => $s->origin, 'destination' => $s->destination,
        'etd' => $fmt($s->estimated_departure), 'eta' => $fmt($s->estimated_arrival),
        'days' => ($s->estimated_departure && $s->estimated_arrival) ? max(0, (int) Carbon::parse($s->estimated_departure)->diffInDays(Carbon::parse($s->estimated_arrival))) : null,
        'status' => $s->status, 'notes' => $s->notes,
        'etd_raw' => $s->estimated_departure ? Carbon::parse($s->estimated_departure)->format('Y-m-d\TH:i') : '',
        'eta_raw' => $s->estimated_arrival ? Carbon::parse($s->estimated_arrival)->format('Y-m-d\TH:i') : '',
        'tracks' => $s->relationLoaded('trackingEvents') ? $s->trackingEvents->map(fn ($t) => [
            'status' => $t->status, 'location' => $t->location, 'desc' => $t->description,
            'time' => $t->event_time ? Carbon::parse($t->event_time)->format('d M Y H:i') : '-',
            'lat' => is_numeric($t->latitude) ? (float) $t->latitude : null,
            'lng' => is_numeric($t->longitude) ? (float) $t->longitude : null,
            'by' => $t->updated_by,
        ])->values() : [],
    ])->values();
    $formMode = $errors->any() ? old('_modal') : null;   // create | edit
    $openModal = in_array($formMode, ['create', 'edit'], true);
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

    <a href="{{ route('pengiriman.index') }}" class="ct-link on">
        <i class="fa-solid fa-box"></i>
        <span>Pengiriman</span>
    </a>

    <a href="{{ route('pelacakan.index') }}" class="ct-link">
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

    <!-- HERO -->
    <section class="hero">
        <div>
            <div class="crumb"><a href="{{ route('dashboard') }}" style="text-decoration:none">Dashboard</a> &nbsp;/&nbsp; Pengiriman</div>
            <h1>Pengelolaan Pengiriman</h1>
            <p>Kelola seluruh data pengiriman, pantau tahapan perjalanan barang, dan lihat kurs mata uang dalam satu halaman.</p>
        </div>
        <div class="hero-actions">
            <a href="#kurs" class="btn btn-glass"><i class="fa-solid fa-money-bill-transfer"></i> Kurs Mata Uang</a>
            <button type="button" class="btn" id="openCreate"><i class="fa-solid fa-plus"></i> Tambah Pengiriman</button>
        </div>
    </section>

    @if(session('success'))<div class="alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if($errors->any() && !$openModal)<div class="alert err"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <!-- 7. STATUS PENGIRIMAN -->
    <section class="flow">
        @foreach($flow as [$key, $label, $icon, $color, $desc])
            <a href="{{ route('pengiriman.index', ['status' => $key]) }}#daftar" class="step {{ request('status') === $key ? 'on' : '' }}" style="--c:{{ $color }}">
                <div class="ic"><i class="fa-solid {{ $icon }}"></i></div>
                <b>{{ (int) ($statusCounts[$key] ?? 0) }}</b>
                <strong>{{ $label }}</strong>
                <small>{{ $desc }}</small>
            </a>
        @endforeach
    </section>

    <!-- 1. DAFTAR PENGIRIMAN -->
    <section class="panel sect" id="daftar">
        <div class="ph">
            <div>
                <h2><span class="hi"><i class="fa-solid fa-list"></i></span> Daftar Pengiriman</h2>
                <p>{{ $totalAll }} pengiriman tersimpan. Klik baris untuk melihat detail lengkap.</p>
            </div>
            <button type="button" class="btn" id="openCreate2"><i class="fa-solid fa-plus"></i> Tambah</button>
        </div>

        <form action="{{ route('pengiriman.index') }}#daftar" method="GET">
            <div class="filters">
                <input class="grow" type="text" name="search" placeholder="Cari nomor resi, pelanggan, atau nama barang..." value="{{ request('search') }}" aria-label="Pencarian">
                <select name="status" aria-label="Status"><option value="">Semua status</option>
                    @foreach($flow as [$key, $label])<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach
                </select>
                <select name="carrier" aria-label="Carrier"><option value="">Semua perusahaan</option>
                    @foreach($carriers as $c)<option value="{{ $c }}" @selected(request('carrier') === $c)>{{ $c }}</option>@endforeach
                </select>
                <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                <a href="{{ route('pengiriman.index') }}#daftar" class="btn btn-light">Reset</a>
            </div>
            <details class="adv" @if(request()->hasAny(['origin','destination','start_date','end_date'])) open @endif>
                <summary><i class="fa-solid fa-sliders"></i> Filter lanjutan (asal, tujuan, tanggal)</summary>
                <div class="row">
                    <div><label for="origin">LOKASI ASAL</label><input id="origin" type="text" name="origin" value="{{ request('origin') }}" placeholder="Cari asal"></div>
                    <div><label for="destination">LOKASI TUJUAN</label><input id="destination" type="text" name="destination" value="{{ request('destination') }}" placeholder="Cari tujuan"></div>
                    <div><label for="start_date">TANGGAL MULAI</label><input id="start_date" type="date" name="start_date" value="{{ request('start_date') }}"></div>
                    <div><label for="end_date">TANGGAL AKHIR</label><input id="end_date" type="date" name="end_date" value="{{ request('end_date') }}"></div>
                </div>
            </details>
        </form>

        <div class="table-wrap">
            <table>
                <thead><tr><th>No. Resi</th><th>Pelanggan</th><th>Perusahaan</th><th>Asal → Tujuan</th><th>Barang</th><th>Jadwal</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($shipments as $i => $s)
                    <tr data-i="{{ $i }}" tabindex="0">
                        <td><span class="num">{{ $s->shipment_number }}</span><small>{{ $s->shipment_name }}</small></td>
                        <td>{{ $s->customer_name }}</td>
                        <td><span class="tmode"><i class="fa-solid {{ $modeIcon[$s->transport_type] ?? 'fa-box' }}"></i></span>{{ $s->carrier }}</td>
                        <td><div class="route">{{ $s->origin }} <i class="fa-solid fa-arrow-right"></i> {{ $s->destination }}</div></td>
                        <td>{{ $s->cargo_type }}<small>{{ rtrim(rtrim(number_format((float) $s->weight, 2, ',', '.'), '0'), ',') }} kg</small></td>
                        <td>{{ $fmt($s->estimated_departure, 'd M') }} → {{ $fmt($s->estimated_arrival, 'd M Y') }}</td>
                        <td><span class="status st-{{ $s->status }}">{{ str_replace('_', ' ', $s->status) }}</span></td>
                        <td><div class="acts">
                            <button type="button" class="ab" data-act="track" data-i="{{ $i }}" title="Lacak" aria-label="Lacak"><i class="fa-solid fa-location-dot"></i></button>
                            <button type="button" class="ab" data-act="edit" data-i="{{ $i }}" title="Edit" aria-label="Edit"><i class="fa-solid fa-pen"></i></button>
                            <button type="button" class="ab del" data-act="del" data-i="{{ $i }}" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></button>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty"><i class="fa-solid fa-box-open"></i><strong style="color:var(--navy)">Belum ada pengiriman</strong><br>Ubah filter atau tambahkan pengiriman pertama kamu.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pager">
            <span>@if($shipments->total() > 0)Menampilkan {{ $shipments->firstItem() }}–{{ $shipments->lastItem() }} dari {{ $shipments->total() }} pengiriman@else Belum ada data pengiriman @endif</span>
            @if($shipments->hasPages())<div>{{ $shipments->links() }}</div>@endif
        </div>
    </section>

    <!-- 3-6. PERUSAHAAN, ASAL-TUJUAN, BARANG, JADWAL -->
    <section class="grid-2">
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-building"></i></span> Perusahaan Pengiriman</h2><p>Perusahaan logistik yang menangani pengiriman.</p></div></div>
            @forelse($carrierStats as $c)
                <div class="li">
                    <div class="t"><strong>{{ $c->carrier }}</strong><span class="pill">{{ $c->total }} pengiriman</span></div>
                    <div class="meta">{{ (int) $c->in_transit }} dalam perjalanan · {{ (int) $c->delivered }} terkirim</div>
                    <div class="bar"><i style="width:{{ $totalAll > 0 ? round($c->total / $totalAll * 100) : 0 }}%"></i></div>
                </div>
            @empty<div class="nodata">Belum ada data perusahaan pengiriman.</div>@endforelse
        </div>

        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-route"></i></span> Asal dan Tujuan</h2><p>Rute pengiriman yang paling sering digunakan.</p></div></div>
            @forelse($topRoutes as $r)
                <div class="li">
                    <div class="t"><strong>{{ $r->origin }} → {{ $r->destination }}</strong><span class="pill">{{ $r->total }}×</span></div>
                    <div class="rt"><i class="d"></i><span>{{ $r->origin }}</span><div class="ln"><i class="fa-solid fa-truck-fast"></i></div><span style="text-align:right">{{ $r->destination }}</span><i class="d e"></i></div>
                </div>
            @empty<div class="nodata">Belum ada data rute.</div>@endforelse
        </div>

        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-boxes-stacked"></i></span> Informasi Barang</h2><p>Jenis barang, berat, dan volume yang dikirim.</p></div></div>
            <div class="tot">
                <div><b>{{ number_format($totalWeight, 0, ',', '.') }} kg</b><small>Total berat</small></div>
                <div><b>{{ number_format($totalVolume, 1, ',', '.') }} m³</b><small>Total volume</small></div>
            </div>
            @php $maxW = max((float) $cargoStats->max('weight'), 1); @endphp
            @forelse($cargoStats as $c)
                <div class="li">
                    <div class="t"><strong>{{ $c->cargo_type }}</strong><span class="pill">{{ $c->total }} item</span></div>
                    <div class="meta">{{ number_format((float) $c->weight, 0, ',', '.') }} kg · {{ number_format((float) $c->volume, 1, ',', '.') }} m³</div>
                    <div class="bar"><i style="width:{{ round($c->weight / $maxW * 100) }}%"></i></div>
                </div>
            @empty<div class="nodata">Belum ada data barang.</div>@endforelse
        </div>

        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-calendar-days"></i></span> Jadwal Pengiriman</h2><p>Keberangkatan terdekat yang belum berangkat.</p></div></div>
            @forelse($upcoming as $u)
                @php $d = Carbon::parse($u->estimated_departure); @endphp
                <div class="sch">
                    <div class="cal"><em>{{ strtoupper($d->translatedFormat('M')) }}</em><b>{{ $d->format('d') }}</b></div>
                    <div style="flex:1;min-width:0">
                        <div class="t" style="display:flex;justify-content:space-between;gap:8px"><strong style="font-size:12.5px;color:var(--navy)">{{ $u->shipment_number }}</strong><span class="status st-{{ $u->status }}">{{ $u->status }}</span></div>
                        <div class="meta" style="font-size:11px;color:var(--mut);margin-top:5px">{{ $u->origin }} → {{ $u->destination }}</div>
                        <div class="meta" style="font-size:11px;color:#8ca6c4;margin-top:3px">Berangkat {{ $d->format('H:i') }} · Tiba {{ $fmt($u->estimated_arrival, 'd M Y H:i') }}</div>
                    </div>
                </div>
            @empty<div class="nodata">Tidak ada jadwal keberangkatan yang menunggu.</div>@endforelse
        </div>
    </section>

    <!-- 8. KURS MATA UANG -->
    <section class="panel fx-wrap sect" id="kurs">
        <div class="fx-calc fx">
            <h2><i class="fa-solid fa-money-bill-transfer"></i> Kalkulator Kurs</h2>
            <p class="s">Konversi Rupiah ke mata uang asing, atau sebaliknya.</p>
            <label for="fxAmt">JUMLAH</label>
            <input id="fxAmt" type="number" min="0" step="any" value="1" inputmode="decimal">
            <div class="pair">
                <div><label for="fxFrom">DARI</label><select id="fxFrom"></select></div>
                <button type="button" class="swap" id="fxSwap" aria-label="Tukar mata uang"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
                <div><label for="fxTo">KE</label><select id="fxTo"></select></div>
            </div>
            <div class="fx-res">
                <small id="fxLine">Memuat kurs…</small>
                <b id="fxOut">—</b>
                <em id="fxRate"></em>
            </div>
        </div>
        <div class="fx-list">
            <div class="fx-top">
                <div><h3>Nilai Tukar terhadap Rupiah</h3><small id="fxMeta">Memuat data…</small></div>
                <div class="fx-search"><i class="fa-solid fa-magnifying-glass"></i><input id="fxQ" type="text" placeholder="Cari negara atau kode, mis. USD" aria-label="Cari mata uang"></div>
                <button type="button" class="btn btn-light" id="fxRefresh"><i class="fa-solid fa-rotate"></i></button>
            </div>
            <div class="fx-feat" id="fxFeat"></div>
            <div class="fx-rows" id="fxRows"><div class="skel"></div><div class="skel"></div><div class="skel"></div><div class="skel"></div></div>
        </div>
    </section>

</section>
</main>

<!-- DRAWER DETAIL -->
<div class="veil" id="veil"></div>
<aside class="drawer" id="drawer" aria-hidden="true">
    <div class="dr-head"><button type="button" class="x" id="drClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button><h3 id="drNum">—</h3><p id="drSub"></p>
        <div class="tabs"><button type="button" class="tab on" data-t="detail">Detail</button><button type="button" class="tab" data-t="track">Lacak</button></div>
    </div>
    <div class="dr-body" id="drBody"></div>
    <div class="dr-foot" id="drFoot"></div>
</aside>

<!-- MODAL TAMBAH -->
<div class="modal {{ $openModal ? 'open' : '' }}" id="modal">
    <div class="mbox" role="dialog" aria-modal="true" aria-labelledby="mTitle">
        <div class="mhead">
            <div><h3 id="mTitle">Tambah Pengiriman</h3><p id="mSub">Isi data pelanggan, barang, rute, dan jadwal.</p></div>
            <button type="button" class="x" id="mClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('pengiriman.store') }}" id="shipForm">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="mMethod" @if($formMode !== 'edit') disabled @endif>
            <input type="hidden" name="_modal" value="{{ $formMode === 'edit' ? 'edit' : 'create' }}" id="mMode">
            <input type="hidden" name="_id" value="{{ old('_id') }}" id="mId">
            <div class="fgrid">
                @php $err = fn ($f) => $openModal && $errors->has($f) ? '<div class="ferr">' . e($errors->first($f)) . '</div>' : ''; @endphp
                <div class="fsec"><i class="fa-solid fa-user"></i> PELANGGAN</div>
                <div><label for="f_no">NO. RESI</label><input id="f_no" name="shipment_number" value="{{ old('shipment_number', $nextNumber) }}" required>{!! $err('shipment_number') !!}</div>
                <div><label for="f_cu">NAMA PELANGGAN</label><input id="f_cu" name="customer_name" value="{{ old('customer_name') }}" required>{!! $err('customer_name') !!}</div>

                <div class="fsec"><i class="fa-solid fa-box"></i> BARANG</div>
                <div><label for="f_sn">NAMA BARANG</label><input id="f_sn" name="shipment_name" value="{{ old('shipment_name') }}" required>{!! $err('shipment_name') !!}</div>
                <div><label for="f_ct">JENIS BARANG</label><input id="f_ct" name="cargo_type" value="{{ old('cargo_type') }}" placeholder="Elektronik, Makanan, ..." required>{!! $err('cargo_type') !!}</div>
                <div><label for="f_w">BERAT (KG)</label><input id="f_w" type="number" step="0.01" min="0.01" name="weight" value="{{ old('weight') }}" required>{!! $err('weight') !!}</div>
                <div><label for="f_v">VOLUME (M³)</label><input id="f_v" type="number" step="0.01" min="0" name="volume" value="{{ old('volume') }}">{!! $err('volume') !!}</div>

                <div class="fsec"><i class="fa-solid fa-truck-fast"></i> PERUSAHAAN &amp; RUTE</div>
                <div><label for="f_ca">PERUSAHAAN PENGIRIMAN</label><input id="f_ca" name="carrier" list="carrierList" value="{{ old('carrier') }}" required>
                    <datalist id="carrierList">@foreach($carriers as $c)<option value="{{ $c }}">@endforeach</datalist>{!! $err('carrier') !!}</div>
                <div><label for="f_tt">MODA TRANSPORT</label>
                    <select id="f_tt" name="transport_type" required>
                        @foreach(['darat' => 'Darat', 'laut' => 'Laut', 'udara' => 'Udara'] as $v => $l)<option value="{{ $v }}" @selected(old('transport_type', 'darat') === $v)>{{ $l }}</option>@endforeach
                    </select>{!! $err('transport_type') !!}</div>
                <div><label for="f_or">ALAMAT / KOTA ASAL</label><input id="f_or" name="origin" value="{{ old('origin') }}" required>{!! $err('origin') !!}</div>
                <div><label for="f_de">ALAMAT / KOTA TUJUAN</label><input id="f_de" name="destination" value="{{ old('destination') }}" required>{!! $err('destination') !!}</div>

                <div class="fsec"><i class="fa-solid fa-calendar-days"></i> JADWAL &amp; STATUS</div>
                <div><label for="f_ed">KEBERANGKATAN</label><input id="f_ed" type="datetime-local" name="estimated_departure" value="{{ old('estimated_departure') }}" required>{!! $err('estimated_departure') !!}</div>
                <div><label for="f_ea">ESTIMASI TIBA</label><input id="f_ea" type="datetime-local" name="estimated_arrival" value="{{ old('estimated_arrival') }}" required>{!! $err('estimated_arrival') !!}</div>
                <div><label for="f_st">STATUS</label>
                    <select id="f_st" name="status" required>
                        @foreach($flow as [$key, $label])<option value="{{ $key }}" @selected(old('status', 'CREATED') === $key)>{{ $label }}</option>@endforeach
                    </select>{!! $err('status') !!}</div>
                <div class="full"><label for="f_nt">CATATAN (OPSIONAL)</label><textarea id="f_nt" name="notes">{{ old('notes') }}</textarea></div>
            </div>
            <div class="mfoot">
                <button type="button" class="btn btn-light" id="mCancel">Batal</button>
                <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> <span id="mSaveTx">{{ $formMode === 'edit' ? 'Simpan Perubahan' : 'Simpan Pengiriman' }}</span></button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));

/* ===== DATA & HELPER ===== */
const ROWS = @json($rows), BASE = @json(url('/pengiriman')), CSRF = @json(csrf_token()), STORE = @json(route('pengiriman.store'));
const FORM = { mode: @json($formMode), id: @json(old('_id')) };
const FLOW = [['CREATED','Created','fa-file-circle-plus'],['BOOKED','Booked','fa-calendar-check'],['PICKED_UP','Picked','fa-hand-holding-box'],['IN_TRANSIT','Transit','fa-truck-fast'],['DELIVERED','Delivered','fa-check']];
const MODE = { udara:'Udara (pesawat)', laut:'Laut (kapal)', darat:'Darat (truk)' };
const nice = v => esc(String(v || '').replace(/_/g, ' '));

/* ===== MODAL TAMBAH / EDIT ===== */
const modal = $('modal'), shipForm = $('shipForm');
function setForm(mode, s) {
    $('mMode').value = mode; $('mId').value = s ? s.id : '';
    $('mMethod').disabled = mode !== 'edit';
    shipForm.action = mode === 'edit' ? BASE + '/' + encodeURIComponent(s.id) : STORE;
    $('mTitle').textContent = mode === 'edit' ? 'Edit Pengiriman' : 'Tambah Pengiriman';
    $('mSub').textContent = mode === 'edit' ? 'Perbarui data pengiriman ' + (s.number || '') + '.' : 'Isi data pelanggan, barang, rute, dan jadwal.';
    $('mSaveTx').textContent = mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Pengiriman';
}
function openCreate() {
    shipForm.reset(); shipForm.querySelectorAll('.ferr').forEach(e => e.remove());
    setForm('create'); modal.classList.add('open'); setTimeout(() => $('f_cu').focus(), 50);
}
function openEdit(s) {
    if (!s) return;
    shipForm.reset(); shipForm.querySelectorAll('.ferr').forEach(e => e.remove()); setForm('edit', s);
    const set = (id, v) => { $(id).value = v ?? ''; };
    set('f_no', s.number); set('f_cu', s.customer); set('f_sn', s.name); set('f_ct', s.cargo); set('f_w', s.weight); set('f_v', s.volume);
    set('f_ca', s.carrier); set('f_tt', s.mode); set('f_or', s.origin); set('f_de', s.destination);
    set('f_ed', s.etd_raw); set('f_ea', s.eta_raw); set('f_st', s.status); set('f_nt', s.notes);
    closeDrawer(); modal.classList.add('open'); setTimeout(() => $('f_cu').focus(), 50);
}
const closeModal = () => modal.classList.remove('open');
['openCreate', 'openCreate2'].forEach(id => $(id).addEventListener('click', openCreate));
['mClose', 'mCancel'].forEach(id => $(id).addEventListener('click', closeModal));
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
if (FORM.mode === 'edit' && FORM.id) setForm('edit', { id: FORM.id, number: '' });   // buka ulang setelah validasi gagal

/* ===== HAPUS ===== */
function delShip(i) {
    const s = ROWS[i];
    if (!s || !confirm('Hapus pengiriman ' + s.number + '? Data yang dihapus tidak dapat dikembalikan.')) return;
    const f = document.createElement('form');
    f.method = 'POST'; f.action = BASE + '/' + encodeURIComponent(s.id);
    f.innerHTML = '<input type="hidden" name="_token" value="' + CSRF + '"><input type="hidden" name="_method" value="DELETE">';
    document.body.appendChild(f); f.submit();
}

/* ===== DRAWER: DETAIL + LACAK ===== */
const dr = $('drawer'), veil = $('veil');
let cur = null, tmap = null;
const kv = (k, v, full) => '<div' + (full ? ' class="full"' : '') + '><small>' + k + '</small><b>' + (v === null || v === '' || v === undefined ? '-' : esc(v)) + '</b></div>';

function killMap() { if (tmap) { tmap.remove(); tmap = null; } }
function closeDrawer() { dr.classList.remove('open'); veil.classList.remove('open'); dr.setAttribute('aria-hidden', 'true'); killMap(); }

function tabDetail(s) {
    const idx = FLOW.findIndex(f => f[0] === s.status);
    return '<div class="dr-sec"><h4><i class="fa-solid fa-signal"></i> STATUS PENGIRIMAN</h4><div class="prog">' +
            FLOW.map((f, n) => '<div class="pg ' + (n < idx ? 'done' : n === idx ? 'now' : '') + '"><i class="fa-solid ' + (n < idx ? 'fa-check' : f[2]) + '"></i><small>' + f[1] + '</small></div>').join('') +
        '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-building"></i> PERUSAHAAN PENGIRIMAN</h4><div class="kv">' + kv('Perusahaan', s.carrier) + kv('Moda transport', MODE[s.mode] || s.mode) + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-route"></i> ASAL DAN TUJUAN</h4><div class="kv">' + kv('Asal', s.origin) + kv('Tujuan', s.destination) + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-box"></i> INFORMASI BARANG</h4><div class="kv">' + kv('Nama barang', s.name) + kv('Jenis barang', s.cargo) +
            kv('Berat', s.weight != null ? Number(s.weight).toLocaleString('id-ID') + ' kg' : '-') + kv('Volume', s.volume != null ? Number(s.volume).toLocaleString('id-ID') + ' m³' : '-') + kv('Deskripsi / catatan', s.notes, true) + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-calendar-days"></i> JADWAL PENGIRIMAN</h4><div class="kv">' + kv('Keberangkatan', s.etd) + kv('Estimasi tiba', s.eta) + (s.days !== null ? kv('Estimasi durasi', s.days + ' hari', true) : '') + '</div></div>';
}

function tabTrack(s) {
    const t = s.tracks || [];
    if (!t.length) return '<div class="nodata"><i class="fa-solid fa-location-crosshairs" style="font-size:30px;color:#9db6dc"></i><br><br>Belum ada riwayat tracking untuk pengiriman ini.</div>';
    const last = t[t.length - 1], hasPts = t.some(x => x.lat !== null && x.lng !== null);
    return '<div class="lastloc"><i class="fa-solid fa-location-dot"></i><div><b>' + esc(last.location || 'Lokasi tidak tercatat') + '</b><small>' + nice(last.status) + ' · ' + esc(last.time) + '</small></div></div>' +
        (hasPts ? '<div class="tmap" id="tmap"></div>' : '<div class="nodata" style="padding:4px 0 16px;font-size:11.5px">Peta tampil jika event tracking punya koordinat.</div>') +
        '<div class="dr-sec"><h4><i class="fa-solid fa-clock-rotate-left"></i> RIWAYAT PERJALANAN</h4><div class="tl">' +
        t.slice().reverse().map((e, n) => '<div class="ti ' + (n === 0 ? 'last' : '') + '"><b>' + nice(e.status) + '</b><span>' + esc(e.location || '-') + '</span>' +
            (e.desc ? '<small>' + esc(e.desc) + '</small>' : '') + '<small>' + esc(e.time) + (e.by ? ' · ' + esc(e.by) : '') + '</small></div>').join('') + '</div></div>';
}

function drawMap(s) {
    const pts = (s.tracks || []).filter(x => x.lat !== null && x.lng !== null), el = $('tmap');
    if (!el || !pts.length || typeof L === 'undefined') return;
    tmap = L.map(el);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap', maxZoom:18 }).addTo(tmap);
    const ll = pts.map(p => [p.lat, p.lng]);
    if (ll.length > 1) L.polyline(ll, { color:'#2563eb', weight:3, opacity:.85 }).addTo(tmap);
    pts.forEach((p, n) => L.circleMarker([p.lat, p.lng], { radius:n === pts.length - 1 ? 8 : 5, color:'#fff', weight:2, fillColor:n === pts.length - 1 ? '#f59e0b' : '#2563eb', fillOpacity:1 })
        .bindPopup('<b>' + esc(p.location || '-') + '</b><br>' + esc(p.time)).addTo(tmap));
    tmap.fitBounds(ll, { padding:[30,30], maxZoom:10 });
    setTimeout(() => tmap && tmap.invalidateSize(), 320);
}

function setTab(t) {
    const s = ROWS[cur]; if (!s) return;
    document.querySelectorAll('.tab').forEach(b => b.classList.toggle('on', b.dataset.t === t));
    killMap();
    $('drBody').innerHTML = t === 'track' ? tabTrack(s) : tabDetail(s);
    $('drBody').scrollTop = 0;
    if (t === 'track') drawMap(s);
}

function showDrawer(i, tab) {
    const s = ROWS[i]; if (!s) return;
    cur = i;
    $('drNum').textContent = s.number;
    $('drSub').textContent = (s.name || '') + (s.customer ? ' · ' + s.customer : '');
    $('drFoot').innerHTML =
        '<button type="button" class="btn" id="drEdit"><i class="fa-solid fa-pen"></i> Edit Pengiriman</button>' +
        '<button type="button" class="btn btn-danger" id="drDel"><i class="fa-solid fa-trash"></i> Hapus</button>';
    $('drEdit').addEventListener('click', () => openEdit(ROWS[cur]));
    $('drDel').addEventListener('click', () => delShip(cur));
    dr.classList.add('open'); veil.classList.add('open'); dr.setAttribute('aria-hidden', 'false');
    setTab(tab || 'detail');
}

document.querySelectorAll('tbody tr[data-i]').forEach(tr => {
    tr.addEventListener('click', () => showDrawer(+tr.dataset.i));
    tr.addEventListener('keydown', e => { if (e.key === 'Enter' && e.target === tr) showDrawer(+tr.dataset.i); });
});
document.querySelectorAll('.ab').forEach(b => b.addEventListener('click', e => {
    e.stopPropagation();
    const i = +b.dataset.i, a = b.dataset.act;
    if (a === 'track') showDrawer(i, 'track'); else if (a === 'edit') openEdit(ROWS[i]); else delShip(i);
}));
document.querySelectorAll('.tab').forEach(b => b.addEventListener('click', () => setTab(b.dataset.t)));
$('drClose').addEventListener('click', closeDrawer); veil.addEventListener('click', closeDrawer);
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeModal(); closeDrawer(); } });

/* ===== BUKA OTOMATIS DARI LINK LAMA (?buka=tambah|detail|lacak|edit&id=..) ===== */
(function () {
    const q = new URLSearchParams(location.search), b = q.get('buka');
    if (!b) return;
    if (b === 'tambah') openCreate();
    else {
        const i = ROWS.findIndex(r => String(r.id) === q.get('id'));
        if (i >= 0) { if (b === 'edit') openEdit(ROWS[i]); else showDrawer(i, b === 'lacak' ? 'track' : 'detail'); }
    }
    q.delete('buka'); q.delete('id');
    history.replaceState(null, '', location.pathname + (q.toString() ? '?' + q.toString() : ''));
})();

/* ===== KURS MATA UANG ===== */
(function () {
    const EP = { api: @json($rt('mata-uang.api')), cur: @json($rt('mata-uang.currencies')) };
    const NAMES = { IDR:'Rupiah Indonesia', USD:'Dolar Amerika Serikat', SGD:'Dolar Singapura', EUR:'Euro (Uni Eropa)', JPY:'Yen Jepang', MYR:'Ringgit Malaysia', GBP:'Pound Sterling Inggris', AUD:'Dolar Australia', CNY:'Yuan Tiongkok', SAR:'Riyal Arab Saudi', KRW:'Won Korea Selatan', THB:'Baht Thailand', HKD:'Dolar Hong Kong', INR:'Rupee India', CHF:'Franc Swiss', NZD:'Dolar Selandia Baru', CAD:'Dolar Kanada', AED:'Dirham UEA', VND:'Dong Vietnam', PHP:'Peso Filipina' };
    const FEAT = ['USD', 'SGD', 'EUR', 'JPY'];
    let rates = null, names = Object.assign({}, NAMES), change = {}, since = null;

    const getJSON = async url => {
        const r = await fetch(url, { headers:{ 'Accept':'application/json' } });
        let j = null; try { j = await r.json(); } catch (_) {}
        if (!r.ok || j?.success === false) throw new Error(j?.message || ('HTTP ' + r.status));
        return j;
    };
    const withBase = u => u + (u.includes('?') ? '&' : '?') + 'base=IDR';

    function parseRates(d) {
        let r = d?.rates || d?.conversion_rates || d?.data?.rates || d?.result?.rates || d?.data;
        if (Array.isArray(r)) { const o = {}; r.forEach(x => { const c = x.quote || x.code || x.currency || x.symbol, v = x.rate ?? x.value; if (c && v != null) o[String(c).toUpperCase()] = +v; }); r = o; }
        if (!r || typeof r !== 'object') return null;
        const o = {};
        for (const k in r) { const v = (r[k] && typeof r[k] === 'object') ? +(r[k].rate ?? r[k].value) : +r[k]; if (isFinite(v) && v > 0) o[k.toUpperCase()] = v; }
        const b = String(d?.base || d?.base_code || d?.data?.base || d?.result?.base || 'USD').toUpperCase();
        if (!(b in o)) o[b] = 1;
        return o;
    }
    let dn = null; try { dn = new Intl.DisplayNames(['id'], { type:'currency' }); } catch (_) {}
    function fillNames() {
        Object.keys(rates).forEach(c => {
            if (names[c]) return;
            let n; try { n = dn && dn.of(c); } catch (_) {}
            names[c] = (n && n !== c) ? n : c;
        });
    }
    const idr = c => c === 'IDR' ? 1 : (rates.IDR / rates[c]);          // 1 unit c = ? IDR
    const nf = (v, max) => new Intl.NumberFormat('id-ID', { maximumFractionDigits:max }).format(v);
    const rp = v => 'Rp ' + nf(v, v < 100 ? 4 : 2);

    function calc() {
        if (!rates) return;
        const a = parseFloat($('fxAmt').value), f = $('fxFrom').value, t = $('fxTo').value;
        if (isNaN(a)) { $('fxOut').textContent = '—'; return; }
        const v = a * idr(f) / idr(t), r1 = idr(f) / idr(t);
        $('fxLine').textContent = nf(a, 6) + ' ' + f + ' =';
        $('fxOut').textContent = (t === 'IDR' ? 'Rp ' : '') + nf(v, v < 1 ? 6 : 2) + (t === 'IDR' ? '' : ' ' + t);
        $('fxRate').textContent = '1 ' + f + ' = ' + nf(r1, r1 < 1 ? 6 : 2) + ' ' + t;
    }
    function chg(c) {
        const v = change[c];
        if (v === undefined || !isFinite(v)) return '<span class="chg na">—</span>';
        if (Math.abs(v) < 0.005) return '<span class="chg na">0,00%</span>';
        return '<span class="chg ' + (v > 0 ? 'up' : 'dn') + '">' + (v > 0 ? '▲ ' : '▼ ') + nf(Math.abs(v), 2) + '%</span>';
    }
    function list() {
        const q = $('fxQ').value.toLowerCase().trim();
        const codes = Object.keys(rates).filter(c => c !== 'IDR' && (rates.IDR && rates[c])).sort();
        $('fxFeat').innerHTML = FEAT.filter(c => rates[c]).map(c => '<button type="button" class="fx-card" data-c="' + c + '"><b>' + c + '</b><span>' + rp(idr(c)) + '</span></button>').join('');
        const shown = codes.filter(c => !q || c.toLowerCase().includes(q) || (names[c] || '').toLowerCase().includes(q));
        $('fxRows').innerHTML = shown.length ? shown.map(c => '<div class="fx-row" data-c="' + c + '"><div class="fx-code">' + c + '</div><div class="fx-n"><strong>' + esc(names[c] || c) + '</strong><small>1 IDR = ' + nf(1 / idr(c), 1 / idr(c) < 1 ? 6 : 4) + ' ' + c + '</small></div><div class="fx-v"><b>' + rp(idr(c)) + '</b>' + chg(c) + '</div></div>').join('')
            : '<div class="fx-state">Mata uang tidak ditemukan.</div>';
        document.querySelectorAll('#fxFeat .fx-card,#fxRows .fx-row').forEach(el => el.addEventListener('click', () => { $('fxFrom').value = el.dataset.c; $('fxTo').value = 'IDR'; calc(); document.querySelector('.fx-calc').scrollIntoView({ behavior:'smooth', block:'nearest' }); }));
    }
    function fillSelects() {
        const codes = ['IDR', ...Object.keys(rates).filter(c => c !== 'IDR' && rates[c]).sort()];
        const opt = codes.map(c => '<option value="' + c + '">' + c + ' — ' + esc(names[c] || c) + '</option>').join('');
        const pf = $('fxFrom').value, pt = $('fxTo').value;
        $('fxFrom').innerHTML = opt; $('fxTo').innerHTML = opt;
        $('fxFrom').value = codes.includes(pf) ? pf : (rates.USD ? 'USD' : codes[1] || 'IDR');
        $('fxTo').value = codes.includes(pt) ? pt : 'IDR';
    }
    function snapshot() {
        try {
            const cur = {}; Object.keys(rates).forEach(c => { if (c !== 'IDR') cur[c] = idr(c); });
            const old = JSON.parse(localStorage.getItem('ct_fx') || 'null'), now = Date.now();
            change = {}; since = null;
            if (old && now - old.t > 5 * 60 * 1000) { since = new Date(old.t); for (const c in cur) if (old.r[c]) change[c] = (cur[c] - old.r[c]) / old.r[c] * 100; }
            if (!old || now - old.t > 24 * 3600 * 1000) localStorage.setItem('ct_fx', JSON.stringify({ t:now, r:cur }));
        } catch (_) {}
    }
    async function load() {
        $('fxMeta').textContent = 'Memuat data…';
        $('fxRows').innerHTML = '<div class="skel"></div><div class="skel"></div><div class="skel"></div><div class="skel"></div>';
        try {
            const d = await getJSON(withBase(EP.api));
            rates = parseRates(d);
            if (!rates) throw new Error('Format data kurs tidak dikenali.');
            if (!rates.IDR) throw new Error('Data kurs tidak memuat Rupiah (IDR).');
            fillNames();
            snapshot(); fillSelects(); list(); calc();
            const t = d?.updated_at || d?.date || d?.last_updated || d?.time_last_update_utc;
            $('fxMeta').textContent = 'Diperbarui ' + (t && !isNaN(new Date(t)) ? new Date(t).toLocaleString('id-ID', { dateStyle:'medium', timeStyle:'short' }) : new Date().toLocaleString('id-ID', { dateStyle:'medium', timeStyle:'short' })) +
                (since ? ' · perubahan dibanding ' + since.toLocaleDateString('id-ID', { day:'numeric', month:'short' }) : ' · perubahan tampil setelah dibuka lagi nanti');
        } catch (e) {
            $('fxMeta').textContent = 'Gagal memuat kurs';
            $('fxFeat').innerHTML = '';
            $('fxRows').innerHTML = '<div class="fx-state"><i class="fa-solid fa-triangle-exclamation" style="font-size:24px;color:#f59e0b"></i><br>' + esc(e.message || 'Kurs belum dapat dimuat.') + '<br>Periksa API kurs di MataUangController.<br><button class="btn" type="button" id="fxRetry">Coba lagi</button></div>';
            $('fxLine').textContent = 'Kurs belum tersedia'; $('fxOut').textContent = '—'; $('fxRate').textContent = '';
            $('fxRetry')?.addEventListener('click', load);
        }
    }
    ['fxAmt', 'fxFrom', 'fxTo'].forEach(id => $(id).addEventListener('input', calc));
    $('fxSwap').addEventListener('click', () => { const f = $('fxFrom').value; $('fxFrom').value = $('fxTo').value; $('fxTo').value = f; calc(); });
    $('fxQ').addEventListener('input', () => rates && list());
    $('fxRefresh').addEventListener('click', load);
    load();
})();
</script>
</body>
</html>
