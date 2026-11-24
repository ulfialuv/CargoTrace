<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Berita | CargoTrace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--navy:#0b1b3b;--blue:#2563eb;--sky:#38bdf8;--bg:#eef3fb;--card:#fff;--ink:#14264d;--mut:#6b7f9e;--line:#e1e9f6;--ok:#16a34a}
@media(prefers-color-scheme:dark){:root{--bg:#0a1226;--card:#111c38;--ink:#e6eeff;--mut:#8ea3c9;--line:#1f2f55}}
body{font-family:'Plus Jakarta Sans',system-ui,Arial,sans-serif;background:var(--bg);color:var(--ink);font-size:14px;line-height:1.5}
a{color:inherit;text-decoration:none}
button,select,input{font-family:inherit}
:focus-visible{outline:2px solid var(--blue);outline-offset:2px}

.top{background:linear-gradient(120deg,#061533,#0b2a6b 60%,#1d4ed8);color:#fff;padding:26px 28px 74px}
.top-in{max-width:1240px;margin:0 auto}
.bar{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap}
.brand{display:flex;align-items:center;gap:10px;font-weight:800;font-size:18px}
.brand i{display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2563eb)}
.back{display:inline-flex;align-items:center;gap:8px;padding:9px 15px;border:1px solid #ffffff44;border-radius:12px;background:#ffffff1a;color:#fff;font-weight:700;font-size:12.5px}
.back:hover{background:#ffffff30}
.top h1{margin-top:26px;font-size:30px;font-weight:800;letter-spacing:-.6px}
.top p{margin-top:6px;color:#c9dcff;max-width:640px}
.today{margin-top:10px;font-size:12px;color:#a9c8f5}

.wrap{max-width:1240px;margin:-50px auto 0;padding:0 28px 50px;position:relative}
.search{background:var(--card);border:1px solid var(--line);border-radius:20px;padding:14px;box-shadow:0 18px 40px #0b2a6b22}
.srow{display:flex;gap:10px;flex-wrap:wrap}
.sin{position:relative;flex:1;min-width:240px}
.sin i{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--mut)}
.sin input{width:100%;padding:14px 16px 14px 44px;border:1px solid var(--line);border-radius:13px;background:var(--bg);color:var(--ink);font-size:14px;font-weight:500}
.sin input:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px #2563eb22}
.sel{padding:13px 14px;border:1px solid var(--line);border-radius:13px;background:var(--bg);color:var(--ink);font-size:13px;font-weight:600;cursor:pointer;max-width:100%}
.btn{display:inline-flex;align-items:center;gap:8px;padding:13px 20px;border:0;border-radius:13px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font-weight:700;font-size:13px;cursor:pointer;box-shadow:0 8px 20px #2563eb40}
.btn:hover{filter:brightness(1.08)}
.btn.ghost{background:var(--bg);color:var(--ink);box-shadow:none;border:1px solid var(--line)}
.chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
.chip{padding:8px 14px;border:1px solid var(--line);border-radius:999px;background:var(--card);color:var(--mut);font-size:12px;font-weight:700;cursor:pointer;transition:.15s}
.chip:hover{border-color:var(--blue);color:var(--blue)}
.chip.on{background:var(--navy);border-color:var(--navy);color:#fff}
@media(prefers-color-scheme:dark){.chip.on{background:var(--blue);border-color:var(--blue)}}
.try{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:12px;font-size:12px;color:var(--mut)}
.try button{border:0;background:#2563eb14;color:var(--blue);padding:5px 11px;border-radius:999px;font-size:11.5px;font-weight:700;cursor:pointer}
.try button:hover{background:#2563eb26}

.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin:18px 0 6px}
.st{display:flex;align-items:center;gap:12px;padding:15px 16px;background:var(--card);border:1px solid var(--line);border-radius:16px}
.st i{display:grid;place-items:center;width:40px;height:40px;border-radius:12px;background:#2563eb18;color:var(--blue);font-size:16px;flex:none}
.st b{display:block;font-size:20px;font-weight:800;line-height:1.2}
.st small{color:var(--mut);font-size:11.5px}

.sec{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin:26px 0 14px}
.sec h2{font-size:18px;font-weight:800;display:flex;align-items:center;gap:10px}
.sec h2 i{color:var(--blue)}
.sec span{color:var(--mut);font-size:12px}

.hero{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(0,1fr);gap:16px}
.lead{position:relative;display:flex;flex-direction:column;justify-content:flex-end;min-height:340px;padding:26px;border-radius:24px;color:#fff;overflow:hidden;transition:transform .2s}
.lead:hover{transform:translateY(-3px)}
.lead::before{content:"";position:absolute;inset:0;z-index:1;background:linear-gradient(180deg,#0000 15%,#000c 100%)}
.lead>*{position:relative;z-index:2}
.lead>img.bg{position:absolute;inset:0;z-index:0;width:100%;height:100%;object-fit:cover;transition:transform .5s}
.lead:hover>img.bg{transform:scale(1.04)}
.ph{position:relative;overflow:hidden;display:grid;place-items:center;color:#ffffffcc;font-weight:800}
.ph img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;background:#0001}
.lead .fav{position:absolute;top:22px;right:22px;width:46px;height:46px;border-radius:14px;background:#fff;padding:8px}
.lead h3{margin-top:12px;font-size:24px;line-height:1.35;font-weight:800;letter-spacing:-.3px}
.tag{display:inline-flex;align-items:center;gap:7px;width:fit-content;padding:5px 11px;border-radius:999px;background:#ffffff26;font-size:11px;font-weight:700}
.tag img{width:14px;height:14px;border-radius:4px;background:#fff}
.lead .m{margin-top:12px;font-size:12px;color:#e4eeff;display:flex;gap:10px;flex-wrap:wrap}
.side{display:flex;flex-direction:column;gap:12px}
.si{display:flex;gap:14px;align-items:flex-start;padding:16px;background:var(--card);border:1px solid var(--line);border-radius:18px;transition:.2s;flex:1}
.si:hover{transform:translateY(-2px);border-color:#9fc2f7;box-shadow:0 10px 24px #2563eb18}
.si .th{flex:none;width:104px;height:80px;border-radius:13px;font-size:22px}
.si h4{font-size:14px;line-height:1.45;font-weight:700;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.si small{display:block;margin-top:6px;color:var(--mut);font-size:11.5px}

.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.cd{display:flex;flex-direction:column;background:var(--card);border:1px solid var(--line);border-radius:20px;overflow:hidden;transition:.2s}
.cd:hover{transform:translateY(-4px);box-shadow:0 16px 32px #2563eb1c;border-color:#9fc2f7}
.cd .ph{aspect-ratio:16/9;font-size:46px}
.cd .src{display:flex;align-items:center;gap:7px;margin-bottom:8px;font-size:11.5px;font-weight:700;color:var(--blue)}
.cd .src img{width:16px;height:16px;border-radius:4px;background:#fff}
.cd p{margin-top:8px;font-size:12.5px;color:var(--mut);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.cd .bd{display:flex;flex-direction:column;flex:1;padding:16px}
.cd h3{font-size:15.5px;line-height:1.5;font-weight:700;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.cd .m{margin-top:auto;padding-top:14px;display:flex;justify-content:space-between;gap:8px;color:var(--mut);font-size:11.5px}
.cd .m b{color:var(--blue);font-weight:700}

.more{display:flex;justify-content:center;margin-top:24px}
.state{grid-column:1/-1;padding:44px 20px;text-align:center;background:var(--card);border:1px dashed var(--line);border-radius:20px;color:var(--mut)}
.state i{display:block;font-size:34px;color:#9db6dc;margin-bottom:12px}
.state strong{display:block;color:var(--ink);font-size:16px;margin-bottom:6px}
.state .btn{margin-top:16px}
.sk{border-radius:20px;background:linear-gradient(90deg,var(--line),var(--card),var(--line));background-size:200% 100%;animation:sk 1.2s infinite;min-height:190px}
.sk.big{min-height:340px}
@keyframes sk{to{background-position:-200% 0}}
.note{margin-top:30px;text-align:center;font-size:11.5px;color:var(--mut)}

@media(max-width:1000px){.hero{grid-template-columns:1fr}.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.top{padding:20px 16px 70px}.wrap{padding:0 14px 40px}.top h1{font-size:24px}.grid{grid-template-columns:1fr}.lead{min-height:280px;padding:20px}.lead h3{font-size:19px}.btn,.sel{flex:1}}
@media(prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>
</head>
<body>

<header class="top">
  <div class="top-in">
    <div class="bar">
      <div class="brand"><i class="fa-solid fa-route"></i> CargoTrace</div>
      <a href="{{ route('dashboard') }}" class="back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>
    <h1>Berita &amp; Informasi</h1>
    <p>Cari berita apa saja: dari kampung terpencil sampai negara di ujung dunia, dalam berbagai bahasa, seperti Google News.</p>
    <div class="today"><i class="fa-regular fa-calendar"></i> <span id="today"></span></div>
  </div>
</header>

<main class="wrap">
  <section class="search">
    <form class="srow" id="form">
      <div class="sin">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input id="q" type="search" placeholder="Cari apa saja: nama desa, kota, negara, tokoh, kejadian…" aria-label="Cari berita" autocomplete="off">
      </div>
      <select class="sel" id="region" aria-label="Wilayah / bahasa berita">
        @foreach($regions as $key => $r)
          <option value="{{ $key }}">{{ $r['label'] }}</option>
        @endforeach
      </select>
      <select class="sel" id="when" aria-label="Rentang waktu">
        <option value="any">Kapan saja</option>
        <option value="1h">1 jam terakhir</option>
        <option value="1d">24 jam terakhir</option>
        <option value="7d">7 hari terakhir</option>
        <option value="30d">30 hari terakhir</option>
        <option value="1y">1 tahun terakhir</option>
      </select>
      <select class="sel" id="sort" aria-label="Urutan">
        <option value="rel">Paling relevan</option>
        <option value="new">Terbaru</option>
      </select>
      <button class="btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
      <button class="btn ghost" type="button" id="reset"><i class="fa-solid fa-rotate-left"></i> Reset</button>
    </form>

    <div class="chips" id="cats">
      <button class="chip on" data-c="all" type="button">Semua</button>
      <button class="chip" data-c="nation" type="button">Nasional</button>
      <button class="chip" data-c="world" type="button">Internasional</button>
      <button class="chip" data-c="business" type="button">Ekonomi &amp; Bisnis</button>
      <button class="chip" data-c="logistics" type="button">Logistik</button>
      <button class="chip" data-c="technology" type="button">Teknologi</button>
      <button class="chip" data-c="science" type="button">Sains</button>
      <button class="chip" data-c="health" type="button">Kesehatan</button>
      <button class="chip" data-c="sports" type="button">Olahraga</button>
      <button class="chip" data-c="entertainment" type="button">Hiburan</button>
    </div>

    <div class="try">Coba cari:
      <button type="button" data-s="Papua">Papua</button>
      <button type="button" data-s="pelabuhan Belawan">pelabuhan Belawan</button>
      <button type="button" data-s="Rantauprapat">Rantauprapat</button>
      <button type="button" data-s="harga BBM">harga BBM</button>
      <button type="button" data-s="kapal kargo">kapal kargo</button>
      <button type="button" data-s="Greenland">Greenland</button>
    </div>
  </section>

  <section class="stats">
    <div class="st"><i class="fa-regular fa-newspaper"></i><div><b id="sTotal">—</b><small>Berita ditemukan</small></div></div>
    <div class="st"><i class="fa-solid fa-tower-broadcast"></i><div><b id="sSrc">—</b><small>Sumber media</small></div></div>
    <div class="st"><i class="fa-solid fa-bolt"></i><div><b id="sFresh">—</b><small>Terbit 24 jam terakhir</small></div></div>
    <div class="st"><i class="fa-solid fa-earth-asia"></i><div><b id="sReg">—</b><small>Wilayah berita</small></div></div>
  </section>

  <div class="sec"><h2><i class="fa-solid fa-fire"></i> <span id="title" style="color:inherit;font-size:inherit">Berita utama</span></h2><span id="sub"></span></div>
  <div class="hero" id="hero"></div>

  <div class="sec"><h2><i class="fa-regular fa-newspaper"></i> Berita lainnya</h2><span id="count"></span></div>
  <div class="grid" id="grid"></div>
  <div class="more"><button class="btn ghost" id="more" type="button" hidden><i class="fa-solid fa-angles-down"></i> Muat lebih banyak</button></div>

  <p class="note">Berita dikumpulkan dari Bing News dan Google News. Klik artikel untuk membaca lengkap di situs penerbitnya.</p>
</main>

<script>
(() => {
  const ENDPOINT = @json(route('berita.api'));
  const PAGE = 12;
  const $ = id => document.getElementById(id);
  const st = { c:'all', q:'', region:'id', when:'any', sort:'rel', all:[], shown:PAGE, ctl:null };

  const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  const safe = u => { try { const x = new URL(u); return /^https?:$/.test(x.protocol) ? x.href : ''; } catch (_) { return ''; } };
  const CAT = { all:'Berita utama', nation:'Berita nasional', world:'Berita dunia', business:'Ekonomi & bisnis', logistics:'Logistik & pengiriman', technology:'Teknologi', science:'Sains', health:'Kesehatan', sports:'Olahraga', entertainment:'Hiburan' };

  $('today').textContent = new Intl.DateTimeFormat('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' }).format(new Date());

  const hue = s => { let h = 0; for (const ch of String(s)) h = (h * 31 + ch.charCodeAt(0)) % 360; return h; };
  const grad = s => { const h = hue(s); return 'linear-gradient(135deg,hsl(' + h + ' 62% 40%),hsl(' + ((h + 45) % 360) + ' 65% 26%))'; };
  const fav = a => a.host ? 'https://www.google.com/s2/favicons?sz=64&domain=' + encodeURIComponent(a.host) : '';
  const favImg = (a, cls) => fav(a) ? '<img ' + (cls ? 'class="' + cls + '" ' : '') + 'src="' + esc(fav(a)) + '" alt="" loading="lazy" onerror="this.style.display=\'none\'">' : '';
  const ago = iso => {
    if (!iso) return 'waktu tidak diketahui';
    const m = Math.floor((Date.now() - new Date(iso).getTime()) / 60000);
    if (isNaN(m)) return 'waktu tidak diketahui';
    if (m < 1) return 'baru saja';
    if (m < 60) return m + ' menit lalu';
    if (m < 1440) return Math.floor(m / 60) + ' jam lalu';
    if (m < 43200) return Math.floor(m / 1440) + ' hari lalu';
    return new Intl.DateTimeFormat('id-ID', { day:'numeric', month:'short', year:'numeric' }).format(new Date(iso));
  };

  const initial = a => esc((a.source || '?').charAt(0).toUpperCase());
  const pic = (a, cls) => '<div class="ph ' + cls + '" style="background:' + grad(a.source) + '">' + initial(a) +
    (a.image ? '<img src="' + esc(a.image) + '" alt="" loading="lazy" referrerpolicy="no-referrer" onerror="this.remove()">' : '') + '</div>';

  function lead(a) {
    return '<a class="lead" href="' + esc(safe(a.url)) + '" target="_blank" rel="noopener noreferrer" style="background:' + grad(a.source) + '">' +
      (a.image ? '<img class="bg" src="' + esc(a.image) + '" alt="" referrerpolicy="no-referrer" onerror="this.remove()">' : '') +
      '<span class="tag">' + favImg(a) + esc(a.source) + '</span><h3>' + esc(a.title) + '</h3>' +
      (a.description ? '<p style="margin-top:8px;font-size:13px;color:#dbe8ff;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">' + esc(a.description) + '</p>' : '') +
      '<div class="m"><span><i class="fa-regular fa-clock"></i> ' + ago(a.publishedAt) + '</span><span>Baca selengkapnya <i class="fa-solid fa-arrow-up-right-from-square"></i></span></div></a>';
  }
  function side(a) {
    return '<a class="si" href="' + esc(safe(a.url)) + '" target="_blank" rel="noopener noreferrer">' + pic(a, 'th') +
      '<div><h4>' + esc(a.title) + '</h4><small>' + esc(a.source) + ' · ' + ago(a.publishedAt) + '</small></div></a>';
  }
  function card(a) {
    return '<a class="cd" href="' + esc(safe(a.url)) + '" target="_blank" rel="noopener noreferrer">' + pic(a, '') +
      '<div class="bd"><div class="src">' + favImg(a) + esc(a.source) + '</div><h3>' + esc(a.title) + '</h3>' +
      (a.description ? '<p>' + esc(a.description) + '</p>' : '') +
      '<div class="m"><span><i class="fa-regular fa-clock"></i> ' + ago(a.publishedAt) + '</span><b>Baca <i class="fa-solid fa-arrow-up-right-from-square"></i></b></div></div></a>';
  }

  function sorted() {
    const l = st.all.slice();
    if (st.sort === 'new') l.sort((a, b) => new Date(b.publishedAt || 0) - new Date(a.publishedAt || 0));
    return l;
  }

  function render(regionLabel) {
    const list = sorted();
    $('title').textContent = st.q ? 'Hasil untuk “' + st.q + '”' : (CAT[st.c] || 'Berita');
    $('sub').textContent = regionLabel || '';
    $('sTotal').textContent = list.length;
    $('sSrc').textContent = new Set(list.map(a => a.source)).size;
    $('sFresh').textContent = list.filter(a => a.publishedAt && Date.now() - new Date(a.publishedAt).getTime() < 864e5).length;
    $('sReg').textContent = regionLabel || '—';

    if (!list.length) {
      $('hero').innerHTML = '<div class="state" style="grid-column:1/-1"><i class="fa-regular fa-face-frown"></i><strong>Berita tidak ditemukan</strong>Coba ejaan lain, perluas rentang waktu ke “Kapan saja”, atau ganti wilayah / bahasa berita.</div>';
      $('grid').innerHTML = ''; $('count').textContent = '0 artikel'; $('more').hidden = true;
      return;
    }
    // berita ber-gambar didahulukan untuk berita utama dan 3 berita samping
    const withImg = list.filter(x => x.image), noImg = list.filter(x => !x.image);
    const top = withImg.slice(0, 4).concat(noImg.slice(0, Math.max(0, 4 - withImg.length)));
    const rest0 = list.filter(x => !top.includes(x));
    list.length = 0; list.push(...top, ...rest0);
    $('hero').innerHTML = lead(list[0]) + '<div class="side">' + list.slice(1, 4).map(side).join('') + '</div>';
    const rest = list.slice(4), vis = rest.slice(0, st.shown);
    $('grid').innerHTML = vis.map(card).join('') || '<div class="state"><i class="fa-regular fa-newspaper"></i>Tidak ada artikel tambahan.</div>';
    $('count').textContent = 'Menampilkan ' + (vis.length + Math.min(4, list.length)) + ' dari ' + list.length + ' artikel';
    $('more').hidden = vis.length >= rest.length;
  }

  function loading() {
    $('hero').innerHTML = '<div class="sk big"></div><div class="side"><div class="sk"></div><div class="sk"></div><div class="sk"></div></div>';
    $('grid').innerHTML = '<div class="sk"></div><div class="sk"></div><div class="sk"></div>';
    $('more').hidden = true; $('count').textContent = 'Memuat…'; $('sub').textContent = '';
  }

  function sync() {
    const p = new URLSearchParams();
    if (st.q) p.set('q', st.q);
    if (st.c !== 'all') p.set('category', st.c);
    if (st.region !== 'id') p.set('region', st.region);
    if (st.when !== 'any') p.set('when', st.when);
    history.replaceState(null, '', location.pathname + (p.toString() ? '?' + p : ''));
  }

  async function load() {
    if (st.ctl) st.ctl.abort();
    const ctl = st.ctl = new AbortController();
    const timer = setTimeout(() => ctl.abort(), 25000);
    st.shown = PAGE; loading(); sync();
    const u = new URL(ENDPOINT, location.origin);
    u.searchParams.set('q', st.q); u.searchParams.set('category', st.c);
    u.searchParams.set('region', st.region); u.searchParams.set('when', st.when);
    try {
      const r = await fetch(u, { headers:{ 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }, signal:ctl.signal });
      const d = await r.json();
      if (!r.ok || d.success === false) throw new Error(d.message || 'HTTP ' + r.status);
      st.all = Array.isArray(d.articles) ? d.articles : [];
      render(d.region);
    } catch (e) {
      if (ctl.signal.aborted && st.ctl !== ctl) return;
      $('hero').innerHTML = '<div class="state" style="grid-column:1/-1"><i class="fa-solid fa-plug-circle-exclamation"></i><strong>Berita belum bisa dimuat</strong>' + esc(e.name === 'AbortError' ? 'Permintaan terlalu lama. Coba lagi.' : (e.message || 'Periksa koneksi internet.')) + '<br><button class="btn" id="retry" type="button"><i class="fa-solid fa-rotate"></i> Coba lagi</button></div>';
      $('grid').innerHTML = ''; $('count').textContent = ''; $('more').hidden = true;
      $('retry') && $('retry').addEventListener('click', load);
    } finally { clearTimeout(timer); }
  }

  $('form').addEventListener('submit', e => { e.preventDefault(); st.q = $('q').value.trim(); load(); });
  $('region').addEventListener('change', () => { st.region = $('region').value; load(); });
  $('when').addEventListener('change', () => { st.when = $('when').value; load(); });
  $('sort').addEventListener('change', () => { st.sort = $('sort').value; st.shown = PAGE; if (st.all.length) render($('sReg').textContent); });
  $('more').addEventListener('click', () => { st.shown += PAGE; render($('sReg').textContent); });
  $('reset').addEventListener('click', () => {
    Object.assign(st, { c:'all', q:'', region:'id', when:'any', sort:'rel' });
    $('q').value = ''; $('region').value = 'id'; $('when').value = 'any'; $('sort').value = 'rel';
    document.querySelectorAll('#cats .chip').forEach(b => b.classList.toggle('on', b.dataset.c === 'all'));
    load();
  });
  $('cats').addEventListener('click', e => {
    const b = e.target.closest('[data-c]'); if (!b) return;
    st.c = b.dataset.c; st.q = ''; $('q').value = '';
    document.querySelectorAll('#cats .chip').forEach(x => x.classList.toggle('on', x === b));
    load();
  });
  document.querySelector('.try').addEventListener('click', e => {
    const b = e.target.closest('[data-s]'); if (!b) return;
    st.q = b.dataset.s; $('q').value = st.q; st.c = 'all';
    document.querySelectorAll('#cats .chip').forEach(x => x.classList.toggle('on', x.dataset.c === 'all'));
    load();
  });

  /* kondisi awal dari URL (supaya refresh / dibagikan tetap sama) */
  const p = new URLSearchParams(location.search);
  st.q = (p.get('q') || '').slice(0, 150); st.c = CAT[p.get('category')] ? p.get('category') : 'all';
  if ($('region').querySelector('option[value="' + (p.get('region') || '') + '"]')) st.region = p.get('region');
  if ($('when').querySelector('option[value="' + (p.get('when') || '') + '"]')) st.when = p.get('when');
  $('q').value = st.q; $('region').value = st.region; $('when').value = st.when;
  document.querySelectorAll('#cats .chip').forEach(x => x.classList.toggle('on', x.dataset.c === st.c));
  load();
})();
</script>
</body>
</html>
