<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Pengguna &amp; Hak Akses | CargoTrace</title>
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

.hero{position:relative;overflow:hidden;padding:38px 42px;border-radius:30px;color:#fff;display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap;margin-bottom:22px;
 background:linear-gradient(100deg,#061533f5 0%,#0b2a6bdd 55%,#1d6fe870 100%),url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?auto=format&fit=crop&w=1800&q=80') center/cover,#0b2a6b;box-shadow:0 26px 54px #0b2a6b46}
.hero>*{position:relative;z-index:2}
.crumb{font-size:12px;color:#cfe0ff;margin-bottom:10px}
.hero h1{font-weight:800;font-size:38px;letter-spacing:-1px;line-height:1.15}
.hero p{margin-top:14px;max-width:560px;line-height:1.8;color:#dbe9ff;font-size:14px}
.hbtn{display:inline-flex;align-items:center;gap:9px;padding:15px 22px;border:0;border-radius:14px;font-size:13px;font-weight:700;cursor:pointer;color:#fff;background:linear-gradient(135deg,#3b8bf5,#1d6fe8);box-shadow:0 10px 24px #1d6fe855}
.hbtn:hover{filter:brightness(1.08)}

.cx-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:22px}
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
.filters input:focus,.filters select:focus,.f input:focus,.f select:focus{border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}

.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:separate;border-spacing:0;min-width:760px}
th{background:#f1f6ff;color:#5d7aa5;text-align:left;font-size:12px;font-weight:700;padding:15px 16px;white-space:nowrap}
th:first-child{border-radius:14px 0 0 14px}th:last-child{border-radius:0 14px 14px 0}
td{padding:16px;border-bottom:1px solid #edf3fa;color:#41608a;vertical-align:middle;font-size:13px}
tbody tr.row{cursor:pointer;transition:.15s}
tbody tr.row:hover{background:#f6faff}
.u-info{display:flex;align-items:center;gap:13px;min-width:220px}
.u-av{display:grid;place-items:center;flex:none;width:44px;height:44px;border-radius:50%;background:#e8f1ff;color:#2563eb;font-weight:800;font-size:16px}
.u-name{font-weight:700;color:var(--navy)}
.u-sub{margin-top:4px;font-size:11.5px;color:#8ca6c4}
.tag{display:inline-block;padding:6px 12px;border-radius:20px;font-size:11px;font-weight:800;white-space:nowrap}
.r-admin{background:#f0eaff;color:#7044bd}.r-operator{background:#e8f0ff;color:#2861ae}.r-customer{background:#e5f8ef;color:#168354}
.you{margin-left:7px;padding:3px 8px;border-radius:10px;background:#e8f1ff;color:#2563eb;font-size:10px;font-weight:700}
.acts{display:flex;gap:7px}
.acts form{margin:0}
.ab{display:grid;place-items:center;width:36px;height:36px;border:0;border-radius:11px;cursor:pointer;font-size:13px;transition:.15s}
.ab:hover{filter:brightness(.95);transform:translateY(-1px)}
.ab-d{background:#e8f1ff;color:#1d4ed8}.ab-e{background:#fff4dc;color:#b77900}.ab-x{background:#ffe9e9;color:#c92f3a}
td.empty{text-align:center;padding:42px;color:#8ca6c4}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-top:20px;font-size:12px;color:#8ca6c4}
.pager nav svg{width:16px;height:16px}

/* PERAN & MATRIKS */
.role-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.role-card{padding:22px;border:1px solid #e9f0fb;border-radius:22px;background:#fbfdff}
.role-top{display:flex;align-items:center;gap:14px;margin-bottom:16px}
.role-ico{display:grid;place-items:center;width:48px;height:48px;border-radius:14px;font-size:19px}
.role-card h4{font-weight:800;font-size:15px;color:var(--navy)}
.role-card .rs{margin-top:3px;font-size:12px;color:#8ca6c4}
.role-card ul{list-style:none;display:grid;gap:9px}
.role-card li{display:flex;gap:9px;font-size:12.5px;color:#41608a;line-height:1.5}
.role-card li i{margin-top:3px;font-size:11px;color:#16a34a}
.pm th:not(:first-child),.pm td:not(:first-child){text-align:center}
.pm td:first-child{font-weight:700;color:var(--navy)}
.pm-yes{display:inline-block;padding:5px 11px;border-radius:20px;background:#e8f7ef;color:#26734d;font-size:11px;font-weight:700}
.pm-lim{display:inline-block;padding:5px 11px;border-radius:20px;background:#fff4dc;color:#b77900;font-size:11px;font-weight:700}
.pm-no{color:#c0c8d3;font-size:16px}

/* MODAL */
.modal{display:none;position:fixed;inset:0;background:rgba(6,21,51,.6);backdrop-filter:blur(3px);z-index:9999;align-items:center;justify-content:center;padding:18px}
.modal.show{display:flex}
.mbox{background:#fff;width:100%;max-width:640px;max-height:92vh;overflow-y:auto;border-radius:26px;box-shadow:0 24px 70px rgba(6,21,51,.35)}
.mhead{position:sticky;top:0;z-index:2;display:flex;justify-content:space-between;align-items:center;gap:12px;padding:22px 28px;background:linear-gradient(100deg,#061533,#0b2a6b 70%,#1747b8);color:#fff}
.mhead h3{font-weight:800;font-size:18px}
.mclose{border:0;background:#ffffff26;color:#fff;border-radius:10px;width:34px;height:34px;font-size:20px;cursor:pointer}
.mbody{padding:26px 28px}
.mfoot{display:flex;justify-content:flex-end;gap:10px;padding:18px 28px;border-top:1px solid #edf3fa}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.f label{display:block;font-size:12px;font-weight:700;color:#4a6694;margin-bottom:8px}
.f input,.f select{width:100%;padding:13px 14px;border:1px solid #dce8f8;border-radius:12px;font-size:13px;color:var(--ink);background:#fff;outline:none}
.f.full{grid-column:1/-1}
.f small{display:block;margin-top:7px;font-size:11px;color:#8ca6c4}
.d-head{display:flex;align-items:center;gap:16px;padding-bottom:20px;margin-bottom:20px;border-bottom:1px solid #edf3fa}
.d-head .u-av{width:64px;height:64px;font-size:24px}
.d-head h4{font-weight:800;font-size:19px;color:var(--navy)}
.d-head p{margin-top:5px;font-size:12px;color:#8ca6c4}
.d-item label{display:block;font-size:11px;font-weight:700;color:#8ca6c4;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.d-item strong{font-size:13.5px;color:var(--navy);overflow-wrap:anywhere;font-weight:600;line-height:1.6}
.d-item.full{grid-column:1/-1}

@media(max-width:1350px){.role-grid{grid-template-columns:1fr}}
@media(max-width:1100px){.content{padding:20px}.cx-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.hero{padding:30px 26px}.hero h1{font-size:30px}}
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
            <div class="crumb">Dashboard / Pengguna &amp; Hak Akses</div>
            <h1>Pengelolaan Pengguna &amp; Hak Akses</h1>
            <p>Kelola akun pengguna, peran, dan pembagian hak akses CargoTrace dalam satu halaman.</p>
        </div>
        <button type="button" class="hbtn" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> Tambah Pengguna</button>
    </section>

    <!-- STATISTIK -->
    <section class="cx-stats">
        <a href="{{ route('users.index') }}" class="cx-stat">
            <div class="cx-ico" style="background:#eef2f8;color:#64748b"><i class="fa-solid fa-users"></i></div>
            <b>{{ number_format($totalUsers) }}</b><strong>Total</strong><small>Seluruh akun terdaftar.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('users.index', ['role' => 'admin']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#f0eaff;color:#7c3aed"><i class="fa-solid fa-user-shield"></i></div>
            <b>{{ number_format($adminUsers) }}</b><strong>Administrator</strong><small>Akses penuh sistem.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('users.index', ['role' => 'operator']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#e8f0ff;color:#2563eb"><i class="fa-solid fa-box-open"></i></div>
            <b>{{ number_format($operatorUsers) }}</b><strong>Logistics Operator</strong><small>Akses operasional.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('users.index', ['role' => 'customer']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#fff3dc;color:#f59e0b"><i class="fa-solid fa-user"></i></div>
            <b>{{ number_format($customerUsers) }}</b><strong>Customer</strong><small>Akses data pribadi.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
    </section>

    <!-- DAFTAR -->
    <section class="panel">
        <div class="ph">
            <div class="ph-left">
                <div class="ph-ico"><i class="fa-solid fa-list"></i></div>
                <h2>Daftar Pengguna</h2>
            </div>
            <button type="button" class="btn" onclick="openCreateModal()"><i class="fa-solid fa-plus"></i> Tambah</button>
        </div>
        <p class="ph-sub">{{ $users->total() }} pengguna tersimpan. Klik baris untuk melihat detail lengkap.</p>

        <form action="{{ route('users.index') }}" method="GET" class="filters">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email pengguna..." aria-label="Pencarian">
            <select name="role" aria-label="Role">
                <option value="">Semua role</option>
                @foreach($roles as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a href="{{ route('users.index') }}" class="btn btn-light">Reset</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role / Peran</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    @php
                        $isMe = (int) auth()->id() === (int) $user->id;
                        $json = json_encode([
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'created' => $user->created_at?->format('d M Y, H:i') ?? '-',
                        ], JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP|JSON_HEX_TAG);
                        $roleLabel = $roles[$user->role] ?? ucfirst((string) $user->role);
                    @endphp
                    <tr class="row" data-user="{{ $json }}" onclick="showDetail(this)">
                        <td>
                            <div class="u-info">
                                <div class="u-av">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <div class="u-name">{{ $user->name }}@if($isMe)<span class="you">Kamu</span>@endif</div>
                                    <div class="u-sub">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="tag r-{{ in_array($user->role, ['admin','operator','customer']) ? $user->role : 'customer' }}">{{ strtoupper($roleLabel) }}</span></td>
                        <td>{{ $user->created_at?->format('d M Y, H:i') ?? '-' }}</td>
                        <td onclick="event.stopPropagation()">
                            <div class="acts">
                                <button type="button" class="ab ab-d" title="Detail" data-user="{{ $json }}" onclick="showDetail(this)"><i class="fa-solid fa-eye"></i></button>
                                <button type="button" class="ab ab-e" title="Edit" data-user="{{ $json }}" onclick="editUser(this)"><i class="fa-solid fa-pen"></i></button>
                                @unless($isMe)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" data-name="{{ $user->name }}" onsubmit="return confirmDelete(this)">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ab ab-x" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endunless
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">Belum ada pengguna yang cocok. Ubah filter atau tambah pengguna baru.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pager">
            <span>Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna</span>
            @if($users->hasPages())<div>{{ $users->links() }}</div>@endif
        </div>
    </section>

    <!-- MANAJEMEN PERAN -->
    <section class="panel">
        <div class="ph">
            <div class="ph-left">
                <div class="ph-ico"><i class="fa-solid fa-user-lock"></i></div>
                <h2>Manajemen Peran Pengguna</h2>
            </div>
        </div>
        <p class="ph-sub">Pembagian tanggung jawab berdasarkan tiga role utama CargoTrace.</p>

        <div class="role-grid">
            <div class="role-card">
                <div class="role-top">
                    <div class="role-ico" style="background:#f0eaff;color:#7c3aed"><i class="fa-solid fa-user-shield"></i></div>
                    <div><h4>Administrator</h4><div class="rs">Akses penuh sistem</div></div>
                </div>
                <ul>
                    <li><i class="fa-solid fa-check"></i>Mengelola seluruh data pengiriman</li>
                    <li><i class="fa-solid fa-check"></i>Mengelola perusahaan pengiriman</li>
                    <li><i class="fa-solid fa-check"></i>Mengelola lokasi dan fasilitas</li>
                    <li><i class="fa-solid fa-check"></i>Mengelola pengguna dan hak akses</li>
                    <li><i class="fa-solid fa-check"></i>Melihat seluruh pengecualian</li>
                    <li><i class="fa-solid fa-check"></i>Mengakses laporan dan konfigurasi</li>
                </ul>
            </div>
            <div class="role-card">
                <div class="role-top">
                    <div class="role-ico" style="background:#e8f0ff;color:#2563eb"><i class="fa-solid fa-box-open"></i></div>
                    <div><h4>Logistics Operator</h4><div class="rs">Akses operasional</div></div>
                </div>
                <ul>
                    <li><i class="fa-solid fa-check"></i>Membuat data pengiriman</li>
                    <li><i class="fa-solid fa-check"></i>Memperbarui informasi pengiriman</li>
                    <li><i class="fa-solid fa-check"></i>Mengelola status dan pelacakan</li>
                    <li><i class="fa-solid fa-check"></i>Mencatat pengecualian</li>
                    <li><i class="fa-solid fa-check"></i>Menangani pengecualian</li>
                    <li><i class="fa-solid fa-check"></i>Melihat perusahaan dan fasilitas</li>
                </ul>
            </div>
            <div class="role-card">
                <div class="role-top">
                    <div class="role-ico" style="background:#e5f8ef;color:#168354"><i class="fa-solid fa-user"></i></div>
                    <div><h4>Customer</h4><div class="rs">Akses data pribadi</div></div>
                </div>
                <ul>
                    <li><i class="fa-solid fa-check"></i>Melihat pengiriman miliknya</li>
                    <li><i class="fa-solid fa-check"></i>Melihat status pengiriman</li>
                    <li><i class="fa-solid fa-check"></i>Melihat lokasi barang</li>
                    <li><i class="fa-solid fa-check"></i>Melihat riwayat perjalanan</li>
                    <li><i class="fa-solid fa-check"></i>Melihat estimasi waktu tiba</li>
                    <li><i class="fa-solid fa-check"></i>Menerima notifikasi pengiriman</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- MATRIKS HAK AKSES -->
    <section class="panel">
        <div class="ph">
            <div class="ph-left">
                <div class="ph-ico"><i class="fa-solid fa-table-list"></i></div>
                <h2>Matriks Hak Akses</h2>
            </div>
        </div>
        <p class="ph-sub">Gambaran pembagian akses fitur berdasarkan peran pengguna.</p>

        <div class="table-wrap">
            <table class="pm" style="min-width:680px">
                <thead>
                    <tr><th>Fitur</th><th>Administrator</th><th>Operator</th><th>Customer</th></tr>
                </thead>
                <tbody>
                    <tr><td>Dashboard</td><td><span class="pm-yes">Penuh</span></td><td><span class="pm-lim">Operasional</span></td><td><span class="pm-lim">Pribadi</span></td></tr>
                    <tr><td>Pengiriman</td><td><span class="pm-yes">Kelola Semua</span></td><td><span class="pm-yes">Kelola Operasional</span></td><td><span class="pm-lim">Milik Sendiri</span></td></tr>
                    <tr><td>Pelacakan</td><td><span class="pm-yes">Semua</span></td><td><span class="pm-yes">Operasional</span></td><td><span class="pm-lim">Milik Sendiri</span></td></tr>
                    <tr><td>Pengecualian</td><td><span class="pm-yes">Kelola Semua</span></td><td><span class="pm-yes">Tangani</span></td><td><span class="pm-lim">Lihat Terkait</span></td></tr>
                    <tr><td>Perusahaan Pengiriman</td><td><span class="pm-yes">Kelola</span></td><td><span class="pm-lim">Lihat</span></td><td><span class="pm-no">—</span></td></tr>
                    <tr><td>Lokasi &amp; Fasilitas</td><td><span class="pm-yes">Kelola</span></td><td><span class="pm-lim">Lihat</span></td><td><span class="pm-no">—</span></td></tr>
                    <tr><td>Pengguna &amp; Hak Akses</td><td><span class="pm-yes">Kelola</span></td><td><span class="pm-no">—</span></td><td><span class="pm-no">—</span></td></tr>
                    <tr><td>Notifikasi</td><td><span class="pm-yes">Semua</span></td><td><span class="pm-lim">Terkait Tugas</span></td><td><span class="pm-lim">Milik Sendiri</span></td></tr>
                    <tr><td>Audit Log</td><td><span class="pm-yes">Lihat</span></td><td><span class="pm-no">—</span></td><td><span class="pm-no">—</span></td></tr>
                </tbody>
            </table>
        </div>
    </section>

</section>
</main>

<!-- MODAL TAMBAH / EDIT -->
<div class="modal" id="userModal">
    <div class="mbox">
        <div class="mhead">
            <h3 id="modalTitle">Tambah Pengguna</h3>
            <button type="button" class="mclose" onclick="closeModal('userModal')">&times;</button>
        </div>
        <form method="POST" id="userForm" action="{{ route('users.store') }}" autocomplete="off">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="_mode" id="formMode" value="create">
            <input type="hidden" name="_id" id="formId" value="">
            <div class="mbody">
                <div class="grid2">
                    <div class="f full"><label>Nama Lengkap *</label><input name="name" id="f_name" maxlength="255" placeholder="Masukkan nama lengkap" required></div>
                    <div class="f full"><label>Email *</label><input type="email" name="email" id="f_email" placeholder="nama@email.com" required></div>
                    <div class="f"><label>Password <span id="pwReq">*</span></label><input type="password" name="password" id="f_password" minlength="8" placeholder="Minimal 8 karakter" autocomplete="new-password"><small id="pwHint">Password minimal 8 karakter.</small></div>
                    <div class="f"><label>Konfirmasi Password <span id="pwReq2">*</span></label><input type="password" name="password_confirmation" id="f_password_confirmation" minlength="8" placeholder="Ulangi password" autocomplete="new-password"></div>
                    <div class="f full"><label>Role / Hak Akses *</label>
                        <select name="role" id="f_role" required>
                            <option value="">Pilih role pengguna</option>
                            <option value="admin">Administrator</option>
                            <option value="operator">Logistics Operator</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mfoot">
                <button type="button" class="btn btn-light" onclick="closeModal('userModal')">Batal</button>
                <button type="submit" class="btn" id="submitButton">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal" id="detailModal">
    <div class="mbox">
        <div class="mhead">
            <h3>Detail Pengguna</h3>
            <button type="button" class="mclose" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="mbody">
            <div class="d-head">
                <div class="u-av" id="detailAvatar">U</div>
                <div><h4 id="detailName">-</h4><p id="detailEmail">-</p></div>
            </div>
            <div class="grid2" id="detailContent"></div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-light" onclick="closeModal('detailModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
const storeUrl = @json(route('users.store'));
const updateUrlTemplate = @json(route('users.update', ['pengguna' => '__ID__']));
const ROLE_NAME = { admin:'Administrator', operator:'Logistics Operator', customer:'Customer' };
const ROLE_DESC = {
    admin:'Memiliki akses penuh untuk mengelola seluruh fitur dan data sistem CargoTrace.',
    operator:'Memiliki akses operasional untuk mengelola pengiriman, pelacakan, dan penanganan pengecualian.',
    customer:'Memiliki akses untuk melihat pengiriman, pelacakan, dan informasi yang berkaitan dengan akun sendiri.'
};

function openModal(id){document.getElementById(id).classList.add('show')}
function closeModal(id){document.getElementById(id).classList.remove('show')}

document.querySelectorAll('.modal').forEach(function(m){
    m.addEventListener('click',function(e){ if(e.target===m) m.classList.remove('show'); });
});
document.addEventListener('keydown',function(e){
    if(e.key==='Escape') document.querySelectorAll('.modal.show').forEach(function(m){m.classList.remove('show')});
});

function readUser(el){
    try{return JSON.parse(el.dataset.user)}
    catch(e){alert('Data pengguna tidak dapat dibaca.');return null}
}

function setMode(mode,id){
    const form=document.getElementById('userForm');
    const edit=mode==='edit';
    document.getElementById('formMode').value=mode;
    document.getElementById('formId').value=id||'';
    form.action=edit?updateUrlTemplate.replace('__ID__',id):storeUrl;
    document.getElementById('formMethod').value=edit?'PUT':'POST';
    document.getElementById('modalTitle').textContent=edit?'Edit Pengguna':'Tambah Pengguna';
    document.getElementById('submitButton').textContent=edit?'Simpan Perubahan':'Simpan Pengguna';
    document.getElementById('f_password').required=!edit;
    document.getElementById('f_password_confirmation').required=!edit;
    document.getElementById('pwReq').style.display=edit?'none':'inline';
    document.getElementById('pwReq2').style.display=edit?'none':'inline';
    document.getElementById('pwHint').textContent=edit?'Kosongkan jika tidak ingin mengganti password.':'Password minimal 8 karakter.';
}

function fillForm(d){
    ['name','email','role'].forEach(function(f){ document.getElementById('f_'+f).value=(d&&d[f])??''; });
}

function openCreateModal(){
    document.getElementById('userForm').reset();
    setMode('create');
    fillForm({});
    openModal('userModal');
}

function editUser(btn){
    const u=readUser(btn); if(!u)return;
    document.getElementById('userForm').reset();
    setMode('edit',u.id);
    fillForm(u);
    openModal('userModal');
}

function showDetail(el){
    const u=readUser(el); if(!u)return;
    document.getElementById('detailName').textContent=u.name||'-';
    document.getElementById('detailEmail').textContent=u.email||'-';
    document.getElementById('detailAvatar').textContent=(u.name||'U').charAt(0).toUpperCase();
    const items=[
        ['Role / Peran',ROLE_NAME[u.role]||u.role],
        ['Tanggal Pendaftaran',u.created],
        ['Informasi Hak Akses',ROLE_DESC[u.role],true]
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
    openModal('detailModal');
}

function confirmDelete(form){
    return confirm('Yakin ingin menghapus pengguna '+(form.dataset.name||'ini')+'?');
}

@if($errors->any() && old('_mode'))
(function(){
    const old=@json(old());
    document.getElementById('userForm').reset();
    setMode(old._mode==='edit'?'edit':'create',old._id);
    fillForm(old);
    openModal('userModal');
})();
@endif
</script>
</body>
</html>
