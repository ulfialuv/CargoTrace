<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Audit Log | CargoTrace</title>
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
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 55%,#1d6fe870 100%),url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1800&q=80') center/cover;box-shadow:0 24px 50px #0b2a6b44}
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

/* ===== AUDIT LOG ===== */
.hide{display:none!important}
.muted{color:#9db0cc}
.ucell{display:flex;align-items:center;gap:10px;min-width:0}
.uav{flex:none;display:grid;place-items:center;width:36px;height:36px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2563eb);color:#fff;font:800 13px var(--h)}
.un{font-weight:700;color:var(--navy);font-size:12.5px}
.ue{margin-top:3px;font-size:10.5px;color:#8ca6c4}
.role{display:inline-block;padding:5px 11px;border-radius:20px;font-size:10px;font-weight:800;white-space:nowrap}
.role.admin{background:#e0edff;color:#1d4ed8}.role.operator{background:#e5f7f1;color:#1f8a6b}.role.customer{background:#f0eaff;color:#7c3aed}.role.x{background:#eef2f9;color:#8ca6c4}
.act{display:inline-block;padding:5px 11px;border-radius:8px;font-size:10px;font-weight:800;white-space:nowrap;background:#eef2f9;color:#64748b}
.act.CREATE{background:#e8f7ef;color:#26734d}.act.UPDATE{background:#e0edff;color:#1d4ed8}.act.DELETE{background:#ffe9e9;color:#c92f3a}
.act.STATUS-CHANGE,.act.EXCEPTION-UPDATE{background:#fff4dc;color:#b77900}.act.USER-MANAGEMENT{background:#f0eaff;color:#7c3aed}
.mod{font-weight:700;color:#3d68a8}
.bars{display:flex;align-items:flex-end;gap:12px;height:190px;padding-top:10px}
.bc{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;min-width:0}
.bc b{font:700 12px var(--h);color:var(--navy);margin-bottom:6px}
.bc i{display:block;width:100%;max-width:46px;border-radius:12px 12px 6px 6px;background:linear-gradient(180deg,#38bdf8,#1d4ed8);min-height:6px;transition:.2s}
.bc:hover i{filter:brightness(1.12)}
.bc small{margin-top:9px;font-size:10.5px;font-weight:700;color:var(--mut)}
.bc em{font-style:normal;font-size:9.5px;color:#9db0cc}
.dif{width:100%;border-collapse:collapse;font-size:11.5px;min-width:0}
.dif th{background:#f1f6ff;padding:9px 10px;font-size:10px}
.dif td{padding:9px 10px;border-bottom:1px solid #edf3fa;overflow-wrap:anywhere;vertical-align:top}
.dif tr.chg td{background:#fff9e8}
.dif tr.chg td:nth-child(2){color:#c92f3a}.dif tr.chg td:nth-child(3){color:#26734d;font-weight:700}
.pre{padding:13px 15px;border-radius:14px;background:#0b1b3c;color:#cfe0ff;font:12px/1.7 ui-monospace,Menlo,Consolas,monospace;white-space:pre-wrap;overflow-wrap:anywhere;max-height:240px;overflow:auto}
.tx{padding:13px 15px;border-radius:14px;background:#f4f8ff;font-size:12px;line-height:1.75;color:#41608a;white-space:pre-line;overflow-wrap:anywhere}
.filters.al input[type=date],.filters.al select{min-width:140px}
@media(max-width:750px){.bars{gap:6px}}

</style>
</head>
<body>

@php
    $rt = fn ($name) => Route::has($name) ? route($name) : '#';
    $today = now()->toDateString();
    $cards = [
        ['', 'Total Aktivitas', $totalActivities, 'fa-list-check', '#2563eb', 'Seluruh aktivitas tercatat.', route('audit-logs.index')],
        ['today', 'Aktivitas Hari Ini', $todayActivities, 'fa-calendar-day', '#7c3aed', 'Aktivitas pada hari ini.', route('audit-logs.index', ['date_from' => $today, 'date_to' => $today])],
        ['CREATE', 'CREATE', $createActivities, 'fa-circle-plus', '#16a34a', 'Penambahan data.', route('audit-logs.index', ['action' => 'CREATE'])],
        ['UPDATE', 'UPDATE', $updateActivities, 'fa-pen-to-square', '#f59e0b', 'Perubahan data.', route('audit-logs.index', ['action' => 'UPDATE'])],
        ['DELETE', 'DELETE', $deleteActivities, 'fa-trash', '#e5484d', 'Penghapusan data.', route('audit-logs.index', ['action' => 'DELETE'])],
    ];
    $actions = ['LOGIN', 'LOGOUT', 'CREATE', 'UPDATE', 'DELETE', 'STATUS CHANGE', 'EXCEPTION UPDATE', 'USER MANAGEMENT'];

    $rows = $logs->getCollection()->map(function ($l) {
        $name = $l->user_name ?? optional($l->user)->name ?? 'Pengguna tidak diketahui';
        $subject = $l->subject_type ? class_basename($l->subject_type) . ' #' . ($l->subject_id ?? '-') : null;
        return [
            'id' => $l->id,
            'user_id' => $l->user_id,
            'name' => $name,
            'email' => $l->user_email ?? optional($l->user)->email,
            'role' => $l->user_role,
            'action' => $l->action,
            'module' => $l->module,
            'subject_id' => $l->subject_id,
            'ref' => $l->reference_number,
            'target' => $l->reference_number ?: $subject,
            'ip' => $l->ip_address,
            'desc' => $l->description,
            'old' => $l->old_values,
            'new' => $l->new_values,
            'date' => $l->created_at ? $l->created_at->format('d M Y') : '-',
            'time' => $l->created_at ? $l->created_at->format('H:i:s') : '-',
        ];
    })->values();

    $maxD = max((int) $daily->max('value'), 1);
    $maxM = max((int) ($moduleStats->max() ?? 1), 1);
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
        <a href="{{ route('dashboard') }}" class="ct-link"><i class="fa-solid fa-border-all"></i><span>Dashboard</span></a>
        <a href="{{ $rt('pengiriman.index') }}" class="ct-link"><i class="fa-solid fa-box"></i><span>Pengiriman</span></a>
        <a href="{{ $rt('pelacakan.index') }}" class="ct-link"><i class="fa-solid fa-location-dot"></i><span>Pelacakan</span></a>
        <a href="{{ $rt('exceptions.index') }}" class="ct-link"><i class="fa-solid fa-triangle-exclamation"></i><span>Pengecualian</span></a>
        <a href="{{ $rt('carriers.index') }}" class="ct-link"><i class="fa-solid fa-building"></i><span>Perusahaan Pengiriman</span></a>
        <a href="{{ $rt('fasilitas.index') }}" class="ct-link"><i class="fa-solid fa-warehouse"></i><span>Lokasi &amp; Fasilitas</span></a>
        <a href="{{ $rt('users.index') }}" class="ct-link"><i class="fa-solid fa-users-gear"></i><span>Pengguna &amp; Hak Akses</span></a>
        <a href="{{ $rt('notifications.index') }}" class="ct-link"><i class="fa-solid fa-bell"></i><span>Notifikasi</span></a>
        <a href="{{ $rt('audit-logs.index') }}" class="ct-link on"><i class="fa-solid fa-clipboard-list"></i><span>Audit Log</span></a>
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
            <div class="crumb"><a href="{{ route('dashboard') }}" style="text-decoration:none">Dashboard</a> &nbsp;/&nbsp; Audit Log</div>
            <h1>Riwayat Aktivitas Sistem</h1>
            <p>Pantau seluruh tindakan pengguna yang tercatat di CargoTrace, lengkap dengan data sebelum dan sesudah perubahan.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('audit-logs.export', request()->query()) }}" class="btn"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
        </div>
    </section>

    @if(session('success'))<div class="alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif

    <!-- STATISTIK -->
    <section class="flow">
        @foreach($cards as [$key, $label, $count, $icon, $color, $desc, $url])
            <a href="{{ $url }}#daftar" class="step {{ ($key !== '' && $key !== 'today' && request('action') === $key) ? 'on' : '' }}" style="--c:{{ $color }}">
                <div class="ic"><i class="fa-solid {{ $icon }}"></i></div>
                <b>{{ number_format($count) }}</b>
                <strong>{{ $label }}</strong>
                <small>{{ $desc }}</small>
            </a>
        @endforeach
    </section>

    <!-- GRAFIK -->
    <section class="grid-2">
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-chart-column"></i></span> Aktivitas 7 Hari Terakhir</h2><p>Jumlah aktivitas yang tercatat per hari.</p></div></div>
            <div class="bars">
                @foreach($daily as $d)
                    <div class="bc" title="{{ $d['date'] }}: {{ $d['value'] }} aktivitas">
                        <b>{{ $d['value'] }}</b>
                        <i style="height:{{ max($d['value'] / $maxD * 100, 3) }}%"></i>
                        <small>{{ $d['label'] }}</small><em>{{ $d['date'] }}</em>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="panel">
            <div class="ph"><div><h2><span class="hi"><i class="fa-solid fa-layer-group"></i></span> Aktivitas per Modul</h2><p>Modul dengan aktivitas terbanyak.</p></div></div>
            @forelse($moduleStats as $name => $n)
                <div class="li">
                    <div class="t"><strong>{{ $name }}</strong><span class="pill">{{ number_format($n) }}</span></div>
                    <div class="bar"><i style="width:{{ $n / $maxM * 100 }}%"></i></div>
                </div>
            @empty<div class="nodata">Belum ada data modul.</div>@endforelse
        </div>
    </section>

    <!-- DAFTAR -->
    <section class="panel sect" id="daftar">
        <div class="ph">
            <div>
                <h2><span class="hi"><i class="fa-solid fa-list"></i></span> Riwayat Aktivitas Terbaru</h2>
                <p>{{ number_format($totalActivities) }} aktivitas tercatat. Klik baris untuk melihat detail dan perubahan data.</p>
            </div>
        </div>

        <form action="{{ route('audit-logs.index') }}#daftar" method="GET" class="filters al">
            <input class="grow" type="text" name="search" placeholder="Cari nama, email, atau nomor pengiriman..." value="{{ request('search') }}" aria-label="Pencarian">
            <select name="role" aria-label="Role"><option value="">Semua role</option>
                <option value="admin" @selected(request('role') === 'admin')>Administrator</option>
                <option value="operator" @selected(request('role') === 'operator')>Logistics Operator</option>
                <option value="customer" @selected(request('role') === 'customer')>Customer</option>
            </select>
            <select name="action" aria-label="Aktivitas"><option value="">Semua aktivitas</option>
                @foreach($actions as $a)<option value="{{ $a }}" @selected(request('action') === $a)>{{ $a }}</option>@endforeach
            </select>
            <select name="module" aria-label="Modul"><option value="">Semua modul</option>
                @foreach($modules as $m)<option value="{{ $m }}" @selected(request('module') === $m)>{{ $m }}</option>@endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" title="Tanggal mulai" aria-label="Tanggal mulai">
            <input type="date" name="date_to" value="{{ request('date_to') }}" title="Tanggal akhir" aria-label="Tanggal akhir">
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
            <a href="{{ route('audit-logs.index') }}#daftar" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead><tr><th>Pengguna</th><th>Role</th><th>Aktivitas</th><th>Modul</th><th>Data Terpengaruh</th><th>Tanggal &amp; Waktu</th><th>Detail</th></tr></thead>
                <tbody>
                @forelse($logs as $i => $log)
                    @php
                        $r = $rows[$i];
                        $roleClass = in_array(strtolower((string) $log->user_role), ['admin', 'operator', 'customer'], true) ? strtolower($log->user_role) : 'x';
                    @endphp
                    <tr data-i="{{ $i }}" tabindex="0">
                        <td><div class="ucell">
                            <div class="uav">{{ strtoupper(substr($r['name'], 0, 1)) }}</div>
                            <div><div class="un">{{ $r['name'] }}</div><div class="ue">{{ $r['email'] ?: '-' }}</div></div>
                        </div></td>
                        <td><span class="role {{ $roleClass }}">{{ strtoupper($log->user_role ?? '-') }}</span></td>
                        <td><span class="act {{ str_replace(' ', '-', strtoupper((string) $log->action)) }}">{{ $log->action }}</span></td>
                        <td><span class="mod">{{ $log->module ?: '-' }}</span></td>
                        <td>{{ $r['target'] ?: '-' }}</td>
                        <td style="white-space:nowrap"><strong style="color:var(--navy)">{{ $r['date'] }}</strong><small>{{ $r['time'] }}</small></td>
                        <td><div class="acts"><button type="button" class="ab" data-i="{{ $i }}" title="Detail" aria-label="Detail"><i class="fa-solid fa-eye"></i></button></div></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty"><i class="fa-solid fa-clipboard-list"></i><strong style="color:var(--navy)">Belum ada riwayat aktivitas</strong><br>Catatan aktivitas yang tersimpan akan ditampilkan di sini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pager">
            <span>@if($logs->total() > 0)Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ number_format($logs->total()) }} aktivitas@else Belum ada data aktivitas @endif</span>
            @if($logs->hasPages())<div>{{ $logs->links() }}</div>@endif
        </div>
    </section>

</section>
</main>

<!-- DRAWER DETAIL -->
<div class="veil" id="veil"></div>
<aside class="drawer" id="drawer" aria-hidden="true" style="width:min(560px,100%)">
    <div class="dr-head"><button type="button" class="x" id="drClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button><h3 id="drNum">Detail Aktivitas</h3><p id="drSub"></p></div>
    <div class="dr-body" id="drBody"></div>
</aside>

<script>
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
const ROWS = @json($rows);

const parse = v => {
    if (v === null || v === undefined || v === '') return null;
    if (typeof v === 'string') { try { return JSON.parse(v); } catch (_) { return v; } }
    return v;
};
const isObj = x => x && typeof x === 'object' && !Array.isArray(x);
const cell = v => v === undefined ? '<span class="muted">—</span>' : esc(typeof v === 'object' ? JSON.stringify(v) : String(v));
const kv = (k, v, full) => '<div' + (full ? ' class="full"' : '') + '><small>' + k + '</small><b>' + (v === null || v === '' || v === undefined ? '-' : esc(v)) + '</b></div>';

function diffHtml(o, n) {
    o = parse(o); n = parse(n);
    if (o === null && n === null) return '<div class="tx"><span class="muted">Tidak ada data perubahan yang tercatat untuk aktivitas ini.</span></div>';
    if (isObj(o) || isObj(n)) {
        const a = isObj(o) ? o : {}, b = isObj(n) ? n : {};
        const keys = [...new Set([...Object.keys(a), ...Object.keys(b)])];
        if (keys.length) {
            return '<div class="table-wrap"><table class="dif"><thead><tr><th>Kolom</th><th>Sebelum</th><th>Sesudah</th></tr></thead><tbody>' +
                keys.map(k => { const ch = JSON.stringify(a[k]) !== JSON.stringify(b[k]);
                    return '<tr class="' + (ch ? 'chg' : '') + '"><td><b>' + esc(k) + '</b></td><td>' + cell(a[k]) + '</td><td>' + cell(b[k]) + '</td></tr>'; }).join('') +
                '</tbody></table></div><p style="margin-top:8px;font-size:10.5px;color:#8ca6c4">Baris berwarna kuning adalah kolom yang berubah.</p>';
        }
    }
    const show = v => v === null ? 'Tidak ada data' : (typeof v === 'object' ? JSON.stringify(v, null, 2) : String(v));
    return '<h4 style="font-size:10.5px;color:#8ca6c4;margin:0 0 8px">DATA SEBELUMNYA</h4><div class="pre">' + esc(show(o)) + '</div>' +
           '<h4 style="font-size:10.5px;color:#8ca6c4;margin:14px 0 8px">DATA TERBARU</h4><div class="pre">' + esc(show(n)) + '</div>';
}

function detailHtml(l) {
    const role = String(l.role || '').toLowerCase(), rc = ['admin', 'operator', 'customer'].includes(role) ? role : 'x';
    return '<div class="bdgs" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:18px"><span class="act ' + esc(String(l.action || '').toUpperCase().replace(/ /g, '-')) + '">' + esc(l.action) + '</span><span class="role ' + rc + '">' + esc((l.role || '-').toUpperCase()) + '</span></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-user"></i> PENGGUNA</h4><div class="kv">' + kv('Nama pengguna', l.name) + kv('Email', l.email) + kv('User ID', l.user_id) + kv('IP address', l.ip) + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-bolt"></i> AKTIVITAS</h4><div class="kv">' + kv('Modul', l.module) + kv('ID data', l.subject_id) + kv('Nomor referensi', l.ref) + kv('Waktu aktivitas', l.date + ' · ' + l.time) + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-align-left"></i> KETERANGAN</h4><div class="tx">' + (l.desc ? esc(l.desc) : '<span class="muted">Tidak ada keterangan.</span>') + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-code-compare"></i> PERUBAHAN DATA</h4>' + diffHtml(l.old, l.new) + '</div>';
}

const dr = $('drawer'), veil = $('veil');
function closeDrawer() { dr.classList.remove('open'); veil.classList.remove('open'); dr.setAttribute('aria-hidden', 'true'); }
function showDrawer(i) {
    const l = ROWS[i]; if (!l) return;
    $('drNum').textContent = 'Aktivitas #' + l.id;
    $('drSub').textContent = (l.action || '') + (l.module ? ' · ' + l.module : '');
    $('drBody').innerHTML = detailHtml(l); $('drBody').scrollTop = 0;
    dr.classList.add('open'); veil.classList.add('open'); dr.setAttribute('aria-hidden', 'false');
}

document.querySelectorAll('tbody tr[data-i]').forEach(tr => {
    tr.addEventListener('click', () => showDrawer(+tr.dataset.i));
    tr.addEventListener('keydown', ev => { if (ev.key === 'Enter' && ev.target === tr) showDrawer(+tr.dataset.i); });
});
document.querySelectorAll('.ab').forEach(b => b.addEventListener('click', ev => { ev.stopPropagation(); showDrawer(+b.dataset.i); }));
$('drClose').addEventListener('click', closeDrawer); veil.addEventListener('click', closeDrawer);
document.addEventListener('keydown', ev => { if (ev.key === 'Escape') closeDrawer(); });
</script>
</body>
</html>
