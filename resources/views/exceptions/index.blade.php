<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Pengecualian | CargoTrace</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
.alert.info{background:#e5f1ff;color:#1d4ed8}

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

/* ===== PENGECUALIAN ===== */
.hide{display:none!important}
.flow.four{grid-template-columns:repeat(4,minmax(0,1fr))}
.flow.four .step::after{display:none}
.st-OPEN{background:#ffe9e9;color:#c92f3a}.st-INVESTIGATING{background:#fff4dc;color:#b77900}.st-RESOLVED{background:#e8f7ef;color:#26734d}
.sv-LOW{background:#e8f7ef;color:#26734d}.sv-MEDIUM{background:#fff4dc;color:#b77900}.sv-HIGH{background:#ffe9e9;color:#c92f3a}
.tbadge{display:inline-flex;align-items:center;gap:8px;font-weight:700;color:var(--navy);white-space:nowrap}
.tbadge i{display:grid;place-items:center;width:28px;height:28px;border-radius:9px;background:#e8f1ff;color:var(--blue);font-size:12px}
.muted{color:#9db0cc}
.stack{display:flex;height:14px;border-radius:8px;overflow:hidden;background:#e9f0fc;margin:6px 0 16px}
.stack i{display:block;height:100%}
.lg{display:flex;justify-content:space-between;align-items:center;padding:11px 14px;border-radius:14px;background:#f4f8ff;margin-bottom:8px}
.lg span{display:flex;align-items:center;gap:9px;font-weight:600;color:var(--navy)}
.lg span i{width:10px;height:10px;border-radius:50%;display:inline-block}
.lg b{font:800 17px var(--h);color:var(--navy)}
.filters.ex select{min-width:140px}
.filters.ex input[type=date]{min-width:140px}
.fgrid input[type=file]{padding:10px;background:#f4f8ff;border-style:dashed}
.curfile{padding:11px 14px;border-radius:12px;background:#f4f8ff;font-size:12px;color:var(--mut);margin-bottom:8px}
.curfile a{color:var(--blue);font-weight:700}
.att{display:flex;align-items:center;gap:12px;padding:14px;border:1px dashed #b9d2f7;border-radius:16px;background:#f7faff}
.att i.f{display:grid;place-items:center;width:42px;height:42px;border-radius:12px;background:#e8f1ff;color:var(--blue);font-size:18px;flex:none}
.att b{display:block;font-size:12px;color:var(--navy);overflow-wrap:anywhere}
.att a{font-size:11px;font-weight:700;color:var(--blue)}
.attimg{display:block;width:100%;max-height:220px;object-fit:cover;border-radius:14px;margin-bottom:10px}
.tx{padding:13px 15px;border-radius:14px;background:#f4f8ff;font-size:12px;line-height:1.75;color:#41608a;white-space:pre-line;overflow-wrap:anywhere}
.bdgs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px}
@media(max-width:1250px){.flow.four{grid-template-columns:repeat(2,minmax(0,1fr))}}

/* ===== OTOMATIS + ETA/DELAY ===== */
.autotag{display:inline-flex;align-items:center;gap:5px;margin-top:4px;padding:2px 8px;border-radius:20px;background:#e5f1ff;color:#1d4ed8;font-size:9.5px;font-weight:700}
.rules{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
.rules .status::before{display:none}
.mon{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:12px}
.mon a{display:block;padding:16px;border:1px solid var(--line);border-radius:18px;background:#fbfdff;text-decoration:none;transition:.2s}
.mon a:hover{border-color:#9fc2f7;transform:translateY(-2px)}
.mon .top{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:10px}
.mon .top strong{color:var(--navy);font-size:13px}
.mon dl{display:grid;grid-template-columns:auto 1fr;gap:5px 12px;font-size:11.5px}
.mon dt{color:#8ca6c4;font-weight:600}.mon dd{color:var(--navy);font-weight:600;text-align:right}
.mon .late{margin-top:10px;padding:9px 12px;border-radius:12px;background:#fff4dc;color:#8a5a00;font-weight:700;font-size:11.5px;display:flex;justify-content:space-between;gap:8px}
.mon .link{margin-top:8px;font-size:11px;color:var(--blue);font-weight:700}
.reason{padding:13px 15px;border-radius:14px;background:#fff8e8;border:1px solid #fde7b0;color:#7a5200;font-size:12px;line-height:1.7;margin-top:10px}
</style>
</head>
<body>

@php
    use Carbon\Carbon;
    $rt = fn ($name) => Route::has($name) ? route($name) : '#';
    $fmt = fn ($v, $f = 'd M Y H:i') => $v ? Carbon::parse($v)->format($f) : null;
    $n = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.') ?: '0';
    $stripRe = '/^\[AUTO-(?:DELAY|EVENT:\d+)\]\s*/';
    $types = [
        'CUSTOMS_HOLD'        => ['Customs Hold', 'fa-stamp'],
        'WEATHER_DELAY'       => ['Weather Delay', 'fa-cloud-showers-heavy'],
        'PORT_CONGESTION'     => ['Port Congestion', 'fa-anchor'],
        'DAMAGED'             => ['Damaged Cargo', 'fa-box-open'],
        'MISROUTED'           => ['Misrouted', 'fa-route'],
        'DOCUMENTATION_ISSUE' => ['Documentation Issue', 'fa-file-circle-exclamation'],
    ];
    $sevs = ['LOW' => 'Low - Rendah', 'MEDIUM' => 'Medium - Sedang', 'HIGH' => 'High - Tinggi'];
    $cards = [
        ['', 'Total Pengecualian', $stats['total'], 'fa-triangle-exclamation', '#2563eb', 'Seluruh laporan pengecualian.'],
        ['OPEN', 'Belum Ditangani', $stats['open'], 'fa-circle-exclamation', '#e5484d', 'Pengecualian berstatus Open.'],
        ['INVESTIGATING', 'Sedang Investigasi', $stats['investigating'], 'fa-magnifying-glass', '#f59e0b', 'Dalam proses penanganan.'],
        ['RESOLVED', 'Selesai', $stats['resolved'], 'fa-circle-check', '#16a34a', 'Sudah diselesaikan.'],
    ];

    // Data untuk panel detail & form edit (termasuk pengecualian yang dibuka lewat link lama).
    $list = $exceptions->getCollection();
    if ($focus && !$list->contains('id', $focus->id)) { $list = $list->concat([$focus]); }
    $rows = $list->map(fn ($e) => [
        'id' => $e->id,
        'code' => 'EXC-' . str_pad($e->id, 5, '0', STR_PAD_LEFT),
        'shipment_id' => $e->shipment_id,
        'sn' => optional($e->shipment)->shipment_number,
        'sname' => optional($e->shipment)->shipment_name,
        'customer' => optional($e->shipment)->customer_name,
        'carrier' => optional($e->shipment)->carrier,
        'origin' => optional($e->shipment)->origin,
        'destination' => optional($e->shipment)->destination,
        'sstatus' => optional($e->shipment)->status,
        'type' => $e->type,
        'type_label' => $types[$e->type][0] ?? str_replace('_', ' ', $e->type),
        'location' => $e->location,
        'occurred' => $fmt($e->occurred_at),
        'occurred_raw' => $e->occurred_at ? Carbon::parse($e->occurred_at)->format('Y-m-d\TH:i') : '',
        'severity' => $e->severity,
        'status' => $e->status,
        'auto' => (bool) preg_match('/^\[AUTO-/', (string) $e->description),
        'description' => preg_replace($stripRe, '', (string) $e->description),
        'cause' => $e->cause,
        'impact' => $e->impact,
        'delay' => $delayMap[$e->id] ?? null,
        'assigned_to' => $e->assigned_to,
        'assigned' => optional($e->assignedStaff)->name,
        'reporter' => optional($e->reporter)->name,
        'created' => $fmt($e->created_at),
        'resolved' => $fmt($e->resolved_at),
        'att' => $e->attachment_path ? asset('storage/' . $e->attachment_path) : null,
        'att_name' => $e->attachment_path ? basename($e->attachment_path) : null,
        'hist' => $e->relationLoaded('histories') ? $e->histories->sortBy('created_at')->map(fn ($h) => [
            'action' => $h->action, 'old' => $h->old_status, 'new' => $h->new_status, 'note' => $h->note,
            'user' => optional($h->user)->name, 'time' => $fmt($h->created_at),
        ])->values() : [],
    ])->values();

    $formMode = $errors->any() ? old('_modal') : null;   // create | edit
    $openModal = $canManage && in_array($formMode, ['create', 'edit'], true);
    $sevTotal = max($stats['low'] + $stats['medium'] + $stats['high'], 1);
    $maxT = max((int) ($typeStats->max() ?? 1), 1);
    $syncTotal = $sync ? ($sync['created'] + $sync['updated'] + $sync['resolved']) : 0;
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

    <a href="{{ route('pelacakan.index') }}" class="ct-link">
        <i class="fa-solid fa-location-dot"></i>
        <span>Pelacakan</span>
    </a>

    <a href="{{ route('exceptions.index') }}" class="ct-link on">
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
            <div class="crumb"><a href="{{ route('dashboard') }}" style="text-decoration:none">Dashboard</a> &nbsp;/&nbsp; Pengecualian</div>
            <h1>Manajemen Pengecualian</h1>
            <p>Pantau, identifikasi, dan tangani kendala pengiriman. Keterlambatan (ETA melewati jadwal) terdeteksi dan masuk otomatis sebagai pengecualian.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('exceptions.index', ['sync' => 1]) }}#monitor" class="btn btn-glass"><i class="fa-solid fa-rotate"></i> Sinkronkan Delay</a>
            @if($canManage)<button type="button" class="btn" id="openCreate"><i class="fa-solid fa-plus"></i> Tambah Pengecualian</button>@endif
        </div>
    </section>

    @if(session('success'))<div class="alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if($errors->any() && !$openModal)<div class="alert err"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif
    @if($sync && $sync['error'])<div class="alert err"><i class="fa-solid fa-circle-exclamation"></i> Deteksi otomatis gagal dijalankan. Periksa storage/logs/laravel.log.</div>@endif
    @if($syncTotal > 0)<div class="alert info"><i class="fa-solid fa-robot"></i> Deteksi otomatis: {{ $sync['created'] }} pengecualian baru, {{ $sync['updated'] }} diperbarui, {{ $sync['resolved'] }} diselesaikan otomatis.</div>@endif

    <!-- STATISTIK -->
    <section class="flow four">
        @foreach($cards as [$key, $label, $count, $icon, $color, $desc])
            <a href="{{ $key === '' ? route('exceptions.index') . '#daftar' : route('exceptions.index', ['status' => $key]) . '#daftar' }}" class="step {{ ($key !== '' && request('status') === $key) ? 'on' : '' }}" style="--c:{{ $color }}">
                <div class="ic"><i class="fa-solid {{ $icon }}"></i></div>
                <b>{{ number_format($count) }}</b>
                <strong>{{ $label }}</strong>
                <small>{{ $desc }}</small>
            </a>
        @endforeach
    </section>

    <!-- PEMANTAUAN ETA & DELAY -->
    <section class="panel sect" id="monitor">
        <div class="ph">
            <div>
                <h2><span class="hi"><i class="fa-solid fa-hourglass-half"></i></span> Pemantauan ETA &amp; Delay</h2>
                <p>Estimated arrival dibandingkan dengan expected arrival. Shipment yang terlambat otomatis dibuatkan pengecualian ({{ $stats['auto'] }} pengecualian otomatis tercatat).</p>
            </div>
            <span class="pill">{{ $delayOverview['total'] }} shipment terlambat</span>
        </div>

        <div class="rules">
            <span class="status sv-LOW">0–{{ $n($rules['low_max']) }} jam · LOW</span>
            <span class="status sv-MEDIUM">&gt;{{ $n($rules['low_max']) }}–{{ $n($rules['medium_max']) }} jam · MEDIUM</span>
            <span class="status sv-HIGH">&gt;{{ $n($rules['medium_max']) }} jam · HIGH</span>
            <span class="status st-CREATED">Threshold minimal: {{ $n($rules['threshold']) }} jam</span>
        </div>

        @if(!$etaCols)
            <div class="nodata"><i class="fa-solid fa-circle-info" style="font-size:22px;color:var(--blue)"></i><br>Kolom ETA belum ditemukan di tabel <b>shipments</b>.<br>Tambahkan kolom <b>estimated_arrival</b> dan <b>expected_arrival</b> (tipe datetime) agar deteksi delay otomatis berjalan.</div>
        @elseif(empty($delayOverview['rows']))
            <div class="nodata"><i class="fa-solid fa-circle-check" style="font-size:22px;color:#16a34a"></i><br>Tidak ada shipment aktif yang terlambat. Semua sesuai jadwal.</div>
        @else
            <div class="mon">
                @foreach($delayOverview['rows'] as $d)
                    <a href="{{ $d['exception_id'] ? route('exceptions.index', ['buka' => 'detail', 'id' => $d['exception_id']]) : '#monitor' }}">
                        <div class="top"><strong>{{ $d['shipment_number'] }}</strong><span class="status sv-{{ $d['severity'] }}">{{ $d['severity'] }}</span></div>
                        <dl>
                            <dt>Pelanggan</dt><dd>{{ $d['customer'] ?: '-' }}</dd>
                            <dt>Expected arrival</dt><dd>{{ $d['expected'] }}</dd>
                            <dt>Estimated (ETA)</dt><dd>{{ $d['estimated'] }}</dd>
                        </dl>
                        <div class="late"><span><i class="fa-solid fa-clock"></i> Terlambat</span><span>{{ $d['duration'] }}</span></div>
                        @if($d['exception_code'])<div class="link">{{ $d['exception_code'] }} · {{ $d['exception_status'] }} →</div>@endif
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <!-- KEPARAHAN + JENIS -->
    <section class="grid-2">
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-gauge-high"></i></span> Tingkat Keparahan</h2><p>Sebaran pengecualian berdasarkan severity.</p></div></div>
            <div class="stack" title="Low / Medium / High">
                <i style="width:{{ $stats['low'] / $sevTotal * 100 }}%;background:#2eaa70"></i>
                <i style="width:{{ $stats['medium'] / $sevTotal * 100 }}%;background:#f5b73a"></i>
                <i style="width:{{ $stats['high'] / $sevTotal * 100 }}%;background:#e5484d"></i>
            </div>
            <div class="lg"><span><i style="background:#2eaa70"></i> Low Severity</span><b>{{ $stats['low'] }}</b></div>
            <div class="lg"><span><i style="background:#f5b73a"></i> Medium Severity</span><b>{{ $stats['medium'] }}</b></div>
            <div class="lg" style="margin-bottom:0"><span><i style="background:#e5484d"></i> High Severity</span><b>{{ $stats['high'] }}</b></div>
        </div>
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-chart-simple"></i></span> Berdasarkan Jenis</h2><p>Jenis kendala yang paling sering terjadi.</p></div></div>
            @foreach($types as $key => [$label, $icon])
                @php $cnt = (int) ($typeStats[$key] ?? 0); @endphp
                <div class="li">
                    <div class="t"><strong>{{ $label }}</strong><span class="pill">{{ $cnt }}</span></div>
                    <div class="bar"><i style="width:{{ $cnt / $maxT * 100 }}%"></i></div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- DAFTAR PENGECUALIAN -->
    <section class="panel sect" id="daftar">
        <div class="ph">
            <div>
                <h2><span class="hi"><i class="fa-solid fa-list"></i></span> Daftar Pengecualian</h2>
                <p>{{ $stats['total'] }} laporan tersimpan. Klik baris untuk melihat detail dan riwayat penanganan.</p>
            </div>
            @if($canManage)<button type="button" class="btn" id="openCreate2"><i class="fa-solid fa-plus"></i> Tambah</button>@endif
        </div>

        <form action="{{ route('exceptions.index') }}#daftar" method="GET" class="filters ex">
            <input class="grow" type="text" name="search" placeholder="Cari nomor pengiriman, pelanggan, lokasi..." value="{{ request('search') }}" aria-label="Pencarian">
            <select name="type" aria-label="Jenis"><option value="">Semua jenis</option>
                @foreach($types as $key => [$label])<option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>@endforeach
            </select>
            <select name="severity" aria-label="Severity"><option value="">Semua severity</option>
                @foreach(['LOW', 'MEDIUM', 'HIGH'] as $v)<option value="{{ $v }}" @selected(request('severity') === $v)>{{ $v }}</option>@endforeach
            </select>
            <select name="status" aria-label="Status"><option value="">Semua status</option>
                @foreach(['OPEN', 'INVESTIGATING', 'RESOLVED'] as $v)<option value="{{ $v }}" @selected(request('status') === $v)>{{ $v }}</option>@endforeach
            </select>
            <select name="source" aria-label="Sumber"><option value="">Semua sumber</option>
                <option value="AUTO" @selected(request('source') === 'AUTO')>Otomatis (sistem)</option>
                <option value="MANUAL" @selected(request('source') === 'MANUAL')>Manual</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" title="Dari tanggal" aria-label="Dari tanggal">
            <input type="date" name="date_to" value="{{ request('date_to') }}" title="Sampai tanggal" aria-label="Sampai tanggal">
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a href="{{ route('exceptions.index') }}#daftar" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead><tr><th>ID</th><th>Pengiriman</th><th>Jenis</th><th>Lokasi</th><th>Waktu</th><th>Severity</th><th>Status</th><th>Petugas</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($exceptions as $i => $e)
                    <tr data-i="{{ $i }}" tabindex="0">
                        <td><span class="num">EXC-{{ str_pad($e->id, 5, '0', STR_PAD_LEFT) }}</span>@if(preg_match('/^\[AUTO-/', (string) $e->description))<br><span class="autotag"><i class="fa-solid fa-robot"></i> Otomatis</span>@endif</td>
                        <td><strong style="color:var(--navy)">{{ $e->shipment->shipment_number ?? '-' }}</strong><small>{{ $e->shipment->customer_name ?? '-' }}</small></td>
                        <td><span class="tbadge"><i class="fa-solid {{ $types[$e->type][1] ?? 'fa-triangle-exclamation' }}"></i>{{ $types[$e->type][0] ?? str_replace('_', ' ', $e->type) }}</span></td>
                        <td>{{ $e->location }}</td>
                        <td style="white-space:nowrap">{{ $fmt($e->occurred_at, 'd/m/Y H:i') ?? '-' }}</td>
                        <td><span class="status sv-{{ $e->severity }}">{{ $e->severity }}</span></td>
                        <td><span class="status st-{{ $e->status }}">{{ $e->status }}</span></td>
                        <td>@if($e->assignedStaff){{ $e->assignedStaff->name }}@else<span class="muted">Belum ditugaskan</span>@endif</td>
                        <td><div class="acts">
                            <button type="button" class="ab" data-act="view" data-i="{{ $i }}" title="Detail" aria-label="Detail"><i class="fa-solid fa-eye"></i></button>
                            @if($canManage)
                            <button type="button" class="ab" data-act="edit" data-i="{{ $i }}" title="Edit" aria-label="Edit"><i class="fa-solid fa-pen"></i></button>
                            <button type="button" class="ab del" data-act="del" data-i="{{ $i }}" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></button>
                            @endif
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty"><i class="fa-solid fa-clipboard-check"></i><strong style="color:var(--navy)">Belum ada data pengecualian</strong><br>Belum ada laporan yang sesuai. Ubah filter atau tambahkan laporan baru.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pager">
            <span>@if($exceptions->total() > 0)Menampilkan {{ $exceptions->firstItem() }}–{{ $exceptions->lastItem() }} dari {{ $exceptions->total() }} pengecualian@else Belum ada data pengecualian @endif</span>
            @if($exceptions->hasPages())<div>{{ $exceptions->links() }}</div>@endif
        </div>
    </section>

</section>
</main>

<!-- DRAWER DETAIL -->
<div class="veil" id="veil"></div>
<aside class="drawer" id="drawer" aria-hidden="true">
    <div class="dr-head"><button type="button" class="x" id="drClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button><h3 id="drNum">—</h3><p id="drSub"></p></div>
    <div class="dr-body" id="drBody"></div>
    <div class="dr-foot" id="drFoot"></div>
</aside>

@if($canManage)
<!-- MODAL TAMBAH / EDIT -->
<div class="modal {{ $openModal ? 'open' : '' }}" id="modal">
    <div class="mbox" role="dialog" aria-modal="true" aria-labelledby="mTitle">
        <div class="mhead">
            <div><h3 id="mTitle">Tambah Pengecualian</h3><p id="mSub">Laporkan kendala yang terjadi selama proses pengiriman.</p></div>
            <button type="button" class="x" id="mClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('exceptions.store') }}" id="exForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="PUT" id="mMethod" @if($formMode !== 'edit') disabled @endif>
            <input type="hidden" name="_modal" value="{{ $formMode === 'edit' ? 'edit' : 'create' }}" id="mMode">
            <input type="hidden" name="_id" value="{{ old('_id') }}" id="mId">
            <div class="fgrid">
                @php $err = fn ($f) => $openModal && $errors->has($f) ? '<div class="ferr">' . e($errors->first($f)) . '</div>' : ''; @endphp

                <div class="fsec"><i class="fa-solid fa-box"></i> 01. INFORMASI PENGIRIMAN</div>
                <div class="full"><label for="f_sh">NOMOR PENGIRIMAN</label>
                    <select id="f_sh" name="shipment_id" required>
                        <option value="">Pilih nomor pengiriman</option>
                        @foreach($shipments as $s)<option value="{{ $s->id }}" @selected((string) old('shipment_id') === (string) $s->id)>{{ $s->shipment_number }} - {{ $s->customer_name }}</option>@endforeach
                    </select>{!! $err('shipment_id') !!}</div>

                <div class="fsec"><i class="fa-solid fa-triangle-exclamation"></i> 02. DETAIL KEJADIAN</div>
                <div><label for="f_ty">JENIS PENGECUALIAN</label>
                    <select id="f_ty" name="type" required>
                        <option value="">Pilih jenis</option>
                        @foreach($types as $key => [$label])<option value="{{ $key }}" @selected(old('type') === $key)>{{ $label }}</option>@endforeach
                    </select>{!! $err('type') !!}</div>
                <div><label for="f_lo">LOKASI KEJADIAN</label><input id="f_lo" name="location" value="{{ old('location') }}" placeholder="Contoh: Pelabuhan Belawan" required>{!! $err('location') !!}</div>
                <div><label for="f_oc">TANGGAL &amp; WAKTU KEJADIAN</label><input id="f_oc" type="datetime-local" name="occurred_at" value="{{ old('occurred_at') }}" required>{!! $err('occurred_at') !!}</div>
                <div><label for="f_sv">TINGKAT KEPARAHAN</label>
                    <select id="f_sv" name="severity" required>
                        <option value="">Pilih severity</option>
                        @foreach($sevs as $key => $label)<option value="{{ $key }}" @selected(old('severity') === $key)>{{ $label }}</option>@endforeach
                    </select>{!! $err('severity') !!}</div>
                <div class="full"><label for="f_de">DESKRIPSI MASALAH</label><textarea id="f_de" name="description" placeholder="Jelaskan kejadian atau masalah yang dialami..." required>{{ old('description') }}</textarea>{!! $err('description') !!}</div>

                <div class="fsec"><i class="fa-solid fa-magnifying-glass-chart"></i> 03. ANALISIS PENGECUALIAN</div>
                <div><label for="f_ca">PENYEBAB MASALAH</label><textarea id="f_ca" name="cause" placeholder="Penyebab terjadinya masalah...">{{ old('cause') }}</textarea>{!! $err('cause') !!}</div>
                <div><label for="f_im">DAMPAK TERHADAP PENGIRIMAN</label><textarea id="f_im" name="impact" placeholder="Dampak pada waktu, biaya, atau kondisi barang...">{{ old('impact') }}</textarea>{!! $err('impact') !!}</div>

                <div class="fsec"><i class="fa-solid fa-user-check"></i> 04. PENANGGUNG JAWAB</div>
                <div><label for="f_as">PETUGAS YANG BERTANGGUNG JAWAB</label>
                    <select id="f_as" name="assigned_to">
                        <option value="">Belum ditentukan</option>
                        @foreach($staff as $p)<option value="{{ $p->id }}" @selected((string) old('assigned_to') === (string) $p->id)>{{ $p->name }}</option>@endforeach
                    </select>{!! $err('assigned_to') !!}</div>
                <div id="stWrap" class="{{ $formMode === 'edit' ? '' : 'hide' }}"><label for="f_st">STATUS PENANGANAN</label>
                    <select id="f_st" name="status">
                        @foreach(['OPEN' => 'Open - Belum Ditangani', 'INVESTIGATING' => 'Investigating - Sedang Ditangani', 'RESOLVED' => 'Resolved - Selesai'] as $key => $label)<option value="{{ $key }}" @selected(old('status', 'OPEN') === $key)>{{ $label }}</option>@endforeach
                    </select>{!! $err('status') !!}</div>

                <div class="fsec"><i class="fa-solid fa-paperclip"></i> 05. BUKTI &amp; LAMPIRAN</div>
                <div class="full">
                    <div class="curfile hide" id="curFile"></div>
                    <label for="f_at">UNGGAH BUKTI KEJADIAN (JPG, PNG, PDF, DOC, DOCX · MAKS. 5 MB)</label>
                    <input id="f_at" type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">{!! $err('attachment') !!}
                </div>
            </div>
            <div class="mfoot">
                <button type="button" class="btn btn-light" id="mCancel">Batal</button>
                <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> <span id="mSaveTx">{{ $formMode === 'edit' ? 'Simpan Perubahan' : 'Simpan Pengecualian' }}</span></button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
const nice = v => esc(String(v || '').replace(/_/g, ' '));

const ROWS = @json($rows), BASE = @json(rtrim(route('exceptions.index'), '/')), STORE = @json(route('exceptions.store')), CSRF = @json(csrf_token());
const CAN = @json($canManage);
const FORM = { mode: @json($formMode), id: @json(old('_id')) };
const STEPS = [['OPEN','Open','fa-circle-exclamation'],['INVESTIGATING','Investigating','fa-magnifying-glass'],['RESOLVED','Resolved','fa-circle-check']];
const HACT = { CREATED:'Pengecualian dibuat', UPDATED:'Data diperbarui', STATUS_CHANGED:'Status berubah' };
const AUTO_RE = /^\[AUTO[^\]]*\]\s*/;

/* ===== MODAL TAMBAH / EDIT ===== */
const modal = $('modal'), exForm = $('exForm');
function setForm(mode, e) {
    $('mMode').value = mode; $('mId').value = e ? e.id : '';
    $('mMethod').disabled = mode !== 'edit';
    exForm.action = mode === 'edit' ? BASE + '/' + encodeURIComponent(e.id) : STORE;
    $('mTitle').textContent = mode === 'edit' ? 'Edit Pengecualian' : 'Tambah Pengecualian';
    $('mSub').textContent = mode === 'edit' ? 'Perbarui data dan proses penanganan ' + (e.code || '') + '.' : 'Laporkan kendala yang terjadi selama proses pengiriman.';
    $('mSaveTx').textContent = mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Pengecualian';
    $('stWrap').classList.toggle('hide', mode !== 'edit');
}
function cleanForm() { exForm.reset(); exForm.querySelectorAll('.ferr').forEach(x => x.remove()); }
function openCreate() {
    if (!CAN) return;
    cleanForm(); setForm('create'); $('curFile').classList.add('hide');
    $('f_st').value = 'OPEN'; modal.classList.add('open'); setTimeout(() => $('f_sh').focus(), 50);
}
function openEdit(e) {
    if (!CAN || !e) return;
    cleanForm(); setForm('edit', e);
    const set = (id, v) => { $(id).value = v ?? ''; };
    set('f_sh', e.shipment_id); set('f_ty', e.type); set('f_lo', e.location); set('f_oc', e.occurred_raw); set('f_sv', e.severity);
    set('f_de', e.description); set('f_ca', e.cause); set('f_im', e.impact); set('f_as', e.assigned_to); set('f_st', e.status);
    const cf = $('curFile'); cf.classList.remove('hide');
    cf.innerHTML = e.att ? 'Lampiran saat ini: <a href="' + esc(e.att) + '" target="_blank" rel="noopener">Lihat lampiran</a> · pilih file baru di bawah untuk menggantinya.' : 'Belum ada lampiran bukti.';
    closeDrawer(); modal.classList.add('open'); setTimeout(() => $('f_lo').focus(), 50);
}
const closeModal = () => { if (modal) modal.classList.remove('open'); };
if (CAN) {
    ['openCreate', 'openCreate2'].forEach(id => $(id)?.addEventListener('click', openCreate));
    ['mClose', 'mCancel'].forEach(id => $(id).addEventListener('click', closeModal));
    modal.addEventListener('click', ev => { if (ev.target === modal) closeModal(); });
    if (FORM.mode === 'edit' && FORM.id) setForm('edit', { id: FORM.id, code: '' });   // buka ulang setelah validasi gagal
}

/* ===== HAPUS ===== */
function delItem(i) {
    const e = ROWS[i];
    if (!CAN || !e || !confirm('Hapus pengecualian ' + e.code + '? Data dan lampirannya tidak dapat dikembalikan.')) return;
    const f = document.createElement('form');
    f.method = 'POST'; f.action = BASE + '/' + encodeURIComponent(e.id);
    f.innerHTML = '<input type="hidden" name="_token" value="' + CSRF + '"><input type="hidden" name="_method" value="DELETE">';
    document.body.appendChild(f); f.submit();
}

/* ===== DRAWER DETAIL ===== */
const dr = $('drawer'), veil = $('veil');
let cur = null;
const kv = (k, v, full) => '<div' + (full ? ' class="full"' : '') + '><small>' + k + '</small><b>' + (v === null || v === '' || v === undefined ? '-' : esc(v)) + '</b></div>';
const tx = (v, empty) => '<div class="tx">' + (v ? esc(v) : '<span class="muted">' + empty + '</span>') + '</div>';
function closeDrawer() { dr.classList.remove('open'); veil.classList.remove('open'); dr.setAttribute('aria-hidden', 'true'); }

function delayHtml(e) {
    const d = e.delay;
    if (!d) return '';
    return '<div class="dr-sec"><h4><i class="fa-solid fa-hourglass-half"></i> PERHITUNGAN ETA &amp; DELAY</h4><div class="kv">' +
        kv('Expected arrival', d.expected) + kv('Estimated arrival (ETA)', d.estimated) +
        kv('Durasi keterlambatan', d.duration) + kv('Severity hasil rule', d.delayed ? d.severity : 'Tidak ada (tepat waktu)') +
        kv('Aturan severity', d.rule, true) + '</div>' +
        '<div class="reason"><b>Alasan:</b> ' + esc(d.reason) + '</div></div>';
}

function detailHtml(e) {
    const idx = STEPS.findIndex(s => s[0] === e.status);
    const isImg = e.att && /\.(jpe?g|png)(\?|$)/i.test(e.att);
    const att = e.att
        ? (isImg ? '<img class="attimg" src="' + esc(e.att) + '" alt="Bukti kejadian">' : '') +
          '<div class="att"><i class="fa-solid fa-paperclip f"></i><div><b>' + esc(e.att_name || 'Lampiran bukti') + '</b><a href="' + esc(e.att) + '" target="_blank" rel="noopener">Lihat atau unduh lampiran →</a></div></div>'
        : '<div class="tx"><span class="muted">Belum ada lampiran bukti untuk pengecualian ini.</span></div>';
    const hist = (e.hist || []).slice().reverse();
    const histHtml = hist.length
        ? '<div class="tl">' + hist.map((h, n) => {
            const auto = /^\[AUTO/.test(h.note || '');
            return '<div class="ti ' + (n === 0 ? 'last' : '') + '"><b>' + esc(HACT[h.action] || String(h.action || '').replace(/_/g, ' ')) + '</b>' +
            ((h.old || h.new) ? '<span>Status: ' + nice(h.old || 'Belum ada') + ' → ' + nice(h.new || '-') + '</span>' : '') +
            (h.note ? '<small>' + esc(h.note.replace(AUTO_RE, '')) + '</small>' : '') + '<small>' + esc(auto ? 'Sistem otomatis' : (h.user || 'Sistem')) + ' · ' + esc(h.time || '') + '</small></div>';
        }).join('') + '</div>'
        : '<div class="tx"><span class="muted">Belum ada riwayat penanganan.</span></div>';

    return '<div class="bdgs"><span class="status sv-' + esc(e.severity) + '">' + esc(e.severity) + ' SEVERITY</span><span class="status st-' + esc(e.status) + '">' + esc(e.status) + '</span>' +
        (e.auto ? '<span class="status st-BOOKED"><i class="fa-solid fa-robot"></i> Terdeteksi otomatis</span>' : '') + '</div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-signal"></i> STATUS PENANGANAN</h4><div class="prog">' +
            STEPS.map((s, n) => '<div class="pg ' + (n < idx ? 'done' : n === idx ? 'now' : '') + '"><i class="fa-solid ' + (n < idx ? 'fa-check' : s[2]) + '"></i><small>' + s[1] + '</small></div>').join('') + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-box"></i> INFORMASI PENGIRIMAN</h4><div class="kv">' + kv('Nomor pengiriman', e.sn) + kv('Pelanggan', e.customer) + kv('Nama barang', e.sname) + kv('Perusahaan', e.carrier) + kv('Asal', e.origin) + kv('Tujuan', e.destination) + kv('Status pengiriman', e.sstatus ? String(e.sstatus).replace(/_/g, ' ') : '-', true) + '</div></div>' +
        delayHtml(e) +
        '<div class="dr-sec"><h4><i class="fa-solid fa-triangle-exclamation"></i> DETAIL KEJADIAN</h4><div class="kv">' + kv('Jenis', e.type_label) + kv('Lokasi', e.location) + kv('Waktu kejadian', e.occurred, true) + '</div><div style="margin-top:10px">' + tx(e.description, 'Tidak ada deskripsi.') + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-magnifying-glass-chart"></i> PENYEBAB</h4>' + tx(e.cause, 'Belum ada informasi penyebab.') + '</div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-burst"></i> DAMPAK PENGIRIMAN</h4>' + tx(e.impact, 'Belum ada informasi dampak.') + '</div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-paperclip"></i> BUKTI &amp; LAMPIRAN</h4>' + att + '</div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-user-check"></i> PENANGANAN</h4><div class="kv">' + kv('Petugas', e.assigned || 'Belum ditugaskan') + kv('Dilaporkan oleh', e.auto ? 'Sistem (otomatis)' : (e.reporter || 'Tidak diketahui')) + kv('Waktu deteksi / pelaporan', e.created) + kv('Waktu penyelesaian', e.resolved || 'Belum diselesaikan') + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-clock-rotate-left"></i> RIWAYAT PENANGANAN</h4>' + histHtml + '</div>';
}

function showDrawer(i) {
    const e = ROWS[i]; if (!e) return;
    cur = i;
    $('drNum').textContent = e.code;
    $('drSub').textContent = (e.type_label || '') + (e.sn ? ' · ' + e.sn : '');
    $('drBody').innerHTML = detailHtml(e); $('drBody').scrollTop = 0;
    $('drFoot').innerHTML = CAN
        ? '<button type="button" class="btn" id="drEdit"><i class="fa-solid fa-pen"></i> Edit Pengecualian</button>' +
          '<button type="button" class="btn btn-danger" id="drDel"><i class="fa-solid fa-trash"></i> Hapus</button>'
        : '';
    if (CAN) {
        $('drEdit').addEventListener('click', () => openEdit(ROWS[cur]));
        $('drDel').addEventListener('click', () => delItem(cur));
    }
    dr.classList.add('open'); veil.classList.add('open'); dr.setAttribute('aria-hidden', 'false');
}

document.querySelectorAll('tbody tr[data-i]').forEach(tr => {
    tr.addEventListener('click', () => showDrawer(+tr.dataset.i));
    tr.addEventListener('keydown', ev => { if (ev.key === 'Enter' && ev.target === tr) showDrawer(+tr.dataset.i); });
});
document.querySelectorAll('.ab').forEach(b => b.addEventListener('click', ev => {
    ev.stopPropagation();
    const i = +b.dataset.i, a = b.dataset.act;
    if (a === 'view') showDrawer(i); else if (a === 'edit') openEdit(ROWS[i]); else delItem(i);
}));
$('drClose').addEventListener('click', closeDrawer); veil.addEventListener('click', closeDrawer);
document.addEventListener('keydown', ev => { if (ev.key === 'Escape') { closeModal(); closeDrawer(); } });

/* ===== BUKA OTOMATIS DARI LINK (?buka=tambah|detail|edit&id=..) ===== */
(function () {
    const q = new URLSearchParams(location.search), b = q.get('buka');
    if (!b) return;
    if (b === 'tambah') openCreate();
    else {
        const i = ROWS.findIndex(r => String(r.id) === q.get('id'));
        if (i >= 0) { if (b === 'edit') openEdit(ROWS[i]); else showDrawer(i); }
    }
    q.delete('buka'); q.delete('id');
    history.replaceState(null, '', location.pathname + (q.toString() ? '?' + q.toString() : ''));
})();
</script>
</body>
</html>
