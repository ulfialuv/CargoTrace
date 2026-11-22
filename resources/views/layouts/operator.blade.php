<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Operator') - CargoTrace</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f4f7fb; color:#172033; }
        a { text-decoration:none; color:inherit; }
        .app-layout { min-height:100vh; display:flex; }
        .sidebar {
            width:260px; min-height:100vh;
            background:linear-gradient(180deg,#101c3a 0%,#172b50 55%,#203b65 100%);
            color:white; padding:25px 18px;
            display:flex; flex-direction:column; flex-shrink:0;
        }
        .brand { display:flex; align-items:center; gap:12px; padding:5px 10px 30px; }
        .brand-logo {
            width:45px; height:45px; border-radius:14px;
            background:linear-gradient(135deg,#fff,#dce8ff);
            color:#14264b; display:flex; align-items:center;
            justify-content:center; font-size:23px; font-weight:800;
        }
        .brand-name { font-size:19px; font-weight:800; }
        .brand-description { font-size:10px; color:#b9c8e2; margin-top:4px; letter-spacing:1.5px; }
        .menu-label { font-size:10px; font-weight:700; color:#91a6c9; letter-spacing:1.5px; padding:12px; }
        .menu { display:flex; flex-direction:column; gap:5px; }
        .menu-link {
            display:flex; align-items:center; gap:12px;
            padding:12px 14px; border-radius:12px;
            color:#d5deef; font-size:13px; font-weight:500; transition:.2s;
        }
        .menu-link:hover { background:rgba(255,255,255,.09); color:white; }
        .menu-link.active {
            background:linear-gradient(135deg,#fff,#e5edff);
            color:#172b50; font-weight:700;
            box-shadow:0 5px 15px rgba(0,0,0,.12);
        }
        .menu-icon { width:22px; text-align:center; font-size:17px; }
        .sidebar-bottom { margin-top:auto; padding-top:25px; }
        .sidebar-card {
            background:rgba(255,255,255,.08);
            border:1px solid rgba(255,255,255,.12);
            border-radius:15px; padding:15px;
        }
        .sidebar-card-title { font-size:12px; font-weight:700; margin-bottom:7px; }
        .sidebar-card-text { font-size:11px; color:#c0cde2; line-height:1.7; }
        .main-content { flex:1; min-width:0; display:flex; flex-direction:column; }
        .topbar {
            min-height:76px; background:white; border-bottom:1px solid #e8edf5;
            display:flex; align-items:center; justify-content:space-between;
            padding:15px 32px; gap:20px;
        }
        .topbar-title { font-size:18px; font-weight:800; color:#152747; }
        .topbar-subtitle { font-size:11px; color:#8b97aa; margin-top:5px; }
        .topbar-right { display:flex; align-items:center; gap:12px; }
        .system-status {
            display:flex; align-items:center; gap:7px;
            background:#effaf4; color:#25804e;
            border-radius:20px; padding:9px 13px;
            font-size:11px; font-weight:700;
        }
        .status-dot { width:7px; height:7px; border-radius:50%; background:#36b66b; }
        .user-profile { display:flex; align-items:center; gap:10px; padding-left:12px; border-left:1px solid #edf0f5; }
        .user-avatar {
            width:37px; height:37px; border-radius:12px;
            background:#e9effb; color:#19345f;
            display:flex; align-items:center; justify-content:center;
            font-weight:800; font-size:13px;
        }
        .user-name { font-size:12px; font-weight:700; }
        .user-role { font-size:10px; color:#8995a8; margin-top:3px; }
        .page-content { flex:1; padding:30px; }
        .page-footer { padding:18px 30px; color:#8b97aa; font-size:10px; border-top:1px solid #e8edf5; background:white; }
        .logout-form { margin-top:8px; }
        .logout-button {
            width:100%; border:0; cursor:pointer; text-align:left;
            background:rgba(255,255,255,.08); color:#ffdede;
            padding:12px 14px; border-radius:12px;
            font:500 13px 'Inter',sans-serif;
        }
        .logout-button:hover { background:rgba(255,255,255,.15); }
        @media(max-width:900px) {
            .sidebar { width:220px; }
            .topbar { padding:15px 20px; }
            .page-content { padding:20px; }
        }
        @media(max-width:650px) {
            .app-layout { flex-direction:column; }
            .sidebar { width:100%; min-height:auto; padding:15px; }
            .brand { padding-bottom:15px; }
            .menu { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); }
            .menu-label { padding-top:5px; }
            .sidebar-bottom { display:none; }
            .topbar { padding:15px; }
            .system-status { display:none; }
            .page-content { padding:15px; }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">C</div>
            <div>
                <div class="brand-name">CargoTrace</div>
                <div class="brand-description">SMART LOGISTICS SYSTEM</div>
            </div>
        </div>

        
<div class="menu-label">MENU OPERATOR</div>

<nav class="menu">

    {{-- DASHBOARD --}}
    <a href="{{ route('operator.dashboard') }}"
       class="menu-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
        <span class="menu-icon">▦</span>
        <span>Dashboard</span>
    </a>

    {{-- DAFTAR PENGIRIMAN --}}
    <a href="{{ route('operator.pengiriman.index') }}"
       class="menu-link {{ request()->routeIs('operator.pengiriman.*') ? 'active' : '' }}">
        <span class="menu-icon">📦</span>
        <span>Daftar Pengiriman</span>
    </a>

    {{-- PELACAKAN --}}
    <a href="{{ route('operator.pelacakan.index') }}"
       class="menu-link {{ request()->routeIs('operator.pelacakan.*') ? 'active' : '' }}">
        <span class="menu-icon">⌖</span>
        <span>Pelacakan</span>
    </a>

    {{-- PEMBARUAN STATUS --}}
    <a href="{{ route('operator.pembaruan-status.index') }}"
       class="menu-link {{ request()->routeIs('operator.pembaruan-status.*') ? 'active' : '' }}">
        <span class="menu-icon">↻</span>
        <span>Pembaruan Status</span>
    </a>

    {{-- PENGECUALIAN --}}
    <a href="{{ route('operator.exceptions.index') }}"
       class="menu-link {{ request()->routeIs('operator.exceptions.*') ? 'active' : '' }}">
        <span class="menu-icon">⚠</span>
        <span>Pengecualian</span>
    </a>

    {{-- NOTIFIKASI --}}
    <a href="{{ route('operator.notifications.index') }}"
       class="menu-link {{ request()->routeIs('operator.notifications.*') ? 'active' : '' }}">
        <span class="menu-icon">🔔</span>
        <span>Notifikasi</span>
    </a>

</nav>


        <div class="sidebar-bottom">
            <div class="sidebar-card">
                <div class="sidebar-card-title">CargoTrace Operator</div>
                <div class="sidebar-card-text">
                    Kelola proses pengiriman, pantau perjalanan barang,
                    dan perbarui status pengiriman.
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-button">↪ Keluar dari Sistem</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <div class="topbar-title">@yield('page-title','Dashboard Operator')</div>
                <div class="topbar-subtitle">Sistem Informasi Logistik Terintegrasi</div>
            </div>
            <div class="topbar-right">
                <div class="system-status"><span class="status-dot"></span>Sistem Aktif</div>
                <div class="user-profile">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'O',0,1)) }}</div>
                    <div>
                        <div class="user-name">{{ auth()->user()->name ?? 'Operator' }}</div>
                        <div class="user-role">Petugas / Operator</div>
                    </div>
                </div>
            </div>
        </header>

        <section class="page-content">
            @yield('content')
        </section>

        <footer class="page-footer">
            © {{ date('Y') }} CargoTrace. Sistem Informasi Logistik.
        </footer>
    </main>
</div>
@stack('scripts')
</body>
</html>
