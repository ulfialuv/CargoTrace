<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Notifikasi | CargoTrace</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--navy:#061533;--blue:#2563eb;--ink:#12306a;--mut:#6c83ab;--line:#e0e9fa;--bg:#edf3ff}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--ink);font-size:13px}
a{color:inherit;text-decoration:none}
button,input,select{font-family:inherit}
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
.bdg{margin-left:auto;min-width:20px;padding:2px 6px;border-radius:10px;background:#e5484d;color:#fff;font-size:10px;font-weight:700;text-align:center;font-style:normal}
.s-user{margin-top:auto;position:relative;padding:14px;border-radius:16px;background:#ffffff14;border:1px solid #ffffff2b}
.s-user .who{display:flex;align-items:center;gap:11px}
.avatar{display:grid;place-items:center;width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#38bdf8,#2563eb);font-weight:800}
.s-user b{display:block;font-size:12.5px}.s-user small{font-size:10px;color:#a9c4f0}
.s-user button{width:100%;margin-top:12px;padding:9px;border:1px solid #ffffff33;border-radius:10px;background:transparent;color:#dce8ff;font:600 11px 'Plus Jakarta Sans',sans-serif;cursor:pointer}
.s-user button:hover{background:#ffffff1c}

/* ===== MAIN ===== */
.main{margin-left:272px;min-height:100vh;background:radial-gradient(circle at 100% 0,#cfe3ff 0,#0000 380px),radial-gradient(circle at 0 100%,#d7e8ff 0,#0000 340px),var(--bg)}
.content{padding:26px 32px 40px}
.alert{padding:13px 17px;margin-bottom:16px;border-radius:12px;font-size:12px;background:#e5f7ed;color:#187344}

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
.filters input:focus,.filters select:focus{border-color:var(--blue);box-shadow:0 0 0 3px #2563eb1f}

/* DAFTAR NOTIFIKASI */
.nlist{display:grid;gap:12px}
.ntf{position:relative;display:flex;align-items:flex-start;gap:16px;padding:20px 22px;border:1px solid #e9f0fb;border-radius:22px;background:#fff;transition:.2s}
.ntf:hover{border-color:#b9d2f7;background:#fbfdff}
.ntf.unread{background:#f4f8ff;border-color:#cfe0fb}
.ntf.unread::before{content:"";position:absolute;left:0;top:20px;bottom:20px;width:4px;border-radius:0 4px 4px 0;background:var(--blue)}
.n-ico{display:grid;place-items:center;flex:none;width:50px;height:50px;border-radius:15px;font-size:19px}
.n-info{background:#e8f1ff;color:#2563eb}.n-success{background:#e6f7ee;color:#16a34a}.n-warning{background:#fff3dc;color:#d58a20}.n-danger{background:#ffebeb;color:#e5484d}
.n-body{flex:1;min-width:0}
.n-title{display:flex;align-items:center;flex-wrap:wrap;gap:9px;font-weight:800;font-size:14.5px;color:var(--navy)}
.pill{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:10px;font-weight:700}
.pill-unread{background:#e0ecff;color:#1d4ed8}
.pill-read{background:#eef1f6;color:#7b879a}
.pill-imp{background:#fff3dc;color:#b77900}
.n-msg{margin-top:8px;font-size:13px;line-height:1.7;color:#41608a}
.n-meta{display:flex;flex-wrap:wrap;gap:8px 18px;margin-top:11px;font-size:12px;color:#8ca6c4}
.n-meta i{margin-right:5px}
.n-link{display:inline-flex;align-items:center;gap:7px;margin-top:12px;font-size:12px;font-weight:700;color:var(--blue)}
.n-link:hover{text-decoration:underline}
.n-acts{display:flex;gap:7px;flex:none}
.n-acts form{margin:0}
.ab{display:grid;place-items:center;width:38px;height:38px;border:0;border-radius:11px;cursor:pointer;font-size:13px;transition:.15s}
.ab:hover{filter:brightness(.95);transform:translateY(-1px)}
.ab-ok{background:#e6f7ee;color:#16a34a}.ab-x{background:#ffe9e9;color:#c92f3a}
.empty{text-align:center;padding:52px 14px;color:#8ca6c4}
.empty i{display:grid;place-items:center;width:64px;height:64px;margin:0 auto 14px;border-radius:50%;background:#e8f1ff;color:var(--blue);font-size:26px}
.empty h3{font-size:16px;color:var(--navy);margin-bottom:6px}
.pager{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-top:22px;font-size:12px;color:#8ca6c4}
.pager nav svg{width:16px;height:16px}

@media(max-width:1100px){.content{padding:20px}.cx-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.hero{padding:30px 26px}.hero h1{font-size:30px}}
@media(max-width:750px){
 .sidebar{width:72px;padding:18px 8px}.brand{justify-content:center}
 .brand-name,.brand-sub,.s-title,.s-link span,.s-user b,.s-user small,.s-user button,.bdg{display:none}
 .s-link{justify-content:center;padding:13px 4px}.s-user{padding:8px;background:none;border:0}.s-user .who{justify-content:center}
 .main{margin-left:72px}.content{padding:16px 12px 28px}.panel{padding:20px}
 .ntf{flex-wrap:wrap}.n-acts{width:100%;justify-content:flex-end}
}
@media(max-width:600px){.cx-stats{grid-template-columns:1fr}.hero h1{font-size:26px}}
</style>
</head>
<body>

@php
    $typeLabels = [
        'shipment_created' => 'Shipment Created',
        'shipment_status_update' => 'Shipment Status Update',
        'shipment_in_transit' => 'Shipment In Transit',
        'shipment_delivered' => 'Shipment Delivered',
        'shipment_delayed' => 'Shipment Delayed',
        'new_exception' => 'New Exception',
        'exception_resolved' => 'Exception Resolved',
        'tracking_update' => 'Tracking Update',
    ];
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
            <a href="{{ route('notifications.index') }}" class="s-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i><span>Notifikasi</span>
                @if($unreadNotifications > 0)<em class="bdg">{{ $unreadNotifications }}</em>@endif
            </a>
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

    @if(session('success'))<div class="alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>@endif

    <!-- HERO -->
    <section class="hero">
        <div>
            <div class="crumb">Dashboard / Notifikasi</div>
            <h1>Pusat Notifikasi</h1>
            <p>Pantau pembaruan pengiriman, pelacakan, dan pengecualian terbaru dalam satu halaman.</p>
        </div>
        @if($unreadNotifications > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="hbtn"><i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca</button>
            </form>
        @endif
    </section>

    <!-- STATISTIK -->
    <section class="cx-stats">
        <a href="{{ route('notifications.index') }}" class="cx-stat">
            <div class="cx-ico" style="background:#eef2f8;color:#64748b"><i class="fa-solid fa-bell"></i></div>
            <b>{{ number_format($totalNotifications) }}</b><strong>Total</strong><small>Seluruh pemberitahuan.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('notifications.index', ['status' => 'unread']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#e8f1ff;color:#2563eb"><i class="fa-solid fa-envelope"></i></div>
            <b>{{ number_format($unreadNotifications) }}</b><strong>Belum Dibaca</strong><small>Notifikasi yang belum dibuka.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <a href="{{ route('notifications.index', ['status' => 'read']) }}" class="cx-stat">
            <div class="cx-ico" style="background:#e6f7ee;color:#16a34a"><i class="fa-solid fa-envelope-open"></i></div>
            <b>{{ number_format($readNotifications) }}</b><strong>Sudah Dibaca</strong><small>Notifikasi yang telah dibaca.</small>
            <span class="cx-go"><i class="fa-solid fa-chevron-right"></i></span>
        </a>
        <div class="cx-stat">
            <div class="cx-ico" style="background:#fff3dc;color:#f59e0b"><i class="fa-solid fa-star"></i></div>
            <b>{{ number_format($importantNotifications) }}</b><strong>Penting</strong><small>Pemberitahuan prioritas.</small>
        </div>
    </section>

    <!-- DAFTAR -->
    <section class="panel">
        <div class="ph">
            <div class="ph-left">
                <div class="ph-ico"><i class="fa-solid fa-list"></i></div>
                <h2>Daftar Notifikasi</h2>
            </div>
            @if($unreadNotifications > 0)
                <form action="{{ route('notifications.readAll') }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn"><i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca</button>
                </form>
            @endif
        </div>
        <p class="ph-sub">{{ $notifications->total() }} notifikasi ditemukan. Informasi terbaru mengenai aktivitas pengiriman.</p>

        <form action="{{ route('notifications.index') }}" method="GET" class="filters">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, pesan, atau nomor pengiriman..." aria-label="Pencarian">
            <select name="type" aria-label="Jenis">
                <option value="">Semua jenis</option>
                @foreach($typeLabels as $val => $label)
                    <option value="{{ $val }}" @selected(request('type') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" aria-label="Status">
                <option value="">Semua status</option>
                <option value="unread" @selected(request('status') === 'unread')>Belum dibaca</option>
                <option value="read" @selected(request('status') === 'read')>Sudah dibaca</option>
            </select>
            <button type="submit" class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a href="{{ route('notifications.index') }}" class="btn btn-light">Reset</a>
        </form>

        <div class="nlist">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data;
                $type = $data['type'] ?? '';
                $isUnread = is_null($notification->read_at);
                $icon = match($type) {
                    'shipment_created' => 'fa-box',
                    'shipment_status_update' => 'fa-arrows-rotate',
                    'shipment_in_transit' => 'fa-truck-fast',
                    'shipment_delivered' => 'fa-circle-check',
                    'shipment_delayed' => 'fa-clock',
                    'new_exception' => 'fa-triangle-exclamation',
                    'exception_resolved' => 'fa-shield-halved',
                    'tracking_update' => 'fa-location-dot',
                    default => 'fa-bell',
                };
                $style = match($type) {
                    'shipment_delivered', 'exception_resolved' => 'n-success',
                    'shipment_delayed' => 'n-warning',
                    'new_exception' => 'n-danger',
                    default => 'n-info',
                };
            @endphp
            <article class="ntf {{ $isUnread ? 'unread' : '' }}">
                <div class="n-ico {{ $style }}"><i class="fa-solid {{ $icon }}"></i></div>

                <div class="n-body">
                    <div class="n-title">
                        {{ $data['title'] ?? 'Pemberitahuan CargoTrace' }}
                        @if($data['important'] ?? false)
                            <span class="pill pill-imp"><i class="fa-solid fa-star"></i> Penting</span>
                        @endif
                        <span class="pill {{ $isUnread ? 'pill-unread' : 'pill-read' }}">{{ $isUnread ? 'BARU' : 'DIBACA' }}</span>
                    </div>
                    <p class="n-msg">{{ $data['message'] ?? $data['body'] ?? 'Terdapat pembaruan informasi CargoTrace.' }}</p>
                    <div class="n-meta">
                        @if(!empty($data['shipment_number']))
                            <span><i class="fa-solid fa-box"></i>{{ $data['shipment_number'] }}</span>
                        @endif
                        <span><i class="fa-regular fa-clock"></i>{{ $notification->created_at->format('d M Y, H:i') }} · {{ $notification->created_at->diffForHumans() }}</span>
                        <span><i class="fa-solid fa-tag"></i>{{ $typeLabels[$type] ?? str_replace('_', ' ', ucfirst($type ?: 'general')) }}</span>
                    </div>
                    @if(!empty($data['url']))
                        <a href="{{ $data['url'] }}" class="n-link">Lihat Detail <i class="fa-solid fa-arrow-right"></i></a>
                    @endif
                </div>

                <div class="n-acts">
                    @if($isUnread)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="ab ab-ok" type="submit" title="Tandai sudah dibaca"><i class="fa-solid fa-check"></i></button>
                        </form>
                    @endif
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                        @csrf @method('DELETE')
                        <button class="ab ab-x" type="submit" title="Hapus notifikasi"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>
            </article>
        @empty
            <div class="empty">
                <i class="fa-regular fa-bell-slash"></i>
                <h3>Belum Ada Notifikasi</h3>
                <p>Notifikasi aktivitas CargoTrace akan muncul di sini.</p>
            </div>
        @endforelse
        </div>

        <div class="pager">
            <span>Menampilkan {{ $notifications->firstItem() ?? 0 }}–{{ $notifications->lastItem() ?? 0 }} dari {{ $notifications->total() }} notifikasi</span>
            @if($notifications->hasPages())<div>{{ $notifications->links() }}</div>@endif
        </div>
    </section>

</section>
</main>
</body>
</html>
