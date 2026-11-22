<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita | CargoTrace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body style="margin:0;font-family:Inter,Arial,sans-serif">

<style>
:root{--ct-navy:#101d3a;--ct-navy2:#172b50;--ct-blue:#284b80;--ct-bg:#f4f6fb;--ct-white:#fff;--ct-text:#182338;--ct-muted:#778198;--ct-border:#e5eaf2;--ct-yellow:#f4c542}
.news-page{color:var(--ct-text);padding:24px;background:var(--ct-bg);min-height:calc(100vh - 70px)}
.news-head{display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:22px}
.news-head h1{font-size:28px;font-weight:800;margin:0;color:var(--ct-navy)}.news-head p{margin:6px 0 0;color:var(--ct-muted)}
.news-date{background:#fff;border:1px solid var(--ct-border);border-radius:12px;padding:10px 15px;color:var(--ct-muted);font-size:13px}
.news-head-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.back-dashboard{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 15px;background:var(--ct-navy);color:#fff;border:1px solid var(--ct-navy);border-radius:12px;text-decoration:none;font-size:13px;font-weight:700;transition:background .2s,transform .2s}
.back-dashboard:hover{background:var(--ct-blue);border-color:var(--ct-blue);transform:translateY(-2px)}
.back-dashboard:focus-visible{outline:3px solid var(--ct-yellow);outline-offset:3px}
@media(max-width:650px){.news-head-actions{width:100%;justify-content:space-between}.back-dashboard{flex:1}.news-date{white-space:nowrap}}

.news-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:22px}
.news-stat{background:#fff;border:1px solid var(--ct-border);border-radius:16px;padding:18px;display:flex;gap:13px;align-items:center;box-shadow:0 4px 16px #15284b08}
.news-stat-icon{width:45px;height:45px;border-radius:13px;background:#eaf0fb;color:var(--ct-blue);display:grid;place-items:center;font-size:20px;flex-shrink:0}
.news-stat strong{display:block;font-size:23px;color:var(--ct-navy);line-height:1.2}.news-stat small{color:var(--ct-muted);font-size:12px}
.news-toolbar{background:#fff;border:1px solid var(--ct-border);border-radius:16px;padding:16px;margin-bottom:18px}
.news-search{display:flex;gap:10px;align-items:center}.news-search input{flex:1;min-width:0;border:1px solid var(--ct-border);background:#f8faff;border-radius:11px;padding:12px 14px;outline:none;color:var(--ct-text)}.news-search input:focus{border-color:#5276b5;box-shadow:0 0 0 3px #5276b51a}
.news-btn{border:0;border-radius:10px;padding:12px 17px;background:var(--ct-navy);color:#fff;font-weight:700;cursor:pointer}.news-btn:hover{background:var(--ct-blue)}.news-btn.secondary{background:#edf2fa;color:var(--ct-navy)}
.news-categories{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}.news-cat{border:1px solid var(--ct-border);background:#fff;color:#65718a;border-radius:999px;padding:8px 14px;font-size:12px;font-weight:700;cursor:pointer}.news-cat.active,.news-cat:hover{background:var(--ct-navy);border-color:var(--ct-navy);color:#fff}
.news-section-title{display:flex;justify-content:space-between;align-items:center;gap:12px;margin:22px 0 14px}.news-section-title h2{margin:0;color:var(--ct-navy);font-size:19px;font-weight:800}.news-section-title span{color:var(--ct-muted);font-size:12px}
.news-feature{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(260px,.85fr);gap:16px;margin-bottom:20px}
.news-feature-main,.news-feature-side{border:1px solid var(--ct-border);background:#fff;border-radius:17px;overflow:hidden;min-width:0}.news-feature-main{display:grid;grid-template-columns:1fr 1fr;min-height:260px}
.news-image{width:100%;height:100%;min-height:180px;object-fit:cover;background:#e8edf5;display:block}.news-feature-copy{padding:20px;display:flex;flex-direction:column;align-items:flex-start}.news-tag{display:inline-block;background:#eaf0fb;color:#294c80;padding:5px 9px;border-radius:6px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.4px}.news-feature-copy h3{font-size:20px;line-height:1.4;color:var(--ct-navy);margin:12px 0 8px}.news-feature-copy p{font-size:13px;line-height:1.65;color:#69758b;margin:0 0 14px}.news-meta{font-size:11px;color:#8993a6;margin-top:auto;display:flex;gap:8px;flex-wrap:wrap}.news-read{margin-top:14px;color:#244a83;text-decoration:none;font-size:12px;font-weight:800}.news-feature-side{padding:18px;display:flex;flex-direction:column;gap:14px}.news-side-item{display:flex;gap:12px;min-height:68px}.news-side-item img{width:82px;height:68px;border-radius:9px;object-fit:cover;background:#e8edf5;flex-shrink:0}.news-side-item h4{font-size:13px;line-height:1.4;margin:0 0 6px;color:var(--ct-navy)}.news-side-item small{font-size:10px;color:var(--ct-muted)}
.news-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.news-card{background:#fff;border:1px solid var(--ct-border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;min-width:0;transition:transform .18s,box-shadow .18s}.news-card:hover{transform:translateY(-3px);box-shadow:0 10px 25px #15284b12}.news-card img{height:170px;width:100%;object-fit:cover;background:#e8edf5}.news-card-body{padding:16px;display:flex;flex-direction:column;flex:1}.news-card h3{font-size:15px;line-height:1.5;color:var(--ct-navy);margin:10px 0}.news-card p{font-size:12px;line-height:1.65;color:#6e7a90;margin:0 0 15px}.news-card .news-meta{margin-top:auto}.news-card .news-read{align-self:flex-start}
.news-status{padding:30px 18px;text-align:center;background:#fff;border:1px solid var(--ct-border);border-radius:15px;color:var(--ct-muted);grid-column:1/-1}.news-status strong{display:block;color:var(--ct-navy);margin-bottom:7px}.news-status .news-btn{margin-top:12px}.news-pagination{display:flex;justify-content:center;align-items:center;gap:12px;margin:24px 0 8px}.news-pagination button{border:1px solid var(--ct-border);background:#fff;color:var(--ct-navy);border-radius:9px;padding:9px 14px;cursor:pointer;font-weight:700}.news-pagination button:disabled{opacity:.4;cursor:not-allowed}.news-pagination span{font-size:12px;color:var(--ct-muted)}.news-footer-note{font-size:11px;color:#929bad;text-align:center;margin-top:18px}
@media(max-width:1050px){.news-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.news-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.news-feature{grid-template-columns:1fr}.news-feature-side{display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:650px){.news-page{padding:15px}.news-stats{grid-template-columns:1fr 1fr;gap:9px}.news-stat{padding:12px;gap:9px}.news-stat-icon{width:35px;height:35px}.news-stat strong{font-size:19px}.news-search{flex-wrap:wrap}.news-search input{flex-basis:100%}.news-search .news-btn{flex:1}.news-feature-main{grid-template-columns:1fr}.news-feature-main .news-image{height:190px}.news-feature-side{grid-template-columns:1fr}.news-grid{grid-template-columns:1fr}.news-card img{height:190px}.news-head h1{font-size:23px}}
</style>

<div class="news-page">
    <div class="news-head">
        <div><h1>Berita & Informasi</h1><p>Informasi terbaru seputar logistik, transportasi, ekonomi, dan dunia.</p></div>
        <div class="news-head-actions">
            <a href="{{ route('dashboard') }}" class="back-dashboard">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
            <div class="news-date">
                <i class="fa-regular fa-calendar"></i>
                <span id="newsToday">Memuat tanggal...</span>
            </div>
        </div>
    </div>

    <div class="news-stats">
        <div class="news-stat"><div class="news-stat-icon"><i class="fa-regular fa-newspaper"></i></div><div><strong id="statTotal">—</strong><small>Berita ditampilkan</small></div></div>
        <div class="news-stat"><div class="news-stat-icon"><i class="fa-solid fa-globe"></i></div><div><strong id="statGlobal">—</strong><small>Berita internasional</small></div></div>
        <div class="news-stat"><div class="news-stat-icon"><i class="fa-solid fa-flag"></i></div><div><strong id="statNational">—</strong><small>Berita nasional</small></div></div>
        <div class="news-stat"><div class="news-stat-icon"><i class="fa-solid fa-rotate"></i></div><div><strong id="statUpdate">Live</strong><small>Status pembaruan</small></div></div>
    </div>

    <div class="news-toolbar">
        <form class="news-search" id="newsSearchForm">
            <input id="newsSearchInput" type="search" placeholder="Cari berita, topik, atau kata kunci..." aria-label="Cari berita">
            <button class="news-btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <button class="news-btn secondary" type="button" id="newsReset"><i class="fa-solid fa-rotate-left"></i> Reset</button>
        </form>
        <div class="news-categories" id="newsCategories">
            <button class="news-cat active" data-category="all">Semua</button>
            <button class="news-cat" data-category="nation">Nasional</button>
            <button class="news-cat" data-category="world">Internasional</button>
            <button class="news-cat" data-category="business">Ekonomi & Bisnis</button>
            <button class="news-cat" data-category="technology">Teknologi</button>
            <button class="news-cat" data-category="science">Sains</button>
            <button class="news-cat" data-category="health">Kesehatan</button>
            <button class="news-cat" data-category="sports">Olahraga</button>
            <button class="news-cat" data-category="entertainment">Hiburan</button>
        </div>
    </div>

    <div class="news-section-title"><h2><i class="fa-solid fa-bolt"></i> Berita Pilihan</h2><span id="newsResultLabel">Berita terbaru</span></div>
    <div class="news-feature" id="newsFeature">
        <div class="news-status"><strong>Memuat berita...</strong>Mohon tunggu sebentar.</div>
    </div>

    <div class="news-section-title"><h2><i class="fa-regular fa-newspaper"></i> Berita Terbaru</h2><span id="newsCountLabel">0 artikel</span></div>
    <div class="news-grid" id="newsGrid"><div class="news-status"><strong>Memuat artikel...</strong>Berita sedang diambil dari sumber API.</div></div>
    <div class="news-pagination">
        <button type="button" id="newsPrev" disabled><i class="fa-solid fa-chevron-left"></i> Sebelumnya</button>
        <span id="newsPageLabel">Halaman 1</span>
        <button type="button" id="newsNext">Selanjutnya <i class="fa-solid fa-chevron-right"></i></button>
    </div>
    <div class="news-footer-note">Berita bersumber dari GNews API. Klik artikel untuk membaca informasi lengkap di situs penerbit.</div>
</div>

<script>
(() => {
    const endpoint = @json(route('berita.api'));
    const state = { category: 'all', query: '', page: 1, loading: false, articles: [], total: 0 };
    const $ = id => document.getElementById(id);
    const feature = $('newsFeature'), grid = $('newsGrid');

    $('newsToday').textContent = new Intl.DateTimeFormat('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' }).format(new Date());

    function safeUrl(value) {
        try { const u = new URL(value, window.location.origin); return ['http:','https:'].includes(u.protocol) ? u.href : ''; }
        catch (_) { return ''; }
    }
    function imageUrl(value) { return safeUrl(value) || ''; }
    function dateText(value) {
        if (!value) return 'Tanggal tidak tersedia';
        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? 'Tanggal tidak tersedia' : new Intl.DateTimeFormat('id-ID',{day:'numeric',month:'short',year:'numeric'}).format(date);
    }
    function shortText(value, max=150) {
        const text = String(value || '').trim();
        return text.length > max ? text.slice(0,max).trim() + '…' : text;
    }
    function articleImage(article) { return imageUrl(article.image) || 'https://placehold.co/800x500/e8edf5/294c80?text=CargoTrace+News'; }
    function articleLink(article) { return safeUrl(article.url); }
    function meta(article) {
        const source = article.source && typeof article.source === 'object' ? article.source.name : article.source;
        return `<div class="news-meta"><span><i class="fa-regular fa-clock"></i> ${dateText(article.publishedAt)}</span>${source ? `<span>• ${escapeHtml(source)}</span>` : ''}</div>`;
    }
    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
    }
    function card(article) {
        const title = escapeHtml(article.title || 'Berita tanpa judul');
        const desc = escapeHtml(shortText(article.description || article.content || 'Buka artikel untuk membaca informasi selengkapnya.', 135));
        const link = articleLink(article), source = article.source && typeof article.source === 'object' ? article.source.name : article.source;
        return `<article class="news-card"><img src="${escapeHtml(articleImage(article))}" alt="" loading="lazy" onerror="this.onerror=null;this.src='https://placehold.co/800x500/e8edf5/294c80?text=CargoTrace+News'"><div class="news-card-body"><span class="news-tag">${escapeHtml(source || 'Berita')}</span><h3>${title}</h3><p>${desc}</p>${meta(article)}${link ? `<a class="news-read" href="${escapeHtml(link)}" target="_blank" rel="noopener noreferrer">Baca selengkapnya <i class="fa-solid fa-arrow-up-right-from-square"></i></a>` : ''}</div></article>`;
    }
    function renderFeature(articles) {
        if (!articles.length) { feature.innerHTML = '<div class="news-status"><strong>Berita pilihan belum tersedia</strong>Coba pilih kategori lain atau gunakan pencarian.</div>'; return; }
        const main = articles[0], side = articles.slice(1,4);
        const link = articleLink(main), source = main.source && typeof main.source === 'object' ? main.source.name : main.source;
        feature.innerHTML = `<article class="news-feature-main"><img class="news-image" src="${escapeHtml(articleImage(main))}" alt="" loading="lazy" onerror="this.onerror=null;this.src='https://placehold.co/800x500/e8edf5/294c80?text=CargoTrace+News'"><div class="news-feature-copy"><span class="news-tag">${escapeHtml(source || 'Berita utama')}</span><h3>${escapeHtml(main.title || 'Berita tanpa judul')}</h3><p>${escapeHtml(shortText(main.description || main.content, 190))}</p>${meta(main)}${link ? `<a class="news-read" href="${escapeHtml(link)}" target="_blank" rel="noopener noreferrer">Baca berita <i class="fa-solid fa-arrow-up-right-from-square"></i></a>` : ''}</div></article><div class="news-feature-side">${side.length ? side.map(a => {const u=articleLink(a);return `<div class="news-side-item"><img src="${escapeHtml(articleImage(a))}" alt="" loading="lazy" onerror="this.onerror=null;this.src='https://placehold.co/200x150/e8edf5/294c80?text=News'"><div><h4>${escapeHtml(a.title || 'Berita tanpa judul')}</h4><small>${dateText(a.publishedAt)}${a.source ? ' • '+escapeHtml(typeof a.source==='object'?a.source.name:a.source):''}</small>${u?`<div><a class="news-read" href="${escapeHtml(u)}" target="_blank" rel="noopener noreferrer">Baca <i class="fa-solid fa-arrow-up-right-from-square"></i></a></div>`:''}</div></div>`}).join('') : '<div class="news-status">Belum ada berita tambahan.</div>'}</div>`;
    }
    function render(articles) {
        state.articles = articles;
        renderFeature(articles);
        const rest = articles.slice(4);
        grid.innerHTML = rest.length ? rest.map(card).join('') : '<div class="news-status"><strong>Belum ada artikel lainnya</strong>Artikel yang tersedia ditampilkan pada bagian berita pilihan.</div>';
        $('newsCountLabel').textContent = `${articles.length} artikel diterima`;
        $('statTotal').textContent = articles.length;
        $('statGlobal').textContent = articles.filter(a => state.category === 'world' || /international|world/i.test(String(a.source?.name || a.source || '')+' '+String(a.title||''))).length;
        $('statNational').textContent = articles.filter(a => /indonesia|nasional|jakarta|sumatera|aceh/i.test(String(a.source?.name || a.source || '')+' '+String(a.title||''))).length;
        $('statUpdate').textContent = 'Aktif';
        $('newsPageLabel').textContent = `Halaman ${state.page}`;
        $('newsPrev').disabled = state.page <= 1;
        $('newsNext').disabled = articles.length < 10;
    }
    function showError(message) {
        feature.innerHTML = `<div class="news-status"><strong>Berita belum dapat dimuat</strong>${escapeHtml(message)}<br><button class="news-btn" type="button" id="newsRetry">Coba lagi</button></div>`;
        grid.innerHTML = '';
        $('newsRetry')?.addEventListener('click', loadNews);
        $('statUpdate').textContent = 'Gangguan';
        $('newsCountLabel').textContent = '0 artikel';
    }
    async function loadNews() {
        if (state.loading) return;
        state.loading = true;
        feature.innerHTML = '<div class="news-status"><strong>Memuat berita...</strong>API sedang mengambil artikel.</div>';
        grid.innerHTML = '<div class="news-status"><strong>Memuat artikel...</strong>Mohon tunggu.</div>';
        $('statUpdate').textContent = 'Memuat';
        const url = new URL(endpoint, window.location.origin);
        url.searchParams.set('category', state.category);
        url.searchParams.set('page', state.page);
        if (state.query) url.searchParams.set('q', state.query);
        try {
            const response = await fetch(url.toString(), { headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } });
            const data = await response.json();
            if (!response.ok || data.error) throw new Error(data.message || data.error || `HTTP ${response.status}`);
            const articles = Array.isArray(data.articles) ? data.articles : (Array.isArray(data.data) ? data.data : []);
            if (!articles.length) {
                feature.innerHTML = '<div class="news-status"><strong>Berita tidak ditemukan</strong>Coba kata kunci lain atau pilih kategori Semua.</div>';
                grid.innerHTML = '';
                $('statTotal').textContent = '0'; $('statGlobal').textContent = '0'; $('statNational').textContent = '0'; $('statUpdate').textContent = 'Aktif';
                $('newsCountLabel').textContent = '0 artikel'; $('newsPageLabel').textContent = `Halaman ${state.page}`;
                $('newsPrev').disabled = state.page <= 1; $('newsNext').disabled = true;
            } else render(articles);
        } catch (error) {
            showError(error.message || 'Periksa koneksi dan konfigurasi API berita pada server.');
        } finally { state.loading = false; }
    }
    $('newsSearchForm').addEventListener('submit', event => { event.preventDefault(); state.query = $('newsSearchInput').value.trim(); state.page = 1; loadNews(); });
    $('newsReset').addEventListener('click', () => { state.query=''; state.category='all'; state.page=1; $('newsSearchInput').value=''; document.querySelectorAll('.news-cat').forEach(b=>b.classList.toggle('active',b.dataset.category==='all')); loadNews(); });
    $('newsCategories').addEventListener('click', event => {
        const button = event.target.closest('[data-category]');
        if (!button) return;

        // Pilih kategori baru dan mulai dari halaman pertama.
        state.category = button.dataset.category;
        state.page = 1;

        // Hapus kata pencarian lama agar kategori tidak tetap mencari "jerman".
        state.query = '';
        $('newsSearchInput').value = '';

        document.querySelectorAll('.news-cat').forEach(b => {
            b.classList.toggle('active', b === button);
        });

        loadNews();
    });
    $('newsPrev').addEventListener('click', () => { if(state.page>1){state.page--;loadNews();} });
    $('newsNext').addEventListener('click', () => { if(state.articles.length>=10){state.page++;loadNews();} });
    loadNews();
})();
</script>
</body>
</html>
