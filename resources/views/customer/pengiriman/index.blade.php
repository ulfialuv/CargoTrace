<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Pengiriman Saya | CargoTrace</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
:root{--navy:#071633;--blue:#2457f5;--sky:#38bdf8;--ink:#17306a;--mut:#6b7fa3;--line:#e1e9f7;--bg:#eef3fb;--h:'Sora',sans-serif}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--ink);font-size:13px}
body.lock{overflow:hidden}
a{color:inherit}
button{font-family:inherit}
:focus-visible{outline:2px solid var(--blue);outline-offset:2px}

/* SIDEBAR */
.sidebar{position:fixed;inset:0 auto 0 0;width:272px;padding:26px 18px 18px;display:flex;flex-direction:column;background:linear-gradient(180deg,#071633 0%,#0a2257 65%,#0f3a8f 100%);color:#fff;z-index:100;overflow-y:auto}
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
.top-right{display:flex;align-items:center;gap:12px}
.date-box{display:flex;align-items:center;gap:9px;padding:13px 17px;background:#fff;border:1px solid var(--line);border-radius:14px;color:#3d68a8;font-weight:600;white-space:nowrap}
.date-box i{color:var(--blue)}
.bell{position:relative;display:grid;place-items:center;width:46px;height:46px;background:#fff;border:1px solid var(--line);border-radius:14px;color:#3d68a8;text-decoration:none;font-size:16px}
.bell span{position:absolute;top:10px;right:11px;width:9px;height:9px;border-radius:50%;background:#ef4444;border:2px solid #fff}

/* HERO */
.hero{position:relative;overflow:hidden;min-height:290px;padding:42px 48px 116px;border-radius:32px;color:#fff;display:flex;justify-content:space-between;gap:28px;
 background:linear-gradient(100deg,#071633f5 0%,#0b2a6bdd 48%,#1d6fe866 100%),url('https://images.unsplash.com/photo-1494412651409-8963ce7935a7?auto=format&fit=crop&w=1900&q=80') center/cover,linear-gradient(135deg,#071633,#1d4ed8);box-shadow:0 28px 58px #0b2a6b46}
.hero::after{content:"";position:absolute;width:460px;height:460px;right:-130px;top:-220px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.live{display:inline-flex;align-items:center;gap:8px;margin-bottom:18px;padding:7px 14px;border-radius:20px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11px;font-weight:600}
.live i{width:8px;height:8px;border-radius:50%;background:#4ade80;animation:ping 1.8s infinite}
@keyframes ping{0%{box-shadow:0 0 0 0 #4ade80aa}70%{box-shadow:0 0 0 9px #4ade8000}100%{box-shadow:0 0 0 0 #4ade8000}}
.hero h2{font:800 32px/1.22 var(--h);letter-spacing:-1px;max-width:560px}
.hero p{margin-top:14px;max-width:490px;line-height:1.85;color:#cfe0ff}
.quick{display:flex;align-items:center;gap:8px;margin-top:22px;max-width:500px;padding:7px;border-radius:18px;background:#fff;box-shadow:0 18px 40px #02081c55}
.quick i{padding:0 8px 0 14px;color:#8ca6c4}
.quick input{flex:1;min-width:0;border:0;outline:0;background:transparent;font:500 13px 'Plus Jakarta Sans',sans-serif;color:var(--ink)}
.quick button{padding:13px 20px;border:0;border-radius:13px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer;white-space:nowrap}
.quick button:hover{filter:brightness(1.1)}
.hero-side{display:flex;flex-direction:column;gap:12px;align-self:flex-start}
.hchip{display:flex;align-items:center;gap:14px;min-width:230px;padding:15px 19px;background:#ffffff1c;border:1px solid #ffffff35;border-radius:18px;backdrop-filter:blur(10px);font-size:11.5px;color:#dbe9ff}
.hchip b{font:800 26px var(--h);color:#fff;min-width:38px}

/* STATISTIK */
.stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin:-76px 22px 28px;position:relative;z-index:3}
.stat{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:18px;background:#ffffffee;backdrop-filter:blur(10px);border:1px solid #fff;border-radius:22px;box-shadow:0 18px 40px #0b2a6b24;min-width:0;color:inherit;text-align:left;cursor:pointer;font:inherit;transition:transform .2s}
.stat:hover{transform:translateY(-4px)}
.stat small{display:block;font-size:11.5px;color:var(--mut);font-weight:600}
.stat b{display:block;margin-top:7px;font:800 28px var(--h);color:var(--navy);line-height:1}
.stat em{display:block;margin-top:8px;font-style:normal;font-size:10.5px;font-weight:700}
.ring{position:relative;flex:none;width:56px;height:56px}
.ring svg{width:56px;height:56px;transform:rotate(-90deg)}
.ring circle{fill:none;stroke-width:6;stroke-linecap:round}
.ring .rb{stroke:#e6eefc}
.ring i{position:absolute;inset:0;display:grid;place-items:center;font-size:15px}
.c-blue{color:#2457f5}.c-purple{color:#8b5cf6}.c-orange{color:#f59e0b}.c-green{color:#16a34a}.c-red{color:#e5484d}

/* PANEL */
.panel{padding:26px;background:#fff;border:1px solid var(--line);border-radius:28px;box-shadow:0 10px 30px #2457f50d}
.sect{margin-bottom:24px}
.ph{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:18px;flex-wrap:wrap}
.ph h2{font:700 18px var(--h);color:var(--navy);letter-spacing:-.3px}
.ph p{margin-top:6px;font-size:12px;color:var(--mut)}
.count{padding:8px 14px;border-radius:20px;background:#e8f1ff;color:#1d4ed8;font-size:11px;font-weight:700;white-space:nowrap}

/* FILTER */
.f-row{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:12px;margin-bottom:12px}
.f-row2{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.field label{display:block;font-size:11px;font-weight:700;color:#5d7aa5;margin-bottom:7px}
.field input,.field select{width:100%;height:42px;padding:0 13px;border:1px solid #dce8f8;border-radius:11px;background:#fbfdff;color:var(--ink);font:12px 'Plus Jakarta Sans',sans-serif;outline:none}
.field input:focus,.field select:focus{border-color:var(--blue);box-shadow:0 0 0 3px #2457f51f}
.f-act{display:flex;align-items:flex-end;gap:8px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 18px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:12px;font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.2s;box-shadow:0 10px 22px #2457f540;white-space:nowrap}
.btn:hover{filter:brightness(1.1);transform:translateY(-1px)}
.btn-light{background:#e8f1ff;color:#1d4ed8;box-shadow:none}

/* TOGGLE */
.toggle{display:inline-flex;padding:4px;border-radius:13px;background:#eaf1ff}
.toggle button{display:inline-flex;align-items:center;gap:7px;padding:9px 14px;border:0;border-radius:10px;background:transparent;color:#5d7aa5;font:700 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.toggle button.on{background:#fff;color:var(--blue);box-shadow:0 4px 12px #2457f522}

/* STATUS */
.status{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:20px;font-size:10.5px;font-weight:700;white-space:nowrap}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.status-transit{background:#e5f1ff;color:#2876c5}
.status-delivered{background:#e8f7ef;color:#26734d}
.status-created{background:#fff4dc;color:#b77900}
.status-delayed{background:#ffe9e9;color:#c92f3a}
.status-default{background:#eef0f4;color:#667085}

/* KARTU */
.cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
.sc{overflow:hidden;border-radius:26px;background:#fff;border:1px solid var(--line);box-shadow:0 12px 30px #2457f512;transition:.25s;display:flex;flex-direction:column;cursor:pointer}
.sc:hover{transform:translateY(-5px);box-shadow:0 22px 44px #2457f526}
.sc-photo{position:relative;min-height:138px;padding:16px 18px;color:#fff;display:flex;flex-direction:column;justify-content:space-between}
.sc-photo.darat{background:linear-gradient(180deg,#07163355 0%,#071633e8 100%),url('https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?auto=format&fit=crop&w=900&q=70') center/cover,linear-gradient(135deg,#0a2257,#2457f5)}
.sc-photo.laut{background:linear-gradient(180deg,#07163355 0%,#071633e8 100%),url('https://images.unsplash.com/photo-1494412651409-8963ce7935a7?auto=format&fit=crop&w=900&q=70') center/cover,linear-gradient(135deg,#0a2257,#0ea5e9)}
.sc-photo.udara{background:linear-gradient(180deg,#07163355 0%,#071633e8 100%),url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=900&q=70') center/cover,linear-gradient(135deg,#0a2257,#6366f1)}
.sc-top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px}
.sc-type{display:inline-flex;align-items:center;gap:7px;padding:6px 12px;border-radius:20px;background:#ffffff24;border:1px solid #ffffff3a;backdrop-filter:blur(8px);font-size:10.5px;font-weight:700}
.sc-num small{display:block;font-size:10.5px;color:#9fd0ff;font-weight:600}
.sc-num b{font:800 19px var(--h);letter-spacing:-.3px}
.sc-body{padding:20px 22px 22px;display:flex;flex-direction:column;gap:16px;flex:1}
.sc-body h3{font:700 15px var(--h);color:var(--navy);letter-spacing:-.2px}
.sc-body .sub{margin-top:5px;font-size:11.5px;color:var(--mut)}
.route{display:flex;align-items:center;gap:12px;padding:14px 16px;border-radius:16px;background:#f4f8ff;border:1px solid #e3ecfb}
.route .pt{flex:1;min-width:0}
.route .pt label{display:block;font-size:10.5px;color:#8ca6c4;margin-bottom:4px}
.route .pt strong{font-size:12.5px;color:var(--navy);overflow-wrap:anywhere}
.route .mid{display:grid;place-items:center;flex:none;width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font-size:12px}
.route .pt.r{text-align:right}
.bar{height:8px;border-radius:8px;background:#e6eefc;overflow:hidden}
.bar span{display:block;height:100%;border-radius:8px;background:linear-gradient(90deg,#38bdf8,#2457f5)}
.bar.red span{background:linear-gradient(90deg,#fb7185,#e5484d)}
.bar-l{display:flex;justify-content:space-between;margin-bottom:7px;font-size:11px;color:var(--mut);font-weight:600}
.sc-meta{display:grid;grid-template-columns:repeat(2,1fr);gap:10px 16px}
.sc-meta label{display:block;font-size:10.5px;color:#8ca6c4;margin-bottom:3px}
.sc-meta strong{font-size:12px;color:#2c4a7c}
.sc-foot{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:auto;padding-top:14px;border-top:1px dashed #dbe6f8}
.sc-foot span{font-size:11px;color:var(--mut)}

/* TABEL */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:1000px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:11px;font-weight:700;padding:13px 14px;white-space:nowrap}
th:first-child{border-radius:12px 0 0 12px}th:last-child{border-radius:0 12px 12px 0}
td{padding:14px;border-bottom:1px solid #edf3fa;color:#41608a;white-space:nowrap}
tbody tr{cursor:pointer}
tbody tr:hover{background:#f6faff}
td.num{font-weight:800;color:var(--navy)}
.act{display:inline-grid;place-items:center;width:34px;height:34px;border:0;border-radius:11px;background:#e8f1ff;color:var(--blue);cursor:pointer;transition:.2s}
.act:hover{background:var(--blue);color:#fff}
.view{display:none}.view.on{display:block}

.empty{padding:46px 14px;text-align:center;color:var(--mut)}
.empty>i{display:grid;place-items:center;width:70px;height:70px;margin:0 auto 14px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:28px}
.empty strong{display:block;font:700 16px var(--h);color:var(--navy);margin-bottom:6px}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top:22px;font-size:11px;color:#8ca6c4;flex-wrap:wrap}
.pager nav svg{width:16px;height:16px}

/* CTA */
.cta{position:relative;overflow:hidden;display:flex;justify-content:space-between;align-items:center;gap:24px;padding:38px 44px;border-radius:30px;color:#fff;margin-bottom:24px;
 background:linear-gradient(100deg,#071633f2 0%,#0b2a6bd0 55%,#2457f588 100%),url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1800&q=75') center/cover,linear-gradient(135deg,#071633,#1d4ed8);box-shadow:0 24px 50px #0b2a6b3c}
.cta h3{font:800 24px/1.3 var(--h);letter-spacing:-.6px;max-width:480px}
.cta p{margin-top:8px;color:#cfe0ff;max-width:440px;line-height:1.75}
.cta .btn{background:#fff;color:#1d4ed8;box-shadow:0 14px 28px #02081c44;padding:15px 24px;font-size:13px}
.footer{text-align:center;color:#8ca6c4;font-size:11px;margin-top:30px}

/* ===== PANEL DETAIL (DRAWER) ===== */
.ov{position:fixed;inset:0;background:#030b1fb0;backdrop-filter:blur(3px);opacity:0;visibility:hidden;transition:.3s;z-index:900}
.ov.on{opacity:1;visibility:visible}
.drawer{position:fixed;top:0;right:0;bottom:0;width:min(680px,100%);background:var(--bg);z-index:910;transform:translateX(100%);transition:transform .35s cubic-bezier(.2,.8,.2,1);display:flex;flex-direction:column;box-shadow:-30px 0 60px #02081c44}
.drawer.on{transform:none}
.d-close{position:absolute;top:18px;right:18px;z-index:5;width:40px;height:40px;border:1px solid #ffffff44;border-radius:12px;background:#ffffff22;color:#fff;font-size:15px;cursor:pointer;backdrop-filter:blur(6px)}
.d-close:hover{background:#ffffff40}
.d-scroll{flex:1;overflow-y:auto;overscroll-behavior:contain}

.dh{position:relative;padding:30px 28px 26px;color:#fff;overflow:hidden}
.dh.darat{background:linear-gradient(180deg,#071633cc,#071633f5),url('https://images.unsplash.com/photo-1601584115197-04ecc0da31d7?auto=format&fit=crop&w=1200&q=70') center/cover,#0a2257}
.dh.laut{background:linear-gradient(180deg,#071633cc,#071633f5),url('https://images.unsplash.com/photo-1494412651409-8963ce7935a7?auto=format&fit=crop&w=1200&q=70') center/cover,#0a2257}
.dh.udara{background:linear-gradient(180deg,#071633cc,#071633f5),url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=70') center/cover,#0a2257}
.dh small{display:block;font-size:11px;color:#9fd0ff;font-weight:600;margin-bottom:6px}
.dh h2{font:800 24px var(--h);letter-spacing:-.6px;padding-right:50px}
.dh p{margin-top:8px;color:#cfe0ff;font-size:12px;line-height:1.6}
.dh .row{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}
.dh .chip{display:inline-flex;align-items:center;gap:7px;padding:6px 12px;border-radius:20px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11px;font-weight:600}
.dh .status{background:#ffffffee}

.dr{display:flex;align-items:stretch;gap:14px;margin:-22px 20px 0;padding:18px 20px;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 16px 36px #0b2a6b22;position:relative}
.dr .pt{flex:1;min-width:0}
.dr .pt label{display:block;font-size:10.5px;color:#8ca6c4;margin-bottom:4px}
.dr .pt strong{font:700 14px var(--h);color:var(--navy);overflow-wrap:anywhere}
.dr .pt span{display:block;margin-top:3px;font-size:11px;color:var(--mut)}
.dr .pt.r{text-align:right}
.dr .mid{flex:none;align-self:center;display:grid;place-items:center;width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font-size:12px}

.steps{display:grid;grid-template-columns:repeat(5,1fr);gap:4px;margin:22px 22px 6px;position:relative}
.steps::before{content:"";position:absolute;left:10%;right:10%;top:15px;height:3px;background:#dbe6f8;border-radius:3px}
.steps .fill{position:absolute;left:10%;top:15px;height:3px;border-radius:3px;background:linear-gradient(90deg,#38bdf8,#2457f5)}
.step{position:relative;text-align:center;z-index:1}
.step i{display:grid;place-items:center;width:32px;height:32px;margin:0 auto 8px;border-radius:50%;background:#fff;border:2px solid #dbe6f8;color:#9bb0d0;font-size:11px;font-style:normal}
.step.done i{background:#2457f5;border-color:#2457f5;color:#fff}
.step.cur i{border-color:#2457f5;color:#2457f5;box-shadow:0 0 0 5px #2457f522}
.step small{display:block;font-size:10px;line-height:1.4;color:#8ca6c4}
.step.done small,.step.cur small{color:var(--navy);font-weight:700}
.alert{margin:14px 22px 0;padding:13px 15px;border-radius:14px;background:#fff0f0;color:#c0343f;font-size:11.5px;line-height:1.6;display:flex;gap:10px}

.tabs{position:sticky;top:0;z-index:4;display:flex;gap:4px;padding:16px 22px 12px;background:linear-gradient(var(--bg) 82%,#0000);overflow-x:auto}
.tabs button{flex:none;display:inline-flex;align-items:center;gap:7px;padding:10px 15px;border:0;border-radius:12px;background:transparent;color:#5d7aa5;font-size:12px;font-weight:700;cursor:pointer}
.tabs button:hover{background:#e3ecfc}
.tabs button.on{background:#fff;color:var(--blue);box-shadow:0 6px 16px #2457f522}

.sec{margin:0 22px 14px;padding:20px;background:#fff;border:1px solid var(--line);border-radius:20px}
.sec h4{display:flex;align-items:center;gap:10px;margin-bottom:16px;font:700 13.5px var(--h);color:var(--navy)}
.sec h4 i{display:grid;place-items:center;width:30px;height:30px;border-radius:10px;background:#e8f1ff;color:var(--blue);font-size:12px}
.kv{display:grid;grid-template-columns:1fr 1fr;gap:16px 20px}
.kv div.full{grid-column:1/-1}
.kv label{display:block;font-size:10.5px;color:#8ca6c4;margin-bottom:5px}
.kv strong{font-size:12.5px;color:#26417a;overflow-wrap:anywhere;font-weight:600}
.kv .na{color:#a9b6cc;font-weight:500}
.note{margin-top:14px;font-size:11px;color:#9aaac3;line-height:1.6}

.tl{position:relative}
.tl-i{position:relative;display:flex;gap:14px;padding-bottom:20px}
.tl-i:last-child{padding-bottom:0}
.tl-i:not(:last-child)::before{content:"";position:absolute;left:10px;top:24px;bottom:0;width:2px;background:#dbe6f8}
.tl-i>i{flex:none;display:grid;place-items:center;width:22px;height:22px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:9px;z-index:1}
.tl-i:first-child>i{background:var(--blue);color:#fff;box-shadow:0 0 0 5px #2457f522}
.tl-i b{display:block;font-size:12.5px;color:var(--navy)}
.tl-i p{margin-top:4px;font-size:11.5px;color:var(--mut);line-height:1.6}
.tl-i small{display:block;margin-top:4px;font-size:10.5px;color:#a3b2c9}
.ex{display:flex;justify-content:space-between;gap:12px;padding:14px;border:1px solid #edf2fa;border-radius:14px;margin-bottom:10px}
.ex:last-child{margin-bottom:0}
.ex b{font-size:12.5px;color:var(--navy)}
.ex p{margin-top:4px;font-size:11.5px;color:var(--mut);line-height:1.6}
.ok{text-align:center;padding:14px 8px;color:#8a9bb6;font-size:12px}
.ok i{display:block;font-size:24px;margin-bottom:8px;color:#16a34a}
.ok.none i{color:#a9b6cc}
.doc{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:13px 14px;border:1px solid #edf2fa;border-radius:14px;margin-bottom:10px;font-size:12px;font-weight:600;color:#26417a}
.doc:last-child{margin-bottom:0}
.doc>span i{margin-right:9px;color:var(--blue)}
.doc a{padding:7px 13px;border-radius:10px;background:#e8f1ff;color:var(--blue);text-decoration:none;font-size:11px;font-weight:700}
.doc a:hover{background:var(--blue);color:#fff}
.cost{display:flex;align-items:baseline;gap:8px;padding:16px;border-radius:16px;background:linear-gradient(135deg,#eaf2ff,#f6faff);margin-bottom:16px}
.cost b{font:800 24px var(--h);color:var(--navy);letter-spacing:-.5px}
.cost span{font-size:12px;font-weight:700;color:var(--blue)}
.cost em{font-style:normal;font-size:12.5px;color:var(--mut)}

.d-foot{flex:none;display:flex;gap:10px;padding:14px 22px;background:#fff;border-top:1px solid var(--line)}
.d-foot .btn{flex:1}

/* RESPONSIVE */
@media(max-width:1350px){.hero-side{display:none}.stats{grid-template-columns:repeat(3,minmax(0,1fr));margin-top:-60px}}
@media(max-width:1200px){.f-row,.f-row2{grid-template-columns:repeat(2,1fr)}.cards{grid-template-columns:1fr}}
@media(max-width:1100px){.content{padding:20px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .topline{flex-direction:column;align-items:flex-start}.date-box{display:none}
 .hero{padding:30px 22px 100px}.hero h2{font-size:23px}.stats{grid-template-columns:repeat(2,minmax(0,1fr));margin:-70px 6px 22px}.panel{padding:18px}
 .f-row,.f-row2{grid-template-columns:1fr}.f-act .btn{flex:1}.sc-meta{grid-template-columns:1fr}
 .cta{flex-direction:column;align-items:flex-start;padding:28px 24px}
 .kv{grid-template-columns:1fr}.dh{padding:26px 20px 24px}.dr,.sec,.steps,.alert{margin-left:14px;margin-right:14px}.tabs{padding-left:14px;padding-right:14px}
}
@media(max-width:480px){.stats{grid-template-columns:1fr}.route{flex-direction:column;align-items:stretch}.route .pt.r{text-align:left}.route .mid{align-self:center;transform:rotate(90deg)}.step small{font-size:9px}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>

<body>

@php
    $statusLabels = [
        'CREATED' => 'Dibuat',
        'BOOKED' => 'Dijadwalkan',
        'PICKED_UP' => 'Telah Dijemput',
        'IN_TRANSIT' => 'Dalam Perjalanan',
        'DELIVERED' => 'Terkirim',
        'DELAYED' => 'Terlambat',
    ];

    $stepOrder = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];
    $stepLabels = [
        'CREATED' => 'Pengiriman Dibuat',
        'BOOKED' => 'Dijadwalkan',
        'PICKED_UP' => 'Barang Dijemput',
        'IN_TRANSIT' => 'Dalam Perjalanan',
        'DELIVERED' => 'Barang Diterima',
    ];

    $getStatusLabel = fn ($s) => $statusLabels[strtoupper($s ?? '')] ?? ($s ?: 'Belum ada status');

    $getStatusClass = fn ($s) => match (strtoupper($s ?? '')) {
        'PICKED_UP', 'IN_TRANSIT' => 'status-transit',
        'DELIVERED' => 'status-delivered',
        'CREATED', 'BOOKED' => 'status-created',
        'DELAYED' => 'status-delayed',
        default => 'status-default',
    };

    $progressOf = fn ($s) => match (strtoupper($s ?? '')) {
        'CREATED' => 10, 'BOOKED' => 30, 'PICKED_UP' => 55,
        'IN_TRANSIT' => 78, 'DELIVERED' => 100, 'DELAYED' => 60,
        default => 5,
    };

    $transportOf = fn ($t) => match (strtoupper($t ?? '')) {
        'SEA' => ['laut', 'fa-ship', 'Jalur Laut'],
        'AIR' => ['udara', 'fa-plane', 'Jalur Udara'],
        default => ['darat', 'fa-truck', 'Jalur Darat'],
    };

    // Tanggal aman: tidak error jika format salah atau kosong.
    $fmt = function ($value, $format = 'd M Y') {
        if (!$value) return null;
        try { return \Carbon\Carbon::parse($value)->format($format); }
        catch (\Throwable $e) { return $value; }
    };

    // Nilai kosong ditampilkan sebagai teks redup.
    $kv = function ($label, $value, $full = false) {
        $empty = $value === null || $value === '';
        return '<div' . ($full ? ' class="full"' : '') . '><label>' . e($label) . '</label><strong'
            . ($empty ? ' class="na"' : '') . '>' . e($empty ? 'Belum tersedia' : $value) . '</strong></div>';
    };

    $base = max((int) $totalShipments, 1);
    $circ = 138.2;
    $pct = fn ($n) => round($n / $base * 100);
    $dash = fn ($n) => round($n / $base * $circ, 1);

    $unread = auth()->user()->unreadNotifications()->count();
    $userName = auth()->user()->name;
@endphp

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="brand">
        <div class="brand-logo"><i class="fa-solid fa-route"></i></div>
        <div class="brand-name">CargoTrace</div>
    </div>
    <div class="brand-sub">Logistics &amp; Shipment Tracking</div>

    <div class="s-wrap">
        <div class="s-title">Menu Customer</div>
        <nav class="s-menu">
            <a href="{{ route('customer.dashboard') }}" class="s-link {{ request()->is('customer/dashboard*') ? 'active' : '' }}">
                <i class="fa-solid fa-border-all"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('customer.pengiriman.index') }}" class="s-link {{ request()->is('customer/pengiriman*') ? 'active' : '' }}">
                <i class="fa-solid fa-box"></i><span>Pengiriman Saya</span>
            </a>
            <a href="{{ url('/customer/pelacakan') }}" class="s-link {{ request()->is('customer/pelacakan*') ? 'active' : '' }}">
                <i class="fa-solid fa-location-dot"></i><span>Pelacakan Pengiriman</span>
            </a>
            <a href="{{ url('/customer/notifikasi') }}" class="s-link {{ request()->is('customer/notifikasi*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i><span>Notifikasi</span>
                @if($unread > 0)<em class="bdg">{{ $unread }}</em>@endif
            </a>
            <a href="{{ url('/customer/profil') }}" class="s-link {{ request()->is('customer/profil*') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i><span>Profil Saya</span>
            </a>
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
            <h1>Pengiriman Saya</h1>
            <p>Kelola dan pantau seluruh informasi pengiriman barang Anda.</p>
        </div>
        <div class="top-right">
            <div class="date-box"><i class="fa-regular fa-calendar"></i> {{ now()->translatedFormat('d M Y') }}</div>
            <a href="{{ url('/customer/notifikasi') }}" class="bell" aria-label="Notifikasi">
                <i class="fa-regular fa-bell"></i>
                @if($unread > 0)<span></span>@endif
            </a>
        </div>
    </div>

    {{-- HERO --}}
    <div class="hero">
        <div>
            <div class="live"><i></i> Semua pengiriman terpantau</div>
            <h2>Seluruh pengiriman Anda, dalam satu tempat.</h2>
            <p>Periksa status, jadwal, rute, dan detail setiap pengiriman tanpa pindah halaman.</p>

            <form action="{{ route('customer.pengiriman.index') }}" method="GET" class="quick">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor resi atau nama barang..." aria-label="Cari pengiriman">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="hero-side">
            <div class="hchip"><b>{{ $inTransit }}</b> pengiriman<br>sedang dalam perjalanan</div>
            <div class="hchip"><b>{{ $delivered }}</b> pengiriman<br>telah tiba di tujuan</div>
        </div>
    </div>

    {{-- STATISTIK (klik untuk memfilter status) --}}
    <div class="stats">
        <a class="stat" href="{{ route('customer.pengiriman.index') }}" style="text-decoration:none">
            <div><small>Total Pengiriman</small><b>{{ $totalShipments }}</b><em class="c-blue">Semua pengiriman</em></div>
            <div class="ring">
                <svg viewBox="0 0 56 56"><circle class="rb" cx="28" cy="28" r="22"/><circle cx="28" cy="28" r="22" stroke="#2457f5" stroke-dasharray="{{ $circ }} {{ $circ }}"/></svg>
                <i class="fa-solid fa-boxes-stacked c-blue"></i>
            </div>
        </a>

        <a class="stat" href="{{ route('customer.pengiriman.index', ['status' => 'CREATED']) }}" style="text-decoration:none" title="Tampilkan yang berstatus Dibuat">
            <div><small>Sedang Diproses</small><b>{{ $processing }}</b><em class="c-purple">{{ $pct($processing) }}% dari total</em></div>
            <div class="ring">
                <svg viewBox="0 0 56 56"><circle class="rb" cx="28" cy="28" r="22"/><circle cx="28" cy="28" r="22" stroke="#8b5cf6" stroke-dasharray="{{ $dash($processing) }} {{ $circ }}"/></svg>
                <i class="fa-solid fa-clock c-purple"></i>
            </div>
        </a>

        <a class="stat" href="{{ route('customer.pengiriman.index', ['status' => 'IN_TRANSIT']) }}" style="text-decoration:none" title="Tampilkan yang sedang dalam perjalanan">
            <div><small>Dalam Perjalanan</small><b>{{ $inTransit }}</b><em class="c-orange">{{ $pct($inTransit) }}% dari total</em></div>
            <div class="ring">
                <svg viewBox="0 0 56 56"><circle class="rb" cx="28" cy="28" r="22"/><circle cx="28" cy="28" r="22" stroke="#f59e0b" stroke-dasharray="{{ $dash($inTransit) }} {{ $circ }}"/></svg>
                <i class="fa-solid fa-truck-fast c-orange"></i>
            </div>
        </a>

        <a class="stat" href="{{ route('customer.pengiriman.index', ['status' => 'DELIVERED']) }}" style="text-decoration:none" title="Tampilkan yang sudah terkirim">
            <div><small>Terkirim</small><b>{{ $delivered }}</b><em class="c-green">{{ $pct($delivered) }}% selesai</em></div>
            <div class="ring">
                <svg viewBox="0 0 56 56"><circle class="rb" cx="28" cy="28" r="22"/><circle cx="28" cy="28" r="22" stroke="#16a34a" stroke-dasharray="{{ $dash($delivered) }} {{ $circ }}"/></svg>
                <i class="fa-solid fa-circle-check c-green"></i>
            </div>
        </a>

        <a class="stat" href="{{ route('customer.pengiriman.index', ['status' => 'DELAYED']) }}" style="text-decoration:none" title="Tampilkan yang terlambat">
            <div><small>Terlambat</small><b>{{ $delayed }}</b><em class="c-red">{{ $pct($delayed) }}% perlu perhatian</em></div>
            <div class="ring">
                <svg viewBox="0 0 56 56"><circle class="rb" cx="28" cy="28" r="22"/><circle cx="28" cy="28" r="22" stroke="#e5484d" stroke-dasharray="{{ $dash($delayed) }} {{ $circ }}"/></svg>
                <i class="fa-solid fa-triangle-exclamation c-red"></i>
            </div>
        </a>
    </div>

    {{-- FILTER --}}
    <section class="panel sect">
        <div class="ph"><div><h2>Cari Pengiriman</h2><p>Gunakan pencarian dan filter untuk menemukan pengiriman tertentu.</p></div></div>

        <form method="GET" action="{{ route('customer.pengiriman.index') }}">
            <div class="f-row">
                <div class="field">
                    <label for="f-search">Nomor Resi / Nama Barang</label>
                    <input id="f-search" type="text" name="search" value="{{ request('search') }}" placeholder="Contoh: CT-2026-001">
                </div>
                <div class="field">
                    <label for="f-status">Status Pengiriman</label>
                    <select id="f-status" name="status">
                        <option value="">Semua Status</option>
                        @foreach($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="f-carrier">Perusahaan Pengiriman</label>
                    <select id="f-carrier" name="carrier">
                        <option value="">Semua Perusahaan</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier }}" @selected(request('carrier') === $carrier)>{{ $carrier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="f-origin">Lokasi Asal</label>
                    <input id="f-origin" type="text" name="origin" value="{{ request('origin') }}" placeholder="Cari kota asal">
                </div>
            </div>

            <div class="f-row2">
                <div class="field">
                    <label for="f-dest">Lokasi Tujuan</label>
                    <input id="f-dest" type="text" name="destination" value="{{ request('destination') }}" placeholder="Cari kota tujuan">
                </div>
                <div class="field">
                    <label for="f-from">Tanggal Dari</label>
                    <input id="f-from" type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="field">
                    <label for="f-to">Tanggal Sampai</label>
                    <input id="f-to" type="date" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="f-act">
                    <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                    <a href="{{ route('customer.pengiriman.index') }}" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i> Reset</a>
                </div>
            </div>
        </form>
    </section>

    {{-- DAFTAR PENGIRIMAN --}}
    <section class="panel sect">
        <div class="ph">
            <div><h2>Daftar Pengiriman</h2><p>Pilih sebuah pengiriman untuk melihat detail lengkapnya.</p></div>

            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                <span class="count">{{ $shipments->total() }} data ditemukan</span>
                <div class="toggle" aria-label="Tampilan">
                    <button type="button" class="on" data-view="cards"><i class="fa-solid fa-table-cells-large"></i> Kartu</button>
                    <button type="button" data-view="table"><i class="fa-solid fa-list"></i> Tabel</button>
                </div>
            </div>
        </div>

        @if($shipments->count() === 0)

            <div class="empty">
                <i class="fa-solid fa-box-open"></i>
                <strong>Belum ada pengiriman</strong>
                Pengiriman milik akun Anda akan tampil di halaman ini.
            </div>

        @else

            {{-- KARTU --}}
            <div class="view on" id="view-cards">
                <div class="cards">
                    @foreach($shipments as $shipment)
                        @php
                            [$tClass, $tIcon, $tLabel] = $transportOf($shipment->transport_type ?? null);
                            $prog = $progressOf($shipment->status);
                            $isDelayed = strtoupper($shipment->status ?? '') === 'DELAYED';
                        @endphp

                        <article class="sc" data-open="{{ $shipment->id }}" tabindex="0" role="button" aria-label="Lihat detail {{ $shipment->shipment_number }}">
                            <div class="sc-photo {{ $tClass }}">
                                <div class="sc-top">
                                    <span class="sc-type"><i class="fa-solid {{ $tIcon }}"></i> {{ $tLabel }}</span>
                                    <span class="status {{ $getStatusClass($shipment->status) }}" style="background:#ffffffee">{{ $getStatusLabel($shipment->status) }}</span>
                                </div>
                                <div class="sc-num">
                                    <small>Nomor resi</small>
                                    <b>{{ $shipment->shipment_number }}</b>
                                </div>
                            </div>

                            <div class="sc-body">
                                <div>
                                    <h3>{{ $shipment->shipment_name }}</h3>
                                    <p class="sub">{{ $shipment->cargo_type ?: 'Jenis barang tidak dicantumkan' }} &middot; {{ $shipment->carrier ?: '-' }}</p>
                                </div>

                                <div class="route">
                                    <div class="pt"><label>Asal</label><strong>{{ $shipment->origin ?: '-' }}</strong></div>
                                    <div class="mid"><i class="fa-solid fa-arrow-right"></i></div>
                                    <div class="pt r"><label>Tujuan</label><strong>{{ $shipment->destination ?: '-' }}</strong></div>
                                </div>

                                <div>
                                    <div class="bar-l"><span>Progres pengiriman</span><span>{{ $prog }}%</span></div>
                                    <div class="bar {{ $isDelayed ? 'red' : '' }}"><span style="width:{{ $prog }}%"></span></div>
                                </div>

                                <div class="sc-meta">
                                    <div>
                                        <label>Tanggal Kirim</label>
                                        <strong>{{ $fmt($shipment->estimated_departure) ?? ($shipment->created_at?->format('d M Y') ?? '-') }}</strong>
                                    </div>
                                    <div>
                                        <label>Estimasi Tiba</label>
                                        <strong>{{ $fmt($shipment->estimated_arrival) ?? '-' }}</strong>
                                    </div>
                                </div>

                                <div class="sc-foot">
                                    <span><i class="fa-regular fa-clock"></i> Dibuat {{ $shipment->created_at?->format('d M Y') ?? '-' }}</span>
                                    <button type="button" class="btn" data-open="{{ $shipment->id }}"><i class="fa-solid fa-eye"></i> Lihat Detail</button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            {{-- TABEL --}}
            <div class="view" id="view-table">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th><th>Nomor Resi</th><th>Barang</th><th>Jenis Barang</th><th>Perusahaan</th>
                                <th>Asal</th><th>Tujuan</th><th>Tanggal Kirim</th><th>Estimasi Tiba</th><th>Status</th><th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shipments as $index => $shipment)
                                <tr data-open="{{ $shipment->id }}">
                                    <td>{{ $shipments->firstItem() + $index }}</td>
                                    <td class="num">{{ $shipment->shipment_number }}</td>
                                    <td class="num">{{ $shipment->shipment_name }}</td>
                                    <td>{{ $shipment->cargo_type ?: '-' }}</td>
                                    <td>{{ $shipment->carrier ?: '-' }}</td>
                                    <td>{{ $shipment->origin ?: '-' }}</td>
                                    <td>{{ $shipment->destination ?: '-' }}</td>
                                    <td>{{ $fmt($shipment->estimated_departure) ?? ($shipment->created_at?->format('d M Y') ?? '-') }}</td>
                                    <td>{{ $fmt($shipment->estimated_arrival) ?? '-' }}</td>
                                    <td><span class="status {{ $getStatusClass($shipment->status) }}">{{ $getStatusLabel($shipment->status) }}</span></td>
                                    <td>
                                        <button type="button" class="act" data-open="{{ $shipment->id }}" title="Lihat Detail" aria-label="Lihat detail {{ $shipment->shipment_number }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @endif

        <div class="pager">
            <span>Menampilkan {{ $shipments->firstItem() ?? 0 }} sampai {{ $shipments->lastItem() ?? 0 }} dari {{ $shipments->total() }} pengiriman</span>
            @if($shipments->hasPages())<div>{{ $shipments->links() }}</div>@endif
        </div>
    </section>

    {{-- CTA --}}
    <div class="cta">
        <div>
            <h3>Ingin tahu posisi barang Anda saat ini?</h3>
            <p>Masukkan nomor resi dan lihat perjalanan pengiriman Anda secara lengkap, dari asal sampai tujuan.</p>
        </div>
        <a href="{{ url('/customer/pelacakan') }}" class="btn"><i class="fa-solid fa-location-dot"></i> Lacak Pengiriman</a>
    </div>

    <footer class="footer">
        © {{ date('Y') }} CargoTrace. Sistem Informasi Pelacakan Pengiriman.
    </footer>

</section>
</main>


{{-- ========== TEMPLATE DETAIL (tersembunyi, dipindahkan ke panel saat dibuka) ========== --}}
@foreach($detailShipments as $shipment)
@php
    $d = $details[$shipment->id];
    $latest = $latestTrackings[$shipment->id] ?? null;
    $cur = strtoupper($shipment->status ?? 'CREATED');
    $curIdx = array_search($cur, $stepOrder);
    $curIdx = $curIdx === false ? -1 : $curIdx;
    [$tClass, $tIcon, $tLabel] = $transportOf($shipment->transport_type ?? null);
    $events = $shipment->trackingEvents->sortByDesc('event_time');
    $exceptions = $shipment->exceptions->sortByDesc('created_at');
    $docs = [
        'Bukti / Tanda Terima' => $d['receipt_document'],
        'Dokumen Pengiriman' => $d['shipment_document'],
        'Invoice Pengiriman' => $d['invoice_document'],
    ];
    $fillPct = $curIdx > 0 ? min($curIdx / 4, 1) * 80 : 0;
@endphp

<div id="dp-{{ $shipment->id }}" hidden>

    <header class="dh {{ $tClass }}">
        <small>Nomor resi pengiriman</small>
        <h2>{{ $shipment->shipment_number }}</h2>
        <p>{{ $shipment->shipment_name }}</p>
        <div class="row">
            <span class="status {{ $getStatusClass($shipment->status) }}">{{ $getStatusLabel($shipment->status) }}</span>
            <span class="chip"><i class="fa-solid {{ $tIcon }}"></i> {{ $tLabel }}</span>
            <span class="chip"><i class="fa-solid fa-building"></i> {{ $shipment->carrier ?: 'Perusahaan belum tersedia' }}</span>
        </div>
    </header>

    <div class="dr">
        <div class="pt">
            <label>Asal</label>
            <strong>{{ $d['origin_city'] ?? $shipment->origin ?? '-' }}</strong>
            <span>{{ $d['origin_country'] ?? '' }}</span>
        </div>
        <div class="mid"><i class="fa-solid fa-arrow-right"></i></div>
        <div class="pt r">
            <label>Tujuan</label>
            <strong>{{ $d['destination_city'] ?? $shipment->destination ?? '-' }}</strong>
            <span>{{ $d['destination_country'] ?? '' }}</span>
        </div>
    </div>

    <div class="steps">
        <div class="fill" style="width:{{ $fillPct }}%"></div>
        @foreach($stepOrder as $i => $st)
            <div class="step {{ $i < $curIdx ? 'done' : ($i === $curIdx ? 'cur' : '') }}">
                <i>@if($i < $curIdx)<span class="fa-solid fa-check"></span>@else{{ $i + 1 }}@endif</i>
                <small>{{ $stepLabels[$st] }}</small>
            </div>
        @endforeach
    </div>

    @if($cur === 'DELAYED')
        <div class="alert">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Pengiriman ini terlambat. Lihat tab Riwayat untuk catatan perjalanan dan pengecualian.</span>
        </div>
    @endif

    <div class="tabs" role="tablist">
        <button type="button" class="on" data-tab="info"><i class="fa-solid fa-circle-info"></i> Informasi</button>
        <button type="button" data-tab="pihak"><i class="fa-solid fa-users"></i> Pengirim &amp; Penerima</button>
        <button type="button" data-tab="riwayat"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat</button>
        <button type="button" data-tab="biaya"><i class="fa-solid fa-receipt"></i> Biaya &amp; Dokumen</button>
    </div>

    {{-- TAB: INFORMASI --}}
    <section class="sec" data-sec="info">
        <h4><i class="fa-solid fa-truck-fast"></i> Informasi pengiriman</h4>
        <div class="kv">
            {!! $kv('Nomor Pengiriman', $shipment->shipment_number) !!}
            {!! $kv('Status Terkini', $getStatusLabel($shipment->status)) !!}
            {!! $kv('Tanggal Dibuat', $fmt($shipment->created_at)) !!}
            {!! $kv('Jenis Transportasi', $shipment->transport_type) !!}
            {!! $kv('Tanggal Keberangkatan', $fmt($shipment->estimated_departure)) !!}
            {!! $kv('Estimasi Tiba', $fmt($shipment->estimated_arrival)) !!}
            {!! $kv('Pembaruan Tracking Terakhir', $fmt($latest?->event_time, 'd M Y, H:i'), true) !!}
        </div>
    </section>

    <section class="sec" data-sec="info">
        <h4><i class="fa-solid fa-box"></i> Informasi barang</h4>
        <div class="kv">
            {!! $kv('Nama Barang', $shipment->shipment_name) !!}
            {!! $kv('Jenis / Kategori', $shipment->cargo_type) !!}
            {!! $kv('Berat Barang', $shipment->weight !== null ? $shipment->weight . ' kg' : null) !!}
            {!! $kv('Volume Barang', $shipment->volume !== null ? $shipment->volume . ' m³' : null) !!}
            {!! $kv('Jumlah Barang', $d['quantity']) !!}
            {!! $kv('Deskripsi Barang', $d['description'], true) !!}
        </div>
    </section>

    <section class="sec" data-sec="info">
        <h4><i class="fa-solid fa-building"></i> Perusahaan pengiriman</h4>
        <div class="kv">
            {!! $kv('Nama Perusahaan Logistik', $shipment->carrier) !!}
            {!! $kv('Jenis Layanan / Transportasi', $d['service_type']) !!}
        </div>
    </section>

    <section class="sec" data-sec="info">
        <h4><i class="fa-solid fa-map-location-dot"></i> Informasi lokasi</h4>
        <div class="kv">
            {!! $kv('Negara Asal', $d['origin_country']) !!}
            {!! $kv('Kota Asal', $d['origin_city'] ?? $shipment->origin) !!}
            {!! $kv('Negara Tujuan', $d['destination_country']) !!}
            {!! $kv('Kota Tujuan', $d['destination_city'] ?? $shipment->destination) !!}
        </div>
    </section>

    {{-- TAB: PENGIRIM & PENERIMA --}}
    <section class="sec" data-sec="pihak" hidden>
        <h4><i class="fa-solid fa-user"></i> Pengirim</h4>
        <div class="kv">
            {!! $kv('Nama Pengirim', $d['sender_name']) !!}
            {!! $kv('Nomor Telepon', $d['sender_phone']) !!}
            {!! $kv('Alamat Pengirim', $d['sender_address'], true) !!}
        </div>
    </section>

    <section class="sec" data-sec="pihak" hidden>
        <h4><i class="fa-solid fa-user-check"></i> Penerima</h4>
        <div class="kv">
            {!! $kv('Nama Penerima', $d['receiver_name']) !!}
            {!! $kv('Nomor Telepon', $d['receiver_phone']) !!}
            {!! $kv('Alamat Penerima', $d['receiver_address'], true) !!}
        </div>
    </section>

    {{-- TAB: RIWAYAT --}}
    <section class="sec" data-sec="riwayat" hidden>
        <h4><i class="fa-solid fa-clock-rotate-left"></i> Riwayat perjalanan</h4>

        @if($events->isEmpty())
            <div class="ok none"><i class="fa-solid fa-clock"></i> Belum ada riwayat perjalanan untuk pengiriman ini.</div>
        @else
            <div class="tl">
                @foreach($events as $event)
                    <div class="tl-i">
                        <i class="fa-solid fa-check"></i>
                        <div>
                            <b>{{ $statusLabels[strtoupper($event->status ?? '')] ?? ($event->status ?? 'Pembaruan Status') }}</b>
                            <p><i class="fa-solid fa-location-dot"></i> {{ $event->location ?: 'Lokasi belum tersedia' }}</p>
                            @if($event->description)<p>{{ $event->description }}</p>@endif
                            <small>{{ $fmt($event->event_time, 'd M Y, H:i') ?? '-' }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="sec" data-sec="riwayat" hidden>
        <h4><i class="fa-solid fa-triangle-exclamation"></i> Pengecualian</h4>

        @forelse($exceptions as $exception)
            <div class="ex">
                <div>
                    <b>{{ str_replace('_', ' ', $exception->type ?? 'Pengecualian') }}</b>
                    <p>{{ $exception->description ?: 'Tidak ada deskripsi.' }}</p>
                    <p>{{ $fmt($exception->occurred_at ?? $exception->created_at, 'd M Y, H:i') ?? '-' }} &middot; {{ $exception->location ?: 'Lokasi tidak tersedia' }}</p>
                </div>
                <span class="status {{ strtoupper($exception->status ?? '') === 'RESOLVED' ? 'status-delivered' : 'status-delayed' }}" style="align-self:flex-start">
                    {{ strtoupper($exception->status ?? '') === 'RESOLVED' ? 'Selesai' : 'Terbuka' }}
                </span>
            </div>
        @empty
            <div class="ok"><i class="fa-solid fa-circle-check"></i> Tidak ada pengecualian pada pengiriman ini.</div>
        @endforelse
    </section>

    {{-- TAB: BIAYA & DOKUMEN --}}
    <section class="sec" data-sec="biaya" hidden>
        <h4><i class="fa-solid fa-money-bill-wave"></i> Biaya pengiriman</h4>

        @if($d['shipping_cost'] !== null)
            <div class="cost">
                <span>{{ $d['currency'] ?? '' }}</span>
                <b>{{ number_format((float) $d['shipping_cost'], 2, ',', '.') }}</b>
            </div>
        @else
            <div class="cost"><em>Biaya belum dicatat untuk pengiriman ini.</em></div>
        @endif

        <div class="kv">
            {!! $kv('Mata Uang', $d['currency']) !!}
        </div>
    </section>

    <section class="sec" data-sec="biaya" hidden>
        <h4><i class="fa-solid fa-file-lines"></i> Dokumen pengiriman</h4>

        @php $hasDoc = false; @endphp
        @foreach($docs as $label => $path)
            @if($path)
                @php $hasDoc = true; @endphp
                <div class="doc">
                    <span><i class="fa-solid fa-file-pdf"></i>{{ $label }}</span>
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($path) }}" target="_blank" rel="noopener">Lihat dokumen</a>
                </div>
            @endif
        @endforeach

        @unless($hasDoc)
            <div class="ok none"><i class="fa-regular fa-folder-open"></i> Belum ada dokumen pengiriman yang tersedia.</div>
        @endunless
    </section>

    <div style="height:18px"></div>
    <input type="hidden" class="dp-track" value="{{ url('/customer/pelacakan?tracking=' . urlencode($shipment->shipment_number)) }}">
</div>
@endforeach


{{-- PANEL DETAIL --}}
<div class="ov" id="ov"></div>
<aside class="drawer" id="drawer" role="dialog" aria-modal="true" aria-label="Detail pengiriman" aria-hidden="true">
    <button type="button" class="d-close" id="dClose" aria-label="Tutup detail"><i class="fa-solid fa-xmark"></i></button>
    <div class="d-scroll" id="dBody"></div>
    <div class="d-foot">
        <a href="#" class="btn" id="dTrack"><i class="fa-solid fa-location-dot"></i> Lacak Pengiriman</a>
        <button type="button" class="btn btn-light" id="dBack"><i class="fa-solid fa-arrow-left"></i> Kembali ke daftar</button>
    </div>
</aside>

<script>
(function () {
    // Pergantian tampilan kartu / tabel
    var toggles = document.querySelectorAll('.toggle button');
    var views = { cards: document.getElementById('view-cards'), table: document.getElementById('view-table') };

    toggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            toggles.forEach(function (b) { b.classList.remove('on'); });
            btn.classList.add('on');
            Object.keys(views).forEach(function (k) {
                if (views[k]) views[k].classList.toggle('on', k === btn.dataset.view);
            });
        });
    });

    // Panel detail
    var drawer = document.getElementById('drawer');
    var ov = document.getElementById('ov');
    var body = document.getElementById('dBody');
    var track = document.getElementById('dTrack');
    var lastFocus = null;

    function showTab(name) {
        body.querySelectorAll('.tabs button').forEach(function (b) {
            b.classList.toggle('on', b.dataset.tab === name);
        });
        body.querySelectorAll('[data-sec]').forEach(function (s) {
            s.hidden = s.dataset.sec !== name;
        });
    }

    function openDetail(id) {
        var src = document.getElementById('dp-' + id);
        if (!src) return;

        lastFocus = document.activeElement;
        body.innerHTML = src.innerHTML;
        body.scrollTop = 0;

        var t = body.querySelector('.dp-track');
        track.href = t ? t.value : '#';

        showTab('info');
        drawer.classList.add('on');
        ov.classList.add('on');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.classList.add('lock');
        document.getElementById('dClose').focus();

        try {
            var u = new URL(window.location);
            u.searchParams.set('detail', id);
            history.replaceState(null, '', u);
        } catch (e) {}
    }

    function closeDetail() {
        drawer.classList.remove('on');
        ov.classList.remove('on');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('lock');

        try {
            var u = new URL(window.location);
            u.searchParams.delete('detail');
            history.replaceState(null, '', u);
        } catch (e) {}

        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }

    document.addEventListener('click', function (e) {
        var tab = e.target.closest('.tabs button');
        if (tab && body.contains(tab)) { showTab(tab.dataset.tab); return; }

        var opener = e.target.closest('[data-open]');
        if (opener) { openDetail(opener.dataset.open); }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && drawer.classList.contains('on')) closeDetail();

        if ((e.key === 'Enter' || e.key === ' ') && e.target.matches('article.sc[data-open]')) {
            e.preventDefault();
            openDetail(e.target.dataset.open);
        }
    });

    ov.addEventListener('click', closeDetail);
    document.getElementById('dClose').addEventListener('click', closeDetail);
    document.getElementById('dBack').addEventListener('click', closeDetail);

    // Buka otomatis jika ada ?detail=ID
    @if($openId)
        openDetail({{ (int) $openId }});
    @endif
})();
</script>

</body>
</html>
