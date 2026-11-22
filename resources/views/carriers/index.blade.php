<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Perusahaan Pengiriman | CargoTrace</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--navy:#061533;--blue:#2563eb;--ink:#12306a;--mut:#6c83ab;--line:#e0e9fa;--bg:#edf3ff}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--ink);font-size:13px}
a{color:inherit;text-decoration:none}
button,input,select,textarea{font-family:inherit}
:focus-visible{outline:2px solid var(--blue);outline-offset:2px}

/* ===== SIDEBAR ===== */
.sidebar{position:fixed;inset:0 auto 0 0;width:272px;padding:26px 18px 18px;display:flex;flex-direction:column;background:linear-gradient(180deg,#061533 0%,#0a2257 65%,#0f3a8f 100%);color:#fff;z-index:1000;overflow-y:auto}
.sidebar::before{content:"";position:absolute;width:300px;height:300px;top:-140px;right:-150px;border-radius:50%;background:radial-gradient(#38bdf860,#0000 70%);pointer-events:none}
.brand{display:flex;align-items:center;gap:11px;padding:0 8px;position:relative}
.brand-logo{display:grid;place-items:center;width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-size:20px;box-shadow:0 8px 22px #2563eb77}
.brand-name{font-weight:800;font-size:22px;letter-spacing:-.5px}
.brand-sub{margin:8px 8px 26px;font-size:9.5px;letter-spacing:.8px;color:#9fbcea;position:relative}
.s-title{margin:0 12px 10px;font-size:10px;font-weight:700;letter-spacing:1.3px;color:#6f95d6}
.s-wrap{position:relative;margin-bottom:22px}
.s-menu{display:flex;flex-direction:column;gap:4px;position:relative}
.s-link{display:flex;align-items:center;gap:13px;min-height:44px;padding:10px 14px;border-radius:13px;color:#dce8ff;font-weight:500;transition:.2s}
.s-link i{width:18px;text-align:center;font-size:14px;color:#8fb3ec}
.s-link:hover{background:#ffffff18}
.s-link.active{background:linear-gradient(90deg,#2563eb,#3b8bf5);color:#fff;font-weight:700;box-shadow:0 10px 24px #2563eb55}
.s-link.active i{color:#fff}
.s-user{margin-top:auto;position:relative;padding:14px;border-radius:16px;background:#ffffff14;border:1px solid #ffffff2b}
.s-user .who{display:flex;align-items:center;gap:11px}
.avatar{display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-weight:800}
.s-user b{display:block;font-size:12.5px}.s-user small{font-size:10px;color:#a9c4f0}
.s-user button{width:100%;margin-top:12px;padding:9px;border:1px solid #ffffff33;border-radius:10px;background:transparent;color:#dce8ff;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.s-user button:hover{background:#ffffff1c}

/* ===== MAIN ===== */
.main{margin-left:272px;min-height:100vh;background:radial-gradient(circle at 100% 0,#cfe3ff 0,#0000 380px),radial-gradient(circle at 0 100%,#d7e8ff 0,#0000 340px),var(--bg)}
.content{padding:26px 32px 40px}
.alert{padding:13px 17px;margin-bottom:16px;border-radius:12px;font-size:12px}
.alert-success{background:#e5f7ed;color:#187344}
.alert-error{background:#ffebeb;color:#c92f3a}

/* HERO */
.hero{position:relative;overflow:hidden;padding:38px 42px;border-radius:30px;color:#fff;display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap;margin-bottom:22px;
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 55%,#1d6fe870 100%),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1800&q=80') center/cover,#0b2a6b;box-shadow:0 26px 54px #0b2a6b46}
.hero>*{position:relative;z-index:2}
.crumb{font-size:12px;color:#cfe0ff;margin-bottom:10px}
.hero h1{font-weight:800;font-size:38px;letter-spacing:-1px;line-height:1.15}
.hero p{margin-top:14px;max-width:560px;line-height:1.8;color:#dbe9ff;font-size:14px}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap}
.hbtn{display:inline-flex;align-items:center;gap:9px;padding:15px 22px;border:0;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;color:#fff;background:linear-gradient(135deg,#3b8bf5,#1d6fe8);box-shadow:0 10px 24px #1d6fe855}
.hbtn:hover{filter:brightness(1.08)}

/* STATISTIK */
.cx-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:22px}
.cx-stat{position:relative;display:block;padding:24px 22px;background:#fff;border:1px solid #e9f0fb;border-radius:24px;box-shadow:0 10px 28px #2563eb0f;transition:.25s}
a.cx-stat:hover{transform:translateY(-4px);border-color:#b9d2f7}
.cx-ico{display:grid;place-items:center;width:56px;height:56px;border-radius:16px;font-size:22px;margin-bottom:18px}
.cx-stat b{display:block;font-weight:800;font-size:32px;color:var(--navy);line-height:1}
.cx-stat strong{display:block;margin-top:9px;font-size:14px;font-weight:700;color:var(--navy)}
.cx-stat small{display:block;margin-top:4px;font-size:12px;color:#8ca6c4}
.cx-go{position:absolute;right:14px;top:50%;transform:translateY(-50%);display:grid;place-items:center;width:26px;height:26px;border-radius:50%;background:#fff;border:1px solid #e0e9fa;color:#8ca6c4;font-size:10px}

/* PANEL */
.panel{padding:30px;background:#fff;border:1px solid var(--line);border-radius:30px;box-shadow:0 14px 36px #2563eb0f}
.ph{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
.ph-left{display:flex;align-items:center;gap:14px}
.ph-ico{display:grid;place-items:center;width:46px;height:46px;border-radius:14px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;font-size:17px;box-shadow:0 8px 18px #2563eb44}
.ph h2{font-weight:800;font-size:22px;color:var(--navy);letter-spacing:-.4px}
.ph-sub{margin:10px 0 20px;font-size:13px;color:var(--mut)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:14px 20px;background:linear-gradient(135deg,#3b8bf5,#1d4ed8);color:#fff;border:0;border-radius:14px;cursor:pointer;font-weight:700;font-size:13px;white-space:nowrap;box-shadow:0 10px 22px #2563eb40;transition:.2s}
.btn:hover{filter:brightness(1.08)}
.btn-light{background:#e8f1ff;color:#1d4ed8;box-shadow:none}
.filters{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:22px}
.filters input,.filters select{padding:14px 16px;border:1px solid #dce8f8;border-radius:14px;background:#fff;color:var(--ink);font-size:13px;outline:none}
.filters input{flex:1;min-width:240px}
.filters input:focus,.filters select:focus,.f input:focus,.f select:focus,.f textarea:focus{border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}

/* TABEL */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:separate;border-spacing:0;min-width:900px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:12px;font-weight:700;padding:15px 16px;white-space:nowrap}
th:first-child{border-radius:14px 0 0 14px}th:last-child{border-radius:0 14px 14px 0}
td{padding:16px;border-bottom:1px solid #edf3fa;color:#41608a;vertical-align:middle;font-size:13px}
tbody tr.row{cursor:pointer;transition:.15s}
tbody tr.row:hover{background:#f6faff}
.c-info{display:flex;align-items:center;gap:13px;min-width:210px}
.c-logo{width:44px;height:44px;border-radius:12px;object-fit:contain;background:#f2f6fc;padding:5px;border:1px solid #e3ecf9}
.c-ph{display:grid;place-items:center;width:44px;height:44px;border-radius:12px;background:#e8f1ff;color:#2563eb;font-weight:800;font-size:17px}
.c-name{font-weight:700;color:var(--navy)}
.c-sub{margin-top:4px;font-size:11.5px;color:#8ca6c4}
.code{font-weight:800;color:#2563eb}
.tag{display:inline-block;padding:5px 11px;border-radius:9px;background:#eef4ff;color:#3d68a8;font-size:11.5px;font-weight:600}
.status{display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.st-ACTIVE{background:#e8f7ef;color:#26734d}
.st-INACTIVE{background:#ffe9e9;color:#c92f3a}
.acts{display:flex;gap:7px}
.acts form{margin:0}
.ab{display:grid;place-items:center;width:36px;height:36px;border:0;border-radius:11px;cursor:pointer;font-size:13px;transition:.15s}
.ab:hover{filter:brightness(.95);transform:translateY(-1px)}
.ab-d{background:#e8f1ff;color:#1d4ed8}.ab-e{background:#fff4dc;color:#b77900}.ab-x{background:#ffe9e9;color:#c92f3a}
td.empty{text-align:center;padding:42px;color:#8ca6c4}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-top:20px;font-size:12px;color:#8ca6c4}
.pager nav svg{width:16px;height:16px}

/* MODAL */
.modal{display:none;position:fixed;inset:0;background:rgba(6,21,51,.6);backdrop-filter:blur(3px);z-index:9999;align-items:center;justify-content:center;padding:18px}
.modal.show{display:flex}
.mbox{background:#fff;width:100%;max-width:760px;max-height:92vh;overflow-y:auto;border-radius:26px;box-shadow:0 24px 70px rgba(6,21,51,.35)}
.mhead{position:sticky;top:0;z-index:2;display:flex;justify-content:space-between;align-items:center;gap:12px;padding:22px 28px;background:linear-gradient(100deg,#061533,#0b2a6b 70%,#1747b8);color:#fff}
.mhead h3{font-weight:800;font-size:18px}
.mclose{border:0;background:#ffffff26;color:#fff;border-radius:10px;width:34px;height:34px;font-size:20px;cursor:pointer}
.mbody{padding:26px 28px}
.mfoot{display:flex;justify-content:flex-end;gap:10px;padding:18px 28px;border-top:1px solid #edf3fa}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.f label{display:block;font-size:12px;font-weight:700;color:#4a6694;margin-bottom:8px}
.f input,.f select,.f textarea{width:100%;padding:13px 14px;border:1px solid #dce8f8;border-radius:12px;font-size:13px;color:var(--ink);background:#fff;outline:none}
.f textarea{min-height:90px;resize:vertical}
.f.full{grid-column:1/-1}
.f small{display:block;margin-top:7px;font-size:11px;color:#8ca6c4}
.d-head{display:flex;align-items:center;gap:16px;padding-bottom:20px;margin-bottom:20px;border-bottom:1px solid #edf3fa}
.d-head .c-logo,.d-head .c-ph{width:64px;height:64px;border-radius:16px;font-size:24px}
.d-head h4{font-weight:800;font-size:19px;color:var(--navy)}
.d-head p{margin-top:5px;font-size:12px;color:#8ca6c4}
.d-item label{display:block;font-size:11px;font-weight:700;color:#8ca6c4;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.d-item strong{font-size:13.5px;color:var(--navy);overflow-wrap:anywhere;font-weight:600;line-height:1.6}
.d-item.full{grid-column:1/-1}

/* RESPONSIVE */
@media(max-width:1350px){.cx-stats{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:1000px){.content{padding:20px}.cx-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.hero{padding:30px 26px}.hero h1{font-size:30px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}.panel{padding:20px}
}
@media(max-width:600px){.cx-stats{grid-template-columns:1fr}.grid2{grid-template-columns:1fr}.f.full,.d-item.full{grid-column:auto}.mbody,.mhead,.mfoot{padding-left:18px;padding-right:18px}.hero h1{font-size:26px}}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="brand">
        <div class="brand-logo"><i class="fa-solid fa-route"></i></div>
        <div class="brand-name">CargoTrace</div>
    </div>
    <div class="brand-sub">LOGISTICS &amp; SHIPMENT TRACKING</div>

    <div class="s-wrap">
        <div class="s-title">MENU NAVIGASI</div>
        <nav class="s-menu">
            <a href="{{ route('dashboard') }}" class="s-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-border-all"></i><span>Dashboard</span></a>
            <a href="{{ route('pengiriman.index') }}" class="s-link {{ request()->routeIs('pengiriman.*') ? 'active' : '' }}"><i class="fa-solid fa-box"></i><span>Pengiriman</span></a>
            <a href="{{ route('pelacakan.index') }}" class="s-link {{ request()->routeIs('pelacakan.*') ? 'active' : '' }}"><i class="fa-solid fa-location-dot"></i><span>Pelacakan</span></a>
            <a href="{{ route('exceptions.index') }}" class="s-link {{ request()->routeIs('exceptions.*') ? 'active' : '' }}"><i class="fa-solid fa-triangle-exclamation"></i><span>Pengecualian</span></a>
            <a href="{{ route('carriers.index') }}" class="s-link {{ request()->routeIs('carriers.*') ? 'active' : '' }}"><i class="fa-solid fa-building"></i><span>Perusahaan Pengiriman</span></a>
            <a href="{{ route('fasilitas.index') }}" class="s-link {{ request()->routeIs('fasilitas.*') ? 'active' : '' }}"><i class="fa-solid fa-warehouse"></i><span>Lokasi &amp; Fasilitas</span></a>
            <a href="{{ route('users.index') }}" class="s-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="fa-solid fa-users-gear"></i><span>Pengguna &amp; Hak Akses</span></a>
            <a href="{{ route('notifications.index') }}" class="s-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"><i class="fa-solid fa-bell"></i><span>Notifikasi</span></a>
            <a href="{{ route('audit-logs.index') }}" class="s-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-list"></i><span>Audit Log</span></a>
        </nav>
    </div>

    <div class="s-user">
        <div class="who">
            <div class="avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}</div>
            <div><b>{{ auth()->user()?->name ?? 'Admin CargoTrace' }}</b><small>Shipment Tracking System</small></div>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Keluar</button>
        </form>
    </div>
</aside>

<main class="main">
<section class="content">

    @if(session('success'))<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-error">
            <strong>Periksa kembali data yang dimasukkan:</strong>
            <ul style="margin:7px 0 0 18px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <!-- HERO -->
    <section class="hero">
        <div>
            <div class="crumb">Dashboard / Perusahaan Pengiriman</div>
            <h1>Pengelolaan Perusahaan Pengiriman</h1>
            <p>Kelola data perusahaan ekspedisi, jenis layanan, kontak, dan status kerja sama dalam satu halaman.</p>
        </div>
        <div class="hero-actions">
            <button type="button" class="hbtn" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> Tambah Perusahaan</button>
        </div>
    </section>

    <!-- STATISTIK -->
    <section class="cx-stats">
        <a href="{{ route('carriers.index') }}" class="cx-stat">
            <div class="cx-ico" style="background:#eef2f8;color:#64748b"><i class="fa-solid fa-building"></i></div>
            <b>{{ number_format($totalCarriers) }}</b><strong>Total</strong><small>Seluruh perusahaan terdaftar.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('carriers.index', ['status' => 'ACTIVE']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#e6f7ee;color:#16a34a"><i class="fa-solid fa-circle-check"></i></div>
            <b>{{ number_format($activeCarriers) }}</b><strong>Aktif</strong><small>Perusahaan sedang bekerja sama.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('carriers.index', ['status' => 'INACTIVE']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#ffebeb;color:#e5484d"><i class="fa-solid fa-circle-pause"></i></div>
            <b>{{ number_format($inactiveCarriers) }}</b><strong>Tidak Aktif</strong><small>Kerja sama dihentikan sementara.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <div class="cx-stat">
            <div class="cx-ico" style="background:#fff3dc;color:#f59e0b"><i class="fa-solid fa-truck-fast"></i></div>
            <b>{{ number_format($carriersInUse) }}</b><strong>Digunakan</strong><small>Sudah punya data pengiriman.</small>
        </div>
        <div class="cx-stat">
            <div class="cx-ico" style="background:#efe9ff;color:#7c3aed"><i class="fa-solid fa-box-open"></i></div>
            <b>{{ number_format(max($totalCarriers - $carriersInUse, 0)) }}</b><strong>Belum Dipakai</strong><small>Belum punya pengiriman.</small>
        </div>
    </section>

    <!-- DAFTAR -->
    <section class="panel">
        <div class="ph">
            <div class="ph-left">
                <div class="ph-ico"><i class="fa-solid fa-list"></i></div>
                <h2>Daftar Perusahaan</h2>
            </div>
            <button type="button" class="btn" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> Tambah</button>
        </div>
        <p class="ph-sub">{{ $carriers->total() }} perusahaan tersimpan. Klik baris untuk melihat detail lengkap.</p>

        <form action="{{ route('carriers.index') }}" method="GET" class="filters">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, telepon, atau email..." aria-label="Pencarian">
            <select name="service_type" aria-label="Jenis layanan">
                <option value="">Semua layanan</option>
                @foreach($serviceTypes as $service)
                    <option value="{{ $service }}" @selected(request('service_type') === $service)>{{ $service }}</option>
                @endforeach
            </select>
            <select name="status" aria-label="Status">
                <option value="">Semua status</option>
                <option value="ACTIVE" @selected(request('status') === 'ACTIVE')>Aktif</option>
                <option value="INACTIVE" @selected(request('status') === 'INACTIVE')>Tidak Aktif</option>
            </select>
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a href="{{ route('carriers.index') }}" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Perusahaan</th>
                        <th>Kode</th>
                        <th>Layanan</th>
                        <th>Kontak</th>
                        <th>Pengiriman</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($carriers as $carrier)
                    @php
                        $logoUrl = $carrier->logo ? asset('storage/' . $carrier->logo) : '';
                        $json = json_encode(array_merge($carrier->toArray(), [
                            'logo_url' => $logoUrl,
                            'shipments_count' => $carrier->shipments_count ?? 0,
                        ]), JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_TAG);
                    @endphp
                    <tr class="row" data-carrier="{{ $json }}" onclick="showDetail(this)">
                        <td>
                            <div class="c-info">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="Logo {{ $carrier->name }}" class="c-logo">
                                @else
                                    <div class="c-ph">{{ strtoupper(substr($carrier->name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div class="c-name">{{ $carrier->name }}</div>
                                    <div class="c-sub">{{ $carrier->address ? \Illuminate\Support\Str::limit($carrier->address, 34) : 'Alamat belum diisi' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="code">{{ $carrier->code }}</td>
                        <td><span class="tag">{{ $carrier->service_type }}</span></td>
                        <td>
                            <div>{{ $carrier->phone ?: '-' }}</div>
                            <div class="c-sub">{{ $carrier->email ?: '-' }}</div>
                        </td>
                        <td>
                            <div class="c-name">{{ number_format($carrier->shipments_count ?? 0) }}</div>
                            <div class="c-sub">shipment</div>
                        </td>
                        <td><span class="status st-{{ $carrier->status }}">{{ $carrier->status === 'ACTIVE' ? 'AKTIF' : 'TIDAK AKTIF' }}</span></td>
                        <td onclick="event.stopPropagation()">
                            <div class="acts">
                                <button type="button" class="ab ab-d" title="Detail" data-carrier="{{ $json }}" onclick="showDetail(this)"><i class="fa-solid fa-eye"></i></button>
                                <button type="button" class="ab ab-e" title="Edit" data-carrier="{{ $json }}" onclick="editCarrier(this)"><i class="fa-solid fa-pen"></i></button>
                                <form action="{{ route('carriers.destroy', $carrier->id) }}" method="POST" data-name="{{ $carrier->name }}" onsubmit="return confirmDelete(this)">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ab ab-x" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada perusahaan yang cocok. Ubah filter atau tambah perusahaan baru.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            <span>Menampilkan {{ $carriers->firstItem() ?? 0 }}–{{ $carriers->lastItem() ?? 0 }} dari {{ $carriers->total() }} perusahaan</span>
            @if($carriers->hasPages())<div>{{ $carriers->links() }}</div>@endif
        </div>
    </section>

</section>
</main>

<!-- MODAL TAMBAH / EDIT -->
<div class="modal" id="carrierModal">
    <div class="mbox">
        <div class="mhead">
            <h3 id="modalTitle">Tambah Perusahaan Pengiriman</h3>
            <button type="button" class="mclose" onclick="closeModal('carrierModal')">&times;</button>
        </div>
        <form method="POST" id="carrierForm" action="{{ route('carriers.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="_mode" id="formMode" value="create">
            <input type="hidden" name="_id" id="formId" value="">
            <div class="mbody">
                <div class="grid2">
                    <div class="f"><label>Nama Perusahaan *</label><input name="name" id="f_name" placeholder="Contoh: JNE Express" required></div>
                    <div class="f"><label>Kode Perusahaan *</label><input name="code" id="f_code" placeholder="Contoh: JNE" required></div>
                    <div class="f"><label>Jenis Layanan *</label>
                        <select name="service_type" id="f_service_type" required>
                            <option value="">Pilih Jenis Layanan</option>
                            <option value="Darat">Pengiriman Darat</option>
                            <option value="Laut">Pengiriman Laut</option>
                            <option value="Udara">Pengiriman Udara</option>
                            <option value="Multimoda">Multimoda</option>
                            <option value="Ekspres">Ekspres</option>
                            <option value="Reguler">Reguler</option>
                        </select>
                    </div>
                    <div class="f"><label>Status *</label>
                        <select name="status" id="f_status" required>
                            <option value="ACTIVE">Aktif</option>
                            <option value="INACTIVE">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="f"><label>Nomor Telepon</label><input name="phone" id="f_phone" placeholder="Nomor telepon perusahaan"></div>
                    <div class="f"><label>Email Perusahaan</label><input type="email" name="email" id="f_email" placeholder="email@perusahaan.com"></div>
                    <div class="f full"><label>Alamat Perusahaan</label><textarea name="address" id="f_address" placeholder="Masukkan alamat perusahaan"></textarea></div>
                    <div class="f full"><label>Deskripsi Perusahaan</label><textarea name="description" id="f_description" placeholder="Informasi singkat tentang perusahaan"></textarea></div>
                    <div class="f full"><label>Logo Perusahaan</label>
                        <input type="file" name="logo" id="f_logo" accept=".jpg,.jpeg,.png,.webp">
                        <small id="logoHint">Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</small>
                    </div>
                </div>
            </div>
            <div class="mfoot">
                <button type="button" class="btn btn-light" onclick="closeModal('carrierModal')">Batal</button>
                <button type="submit" class="btn" id="submitButton">Simpan Perusahaan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal" id="detailModal">
    <div class="mbox">
        <div class="mhead">
            <h3>Detail Perusahaan</h3>
            <button type="button" class="mclose" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="mbody">
            <div class="d-head">
                <div id="detailLogo"></div>
                <div><h4 id="detailName">-</h4><p id="detailCode">Kode: -</p></div>
            </div>
            <div class="grid2" id="detailContent"></div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-light" onclick="closeModal('detailModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
const storeUrl = @json(route('carriers.store'));
const updateUrlTemplate = @json(route('carriers.update', ['perusahaan_pengiriman' => '__ID__']));
const fieldIds = ['name','code','service_type','status','phone','email','address','description'];

function openModal(id){document.getElementById(id).classList.add('show')}
function closeModal(id){document.getElementById(id).classList.remove('show')}

document.querySelectorAll('.modal').forEach(function(m){
    m.addEventListener('click',function(e){ if(e.target===m) m.classList.remove('show'); });
});
document.addEventListener('keydown',function(e){
    if(e.key==='Escape') document.querySelectorAll('.modal.show').forEach(function(m){m.classList.remove('show')});
});

function readCarrier(el){
    try{return JSON.parse(el.dataset.carrier)}
    catch(e){alert('Data perusahaan tidak dapat dibaca.');return null}
}

function setMode(mode,id){
    const form=document.getElementById('carrierForm');
    document.getElementById('formMode').value=mode;
    document.getElementById('formId').value=id||'';
    const hint=document.getElementById('logoHint');
    if(mode==='edit'){
        form.action=updateUrlTemplate.replace('__ID__',id);
        document.getElementById('formMethod').value='PUT';
        document.getElementById('modalTitle').textContent='Edit Perusahaan Pengiriman';
        document.getElementById('submitButton').textContent='Simpan Perubahan';
        hint.textContent='Kosongkan jika tidak ingin mengganti logo. Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.';
    }else{
        form.action=storeUrl;
        document.getElementById('formMethod').value='POST';
        document.getElementById('modalTitle').textContent='Tambah Perusahaan Pengiriman';
        document.getElementById('submitButton').textContent='Simpan Perusahaan';
        hint.textContent='Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.';
    }
}

function fillForm(data){
    fieldIds.forEach(function(f){ document.getElementById('f_'+f).value=(data&&data[f])??''; });
}

function openCreateModal(){
    document.getElementById('carrierForm').reset();
    setMode('create');
    fillForm({status:'ACTIVE'});
    openModal('carrierModal');
}

function editCarrier(btn){
    const c=readCarrier(btn); if(!c)return;
    document.getElementById('carrierForm').reset();
    setMode('edit',c.id);
    fillForm(c);
    openModal('carrierModal');
}

function showDetail(el){
    const c=readCarrier(el); if(!c)return;
    document.getElementById('detailName').textContent=c.name||'-';
    document.getElementById('detailCode').textContent='Kode: '+(c.code||'-');

    const logo=document.getElementById('detailLogo');
    logo.replaceChildren();
    if(c.logo_url){
        const img=document.createElement('img');
        img.src=c.logo_url;img.alt='Logo perusahaan';img.className='c-logo';
        logo.appendChild(img);
    }else{
        const ph=document.createElement('div');
        ph.className='c-ph';
        ph.textContent=(c.name||'P').charAt(0).toUpperCase();
        logo.appendChild(ph);
    }

    const items=[
        ['Jenis Layanan',c.service_type],
        ['Status',c.status==='ACTIVE'?'AKTIF':'TIDAK AKTIF'],
        ['Nomor Telepon',c.phone],
        ['Email',c.email],
        ['Total Pengiriman',c.shipments_count],
        ['Alamat Perusahaan',c.address,true],
        ['Deskripsi',c.description,true]
    ];
    const box=document.getElementById('detailContent');
    box.replaceChildren();
    items.forEach(function(it){
        const d=document.createElement('div');
        d.className='d-item'+(it[2]?' full':'');
        const l=document.createElement('label'); l.textContent=it[0];
        const s=document.createElement('strong');
        s.textContent=(it[1]===null||it[1]===undefined||it[1]==='')?'-':it[1];
        d.append(l,s); box.appendChild(d);
    });
    openModal('detailModal');
}

function confirmDelete(form){
    return confirm('Yakin ingin menghapus perusahaan '+(form.dataset.name||'ini')+'?');
}

@if($errors->any() && old('_mode'))
(function(){
    const old=@json(old());
    document.getElementById('carrierForm').reset();
    setMode(old._mode==='edit'?'edit':'create',old._id);
    fillForm(old);
    openModal('carrierModal');
})();
@endif
</script>
</body>
</html>
