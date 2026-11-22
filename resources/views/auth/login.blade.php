<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | CargoTrace</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--navy:#061533;--blue:#2563eb;--sky:#38bdf8;--ink:#12306a;--mut:#6c83ab;--line:#dce8f8;--h:'Sora',sans-serif}
body{min-height:100vh;display:grid;place-items:center;padding:24px;font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;color:var(--ink);
 background:radial-gradient(circle at 15% 10%,#2f8cf5 0,#0000 420px),radial-gradient(circle at 90% 95%,#38bdf8 0,#0000 380px),linear-gradient(135deg,#061533,#0b2a6b 60%,#0f3a8f)}
:focus-visible{outline:2px solid var(--sky);outline-offset:2px}

.shell{position:relative;width:100%;max-width:1020px;min-height:600px;display:grid;grid-template-columns:1.05fr 1fr;border-radius:32px;overflow:hidden;background:#fff;box-shadow:0 40px 90px #02081c80}

/* PANEL KIRI */
.side{position:relative;display:flex;flex-direction:column;padding:44px 46px;color:#fff;overflow:hidden;
 background:linear-gradient(160deg,#061533f2 0%,#0b2a6be6 55%,#1d6fe8aa 100%),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1400&q=80') center/cover}
.side::before{content:"";position:absolute;width:380px;height:380px;right:-140px;top:-160px;border-radius:50%;background:radial-gradient(#7cc8ff66,#0000 70%)}
.side::after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(115deg,#ffffff08 0 2px,#0000 2px 28px);pointer-events:none}
.side>*{position:relative;z-index:2}
.brand{display:flex;align-items:center;gap:12px}
.logo{display:grid;place-items:center;width:48px;height:48px;border-radius:15px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-size:21px;box-shadow:0 10px 26px #2563eb88}
.bn{font:800 24px var(--h);letter-spacing:-.6px}
.bs{margin-top:4px;font-size:9.5px;letter-spacing:.9px;color:#a9c8f5}
.copy{margin:auto 0;padding:34px 0}
.live{display:inline-flex;align-items:center;gap:8px;margin-bottom:18px;padding:7px 14px;border-radius:20px;background:#ffffff1f;border:1px solid #ffffff33;font-size:11px;font-weight:600}
.live i{width:8px;height:8px;border-radius:50%;background:#4ade80;animation:ping 1.8s infinite}
@keyframes ping{0%{box-shadow:0 0 0 0 #4ade80aa}70%{box-shadow:0 0 0 9px #4ade8000}100%{box-shadow:0 0 0 0 #4ade8000}}
.copy h1{font:800 34px/1.25 var(--h);letter-spacing:-1px}
.copy p{margin-top:14px;max-width:420px;line-height:1.85;color:#cfe0ff}
.feats{display:grid;gap:10px}
.ft{display:flex;align-items:center;gap:14px;padding:13px 16px;border-radius:18px;background:#ffffff17;border:1px solid #ffffff2c;backdrop-filter:blur(8px)}
.ft i{display:grid;place-items:center;flex:none;width:38px;height:38px;border-radius:12px;background:#ffffff24;font-size:15px}
.ft b{display:block;font-size:12.5px}.ft small{display:block;margin-top:3px;font-size:11px;color:#bcd3f7}

/* PANEL KANAN */
.form{display:flex;flex-direction:column;justify-content:center;padding:52px 52px 34px;background:#fff}
.hi{display:inline-flex;align-items:center;gap:8px;align-self:flex-start;margin-bottom:14px;padding:6px 12px;border-radius:20px;background:#e8f1ff;color:#1d4ed8;font-size:11px;font-weight:700}
.form h2{font:800 30px var(--h);color:var(--navy);letter-spacing:-.8px}
.sub{margin:10px 0 28px;color:var(--mut);line-height:1.7;font-size:13px}
.err{display:flex;gap:10px;align-items:flex-start;margin-bottom:18px;padding:13px 15px;border-radius:14px;background:#ffe9e9;color:#b9302d;font-size:12.5px;line-height:1.6;font-weight:600}
.err i{margin-top:2px}
.fg{margin-bottom:18px}
.fg label{display:block;margin-bottom:8px;font-size:11px;font-weight:700;letter-spacing:.6px;color:#5d7aa5}
.in{position:relative}
.in>i.l{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#8ca6c4;font-size:14px;pointer-events:none}
.in input{width:100%;padding:15px 48px 15px 46px;border:1.5px solid var(--line);border-radius:14px;background:#f7faff;color:var(--navy);font:600 14px 'Plus Jakarta Sans',sans-serif;transition:.2s}
.in input::placeholder{color:#9db0cc;font-weight:500}
.in input:focus{outline:none;border-color:var(--blue);background:#fff;box-shadow:0 0 0 4px #2563eb1c}
.in.bad input{border-color:#e5484d;background:#fff8f8}
.eye{position:absolute;right:8px;top:50%;transform:translateY(-50%);display:grid;place-items:center;width:38px;height:38px;border:0;border-radius:10px;background:transparent;color:#8ca6c4;cursor:pointer;font-size:14px}
.eye:hover{background:#e8f1ff;color:var(--blue)}
.caps{display:none;margin-top:8px;font-size:11.5px;font-weight:600;color:#b77900}
.caps.show{display:block}
.go{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;margin-top:8px;padding:16px;border:0;border-radius:14px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font:700 14px 'Plus Jakarta Sans',sans-serif;cursor:pointer;box-shadow:0 14px 28px #2563eb4d;transition:.2s}
.go:hover{filter:brightness(1.08);transform:translateY(-2px)}
.go:disabled{opacity:.75;cursor:wait;transform:none}
.spin{display:none;width:16px;height:16px;border:3px solid #ffffff55;border-top-color:#fff;border-radius:50%;animation:rot .7s linear infinite}
.go.load .spin{display:inline-block}.go.load .arr{display:none}
@keyframes rot{to{transform:rotate(360deg)}}
.note{display:flex;align-items:center;gap:10px;margin-top:22px;padding:13px 15px;border-radius:14px;background:#f4f8ff;color:var(--mut);font-size:11.5px;line-height:1.6}
.note i{color:var(--blue)}
.foot{margin-top:auto;padding-top:26px;text-align:center;font-size:11px;color:#9db0cc}

@media(max-width:900px){.shell{grid-template-columns:1fr;max-width:560px}.side{padding:30px 28px}.copy{padding:26px 0 20px}.copy h1{font-size:26px}.feats{display:none}.form{padding:34px 28px 26px}}
@media(max-width:420px){body{padding:12px}.form h2{font-size:25px}.copy p{font-size:13px}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>
</head>
<body>

<main class="shell">

    <section class="side">
        <div class="brand">
            <div class="logo"><i class="fa-solid fa-route"></i></div>
            <div>
                <div class="bn">CargoTrace</div>
                <div class="bs">LOGISTICS &amp; SHIPMENT TRACKING</div>
            </div>
        </div>

        <div class="copy">
            <span class="live"><i></i> Pemantauan pengiriman langsung</span>
            <h1>Kelola Pengiriman dengan Lebih Mudah</h1>
            <p>Pantau perjalanan kargo, status pengiriman, keterlambatan, dan informasi logistik dalam satu platform yang terintegrasi.</p>
        </div>

        <div class="feats">
            <div class="ft"><i class="fa-solid fa-location-dot"></i><div><b>Pelacakan real-time</b><small>Posisi terakhir barang dan riwayat perjalanan.</small></div></div>
            <div class="ft"><i class="fa-solid fa-map-location-dot"></i><div><b>Peta persebaran</b><small>Rute, armada, pelabuhan, bandara, dan depo.</small></div></div>
            <div class="ft"><i class="fa-solid fa-triangle-exclamation"></i><div><b>Pantau pengecualian</b><small>Tangani keterlambatan dan kendala pengiriman.</small></div></div>
        </div>
    </section>

    <section class="form">
        <span class="hi"><i class="fa-solid fa-lock"></i> Akses aman</span>
        <h2>Selamat Datang</h2>
        <p class="sub">Silakan masuk dengan akun kamu untuk mengakses CargoTrace.</p>

        @if ($errors->any())
            <div class="err" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" id="loginForm" novalidate>
            @csrf

            <div class="fg">
                <label for="email">EMAIL</label>
                <div class="in {{ $errors->has('email') ? 'bad' : '' }}">
                    <i class="fa-regular fa-envelope l"></i>
                    <input type="email" id="email" name="email" placeholder="nama@perusahaan.com" value="{{ old('email') }}" autocomplete="email" required autofocus>
                </div>
            </div>

            <div class="fg">
                <label for="password">PASSWORD</label>
                <div class="in {{ $errors->has('password') ? 'bad' : '' }}">
                    <i class="fa-solid fa-lock l"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
                    <button type="button" class="eye" id="togglePw" aria-label="Tampilkan password"><i class="fa-regular fa-eye"></i></button>
                </div>
                <div class="caps" id="caps"><i class="fa-solid fa-triangle-exclamation"></i> Caps Lock sedang aktif.</div>
            </div>

            <button type="submit" class="go" id="goBtn">
                <span class="spin"></span>
                <span id="goTx">Masuk</span>
                <i class="fa-solid fa-arrow-right arr"></i>
            </button>
        </form>

        <div class="note"><i class="fa-solid fa-shield-halved"></i><span>Hubungi administrator jika kamu lupa password atau belum memiliki akun.</span></div>

        <div class="foot">© {{ date('Y') }} CargoTrace. Sistem Manajemen Pengiriman.</div>
    </section>

</main>

<script>
(function () {
    const pw = document.getElementById('password'), eye = document.getElementById('togglePw'), caps = document.getElementById('caps');
    const form = document.getElementById('loginForm'), btn = document.getElementById('goBtn');

    eye.addEventListener('click', function () {
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        eye.innerHTML = '<i class="fa-regular ' + (show ? 'fa-eye-slash' : 'fa-eye') + '"></i>';
        eye.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        pw.focus();
    });

    function checkCaps(e) { caps.classList.toggle('show', e.getModifierState && e.getModifierState('CapsLock')); }
    pw.addEventListener('keydown', checkCaps);
    pw.addEventListener('keyup', checkCaps);
    pw.addEventListener('blur', function () { caps.classList.remove('show'); });

    form.addEventListener('submit', function (e) {
        const email = document.getElementById('email');
        if (!email.value.trim() || !pw.value) {
            e.preventDefault();
            [email, pw].forEach(function (el) { el.parentElement.classList.toggle('bad', !el.value.trim()); });
            (email.value.trim() ? pw : email).focus();
            return;
        }
        btn.disabled = true;
        btn.classList.add('load');
        document.getElementById('goTx').textContent = 'Memproses…';
    });

    [document.getElementById('email'), pw].forEach(function (el) {
        el.addEventListener('input', function () { el.parentElement.classList.remove('bad'); });
    });
})();
</script>
</body>
</html>