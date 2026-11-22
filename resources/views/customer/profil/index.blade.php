<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Profil Saya | CargoTrace</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
:root{--navy:#071633;--blue:#2457f5;--sky:#38bdf8;--ink:#17306a;--mut:#6b7fa3;--line:#e1e9f7;--bg:#eef3fb;--h:'Sora',sans-serif}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--ink);font-size:13px}
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
.s-avatar{display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2457f5);font-weight:800;flex:none;overflow:hidden}
.s-avatar img{width:100%;height:100%;object-fit:cover}
.s-user b{display:block;font-size:12.5px}.s-user small{font-size:10px;color:#a9c4f0}
.s-user button{width:100%;margin-top:12px;padding:9px;border:1px solid #ffffff33;border-radius:10px;background:transparent;color:#dce8ff;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.s-user button:hover{background:#ffffff1c}

/* MAIN */
.main{margin-left:272px;min-height:100vh;background:radial-gradient(circle at 100% 0,#cfe0ff 0,#0000 380px),radial-gradient(circle at 0 100%,#d7e8ff 0,#0000 340px),var(--bg)}
.content{padding:26px 32px 40px}
.topline{margin-bottom:18px}
.topline h1{font:800 26px var(--h);color:var(--navy);letter-spacing:-.6px}
.topline p{margin-top:5px;color:var(--mut)}

.alert{display:flex;gap:12px;padding:15px 18px;margin-bottom:18px;border-radius:16px;font-size:12.5px;line-height:1.7}
.alert>i{margin-top:3px}
.alert ul{margin:6px 0 0 18px}
.alert-ok{background:#e6f7ee;border:1px solid #c4ebd5;color:#15693f;font-weight:600;animation:toast .4s both}
.alert-err{background:#fff0f1;border:1px solid #ffd9dc;color:#b42332}
@keyframes toast{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}

/* HERO */
.hero{position:relative;overflow:hidden;min-height:280px;padding:40px 44px 110px;border-radius:32px;color:#fff;display:flex;justify-content:space-between;align-items:flex-start;gap:28px;
 background:linear-gradient(100deg,#071633f5 0%,#0b2a6bdd 52%,#1d6fe866 100%),url('https://images.unsplash.com/photo-1553413077-190dd305871c?auto=format&fit=crop&w=1900&q=75') center/cover,linear-gradient(135deg,#071633,#1d4ed8);box-shadow:0 28px 58px #0b2a6b46}
.hero::after{content:"";position:absolute;width:460px;height:460px;right:-130px;top:-220px;border-radius:50%;background:radial-gradient(#7cc8ff55,#0000 70%)}
.hero>*{position:relative;z-index:2}
.who-box{display:flex;align-items:center;gap:24px;min-width:0}
.ava-wrap{position:relative;flex:none}
.ava{display:grid;place-items:center;width:112px;height:112px;border-radius:34px;border:3px solid #ffffff88;background:linear-gradient(135deg,#38bdf8,#2457f5);font:800 38px var(--h);overflow:hidden;box-shadow:0 18px 40px #02081c55}
.ava img{width:100%;height:100%;object-fit:cover}
.cam{position:absolute;right:-8px;bottom:-8px;display:grid;place-items:center;width:38px;height:38px;border:3px solid #0b2a6b;border-radius:13px;background:#fff;color:var(--blue);font-size:14px;cursor:pointer;transition:.2s}
.cam:hover{background:var(--blue);color:#fff}
.who-box h2{font:800 28px/1.2 var(--h);letter-spacing:-.8px;overflow-wrap:anywhere}
.who-box p{margin-top:8px;color:#cfe0ff;font-size:13px;overflow-wrap:anywhere}
.badges{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}
.badge{display:inline-flex;align-items:center;gap:7px;padding:7px 13px;border-radius:20px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11.5px;font-weight:600;backdrop-filter:blur(8px)}
.badge.ok i{color:#4ade80}

.comp{flex:none;display:flex;align-items:center;gap:16px;min-width:270px;max-width:320px;padding:18px 20px;border-radius:24px;background:#ffffff1c;border:1px solid #ffffff35;backdrop-filter:blur(10px)}
.ring{position:relative;flex:none;width:66px;height:66px}
.ring svg{width:66px;height:66px;transform:rotate(-90deg)}
.ring circle{fill:none;stroke-width:7;stroke-linecap:round}
.ring .rb{stroke:#ffffff30}
.ring .rf{stroke:#7dd3fc;transition:stroke-dasharray 1.2s cubic-bezier(.2,.8,.2,1)}
.ring b{position:absolute;inset:0;display:grid;place-items:center;font:800 15px var(--h)}
.comp small{display:block;font-size:11.5px;color:#cfe0ff;line-height:1.6}
.comp strong{display:block;margin-bottom:3px;font:700 14px var(--h)}
.comp a{display:inline-block;margin-top:6px;font-size:11.5px;font-weight:700;color:#7dd3fc;text-decoration:none}

/* STATISTIK */
.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:-76px 22px 26px;position:relative;z-index:3}
.stat{display:flex;align-items:center;gap:14px;padding:18px;background:#ffffffee;backdrop-filter:blur(10px);border:1px solid #fff;border-radius:22px;box-shadow:0 18px 40px #0b2a6b24;min-width:0}
.stat>i{flex:none;display:grid;place-items:center;width:48px;height:48px;border-radius:16px;font-size:18px}
.stat small{display:block;font-size:11.5px;color:var(--mut);font-weight:600}
.stat b{display:block;margin-top:5px;font:700 15px var(--h);color:var(--navy);overflow-wrap:anywhere;line-height:1.3}
.stat b.num{font:800 26px var(--h);line-height:1}
.i-blue{background:#e8f1ff;color:#2457f5}.i-green{background:#e6f7ee;color:#16a34a}.i-orange{background:#fff1e3;color:#e78a32}.i-purple{background:#f0eaff;color:#7c4dff}

/* LAYOUT */
.layout{display:grid;grid-template-columns:230px minmax(0,1fr);gap:22px;align-items:start}
.snav{position:sticky;top:20px;padding:12px;background:#fff;border:1px solid var(--line);border-radius:22px;box-shadow:0 10px 30px #2457f50d}
.snav a{display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:14px;color:#5d7aa5;text-decoration:none;font-weight:600;font-size:12.5px;transition:.2s}
.snav a i{width:18px;text-align:center;font-size:14px}
.snav a:hover{background:#f1f6ff}
.snav a.on{background:var(--navy);color:#fff}

.panel{padding:26px;background:#fff;border:1px solid var(--line);border-radius:28px;box-shadow:0 10px 30px #2457f50d;margin-bottom:20px;scroll-margin-top:20px}
.ph{display:flex;align-items:center;gap:14px;margin-bottom:22px;padding-bottom:18px;border-bottom:1px solid #edf2fa}
.ph>i{flex:none;display:grid;place-items:center;width:44px;height:44px;border-radius:15px;background:#e8f1ff;color:var(--blue);font-size:17px}
.ph h3{font:700 17px var(--h);color:var(--navy);letter-spacing:-.3px}
.ph p{margin-top:5px;font-size:12px;color:var(--mut)}

/* FORM */
.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
.field.full{grid-column:1/-1}
.field label{display:block;margin-bottom:8px;font-size:12px;font-weight:700;color:#4c6896}
.field label em{color:#e5484d;font-style:normal}
.field input,.field select,.field textarea{width:100%;padding:13px 15px;border:1px solid #dce8f8;border-radius:13px;background:#fbfdff;color:var(--ink);font:500 13px 'Plus Jakarta Sans',sans-serif;outline:none;transition:.2s}
.field input:hover,.field select:hover,.field textarea:hover{border-color:#bcd0f5}
.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--blue);background:#fff;box-shadow:0 0 0 4px #2457f51a}
.field textarea{min-height:92px;resize:vertical}
.field input[readonly]{background:#f1f5fb;color:#7f93b3}
.field .err{display:block;margin-top:6px;font-size:11.5px;color:#d6424d}
.field input.bad{border-color:#e5484d}
.actions{display:flex;justify-content:flex-end;gap:10px;margin-top:22px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:13px 20px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;text-decoration:none;border:0;border-radius:13px;font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer;transition:.2s;box-shadow:0 10px 22px #2457f540;white-space:nowrap}
.btn:hover{filter:brightness(1.1);transform:translateY(-1px)}
.btn-light{background:#e8f1ff;color:#1d4ed8;box-shadow:none}
.btn-danger{background:#fff0f1;color:#d6424d;box-shadow:none}
.btn-danger:hover{background:#d6424d;color:#fff;filter:none}
.btn-sm{padding:9px 13px;font-size:11px;border-radius:11px;box-shadow:none}

.photo-row{display:flex;align-items:center;gap:18px;padding:16px;border:1px dashed #bcd0f5;border-radius:18px;background:#f7faff}
.photo-prev{flex:none;display:grid;place-items:center;width:64px;height:64px;border-radius:20px;background:linear-gradient(135deg,#38bdf8,#2457f5);color:#fff;font:800 20px var(--h);overflow:hidden}
.photo-prev img{width:100%;height:100%;object-fit:cover}
.photo-row .t{flex:1;min-width:0}
.photo-row b{display:block;font-size:12.5px;color:var(--navy)}
.photo-row small{display:block;margin-top:4px;font-size:11px;color:var(--mut)}
.photo-row input[type=file]{margin-top:9px;padding:9px;font-size:12px;background:#fff}
.chk{display:flex;align-items:center;gap:8px;margin-top:10px;font-size:12px;color:#c0343f;font-weight:600;cursor:pointer}
.chk input{width:auto}

/* ALAMAT */
.addr-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:20px}
.addr{padding:20px;border:1px solid var(--line);border-radius:22px;background:#fbfdff;transition:.2s}
.addr:hover{border-color:#bcd0f5;box-shadow:0 12px 28px #2457f51a}
.addr.primary{border:2px solid var(--blue);background:linear-gradient(160deg,#f1f6ff,#fff)}
.addr-top{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:14px}
.addr-label{display:flex;align-items:center;gap:10px;font:700 14px var(--h);color:var(--navy)}
.addr-label i{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;background:#e8f1ff;color:var(--blue);font-size:13px}
.pbadge{padding:5px 11px;border-radius:20px;background:var(--blue);color:#fff;font-size:10.5px;font-weight:700}
.addr p{font-size:12.5px;line-height:1.85;color:#506a98;margin-bottom:14px;overflow-wrap:anywhere}
.addr p strong{color:var(--navy)}
.addr-act{display:flex;flex-wrap:wrap;gap:8px}
.addr-act form{display:inline}
.addr-edit{display:none;margin-top:18px;padding-top:18px;border-top:1px solid #dde8f8}
.addr-edit.open{display:block}
.new-addr{display:none;padding:22px;border:1px dashed #bcd0f5;border-radius:22px;background:#f7faff;margin-top:6px}
.new-addr.open{display:block}
.new-addr h4{margin-bottom:16px;font:700 14px var(--h);color:var(--navy)}
.empty{padding:34px 14px;text-align:center;color:var(--mut)}
.empty>i{display:grid;place-items:center;width:66px;height:66px;margin:0 auto 12px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:26px}
.empty strong{display:block;font:700 15px var(--h);color:var(--navy);margin-bottom:5px}

/* KEAMANAN */
.pw{position:relative}
.pw input{padding-right:46px}
.pw button{position:absolute;right:6px;top:50%;transform:translateY(-50%);width:36px;height:36px;border:0;border-radius:10px;background:transparent;color:#8ca6c4;cursor:pointer}
.pw button:hover{background:#eef3fb;color:var(--blue)}
.meter{display:flex;gap:5px;margin-top:10px}
.meter span{flex:1;height:5px;border-radius:5px;background:#e3ebf8;transition:.3s}
.meter.s1 span:nth-child(1){background:#e5484d}
.meter.s2 span:nth-child(-n+2){background:#f59e0b}
.meter.s3 span:nth-child(-n+3){background:#38bdf8}
.meter.s4 span{background:#16a34a}
.hint{display:block;margin-top:7px;font-size:11.5px;color:var(--mut)}
.hint.ok{color:#16a34a}.hint.no{color:#d6424d}
.secinfo{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-bottom:24px}
.secbox{display:flex;align-items:center;gap:14px;padding:16px;border-radius:18px;background:#f4f8ff;border:1px solid #e3ecfb}
.secbox>i{flex:none;display:grid;place-items:center;width:42px;height:42px;border-radius:14px;background:#fff;color:var(--blue);font-size:16px}
.secbox small{display:block;font-size:11px;color:#8ca6c4}
.secbox b{display:block;margin-top:4px;font-size:13px;color:var(--navy);overflow-wrap:anywhere}

/* PREFERENSI */
.prefs{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
.pref{display:flex;align-items:center;gap:14px;padding:16px;border:1px solid var(--line);border-radius:20px;cursor:pointer;transition:.2s}
.pref:hover{border-color:#bcd0f5;background:#f9fbff}
.pref>i{flex:none;display:grid;place-items:center;width:44px;height:44px;border-radius:15px;font-size:17px}
.pref .t{flex:1;min-width:0}
.pref b{display:block;font-size:13px;color:var(--navy)}
.pref small{display:block;margin-top:4px;font-size:11.5px;color:var(--mut);line-height:1.5}
.sw{position:relative;flex:none;width:46px;height:26px}
.sw input{position:absolute;opacity:0;inset:0;cursor:pointer;z-index:2;width:100%;height:100%}
.sw span{position:absolute;inset:0;border-radius:30px;background:#cdd9ee;transition:.25s}
.sw span::before{content:"";position:absolute;width:20px;height:20px;left:3px;top:3px;border-radius:50%;background:#fff;box-shadow:0 2px 6px #0003;transition:.25s}
.sw input:checked+span{background:var(--blue)}
.sw input:checked+span::before{transform:translateX(20px)}
.sw input:focus-visible+span{outline:2px solid var(--blue);outline-offset:2px}

/* AKTIVITAS */
.tl{position:relative}
.tl-i{position:relative;display:flex;gap:16px;padding-bottom:22px}
.tl-i:last-child{padding-bottom:0}
.tl-i:not(:last-child)::before{content:"";position:absolute;left:21px;top:46px;bottom:0;width:2px;background:#dbe6f8}
.tl-i>i{flex:none;display:grid;place-items:center;width:44px;height:44px;border-radius:15px;font-size:16px;z-index:1}
.tl-i b{display:block;margin-top:4px;font-size:13.5px;color:var(--navy)}
.tl-i p{margin-top:5px;font-size:12.5px;color:#506a98}
.tl-i p.na{color:#a9b6cc}
.note{margin-top:20px;padding:14px 16px;border-radius:16px;background:#f4f8ff;color:#6b83ad;font-size:11.5px;line-height:1.7}
.footer{text-align:center;color:#8ca6c4;font-size:11px;margin-top:30px}

/* RESPONSIVE */
@media(max-width:1350px){.comp{display:none}.stats{grid-template-columns:repeat(2,minmax(0,1fr));margin-top:-60px}}
@media(max-width:1100px){
 .content{padding:20px}
 .layout{grid-template-columns:1fr}
 .snav{position:sticky;top:0;z-index:20;display:flex;gap:6px;overflow-x:auto;padding:8px;border-radius:18px}
 .snav a{flex:none;padding:10px 14px}
}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}
 .hero{padding:28px 20px 100px;flex-direction:column}.who-box{flex-direction:column;align-items:flex-start;gap:18px}.who-box h2{font-size:22px}.ava{width:92px;height:92px;font-size:30px;border-radius:28px}
 .stats{margin:-70px 6px 20px;gap:10px}.stat{padding:14px}
 .panel{padding:18px}.grid,.addr-grid,.secinfo,.prefs{grid-template-columns:1fr}
 .photo-row{flex-direction:column;align-items:flex-start}
}
@media(max-width:480px){.stats{grid-template-columns:1fr}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}html{scroll-behavior:auto}}
</style>
</head>

<body>

@php
    $initials = collect(explode(' ', trim($user->name ?? 'Customer')))
        ->filter()->take(2)->map(fn ($p) => strtoupper(substr($p, 0, 1)))->implode('');
    $initials = $initials ?: 'C';

    $photoUrl = $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null;
    $joined = $user->created_at ? $user->created_at->translatedFormat('F Y') : '-';
    $unread = $user->unreadNotifications()->count();

    $circ = 175.9; // keliling lingkaran r=28
    $dash = round($completion / 100 * $circ, 1);

    $fmt = fn ($d) => $d ? $d->format('d F Y, H:i') : null;

    $prefs = [
        ['notify_shipment_updates', 'Pembaruan pengiriman', 'Kabar saat status pengiriman berubah.', 'fa-box', '#e8f1ff', '#2457f5'],
        ['notify_tracking_updates', 'Pembaruan lokasi', 'Kabar saat posisi barang berpindah.', 'fa-location-dot', '#e6f7ee', '#16a34a'],
        ['notify_delay_alerts', 'Peringatan keterlambatan', 'Kabar jika pengiriman melewati estimasi.', 'fa-clock', '#ffe9ea', '#e5484d'],
        ['notify_exception_alerts', 'Peringatan kendala', 'Kabar jika ada kendala atau pengecualian.', 'fa-triangle-exclamation', '#fff1e3', '#e78a32'],
        ['notify_email', 'Notifikasi email', 'Kirim juga pemberitahuan ke email Anda.', 'fa-envelope', '#f0eaff', '#7c4dff'],
    ];

    $activity = [
        ['Akun dibuat', $fmt($user->created_at), 'fa-user-plus', '#e8f1ff', '#2457f5', 'Informasi tidak tersedia'],
        ['Profil terakhir diubah', $fmt($user->profile_updated_at), 'fa-pen', '#e6f7ee', '#16a34a', 'Belum ada perubahan profil tercatat'],
        ['Password terakhir diubah', $fmt($user->password_changed_at), 'fa-key', '#fff1e3', '#e78a32', 'Belum ada perubahan password tercatat'],
        ['Login terakhir', $fmt($user->last_login_at), 'fa-right-to-bracket', '#f0eaff', '#7c4dff', 'Informasi login belum tersedia'],
    ];
@endphp

<aside class="sidebar">
    <div class="brand">
        <div class="brand-logo"><i class="fa-solid fa-route"></i></div>
        <div class="brand-name">CargoTrace</div>
    </div>
    <div class="brand-sub">Logistics &amp; Shipment Tracking</div>

    <div class="s-wrap">
        <div class="s-title">Menu Customer</div>
        <nav class="s-menu">
            <a href="{{ route('customer.dashboard') }}" class="s-link"><i class="fa-solid fa-border-all"></i><span>Dashboard</span></a>
            <a href="{{ route('customer.pengiriman.index') }}" class="s-link"><i class="fa-solid fa-box"></i><span>Pengiriman Saya</span></a>
            <a href="{{ route('customer.pelacakan.index') }}" class="s-link"><i class="fa-solid fa-location-dot"></i><span>Pelacakan Pengiriman</span></a>
            <a href="{{ route('customer.notifikasi.index') }}" class="s-link">
                <i class="fa-solid fa-bell"></i><span>Notifikasi</span>
                @if($unread > 0)<em class="bdg">{{ $unread }}</em>@endif
            </a>
            <a href="{{ route('customer.profil.index') }}" class="s-link active"><i class="fa-solid fa-user"></i><span>Profil Saya</span></a>
        </nav>
    </div>

    <div class="s-user">
        <div class="who">
            <div class="s-avatar">@if($photoUrl)<img src="{{ $photoUrl }}" alt="">@else{{ $initials }}@endif</div>
            <div><b>{{ $user->name }}</b><small>Customer</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </form>
    </div>
</aside>

<main class="main">
<section class="content">

    <div class="topline">
        <h1>Profil Saya</h1>
        <p>Kelola informasi pribadi, alamat, dan keamanan akun Anda.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-ok"><i class="fa-solid fa-circle-check"></i><div>{{ session('success') }}</div></div>
    @endif

    @if($errors->any())
        <div class="alert alert-err">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Periksa kembali data yang dimasukkan:</strong>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        </div>
    @endif

    {{-- HERO PROFIL --}}
    <div class="hero">
        <div class="who-box">
            <div class="ava-wrap">
                <div class="ava" id="heroAva">
                    @if($photoUrl)<img src="{{ $photoUrl }}" alt="Foto profil {{ $user->name }}">@else{{ $initials }}@endif
                </div>
                <button type="button" class="cam" id="camBtn" aria-label="Ganti foto profil"><i class="fa-solid fa-camera"></i></button>
            </div>

            <div>
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>
                <div class="badges">
                    <span class="badge ok"><i class="fa-solid fa-circle-check"></i> Akun aktif</span>
                    <span class="badge"><i class="fa-regular fa-calendar"></i> Bergabung {{ $joined }}</span>
                    @if($user->city)<span class="badge"><i class="fa-solid fa-location-dot"></i> {{ $user->city }}</span>@endif
                </div>
            </div>
        </div>

        <div class="comp">
            <div class="ring">
                <svg viewBox="0 0 66 66"><circle class="rb" cx="33" cy="33" r="28"/><circle class="rf" id="ringFill" cx="33" cy="33" r="28" stroke-dasharray="0 {{ $circ }}" data-dash="{{ $dash }}"/></svg>
                <b>{{ $completion }}%</b>
            </div>
            <div>
                <strong>{{ $completion >= 100 ? 'Profil lengkap' : 'Kelengkapan profil' }}</strong>
                <small>
                    @if($completion >= 100)
                        Semua data sudah terisi.
                    @else
                        Belum diisi: {{ collect($missing)->take(3)->implode(', ') }}{{ count($missing) > 3 ? ' dan ' . (count($missing) - 3) . ' lainnya' : '' }}.
                    @endif
                </small>
                @if($completion < 100)<a href="#personal">Lengkapi sekarang</a>@endif
            </div>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="stats">
        <div class="stat"><i class="i-blue fa-solid fa-boxes-stacked"></i><div><small>Total pengiriman</small><b class="num">{{ $shipmentCount }}</b></div></div>
        <div class="stat"><i class="i-green fa-solid fa-circle-check"></i><div><small>Pengiriman terkirim</small><b class="num">{{ $deliveredCount }}</b></div></div>
        <div class="stat"><i class="i-orange fa-solid fa-map-location-dot"></i><div><small>Alamat tersimpan</small><b class="num">{{ $addresses->count() }}</b></div></div>
        <div class="stat"><i class="i-purple fa-solid fa-phone"></i><div><small>Nomor telepon</small><b>{{ $user->phone ?: 'Belum diisi' }}</b></div></div>
    </div>

    <div class="layout">

        <nav class="snav" id="snav" aria-label="Bagian profil">
            <a href="#personal" class="on"><i class="fa-solid fa-user-pen"></i> Informasi pribadi</a>
            <a href="#alamat"><i class="fa-solid fa-map-location-dot"></i> Alamat</a>
            <a href="#keamanan"><i class="fa-solid fa-shield-halved"></i> Keamanan</a>
            <a href="#preferensi"><i class="fa-solid fa-bell"></i> Notifikasi</a>
            <a href="#aktivitas"><i class="fa-solid fa-clock-rotate-left"></i> Aktivitas</a>
        </nav>

        <div>

            {{-- INFORMASI PRIBADI --}}
            <section class="panel" id="personal">
                <div class="ph">
                    <i class="fa-solid fa-user-pen"></i>
                    <div><h3>Informasi pribadi dan kontak</h3><p>Perbarui identitas dan cara kami menghubungi Anda.</p></div>
                </div>

                <form action="{{ route('customer.profil.update') }}" method="POST" enctype="multipart/form-data" id="personalForm">
                    @csrf
                    @method('PUT')

                    <div class="grid">
                        <div class="field full">
                            <div class="photo-row">
                                <div class="photo-prev" id="photoPrev">@if($photoUrl)<img src="{{ $photoUrl }}" alt="">@else{{ $initials }}@endif</div>
                                <div class="t">
                                    <b>Foto profil</b>
                                    <small>JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
                                    <input type="file" name="profile_photo" id="photoInput" accept=".jpg,.jpeg,.png,.webp" aria-label="Pilih foto profil">
                                    @error('profile_photo')<span class="err">{{ $message }}</span>@enderror
                                    @if($photoUrl)
                                        <label class="chk"><input type="checkbox" name="remove_photo" value="1"> Hapus foto saat ini</label>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <label for="p-name">Nama lengkap <em>*</em></label>
                            <input id="p-name" type="text" name="name" value="{{ old('name', $user->name) }}" class="{{ $errors->has('name') ? 'bad' : '' }}" required>
                            @error('name')<span class="err">{{ $message }}</span>@enderror
                        </div>

                        <div class="field">
                            <label for="p-email">Alamat email <em>*</em></label>
                            <input id="p-email" type="email" name="email" value="{{ old('email', $user->email) }}" class="{{ $errors->has('email') ? 'bad' : '' }}" required>
                            @error('email')<span class="err">{{ $message }}</span>@enderror
                        </div>

                        <div class="field">
                            <label for="p-phone">Nomor telepon</label>
                            <input id="p-phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0812xxxxxxxx">
                        </div>

                        <div class="field">
                            <label for="p-wa">Nomor WhatsApp</label>
                            <input id="p-wa" type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $user->whatsapp_number) }}" placeholder="Nomor WhatsApp">
                        </div>

                        <div class="field">
                            <label for="p-birth">Tanggal lahir</label>
                            <input id="p-birth" type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}" class="{{ $errors->has('birth_date') ? 'bad' : '' }}">
                            @error('birth_date')<span class="err">{{ $message }}</span>@enderror
                        </div>

                        <div class="field">
                            <label for="p-gender">Jenis kelamin</label>
                            <select id="p-gender" name="gender">
                                <option value="">Pilih jenis kelamin</option>
                                @foreach(['Laki-laki', 'Perempuan', 'Lainnya'] as $g)
                                    <option value="{{ $g }}" @selected(old('gender', $user->gender) === $g)>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field full">
                            <label for="p-address">Alamat lengkap</label>
                            <textarea id="p-address" name="address" placeholder="Masukkan alamat lengkap">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="field">
                            <label for="p-city">Kota</label>
                            <input id="p-city" type="text" name="city" value="{{ old('city', $user->city) }}">
                        </div>

                        <div class="field">
                            <label for="p-prov">Provinsi</label>
                            <input id="p-prov" type="text" name="province" value="{{ old('province', $user->province) }}">
                        </div>

                        <div class="field">
                            <label for="p-post">Kode pos</label>
                            <input id="p-post" type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
                        </div>

                        <div class="field">
                            <label for="p-country">Negara</label>
                            <input id="p-country" type="text" name="country" value="{{ old('country', $user->country ?: 'Indonesia') }}">
                        </div>
                    </div>

                    <div class="actions">
                        <button type="reset" class="btn btn-light" id="resetPersonal"><i class="fa-solid fa-rotate-left"></i> Batal</button>
                        <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan perubahan</button>
                    </div>
                </form>
            </section>


            {{-- ALAMAT --}}
            <section class="panel" id="alamat">
                <div class="ph">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <div><h3>Alamat tersimpan</h3><p>Kelola alamat utama dan alamat alternatif untuk pengiriman.</p></div>
                </div>

                @if($addresses->count())
                    <div class="addr-grid">
                        @foreach($addresses as $address)
                            <div class="addr {{ $address->is_primary ? 'primary' : '' }}">
                                <div class="addr-top">
                                    <div class="addr-label">
                                        <i class="fa-solid {{ $address->is_primary ? 'fa-house' : 'fa-location-dot' }}"></i>
                                        {{ $address->label }}
                                    </div>
                                    @if($address->is_primary)<span class="pbadge">Alamat utama</span>@endif
                                </div>

                                <p>
                                    <strong>{{ $address->recipient_name }}</strong><br>
                                    {{ $address->phone }}<br>
                                    {{ $address->address }}<br>
                                    {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}<br>
                                    {{ $address->country }}
                                </p>

                                <div class="addr-act">
                                    <button type="button" class="btn btn-light btn-sm" data-toggle="addr-edit-{{ $address->id }}"><i class="fa-solid fa-pen"></i> Ubah</button>

                                    @unless($address->is_primary)
                                        <form method="POST" action="{{ route('customer.profil.alamat.utama', $address->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm"><i class="fa-solid fa-star"></i> Jadikan utama</button>
                                        </form>
                                    @endunless

                                    <form method="POST" action="{{ route('customer.profil.alamat.destroy', $address->id) }}" onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Hapus</button>
                                    </form>
                                </div>

                                <div class="addr-edit" id="addr-edit-{{ $address->id }}">
                                    <form method="POST" action="{{ route('customer.profil.alamat.update', $address->id) }}">
                                        @csrf @method('PUT')
                                        <div class="grid">
                                            <div class="field"><label>Label alamat</label><input name="label" value="{{ $address->label }}" required></div>
                                            <div class="field"><label>Nama penerima</label><input name="recipient_name" value="{{ $address->recipient_name }}" required></div>
                                            <div class="field"><label>Nomor telepon</label><input name="phone" value="{{ $address->phone }}" required></div>
                                            <div class="field"><label>Kota</label><input name="city" value="{{ $address->city }}" required></div>
                                            <div class="field full"><label>Alamat lengkap</label><textarea name="address" required>{{ $address->address }}</textarea></div>
                                            <div class="field"><label>Provinsi</label><input name="province" value="{{ $address->province }}" required></div>
                                            <div class="field"><label>Kode pos</label><input name="postal_code" value="{{ $address->postal_code }}"></div>
                                            <div class="field full"><label>Negara</label><input name="country" value="{{ $address->country }}" required></div>
                                        </div>
                                        <div class="actions"><button type="submit" class="btn btn-sm"><i class="fa-solid fa-floppy-disk"></i> Simpan perubahan</button></div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-light" data-toggle="newAddr"><i class="fa-solid fa-plus"></i> Tambah alamat baru</button>
                @else
                    <div class="empty">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <strong>Belum ada alamat tersimpan</strong>
                        Tambahkan alamat pertama Anda. Alamat pertama otomatis menjadi alamat utama.
                    </div>
                @endif

                <div class="new-addr {{ $addresses->count() ? '' : 'open' }}" id="newAddr">
                    <h4><i class="fa-solid fa-plus"></i> Alamat baru</h4>

                    <form method="POST" action="{{ route('customer.profil.alamat.store') }}">
                        @csrf
                        <div class="grid">
                            <div class="field"><label>Label alamat <em>*</em></label><input name="label" placeholder="Contoh: Rumah, Kantor" required></div>
                            <div class="field"><label>Nama penerima <em>*</em></label><input name="recipient_name" value="{{ $user->name }}" required></div>
                            <div class="field"><label>Nomor telepon penerima <em>*</em></label><input name="phone" value="{{ $user->phone }}" required></div>
                            <div class="field"><label>Kota <em>*</em></label><input name="city" required></div>
                            <div class="field full"><label>Alamat lengkap <em>*</em></label><textarea name="address" required></textarea></div>
                            <div class="field"><label>Provinsi <em>*</em></label><input name="province" required></div>
                            <div class="field"><label>Kode pos</label><input name="postal_code"></div>
                            <div class="field full"><label>Negara <em>*</em></label><input name="country" value="Indonesia" required></div>
                        </div>
                        <div class="actions"><button type="submit" class="btn"><i class="fa-solid fa-plus"></i> Simpan alamat</button></div>
                    </form>
                </div>
            </section>


            {{-- KEAMANAN --}}
            <section class="panel" id="keamanan">
                <div class="ph">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div><h3>Keamanan akun</h3><p>Gunakan password yang kuat dan jangan dibagikan ke siapa pun.</p></div>
                </div>

                <div class="secinfo">
                    <div class="secbox"><i class="fa-solid fa-envelope"></i><div><small>Email akun</small><b>{{ $user->email }}</b></div></div>
                    <div class="secbox"><i class="fa-solid fa-circle-check" style="color:#16a34a"></i><div><small>Status akun</small><b>Aktif</b></div></div>
                </div>

                <form method="POST" action="{{ route('customer.profil.password') }}" id="pwForm">
                    @csrf
                    @method('PUT')

                    <div class="grid">
                        <div class="field full">
                            <label for="current_password">Password saat ini <em>*</em></label>
                            <div class="pw">
                                <input type="password" name="current_password" id="current_password" class="{{ $errors->has('current_password') ? 'bad' : '' }}" autocomplete="current-password" required>
                                <button type="button" data-eye="current_password" aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            @error('current_password')<span class="err">{{ $message }}</span>@enderror
                        </div>

                        <div class="field">
                            <label for="new_password">Password baru <em>*</em></label>
                            <div class="pw">
                                <input type="password" name="password" id="new_password" minlength="8" class="{{ $errors->has('password') ? 'bad' : '' }}" autocomplete="new-password" required>
                                <button type="button" data-eye="new_password" aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            <div class="meter" id="meter"><span></span><span></span><span></span><span></span></div>
                            <span class="hint" id="meterHint">Minimal 8 karakter. Campurkan huruf, angka, dan simbol.</span>
                            @error('password')<span class="err">{{ $message }}</span>@enderror
                        </div>

                        <div class="field">
                            <label for="confirm_password">Konfirmasi password baru <em>*</em></label>
                            <div class="pw">
                                <input type="password" name="password_confirmation" id="confirm_password" minlength="8" autocomplete="new-password" required>
                                <button type="button" data-eye="confirm_password" aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            <span class="hint" id="matchHint"></span>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="reset" class="btn btn-light" id="resetPw"><i class="fa-solid fa-rotate-left"></i> Batal</button>
                        <button type="submit" class="btn"><i class="fa-solid fa-lock"></i> Ubah password</button>
                    </div>
                </form>
            </section>


            {{-- PREFERENSI --}}
            <section class="panel" id="preferensi">
                <div class="ph">
                    <i class="fa-solid fa-bell"></i>
                    <div><h3>Preferensi notifikasi</h3><p>Pilih kabar yang ingin Anda terima.</p></div>
                </div>

                <form method="POST" action="{{ route('customer.profil.preferences') }}">
                    @csrf
                    @method('PUT')

                    <div class="prefs">
                        @foreach($prefs as [$key, $title, $desc, $icon, $bg, $fg])
                            <label class="pref">
                                <i class="fa-solid {{ $icon }}" style="background:{{ $bg }};color:{{ $fg }}"></i>
                                <div class="t"><b>{{ $title }}</b><small>{{ $desc }}</small></div>
                                <span class="sw">
                                    <input type="checkbox" name="{{ $key }}" value="1" @checked($user->{$key})>
                                    <span></span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn"><i class="fa-solid fa-floppy-disk"></i> Simpan preferensi</button>
                    </div>
                </form>
            </section>


            {{-- AKTIVITAS --}}
            <section class="panel" id="aktivitas">
                <div class="ph">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <div><h3>Aktivitas akun</h3><p>Catatan penting tentang akun Anda.</p></div>
                </div>

                <div class="tl">
                    @foreach($activity as [$title, $time, $icon, $bg, $fg, $empty])
                        <div class="tl-i">
                            <i class="fa-solid {{ $icon }}" style="background:{{ $bg }};color:{{ $fg }}"></i>
                            <div>
                                <b>{{ $title }}</b>
                                <p class="{{ $time ? '' : 'na' }}">{{ $time ?? $empty }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="note"><i class="fa-solid fa-circle-info"></i> Riwayat login dan perubahan hanya tampil jika waktunya sudah tercatat pada akun Anda.</p>
            </section>

        </div>
    </div>

    <footer class="footer">
        © {{ date('Y') }} CargoTrace. Customer Portal.
    </footer>

</section>
</main>

<script>
(function () {
    // Cincin kelengkapan profil
    var ring = document.getElementById('ringFill');
    if (ring) {
        setTimeout(function () {
            ring.setAttribute('stroke-dasharray', ring.dataset.dash + ' {{ $circ }}');
        }, 200);
    }

    // Buka/tutup form alamat
    document.querySelectorAll('[data-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var box = document.getElementById(btn.dataset.toggle);
            if (!box) return;
            box.classList.toggle('open');
            if (box.classList.contains('open') && btn.dataset.toggle === 'newAddr') {
                box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });

    // Tampilkan/sembunyikan password
    document.querySelectorAll('[data-eye]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.eye);
            var icon = btn.querySelector('i');
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        });
    });

    // Kekuatan password dan kecocokan konfirmasi
    var np = document.getElementById('new_password');
    var cp = document.getElementById('confirm_password');
    var meter = document.getElementById('meter');
    var mh = document.getElementById('meterHint');
    var xh = document.getElementById('matchHint');

    function strength(v) {
        var s = 0;
        if (v.length >= 8) s++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v) || v.length >= 14) s++;
        return v.length ? Math.max(s, 1) : 0;
    }

    function updatePw() {
        var s = strength(np.value);
        meter.className = 'meter' + (s ? ' s' + s : '');
        mh.textContent = s === 0 ? 'Minimal 8 karakter. Campurkan huruf, angka, dan simbol.'
            : ['', 'Terlalu lemah', 'Cukup', 'Kuat', 'Sangat kuat'][s];

        if (!cp.value) { xh.textContent = ''; xh.className = 'hint'; return; }
        var ok = cp.value === np.value;
        xh.textContent = ok ? 'Password cocok' : 'Konfirmasi belum cocok';
        xh.className = 'hint ' + (ok ? 'ok' : 'no');
    }

    np.addEventListener('input', updatePw);
    cp.addEventListener('input', updatePw);
    document.getElementById('resetPw').addEventListener('click', function () { setTimeout(updatePw, 0); });

    // Pratinjau foto profil
    var input = document.getElementById('photoInput');
    var prev = document.getElementById('photoPrev');
    var hero = document.getElementById('heroAva');
    var original = { prev: prev.innerHTML, hero: hero.innerHTML };

    input.addEventListener('change', function () {
        var f = input.files && input.files[0];
        if (!f) return;
        var url = URL.createObjectURL(f);
        prev.innerHTML = '<img src="' + url + '" alt="">';
        hero.innerHTML = '<img src="' + url + '" alt="">';
    });

    document.getElementById('resetPersonal').addEventListener('click', function () {
        prev.innerHTML = original.prev;
        hero.innerHTML = original.hero;
    });

    document.getElementById('camBtn').addEventListener('click', function () {
        document.getElementById('personal').scrollIntoView({ behavior: 'smooth' });
        setTimeout(function () { input.focus(); input.click(); }, 450);
    });

    // Penanda bagian yang sedang dibaca
    var links = document.querySelectorAll('#snav a');
    var sections = Array.prototype.map.call(links, function (a) {
        return document.querySelector(a.getAttribute('href'));
    });

    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (!e.isIntersecting) return;
                links.forEach(function (a) {
                    a.classList.toggle('on', a.getAttribute('href') === '#' + e.target.id);
                });
            });
        }, { rootMargin: '-25% 0px -65% 0px' });

        sections.forEach(function (s) { if (s) io.observe(s); });
    }
})();
</script>

</body>
</html>
