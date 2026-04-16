
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name','Odovin') }} Admin — @yield('title','Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@300;400;500;600;700&family=Orbitron:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        :root {
            --bg-primary:#0a0e1a; --bg-secondary:#121827;
            --card-bg:rgba(26,32,48,0.85); --border-color:rgba(168,85,247,0.15);
            --accent:#a855f7; --accent-alt:#7c3aed;
            --accent-cyan:#00d4ff; --accent-green:#00ffaa;
            --accent-warning:#ffaa00; --accent-danger:#ff3366;
            --text-primary:#fff; --text-secondary:rgba(255,255,255,0.68);
            --text-tertiary:rgba(255,255,255,0.40);
            --sidebar-w:260px; --input-bg:rgba(168,85,247,0.06);
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:'Chakra Petch',sans-serif;background:var(--bg-primary);color:var(--text-primary);display:flex;min-height:100vh;}
        body::before{content:'';position:fixed;inset:0;
            background:radial-gradient(ellipse 60% 50% at 10% 5%,rgba(168,85,247,.06),transparent),
                        radial-gradient(ellipse 50% 40% at 90% 95%,rgba(124,58,237,.05),transparent);
            pointer-events:none;z-index:0;}
        /* ── Sidebar ── */
        .adm-sidebar{width:var(--sidebar-w);background:rgba(8,10,20,.98);border-right:1px solid var(--border-color);
            display:flex;flex-direction:column;position:fixed;inset:0 auto 0 0;z-index:100;transition:transform .3s;}
        .adm-logo{padding:1.5rem;border-bottom:1px solid var(--border-color);}
        .adm-logo-badge{display:inline-flex;align-items:center;gap:.375rem;
            background:linear-gradient(135deg,var(--accent),var(--accent-alt));
            padding:.3rem .75rem;border-radius:6px;font-size:.58rem;font-weight:700;
            color:#fff;letter-spacing:.1em;text-transform:uppercase;margin-bottom:.5rem;}
        .adm-logo-text{font-family:'Orbitron',sans-serif;font-size:1.2rem;font-weight:900;
            background:linear-gradient(135deg,#c084fc,#a855f7,#7c3aed);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .adm-logo-sub{font-size:.68rem;color:var(--text-tertiary);margin-top:2px;}
        .adm-nav{flex:1;overflow-y:auto;padding:1rem 0;}
        .adm-nav::-webkit-scrollbar{width:3px;}
        .adm-nav::-webkit-scrollbar-thumb{background:rgba(168,85,247,.2);border-radius:2px;}
        .adm-nav-section{padding:0 .75rem;margin-bottom:.25rem;}
        .adm-nav-label{font-size:.63rem;text-transform:uppercase;letter-spacing:.1em;
            color:var(--text-tertiary);font-weight:700;padding:.5rem .5rem;}
        .adm-nav-link{display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;border-radius:10px;
            color:var(--text-secondary);text-decoration:none;font-size:.875rem;font-weight:500;transition:all .25s;}
        .adm-nav-link svg{width:18px;height:18px;flex-shrink:0;}
        .adm-nav-link:hover{background:rgba(168,85,247,.08);color:var(--text-primary);}
        .adm-nav-link.active{background:linear-gradient(135deg,rgba(168,85,247,.18),rgba(124,58,237,.1));
            color:#c084fc;border-left:3px solid var(--accent);}
        .adm-badge{margin-left:auto;background:var(--accent-danger);color:#fff;
            border-radius:20px;font-size:.62rem;padding:2px 7px;font-weight:700;}
        .adm-count{margin-left:auto;font-size:.7rem;color:var(--text-tertiary);}
        .adm-footer{padding:1rem;border-top:1px solid var(--border-color);}
        .adm-user{display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;}
        .adm-avatar{width:40px;height:40px;border-radius:10px;
            background:linear-gradient(135deg,var(--accent),var(--accent-alt));
            display:flex;align-items:center;justify-content:center;
            font-family:'Orbitron',sans-serif;font-weight:800;font-size:.9rem;color:#fff;}
        .adm-user-name{font-size:.8rem;font-weight:600;}
        .adm-user-role{font-size:.68rem;color:var(--text-tertiary);}
        .adm-logout{display:flex;align-items:center;gap:.5rem;padding:.6rem .75rem;border-radius:8px;
            width:100%;background:rgba(255,51,102,.1);border:1px solid rgba(255,51,102,.25);
            color:var(--accent-danger);font-size:.8rem;font-weight:600;cursor:pointer;
            transition:all .25s;font-family:'Chakra Petch',sans-serif;}
        .adm-logout:hover{background:rgba(255,51,102,.2);}
        /* ── Main ── */
        .adm-main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;position:relative;z-index:1;}
        .adm-topbar{background:rgba(8,10,20,.92);backdrop-filter:blur(20px);border-bottom:1px solid var(--border-color);
            padding:1rem 2rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
        .adm-topbar-left{display:flex;align-items:center;gap:1rem;}
        .adm-topbar-title{font-family:'Orbitron',sans-serif;font-size:1rem;font-weight:700;}
        .adm-topbar-right{display:flex;align-items:center;gap:1rem;}
        .adm-status{display:flex;align-items:center;gap:.5rem;font-size:.78rem;color:var(--text-secondary);}
        .adm-status-dot{width:8px;height:8px;border-radius:50%;background:var(--accent);box-shadow:0 0 8px var(--accent);}
        .adm-content{padding:2rem;flex:1;max-width:1500px;width:100%;margin:0 auto;}
        /* ── Flash ── */
        .flash-success,.flash-error{padding:.875rem 1.25rem;border-radius:12px;font-size:.875rem;
            font-weight:600;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;}
        .flash-success{background:rgba(0,255,170,.12);border:1px solid rgba(0,255,170,.3);color:var(--accent-green);}
        .flash-error{background:rgba(255,51,102,.12);border:1px solid rgba(255,51,102,.3);color:var(--accent-danger);}
        /* ── Cards ── */
        .card{background:var(--card-bg);backdrop-filter:blur(16px);border:1px solid var(--border-color);border-radius:16px;padding:1.75rem;}
        .card-title{font-family:'Orbitron',sans-serif;font-size:.95rem;font-weight:700;
            margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--border-color);}
        .stat-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;
            padding:1.5rem;transition:all .3s;position:relative;overflow:hidden;}
        .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;
            background:linear-gradient(90deg,var(--accent-color,#a855f7),transparent);}
        .stat-card:hover{transform:translateY(-3px);border-color:var(--accent-color,#a855f7);
            box-shadow:0 8px 30px rgba(168,85,247,.12);}
        .stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:1rem;}
        .stat-icon svg{width:22px;height:22px;}
        .stat-value{font-family:'Orbitron',sans-serif;font-size:1.875rem;font-weight:800;margin-bottom:.25rem;}
        .stat-label{font-size:.75rem;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:.06em;}
        /* ── Table ── */
        .table-wrap{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;overflow:hidden;}
        table{width:100%;border-collapse:collapse;}
        th{text-align:left;padding:1rem 1.25rem;font-size:.68rem;text-transform:uppercase;
            letter-spacing:.08em;color:var(--text-tertiary);border-bottom:1px solid var(--border-color);
            font-weight:700;background:rgba(168,85,247,.04);}
        td{padding:.875rem 1.25rem;border-bottom:1px solid rgba(168,85,247,.05);vertical-align:middle;font-size:.875rem;}
        tr:last-child td{border-bottom:none;}
        tr:hover td{background:rgba(168,85,247,.03);}
        /* ── Pills ── */
        .pill{display:inline-flex;padding:.25rem .75rem;border-radius:20px;font-size:.68rem;font-weight:700;text-transform:uppercase;white-space:nowrap;}
        .pill-pending{background:rgba(255,170,0,.15);color:#ffaa00;border:1px solid rgba(255,170,0,.3);}
        .pill-confirmed{background:rgba(0,212,255,.15);color:#00d4ff;border:1px solid rgba(0,212,255,.3);}
        .pill-in_progress{background:rgba(168,85,247,.15);color:#a855f7;border:1px solid rgba(168,85,247,.3);}
        .pill-completed{background:rgba(0,255,170,.15);color:#00ffaa;border:1px solid rgba(0,255,170,.3);}
        .pill-cancelled{background:rgba(255,51,102,.15);color:#ff3366;border:1px solid rgba(255,51,102,.3);}
        .pill-active{background:rgba(0,255,170,.15);color:#00ffaa;border:1px solid rgba(0,255,170,.3);}
        .pill-inactive{background:rgba(255,51,102,.15);color:#ff3366;border:1px solid rgba(255,51,102,.3);}
        .pill-admin,.pill-support{background:rgba(168,85,247,.15);color:#c084fc;border:1px solid rgba(168,85,247,.3);}
        .pill-user{background:rgba(0,212,255,.12);color:#7dd3fc;border:1px solid rgba(0,212,255,.2);}
        .pill-provider{background:rgba(0,255,170,.12);color:#6ee7b7;border:1px solid rgba(0,255,170,.2);}
        .pill-verified{background:rgba(0,212,255,.15);color:#00d4ff;border:1px solid rgba(0,212,255,.3);}
        /* ── Buttons ── */
        .btn{display:inline-flex;align-items:center;gap:.5rem;padding:.625rem 1.25rem;border-radius:10px;
            font-weight:600;font-size:.875rem;cursor:pointer;border:none;
            font-family:'Chakra Petch',sans-serif;text-decoration:none;transition:all .3s;}
        .btn svg{width:16px;height:16px;}
        .btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent-alt));color:#fff;}
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(168,85,247,.35);}
        .btn-secondary{background:rgba(168,85,247,.1);color:#c084fc;border:1px solid rgba(168,85,247,.25);}
        .btn-secondary:hover{background:rgba(168,85,247,.2);}
        .btn-danger{background:rgba(255,51,102,.1);color:var(--accent-danger);border:1px solid rgba(255,51,102,.25);}
        .btn-danger:hover{background:rgba(255,51,102,.2);}
        .btn-success{background:rgba(0,255,170,.1);color:var(--accent-green);border:1px solid rgba(0,255,170,.25);}
        .btn-success:hover{background:rgba(0,255,170,.2);}
        .btn-sm{padding:.375rem .875rem;font-size:.8rem;}
        /* ── Forms ── */
        .form-group{margin-bottom:1.25rem;}
        label.form-label{display:block;font-size:.78rem;font-weight:600;color:var(--text-secondary);margin-bottom:.5rem;}
        .form-input,.form-select,.form-textarea{width:100%;padding:.75rem 1rem;background:var(--input-bg);
            border:1px solid var(--border-color);border-radius:10px;color:var(--text-primary);
            font-family:'Chakra Petch',sans-serif;font-size:.875rem;transition:all .3s;outline:none;}
        .form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(168,85,247,.1);}
        .form-input::placeholder{color:var(--text-tertiary);}
        .form-select option{background:#0a0e1a;}
        .form-textarea{resize:vertical;min-height:100px;}
        .form-error{font-size:.75rem;color:var(--accent-danger);margin-top:.25rem;}
        /* ── Filter bar ── */
        .filter-bar{background:var(--card-bg);border:1px solid var(--border-color);border-radius:14px;
            padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;gap:.875rem;flex-wrap:wrap;align-items:flex-end;}
        /* ── Page header ── */
        .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem;}
        .page-title{font-family:'Orbitron',sans-serif;font-size:1.3rem;font-weight:800;}
        .page-sub{font-size:.78rem;color:var(--text-tertiary);margin-top:.25rem;}
        /* ── Action links ── */
        .act{display:inline-flex;align-items:center;gap:.375rem;padding:.35rem .75rem;border-radius:8px;
            font-size:.75rem;font-weight:600;text-decoration:none;transition:all .25s;cursor:pointer;border:none;font-family:'Chakra Petch',sans-serif;}
        .act-view{background:rgba(168,85,247,.1);color:#c084fc;border:1px solid rgba(168,85,247,.2);}
        .act-view:hover{background:rgba(168,85,247,.2);}
        .act-edit{background:rgba(0,212,255,.1);color:#00d4ff;border:1px solid rgba(0,212,255,.2);}
        .act-edit:hover{background:rgba(0,212,255,.2);}
        .act-del{background:rgba(255,51,102,.1);color:#ff3366;border:1px solid rgba(255,51,102,.2);}
        .act-del:hover{background:rgba(255,51,102,.2);}
        .act-ok{background:rgba(0,255,170,.1);color:#00ffaa;border:1px solid rgba(0,255,170,.2);}
        .act-ok:hover{background:rgba(0,255,170,.2);}
        /* ── Grid helpers ── */
        .grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;margin-bottom:1.75rem;}
        .grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-bottom:1.5rem;}
        .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;}
        /* ── Misc ── */
        .section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;}
        .section-title{font-family:'Orbitron',sans-serif;font-size:1rem;font-weight:700;}
        .view-all{font-size:.8rem;color:var(--accent);text-decoration:none;}
        .view-all:hover{text-decoration:underline;}
        .back-link{display:inline-flex;align-items:center;gap:.5rem;color:var(--accent);text-decoration:none;
            font-size:.875rem;font-weight:600;margin-bottom:1.5rem;transition:gap .3s;}
        .back-link:hover{gap:.875rem;}
        .divider{border:none;border-top:1px solid var(--border-color);margin:1.5rem 0;}
        /* ── Notification bell ── */
        .notif-wrap{position:relative;}
        .notif-btn{position:relative;background:rgba(168,85,247,.08);border:1px solid rgba(168,85,247,.18);
            border-radius:10px;width:40px;height:40px;display:flex;align-items:center;justify-content:center;
            color:var(--text-secondary);cursor:pointer;transition:all .25s;}
        .notif-btn:hover,.notif-btn.has-unread{background:rgba(168,85,247,.18);color:#c084fc;border-color:rgba(168,85,247,.45);}
        .notif-btn.has-unread svg{filter:drop-shadow(0 0 6px rgba(168,85,247,.7));}
        .notif-badge{position:absolute;top:-5px;right:-5px;background:var(--accent-danger);color:#fff;
            border-radius:20px;font-size:.6rem;font-weight:800;min-width:18px;height:18px;
            display:flex;align-items:center;justify-content:center;padding:0 4px;
            border:2px solid var(--bg-primary);animation:badgePulse 2s infinite;}
        @keyframes badgePulse{0%,100%{transform:scale(1);}50%{transform:scale(1.15);}}
        .notif-dropdown{position:absolute;top:calc(100% + 10px);right:0;width:360px;
            background:rgba(10,14,26,.98);backdrop-filter:blur(24px);
            border:1px solid var(--border-color);border-radius:16px;
            box-shadow:0 20px 60px rgba(0,0,0,.6);z-index:200;display:none;animation:dropIn .2s ease;}
        .notif-dropdown.open{display:block;}
        @keyframes dropIn{from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:translateY(0);}}
        .notif-header{display:flex;align-items:center;justify-content:space-between;
            padding:1rem 1.25rem;border-bottom:1px solid var(--border-color);}
        .notif-header-title{font-family:'Orbitron',sans-serif;font-size:.875rem;font-weight:700;}
        .notif-mark-all{background:none;border:none;font-size:.75rem;font-weight:600;color:var(--accent);
            cursor:pointer;font-family:'Chakra Petch',sans-serif;padding:.25rem .5rem;border-radius:6px;transition:all .2s;}
        .notif-mark-all:hover{background:rgba(168,85,247,.1);}
        .notif-list{max-height:380px;overflow-y:auto;}
        .notif-list::-webkit-scrollbar{width:4px;}
        .notif-list::-webkit-scrollbar-thumb{background:rgba(168,85,247,.2);border-radius:2px;}
        .notif-item{display:flex;gap:.875rem;padding:.875rem 1.25rem;border-bottom:1px solid rgba(168,85,247,.05);
            cursor:pointer;transition:background .2s;text-decoration:none;position:relative;}
        .notif-item:hover{background:rgba(168,85,247,.05);}
        .notif-item.unread{background:rgba(168,85,247,.04);}
        .notif-item.unread::before{content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);
            width:3px;height:60%;background:var(--accent);border-radius:0 2px 2px 0;}
        .notif-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .notif-icon svg{width:16px;height:16px;}
        .notif-title{font-size:.8rem;font-weight:700;color:var(--text-primary);margin-bottom:.2rem;}
        .notif-msg{font-size:.75rem;color:var(--text-secondary);line-height:1.5;margin-bottom:.25rem;}
        .notif-time{font-size:.7rem;color:var(--text-tertiary);}
        .notif-unread-dot{width:7px;height:7px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:4px;box-shadow:0 0 6px var(--accent);}
        .notif-empty{padding:3rem 1.5rem;text-align:center;color:var(--text-tertiary);font-size:.875rem;}
        .notif-empty svg{width:48px;height:48px;margin:0 auto .75rem;display:block;opacity:.25;}
        .notif-footer{padding:.75rem 1.25rem;border-top:1px solid var(--border-color);text-align:center;}
        .spin{animation:spin 1s linear infinite;}
        @keyframes spin{to{transform:rotate(360deg);}}
        @media(max-width:768px){
            .adm-sidebar{transform:translateX(-100%);}
            .adm-sidebar.open{transform:translateX(0);}
            .adm-main{margin-left:0;}
            .adm-content{padding:1.25rem;}
            .grid-4,.grid-3,.grid-2{grid-template-columns:1fr;}
        }
    </style>
    @stack('styles')
</head>
<body>
<aside class="adm-sidebar" id="admSidebar">
    <div class="adm-logo">
        <div class="adm-logo-badge">
            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Admin Portal
        </div>
        <div class="adm-logo-text">ODOVIN</div>
        <div class="adm-logo-sub">Control Center</div>
    </div>
    <nav class="adm-nav">
        <div class="adm-nav-section">
            <div class="adm-nav-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="adm-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h7v7H3zM14 3h7v4h-7zM14 10h7v11h-7zM3 17h7v4H3z"/></svg>
                Dashboard
            </a>
        </div>

        <div class="adm-nav-section">
            <div class="adm-nav-label">Management</div>
            <a href="{{ route('admin.users.index') }}" class="adm-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Users
                <span class="adm-count">{{ \App\Models\User::where('user_type','user')->count() }}</span>
            </a>
            <a href="{{ route('admin.vehicles.index') }}" class="adm-nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 1h1m8-1h3M4 16h1m12-9l2 2v3h-3M9 6h5"/></svg>
                Vehicles
                <span class="adm-count">{{ \App\Models\Vehicle::count() }}</span>
            </a>
            <a href="{{ route('admin.providers.index') }}" class="adm-nav-link {{ request()->routeIs('admin.providers.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Service Providers
                <span class="adm-count">{{ \App\Models\ServiceProvider::count() }}</span>
            </a>

            <a href="{{ route('admin.jobs.index') }}" class="adm-nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Job Posts
                @php $openJobs = \App\Models\ServiceJobPost::where('status','open')->count() @endphp
                @if($openJobs > 0)
                    <span class="adm-count">{{ $openJobs }} open</span>
                @endif
            </a>
        </div>

        <div class="adm-nav-section">
            <div class="adm-nav-label">Payments</div>
            <a href="{{ route('admin.payments.escrow') }}" class="adm-nav-link {{ request()->routeIs('admin.payments.escrow') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Escrow &amp; Payouts
                @php $overdueEscrow = \App\Models\JobEscrow::overdue()->count() @endphp
                @if($overdueEscrow > 0)
                    <span class="adm-badge">{{ $overdueEscrow }}</span>
                @endif
            </a>
            <a href="{{ route('admin.payments.subscriptions') }}" class="adm-nav-link {{ request()->routeIs('admin.payments.subscriptions') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                Subscriptions
                @php $pastDue = \App\Models\ProviderSubscription::where('status','past_due')->count() @endphp
                @if($pastDue > 0)
                    <span class="adm-badge">{{ $pastDue }}</span>
                @else
                    <span class="adm-count">{{ \App\Models\ProviderSubscription::where('status','active')->count() }} active</span>
                @endif
            </a>
            <a href="{{ route('admin.subscription-plans.index') }}" class="adm-nav-link {{ request()->routeIs('admin.subscription-plans.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Plan Settings
            </a>
        </div>

        <div class="adm-nav-section">
            <div class="adm-nav-label">Analytics</div>
            <a href="{{ route('admin.reports.index') }}" class="adm-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Reports
            </a>
        </div>

        <div class="adm-nav-section">
            <div class="adm-nav-label">System</div>
            <a href="{{ route('admin.settings.index') }}" class="adm-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
        </div>
    </nav>
    <div class="adm-footer">
        <div class="adm-user">
            <div class="adm-avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
            <div>
                <div class="adm-user-name">{{ Str::limit(Auth::user()->name ?? 'Admin', 18) }}</div>
                <div class="adm-user-role">{{ ucfirst(Auth::user()->user_type ?? 'admin') }}</div>
            </div>
        </div>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="adm-logout">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>

<div class="adm-main">
    <header class="adm-topbar">
        <div class="adm-topbar-left">
            <button id="menuBtn" onclick="document.getElementById('admSidebar').classList.toggle('open')"
                style="display:none;background:none;border:none;color:var(--text-primary);cursor:pointer;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="adm-topbar-title">@yield('title','Dashboard')</div>
        </div>
        <div class="adm-topbar-right">
            <div class="adm-status"><div class="adm-status-dot"></div><span>System Online</span></div>
            <span style="font-size:.78rem;color:var(--text-tertiary);">{{ now()->format('D, M d Y') }}</span>
            <div class="notif-wrap" id="notifWrap">
                <button class="notif-btn" id="notifBtn" onclick="toggleNotifDropdown()" aria-label="Notifications">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
                </button>
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <span class="notif-header-title">Notifications</span>
                        <button class="notif-mark-all" onclick="markAllRead()">Mark all read</button>
                    </div>
                    <div class="notif-list" id="notifList">
                        <div class="notif-empty" style="padding:2rem;text-align:center;color:var(--text-tertiary);font-size:.8rem;">Loading…</div>
                    </div>
                    <div class="notif-footer"><span id="notifFooterCount" style="font-size:.75rem;color:var(--text-tertiary);"></span></div>
                </div>
            </div>
        </div>
    </header>
    <main class="adm-content">
        @if(session('success'))
            <div class="flash-success">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash-error">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>
</div>

<script>
if(window.innerWidth<=768) document.getElementById('menuBtn').style.display='block';
const FETCH_URL='{{ route("alerts.fetch") }}',MARK_ALL_URL='{{ route("alerts.mark-all-read") }}',
      MARK_READ_BASE='{{ url("/alerts") }}',CSRF=document.querySelector('meta[name="csrf-token"]')?.content??'';
let notifOpen=false;
function toggleNotifDropdown(){notifOpen=!notifOpen;document.getElementById('notifDropdown').classList.toggle('open',notifOpen);if(notifOpen)fetchNotifications();}
document.addEventListener('click',e=>{if(notifOpen&&!document.getElementById('notifWrap').contains(e.target)){notifOpen=false;document.getElementById('notifDropdown').classList.remove('open');}});
function iconSvg(t){return t==='calendar'?'<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>':'<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';}
function renderNotifications(data){
    const list=document.getElementById('notifList'),badge=document.getElementById('notifBadge'),btn=document.getElementById('notifBtn'),u=data.unread_count;
    u>0?(badge.textContent=u>99?'99+':u,badge.style.display='flex',btn.classList.add('has-unread')):(badge.style.display='none',btn.classList.remove('has-unread'));
    document.getElementById('notifFooterCount').textContent=u>0?`${u} unread`:'All caught up!';
    if(!data.notifications.length){list.innerHTML='<div class="notif-empty"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>No notifications yet</div>';return;}
    list.innerHTML=data.notifications.map(n=>`<a href="${n.action_url??'#'}" class="notif-item ${n.is_read?'':'unread'}" onclick="handleNotifClick(event,${n.id},'${n.action_url??''}')"><div class="notif-icon" style="background:${n.color}1e;color:${n.color};">${iconSvg(n.icon)}</div><div style="flex:1;min-width:0;"><div class="notif-title">${n.title}</div><div class="notif-msg">${n.message}</div><div class="notif-time">${n.time}</div></div>${!n.is_read?'<div class="notif-unread-dot"></div>':''}</a>`).join('');
}
async function fetchNotifications(){
    document.getElementById('notifList').innerHTML='<div class="notif-empty" style="padding:2rem;">Loading…</div>';
    try{renderNotifications(await(await fetch(FETCH_URL,{headers:{'X-Requested-With':'XMLHttpRequest'}})).json());}
    catch(e){document.getElementById('notifList').innerHTML='<div class="notif-empty">Failed to load.</div>';}
}
async function handleNotifClick(e,id,url){e.preventDefault();await fetch(`${MARK_READ_BASE}/${id}/read`,{headers:{'X-Requested-With':'XMLHttpRequest'}});if(url&&url!=='#')window.location.href=url;else fetchNotifications();}
async function markAllRead(){await fetch(MARK_ALL_URL,{method:'POST',headers:{'X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'}});fetchNotifications();}
function pollNotifications(){fetch(FETCH_URL,{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(data=>{const u=data.unread_count,badge=document.getElementById('notifBadge'),btn=document.getElementById('notifBtn');u>0?(badge.textContent=u>99?'99+':u,badge.style.display='flex',btn.classList.add('has-unread')):(badge.style.display='none',btn.classList.remove('has-unread'));if(notifOpen)renderNotifications(data);}).catch(()=>{});}
document.addEventListener('DOMContentLoaded',()=>{pollNotifications();setInterval(pollNotifications,45000);});
</script>
@stack('scripts')
</body>
</html>