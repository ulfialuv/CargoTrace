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
.btn:disabled{opacity:.5;cursor:not-allowed;transform:none;filter:none}
.btn-light{background:#e8f1ff;color:#1d4ed8;box-shadow:none}
.btn-glass{background:#ffffff22;border:1px solid #ffffff44;box-shadow:none;backdrop-filter:blur(8px)}
.btn-danger{background:#ffe9e9;color:#c92f3a;box-shadow:none}
.btn-warn{background:#fff1db;color:#a35f00;box-shadow:none}
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

/* RINGKASAN PEMBAYARAN */
.pstats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:22px}
.pst{padding:18px;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 10px 26px #2563eb0d}
.pst .ic{display:grid;place-items:center;width:42px;height:42px;border-radius:13px;background:color-mix(in srgb,var(--c) 14%,#fff);color:var(--c);font-size:17px;margin-bottom:12px}
.pst b{display:block;font:800 19px/1.2 var(--h);color:var(--navy);overflow-wrap:anywhere}
.pst strong{display:block;margin-top:8px;font-size:12px;color:var(--navy)}
.pst small{display:block;margin-top:4px;font-size:10.5px;color:var(--mut);line-height:1.5}

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
table{width:100%;border-collapse:collapse;min-width:1080px}
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
.pb-NONE{background:#eef2f9;color:#6c83ab}.pb-PENDING{background:#fff4dc;color:#b77900}.pb-PAID{background:#e8f7ef;color:#26734d}.pb-EXPIRED{background:#ececec;color:#777}.pb-FAILED{background:#ffe9e9;color:#c92f3a}.pb-CANCELLED{background:#eef2f9;color:#8ca6c4}.pb-REFUNDED{background:#f0eaff;color:#7c3aed}
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
.fx-calc p.s{margin:6px 0 20px;font-size:12px;color:#9fbcea;line-height:1.6}
.fx label{display:block;margin:12px 0 6px;font-size:10px;font-weight:700;letter-spacing:.6px;color:#9fbcea}
.fx input,.fx select{width:100%;padding:13px 14px;border:1px solid #ffffff33;border-radius:12px;background:#ffffff14;color:#fff;font:600 13px 'Plus Jakarta Sans',sans-serif}
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
.fx-rows{max-height:340px;overflow-y:auto;padding-right:4px}
.fx-row{display:flex;align-items:center;gap:12px;padding:10px 8px;border-bottom:1px solid #edf3fa;cursor:pointer;border-radius:10px}
.fx-row:hover{background:#f4f9ff}
.fx-code{flex:none;display:grid;place-items:center;width:42px;height:30px;border-radius:8px;background:#e8f1ff;overflow:hidden}
.flag{width:100%;height:100%;object-fit:cover;display:block}
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
.hint{font-size:11.5px;color:var(--mut);margin-bottom:10px;line-height:1.6}
.lock{display:flex;gap:12px;align-items:flex-start;padding:13px 14px;margin-bottom:12px;border-radius:14px;background:#eef2f9;color:#41608a}
.lock>i{flex:none;display:grid;place-items:center;width:32px;height:32px;border-radius:10px;background:#dfe7f5;color:#5b6f90;font-size:13px}
.lock b{display:block;font-size:12.5px;color:var(--navy)}.lock small{display:block;margin-top:3px;font-size:11px;line-height:1.6}
.lock.warn{background:#fff6e5;color:#8a5a00}.lock.warn>i{background:#ffe7b8;color:#a35f00}

/* MODAL FORM */
.modal{position:fixed;inset:0;z-index:2100;display:none;place-items:center;padding:20px;background:#061533a8;backdrop-filter:blur(4px);overflow-y:auto}
.modal.open{display:grid}
#paym,#reopenm{z-index:2300}
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

/* PEMBAYARAN (GATEWAY SANDBOX) */
.pm-body{padding:24px 28px 28px;max-height:calc(100vh - 150px);overflow-y:auto}
.pm-sec{font-size:10.5px;font-weight:800;letter-spacing:1px;color:#8ca6c4;margin:18px 0 10px}
.pm-sec:first-child{margin-top:0}
.pm-grp{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:var(--navy);margin:12px 0 8px}.pm-grp i{color:var(--blue)}
.pm-opts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
.mth{position:relative;display:block;padding:12px 14px;border:1.5px solid var(--line);border-radius:14px;cursor:pointer;transition:.15s;background:#fbfdff}
.mth input{position:absolute;opacity:0;pointer-events:none}
.mth:hover{border-color:#9fc2f7}
.mth.on{border-color:var(--blue);background:#eef5ff;box-shadow:0 0 0 3px #2563eb1a}
.mth:focus-within{outline:2px solid var(--blue);outline-offset:2px}
.mth b{display:block;font-size:12.5px;color:var(--navy)}.mth small{display:block;margin-top:3px;font-size:10.5px;color:var(--mut)}
.bill{border:1px solid var(--line);border-radius:16px;padding:6px 16px;background:#f9fbff}
.bill>div{display:flex;justify-content:space-between;gap:12px;padding:9px 0;border-bottom:1px dashed #dce8f8;font-size:12px;color:#41608a}
.bill>div:last-child{border:0}
.bill b{color:var(--navy);text-align:right;overflow-wrap:anywhere}
.bill .grand{font-size:13.5px}.bill .grand b{font:800 18px var(--h);color:var(--blue)}
.pm-go{width:100%;margin-top:16px;padding:14px}
.pm-note{margin-top:12px;font-size:11px;line-height:1.7;color:var(--mut)}
.pm-timer{text-align:center;padding:16px;border-radius:16px;background:linear-gradient(135deg,#fff4dc,#ffe9b8);color:#8a5a00;margin-bottom:14px}
.pm-timer small{font-size:11px;font-weight:600}
.pm-timer b{display:block;font:800 28px var(--h);letter-spacing:1px;margin:4px 0}
.pm-timer em{font-style:normal;font-size:11px}
.pm-code{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin:14px 0;padding:14px 16px;border-radius:14px;background:#eef5ff;border:1px dashed #9fc2f7}
.pm-code small{width:100%;font-size:10px;font-weight:700;letter-spacing:.8px;color:#6c83ab}
.pm-code b{flex:1;min-width:0;font:700 18px ui-monospace,Menlo,Consolas,monospace;letter-spacing:1px;color:var(--navy);overflow-wrap:anywhere}
.pm-qr{width:190px;height:190px;margin:14px auto 6px;padding:10px;border:1px solid var(--line);border-radius:16px;background:#fff}
.pm-qr svg{width:100%;height:100%;display:block}
.pm-qrcap{text-align:center;font-size:10.5px;color:var(--mut)}
.pm-how{margin:10px 0 0 18px;font-size:12px;line-height:1.9;color:#41608a}
.sbx{margin-top:16px;padding:14px 16px;border-radius:16px;background:#f3eefe;border:1px dashed #b79cf0;color:#5b2fb8}
.sbx>b{font-size:12px}
.sbx small{display:block;margin:4px 0 10px;color:#7c5bc7;line-height:1.6;font-size:11px}
.sbx-b{display:flex;gap:8px;flex-wrap:wrap}.sbx-b .btn{flex:1;padding:10px}
.pm-ok{text-align:center;padding:18px;border-radius:18px;background:linear-gradient(135deg,#e8f7ef,#d3f1e1);color:#187344;margin-bottom:14px}
.pm-ok i{font-size:34px}.pm-ok b{display:block;font:800 18px var(--h);margin-top:8px}.pm-ok small{font-size:11.5px}
.pm-rf{display:flex;gap:8px;flex-wrap:wrap}.pm-rf input{flex:1;min-width:180px;padding:11px 12px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.hrow{display:flex;justify-content:space-between;gap:10px;align-items:center;padding:10px 0;border-bottom:1px solid #edf3fa;font-size:11.5px;color:#41608a}
.hrow:last-child{border:0}.hrow strong{display:block;color:var(--navy);font-size:12px}.hrow small{font-size:10.5px;color:#8ca6c4}
.hrow .r{text-align:right;flex:none}

/* TOAST */
.toast{position:fixed;left:50%;bottom:26px;transform:translate(-50%,30px);z-index:3000;display:flex;align-items:center;gap:10px;max-width:min(520px,92vw);padding:13px 18px;border-radius:14px;font-weight:600;font-size:12.5px;line-height:1.5;opacity:0;pointer-events:none;transition:.25s;box-shadow:0 16px 40px #0b2a6b44}
.toast.show{opacity:1;transform:translate(-50%,0)}
.toast.ok{background:#0f5132;color:#d1f5e0}.toast.err{background:#7a1f24;color:#ffe3e5}

/* AKSI BARIS, TIMELINE */
.acts{display:flex;gap:6px}
.ab{display:grid;place-items:center;width:32px;height:32px;border:0;border-radius:10px;background:#e8f1ff;color:#1d4ed8;cursor:pointer;font-size:12px;transition:.15s}
.ab:hover{background:#1d4ed8;color:#fff}.ab.del{background:#ffe9e9;color:#c92f3a}.ab.del:hover{background:#c92f3a;color:#fff}
.ab.pay{background:#e8f7ef;color:#26734d}.ab.pay:hover{background:#26734d;color:#fff}
.tl{position:relative;padding-left:26px}
.tl::before{content:"";position:absolute;left:8px;top:6px;bottom:6px;width:2px;background:#dbe6f8}
.ti{position:relative;padding-bottom:18px}.ti:last-child{padding-bottom:0}
.ti::before{content:"";position:absolute;left:-23px;top:3px;width:12px;height:12px;border-radius:50%;background:#fff;border:3px solid #b9d2f7}
.ti.last::before{border-color:var(--blue);box-shadow:0 0 0 5px #2563eb22}
.ti b{display:block;font-size:12.5px;color:var(--navy)}.ti span{display:block;margin-top:3px;font-size:11.5px;color:#41608a}.ti small{display:block;margin-top:3px;font-size:10.5px;color:#8ca6c4;line-height:1.5}
.lastloc{display:flex;gap:12px;align-items:center;padding:14px;border-radius:16px;background:linear-gradient(135deg,#eaf3ff,#dbeaff);margin-bottom:16px}
.lastloc i{display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:var(--blue);color:#fff;flex:none}
.lastloc b{display:block;font-size:13px;color:var(--navy)}.lastloc small{font-size:11px;color:var(--mut);line-height:1.5}

/* LACAK PAKET (PETA) */
.tkm{position:fixed;inset:0;z-index:2200;display:none;place-items:center;padding:18px;background:#061533b8;backdrop-filter:blur(4px)}
.tkm.open{display:grid}
.tkbox{width:min(1280px,100%);height:min(840px,100%);display:flex;flex-direction:column;background:#fff;border-radius:26px;overflow:hidden;box-shadow:0 30px 70px #0b2a6b66}
.tkhead{display:flex;align-items:center;gap:14px;padding:16px 24px;color:#fff;background:linear-gradient(120deg,#061533,#1747b8)}
.tkhead>div{flex:1;min-width:0}
.tkhead h3{font:800 19px var(--h)}.tkhead p{margin-top:4px;font-size:12px;color:#bcd3f7;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tkhead .x{display:grid;place-items:center;width:36px;height:36px;flex:none;border:1px solid #ffffff44;border-radius:10px;background:#ffffff1c;color:#fff;cursor:pointer}
.tkbody{flex:1;min-height:0;display:grid;grid-template-columns:minmax(0,1.7fr) minmax(330px,1fr)}
.tkmapcol{display:flex;flex-direction:column;min-width:0;min-height:0}
.tktools{display:flex;flex-wrap:wrap;align-items:center;gap:8px 18px;padding:11px 16px;border-bottom:1px solid #edf3fa;background:#fff}
.tg{display:flex;align-items:center;flex-wrap:wrap;gap:6px}
.tgl{font-size:10px;font-weight:700;letter-spacing:.9px;color:#8ca6c4;margin-right:3px}
.chip{display:inline-flex;align-items:center;gap:7px;padding:8px 13px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;color:#3d68a8;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.15s}
.chip:hover{background:#eef6ff}
.chip.on{background:var(--blue);border-color:var(--blue);color:#fff;box-shadow:0 6px 16px #2563eb44}
.msearch{position:relative;margin-left:auto;min-width:210px;flex:1;max-width:300px}
.msearch i{position:absolute;top:12px;left:13px;font-size:12px;color:#8ca6c4}
.msearch input{width:100%;padding:9px 12px 9px 36px;border:1px solid #dce8f8;border-radius:10px;background:#fbfdff;font:12px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.msearch input:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}
.tkmapwrap{position:relative;flex:1;min-height:340px;isolation:isolate}
#tkMap{position:absolute;inset:0;background:#dfeafb}
.tkside{overflow-y:auto;padding:20px 22px;border-left:1px solid var(--line)}
.basebox{position:absolute;top:12px;right:12px;z-index:700;display:flex;gap:4px;padding:5px;background:#ffffffee;border:1px solid var(--line);border-radius:14px;box-shadow:0 8px 22px #0b2a6b2a;backdrop-filter:blur(8px)}
.bb{display:flex;flex-direction:column;align-items:center;gap:3px;min-width:62px;padding:7px 8px;border:0;border-radius:10px;background:transparent;color:#3d68a8;font:600 10px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.15s}
.bb i{font-size:15px}
.bb:hover{background:#eef6ff}
.bb.on{background:var(--blue);color:#fff;box-shadow:0 6px 14px #2563eb55}
.clockbox{display:none;position:absolute;top:12px;left:50%;transform:translateX(-50%);z-index:640;gap:16px;padding:9px 18px;background:#061533ee;color:#fff;border-radius:14px;box-shadow:0 8px 22px #0b2a6b55}
.clockbox.show{display:flex}
.clockbox div{text-align:center}
.clockbox b{display:block;font:800 17px var(--h);letter-spacing:.5px}
.clockbox small{font-size:9.5px;color:#9fbcea}
.tk-legend{position:absolute;left:12px;bottom:22px;z-index:500;display:flex;flex-wrap:wrap;gap:12px;padding:8px 13px;background:#ffffffee;border:1px solid var(--line);border-radius:12px;font-size:11px;color:#3d68a8;font-weight:600;box-shadow:0 8px 20px #0b2a6b22;max-width:calc(100% - 24px)}
.tk-legend span{display:flex;align-items:center;gap:6px}.tk-legend i.d{display:inline-block;width:10px;height:10px;border-radius:50%}
.tk-legend i.ln{display:inline-block;width:18px;border-top:3px solid #f59e0b}.tk-legend i.ln.ds{border-top:3px dashed #2f8cf5}
.tk-empty{position:absolute;inset:0;z-index:600;display:none;place-items:center;background:#ffffffc8;text-align:center;padding:20px}
.tk-empty.show{display:grid}
.tk-empty div{max-width:360px;padding:24px;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 16px 40px #0b2a6b22;line-height:1.7;color:var(--mut)}
.tk-empty strong{display:block;color:var(--navy);font-size:14px}
.tk-msg{font-size:11px;color:#c92f3a;font-weight:600}
.pw{position:relative;width:34px;height:34px;display:grid;place-items:center}
.pin{display:grid;place-items:center;width:34px;height:34px;border-radius:50%;border:3px solid #fff;color:#fff;font-size:14px;box-shadow:0 4px 14px #0005}
.pin.big{width:42px;height:42px;font-size:17px}
.pin.fleet{animation:pulse 2s infinite}
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
.pop{min-width:180px;font:12px/1.7 'Plus Jakarta Sans',sans-serif;color:#365775}.pop strong{display:block;font-size:13px;color:var(--navy)}.pop small{color:#8ca6c4}
.pop .veh{display:inline-flex;align-items:center;gap:6px;margin:2px 0;padding:2px 9px;border-radius:12px;background:#eef4ff;color:#1d4ed8;font-weight:700}
.pop .ctx{display:block;margin-top:4px;padding-top:4px;border-top:1px dashed #dce8f8}
.stt-big{font:800 16px var(--h);color:var(--navy)}

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
@media(max-width:1100px){.grid-2{grid-template-columns:1fr}.content{padding:20px}details.adv .row{grid-template-columns:1fr 1fr}.fx-feat{grid-template-columns:1fr 1fr}.pstats{grid-template-columns:1fr 1fr}}
@media(max-width:900px){.tkm{padding:0}.tkbox{border-radius:0;height:100%}.tkbody{grid-template-columns:1fr;overflow-y:auto;display:block}.tkmapcol{min-height:auto}.tkmapwrap{flex:none;height:440px}.tkside{border-left:0;overflow:visible}.basebox{left:12px;right:12px;justify-content:space-between}.bb{min-width:0;flex:1}.bb span{display:none}.clockbox{top:70px}.msearch{max-width:none;margin-left:0}}
@media(max-width:750px){.main{margin-left:72px}.content{padding:16px 12px 30px}.hero{padding:26px 22px}.hero h1{font-size:22px}.flow{grid-template-columns:1fr 1fr}.panel{padding:18px}.fgrid{grid-template-columns:1fr}.fx-calc,.fx-list{padding:20px}.fx .pair{grid-template-columns:1fr}.swap{width:100%;height:38px}.pm-opts{grid-template-columns:1fr}.pm-body{padding:20px 18px 24px}.pstats{grid-template-columns:1fr}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>
<body>

@php
    use Carbon\Carbon;
    $fmt = fn ($v, $f = 'd M Y H:i') => $v ? Carbon::parse($v)->format($f) : '-';
    $rp = fn ($v) => 'Rp ' . number_format((int) $v, 0, ',', '.');
    $modeIcon = ['udara' => 'fa-plane', 'laut' => 'fa-ship', 'darat' => 'fa-truck'];
    $flow = [
        ['CREATED',    'Created',    'fa-file-circle-plus', '#64748b', 'Pengiriman baru dibuat.'],
        ['BOOKED',     'Booked',     'fa-calendar-check',   '#2563eb', 'Pengiriman sudah dijadwalkan.'],
        ['PICKED_UP',  'Picked Up',  'fa-hand-holding-box', '#7c3aed', 'Barang sudah dijemput.'],
        ['IN_TRANSIT', 'In Transit', 'fa-truck-fast',       '#f59e0b', 'Barang sedang dalam perjalanan.'],
        ['DELIVERED',  'Delivered',  'fa-circle-check',     '#16a34a', 'Barang sudah diterima.'],
    ];
    $payLabel = ['NONE' => 'Belum ditagih', 'PENDING' => 'Menunggu bayar', 'PAID' => 'Lunas', 'EXPIRED' => 'Kedaluwarsa', 'FAILED' => 'Gagal', 'CANCELLED' => 'Dibatalkan', 'REFUNDED' => 'Refund'];
    $totalAll = max((int) $statusCounts->sum(), 0);
    $rows = $shipments->getCollection()->map(fn ($s) => [
        'id' => $s->id, 'number' => $s->shipment_number, 'name' => $s->shipment_name, 'customer' => $s->customer_name,
        'cargo' => $s->cargo_type, 'weight' => $s->weight, 'volume' => $s->volume, 'carrier' => $s->carrier,
        'mode' => $s->transport_type, 'origin' => $s->origin, 'destination' => $s->destination,
        'vnum' => $s->vehicle_number ?? $s->vessel_name ?? $s->flight_number ?? $s->plate_number ?? null,
        'etd' => $fmt($s->estimated_departure), 'eta' => $fmt($s->estimated_arrival),
        'days' => ($s->estimated_departure && $s->estimated_arrival) ? max(0, (int) Carbon::parse($s->estimated_departure)->diffInDays(Carbon::parse($s->estimated_arrival))) : null,
        'status' => $s->status, 'notes' => $s->notes,
        'etd_raw' => $s->estimated_departure ? Carbon::parse($s->estimated_departure)->format('Y-m-d\TH:i') : '',
        'eta_raw' => $s->estimated_arrival ? Carbon::parse($s->estimated_arrival)->format('Y-m-d\TH:i') : '',
        'state' => $states[$s->id] ?? null,
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
            <p>Kelola seluruh data pengiriman, tagihan &amp; pembayaran, lacak posisi barang di peta, dan lihat kurs mata uang seluruh negara dalam satu halaman.</p>
        </div>
        <div class="hero-actions">
            <a href="#transaksi" class="btn btn-glass"><i class="fa-solid fa-credit-card"></i> Transaksi</a>
            <a href="#kurs" class="btn btn-glass"><i class="fa-solid fa-money-bill-transfer"></i> Kurs Mata Uang</a>
            <button type="button" class="btn" id="openCreate"><i class="fa-solid fa-plus"></i> Tambah Pengiriman</button>
        </div>
    </section>

    @if(session('success'))<div class="alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if($errors->any() && !$openModal)<div class="alert err"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>@endif

    <!-- STATUS PENGIRIMAN -->
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

    <!-- RINGKASAN PEMBAYARAN -->
    <section class="pstats">
        <a href="{{ route('pengiriman.index', ['pay' => 'PAID']) }}#daftar" class="pst" style="--c:#16a34a;text-decoration:none">
            <div class="ic"><i class="fa-solid fa-sack-dollar"></i></div>
            <b>{{ $rp($payStats['paid_total']) }}</b><strong>Pendapatan Lunas</strong><small>{{ $payStats['paid_count'] }} transaksi berhasil</small>
        </a>
        <a href="{{ route('pengiriman.index', ['pay' => 'PENDING']) }}#daftar" class="pst" style="--c:#f59e0b;text-decoration:none">
            <div class="ic"><i class="fa-solid fa-hourglass-half"></i></div>
            <b>{{ $rp($payStats['pending_total']) }}</b><strong>Menunggu Pembayaran</strong><small>{{ $payStats['pending_count'] }} tagihan aktif</small>
        </a>
        <a href="{{ route('pengiriman.index', ['pay' => 'UNPAID']) }}#daftar" class="pst" style="--c:#2563eb;text-decoration:none">
            <div class="ic"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <b>{{ $payStats['unpaid'] }} pengiriman</b><strong>Belum Lunas</strong><small>Belum ada pembayaran berstatus lunas</small>
        </a>
        <div class="pst" style="--c:#7c3aed">
            <div class="ic"><i class="fa-solid fa-rotate-left"></i></div>
            <b>{{ $rp($payStats['refunded_total']) }}</b><strong>Total Refund</strong><small>{{ $payStats['refunded_count'] }} transaksi di-refund</small>
        </div>
    </section>

    <!-- DAFTAR PENGIRIMAN -->
    <section class="panel sect" id="daftar">
        <div class="ph">
            <div>
                <h2><span class="hi"><i class="fa-solid fa-list"></i></span> Daftar Pengiriman</h2>
                <p>{{ $totalAll }} pengiriman tersimpan. Klik baris untuk detail, tombol <i class="fa-solid fa-location-dot"></i> untuk melacak, atau <i class="fa-solid fa-credit-card"></i> untuk pembayaran.</p>
            </div>
            <button type="button" class="btn" id="openCreate2"><i class="fa-solid fa-plus"></i> Tambah</button>
        </div>

        <form action="{{ route('pengiriman.index') }}#daftar" method="GET">
            <div class="filters">
                <input class="grow" type="text" name="search" placeholder="Cari nomor resi, pelanggan, atau nama barang..." value="{{ request('search') }}" aria-label="Pencarian">
                <select name="status" aria-label="Status"><option value="">Semua status</option>
                    @foreach($flow as [$key, $label])<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach
                </select>
                <select name="pay" aria-label="Status pembayaran"><option value="">Semua pembayaran</option>
                    <option value="PAID" @selected(request('pay') === 'PAID')>Lunas</option>
                    <option value="PENDING" @selected(request('pay') === 'PENDING')>Menunggu bayar</option>
                    <option value="UNPAID" @selected(request('pay') === 'UNPAID')>Belum lunas</option>
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
                <thead><tr><th>No. Resi</th><th>Pelanggan</th><th>Perusahaan</th><th>Asal → Tujuan</th><th>Barang</th><th>Jadwal</th><th>Status</th><th>Pembayaran</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($shipments as $i => $s)
                    @php $ps = $states[$s->id]['pay']['status'] ?? 'NONE'; @endphp
                    <tr data-i="{{ $i }}" tabindex="0">
                        <td><span class="num">{{ $s->shipment_number }}</span><small>{{ $s->shipment_name }}</small></td>
                        <td>{{ $s->customer_name }}</td>
                        <td><span class="tmode"><i class="fa-solid {{ $modeIcon[$s->transport_type] ?? 'fa-box' }}"></i></span>{{ $s->carrier }}</td>
                        <td><div class="route">{{ $s->origin }} <i class="fa-solid fa-arrow-right"></i> {{ $s->destination }}</div></td>
                        <td>{{ $s->cargo_type }}<small>{{ rtrim(rtrim(number_format((float) $s->weight, 2, ',', '.'), '0'), ',') }} kg</small></td>
                        <td>{{ $fmt($s->estimated_departure, 'd M') }} → {{ $fmt($s->estimated_arrival, 'd M Y') }}</td>
                        <td class="tstatus"><span class="status st-{{ $s->status }}">{{ str_replace('_', ' ', $s->status) }}</span></td>
                        <td class="tpay"><span class="status pb-{{ $ps }}">{{ $payLabel[$ps] ?? $ps }}</span></td>
                        <td><div class="acts">
                            <button type="button" class="ab" data-act="track" data-i="{{ $i }}" title="Lacak" aria-label="Lacak"><i class="fa-solid fa-location-dot"></i></button>
                            <button type="button" class="ab pay" data-act="pay" data-i="{{ $i }}" title="Pembayaran" aria-label="Pembayaran"><i class="fa-solid fa-credit-card"></i></button>
                            <button type="button" class="ab" data-act="edit" data-i="{{ $i }}" title="Edit" aria-label="Edit"><i class="fa-solid fa-pen"></i></button>
                            <button type="button" class="ab del" data-act="del" data-i="{{ $i }}" title="Hapus" aria-label="Hapus"><i class="fa-solid fa-trash"></i></button>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="empty"><i class="fa-solid fa-box-open"></i><strong style="color:var(--navy)">Belum ada pengiriman</strong><br>Ubah filter atau tambahkan pengiriman pertama kamu.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pager">
            <span>@if($shipments->total() > 0)Menampilkan {{ $shipments->firstItem() }}–{{ $shipments->lastItem() }} dari {{ $shipments->total() }} pengiriman@else Belum ada data pengiriman @endif</span>
            @if($shipments->hasPages())<div>{{ $shipments->links() }}</div>@endif
        </div>
    </section>

    <!-- RIWAYAT TRANSAKSI -->
    <section class="panel sect" id="transaksi">
        <div class="ph">
            <div>
                <h2><span class="hi"><i class="fa-solid fa-receipt"></i></span> Riwayat Transaksi</h2>
                <p>8 tagihan terbaru dari payment gateway{{ $sandbox ? ' (mode sandbox, tidak ada uang nyata yang berpindah)' : '' }}.</p>
            </div>
        </div>
        <div class="table-wrap">
            <table style="min-width:780px">
                <thead><tr><th>Invoice</th><th>No. Resi</th><th>Metode</th><th>Total</th><th>Status</th><th>Dibuat</th></tr></thead>
                <tbody>
                @forelse($recentPayments as $t)
                    <tr style="cursor:default">
                        <td><span class="num">{{ $t['invoice'] }}</span></td>
                        <td>{{ $t['number'] }}</td>
                        <td>{{ $t['method_label'] }}</td>
                        <td><strong style="color:var(--navy)">{{ $rp($t['total']) }}</strong></td>
                        <td><span class="status pb-{{ $t['status'] }}">{{ $payLabel[$t['status']] ?? $t['status'] }}</span></td>
                        <td>{{ $t['time'] }}</td>
                    </tr>
                @empty
                    <tr style="cursor:default"><td colspan="6" class="empty"><i class="fa-solid fa-receipt"></i><strong style="color:var(--navy)">Belum ada transaksi</strong><br>Buka detail pengiriman lalu pilih Pembayaran untuk membuat tagihan pertama.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- PERUSAHAAN, ASAL-TUJUAN, BARANG, JADWAL -->
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

    <!-- KURS MATA UANG SELURUH NEGARA -->
    <section class="panel fx-wrap sect" id="kurs">
        <div class="fx-calc fx">
            <h2><i class="fa-solid fa-money-bill-transfer"></i> Kalkulator Kurs</h2>
            <p class="s">Pilih negara asal dan negara tujuan, misalnya Jerman ke Indonesia, lalu lihat berapa rupiah hasilnya. Mendukung seluruh negara.</p>
            <label for="fxAmt">JUMLAH</label>
            <input id="fxAmt" type="number" min="0" step="any" value="1" inputmode="decimal">
            <div class="pair">
                <div><label for="fxFrom">DARI NEGARA</label><select id="fxFrom"></select></div>
                <button type="button" class="swap" id="fxSwap" aria-label="Tukar negara"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
                <div><label for="fxTo">KE NEGARA</label><select id="fxTo"></select></div>
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
                <div class="fx-search"><i class="fa-solid fa-magnifying-glass"></i><input id="fxQ" type="text" placeholder="Cari negara / mata uang, mis. Jerman, Germany, EUR" aria-label="Cari negara atau mata uang"></div>
                <button type="button" class="btn btn-light" id="fxRefresh" aria-label="Muat ulang"><i class="fa-solid fa-rotate"></i></button>
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
    <div class="dr-head"><button type="button" class="x" id="drClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button><h3 id="drNum">—</h3><p id="drSub"></p></div>
    <div class="dr-body" id="drBody"></div>
    <div class="dr-foot" id="drFoot"></div>
</aside>

<!-- MODAL LACAK PAKET -->
<div class="tkm" id="tkm" aria-hidden="true">
    <div class="tkbox" role="dialog" aria-modal="true" aria-labelledby="tkNum">
        <div class="tkhead">
            <div><h3 id="tkNum">—</h3><p id="tkSub"></p></div>
            <span class="status" id="tkStatus"></span>
            <button type="button" class="x" id="tkClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="tkbody">
            <div class="tkmapcol">
                <div class="tktools">
                    <div class="tg" id="tkViewGroup">
                        <span class="tgl">TAMPILAN</span>
                        <button type="button" class="chip on" data-view="loc"><i class="fa-solid fa-location-dot"></i> Lokasi</button>
                        <button type="button" class="chip" data-view="wx"><i class="fa-solid fa-cloud-sun-rain"></i> Cuaca</button>
                        <button type="button" class="chip" data-view="time"><i class="fa-regular fa-clock"></i> Waktu</button>
                    </div>
                    <div class="tg" id="tkRadarGroup" style="display:none">
                        <button type="button" class="chip on" id="tkRadarBtn"><i class="fa-solid fa-satellite-dish"></i> Radar Hujan</button>
                    </div>
                    <div class="msearch"><i class="fa-solid fa-magnifying-glass"></i><input type="text" id="tkQ" placeholder="Cari tempat / desa / jalan lalu Enter" aria-label="Cari tempat di peta"></div>
                    <span class="tk-msg" id="tkMsg"></span>
                </div>
                <div class="tkmapwrap">
                    <div id="tkMap"></div>
                    <div class="basebox" id="tkBaseBox" role="group" aria-label="Jenis peta">
                        <button class="bb on" data-base="default" type="button" title="Peta standar"><i class="fa-solid fa-map"></i><span>Default</span></button>
                        <button class="bb" data-base="sat" type="button" title="Citra satelit"><i class="fa-solid fa-satellite"></i><span>Satelit</span></button>
                        <button class="bb" data-base="terrain" type="button" title="Kontur medan"><i class="fa-solid fa-mountain"></i><span>Medan</span></button>
                        <button class="bb" data-base="transit" type="button" title="Jalur transportasi umum"><i class="fa-solid fa-train-subway"></i><span>Transportasi</span></button>
                        <button class="bb" data-base="traffic" type="button" title="Kondisi lalu lintas"><i class="fa-solid fa-traffic-light"></i><span>Lalu lintas</span></button>
                    </div>
                    <div class="clockbox" id="tkClock">
                        <div><b id="ckLocal">--:--:--</b><small id="ckZone">Waktu Anda</small></div>
                        <div><b id="ckUtc">--:--:--</b><small>UTC</small></div>
                    </div>
                    <div class="tk-empty" id="tkEmpty"><div><strong>Lokasi belum bisa ditemukan</strong>Nama kota asal/tujuan tidak dikenali peta. Isi asal &amp; tujuan dengan nama kota/kecamatan yang jelas, atau tambahkan event tracking berkoordinat.</div></div>
                    <div class="tk-legend" id="tkLegend"></div>
                </div>
            </div>
            <div class="tkside" id="tkSide"></div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH / EDIT -->
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
                    </select><div class="hint" id="f_sthint" style="margin:6px 0 0"></div>{!! $err('status') !!}</div>
                <div class="full"><label for="f_nt">CATATAN (OPSIONAL)</label><textarea id="f_nt" name="notes">{{ old('notes') }}</textarea></div>
            </div>
            <div class="mfoot">
                <button type="button" class="btn btn-light" id="mCancel">Batal</button>
                <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> <span id="mSaveTx">{{ $formMode === 'edit' ? 'Simpan Perubahan' : 'Simpan Pengiriman' }}</span></button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PEMBAYARAN (GATEWAY SANDBOX) -->
<div class="modal" id="paym" aria-hidden="true">
    <div class="mbox" style="width:min(640px,100%)" role="dialog" aria-modal="true" aria-labelledby="pmTitle">
        <div class="mhead">
            <div><h3 id="pmTitle">Pembayaran</h3><p id="pmSub"></p></div>
            <button type="button" class="x" id="pmClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="pm-body" id="pmBody"></div>
    </div>
</div>

<!-- MODAL BUKA KEMBALI STATUS TERMINAL (PROSEDUR KHUSUS) -->
<div class="modal" id="reopenm" aria-hidden="true">
    <div class="mbox" style="width:min(520px,100%)" role="dialog" aria-modal="true" aria-labelledby="rpTitle">
        <div class="mhead">
            <div><h3 id="rpTitle">Buka Kembali Status Terminal</h3><p>Prosedur khusus · <span id="rpNum"></span></p></div>
            <button type="button" class="x" id="rpClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="fgrid" style="grid-template-columns:1fr">
            <div class="lock warn" style="margin:0"><i class="fa-solid fa-triangle-exclamation"></i><div><b>Tindakan sensitif</b><small>DELIVERED adalah status terminal. Membukanya kembali hanya boleh dilakukan Administrator, wajib disertai alasan, dan dicatat permanen di riwayat status.</small></div></div>
            <div><label for="rpTarget">KEMBALIKAN KE STATUS</label><select id="rpTarget"></select></div>
            <div><label for="rpReason">ALASAN (MINIMAL 15 KARAKTER)</label><textarea id="rpReason" placeholder="Contoh: Barang ditolak penerima, dikembalikan untuk pengiriman ulang."></textarea></div>
            <div><label for="rpConfirm">KETIK NO. RESI UNTUK KONFIRMASI</label><input id="rpConfirm" autocomplete="off"></div>
            <div class="ferr" id="rpErr" role="alert"></div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-light" id="rpCancel">Batal</button>
            <button type="button" class="btn btn-warn" id="rpGo"><i class="fa-solid fa-lock-open"></i> Buka Kembali</button>
        </div>
    </div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const $ = id => document.getElementById(id);
const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));

/* ===== DATA & HELPER ===== */
const ROWS = @json($rows), BASE = @json(url('/pengiriman')), CSRF = @json(csrf_token()), STORE = @json(route('pengiriman.store'));
const FORM = { mode: @json($formMode), id: @json(old('_id')) };
const CAN_ADMIN = @json($canAdmin), SANDBOX = @json($sandbox), PAYST = @json($payLabel);
const FLOW = [['CREATED','Created','fa-file-circle-plus'],['BOOKED','Booked','fa-calendar-check'],['PICKED_UP','Picked','fa-hand-holding-box'],['IN_TRANSIT','Transit','fa-truck-fast'],['DELIVERED','Delivered','fa-check']];
const STT = { CREATED:'Pesanan dibuat', BOOKED:'Pengiriman sudah dijadwalkan', PICKED_UP:'Paket sudah dijemput', IN_TRANSIT:'Paket sedang dalam perjalanan', DELIVERED:'Paket sudah diterima' };
const MODE = { udara:'Udara (pesawat)', laut:'Laut (kapal)', darat:'Darat (truk)' };
const VEH = { udara:{ name:'Pesawat', icon:'fa-plane', color:'#2563eb' }, laut:{ name:'Kapal', icon:'fa-ship', color:'#0f9d9a' }, darat:{ name:'Truk', icon:'fa-truck-fast', color:'#f59e0b' } };
const vehOf = m => VEH[m] || VEH.darat;
const nice = v => esc(String(v || '').replace(/_/g, ' '));
const p2 = n => String(n).padStart(2, '0');
const nf = (v, max) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: max }).format(v);
const rupiah = v => 'Rp ' + nf(v || 0, 0);
const fmtDT = iso => { const d = new Date(iso); return isNaN(d) ? '-' : d.toLocaleString('id-ID', { dateStyle:'medium', timeStyle:'short' }); };
let DIRTY = false;   // ada perubahan lewat AJAX -> muat ulang halaman saat panel ditutup supaya ringkasan ikut diperbarui

/* ===== TOAST ===== */
let toastT = null;
function toast(msg, ok = true) {
    const t = $('toast');
    t.className = 'toast show ' + (ok ? 'ok' : 'err');
    t.innerHTML = '<i class="fa-solid ' + (ok ? 'fa-circle-check' : 'fa-circle-exclamation') + '"></i><span>' + esc(msg) + '</span>';
    clearTimeout(toastT); toastT = setTimeout(() => t.classList.remove('show'), 4500);
}

/* ===== API AKSI (PUT /pengiriman/{id} dengan _action) ===== */
const payBadge = p => { const k = p ? p.status : 'NONE'; return '<span class="status pb-' + k + '">' + esc(PAYST[k] || k) + '</span>'; };
const stBadge = s => '<span class="status st-' + esc(s) + '">' + nice(s) + '</span>';
function refreshRow(i) {
    const tr = document.querySelector('tbody tr[data-i="' + i + '"]'), s = ROWS[i];
    if (!tr || !s) return;
    const a = tr.querySelector('.tstatus'), b = tr.querySelector('.tpay');
    if (a) a.innerHTML = stBadge(s.status);
    if (b) b.innerHTML = payBadge(s.state && s.state.pay);
}
async function api(i, body) {
    const s = ROWS[i];
    const r = await fetch(BASE + '/' + encodeURIComponent(s.id), {
        method: 'PUT',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF, 'X-Requested-With':'XMLHttpRequest' },
        body: JSON.stringify(body)
    });
    let j = null; try { j = await r.json(); } catch (_) {}
    if (!r.ok || !j || !j.state) {
        let m = j && j.errors ? Object.values(j.errors)[0] : null; m = Array.isArray(m) ? m[0] : m;
        throw new Error(m || (j && j.message) || ('Permintaan gagal (HTTP ' + r.status + ').'));
    }
    s.state = j.state; s.status = j.state.status; DIRTY = true; refreshRow(i);
    return j;
}

/* ===== MODAL TAMBAH / EDIT ===== */
const modal = $('modal'), shipForm = $('shipForm');

/* Opsi status mengikuti state machine backend: create = CREATED saja; edit = status sekarang + satu tahap berikutnya; DELIVERED terkunci. */
function buildStatusOptions(mode, s) {
    const sel = $('f_st'), keep = sel.value, hint = $('f_sthint');
    let opts, tx = '', lock = false;
    if (mode !== 'edit') { opts = ['CREATED']; tx = 'Pengiriman baru selalu dimulai dari status CREATED.'; }
    else {
        const cur = s && s.status ? s.status : null, i = FLOW.findIndex(f => f[0] === cur);
        if (!cur) opts = FLOW.map(f => f[0]);
        else if (cur === 'DELIVERED') { opts = ['DELIVERED']; lock = true; tx = 'Status terminal terkunci. Gunakan "Buka Kembali" di panel detail (khusus Administrator).'; }
        else { opts = [cur].concat(FLOW[i + 1] ? [FLOW[i + 1][0]] : []); tx = 'Status hanya boleh maju satu tahap. Menuju PICKED UP mewajibkan pembayaran lunas.'; }
    }
    sel.innerHTML = opts.map(k => '<option value="' + k + '">' + nice(k) + '</option>').join('');
    sel.value = opts.includes(keep) ? keep : opts[0];
    sel.disabled = lock; hint.textContent = tx;
}
function setForm(mode, s) {
    $('mMode').value = mode; $('mId').value = s ? s.id : '';
    $('mMethod').disabled = mode !== 'edit';
    shipForm.action = mode === 'edit' ? BASE + '/' + encodeURIComponent(s.id) : STORE;
    $('mTitle').textContent = mode === 'edit' ? 'Edit Pengiriman' : 'Tambah Pengiriman';
    $('mSub').textContent = mode === 'edit' ? 'Perbarui data pengiriman ' + (s.number || '') + '.' : 'Isi data pelanggan, barang, rute, dan jadwal.';
    $('mSaveTx').textContent = mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Pengiriman';
    buildStatusOptions(mode, s);
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
    closeDrawer(); closeTrack(); modal.classList.add('open'); setTimeout(() => $('f_cu').focus(), 50);
}
const closeModal = () => modal.classList.remove('open');
['openCreate', 'openCreate2'].forEach(id => $(id).addEventListener('click', openCreate));
['mClose', 'mCancel'].forEach(id => $(id).addEventListener('click', closeModal));
modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
if (FORM.mode === 'edit' && FORM.id) {   // buka ulang setelah validasi gagal
    const r = ROWS.find(x => String(x.id) === String(FORM.id));
    setForm('edit', r || { id: FORM.id, number: '' });
} else if (FORM.mode === 'create') setForm('create');

/* ===== HAPUS ===== */
function delShip(i) {
    const s = ROWS[i];
    if (!s || !confirm('Hapus pengiriman ' + s.number + '? Data yang dihapus tidak dapat dikembalikan.')) return;
    const f = document.createElement('form');
    f.method = 'POST'; f.action = BASE + '/' + encodeURIComponent(s.id);
    f.innerHTML = '<input type="hidden" name="_token" value="' + CSRF + '"><input type="hidden" name="_method" value="DELETE">';
    document.body.appendChild(f); f.submit();
}

/* ===== DRAWER DETAIL ===== */
const dr = $('drawer'), veil = $('veil');
let cur = null;
const kv = (k, v, full) => '<div' + (full ? ' class="full"' : '') + '><small>' + k + '</small><b>' + (v === null || v === '' || v === undefined ? '-' : esc(v)) + '</b></div>';

function closeDrawer() { dr.classList.remove('open'); veil.classList.remove('open'); dr.setAttribute('aria-hidden', 'true'); }
function dismissDrawer() { closeDrawer(); if (DIRTY) location.reload(); }

function secControl(s) {
    const st = s.state || {}, next = (st.next || [])[0];
    let h = '<div class="dr-sec"><h4><i class="fa-solid fa-shuffle"></i> KONTROL STATUS</h4>';
    if (s.status === 'DELIVERED') {
        h += '<div class="lock"><i class="fa-solid fa-lock"></i><div><b>Status terminal</b><small>DELIVERED terkunci dan tidak dapat kembali ke status awal lewat edit biasa. Prosedur khusus: Administrator + alasan + konfirmasi nomor resi, tercatat di riwayat.</small></div></div>';
        h += CAN_ADMIN
            ? '<button type="button" class="btn btn-warn" id="drReopen" style="width:100%"><i class="fa-solid fa-lock-open"></i> Buka Kembali (Prosedur Khusus)</button>'
            : '<div class="hint">Hanya Administrator yang dapat membuka kembali status terminal.</div>';
    } else if (next) {
        h += '<div class="hint">Transisi valid berikutnya: <b>' + nice(s.status) + ' → ' + nice(next) + '</b></div>';
        if (st.blocked) h += '<div class="lock warn"><i class="fa-solid fa-credit-card"></i><div><b>Terkunci oleh pembayaran</b><small>' + esc(st.blocked) + '</small></div></div>';
        h += '<button type="button" class="btn" id="drAdvance" style="width:100%"' + (st.blocked ? ' disabled' : '') + '><i class="fa-solid fa-forward-step"></i> Lanjutkan ke ' + nice(next) + '</button>';
    }
    return h + '</div>';
}
function secPay(s) {
    const st = s.state || {}, p = st.pay, q = st.quote || {};
    let h = '<div class="dr-sec"><h4><i class="fa-solid fa-credit-card"></i> PEMBAYARAN</h4><div class="kv">';
    h += kv('Status', p ? (PAYST[p.status] || p.status) : PAYST.NONE);
    h += kv(p ? 'Total tagihan' : 'Estimasi (sebelum biaya layanan)', p ? rupiah(p.total) : (q.base ? '≈ ' + rupiah(q.base) : '-'));
    if (p) h += kv('Invoice', p.invoice) + kv('Metode', p.method_label);
    return h + '</div></div>';
}
function secLog(s) {
    const L = (s.state && s.state.logs) || [];
    if (!L.length) return '';
    return '<div class="dr-sec"><h4><i class="fa-solid fa-clock-rotate-left"></i> RIWAYAT STATUS</h4><div class="tl">' + L.map((l, n) =>
        '<div class="ti ' + (n === 0 ? 'last' : '') + '"><b>' + (l.from ? nice(l.from) + ' → ' : '') + nice(l.to) +
        (l.type === 'REOPEN' ? ' <span class="pill" style="background:#fff1db;color:#a35f00">BUKA KEMBALI</span>' : '') + '</b>' +
        (l.reason ? '<span>' + esc(l.reason) + '</span>' : '') + '<small>' + esc(l.time) + ' · ' + esc(l.by || '-') + '</small></div>').join('') + '</div></div>';
}

function tabDetail(s) {
    const idx = FLOW.findIndex(f => f[0] === s.status);
    return '<div class="dr-sec"><h4><i class="fa-solid fa-signal"></i> STATUS PENGIRIMAN</h4><div class="prog">' +
            FLOW.map((f, n) => '<div class="pg ' + (n < idx ? 'done' : n === idx ? 'now' : '') + '"><i class="fa-solid ' + (n < idx ? 'fa-check' : f[2]) + '"></i><small>' + f[1] + '</small></div>').join('') +
        '</div></div>' + secControl(s) + secPay(s) +
        '<div class="dr-sec"><h4><i class="fa-solid fa-building"></i> PERUSAHAAN PENGIRIMAN</h4><div class="kv">' + kv('Perusahaan', s.carrier) + kv('Moda transport', MODE[s.mode] || s.mode) + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-route"></i> ASAL DAN TUJUAN</h4><div class="kv">' + kv('Asal', s.origin) + kv('Tujuan', s.destination) + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-box"></i> INFORMASI BARANG</h4><div class="kv">' + kv('Nama barang', s.name) + kv('Jenis barang', s.cargo) +
            kv('Berat', s.weight != null ? Number(s.weight).toLocaleString('id-ID') + ' kg' : '-') + kv('Volume', s.volume != null ? Number(s.volume).toLocaleString('id-ID') + ' m³' : '-') + kv('Deskripsi / catatan', s.notes, true) + '</div></div>' +
        '<div class="dr-sec"><h4><i class="fa-solid fa-calendar-days"></i> JADWAL PENGIRIMAN</h4><div class="kv">' + kv('Keberangkatan', s.etd) + kv('Estimasi tiba', s.eta) + (s.days !== null ? kv('Estimasi durasi', s.days + ' hari', true) : '') + '</div></div>' +
        secLog(s);
}

async function doAdvance(i) {
    const b = $('drAdvance'); if (b) b.disabled = true;
    try { const j = await api(i, { _action:'advance' }); toast(j.message); } catch (e) { toast(e.message, false); }
    paintDrawer();
}

function paintDrawer() {
    const s = ROWS[cur]; if (!s) return;
    $('drNum').textContent = s.number;
    $('drSub').textContent = (s.name || '') + (s.customer ? ' · ' + s.customer : '');
    $('drBody').innerHTML = tabDetail(s);
    $('drFoot').innerHTML =
        '<button type="button" class="btn" id="drTrack"><i class="fa-solid fa-location-dot"></i> Lacak Paket</button>' +
        '<button type="button" class="btn btn-light" id="drPay"><i class="fa-solid fa-credit-card"></i> Pembayaran</button>' +
        '<button type="button" class="btn btn-light" id="drEdit"><i class="fa-solid fa-pen"></i> Edit</button>' +
        '<button type="button" class="btn btn-danger" id="drDel"><i class="fa-solid fa-trash"></i> Hapus</button>';
    $('drTrack').onclick = () => openTrack(cur);
    $('drPay').onclick = () => openPay(cur);
    $('drEdit').onclick = () => openEdit(ROWS[cur]);
    $('drDel').onclick = () => delShip(cur);
    if ($('drAdvance')) $('drAdvance').onclick = () => doAdvance(cur);
    if ($('drReopen')) $('drReopen').onclick = () => openReopen(cur);
}
function showDrawer(i) {
    if (!ROWS[i]) return;
    cur = i; paintDrawer(); $('drBody').scrollTop = 0;
    dr.classList.add('open'); veil.classList.add('open'); dr.setAttribute('aria-hidden', 'false');
}

document.querySelectorAll('tbody tr[data-i]').forEach(tr => {
    tr.addEventListener('click', () => showDrawer(+tr.dataset.i));
    tr.addEventListener('keydown', e => { if (e.key === 'Enter' && e.target === tr) showDrawer(+tr.dataset.i); });
});
document.querySelectorAll('.ab').forEach(b => b.addEventListener('click', e => {
    e.stopPropagation();
    const i = +b.dataset.i, a = b.dataset.act;
    if (a === 'track') openTrack(i); else if (a === 'pay') openPay(i); else if (a === 'edit') openEdit(ROWS[i]); else delShip(i);
}));
$('drClose').addEventListener('click', dismissDrawer); veil.addEventListener('click', dismissDrawer);

/* =====================================================================
   BUKA KEMBALI STATUS TERMINAL (PROSEDUR KHUSUS)
   ===================================================================== */
const rm = $('reopenm'); let RI = null;
function openReopen(i) {
    const s = ROWS[i]; if (!s) return;
    RI = i; $('rpNum').textContent = s.number;
    $('rpTarget').innerHTML = FLOW.filter(f => f[0] !== 'DELIVERED').map(f => '<option value="' + f[0] + '">' + nice(f[0]) + '</option>').join('');
    $('rpTarget').value = 'IN_TRANSIT'; $('rpReason').value = ''; $('rpConfirm').value = ''; $('rpErr').textContent = '';
    rm.classList.add('open'); setTimeout(() => $('rpReason').focus(), 50);
}
const closeReopen = () => { rm.classList.remove('open'); RI = null; };
async function submitReopen() {
    if (RI === null) return;
    const btn = $('rpGo'); btn.disabled = true; $('rpErr').textContent = '';
    try {
        const j = await api(RI, { _action:'reopen', target_status:$('rpTarget').value, reason:$('rpReason').value, confirm_number:$('rpConfirm').value });
        closeReopen(); toast(j.message); paintDrawer();
    } catch (e) { $('rpErr').textContent = e.message; }
    btn.disabled = false;
}
['rpClose', 'rpCancel'].forEach(id => $(id).addEventListener('click', closeReopen));
$('rpGo').addEventListener('click', submitReopen);
rm.addEventListener('click', e => { if (e.target === rm) closeReopen(); });

/* =====================================================================
   PAYMENT GATEWAY (SANDBOX): pilih metode -> tagihan -> bayar (callback) -> lunas / refund
   ===================================================================== */
const pm = $('paym');
const GRP = { va:['Virtual Account', 'fa-building-columns'], qris:['QRIS', 'fa-qrcode'], ewallet:['E-Wallet', 'fa-wallet'], card:['Kartu Kredit / Debit', 'fa-credit-card'] };
let PI = null, PT = null, PSEL = null;

function qrSvg(str) {   // QR ilustrasi deterministik (bukan QRIS sungguhan)
    let h = 2166136261; for (const c of String(str)) h = Math.imul(h ^ c.charCodeAt(0), 16777619) >>> 0; if (!h) h = 1;
    const N = 25, g = Array.from({ length:N }, () => Array(N).fill(0));
    const rnd = () => { h ^= h << 13; h >>>= 0; h ^= h >>> 17; h ^= h << 5; h >>>= 0; return h; };
    for (let y = 0; y < N; y++) for (let x = 0; x < N; x++) g[y][x] = rnd() % 100 < 48 ? 1 : 0;
    const finder = (ox, oy) => { for (let y = -1; y <= 7; y++) for (let x = -1; x <= 7; x++) {
        const X = ox + x, Y = oy + y; if (X < 0 || Y < 0 || X >= N || Y >= N) continue;
        const edge = x === -1 || y === -1 || x === 7 || y === 7, ring = x === 0 || y === 0 || x === 6 || y === 6, core = x >= 2 && x <= 4 && y >= 2 && y <= 4;
        g[Y][X] = edge ? 0 : (ring || core) ? 1 : 0; } };
    finder(0, 0); finder(N - 7, 0); finder(0, N - 7);
    let r = ''; for (let y = 0; y < N; y++) for (let x = 0; x < N; x++) if (g[y][x]) r += '<rect x="' + x + '" y="' + y + '" width="1" height="1"/>';
    return '<svg viewBox="-1 -1 ' + (N + 2) + ' ' + (N + 2) + '" xmlns="http://www.w3.org/2000/svg" shape-rendering="crispEdges" role="img" aria-label="QR sandbox"><rect x="-1" y="-1" width="' + (N + 2) + '" height="' + (N + 2) + '" fill="#fff"/><g fill="#061533">' + r + '</g></svg>';
}

function billHtml(q, k) {
    const m = q.methods[k];
    return '<div><span>Ongkos kirim</span><b>' + rupiah(q.subtotal) + '</b></div><div><span>PPN ' + q.tax_pct + '%</span><b>' + rupiah(q.tax) + '</b></div>' +
        '<div><span>Biaya layanan</span><b>' + (m.fee ? rupiah(m.fee) : 'Gratis') + '</b></div><div class="grand"><span>Total</span><b>' + rupiah(m.total) + '</b></div>';
}

function viewChoose(s, p, q) {
    const ms = q.methods || {}, keys = Object.keys(ms);
    if (!PSEL || !ms[PSEL]) PSEL = keys[0];
    let h = '';
    if (p && ['FAILED', 'EXPIRED', 'CANCELLED', 'REFUNDED'].includes(p.status))
        h += '<div class="lock warn"><i class="fa-solid fa-circle-info"></i><div><b>Tagihan terakhir: ' + esc(PAYST[p.status]) + '</b><small>' + esc(p.invoice) + (p.note ? ' · ' + esc(p.note) : '') + '. Silakan buat tagihan baru.</small></div></div>';
    h += '<div class="pm-sec">PILIH METODE PEMBAYARAN</div>';
    ['va', 'qris', 'ewallet', 'card'].forEach(g => {
        const list = keys.filter(k => ms[k].group === g); if (!list.length) return;
        h += '<div class="pm-grp"><i class="fa-solid ' + GRP[g][1] + '"></i> ' + GRP[g][0] + '</div><div class="pm-opts">' + list.map(k =>
            '<label class="mth' + (k === PSEL ? ' on' : '') + '"><input type="radio" name="pmth" value="' + k + '"' + (k === PSEL ? ' checked' : '') + '><b>' + esc(ms[k].label) + '</b><small>Biaya layanan ' + (ms[k].fee ? rupiah(ms[k].fee) : 'gratis') + '</small></label>').join('') + '</div>';
    });
    h += '<div class="pm-sec">RINCIAN TAGIHAN</div><div class="bill" id="pmBill"></div>';
    h += '<button type="button" class="btn pm-go" id="pmGo"><i class="fa-solid fa-lock"></i> Bayar Sekarang</button>';
    h += '<p class="pm-note">Tarif dihitung server dari berat tertagih ' + nf(q.chargeable, 2) + ' kg × ' + rupiah(q.per_kg) + '/kg (' + esc(MODE[s.mode] || s.mode) + ')' + (q.min_applied ? ', tarif minimum diterapkan' : '') + '.' +
        (SANDBOX ? ' Gateway berjalan dalam mode sandbox: tidak ada uang nyata yang berpindah.' : '') + '</p>';
    return h;
}

function viewPending(s, p) {
    let h = '<div class="pm-timer"><small>Selesaikan pembayaran sebelum</small><b id="pmCd">--:--:--</b><em>' + esc(fmtDT(p.expires_at)) + '</em></div>';
    h += '<div class="bill"><div><span>Invoice</span><b>' + esc(p.invoice) + '</b></div><div><span>Metode</span><b>' + esc(p.method_label) + '</b></div><div class="grand"><span>Total tagihan</span><b>' + rupiah(p.total) + '</b></div></div>';
    if (p.group === 'va') {
        h += '<div class="pm-code"><small>NOMOR VIRTUAL ACCOUNT</small><b>' + esc(p.code) + '</b><button type="button" class="btn btn-light" data-copy="' + esc(p.code) + '"><i class="fa-regular fa-copy"></i> Salin</button></div>' +
            '<ol class="pm-how"><li>Buka m-banking atau ATM, pilih Transfer → Virtual Account.</li><li>Masukkan nomor di atas dan pastikan nominal ' + rupiah(p.total) + '.</li><li>Setelah dibayar, gateway mengirim callback dan status berubah otomatis.</li></ol>';
    } else if (p.group === 'qris') {
        h += '<div class="pm-qr">' + qrSvg(p.code) + '</div><div class="pm-qrcap">Contoh QR sandbox (bukan QRIS sungguhan) · ' + esc(p.code) + '</div>' +
            '<ol class="pm-how"><li>Buka aplikasi bank atau e-wallet yang mendukung QRIS.</li><li>Scan kode, periksa nominal ' + rupiah(p.total) + ', lalu konfirmasi.</li></ol>';
    } else if (p.group === 'ewallet') {
        h += '<div class="pm-code"><small>KODE PEMBAYARAN ' + esc(p.method_label.toUpperCase()) + '</small><b>' + esc(p.code) + '</b><button type="button" class="btn btn-light" data-copy="' + esc(p.code) + '"><i class="fa-regular fa-copy"></i> Salin</button></div>' +
            '<ol class="pm-how"><li>Buka aplikasi ' + esc(p.method_label) + ' lalu pilih Bayar.</li><li>Masukkan kode di atas dan konfirmasi dengan PIN.</li></ol>';
    } else {
        h += '<ol class="pm-how"><li>Pembayaran kartu diproses di halaman aman milik gateway.</li><li>Masukkan nomor kartu, masa berlaku, dan OTP 3-D Secure.</li></ol>';
    }
    if (SANDBOX) h += '<div class="sbx"><b><i class="fa-solid fa-flask"></i> Mode Sandbox: simulasi callback gateway</b><small>Callback ditandatangani HMAC-SHA256 dan diverifikasi di server, idempoten, serta mencocokkan nominal seperti webhook gateway sungguhan.</small>' +
        '<div class="sbx-b"><button type="button" class="btn" data-sim="success">Bayar Berhasil</button><button type="button" class="btn btn-danger" data-sim="failed">Gagal</button><button type="button" class="btn btn-light" data-sim="expired">Kedaluwarsa</button></div></div>';
    h += '<button type="button" class="btn btn-light pm-go" id="pmCancel"><i class="fa-solid fa-xmark"></i> Batalkan tagihan / ganti metode</button>';
    return h;
}

function viewPaid(s, p, st) {
    let h = '<div class="pm-ok"><i class="fa-solid fa-circle-check"></i><b>Pembayaran Lunas</b><small>' + rupiah(p.total) + ' · ' + esc(p.paid_at || '') + '</small></div>';
    h += '<div class="bill"><div><span>Invoice</span><b>' + esc(p.invoice) + '</b></div><div><span>Metode</span><b>' + esc(p.method_label) + '</b></div><div><span>Referensi gateway</span><b>' + esc(p.gateway_ref || '-') + '</b></div>' +
        '<div><span>Ongkos kirim</span><b>' + rupiah(p.subtotal) + '</b></div><div><span>PPN</span><b>' + rupiah(p.tax) + '</b></div><div><span>Biaya layanan</span><b>' + (p.fee ? rupiah(p.fee) : 'Gratis') + '</b></div><div class="grand"><span>Total dibayar</span><b>' + rupiah(p.total) + '</b></div></div>';
    if (st.can_refund) h += '<div class="pm-sec">REFUND (ADMINISTRATOR)</div><div class="pm-rf"><input type="text" id="pmRfNote" maxlength="255" placeholder="Alasan refund (min. 5 karakter)" aria-label="Alasan refund"><button type="button" class="btn btn-danger" id="pmRefund"><i class="fa-solid fa-rotate-left"></i> Refund</button></div>';
    else h += '<p class="pm-note">Refund hanya dapat dilakukan Administrator dan tidak tersedia setelah barang dalam perjalanan atau terkirim.</p>';
    return h;
}

function viewHistory(list) {
    if (!list.length) return '';
    return '<div class="pm-sec">RIWAYAT TAGIHAN</div>' + list.map(p =>
        '<div class="hrow"><div><strong>' + esc(p.invoice) + '</strong><small>' + esc(p.method_label) + ' · ' + esc(p.created || '') + '</small></div><div class="r">' + payBadge(p) + '<br><small>' + rupiah(p.total) + '</small></div></div>').join('');
}

function renderPay() {
    const s = ROWS[PI]; if (!s) return;
    clearInterval(PT);
    const st = s.state || {}, p = st.pay, q = st.quote || {};
    $('pmTitle').textContent = 'Pembayaran · ' + s.number;
    $('pmSub').textContent = (s.name || '') + (s.customer ? ' · ' + s.customer : '');
    let h = p && p.status === 'PAID' ? viewPaid(s, p, st) : p && p.status === 'PENDING' ? viewPending(s, p) : viewChoose(s, p, q);
    h += viewHistory(st.payments || []);
    $('pmBody').innerHTML = h;
    bindPay(p, q);
}

function bindPay(p, q) {
    if ($('pmBill')) $('pmBill').innerHTML = billHtml(q, PSEL);
    pm.querySelectorAll('input[name=pmth]').forEach(r => r.onchange = () => {
        PSEL = r.value;
        pm.querySelectorAll('.mth').forEach(l => l.classList.toggle('on', l.querySelector('input').checked));
        $('pmBill').innerHTML = billHtml(q, PSEL);
    });
    const act = async body => {
        pm.querySelectorAll('button').forEach(b => b.disabled = true);
        try { const j = await api(PI, body); toast(j.message); } catch (e) { toast(e.message, false); }
        if (PI !== null) renderPay();
        if (dr.classList.contains('open')) paintDrawer();
    };
    if ($('pmGo')) $('pmGo').onclick = () => act({ _action:'pay_create', method:PSEL });
    pm.querySelectorAll('[data-sim]').forEach(b => b.onclick = () => act({ _action:'pay_simulate', outcome:b.dataset.sim }));
    if ($('pmCancel')) $('pmCancel').onclick = () => act({ _action:'pay_cancel' });
    if ($('pmRefund')) $('pmRefund').onclick = () => act({ _action:'pay_refund', note:$('pmRfNote').value });
    pm.querySelectorAll('[data-copy]').forEach(b => b.onclick = () => {
        if (navigator.clipboard) navigator.clipboard.writeText(b.dataset.copy).then(() => toast('Disalin ke clipboard.'), () => toast('Gagal menyalin.', false));
    });
    if (p && p.status === 'PENDING' && p.expires_at) {
        let synced = false;
        const tick = () => {
            const el = $('pmCd'); if (!el) return;
            const ms = Date.parse(p.expires_at) - Date.now();
            if (ms <= 0) {
                el.textContent = '00:00:00';
                if (!synced) { synced = true; clearInterval(PT); setTimeout(async () => { try { await api(PI, { _action:'sync' }); } catch (_) {} if (PI !== null) renderPay(); }, 1500); }
                return;
            }
            const t = Math.floor(ms / 1000);
            el.textContent = p2(Math.floor(t / 3600)) + ':' + p2(Math.floor(t % 3600 / 60)) + ':' + p2(t % 60);
        };
        tick(); PT = setInterval(tick, 1000);
    }
}
function openPay(i) {
    if (!ROWS[i]) return;
    PI = i; PSEL = null; renderPay(); pm.classList.add('open'); pm.setAttribute('aria-hidden', 'false');
}
function closePay() {
    if (!pm.classList.contains('open')) return;
    clearInterval(PT); PT = null; pm.classList.remove('open'); pm.setAttribute('aria-hidden', 'true'); PI = null;
    if (DIRTY && !dr.classList.contains('open')) location.reload();
}
$('pmClose').addEventListener('click', closePay);
pm.addEventListener('click', e => { if (e.target === pm) closePay(); });

/* =====================================================================
   LACAK PAKET: peta ala Shopee (posisi armada, rute, cuaca, waktu)
   ===================================================================== */
const TOMTOM_KEY = '';   // opsional: API key TomTom (gratis) agar lapisan lalu lintas resmi. Kosong = tile lalu lintas Google (tidak resmi).

const CITIES = {
    'banda aceh':[5.5483,95.3238],'aceh':[5.5483,95.3238],'sabang':[5.8897,95.3164],'medan':[3.5952,98.6722],'kabanjahe':[3.1001,98.4908],'belawan':[3.7826,98.6930],'binjai':[3.6001,98.4854],
    'rantauprapat':[2.1021,99.8337],'padang':[-0.9471,100.4172],'pekanbaru':[0.5071,101.4478],'dumai':[1.6665,101.4478],'batam':[1.0456,104.0305],'tanjung pinang':[0.9186,104.4586],'jambi':[-1.6101,103.6131],'palembang':[-2.9761,104.7754],
    'bengkulu':[-3.8004,102.2655],'bandar lampung':[-5.4292,105.2610],'lampung':[-5.4292,105.2610],'jakarta':[-6.2088,106.8456],'tanjung priok':[-6.1050,106.8800],'bogor':[-6.5971,106.8060],
    'depok':[-6.4025,106.7942],'bekasi':[-6.2383,106.9756],'tangerang':[-6.1783,106.6319],'bandung':[-6.9175,107.6191],'cirebon':[-6.7320,108.5523],'yogyakarta':[-7.7956,110.3695],
    'jogja':[-7.7956,110.3695],'solo':[-7.5666,110.8167],'semarang':[-6.9667,110.4167],'surabaya':[-7.2575,112.7521],'malang':[-7.9666,112.6326],'banyuwangi':[-8.2192,114.3691],
    'denpasar':[-8.6705,115.2126],'bali':[-8.4095,115.1889],'mataram':[-8.5833,116.1167],'kupang':[-10.1772,123.6070],'pontianak':[-0.0263,109.3425],'banjarmasin':[-3.3186,114.5944],
    'samarinda':[-0.5022,117.1536],'balikpapan':[-1.2379,116.8529],'makassar':[-5.1477,119.4327],'manado':[1.4748,124.8421],'palu':[-0.9,119.8707],'kendari':[-3.9985,122.512],
    'ambon':[-3.6954,128.1814],'sorong':[-0.8762,131.2558],'jayapura':[-2.5916,140.669],
    'singapura':[1.3521,103.8198],'singapore':[1.3521,103.8198],'kuala lumpur':[3.139,101.6869],'malaysia':[3.139,101.6869],'penang':[5.4141,100.3288],'port klang':[3.0000,101.4000],
    'bangkok':[13.7563,100.5018],'thailand':[13.7563,100.5018],'ho chi minh':[10.8231,106.6297],'hanoi':[21.0278,105.8342],'vietnam':[14.0583,108.2772],'manila':[14.5995,120.9842],
    'colombo':[6.9271,79.8612],'chennai':[13.0827,80.2707],'mumbai':[19.076,72.8777],'dubai':[25.2048,55.2708],
    'hong kong':[22.3193,114.1694],'shanghai':[31.2304,121.4737],'tiongkok':[35.8617,104.1954],'china':[35.8617,104.1954],'tokyo':[35.6762,139.6503],'jepang':[36.2048,138.2529],
    'seoul':[37.5665,126.978],'busan':[35.1796,129.0756],'sydney':[-33.8688,151.2093],'australia':[-25.2744,133.7751],
    'berlin':[52.52,13.405],'jerman':[52.52,13.405],'germany':[52.52,13.405],'hamburg':[53.5511,9.9937],'rotterdam':[51.9244,4.4777],'amsterdam':[52.3676,4.9041],'london':[51.5072,-0.1276],'paris':[48.8566,2.3522],
    'new york':[40.7128,-74.006],'los angeles':[34.0522,-118.2437]
};
const GEO = (() => { try { return JSON.parse(localStorage.getItem('ct_geo') || '{}'); } catch (_) { return {}; } })();
const saveGeo = () => { try { localStorage.setItem('ct_geo', JSON.stringify(GEO)); } catch (_) {} };
const sleep = ms => new Promise(r => setTimeout(r, ms));

/* Cari koordinat: kamus kota dulu, lalu OpenStreetMap Nominatim (bisa sampai desa/jalan, hasil disimpan di browser). */
async function geocode(name) {
    if (!name) return null;
    const k = String(name).toLowerCase().trim();
    if (CITIES[k]) return CITIES[k];
    let best = null;
    for (const c in CITIES) if (k.includes(c) && (!best || c.length > best.length)) best = c;
    if (best) return CITIES[best];
    if (k in GEO) return GEO[k];
    const parts = String(name).split(',').map(x => x.trim()).filter(Boolean);
    const tries = [name]; if (parts.length > 2) tries.push(parts.slice(-3).join(', ')); if (parts.length > 1) tries.push(parts.slice(-2).join(', '));
    for (const q of tries) {
        try {
            const r = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&accept-language=id&q=' + encodeURIComponent(q));
            if (!r.ok) return null;
            const j = await r.json();
            if (j && j[0]) { const v = [+j[0].lat, +j[0].lon]; GEO[k] = v; saveGeo(); return v; }
            await sleep(350);
        } catch (_) { return null; }
    }
    GEO[k] = null; saveGeo();
    return null;
}

const kmBetween = (a, b) => { const r = x => x * Math.PI / 180, dl = r(b[0] - a[0]), dn = r(b[1] - a[1]), h = Math.sin(dl / 2) ** 2 + Math.cos(r(a[0])) * Math.cos(r(b[0])) * Math.sin(dn / 2) ** 2; return 12742 * Math.asin(Math.sqrt(h)); };
function arc(a, b, n) {
    const mx = (a[0] + b[0]) / 2, my = (a[1] + b[1]) / 2, dx = b[0] - a[0], dy = b[1] - a[1], cx = mx - dy * .18, cy = my + dx * .18, pts = [];
    for (let i = 0; i <= n; i++) { const t = i / n, u = 1 - t; pts.push([u*u*a[0] + 2*u*t*cx + t*t*b[0], u*u*a[1] + 2*u*t*cy + t*t*b[1]]); }
    return pts;
}

const TK = { map:null, layer:null, srch:null, base:null, bases:null, view:'loc', radar:true, radarLayer:null, data:null, wx:new Map(), timer:null, tok:0 };
const tkm = $('tkm');

const osm = () => L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OpenStreetMap contributors', maxZoom:19 });
function makeBases() {
    return {
        default: osm(),
        sat: L.layerGroup([
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution:'Tiles &copy; Esri, Maxar, Earthstar Geographics', maxZoom:19 }),
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', { maxZoom:19 })
        ]),
        terrain: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', { subdomains:'abc', maxNativeZoom:17, maxZoom:19, attribution:'&copy; OpenStreetMap, SRTM | Style &copy; OpenTopoMap (CC-BY-SA)' }),
        transit: L.tileLayer('https://tileserver.memomaps.de/tilegen/{z}/{x}/{y}.png', { maxNativeZoom:18, maxZoom:19, attribution:'Map &copy; <a href="https://memomaps.de/">memomaps.de</a>, data &copy; OpenStreetMap contributors' }),
        traffic: TOMTOM_KEY
            ? L.layerGroup([osm(), L.tileLayer('https://api.tomtom.com/traffic/map/4/tile/flow/relative0/{z}/{x}/{y}.png?key=' + encodeURIComponent(TOMTOM_KEY), { opacity:.85, maxZoom:19, attribution:'Traffic &copy; TomTom' })])
            : L.tileLayer('https://mt{s}.google.com/vt/lyrs=m,traffic&x={x}&y={y}&z={z}', { subdomains:'0123', maxNativeZoom:19, maxZoom:19, attribution:'Map data &copy; Google' })
    };
}

/* ----- cuaca (Open-Meteo) & waktu lokal ----- */
const wxKey = (lat, lng) => (+lat).toFixed(1) + ',' + (+lng).toFixed(1);
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
const fmtOff = off => { const d = new Date(Date.now() + off * 1000); return p2(d.getUTCHours()) + ':' + p2(d.getUTCMinutes()) + ':' + p2(d.getUTCSeconds()); };
const utcLabel = off => { const a = Math.abs(off), h = Math.floor(a / 3600), m = Math.round((a % 3600) / 60); return 'UTC' + (off < 0 ? '-' : '+') + h + (m ? ':' + p2(m) : ''); };

function ensureWx(points) {
    const seen = new Set(), miss = [];
    points.forEach(p => { const k = wxKey(p[0], p[1]); if (!TK.wx.has(k) && !seen.has(k)) { seen.add(k); miss.push(k); } });
    if (!miss.length) return Promise.resolve(false);
    miss.forEach(k => TK.wx.set(k, null));
    const la = miss.map(k => k.split(',')[0]).join(','), lo = miss.map(k => k.split(',')[1]).join(',');
    return fetch('https://api.open-meteo.com/v1/forecast?latitude=' + la + '&longitude=' + lo + '&current=temperature_2m,weather_code,wind_speed_10m,precipitation,is_day&timezone=auto')
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(j => { (Array.isArray(j) ? j : [j]).forEach((o, i) => { const c = o.current || {}; TK.wx.set(miss[i], { temp:c.temperature_2m, code:c.weather_code, wind:c.wind_speed_10m, day:c.is_day !== 0, off:o.utc_offset_seconds || 0 }); }); return true; })
        .catch(() => { miss.forEach(k => TK.wx.set(k, { fail:true })); return true; });
}
function badge(lat, lng) {
    if (TK.view === 'loc') return '';
    const w = TK.wx.get(wxKey(lat, lng));
    if (TK.view === 'wx') {
        if (!w) return '<span class="bd dim"><i class="fa-solid fa-spinner fa-spin"></i> cuaca…</span>';
        if (w.fail) return '<span class="bd dim">cuaca n/a</span>';
        const x = wxInfo(w.code, w.day), bad = w.code >= 61 || w.wind >= 40;
        return '<span class="bd' + (bad ? ' bad' : '') + '"><i class="fa-solid ' + x.icon + '"></i>' + Math.round(w.temp) + '°C <em>' + x.t + '</em></span>';
    }
    const ok = w && !w.fail, off = ok ? w.off : Math.round(lng / 15) * 3600;
    return '<span class="bd"><i class="fa-solid ' + (ok ? (w.day ? 'fa-sun' : 'fa-moon') : 'fa-clock') + '"></i><span class="tm" data-off="' + off + '">' + fmtOff(off) + '</span><em>' + utcLabel(off) + '</em></span>';
}
function ctxInfo(lat, lng) {
    const w = TK.wx.get(wxKey(lat, lng)); if (!w || w.fail) return '';
    const x = wxInfo(w.code, w.day);
    return '<span class="ctx"><i class="fa-solid ' + x.icon + '"></i> ' + x.t + ', ' + Math.round(w.temp) + '°C · angin ' + Math.round(w.wind) + ' km/j<br><i class="fa-regular fa-clock"></i> Waktu lokal <span class="tm" data-off="' + w.off + '">' + fmtOff(w.off) + '</span> (' + utcLabel(w.off) + ')</span>';
}

/* radar hujan (RainViewer) */
async function setRadar(on) {
    if (!TK.map) return;
    if (!on) { if (TK.radarLayer && TK.map.hasLayer(TK.radarLayer)) TK.map.removeLayer(TK.radarLayer); return; }
    if (!TK.radarLayer) {
        try {
            const j = await (await fetch('https://api.rainviewer.com/public/weather-maps.json')).json();
            const f = ((j.radar && j.radar.past) || []).slice(-1)[0]; if (!f) return;
            TK.radarLayer = L.tileLayer(j.host + f.path + '/256/{z}/{x}/{y}/2/1_1.png', { opacity:.6, maxNativeZoom:7, maxZoom:19, zIndex:5, attribution:'Radar &copy; RainViewer' });
        } catch (_) { return; }
    }
    if (TK.map && TK.view === 'wx' && TK.radar) TK.radarLayer.addTo(TK.map);
}

/* ----- marker ----- */
const pin = (color, icon, cls, label, bdg) => L.divIcon({ className:'',
    html:'<div class="pw"><div class="pin ' + (cls || '') + '" style="background:' + color + '"><i class="fa-solid ' + icon + '"></i></div>' +
        ((label || bdg) ? '<div class="pl">' + (label ? '<span class="vl">' + esc(label) + '</span>' : '') + (bdg || '') + '</div>' : '') + '</div>',
    iconSize:[34,34], iconAnchor:[17,17], popupAnchor:[0,-20] });
const dotIcon = L.divIcon({ className:'', html:'<div class="tp"></div>', iconSize:[10,10], iconAnchor:[5,5] });

/* ----- bangun data tracking ----- */
function timeFrac(s) {
    const a = Date.parse(s.etd_raw), b = Date.parse(s.eta_raw);
    if (isNaN(a) || isNaN(b) || b <= a) return .5;
    return Math.min(.92, Math.max(.08, (Date.now() - a) / (b - a)));
}
async function buildTrack(s) {
    const [o, d] = await Promise.all([geocode(s.origin), geocode(s.destination)]);
    const ev = [];
    let looked = 0;
    for (const t of (s.tracks || [])) {
        let ll = (t.lat !== null && t.lng !== null) ? [t.lat, t.lng] : null;
        if (!ll && t.location && looked < 8) { looked++; ll = await geocode(t.location); }
        if (ll) ev.push({ ll, t });
    }
    const veh = vehOf(s.mode), planned = (o && d) ? arc(o, d, 60) : null;
    let pos = null, exact = false;
    if (ev.length) { pos = ev[ev.length - 1].ll; exact = true; }
    else if (planned) {
        const f = s.status === 'DELIVERED' ? 1 : s.status === 'IN_TRANSIT' ? timeFrac(s) : s.status === 'PICKED_UP' ? .05 : 0;
        pos = planned[Math.round(f * (planned.length - 1))];
    } else pos = o || d;
    let prog = 0, remain = null;
    if (o && d && pos) {
        const a = kmBetween(o, pos), b = kmBetween(pos, d);
        prog = s.status === 'DELIVERED' ? 1 : (a + b > 0 ? a / (a + b) : 0);
        remain = s.status === 'DELIVERED' ? 0 : b;
    }
    const locText = exact ? ((ev[ev.length - 1].t.location) || 'Koordinat terakhir yang tercatat') : (s.status === 'DELIVERED' ? 'Sudah sampai di ' + s.destination : 'Perkiraan posisi di sepanjang rute ' + s.origin + ' → ' + s.destination);
    return { s, o, d, ev, veh, planned, pos, exact, prog, remain, locText };
}

/* ----- panel samping ala Shopee ----- */
function renderSide(d) {
    const s = d.s, v = d.veh, idx = FLOW.findIndex(f => f[0] === s.status), pct = Math.round(d.prog * 100), t = s.tracks || [];
    let h = '<div class="lastloc"><i class="fa-solid ' + (s.status === 'DELIVERED' ? 'fa-circle-check' : v.icon) + '"></i><div><b class="stt-big">' + esc(STT[s.status] || String(s.status).replace(/_/g, ' ')) + '</b><small>' + esc(d.locText) + (d.exact ? '' : ' (perkiraan)') + '</small></div></div>';
    h += '<div class="dr-sec"><div class="prog">' + FLOW.map((f, n) => '<div class="pg ' + (n < idx ? 'done' : n === idx ? 'now' : '') + '"><i class="fa-solid ' + (n < idx ? 'fa-check' : f[2]) + '"></i><small>' + f[1] + '</small></div>').join('') + '</div></div>';
    h += '<div class="dr-sec"><h4><i class="fa-solid fa-route"></i> PERJALANAN</h4><div class="kv">' +
        kv('Armada', v.name + (s.vnum ? ' · ' + s.vnum : '')) + kv('Perusahaan', s.carrier) +
        kv('Asal', s.origin) + kv('Tujuan', s.destination) +
        kv('Estimasi tiba', s.eta) + kv('Jarak tersisa', d.remain !== null ? '± ' + nf(d.remain, 0) + ' km (garis lurus)' : '-') + '</div>' +
        (d.o && d.d ? '<div class="bar"><i style="width:' + pct + '%"></i></div><div style="margin-top:6px;font-size:11px;color:var(--mut)">' + pct + '% dari rute</div>' : '') + '</div>';
    h += '<div class="dr-sec"><h4><i class="fa-solid fa-clock-rotate-left"></i> RIWAYAT PERJALANAN</h4>';
    h += t.length
        ? '<div class="tl">' + t.slice().reverse().map((e, n) => '<div class="ti ' + (n === 0 ? 'last' : '') + '"><b>' + nice(e.status) + '</b><span>' + esc(e.location || '-') + '</span>' +
            (e.desc ? '<small>' + esc(e.desc) + '</small>' : '') + '<small>' + esc(e.time) + (e.by ? ' · ' + esc(e.by) : '') + '</small></div>').join('') + '</div>'
        : '<div class="nodata" style="padding:12px 0">Belum ada riwayat tracking. Posisi di peta diperkirakan dari status &amp; jadwal pengiriman.</div>';
    h += '</div>';
    $('tkSide').innerHTML = h;
}

/* ----- gambar peta ----- */
function popInfo(d, ll, extra) {
    const s = d.s, v = d.veh;
    return '<div class="pop"><strong>' + esc(s.number) + '</strong><span class="veh"><i class="fa-solid ' + v.icon + '"></i> ' + esc(v.name) + (s.vnum ? ' · ' + esc(s.vnum) : '') + '</span><br>' +
        'Customer: ' + esc(s.customer) + '<br>Carrier: ' + esc(s.carrier || '-') + '<br>Rute: ' + esc(s.origin) + ' → ' + esc(s.destination) +
        '<br>Status: ' + nice(s.status) + (s.eta ? '<br>ETA: ' + esc(s.eta) : '') + (extra || '') + (ll ? ctxInfo(ll[0], ll[1]) : '') + '</div>';
}
function drawTrack(fit) {
    const d = TK.data; if (!d || !TK.map) return;
    const tok = TK.tok, layer = TK.layer, bounds = [], need = [];
    layer.clearLayers();
    const bd = ll => { need.push(ll); return badge(ll[0], ll[1]); };
    const s = d.s;

    if (d.planned) L.polyline(d.planned, { color:'#2f8cf5', weight:3, opacity:.7, dashArray:'7,8' }).bindPopup(popInfo(d, null, '<br><small>Garis rencana rute (bukan jalur aktual).</small>')).addTo(layer);
    if (d.ev.length > 1) L.polyline(d.ev.map(e => e.ll), { color:'#f59e0b', weight:4, opacity:.95 }).addTo(layer);
    else if (!d.exact && d.planned && s.status !== 'CREATED' && s.status !== 'BOOKED') {
        const cut = d.planned.findIndex(p => p === d.pos);
        if (cut > 0) L.polyline(d.planned.slice(0, cut + 1), { color:'#f59e0b', weight:4, opacity:.95 }).addTo(layer);
    }
    if (d.o) { L.marker(d.o, { icon:pin('#2563eb', 'fa-box', '', 'Asal', bd(d.o)) }).bindPopup(popInfo(d, d.o, '<br><b>Titik asal</b>')).addTo(layer); bounds.push(d.o); }
    if (d.d) { L.marker(d.d, { icon:pin('#16a34a', 'fa-flag-checkered', '', 'Tujuan', bd(d.d)) }).bindPopup(popInfo(d, d.d, '<br><b>Titik tujuan</b>')).addTo(layer); bounds.push(d.d); }
    d.ev.forEach(e => {
        L.marker(e.ll, { icon:dotIcon }).bindPopup('<div class="pop"><strong>' + esc(s.number) + '</strong>Lokasi: ' + esc(e.t.location || '-') + '<br>Status: ' + nice(e.t.status) + (e.t.desc ? '<br>' + esc(e.t.desc) : '') + '<br><small>' + esc(e.t.time || '') + '</small></div>').addTo(layer);
        bounds.push(e.ll);
    });
    if (d.pos && s.status !== 'DELIVERED') {
        const lbl = d.veh.name + (s.vnum ? ' ' + s.vnum : '');
        L.marker(d.pos, { icon:pin(d.veh.color, d.veh.icon, 'big fleet', lbl, bd(d.pos)), zIndexOffset:900 })
            .bindPopup(popInfo(d, d.pos, d.exact ? '<br><b>Posisi terakhir tercatat</b>' : '<br><small>Posisi diperkirakan di sepanjang rute.</small>')).addTo(layer);
        bounds.push(d.pos);
    }
    $('tkEmpty').classList.toggle('show', !bounds.length);
    if (fit && bounds.length) {
        if (bounds.length === 1) TK.map.setView(bounds[0], 11); else TK.map.fitBounds(bounds, { padding:[70,70], maxZoom:12 });
    }
    if (TK.view !== 'loc') ensureWx(need).then(changed => { if (changed && tok === TK.tok && TK.map && TK.view !== 'loc') drawTrack(false); });
}

function legend(d) {
    const v = d.veh;
    $('tkLegend').innerHTML = '<span><i class="d" style="background:#2563eb"></i> Asal</span><span><i class="d" style="background:#16a34a"></i> Tujuan</span>' +
        '<span><i class="fa-solid ' + v.icon + '" style="color:' + v.color + '"></i> ' + v.name + (d.s.vnum ? ' ' + esc(d.s.vnum) : '') + '</span>' +
        '<span><i class="ln"></i> Sudah dilalui</span><span><i class="ln ds"></i> Rencana rute</span>';
}

/* ----- buka / tutup ----- */
function initMap() {
    if (TK.map) { TK.map.remove(); TK.map = null; }
    TK.map = L.map('tkMap', { worldCopyJump:true, maxZoom:19 }).setView([2, 110], 4);
    TK.bases = makeBases(); TK.base = TK.bases.default.addTo(TK.map);
    TK.layer = L.layerGroup().addTo(TK.map);
    TK.srch = L.layerGroup().addTo(TK.map);
    TK.radarLayer = null;
    setTimeout(() => TK.map && TK.map.invalidateSize(), 250);
}
function resetTools() {
    TK.view = 'loc'; TK.radar = true; TK.wx = new Map();
    document.querySelectorAll('#tkViewGroup .chip').forEach(x => x.classList.toggle('on', x.dataset.view === 'loc'));
    document.querySelectorAll('#tkBaseBox .bb').forEach(x => x.classList.toggle('on', x.dataset.base === 'default'));
    $('tkRadarGroup').style.display = 'none'; $('tkRadarBtn').classList.add('on'); $('tkClock').classList.remove('show');
    $('tkQ').value = ''; $('tkMsg').textContent = ''; $('tkEmpty').classList.remove('show');
}
async function openTrack(i) {
    const s = ROWS[i]; if (!s) return;
    closeDrawer(); closeModal();
    resetTools();
    $('tkNum').textContent = s.number;
    $('tkSub').textContent = [s.name, s.customer, s.carrier].filter(Boolean).join(' · ');
    $('tkStatus').className = 'status st-' + s.status; $('tkStatus').textContent = String(s.status).replace(/_/g, ' ');
    $('tkSide').innerHTML = '<div class="nodata"><i class="fa-solid fa-spinner fa-spin" style="font-size:24px;color:#9db6dc"></i><br><br>Mencari posisi barang…</div>';
    tkm.classList.add('open'); tkm.setAttribute('aria-hidden', 'false');
    initMap();
    clearInterval(TK.timer);
    TK.timer = setInterval(() => {
        document.querySelectorAll('#tkMap .tm').forEach(e => { e.textContent = fmtOff(+e.dataset.off); });
        if (TK.view === 'time') { $('ckLocal').textContent = new Date().toLocaleTimeString('id-ID', { hour12:false }).replace(/\./g, ':'); $('ckUtc').textContent = fmtOff(0); }
    }, 1000);
    const tok = ++TK.tok;
    const d = await buildTrack(s);
    if (tok !== TK.tok) return;
    TK.data = d; renderSide(d); legend(d); drawTrack(true);
}
function closeTrack() {
    if (!tkm.classList.contains('open')) return;
    TK.tok++; clearInterval(TK.timer); TK.timer = null;
    if (TK.map) { TK.map.remove(); TK.map = null; }
    TK.data = null; tkm.classList.remove('open'); tkm.setAttribute('aria-hidden', 'true');
}
$('tkClose').addEventListener('click', closeTrack);
tkm.addEventListener('click', e => { if (e.target === tkm) closeTrack(); });
document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (rm.classList.contains('open')) { closeReopen(); return; }
    if (pm.classList.contains('open')) { closePay(); return; }
    closeModal(); closeTrack();
    if (dr.classList.contains('open')) dismissDrawer();
});
$('ckZone').textContent = 'Waktu Anda · ' + (Intl.DateTimeFormat().resolvedOptions().timeZone || '').replace('_', ' ');

/* ----- kontrol peta ----- */
document.querySelectorAll('#tkBaseBox .bb').forEach(b => b.addEventListener('click', () => {
    if (!TK.map) return;
    document.querySelectorAll('#tkBaseBox .bb').forEach(x => x.classList.toggle('on', x === b));
    TK.map.removeLayer(TK.base); TK.base = TK.bases[b.dataset.base].addTo(TK.map);
    TK.base.bringToBack && TK.base.bringToBack();
}));
document.querySelectorAll('#tkViewGroup .chip').forEach(b => b.addEventListener('click', () => {
    if (!TK.map) return;
    document.querySelectorAll('#tkViewGroup .chip').forEach(x => x.classList.toggle('on', x === b));
    TK.view = b.dataset.view;
    $('tkRadarGroup').style.display = TK.view === 'wx' ? 'flex' : 'none';
    $('tkClock').classList.toggle('show', TK.view === 'time');
    if (TK.view === 'time') { $('ckLocal').textContent = new Date().toLocaleTimeString('id-ID', { hour12:false }).replace(/\./g, ':'); $('ckUtc').textContent = fmtOff(0); }
    setRadar(TK.view === 'wx' && TK.radar);
    drawTrack(false);
}));
$('tkRadarBtn').addEventListener('click', () => {
    TK.radar = !TK.radar; $('tkRadarBtn').classList.toggle('on', TK.radar); setRadar(TK.view === 'wx' && TK.radar);
});

/* cari tempat apa pun (kota, desa, jalan) lalu zoom ke sana */
$('tkQ').addEventListener('keydown', async e => {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const q = e.target.value.trim(); if (!q || !TK.map) return;
    $('tkMsg').textContent = 'Mencari…';
    try {
        const r = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&accept-language=id&q=' + encodeURIComponent(q));
        const j = await r.json();
        if (!j.length) { $('tkMsg').textContent = 'Lokasi tidak ditemukan.'; return; }
        const p = [+j[0].lat, +j[0].lon];
        TK.srch.clearLayers();
        L.marker(p).bindPopup('<div class="pop">' + esc(j[0].display_name) + '</div>').addTo(TK.srch).openPopup();
        TK.map.flyTo(p, 16, { duration:1.2 });
        $('tkMsg').textContent = '';
    } catch (_) { $('tkMsg').textContent = 'Pencarian gagal, coba lagi.'; }
});

/* ===== BUKA OTOMATIS DARI LINK LAMA (?buka=tambah|detail|lacak|edit|bayar&id=..) ===== */
(function () {
    const q = new URLSearchParams(location.search), b = q.get('buka');
    if (!b) return;
    if (b === 'tambah') openCreate();
    else {
        const i = ROWS.findIndex(r => String(r.id) === q.get('id'));
        if (i >= 0) { if (b === 'edit') openEdit(ROWS[i]); else if (b === 'lacak') openTrack(i); else if (b === 'bayar') openPay(i); else showDrawer(i); }
    }
    q.delete('buka'); q.delete('id');
    history.replaceState(null, '', location.pathname + (q.toString() ? '?' + q.toString() : ''));
})();

/* =====================================================================
   KURS MATA UANG SELURUH NEGARA
   Sumber: /pengiriman?kurs=1 (server, cache 1 jam), cadangan API publik langsung dari browser.
   Pencarian: nama negara (Indonesia & Inggris), kode negara, nama / kode mata uang.
   ===================================================================== */
(function () {
    const EP = @json(url('/pengiriman') . '?kurs=1');

    /* kode negara ISO : kode mata uang ("/" = kandidat, dipakai yang tersedia di data kurs) */
    const MAP = ('AF:AFN AL:ALL DZ:DZD AD:EUR AO:AOA AG:XCD AR:ARS AM:AMD AU:AUD AT:EUR AZ:AZN BS:BSD BH:BHD BD:BDT BB:BBD BY:BYN BE:EUR BZ:BZD BJ:XOF BT:BTN BO:BOB BA:BAM BW:BWP BR:BRL BN:BND BG:EUR/BGN BF:XOF BI:BIF CV:CVE KH:KHR CM:XAF CA:CAD CF:XAF TD:XAF CL:CLP CN:CNY CO:COP KM:KMF CG:XAF CD:CDF CR:CRC CI:XOF HR:EUR CU:CUP CY:EUR CZ:CZK DK:DKK DJ:DJF DM:XCD DO:DOP EC:USD EG:EGP SV:USD GQ:XAF ER:ERN EE:EUR SZ:SZL ET:ETB FJ:FJD FI:EUR FR:EUR GA:XAF GM:GMD GE:GEL DE:EUR GH:GHS GR:EUR GD:XCD GT:GTQ GN:GNF GW:XOF GY:GYD HT:HTG HN:HNL HK:HKD HU:HUF IS:ISK IN:INR ID:IDR IR:IRR IQ:IQD IE:EUR IL:ILS IT:EUR JM:JMD JP:JPY JO:JOD KZ:KZT KE:KES KI:AUD KP:KPW KR:KRW XK:EUR KW:KWD KG:KGS LA:LAK LV:EUR LB:LBP LS:LSL LR:LRD LY:LYD LI:CHF LT:EUR LU:EUR MO:MOP MG:MGA MW:MWK MY:MYR MV:MVR ML:XOF MT:EUR MH:USD MR:MRU/MRO MU:MUR MX:MXN FM:USD MD:MDL MC:EUR MN:MNT ME:EUR MA:MAD MZ:MZN MM:MMK NA:NAD NR:AUD NP:NPR NL:EUR NZ:NZD NI:NIO NE:XOF NG:NGN MK:MKD NO:NOK OM:OMR PK:PKR PW:USD PS:ILS PA:PAB PG:PGK PY:PYG PE:PEN PH:PHP PL:PLN PT:EUR QA:QAR RO:RON RU:RUB RW:RWF KN:XCD LC:XCD VC:XCD WS:WST SM:EUR ST:STN/STD SA:SAR SN:XOF RS:RSD SC:SCR SL:SLE/SLL SG:SGD SK:EUR SI:EUR SB:SBD SO:SOS ZA:ZAR SS:SSP ES:EUR LK:LKR SD:SDG SR:SRD SE:SEK CH:CHF SY:SYP TW:TWD TJ:TJS TZ:TZS TH:THB TL:USD TG:XOF TO:TOP TT:TTD TN:TND TR:TRY TM:TMT TV:AUD UG:UGX UA:UAH AE:AED GB:GBP US:USD UY:UYU UZ:UZS VU:VUV VA:EUR VE:VES VN:VND YE:YER ZM:ZMW ZW:ZWG/ZWL ' +
        'PR:USD GU:USD VI:USD AS:USD GL:DKK FO:DKK PF:XPF NC:XPF GI:GIP KY:KYD BM:BMD AW:AWG CW:ANG FK:FKP SH:SHP').split(' ').map(x => { const p = x.split(':'); return [p[0], p[1].split('/')]; });

    /* kata kunci tambahan agar pencarian "jerman / germany / deutschland / uk / usa" dll. selalu ketemu */
    const ALIAS = { DE:'deutschland', US:'usa america amerika serikat as', GB:'uk inggris england britania britain', NL:'holland', CZ:'ceko czechia czech', MM:'burma', SZ:'swaziland eswatini', MK:'makedonia macedonia',
        TR:'turkey turki turkiye', KR:'korsel south korea korea selatan', KP:'korut north korea korea utara', CI:'ivory coast pantai gading', AE:'uea emirates uni emirat arab', CN:'tiongkok cina china', RU:'rusia russia',
        SA:'saudi arab', JP:'jepang japan', TW:'taiwan', HK:'hongkong', VN:'vietnam', MY:'malaysia', SG:'singapura singapore', CH:'swiss switzerland', AT:'austria osterreich', CD:'kongo congo', CG:'kongo congo' };
    const FEAT = [['USD','US'], ['SGD','SG'], ['EUR','DE'], ['JPY','JP']];
    let rates = null, ents = [], byIso = {}, change = {}, since = null, updated = null;

    const mk = (type, l) => { try { return new Intl.DisplayNames([l], { type }); } catch (_) { return null; } };
    const dnR = mk('region', 'id'), dnRe = mk('region', 'en'), dnC = mk('currency', 'id');
    const nm = (d, c) => { try { return (d && d.of(c)) || c; } catch (_) { return c; } };

    function parse(j) {
        let r = j && (j.rates || j.conversion_rates);
        if (!r && j && j.usd) { r = {}; for (const k in j.usd) r[k.toUpperCase()] = j.usd[k]; }
        if (!r || typeof r !== 'object') return null;
        const o = {};
        for (const k in r) { const v = +r[k]; if (isFinite(v) && v > 0) o[k.toUpperCase()] = v; }
        o.USD = 1;   // semua kurs berbasis USD
        return o;
    }
    async function fetchRates() {
        const urls = [EP, 'https://open.er-api.com/v6/latest/USD', 'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/usd.json', 'https://latest.currency-api.pages.dev/v1/currencies/usd.json'];
        let last;
        for (const u of urls) {
            try {
                const r = await fetch(u, { headers:{ 'Accept':'application/json' } });
                const j = await r.json();
                if (!r.ok || j.success === false || j.result === 'error') throw new Error(j.message || ('HTTP ' + r.status));
                const p = parse(j);
                if (p && p.IDR && Object.keys(p).length > 30) return { rates:p, t:j.updated_at || j.time_last_update_utc || j.date || null };
            } catch (e) { last = e; }
        }
        throw last || new Error('Semua sumber kurs gagal dimuat.');
    }

    const idr = c => rates.IDR / rates[c];                              // 1 unit mata uang c = ? IDR
    const rp = v => 'Rp ' + nf(v, v < 100 ? 4 : 2);
    const flag = iso => '<img class="flag" src="https://flagcdn.com/w40/' + iso.toLowerCase() + '.png" alt="" loading="lazy">';

    function build() {
        ents = [];
        MAP.forEach(([iso, cl]) => {
            const cur = cl.find(c => rates[c]); if (!cur) return;
            const id = nm(dnR, iso), en = nm(dnRe, iso), cn = nm(dnC, cur);
            ents.push({ iso, cur, id, en, cn, text:[id, en, iso, cur, cn, ALIAS[iso] || ''].join(' ').toLowerCase() });
        });
        ents.sort((a, b) => a.id.localeCompare(b.id, 'id'));
        byIso = {}; ents.forEach(e => byIso[e.iso] = e);
    }
    function fillSelects() {
        const opt = ents.map(e => '<option value="' + e.iso + '">' + esc(e.id) + ' — ' + e.cur + '</option>').join('');
        const pf = $('fxFrom').value, pt = $('fxTo').value;
        $('fxFrom').innerHTML = opt; $('fxTo').innerHTML = opt;
        $('fxFrom').value = byIso[pf] ? pf : (byIso.DE ? 'DE' : ents[0].iso);
        $('fxTo').value = byIso[pt] ? pt : 'ID';
    }
    function calc() {
        if (!rates) return;
        const a = parseFloat($('fxAmt').value), f = byIso[$('fxFrom').value], t = byIso[$('fxTo').value];
        if (!f || !t || isNaN(a)) { $('fxOut').textContent = '—'; return; }
        const r1 = idr(f.cur) / idr(t.cur), v = a * r1;
        $('fxLine').textContent = nf(a, 6) + ' ' + f.cur + ' (' + f.id + ') =';
        $('fxOut').textContent = (t.cur === 'IDR' ? 'Rp ' : '') + nf(v, v < 1 ? 6 : 2) + (t.cur === 'IDR' ? '' : ' ' + t.cur);
        $('fxRate').textContent = '1 ' + f.cur + ' = ' + nf(r1, r1 < 1 ? 6 : 2) + ' ' + t.cur + '  ·  ' + t.id;
    }
    function chg(c) {
        const v = change[c];
        if (v === undefined || !isFinite(v)) return '<span class="chg na">—</span>';
        if (Math.abs(v) < 0.005) return '<span class="chg na">0,00%</span>';
        return '<span class="chg ' + (v > 0 ? 'up' : 'dn') + '">' + (v > 0 ? '▲ ' : '▼ ') + nf(Math.abs(v), 2) + '%</span>';
    }
    function pick(iso) {
        $('fxFrom').value = iso; $('fxTo').value = 'ID'; calc();
        document.querySelector('.fx-calc').scrollIntoView({ behavior:'smooth', block:'nearest' });
    }
    function list() {
        const q = $('fxQ').value.toLowerCase().trim();
        $('fxFeat').innerHTML = FEAT.filter(f => rates[f[0]]).map(f => '<button type="button" class="fx-card" data-iso="' + f[1] + '"><b>' + f[0] + '</b><span>' + rp(idr(f[0])) + '</span></button>').join('');
        const shown = ents.filter(e => e.iso !== 'ID' && (!q || e.text.includes(q)));
        $('fxRows').innerHTML = shown.length ? shown.map(e => {
            const x = idr(e.cur);
            return '<div class="fx-row" data-iso="' + e.iso + '"><div class="fx-code">' + flag(e.iso) + '</div><div class="fx-n"><strong>' + esc(e.id) + '</strong><small>' + esc(e.cn) + ' (' + e.cur + ') · 1 IDR = ' + nf(1 / x, 1 / x < 1 ? 6 : 4) + ' ' + e.cur + '</small></div><div class="fx-v"><b>' + rp(x) + '</b>' + chg(e.cur) + '</div></div>';
        }).join('') : '<div class="fx-state">Negara atau mata uang tidak ditemukan.</div>';
    }
    function snapshot() {
        try {
            const now = Date.now(), curr = {}; Object.keys(rates).forEach(c => { if (c !== 'IDR') curr[c] = idr(c); });
            const old = JSON.parse(localStorage.getItem('ct_fx2') || 'null');
            change = {}; since = null;
            if (old && now - old.t > 5 * 60 * 1000) { since = new Date(old.t); for (const c in curr) if (old.r[c]) change[c] = (curr[c] - old.r[c]) / old.r[c] * 100; }
            if (!old || now - old.t > 24 * 3600 * 1000) localStorage.setItem('ct_fx2', JSON.stringify({ t:now, r:curr }));
        } catch (_) {}
    }
    const fdt = d => d.toLocaleString('id-ID', { dateStyle:'medium', timeStyle:'short' });
    async function load() {
        $('fxMeta').textContent = 'Memuat data…';
        $('fxRows').innerHTML = '<div class="skel"></div><div class="skel"></div><div class="skel"></div><div class="skel"></div>';
        try {
            const res = await fetchRates();
            rates = res.rates; updated = res.t;
            build(); snapshot(); fillSelects(); list(); calc();
            const t = updated && !isNaN(new Date(updated)) ? new Date(updated) : new Date();
            $('fxMeta').textContent = ents.length + ' negara · diperbarui ' + fdt(t) + (since ? ' · perubahan dibanding ' + since.toLocaleDateString('id-ID', { day:'numeric', month:'short' }) : ' · perubahan tampil setelah dibuka lagi nanti');
        } catch (e) {
            $('fxMeta').textContent = 'Gagal memuat kurs';
            $('fxFeat').innerHTML = '';
            $('fxRows').innerHTML = '<div class="fx-state"><i class="fa-solid fa-triangle-exclamation" style="font-size:24px;color:#f59e0b"></i><br>' + esc(e.message || 'Kurs belum dapat dimuat.') + '<br>Periksa koneksi internet server / browser.<br><button class="btn" type="button" id="fxRetry">Coba lagi</button></div>';
            $('fxLine').textContent = 'Kurs belum tersedia'; $('fxOut').textContent = '—'; $('fxRate').textContent = '';
            $('fxRetry') && $('fxRetry').addEventListener('click', load);
        }
    }
    ['fxAmt', 'fxFrom', 'fxTo'].forEach(id => $(id).addEventListener('input', calc));
    $('fxSwap').addEventListener('click', () => { const f = $('fxFrom').value; $('fxFrom').value = $('fxTo').value; $('fxTo').value = f; calc(); });
    $('fxQ').addEventListener('input', () => rates && list());
    $('fxRefresh').addEventListener('click', load);
    $('kurs').addEventListener('click', e => { const el = e.target.closest('[data-iso]'); if (el && !el.matches('option')) pick(el.dataset.iso); });
    document.addEventListener('error', e => { if (e.target && e.target.classList && e.target.classList.contains('flag')) e.target.style.visibility = 'hidden'; }, true);
    load();
})();
</script>
</body>
</html>
