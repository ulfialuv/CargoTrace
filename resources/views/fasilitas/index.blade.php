<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Lokasi &amp; Fasilitas | CargoTrace</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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

.hero{position:relative;overflow:hidden;padding:38px 42px;border-radius:30px;color:#fff;display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap;margin-bottom:22px;
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 55%,#1d6fe870 100%),url('https://images.unsplash.com/photo-1494412519320-aa613dfb7738?auto=format&fit=crop&w=1800&q=80') center/cover,#0b2a6b;box-shadow:0 26px 54px #0b2a6b46}
.hero>*{position:relative;z-index:2}
.crumb{font-size:12px;color:#cfe0ff;margin-bottom:10px}
.hero h1{font-weight:800;font-size:38px;letter-spacing:-1px;line-height:1.15}
.hero p{margin-top:14px;max-width:560px;line-height:1.8;color:#dbe9ff;font-size:14px}
.hbtn{display:inline-flex;align-items:center;gap:9px;padding:15px 22px;border:0;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;color:#fff;background:linear-gradient(135deg,#3b8bf5,#1d6fe8);box-shadow:0 10px 24px #1d6fe855}
.hbtn:hover{filter:brightness(1.08)}

.cx-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:22px}
.cx-stat{position:relative;display:block;padding:24px 22px;background:#fff;border:1px solid #e9f0fb;border-radius:24px;box-shadow:0 10px 28px #2563eb0f;transition:.25s}
a.cx-stat:hover{transform:translateY(-4px);border-color:#b9d2f7}
.cx-ico{display:grid;place-items:center;width:56px;height:56px;border-radius:16px;font-size:22px;margin-bottom:18px}
.cx-stat b{display:block;font-weight:800;font-size:32px;color:var(--navy);line-height:1}
.cx-stat strong{display:block;margin-top:9px;font-size:14px;font-weight:700;color:var(--navy)}
.cx-stat small{display:block;margin-top:4px;font-size:12px;color:#8ca6c4}
.cx-go{position:absolute;right:14px;top:50%;transform:translateY(-50%);display:grid;place-items:center;width:26px;height:26px;border-radius:50%;background:#fff;border:1px solid #e0e9fa;color:#8ca6c4;font-size:10px}

.panel{padding:30px;background:#fff;border:1px solid var(--line);border-radius:30px;box-shadow:0 14px 36px #2563eb0f;margin-bottom:22px}
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

.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:separate;border-spacing:0;min-width:880px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:12px;font-weight:700;padding:15px 16px;white-space:nowrap}
th:first-child{border-radius:14px 0 0 14px}th:last-child{border-radius:0 14px 14px 0}
td{padding:16px;border-bottom:1px solid #edf3fa;color:#41608a;vertical-align:middle;font-size:13px}
tbody tr.row{cursor:pointer;transition:.15s}
tbody tr.row:hover{background:#f6faff}
.f-info{display:flex;align-items:center;gap:13px;min-width:220px}
.f-ico{display:grid;place-items:center;flex:none;width:44px;height:44px;border-radius:12px;font-size:17px}
.f-name{font-weight:700;color:var(--navy)}
.f-sub{margin-top:4px;font-size:11.5px;color:#8ca6c4}
.tag{display:inline-block;padding:6px 12px;border-radius:9px;font-size:11.5px;font-weight:700;white-space:nowrap}
.t-PORT{background:#e8f0ff;color:#315bba}.t-AIRPORT{background:#f0eaff;color:#8054c7}.t-DEPOT{background:#fff3df;color:#b5791b}.t-WAREHOUSE{background:#e5f8ef;color:#168354}
.ic-PORT{background:#e8f0ff;color:#315bba}.ic-AIRPORT{background:#f0eaff;color:#8054c7}.ic-DEPOT{background:#fff3df;color:#b5791b}.ic-WAREHOUSE{background:#e5f8ef;color:#168354}
.status{display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap}
.status::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}
.st-ACTIVE{background:#e8f7ef;color:#26734d}
.st-INACTIVE{background:#eef1f6;color:#7b879a}
.acts{display:flex;gap:7px}
.acts form{margin:0}
.ab{display:grid;place-items:center;width:36px;height:36px;border:0;border-radius:11px;cursor:pointer;font-size:13px;transition:.15s}
.ab:hover{filter:brightness(.95);transform:translateY(-1px)}
.ab-d{background:#e8f1ff;color:#1d4ed8}.ab-e{background:#fff4dc;color:#b77900}.ab-x{background:#ffe9e9;color:#c92f3a}
td.empty{text-align:center;padding:42px;color:#8ca6c4}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-top:20px;font-size:12px;color:#8ca6c4}
.pager nav svg{width:16px;height:16px}

/* PETA */
.map-box{position:relative;border-radius:20px;overflow:hidden;border:1px solid var(--line)}
#facilityMap{height:460px;width:100%;background:#dfeafb;z-index:1}
.legend{display:flex;flex-wrap:wrap;gap:16px;margin-top:16px}
.legend span{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--mut);font-weight:600}
.legend i{width:11px;height:11px;border-radius:50%}
.map-note{margin-top:12px;font-size:12px;color:#8ca6c4}
.pin{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;border:3px solid #fff;color:#fff;font-size:13px;box-shadow:0 4px 14px #0005}
.leaflet-popup-content-wrapper{border-radius:14px}
.pop{min-width:190px;font:12px/1.7 'Plus Jakarta Sans',sans-serif;color:#365775}.pop strong{display:block;font-size:13px;color:var(--navy)}.pop small{color:#8ca6c4}
.pop button{margin-top:6px;border:0;background:none;color:var(--blue);font:700 12px 'Plus Jakarta Sans',sans-serif;cursor:pointer;padding:0}

/* MODAL */
.modal{display:none;position:fixed;inset:0;background:rgba(6,21,51,.6);backdrop-filter:blur(3px);z-index:9999;align-items:center;justify-content:center;padding:18px}
.modal.show{display:flex}
.mbox{background:#fff;width:100%;max-width:800px;max-height:92vh;overflow-y:auto;border-radius:26px;box-shadow:0 24px 70px rgba(6,21,51,.35)}
.mhead{position:sticky;top:0;z-index:2;display:flex;justify-content:space-between;align-items:center;gap:12px;padding:22px 28px;background:linear-gradient(100deg,#061533,#0b2a6b 70%,#1747b8);color:#fff}
.mhead h3{font-weight:800;font-size:18px}
.mclose{border:0;background:#ffffff26;color:#fff;border-radius:10px;width:34px;height:34px;font-size:20px;cursor:pointer}
.mbody{padding:26px 28px}
.mfoot{display:flex;justify-content:flex-end;gap:10px;padding:18px 28px;border-top:1px solid #edf3fa;flex-wrap:wrap}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.f label{display:block;font-size:12px;font-weight:700;color:#4a6694;margin-bottom:8px}
.f input,.f select,.f textarea{width:100%;padding:13px 14px;border:1px solid #dce8f8;border-radius:12px;font-size:13px;color:var(--ink);background:#fff;outline:none}
.f textarea{min-height:90px;resize:vertical}
.f.full{grid-column:1/-1}
.f small{display:block;margin-top:7px;font-size:11px;color:#8ca6c4}
.d-head{display:flex;align-items:center;gap:16px;padding-bottom:20px;margin-bottom:20px;border-bottom:1px solid #edf3fa}
.d-head .f-ico{width:64px;height:64px;border-radius:16px;font-size:24px}
.d-head h4{font-weight:800;font-size:19px;color:var(--navy)}
.d-head p{margin-top:5px;font-size:12px;color:#8ca6c4}
.d-item label{display:block;font-size:11px;font-weight:700;color:#8ca6c4;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.d-item strong{font-size:13.5px;color:var(--navy);overflow-wrap:anywhere;font-weight:600;line-height:1.6}
.d-item.full{grid-column:1/-1}

@media(max-width:1350px){.cx-stats{grid-template-columns:repeat(3,minmax(0,1fr))}}
@media(max-width:1000px){.content{padding:20px}.cx-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.hero{padding:30px 26px}.hero h1{font-size:30px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}.panel{padding:20px}#facilityMap{height:340px}
}
@media(max-width:600px){.cx-stats{grid-template-columns:1fr}.grid2{grid-template-columns:1fr}.f.full,.d-item.full{grid-column:auto}.mbody,.mhead,.mfoot{padding-left:18px;padding-right:18px}.hero h1{font-size:26px}}
</style>
</head>
<body>

@php
    $typeLabels = ['PORT' => 'Pelabuhan', 'AIRPORT' => 'Bandara', 'DEPOT' => 'Depo', 'WAREHOUSE' => 'Gudang'];
    $typeIcons  = ['PORT' => 'fa-anchor', 'AIRPORT' => 'fa-plane', 'DEPOT' => 'fa-boxes-stacked', 'WAREHOUSE' => 'fa-warehouse'];
@endphp

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
            <div class="crumb">Dashboard / Lokasi &amp; Fasilitas</div>
            <h1>Pengelolaan Lokasi &amp; Fasilitas</h1>
            <p>Kelola pelabuhan, bandara, depo, dan gudang operasional CargoTrace lengkap dengan peta lokasinya.</p>
        </div>
        <button type="button" class="hbtn" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> Tambah Fasilitas</button>
    </section>

    <!-- STATISTIK -->
    <section class="cx-stats">
        <a href="{{ route('fasilitas.index') }}" class="cx-stat">
            <div class="cx-ico" style="background:#eef2f8;color:#64748b"><i class="fa-solid fa-building"></i></div>
            <b>{{ number_format($totalFasilitas) }}</b><strong>Total</strong><small>Seluruh fasilitas terdaftar.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('fasilitas.index', ['type' => 'PORT']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#e8f0ff;color:#315bba"><i class="fa-solid fa-anchor"></i></div>
            <b>{{ number_format($totalPelabuhan) }}</b><strong>Pelabuhan</strong><small>Fasilitas transportasi laut.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('fasilitas.index', ['type' => 'AIRPORT']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#f0eaff;color:#8054c7"><i class="fa-solid fa-plane"></i></div>
            <b>{{ number_format($totalBandara) }}</b><strong>Bandara</strong><small>Fasilitas transportasi udara.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <div class="cx-stat">
            <div class="cx-ico" style="background:#fff3dc;color:#f59e0b"><i class="fa-solid fa-truck-fast"></i></div>
            <b>{{ number_format($totalDarat) }}</b><strong>Darat</strong><small>Depo dan gudang logistik.</small>
        </div>
        <a href="{{ route('fasilitas.index', ['status' => 'INACTIVE']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#ffebeb;color:#e5484d"><i class="fa-solid fa-circle-pause"></i></div>
            <b>{{ number_format($totalNonaktif) }}</b><strong>Tidak Aktif</strong><small>Fasilitas yang dinonaktifkan.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
    </section>

    <!-- DAFTAR -->
    <section class="panel">
        <div class="ph">
            <div class="ph-left">
                <div class="ph-ico"><i class="fa-solid fa-list"></i></div>
                <h2>Daftar Fasilitas</h2>
            </div>
            <button type="button" class="btn" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> Tambah</button>
        </div>
        <p class="ph-sub">{{ $facilities->total() }} fasilitas tersimpan. Klik baris untuk melihat detail lengkap.</p>

        <form action="{{ route('fasilitas.index') }}" method="GET" class="filters">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, kode, kota, atau alamat..." aria-label="Pencarian">
            <select name="type" aria-label="Jenis">
                <option value="">Semua jenis</option>
                @foreach($typeLabels as $val => $label)
                    <option value="{{ $val }}" @selected($type === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="city" aria-label="Kota">
                <option value="">Semua kota</option>
                @foreach($cities as $cityName)
                    <option value="{{ $cityName }}" @selected($city === $cityName)>{{ $cityName }}</option>
                @endforeach
            </select>
            <select name="status" aria-label="Status">
                <option value="">Semua status</option>
                <option value="ACTIVE" @selected($status === 'ACTIVE')>Aktif</option>
                <option value="INACTIVE" @selected($status === 'INACTIVE')>Tidak Aktif</option>
            </select>
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a href="{{ route('fasilitas.index') }}" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fasilitas</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($facilities as $facility)
                    @php
                        $json = json_encode($facility->toArray(), JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_TAG);
                    @endphp
                    <tr class="row" data-facility="{{ $json }}" onclick="showDetail(this)">
                        <td>
                            <div class="f-info">
                                <div class="f-ico ic-{{ $facility->type }}"><i class="fa-solid {{ $typeIcons[$facility->type] ?? 'fa-location-dot' }}"></i></div>
                                <div>
                                    <div class="f-name">{{ $facility->name }}</div>
                                    <div class="f-sub">{{ $facility->facility_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="tag t-{{ $facility->type }}">{{ $typeLabels[$facility->type] ?? $facility->type }}</span></td>
                        <td>
                            <div>{{ $facility->city }}, {{ $facility->province }}</div>
                            <div class="f-sub">{{ $facility->country }}</div>
                        </td>
                        <td>
                            @if($facility->capacity !== null)
                                <span class="f-name">{{ number_format((float) $facility->capacity, 2, ',', '.') }}</span>
                            @else
                                <span class="f-sub">Belum diisi</span>
                            @endif
                        </td>
                        <td><span class="status st-{{ $facility->status }}">{{ $facility->status === 'ACTIVE' ? 'AKTIF' : 'TIDAK AKTIF' }}</span></td>
                        <td onclick="event.stopPropagation()">
                            <div class="acts">
                                <button type="button" class="ab ab-d" title="Detail" data-facility="{{ $json }}" onclick="showDetail(this)"><i class="fa-solid fa-eye"></i></button>
                                <button type="button" class="ab ab-e" title="Edit" data-facility="{{ $json }}" onclick="editFacility(this)"><i class="fa-solid fa-pen"></i></button>
                                @if($facility->status === 'ACTIVE')
                                    <form action="{{ route('fasilitas.destroy', $facility->id) }}" method="POST" data-name="{{ $facility->name }}" onsubmit="return confirmDisable(this)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ab ab-x" title="Nonaktifkan"><i class="fa-solid fa-power-off"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada fasilitas yang cocok. Ubah filter atau tambah fasilitas baru.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            <span>Menampilkan {{ $facilities->firstItem() ?? 0 }}–{{ $facilities->lastItem() ?? 0 }} dari {{ $facilities->total() }} fasilitas</span>
            @if($facilities->hasPages())<div>{{ $facilities->links() }}</div>@endif
        </div>
    </section>

    <!-- PETA -->
    <section class="panel" id="peta">
        <div class="ph">
            <div class="ph-left">
                <div class="ph-ico"><i class="fa-solid fa-map-location-dot"></i></div>
                <h2>Peta Lokasi Fasilitas</h2>
            </div>
        </div>
        <p class="ph-sub">Lokasi fasilitas berdasarkan koordinat dan mengikuti filter di atas. <span id="mapCount"></span></p>

        <div class="map-box"><div id="facilityMap"></div></div>

        <div class="legend">
            <span><i style="background:#315bba"></i> Pelabuhan</span>
            <span><i style="background:#8054c7"></i> Bandara</span>
            <span><i style="background:#d99b22"></i> Depo</span>
            <span><i style="background:#16a36a"></i> Gudang</span>
        </div>
        <p class="map-note">Marker hanya muncul untuk fasilitas yang sudah punya latitude dan longitude.</p>
    </section>

</section>
</main>

<!-- MODAL TAMBAH / EDIT -->
<div class="modal" id="facilityModal">
    <div class="mbox">
        <div class="mhead">
            <h3 id="modalTitle">Tambah Fasilitas</h3>
            <button type="button" class="mclose" onclick="closeModal('facilityModal')">&times;</button>
        </div>
        <form method="POST" id="facilityForm" action="{{ route('fasilitas.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="_mode" id="formMode" value="create">
            <input type="hidden" name="_id" id="formId" value="">
            <div class="mbody">
                <div class="grid2">
                    <div class="f"><label>Kode Fasilitas *</label><input name="facility_code" id="f_facility_code" placeholder="Contoh: PRT-001" required></div>
                    <div class="f"><label>Nama Fasilitas *</label><input name="name" id="f_name" placeholder="Contoh: Pelabuhan Belawan" required></div>
                    <div class="f"><label>Jenis Fasilitas *</label>
                        <select name="type" id="f_type" required>
                            <option value="">Pilih jenis</option>
                            <option value="PORT">Pelabuhan</option>
                            <option value="AIRPORT">Bandara</option>
                            <option value="DEPOT">Depo</option>
                            <option value="WAREHOUSE">Gudang</option>
                        </select>
                    </div>
                    <div class="f"><label>Status *</label>
                        <select name="status" id="f_status" required>
                            <option value="ACTIVE">Aktif</option>
                            <option value="INACTIVE">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="f"><label>Kota *</label><input name="city" id="f_city" required></div>
                    <div class="f"><label>Provinsi *</label><input name="province" id="f_province" required></div>
                    <div class="f"><label>Negara *</label><input name="country" id="f_country" required></div>
                    <div class="f"><label>Kapasitas</label><input type="number" step="0.01" min="0" name="capacity" id="f_capacity"></div>
                    <div class="f"><label>Latitude</label><input type="number" step="any" name="latitude" id="f_latitude" placeholder="3.7826"></div>
                    <div class="f"><label>Longitude</label><input type="number" step="any" name="longitude" id="f_longitude" placeholder="98.6930"></div>
                    <div class="f full"><label>Alamat *</label><textarea name="address" id="f_address" required></textarea></div>
                    <div class="f full"><label>Deskripsi</label><textarea name="description" id="f_description" placeholder="Informasi tambahan tentang fasilitas"></textarea></div>
                </div>
            </div>
            <div class="mfoot">
                <button type="button" class="btn btn-light" onclick="closeModal('facilityModal')">Batal</button>
                <button type="submit" class="btn" id="submitButton">Simpan Fasilitas</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal" id="detailModal">
    <div class="mbox">
        <div class="mhead">
            <h3>Detail Fasilitas</h3>
            <button type="button" class="mclose" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="mbody">
            <div class="d-head">
                <div id="detailIcon"></div>
                <div><h4 id="detailName">-</h4><p id="detailCode">Kode: -</p></div>
            </div>
            <div class="grid2" id="detailContent"></div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-light" id="detailMapBtn" style="display:none"><i class="fa-solid fa-map-pin"></i> Lihat di Peta</button>
            <button type="button" class="btn btn-light" onclick="closeModal('detailModal')">Tutup</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const facilityBase = @json(url('/fasilitas'));
const storeUrl = @json(route('fasilitas.store'));
const fieldIds = ['facility_code','name','type','status','city','province','country','capacity','latitude','longitude','address','description'];

const TYPE_LABEL = { PORT:'Pelabuhan', AIRPORT:'Bandara', DEPOT:'Depo', WAREHOUSE:'Gudang' };
const TYPE_ICON  = { PORT:'fa-anchor', AIRPORT:'fa-plane', DEPOT:'fa-boxes-stacked', WAREHOUSE:'fa-warehouse' };
const TYPE_COLOR = { PORT:'#315bba', AIRPORT:'#8054c7', DEPOT:'#d99b22', WAREHOUSE:'#16a36a' };

const esc = s => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));

function openModal(id){document.getElementById(id).classList.add('show')}
function closeModal(id){document.getElementById(id).classList.remove('show')}

document.querySelectorAll('.modal').forEach(function(m){
    m.addEventListener('click',function(e){ if(e.target===m) m.classList.remove('show'); });
});
document.addEventListener('keydown',function(e){
    if(e.key==='Escape') document.querySelectorAll('.modal.show').forEach(function(m){m.classList.remove('show')});
});

function readFacility(el){
    try{return JSON.parse(el.dataset.facility)}
    catch(e){alert('Data fasilitas tidak dapat dibaca.');return null}
}

function setMode(mode,id){
    const form=document.getElementById('facilityForm');
    document.getElementById('formMode').value=mode;
    document.getElementById('formId').value=id||'';
    if(mode==='edit'){
        form.action=facilityBase+'/'+id;
        document.getElementById('formMethod').value='PUT';
        document.getElementById('modalTitle').textContent='Edit Fasilitas';
        document.getElementById('submitButton').textContent='Simpan Perubahan';
    }else{
        form.action=storeUrl;
        document.getElementById('formMethod').value='POST';
        document.getElementById('modalTitle').textContent='Tambah Fasilitas';
        document.getElementById('submitButton').textContent='Simpan Fasilitas';
    }
}

function fillForm(data){
    fieldIds.forEach(function(f){ document.getElementById('f_'+f).value=(data&&data[f])??''; });
}

function openCreateModal(){
    document.getElementById('facilityForm').reset();
    setMode('create');
    fillForm({status:'ACTIVE',country:'Indonesia'});
    openModal('facilityModal');
}

function editFacility(btn){
    const f=readFacility(btn); if(!f)return;
    document.getElementById('facilityForm').reset();
    setMode('edit',f.id);
    fillForm(f);
    openModal('facilityModal');
}

let currentDetail=null;
function renderDetail(f){
    currentDetail=f;
    document.getElementById('detailName').textContent=f.name||'-';
    document.getElementById('detailCode').textContent='Kode: '+(f.facility_code||'-');

    const icon=document.getElementById('detailIcon');
    icon.replaceChildren();
    const box=document.createElement('div');
    box.className='f-ico ic-'+f.type;
    const i=document.createElement('i');
    i.className='fa-solid '+(TYPE_ICON[f.type]||'fa-location-dot');
    box.appendChild(i); icon.appendChild(box);

    const hasCoord=f.latitude!==null&&f.latitude!==''&&f.longitude!==null&&f.longitude!=='';
    const cap=(f.capacity!==null&&f.capacity!=='')?Number(f.capacity).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}):'-';
    const items=[
        ['Jenis Fasilitas',TYPE_LABEL[f.type]||f.type],
        ['Status',f.status==='ACTIVE'?'AKTIF':'TIDAK AKTIF'],
        ['Kota',f.city],
        ['Provinsi',f.province],
        ['Negara',f.country],
        ['Kapasitas',cap],
        ['Koordinat',hasCoord?(f.latitude+', '+f.longitude):'Belum diisi'],
        ['Alamat',f.address,true],
        ['Deskripsi',f.description,true]
    ];
    const wrap=document.getElementById('detailContent');
    wrap.replaceChildren();
    items.forEach(function(it){
        const d=document.createElement('div');
        d.className='d-item'+(it[2]?' full':'');
        const l=document.createElement('label'); l.textContent=it[0];
        const s=document.createElement('strong');
        s.textContent=(it[1]===null||it[1]===undefined||it[1]==='')?'-':it[1];
        d.append(l,s); wrap.appendChild(d);
    });

    const mb=document.getElementById('detailMapBtn');
    mb.style.display=hasCoord?'inline-flex':'none';
    openModal('detailModal');
}
function showDetail(el){ const f=readFacility(el); if(f) renderDetail(f); }

function confirmDisable(form){
    return confirm('Yakin ingin menonaktifkan fasilitas '+(form.dataset.name||'ini')+'?');
}

/* ===== PETA ===== */
const facilities = @json($mapFacilities);
const byId = {};
const markerById = {};

const map = L.map('facilityMap', { worldCopyJump:true }).setView([2.5, 98.5], 5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom:19, attribution:'&copy; OpenStreetMap contributors' }).addTo(map);
setTimeout(function(){ map.invalidateSize(); }, 250);

function pinIcon(type){
    const color=TYPE_COLOR[type]||'#315bba', icon=TYPE_ICON[type]||'fa-location-dot';
    return L.divIcon({ className:'', html:'<div class="pin" style="background:'+color+'"><i class="fa-solid '+icon+'"></i></div>', iconSize:[34,34], iconAnchor:[17,17], popupAnchor:[0,-18] });
}

const markers=[];
facilities.forEach(function(f){
    const lat=Number(f.latitude), lng=Number(f.longitude);
    if(f.latitude===null||f.longitude===null||!Number.isFinite(lat)||!Number.isFinite(lng)) return;
    byId[f.id]=f;
    const popup='<div class="pop"><strong>'+esc(f.name)+'</strong><small>'+esc((TYPE_LABEL[f.type]||f.type).toUpperCase())+' · '+esc(f.facility_code)+'</small><br>'+
        esc([f.address,f.city,f.province].filter(Boolean).join(', '))+'<br><button type="button" onclick="showDetailById('+Number(f.id)+')">Lihat detail →</button></div>';
    const m=L.marker([lat,lng],{icon:pinIcon(f.type)}).bindPopup(popup).addTo(map);
    markerById[f.id]=m;
    markers.push(m);
});

document.getElementById('mapCount').textContent = markers.length+' fasilitas berkoordinat ditampilkan.';
if(markers.length){ map.fitBounds(L.featureGroup(markers).getBounds().pad(0.15), { maxZoom:12 }); }

function showDetailById(id){ if(byId[id]) renderDetail(byId[id]); }

document.getElementById('detailMapBtn').addEventListener('click',function(){
    if(!currentDetail) return;
    closeModal('detailModal');
    document.getElementById('peta').scrollIntoView({behavior:'smooth',block:'start'});
    const m=markerById[currentDetail.id];
    if(m){ map.flyTo(m.getLatLng(),13,{duration:1.2}); setTimeout(function(){m.openPopup()},1300); }
});

/* Buka kembali modal jika validasi gagal */
@if($errors->any() && old('_mode'))
(function(){
    const old=@json(old());
    document.getElementById('facilityForm').reset();
    setMode(old._mode==='edit'?'edit':'create',old._id);
    fillForm(old);
    openModal('facilityModal');
})();
@endif
</script>
</body>
</html>
