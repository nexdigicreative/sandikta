<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpus Sandikta')</title>
    <link rel="icon" href="https://sandikta.sch.id/wp-content/uploads/2024/04/Logo-Sandikta-png.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --primary-dark: #1e3a8a;
            --primary-gradient: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
            --accent: #06b6d4;
            --sidebar-width: 280px;
            --bg-body: #f0f4ff;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 25px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
            --shadow-xl: 0 20px 40px -5px rgba(30, 64, 175, 0.15);
            --radius: 16px;
            --radius-sm: 10px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%);
            z-index: 1050;
            transition: var(--transition);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-brand-icon {
            width: 46px;
            height: 46px;
            background: var(--primary-gradient);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }

        .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            font-size: 18px;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.5);
            font-size: 11px;
            font-weight: 400;
        }

        .sidebar-menu {
            padding: 16px 12px;
        }

        .sidebar-label {
            color: rgba(255, 255, 255, 0.35);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 8px 14px;
            margin-top: 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 2px;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            transform: translateX(4px);
        }

        .sidebar-link.active {
            background: var(--primary-gradient);
            color: #fff;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .sidebar-link i {
            font-size: 18px;
            width: 22px;
            text-align: center;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: var(--transition);
        }

        /* TOPBAR */
        .topbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 32px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .topbar-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-user .avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
        }

        .topbar-user .info {
            text-align: right;
        }

        .topbar-user .info .name {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-primary);
        }

        .topbar-user .info .role {
            font-size: 11px;
            color: var(--text-secondary);
            text-transform: capitalize;
        }

        .btn-sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-primary);
            cursor: pointer;
        }

        /* CONTENT AREA */
        .content-area {
            padding: 28px 32px;
        }

        /* STAT CARDS */
        .stat-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .stat-card .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .bg-gradient-blue {
            background: linear-gradient(135deg, #3b82f6, #1e40af);
        }

        .bg-gradient-cyan {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        }

        .bg-gradient-emerald {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .bg-gradient-amber {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .bg-gradient-rose {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: #fff;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
        }

        .btn-success-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
            color: #fff;
        }

        /* CARDS */
        .card-modern {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-modern .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-modern .card-header h6 {
            font-weight: 700;
            font-size: 16px;
            margin: 0;
        }

        .card-modern .card-body {
            padding: 24px;
        }

        /* TABLE */
        .table-modern {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table-modern thead th {
            background: #f8fafc;
            border-bottom: 2px solid var(--border-color);
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
        }

        .table-modern tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background: #f8fafc;
        }

        /* BADGE */
        .badge-modern {
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        /* BUTTONS */
        .btn-primary-modern {
            background: var(--primary-gradient);
            border: none;
            color: #fff;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.25);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.35);
            color: #fff;
        }

        .btn-outline-modern {
            border: 2px solid var(--primary-light);
            color: var(--primary-light);
            padding: 8px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            background: transparent;
            transition: var(--transition);
        }

        .btn-outline-modern:hover {
            background: var(--primary-light);
            color: #fff;
        }

        /* FORM */
        .form-control-modern {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 14px;
            transition: var(--transition);
            background: #fff;
        }

        .form-control-modern:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* ACTIVITY ITEM */
        .activity-item {
            display: flex;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 6px;
            flex-shrink: 0;
        }

        /* RESPONSIVE - TABLET */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .btn-sidebar-toggle {
                display: block;
            }

            .content-area {
                padding: 20px 16px;
            }

            .topbar {
                padding: 0 16px;
            }

            /* Sidebar overlay backdrop */
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1049;
                backdrop-filter: blur(4px);
            }

            .sidebar-overlay.show {
                display: block;
            }

            /* Card header stacking */
            .card-modern .card-header {
                padding: 16px 18px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .card-modern .card-body {
                padding: 18px;
            }

            /* Table improvements */
            .table-modern thead th {
                padding: 10px 12px;
                font-size: 11px;
                white-space: nowrap;
            }

            .table-modern tbody td {
                padding: 10px 12px;
                font-size: 13px;
            }
        }

        /* RESPONSIVE - PHONE */
        @media (max-width: 576px) {
            .topbar {
                height: 56px;
                padding: 0 12px;
                gap: 8px;
            }

            .topbar-title {
                font-size: 16px;
            }

            .topbar-user .info {
                display: none;
            }

            .topbar-user .avatar {
                width: 36px;
                height: 36px;
                border-radius: 10px;
            }

            .content-area {
                padding: 16px 12px;
            }

            /* Stat cards */
            .stat-card {
                padding: 18px;
            }

            .stat-card .stat-icon {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                font-size: 20px;
            }

            .stat-card .stat-value {
                font-size: 22px;
            }

            .stat-card .stat-label {
                font-size: 12px;
            }

            /* Page headers - stack title and buttons */
            .d-flex.justify-content-between.align-items-center.mb-4 {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 12px;
            }

            .d-flex.justify-content-between.align-items-center.mb-4>.d-flex.gap-2 {
                width: 100%;
            }

            .d-flex.justify-content-between.align-items-center.mb-4>.d-flex.gap-2 .btn {
                flex: 1;
                text-align: center;
            }

            /* Cards */
            .card-modern {
                border-radius: 14px;
            }

            .card-modern .card-header {
                padding: 14px 16px;
            }

            .card-modern .card-header h6 {
                font-size: 14px;
            }

            .card-modern .card-body {
                padding: 16px;
            }

            /* Buttons */
            .btn-primary-modern,
            .btn-outline-modern,
            .btn-success-modern {
                padding: 10px 16px;
                font-size: 13px;
                border-radius: 10px;
            }

            /* Forms */
            .form-control-modern {
                padding: 10px 14px;
                font-size: 14px;
                border-radius: 10px;
            }

            /* Table */
            .table-modern thead th {
                padding: 8px 10px;
                font-size: 10px;
                letter-spacing: 0.3px;
            }

            .table-modern tbody td {
                padding: 10px;
                font-size: 12px;
            }

            /* Action buttons in table */
            .table-modern .d-flex.gap-1 {
                gap: 2px !important;
            }

            .table-modern .btn-sm {
                padding: 4px 8px !important;
                font-size: 11px;
            }

            /* Badges */
            .badge-modern {
                padding: 4px 8px;
                font-size: 10px;
            }

            /* Filter forms */
            .card-modern .card-body.py-3 .row.g-2 {
                gap: 8px !important;
            }

            /* Activity items */
            .activity-item {
                gap: 10px;
                padding: 10px 0;
            }

            /* Modal */
            .modal-content {
                margin: 8px;
                border-radius: 16px !important;
            }

            .modal-header {
                padding: 18px !important;
            }

            .modal-body {
                padding: 18px !important;
            }

            .modal-title {
                font-size: 16px !important;
            }

            /* Pagination */
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
                gap: 4px;
            }

            .pagination .page-item .page-link {
                padding: 6px 10px;
                font-size: 12px;
            }
        }

        /* Pagination General */
        .pagination {
            margin-bottom: 0;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-weight: 500;
            padding: 8px 14px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pagination .page-link:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(30, 64, 175, 0.2);
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.4;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            border-radius: 8px;
        }

        /* Fix SVG arrow sizes in pagination */
        .pagination .page-link svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .pagination-wrapper {
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: thin;
        }

        .pagination-wrapper::-webkit-scrollbar {
            height: 4px;
        }

        .pagination-wrapper::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        /* Tailwind pagination override (Laravel default) */
        nav[role="navigation"] {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        nav[role="navigation"]>div {
            width: 100%;
        }

        nav[role="navigation"]>div:first-child {
            text-align: center;
            font-size: 13px;
            color: var(--text-secondary);
        }

        /* Fix the flex row with prev/next arrows */
        nav[role="navigation"]>div:last-child>div {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        nav[role="navigation"] span[aria-current="page"]>span {
            background: var(--primary-gradient) !important;
            border-color: transparent !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 10px rgba(30, 64, 175, 0.2);
            padding: 8px 14px !important;
            font-size: 14px !important;
            min-width: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        nav[role="navigation"] a,
        nav[role="navigation"] span:not([aria-current="page"])>span {
            border-radius: 8px !important;
            padding: 8px 14px !important;
            font-size: 14px !important;
            min-width: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        nav[role="navigation"] a svg,
        nav[role="navigation"] span svg {
            width: 16px !important;
            height: 16px !important;
            flex-shrink: 0;
        }

        /* ANIMATIONS */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.5s ease forwards;
        }

        .delay-1 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .delay-2 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .delay-3 {
            animation-delay: 0.3s;
            opacity: 0;
        }

        .delay-4 {
            animation-delay: 0.4s;
            opacity: 0;
        }

        @media print {
            body {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    @auth
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand"><img src="https://sandikta.sch.id/wp-content/uploads/2024/04/Logo-Sandikta-png.png"
                    alt="Logo" style="width: 50px; height: 50px;">
                <div>
                    <h5>Perpus Sandikta</h5>
                    <small>Perpustakaan Digital</small>
                </div>
            </div>
            <nav class="sidebar-menu">
                @if(Auth::user()->role === 'superadmin')
                    <div class="sidebar-label">MENU UTAMA</div>
                    <a href="{{ route('superadmin.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"><i
                            class="bi bi-grid-1x2-fill"></i>Dashboard</a>
                    <a href="{{ route('superadmin.admins.index') }}"
                        class="sidebar-link {{ request()->routeIs('superadmin.admins.*') ? 'active' : '' }}"><i
                            class="bi bi-people-fill"></i>Kelola Admin</a>
                    <a href="{{ route('admin.ebooks.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.ebooks.*') ? 'active' : '' }}"><i
                            class="bi bi-book-fill"></i>Kelola eBook</a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i
                            class="bi bi-tags-fill"></i>Kategori</a>
                    <a href="{{ route('admin.users.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i
                            class="bi bi-people-fill"></i>Anggota</a>
                    <div class="sidebar-label">SISTEM</div>
                    <a href="{{ route('superadmin.logs.index') }}"
                        class="sidebar-link {{ request()->routeIs('superadmin.logs.index') ? 'active' : '' }}"><i
                            class="bi bi-shield-lock-fill"></i>Log Aktivitas</a>
                    <a href="{{ route('superadmin.logs.security') }}"
                        class="sidebar-link {{ request()->routeIs('superadmin.logs.security') ? 'active' : '' }}"><i
                            class="bi bi-shield-check"></i>Log Keamanan</a>
                    <a href="{{ route('superadmin.logs.failed') }}"
                        class="sidebar-link {{ request()->routeIs('superadmin.logs.failed') ? 'active' : '' }}"><i
                            class="bi bi-shield-exclamation"></i>Login Gagal</a>
                    <div class="sidebar-label">PERPUSTAKAAN</div>
                    <a href="{{ route('ebooks.index') }}"
                        class="sidebar-link {{ request()->routeIs('ebooks.index') && !request()->routeIs('admin.ebooks.*') ? 'active' : '' }}"><i
                            class="bi bi-journal-richtext"></i>Koleksi eBook</a>
                    <div class="sidebar-label">AKUN</div>
                    <a href="{{ route('profile.show') }}"
                        class="sidebar-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"><i
                            class="bi bi-person-circle"></i>Profil Saya</a>
                @elseif(Auth::user()->role === 'admin')
                    <div class="sidebar-label">MENU UTAMA</div>
                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i
                            class="bi bi-grid-1x2-fill"></i>Dashboard</a>
                    <a href="{{ route('admin.ebooks.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.ebooks.*') ? 'active' : '' }}"><i
                            class="bi bi-book-fill"></i>Kelola eBook</a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i
                            class="bi bi-tags-fill"></i>Kategori</a>
                    <a href="{{ route('admin.users.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i
                            class="bi bi-people-fill"></i>Anggota</a>
                    <div class="sidebar-label">PERPUSTAKAAN</div>
                    <a href="{{ route('ebooks.index') }}"
                        class="sidebar-link {{ request()->routeIs('ebooks.index') && !request()->routeIs('admin.ebooks.*') ? 'active' : '' }}"><i
                            class="bi bi-journal-richtext"></i>Koleksi eBook</a>
                    <div class="sidebar-label">AKUN</div>
                    <a href="{{ route('profile.show') }}"
                        class="sidebar-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"><i
                            class="bi bi-person-circle"></i>Profil Saya</a>
                @elseif(Auth::user()->role === 'user')
                    <div class="sidebar-label">MENU UTAMA</div>
                    <a href="{{ route('user.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}"><i
                            class="bi bi-grid-1x2-fill"></i>Dashboard</a>
                    <a href="{{ route('ebooks.index') }}"
                        class="sidebar-link {{ request()->routeIs('ebooks.*') && !request()->routeIs('admin.ebooks.*') ? 'active' : '' }}"><i
                            class="bi bi-journal-richtext"></i>Koleksi eBook</a>
                    <div class="sidebar-label">AKUN</div>
                    <a href="{{ route('profile.show') }}"
                        class="sidebar-link {{ request()->routeIs('profile.show') ? 'active' : '' }}"><i
                            class="bi bi-person-circle"></i>Profil Saya</a>
                @endif
            </nav>
        </aside>

        <!-- Sidebar overlay for mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn-sidebar-toggle" onclick="toggleSidebar()">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="topbar-user">
                    <div class="info">
                        <div class="name">{{ Auth::user()->name }}</div>
                        <div class="role">{{ Auth::user()->role }}</div>
                    </div>
                    <div class="avatar">
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-dark" data-bs-toggle="dropdown"><i
                                class="bi bi-three-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i
                                        class="bi bi-person me-2"></i>Profil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i
                                            class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            <div class="content-area">

                @yield('content')
            </div>
        </div>
    @else
        @yield('content')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{!! session('success') !!}',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Tutup'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '{!! session('error') !!}',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Tutup'
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                html: '{!! session('warning') !!}',
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'Tutup'
            });
        @endif

        // Auto-dismiss alerts
        document.querySelectorAll('.alert').forEach(a => setTimeout(() => { if (a) new bootstrap.Alert(a).close(); }, 5000));

        // Sidebar toggle with overlay
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        }

        // Close sidebar on overlay click
        document.getElementById('sidebarOverlay')?.addEventListener('click', closeSidebar);

        // Close sidebar when a menu link is clicked (mobile)
        document.querySelectorAll('.sidebar-link').forEach(function (link) {
            link.addEventListener('click', closeSidebar);
        });

        // Confirm delete
        function confirmDelete(formId, title = 'Yakin ingin menghapus?') {
            Swal.fire({
                title: title, text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#e11d48', cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
        }
        // Confirm action
        function confirmAction(formId, title, text, icon = 'question', confirmText = 'Ya', confirmColor = '#3b82f6') {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
        }

        // Real-time Dashboard Auto-Refresh
        @if(request()->routeIs('superadmin.dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('user.dashboard'))
        setInterval(() => {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.content-area');
                
                if (newContent) {
                    // Destroy charts if they exist to prevent memory leaks and duplication
                    if (typeof Chart !== 'undefined') {
                        Object.values(Chart.instances).forEach(chart => chart.destroy());
                    }
                    
                    // Replace content seamlessly
                    document.querySelector('.content-area').innerHTML = newContent.innerHTML;
                    
                    // Re-execute dashboard scripts to re-initialize charts with new data
                    const newScripts = doc.querySelectorAll('script.dashboard-script');
                    newScripts.forEach(script => {
                        const newScript = document.createElement('script');
                        newScript.textContent = script.textContent;
                        newScript.className = 'dashboard-script';
                        document.body.appendChild(newScript);
                        // Clean up right after execution
                        setTimeout(() => newScript.remove(), 100);
                    });
                }
            })
            .catch(err => console.error('Dashboard refresh failed:', err));
        }, 10000); // 10 seconds refresh rate
        @endif
    </script>
    @stack('scripts')
</body>

</html>