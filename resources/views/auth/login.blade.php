<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk atau Daftar | CargoTrace</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--navy:#040d24;--blue:#2563eb;--blue2:#1d4ed8;--sky:#38bdf8;--cyan:#22d3ee;--txt:#e8f1ff;--mut:#9fbbe8;--h:'Sora',sans-serif}
body{min-height:100vh;display:grid;place-items:center;padding:24px;font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;color:var(--txt);overflow-x:hidden;
 background:radial-gradient(circle at 12% 18%,#1d4ed866,#0000 42%),radial-gradient(circle at 88% 82%,#0ea5e955,#0000 42%),radial-gradient(circle at 50% 50%,#0a246e 0,#040d24 78%)}
:focus-visible{outline:2px solid var(--sky);outline-offset:2px}
button{font-family:inherit}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes drift{to{transform:translate(90px,60px) scale(1.25)}}

/* ===== LATAR PALING BELAKANG (diam, tidak polos) ===== */
.bg{position:fixed;inset:0;z-index:0;pointer-events:none;
 background:radial-gradient(#7fb2ff30 1.2px,#0000 1.8px) 0 0/30px 30px,
 repeating-linear-gradient(135deg,#ffffff06 0 1px,#0000 1px 22px)}
.bg::after{content:"";position:absolute;inset:0;background:radial-gradient(ellipse at center,#0000 40%,#02071a 105%)}
.bg svg{position:absolute;inset:0;width:100%;height:100%;opacity:.5}

/* ===== BINGKAI BERPUTAR ===== */
.frame{position:relative;z-index:2;width:100%;max-width:1120px;padding:2px;border-radius:34px;overflow:hidden;box-shadow:0 50px 120px #000b,0 0 80px #2563eb44;animation:rise .9s cubic-bezier(.2,.8,.2,1) both}
.frame::before{content:"";position:absolute;inset:-60%;background:conic-gradient(from 0deg,#0000 0 55%,var(--sky),var(--blue),#0000 90%);animation:spin 6s linear infinite}
@keyframes rise{from{opacity:0;transform:translateY(40px) scale(.97)}}
.shell{position:relative;min-height:720px;display:grid;grid-template-columns:1.1fr 1fr;border-radius:32px;overflow:hidden;background:#061540}
.shell::after{content:"";position:absolute;inset:0;z-index:6;pointer-events:none;background:radial-gradient(480px circle at var(--mx,50%) var(--my,50%),#38bdf822,#0000 60%)}

/* ===== efek bersama ===== */
.fxl{position:absolute;inset:0;z-index:0;overflow:hidden;pointer-events:none}
.ab{position:absolute;border-radius:50%;filter:blur(60px);animation:drift 13s ease-in-out infinite alternate}
.bub s{position:absolute;bottom:-24px;left:var(--x);width:var(--s);height:var(--s);border-radius:50%;background:#7dd3fc40;border:1px solid #bae6fd66;animation:up var(--t) linear var(--dl) infinite}
@keyframes up{0%{opacity:0;transform:translate(0,0)}10%,85%{opacity:1}100%{opacity:0;transform:translate(var(--dx),-900px)}}
.wv{position:absolute;left:0;right:0;bottom:0;height:90px;background:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 90'%3E%3Cpath d='M0 45 Q150 0 300 45 T600 45 V90 H0Z' fill='%2338bdf8' fill-opacity='.22'/%3E%3C/svg%3E") 0 0/600px 90px repeat-x;animation:wave 9s linear infinite}
.wv.w2{height:70px;opacity:.7;animation-duration:14s;animation-direction:reverse}
@keyframes wave{to{background-position-x:600px}}

/* ===== KIRI ===== */
.side{position:relative;display:flex;flex-direction:column;padding:38px 42px;color:#fff;overflow:hidden;background:linear-gradient(165deg,#061a4d 0%,#0a2f8f 55%,#1b63ec 130%)}
.side .grid{position:absolute;inset:-40px;background:repeating-linear-gradient(0deg,#ffffff0d 0 1px,#0000 1px 40px),repeating-linear-gradient(90deg,#ffffff0d 0 1px,#0000 1px 40px);animation:grid 6s linear infinite}
@keyframes grid{to{transform:translate(40px,40px)}}
.side>.brand,.side>.stage,.side>.copy{position:relative;z-index:2}
.route path{fill:none;stroke:#7dd3fc;stroke-width:1.6;stroke-dasharray:6 10;opacity:.55;animation:dash 2s linear infinite}
@keyframes dash{to{stroke-dashoffset:-32}}
.brand{display:flex;align-items:center;gap:12px}
.logo{display:grid;place-items:center;width:46px;height:46px;border-radius:14px;background:linear-gradient(135deg,var(--sky),var(--blue));font-size:20px;box-shadow:0 10px 26px #2563eb99}
.bn{font:800 23px var(--h);letter-spacing:-.6px}
.bs{margin-top:3px;font-size:10px;letter-spacing:.9px;color:#a9c8f5}

.stage{flex:1;min-height:400px;display:grid;place-items:center}
.core{position:absolute;display:grid;place-items:center;width:78px;height:78px;border-radius:24px;background:linear-gradient(135deg,var(--sky),var(--blue));font-size:30px;box-shadow:0 0 0 10px #38bdf822,0 0 60px #38bdf8cc;animation:glow 3s ease-in-out infinite}
@keyframes glow{50%{box-shadow:0 0 0 22px #38bdf80f,0 0 90px #38bdf8}}
.ring{--d:200px;--t:24s;--dir:normal;--rdir:reverse;position:absolute;width:var(--d);height:var(--d);border:1.5px dashed #ffffff3d;border-radius:50%;animation:spin var(--t) linear infinite var(--dir)}
.r2{--d:310px;--t:36s;--dir:reverse;--rdir:normal}
.r3{--d:420px;--t:52s}
.it{position:absolute;top:50%;left:50%;width:46px;height:46px;margin:-23px 0 0 -23px;transform:rotate(var(--a)) translateY(calc(var(--d) / -2)) rotate(calc(var(--a) * -1))}
.it i{display:grid;place-items:center;width:100%;height:100%;border-radius:15px;background:#ffffff26;border:1px solid #ffffff55;backdrop-filter:blur(8px);font-size:17px;animation:spin var(--t) linear infinite var(--rdir)}
.it.hot i{background:linear-gradient(135deg,#38bdf8,#2563eb);border-color:#fff8;box-shadow:0 0 24px #38bdf8}
.pulse{position:absolute;width:120px;height:120px;border-radius:50%;border:2px solid #38bdf899;animation:ping 3.6s ease-out infinite}
.pulse.p2{animation-delay:-1.8s}
@keyframes ping{from{transform:scale(.4);opacity:1}to{transform:scale(2.6);opacity:0}}

.fc{position:absolute;display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:14px;background:#ffffff1f;border:1px solid #ffffff3d;backdrop-filter:blur(10px);font-size:11.5px;animation:bob 4s ease-in-out infinite}
.fc b{display:block;font-size:12px}.fc small{color:#bcd3f7;font-size:10.5px}
.fc i{display:grid;place-items:center;width:30px;height:30px;border-radius:9px;background:#4ade8033;color:#4ade80}
.f1{left:0;top:24px}.f2{right:0;bottom:30px;animation-delay:-2s}.f2 i{background:#38bdf833;color:var(--sky)}
.f3{right:6px;top:70px;animation-delay:-1s}.f3 i{background:#fbbf2433;color:#fbbf24}
@keyframes bob{50%{transform:translateY(-9px)}}

.copy h1{font:800 34px/1.2 var(--h);letter-spacing:-1px;max-width:420px}
.copy h1 span{display:inline-block;opacity:0;transform:translateY(24px);animation:word .7s cubic-bezier(.2,.8,.2,1) forwards;animation-delay:calc(.5s + var(--i) * .09s)}
@keyframes word{to{opacity:1;transform:none}}
.copy p{margin-top:12px;max-width:400px;line-height:1.75;color:#cfe0ff;opacity:0;animation:word .8s 1.3s forwards}

/* ===== KANAN (biru gelap, bergerak, FLIP) ===== */
.form{position:relative;display:flex;flex-direction:column;padding:44px 50px 26px;overflow:hidden;perspective:1600px;background:linear-gradient(160deg,#071c5a 0%,#0a2c82 55%,#0b3fb0 130%)}
.stripes{position:absolute;inset:-100px;background:repeating-linear-gradient(60deg,#ffffff0b 0 2px,#0000 2px 36px);animation:sl 5s linear infinite}
@keyframes sl{to{transform:translate(31.2px,-18px)}}
.sweep{position:absolute;right:-230px;top:-230px;width:560px;height:560px;border-radius:50%;background:conic-gradient(from 0deg,#38bdf838,#0000 28%);animation:spin 7s linear infinite}
.pg{position:absolute;right:-70px;top:-70px;width:260px;height:260px;border-radius:50%;border:1.5px solid #38bdf877;animation:ping 4s ease-out infinite}
.pg.b{animation-delay:-1.4s}.pg.c{animation-delay:-2.8s}
.cr{position:absolute;left:-50px;font-size:20px;color:#7dd3fc88;animation:cross var(--t) linear var(--dl) infinite}
.cr::after{content:"";position:absolute;right:30px;top:50%;width:90px;height:1.5px;background:linear-gradient(90deg,#0000,#7dd3fc88)}
@keyframes cross{to{transform:translateX(720px)}}
.flipper,.foot{position:relative;z-index:2}
.flipper{display:grid;flex:1;transform-style:preserve-3d;transition:transform .9s cubic-bezier(.7,0,.2,1)}
.flipper.reg{transform:rotateY(180deg)}
.face{grid-area:1/1;display:flex;flex-direction:column;justify-content:center;backface-visibility:hidden;-webkit-backface-visibility:hidden}
.face.back{transform:rotateY(180deg)}

.hi{display:inline-flex;align-items:center;gap:8px;align-self:flex-start;margin-bottom:14px;padding:6px 12px;border-radius:20px;background:#38bdf824;border:1px solid #38bdf855;color:#bae6fd;font-size:11.5px;font-weight:700}
.face h2{font:800 29px var(--h);color:#fff;letter-spacing:-.8px}
.sub{margin:8px 0 22px;color:var(--mut);line-height:1.6;font-size:13px}
.err{display:flex;gap:10px;margin-bottom:16px;padding:12px 14px;border-radius:12px;background:#ef444426;border:1px solid #f8717166;color:#fecaca;font-size:12.5px;font-weight:600;line-height:1.5;animation:shake .45s}
.err i{margin-top:2px}
@keyframes shake{20%,60%{transform:translateX(-6px)}40%,80%{transform:translateX(6px)}}
.fg{margin-bottom:14px}
.fg label{display:block;margin-bottom:7px;font-size:12px;font-weight:700;color:#b6d0f7}
.in{position:relative}
.in>i.l{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#8fb0e6;font-size:14px;pointer-events:none;transition:.25s}
.in input{width:100%;padding:14px 46px 14px 43px;border:1.5px solid #ffffff2e;border-radius:13px;background:#ffffff14;color:#fff;font:600 14px 'Plus Jakarta Sans',sans-serif;backdrop-filter:blur(6px);transition:.25s}
.in input::placeholder{color:#8fb0e6;font-weight:500}
.in input:-webkit-autofill{-webkit-text-fill-color:#fff;box-shadow:0 0 0 100px #0e2f8c inset;caret-color:#fff}
.in input:focus{outline:none;border-color:var(--sky);background:#ffffff22;box-shadow:0 0 0 4px #38bdf829,0 0 24px #38bdf833}
.in:focus-within>i.l{color:var(--sky);transform:translateY(-50%) scale(1.15)}
.in.bad input{border-color:#f87171;background:#ef444422;animation:shake .4s}
.eye{position:absolute;right:6px;top:50%;transform:translateY(-50%);width:36px;height:36px;border:0;border-radius:10px;background:none;color:#a9c8f5;cursor:pointer}
.eye:hover{background:#ffffff22;color:#fff}
.hint{display:none;margin-top:7px;font-size:11.5px;font-weight:600;color:#fcd34d}.hint.show{display:block}
.meter{display:flex;gap:5px;margin-top:9px}
.meter span{flex:1;height:4px;border-radius:4px;background:#ffffff26;transition:.3s}
.meter[data-s="1"] span:nth-child(-n+1){background:#ef4444}
.meter[data-s="2"] span:nth-child(-n+2){background:#f59e0b}
.meter[data-s="3"] span:nth-child(-n+3){background:#84cc16}
.meter[data-s="4"] span{background:#10b981}
.mt{margin-top:5px;min-height:15px;font-size:11.5px;color:var(--mut)}

.go{position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;gap:10px;width:100%;margin-top:6px;padding:15px;border:0;border-radius:13px;background:linear-gradient(135deg,#38bdf8,#2563eb 60%,#1d4ed8);color:#fff;font-weight:700;font-size:14.5px;cursor:pointer;box-shadow:0 14px 30px #2563eb77;transition:.25s}
.go::after{content:"";position:absolute;top:0;left:-70%;width:45%;height:100%;background:linear-gradient(100deg,#0000,#ffffff66,#0000);transform:skewX(-20deg);animation:shine 3.2s ease-in-out infinite}
@keyframes shine{0%,55%{left:-70%}100%{left:130%}}
.go:hover{transform:translateY(-3px);box-shadow:0 20px 40px #38bdf877}
.go i{transition:transform .3s}.go:hover i{transform:translateX(5px)}
.go:disabled{opacity:.8;cursor:wait;transform:none}
.spn{display:none;width:16px;height:16px;border:3px solid #ffffff55;border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite}
.go.load .spn{display:inline-block}.go.load i{display:none}
.switch{margin-top:18px;text-align:center;color:var(--mut);font-size:13px}
.switch button{border:0;background:none;color:#7dd3fc;font-weight:700;cursor:pointer;font-size:13px}
.switch button:hover{text-decoration:underline}
.perk{display:flex;align-items:center;gap:10px;margin-top:14px;padding:12px 14px;border-radius:13px;background:#ffffff12;border:1px solid #ffffff1f;color:var(--mut);font-size:11.5px;line-height:1.55}
.perk i{color:var(--sky)}
.foot{margin-top:16px;text-align:center;font-size:11px;color:#7f9fd6}

@media(max-width:920px){
 .frame{max-width:560px}
 .shell{grid-template-columns:1fr}
 .side{padding:26px}.stage{min-height:250px;transform:scale(.62);margin:-60px 0}
 .copy h1{font-size:25px}.copy p{display:none}.fc{display:none}
 .form{padding:30px 26px 22px}
}
@media(max-width:420px){body{padding:10px}.face h2{font-size:25px}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}.copy h1 span,.copy p{opacity:1;transform:none}}
</style>
</head>
<body>
@php $startTab = (old('form_type') === 'register' || $errors->register->any()) ? 'register' : 'login'; @endphp

<!-- Latar paling belakang: diam tapi tidak polos -->
<div class="bg" aria-hidden="true">
  <svg viewBox="0 0 1200 800" preserveAspectRatio="xMidYMid slice" fill="none" stroke="#5b9bff" stroke-width="1.2">
    <path d="M-20 640 C 200 420, 380 760, 600 520 S 980 300, 1220 460" stroke-dasharray="3 9"/>
    <path d="M-20 200 C 220 60, 420 320, 640 180 S 1000 60, 1220 220" stroke-dasharray="3 9"/>
    <path d="M300 820 C 380 600, 520 560, 700 640 S 1000 760, 1100 820" stroke-dasharray="3 9"/>
    <g fill="#7dd3fc" stroke="none"><circle cx="140" cy="560" r="4"/><circle cx="600" cy="520" r="4"/><circle cx="1010" cy="330" r="4"/><circle cx="420" cy="190" r="4"/><circle cx="880" cy="170" r="4"/><circle cx="700" cy="640" r="4"/></g>
    <g stroke="#3b82f6"><circle cx="140" cy="560" r="16"/><circle cx="600" cy="520" r="16"/><circle cx="1010" cy="330" r="16"/><circle cx="420" cy="190" r="16"/></g>
  </svg>
</div>

<div class="frame">
<main class="shell" id="shell">

  <!-- KIRI -->
  <section class="side">
    <div class="fxl" aria-hidden="true">
      <i class="ab" style="width:360px;height:360px;background:#38bdf866;left:-120px;top:-100px"></i>
      <i class="ab" style="width:320px;height:320px;background:#4f46e566;right:-100px;bottom:60px;animation-delay:-7s"></i>
      <div class="grid"></div>
      <svg class="route" viewBox="0 0 500 800" preserveAspectRatio="xMidYMid slice" style="position:absolute;inset:0;width:100%;height:100%">
        <path id="p1" d="M-20 700 C 120 520, 240 760, 330 560 S 470 380, 540 430"/>
        <path id="p2" d="M-20 150 C 140 40, 260 260, 380 130 S 500 60, 540 90"/>
        <circle r="5" fill="#fff"><animateMotion dur="8s" repeatCount="indefinite"><mpath href="#p1"/></animateMotion></circle>
        <circle r="5" fill="#7dd3fc"><animateMotion dur="11s" repeatCount="indefinite"><mpath href="#p2"/></animateMotion></circle>
      </svg>
      <div class="bub" id="bubL"></div>
      <div class="wv"></div><div class="wv w2"></div>
    </div>

    <div class="brand">
      <div class="logo"><i class="fa-solid fa-route"></i></div>
      <div><div class="bn">CargoTrace</div><div class="bs">LOGISTICS &amp; SHIPMENT TRACKING</div></div>
    </div>

    <div class="stage" aria-hidden="true">
      <div class="pulse"></div><div class="pulse p2"></div>
      <div class="ring"><div class="it" style="--a:0deg;--d:200px"><i class="fa-solid fa-truck"></i></div><div class="it" style="--a:180deg;--d:200px"><i class="fa-solid fa-box"></i></div></div>
      <div class="ring r2"><div class="it hot" style="--a:50deg;--d:310px"><i class="fa-solid fa-ship"></i></div><div class="it" style="--a:230deg;--d:310px"><i class="fa-solid fa-anchor"></i></div></div>
      <div class="ring r3"><div class="it hot" style="--a:130deg;--d:420px"><i class="fa-solid fa-plane"></i></div><div class="it" style="--a:300deg;--d:420px"><i class="fa-solid fa-warehouse"></i></div><div class="it" style="--a:20deg;--d:420px"><i class="fa-solid fa-location-dot"></i></div></div>
      <div class="core"><i class="fa-solid fa-route"></i></div>
      <div class="fc f1"><i class="fa-solid fa-circle-check"></i><div><b>Terkirim tepat waktu</b><small>Jakarta &rarr; Medan</small></div></div>
      <div class="fc f2"><i class="fa-solid fa-location-crosshairs"></i><div><b>Posisi diperbarui</b><small>Kapal MV Nusantara</small></div></div>
      <div class="fc f3"><i class="fa-solid fa-gauge-high"></i><div><b>Dalam perjalanan</b><small>128 paket aktif</small></div></div>
    </div>

    <div class="copy">
      <h1><span style="--i:0">Kelola</span> <span style="--i:1">pengiriman</span> <span style="--i:2">dengan</span> <span style="--i:3">lebih</span> <span style="--i:4">mudah.</span></h1>
      <p>Pantau perjalanan kargo, status pengiriman, dan keterlambatan dalam satu platform.</p>
    </div>
  </section>

  <!-- KANAN -->
  <section class="form">
    <div class="fxl" aria-hidden="true">
      <i class="ab" style="width:380px;height:380px;background:#2563eb88;left:-140px;bottom:-60px"></i>
      <i class="ab" style="width:300px;height:300px;background:#22d3ee44;right:-80px;top:38%;animation-delay:-5s"></i>
      <i class="ab" style="width:260px;height:260px;background:#6366f155;left:30%;top:-90px;animation-delay:-9s"></i>
      <div class="stripes"></div>
      <div class="sweep"></div>
      <i class="pg"></i><i class="pg b"></i><i class="pg c"></i>
      <i class="cr fa-solid fa-truck-fast" style="top:14%;--t:19s;--dl:-4s"></i>
      <i class="cr fa-solid fa-ship" style="top:46%;--t:27s;--dl:-15s"></i>
      <i class="cr fa-solid fa-plane" style="top:74%;--t:14s;--dl:-2s"></i>
      <div class="bub" id="bubR"></div>
      <div class="wv"></div><div class="wv w2"></div>
    </div>

    <div class="flipper {{ $startTab==='register' ? 'reg' : '' }}" id="flipper">

      <!-- LOGIN -->
      <div class="face" id="faceLogin">
        <span class="hi"><i class="fa-solid fa-lock"></i> Akses aman</span>
        <h2>Selamat datang</h2>
        <p class="sub">Masuk dengan akunmu untuk mengakses CargoTrace.</p>

        @if ($errors->any() && !$errors->register->any())
          <div class="err" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ \Illuminate\Support\Facades\Route::has('login.process') ? route('login.process') : url('/login') }}" method="POST" novalidate data-form>
          @csrf
          <input type="hidden" name="form_type" value="login">
          <div class="fg">
            <label for="l_email">Email</label>
            <div class="in {{ $errors->has('email') ? 'bad' : '' }}">
              <i class="fa-regular fa-envelope l"></i>
              <input type="email" id="l_email" name="email" placeholder="nama@perusahaan.com" value="{{ old('form_type')==='login' ? old('email') : '' }}" autocomplete="email" required>
            </div>
          </div>
          <div class="fg">
            <label for="l_pw">Password</label>
            <div class="in {{ $errors->has('password') ? 'bad' : '' }}">
              <i class="fa-solid fa-lock l"></i>
              <input type="password" id="l_pw" name="password" placeholder="Masukkan password" autocomplete="current-password" required data-caps>
              <button type="button" class="eye" data-eye aria-label="Tampilkan password"><i class="fa-regular fa-eye"></i></button>
            </div>
            <div class="hint" id="caps"><i class="fa-solid fa-triangle-exclamation"></i> Caps Lock sedang aktif.</div>
          </div>
          <button type="submit" class="go"><span class="spn"></span><span class="tx">Masuk</span><i class="fa-solid fa-arrow-right"></i></button>
        </form>

        <p class="switch">Belum punya akun? <button type="button" data-go="register">Daftar sekarang</button></p>
        <div class="perk"><i class="fa-solid fa-shield-halved"></i><span>Lupa password? Hubungi administrator.</span></div>
      </div>

      <!-- DAFTAR -->
      <div class="face back" id="faceRegister">
        <span class="hi"><i class="fa-solid fa-user-plus"></i> Akun baru</span>
        <h2>Buat akun</h2>
        <p class="sub">Daftar gratis dan langsung lacak pengirimanmu.</p>

        @if ($errors->register->any())
          <div class="err" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span>{{ $errors->register->first() }}</span></div>
        @endif

        <form action="{{ \Illuminate\Support\Facades\Route::has('register.process') ? route('register.process') : url('/register') }}" method="POST" novalidate data-form data-register>
          @csrf
          <input type="hidden" name="form_type" value="register">
          <div class="fg">
            <label for="r_name">Nama lengkap</label>
            <div class="in {{ $errors->register->has('name') ? 'bad' : '' }}">
              <i class="fa-regular fa-user l"></i>
              <input type="text" id="r_name" name="name" placeholder="Nama kamu" value="{{ old('form_type')==='register' ? old('name') : '' }}" autocomplete="name" required>
            </div>
          </div>
          <div class="fg">
            <label for="r_email">Email</label>
            <div class="in {{ $errors->register->has('email') ? 'bad' : '' }}">
              <i class="fa-regular fa-envelope l"></i>
              <input type="email" id="r_email" name="email" placeholder="nama@perusahaan.com" value="{{ old('form_type')==='register' ? old('email') : '' }}" autocomplete="email" required>
            </div>
          </div>
          <div class="fg">
            <label for="r_pw">Password</label>
            <div class="in {{ $errors->register->has('password') ? 'bad' : '' }}">
              <i class="fa-solid fa-lock l"></i>
              <input type="password" id="r_pw" name="password" placeholder="Minimal 8 karakter, huruf dan angka" autocomplete="new-password" required>
              <button type="button" class="eye" data-eye aria-label="Tampilkan password"><i class="fa-regular fa-eye"></i></button>
            </div>
            <div class="meter" id="meter" data-s="0"><span></span><span></span><span></span><span></span></div>
            <div class="mt" id="mt"></div>
          </div>
          <div class="fg">
            <label for="r_pw2">Ulangi password</label>
            <div class="in">
              <i class="fa-solid fa-shield-halved l"></i>
              <input type="password" id="r_pw2" name="password_confirmation" placeholder="Ketik ulang password" autocomplete="new-password" required>
              <button type="button" class="eye" data-eye aria-label="Tampilkan password"><i class="fa-regular fa-eye"></i></button>
            </div>
          </div>
          <button type="submit" class="go"><span class="spn"></span><span class="tx">Buat akun</span><i class="fa-solid fa-arrow-right"></i></button>
        </form>

        <p class="switch">Sudah punya akun? <button type="button" data-go="login">Masuk</button></p>
      </div>
    </div>

    <div class="foot">© {{ date('Y') }} CargoTrace. Sistem Manajemen Pengiriman.</div>
  </section>
</main>
</div>

<script>
(function () {
  const $ = (s, r) => (r || document).querySelector(s), $$ = (s, r) => [...(r || document).querySelectorAll(s)];
  const flipper = $('#flipper'), faces = { login: $('#faceLogin'), register: $('#faceRegister') };

  // Gelembung melayang di kedua panel
  const rnd = (a, b) => a + Math.random() * (b - a);
  function bubbles(el, n) {
    for (let i = 0; i < n; i++) {
      const s = document.createElement('s');
      s.style.cssText = '--x:' + rnd(0, 100) + '%;--s:' + rnd(4, 16) + 'px;--t:' + rnd(9, 20) + 's;--dl:-' + rnd(0, 20) + 's;--dx:' + rnd(-40, 60) + 'px';
      el.appendChild(s);
    }
  }
  bubbles($('#bubL'), 16); bubbles($('#bubR'), 22);

  // Cahaya mengikuti kursor
  const shell = $('#shell');
  shell.addEventListener('mousemove', e => {
    const r = shell.getBoundingClientRect();
    shell.style.setProperty('--mx', (e.clientX - r.left) + 'px');
    shell.style.setProperty('--my', (e.clientY - r.top) + 'px');
  });

  function go(name, focus) {
    flipper.classList.toggle('reg', name === 'register');
    faces.login.inert = name !== 'login';
    faces.register.inert = name !== 'register';
    if (focus) setTimeout(() => { const i = $('input:not([type=hidden])', faces[name]); i && i.focus({ preventScroll: true }); }, 700);
  }
  go(flipper.classList.contains('reg') ? 'register' : 'login', false);
  $$('[data-go]').forEach(b => b.addEventListener('click', () => go(b.dataset.go, true)));

  $$('[data-eye]').forEach(btn => btn.addEventListener('click', () => {
    const inp = btn.parentElement.querySelector('input'), show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    btn.innerHTML = '<i class="fa-regular ' + (show ? 'fa-eye-slash' : 'fa-eye') + '"></i>';
    btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
  }));

  const lp = $('[data-caps]'), caps = $('#caps');
  const cc = e => caps.classList.toggle('show', !!(e.getModifierState && e.getModifierState('CapsLock')));
  lp.addEventListener('keydown', cc); lp.addEventListener('keyup', cc);
  lp.addEventListener('blur', () => caps.classList.remove('show'));

  const pw = $('#r_pw'), meter = $('#meter'), mt = $('#mt'), words = ['', 'Lemah', 'Cukup', 'Bagus', 'Kuat'];
  pw.addEventListener('input', () => {
    const v = pw.value; let s = 0;
    if (v.length >= 8) s++;
    if (/[a-z]/i.test(v) && /\d/.test(v)) s++;
    if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v) && v.length >= 10) s++;
    if (!v) s = 0;
    meter.dataset.s = s;
    mt.textContent = v ? 'Kekuatan password: ' + words[s] : '';
  });

  $$('[data-form]').forEach(form => {
    form.addEventListener('submit', e => {
      const req = $$('input[required]', form);
      const bad = req.filter(i => !i.value.trim());
      if (form.hasAttribute('data-register')) {
        const a = $('#r_pw'), b = $('#r_pw2');
        if (a.value && b.value && a.value !== b.value && !bad.includes(b)) bad.push(b);
      }
      req.forEach(i => { const w = i.closest('.in'); if (w) w.classList.toggle('bad', bad.includes(i)); });
      if (bad.length) { e.preventDefault(); bad[0].focus(); return; }
      const btn = $('.go', form);
      btn.disabled = true; btn.classList.add('load');
      $('.tx', btn).textContent = 'Memproses…';
    });
    $$('input', form).forEach(i => i.addEventListener('input', () => { const w = i.closest('.in'); w && w.classList.remove('bad'); }));
  });
})();
</script>
</body>
</html>
