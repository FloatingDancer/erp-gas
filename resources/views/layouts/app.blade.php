<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NAGA SAKTI JAYA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- Scripts loaded early so they are available for inline views -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.fn.dataTable.ext.errMode = 'none';
    </script>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        /* Global style for select fields using form-input class */
        select.form-input {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 14px center !important;
            background-size: 16px !important;
            padding-right: 40px !important;
        }

        /* =============================================
           SIDEBAR — Minimalis
        ============================================= */
        .sidebar {
            width: 220px;
            height: 100vh;
            background: #0f172a;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1100;
            transition: width 0.25s ease;
        }

        /* Logo / Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            text-decoration: none;
        }

        .sidebar-brand-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            font-size: 15px;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: 0.3px;
        }

        .sidebar-brand-sub {
            font-size: 10px;
            color: #64748b;
            font-weight: 400;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Nav section label */
        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #475569;
            padding: 16px 18px 6px;
        }

        /* Nav items */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 6px 10px;
        }

        .sidebar-nav::-webkit-scrollbar { width: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 8px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: background 0.15s, color 0.15s;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: #e2e8f0;
        }

        .nav-item.active {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: #6366f1;
            border-radius: 0 3px 3px 0;
        }

        .nav-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            background: rgba(255,255,255,0.05);
        }

        .nav-item.active .nav-icon {
            background: rgba(99, 102, 241, 0.2);
        }

        /* Sidebar bottom: mini user card */
        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 12px 14px;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .sidebar-user-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #cbd5e1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 11px;
            color: #475569;
        }

        /* =============================================
           TOPBAR
        ============================================= */
        .topbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 220px;
            height: 62px;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid #e2e8f0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        /* Topbar left: page breadcrumb / title */
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-greeting {
            font-size: 13.5px;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        @media (max-width: 576px) {
            .topbar-greeting {
                max-width: 140px;
            }
        }

        .topbar-greeting span {
            font-weight: 600;
            color: #1e293b;
        }

        /* Topbar right: profile area */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Notification bell */
        .topbar-bell {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
        }

        .topbar-bell:hover { background: #f1f5f9; }

        /* Profile trigger */
        .topbar-profile-trigger {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px 5px 6px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            position: relative;
        }

        .topbar-profile-trigger:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        @media (max-width: 576px) {
            .topbar-profile-trigger {
                padding: 0;
                border: none;
                background: transparent;
            }
            .topbar-profile-trigger:hover {
                background: transparent;
                border-color: transparent;
            }
        }

        /* Avatar circle */
        .topbar-profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .topbar-profile-info {
            line-height: 1.2;
        }

        .topbar-profile-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
        }
        @media (max-width: 576px) {
            .topbar-profile-name {
                max-width: 80px;
            }
        }

        .topbar-profile-role {
            font-size: 11px;
            color: #94a3b8;
        }

        .topbar-profile-chevron {
            font-size: 10px;
            color: #94a3b8;
            margin-left: 2px;
            transition: transform 0.2s;
        }

        /* Dropdown menu */
        .topbar-profile-dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: white;
            min-width: 210px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            border: 1px solid #e2e8f0;
            padding: 6px;
            z-index: 2000;
        }

        .topbar-profile-dropdown-menu.open { display: block; }

        /* Dropdown header */
        .dropdown-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 10px 12px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 4px;
        }

        .dropdown-header-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .dropdown-header-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .dropdown-header-email {
            font-size: 11.5px;
            color: #94a3b8;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 130px;
        }

        /* Dropdown items */
        .dd-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            border-radius: 8px;
            text-decoration: none;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            cursor: pointer;
            transition: background 0.15s;
        }

        .dd-item:hover { background: #f8fafc; color: #1e293b; }

        .dd-item-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .dd-item.danger { color: #dc2626; }
        .dd-item.danger:hover { background: #fef2f2; }
        .dd-item.danger .dd-item-icon { background: #fee2e2; }

        .dd-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 4px 0;
        }

        /* =============================================
           MAIN CONTENT
        ============================================= */
        .main-content {
            margin-left: 220px;
            margin-top: 62px;
            padding: 28px;
            min-height: calc(100vh - 62px);
            display: flex;
            flex-direction: column;
        }

        /* Global: center all form-card (create/edit pages) */
        .form-card {
            margin-left: auto;
            margin-right: auto;
        }

        /* DataTables Custom Styles */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 16px;
            font-size: 13px;
            color: #475569;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 16px;
            font-size: 13px;
            color: #475569;
        }
        .dataTables_wrapper .form-control {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 13px;
            padding: 5px 10px;
            outline: none;
            display: inline-block;
            width: auto;
        }
        .dataTables_wrapper .form-select {
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            font-size: 13px;
            padding: 5px 32px 5px 12px;
            outline: none;
            display: inline-block;
            width: auto;
            min-width: 70px;
            margin: 0 6px;
        }
        .dataTables_wrapper .form-control:focus,
        .dataTables_wrapper .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }
        /* Notifications Styling */
        .notification-dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: white;
            width: 340px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            border: 1px solid #e2e8f0;
            padding: 12px;
            z-index: 2000;
            display: none;
            text-align: left;
            cursor: default;
        }
        @media (max-width: 576px) {
            .notification-dropdown-menu {
                width: 280px;
                right: -120px;
            }
        }
        .bell-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: white;
            font-size: 9px;
            font-weight: 700;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1.5px solid white;
        }
        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 8px;
            border-radius: 8px;
            transition: background 0.12s;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f8fafc;
        }
        .notification-item:hover {
            background: #f8fafc;
        }
        .notification-item-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }
        .notification-item-icon.warning { background: #fef3c7; color: #d97706; }
        .notification-item-icon.info { background: #dbeafe; color: #2563eb; }
        .notification-item-icon.danger { background: #fee2e2; color: #dc2626; }
        .notification-item-icon.success { background: #dcfce7; color: #16a34a; }
        .notification-item-content {
            font-size: 11.5px;
            color: #374151;
            line-height: 1.4;
            text-align: left;
        }
        .notification-item-time {
            font-size: 9.5px;
            color: #94a3b8;
            margin-top: 2px;
        }
        
        .dataTables_wrapper .dataTables_paginate .page-link {
            border-radius: 6px;
            margin: 0 2px;
            border-color: #e2e8f0;
            color: #475569;
            font-size: 13px;
        }
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }
        .card-clean {
            padding: 24px;
        }
        @media (max-width: 992px) {
            .btn-sidebar-toggle {
                display: block !important;
            }
            .sidebar {
                transform: translateX(-100%);
                width: 240px;
                transition: transform 0.3s ease;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .topbar {
                left: 0 !important;
                padding: 0 16px !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 16px !important;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.4);
                z-index: 1050;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body>

    <!-- =================== SIDEBAR =================== -->
    @php
        $currentPath = request()->path();
        $navItems = [
            ['path' => 'dashboard',  'label' => 'Dashboard',  'icon' => '⊞'],
            ['path' => 'customers',  'label' => 'Customers',  'icon' => '👤'],
            ['path' => 'products',   'label' => 'Products',   'icon' => '📦'],
            ['path' => 'orders',     'label' => 'Orders',     'icon' => '🛒'],
            ['path' => 'deliveries', 'label' => 'Deliveries', 'icon' => '🚚'],
            ['path' => 'drivers',    'label' => 'Drivers',    'icon' => '👷'],
            ['path' => 'invoices',   'label' => 'Invoices',   'icon' => '🧾'],
            ['path' => 'payments',   'label' => 'Payments',   'icon' => '💳'],
        ];
        if (auth()->user()->isDriver()) {
            $navItems = [
                ['path' => 'deliveries', 'label' => 'Deliveries', 'icon' => '🚚'],
            ];
        }
        $initials = strtoupper(substr(auth()->user()->name, 0, 1));
        if (str_contains(auth()->user()->name, ' ')) {
            $parts = explode(' ', auth()->user()->name);
            $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }
    @endphp

    <aside class="sidebar">

        {{-- Brand --}}
        <a href="{{ auth()->user()->isDriver() ? '/deliveries' : '/dashboard' }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">⛽</div>
            <div>
                <div class="sidebar-brand-text">NAGA SAKTI JAYA</div>
                <div class="sidebar-brand-sub">Management System</div>
            </div>
        </a>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu</div>

            @foreach($navItems as $item)
                @php
                    $isActive = $currentPath === $item['path'] ||
                                str_starts_with($currentPath, $item['path'] . '/');
                @endphp
                <a href="/{{ $item['path'] }}"
                   class="nav-item {{ $isActive ? 'active' : '' }}">
                    <span class="nav-icon">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Footer: Mini User --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">{{ $initials }}</div>
                <div style="overflow:hidden;">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>

    </aside>

    <!-- =================== TOPBAR =================== -->
    <header class="topbar">

        {{-- Left: Greeting --}}
        <div class="topbar-left">
            <button class="btn-sidebar-toggle" id="sidebarToggle" style="display:none; background:transparent; border:none; font-size:20px; color:#374151; cursor:pointer; padding:4px 8px; margin-right:8px;">
                ☰
            </button>
            <div class="topbar-greeting">
                <span class="d-none d-sm-inline">Selamat datang, </span><span>{{ auth()->user()->name }}</span> 👋
            </div>
        </div>

        {{-- Right: Bell + Profile --}}
        <div class="topbar-right">

            {{-- Notification Bell --}}
            <div class="topbar-bell" id="notificationTrigger" title="Notifikasi" style="position:relative; cursor:pointer;">
                🔔
                <span class="bell-badge" id="notificationBadge" style="display:none;">0</span>
                
                {{-- Dropdown Menu --}}
                <div class="notification-dropdown-menu" id="notificationDropdown" onclick="event.stopPropagation();">
                    <div class="notification-header" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:8px; margin-bottom:8px;">
                        <span class="notification-header-title" style="font-size:13.5px; font-weight:700; color:#0f172a;">🔔 Notifikasi Sistem</span>
                        <span id="notificationCountText" style="font-size:11.5px; color:#64748b;">0 baru</span>
                    </div>
                    <div class="notification-list" id="notificationList" style="max-height:260px; overflow-y:auto; display:flex; flex-direction:column; gap:4px;">
                        <div class="notification-empty" style="text-align:center; padding:24px 0; color:#94a3b8; font-size:12.5px;">Tidak ada notifikasi baru</div>
                    </div>
                </div>
            </div>

            {{-- Profile Trigger --}}
            <div class="topbar-profile-trigger" id="profileTrigger" style="position:relative;">
                <div class="topbar-profile-avatar">{{ $initials }}</div>
                <div class="topbar-profile-info d-none d-sm-block">
                    <div class="topbar-profile-name">{{ auth()->user()->name }}</div>
                    <div class="topbar-profile-role d-none d-sm-block">{{ auth()->user()->isDriver() ? 'Driver' : 'Administrator' }}</div>
                </div>
                <span class="topbar-profile-chevron d-none d-sm-inline" id="profileChevron">▼</span>

                {{-- Dropdown Menu --}}
                <div class="topbar-profile-dropdown-menu" id="profileDropdown">

                    {{-- Header --}}
                    <div class="dropdown-header">
                        <div class="dropdown-header-avatar">{{ $initials }}</div>
                        <div style="overflow:hidden;">
                            <div class="dropdown-header-name">{{ auth()->user()->name }}</div>
                            <div class="dropdown-header-email" title="{{ auth()->user()->email }}">
                                {{ auth()->user()->email }}
                            </div>
                        </div>
                    </div>

                    {{-- Items --}}
                    <a href="{{ route('profile.index') }}" class="dd-item">
                        <span class="dd-item-icon">👤</span>
                        My Profile
                    </a>
                    <a href="{{ route('profile.edit') }}" class="dd-item">
                        <span class="dd-item-icon">⚙️</span>
                        Account Settings
                    </a>

                    <div class="dd-divider"></div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="button" id="logout-btn" class="dd-item danger">
                            <span class="dd-item-icon">🚪</span>
                            Logout
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </header>

    <!-- =================== CONTENT =================== -->
    <main class="main-content">
        <div style="flex: 1; width: 100%;">
            @yield('content')
        </div>
        <footer style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #94a3b8; letter-spacing: 0.3px;">
            &copy; {{ now()->year }} NAGA SAKTI JAYA. All rights reserved.
        </footer>
    </main>


    <script>
        const profileTrigger = document.getElementById('profileTrigger');
        const profileDropdown = document.getElementById('profileDropdown');
        const profileChevron  = document.getElementById('profileChevron');
        const logoutBtn       = document.getElementById('logout-btn');
        const logoutForm      = document.getElementById('logout-form');

        // Toggle dropdown
        profileTrigger.addEventListener('click', function (e) {
            const isOpen = profileDropdown.classList.toggle('open');
            profileChevron.style.transform = isOpen ? 'rotate(180deg)' : '';
        });

        // Close when clicking outside
        document.addEventListener('click', function (e) {
            if (!profileTrigger.contains(e.target)) {
                profileDropdown.classList.remove('open');
                profileChevron.style.transform = '';
            }
        });

        // Logout with SweetAlert
        logoutBtn.addEventListener('click', function () {
            profileDropdown.classList.remove('open');
            Swal.fire({
                title: 'Logout?',
                text: 'Apakah kamu yakin ingin logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) logoutForm.submit();
            });
        });

        // =============================================
        // NOTIFIKASI SISTEM (REAL-TIME ALERTS)
        // =============================================
        const notificationTrigger = document.getElementById('notificationTrigger');
        const notificationDropdown = document.getElementById('notificationDropdown');

        function loadNotifications() {
            $.ajax({
                url: '/notifications',
                method: 'GET',
                success: function (data) {
                    const badge = $('#notificationBadge');
                    const list = $('#notificationList');
                    const countText = $('#notificationCountText');
                    
                    if (data && data.length > 0) {
                        badge.text(data.length).show();
                        countText.text(data.length + ' baru');
                        
                        let html = '';
                        data.forEach(item => {
                            let iconEmoji = '📢';
                            let iconClass = 'info';
                            if (item.type === 'warning') { iconEmoji = '⚠️'; iconClass = 'warning'; }
                            if (item.type === 'danger') { iconEmoji = '🚨'; iconClass = 'danger'; }
                            if (item.type === 'success') { iconEmoji = '🚚'; iconClass = 'success'; }
                            
                            html += `
                                <div class="notification-item">
                                    <div class="notification-item-icon ${iconClass}">${iconEmoji}</div>
                                    <div class="notification-item-content">
                                        <div style="font-weight:600; color:#0f172a; font-size:12px;">${item.title}</div>
                                        <div style="color:#475569; font-size:11.5px; margin-top:2px;">${item.message}</div>
                                        <div class="notification-item-time">${item.time}</div>
                                    </div>
                                </div>
                            `;
                        });
                        list.html(html);
                    } else {
                        badge.hide();
                        countText.text('0 baru');
                        list.html('<div class="notification-empty" style="text-align:center; padding:24px 0; color:#94a3b8; font-size:12px;">Tidak ada notifikasi baru</div>');
                    }
                },
                error: function (err) {
                    console.error('Failed to load notifications', err);
                }
            });
        }

        // Jalankan saat load awal
        loadNotifications();
        // Cek berkala setiap 30 detik
        setInterval(loadNotifications, 30000);

        // Toggle panel notifikasi
        notificationTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            const isDropdownOpen = notificationDropdown.style.display === 'block';
            notificationDropdown.style.display = isDropdownOpen ? 'none' : 'block';
            
            // Tutup dropdown profil jika dropdown notifikasi dibuka
            profileDropdown.classList.remove('open');
            profileChevron.style.transform = '';
        });

        // Mobile Sidebar Toggle
        const sidebarToggle   = document.getElementById('sidebarToggle');
        const sidebar         = document.querySelector('.sidebar');
        const sidebarOverlay  = document.getElementById('sidebarOverlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.add('open');
                sidebarOverlay.classList.add('show');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                sidebarOverlay.classList.remove('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992 && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.remove('show');
                }
            }
        });
    </script>
    
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
</body>
</html>