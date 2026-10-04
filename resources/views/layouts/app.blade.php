<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TT Dental Lab Attendance System')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        /* ============================================================
           DESIGN TOKENS
        ============================================================ */
        :root {
            --sidebar-w: 240px;
            --topbar-h: 56px;

            /* Brand colours */
            --primary:        #4f46e5;
            --primary-dark:   #4338ca;
            --primary-light:  #eef2ff;
            --primary-glow:   rgba(79,70,229,0.15);

            /* Sidebar */
            --nav-bg:         #0a0e24;
            --nav-border:     rgba(255,255,255,0.08);
            --nav-hover:      rgba(255,255,255,0.06);
            --nav-active:     linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --nav-text:       #94a3b8;
            --nav-text-active:#ffffff;

            /* Surface */
            --bg-body:        #f4f6fb;
            --card-bg:        #ffffff;
            --card-border:    #e2e8f0;
            --card-shadow:    0 1px 4px rgba(0,0,0,0.06);
            --card-shadow-md: 0 4px 16px rgba(0,0,0,0.08);

            /* Text */
            --text-head:      #0f172a;
            --text-body:      #334155;
            --text-muted:     #64748b;
            --text-faint:     #94a3b8;

            /* Status */
            --success:        #16a34a;
            --success-light:  #dcfce7;
            --success-border: rgba(22,163,74,0.2);
            --warning:        #d97706;
            --warning-light:  #fef3c7;
            --warning-border: rgba(217,119,6,0.2);
            --danger:         #dc2626;
            --danger-light:   #fee2e2;
            --danger-border:  rgba(220,38,38,0.2);
            --info:           #0284c7;
            --info-light:     #e0f2fe;
            --info-border:    rgba(2,132,199,0.2);
            --purple:         #7c3aed;
            --purple-light:   #ede9fe;
        }

        /* ============================================================
           RESET
        ============================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-body);
            color: var(--text-body);
            min-height: 100vh;
            display: flex;
            font-size: 14px;
            line-height: 1.6;
        }

        /* ============================================================
           SIDEBAR
        ============================================================ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--nav-bg);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
            border-right: 1px solid var(--nav-border);
            overflow: hidden;
        }

        /* Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 14px 12px;
            border-bottom: 1px solid var(--nav-border);
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-logo {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #ffffff;
            padding: 3px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.2);
            overflow: hidden;
        }

        .brand-logo img {
            width: 100%; height: 100%;
            object-fit: contain; border-radius: 7px;
        }

        .brand-text-wrap h2 {
            font-size: 13.5px; font-weight: 800;
            color: #ffffff; letter-spacing: -0.2px; line-height: 1.2;
        }

        .brand-text-wrap .portal-badge {
            display: inline-block;
            font-size: 8.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.7px;
            color: var(--nav-text); margin-top: 1px;
        }

        /* Nav menu */
        .sidebar-nav {
            flex: 1;
            padding: 10px 8px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        .nav-section-label {
            font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: rgba(148,163,184,0.5);
            padding: 10px 8px 3px;
        }

        .nav-item {
            display: flex; align-items: center; gap: 9px;
            padding: 7px 10px;
            color: var(--nav-text);
            text-decoration: none;
            font-size: 12.5px; font-weight: 600;
            border-radius: 8px;
            transition: all 0.18s ease;
            position: relative;
        }

        .nav-item svg {
            width: 16px; height: 16px;
            stroke-width: 2; flex-shrink: 0;
            opacity: 0.8;
        }

        .nav-item:hover {
            background: var(--nav-hover);
            color: #e2e8f0;
        }

        .nav-item.active {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(79,70,229,0.4);
            font-weight: 700;
        }

        .nav-item.active svg { opacity: 1; }

        .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            border-radius: 999px;
            padding: 1px 5px;
            font-size: 9.5px; font-weight: 700;
            line-height: 1.4;
        }

        .nav-external-icon {
            margin-left: auto;
            opacity: 0.4;
            font-size: 9.5px;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 8px 8px;
            border-top: 1px solid var(--nav-border);
            flex-shrink: 0;
        }

        .user-card {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 8px;
            border-radius: 8px;
            background: rgba(255,255,255,0.04);
            margin-bottom: 6px;
        }

        .user-avatar {
            width: 28px; height: 28px;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 11.5px; color: #fff;
            flex-shrink: 0;
        }

        .user-meta { flex: 1; overflow: hidden; }

        .user-name {
            font-size: 12px; font-weight: 700; color: #f1f5f9;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .user-email {
            font-size: 10px; color: var(--nav-text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        .btn-sign-out {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            width: 100%; padding: 6px 10px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 7px;
            color: #f87171; font-size: 11.5px; font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .btn-sign-out:hover {
            background: #dc2626; color: #fff;
            border-color: #dc2626;
        }

        /* ============================================================
           MAIN WRAPPER
        ============================================================ */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-w));
        }

        /* ============================================================
           TOPBAR
        ============================================================ */
        .topbar {
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--card-border);
            padding: 0 20px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }

        .topbar-left { display: flex; align-items: center; gap: 12px; }

        .mobile-toggle {
            display: none;
            background: none; border: none;
            color: var(--text-body); cursor: pointer;
            padding: 5px; border-radius: 6px;
        }

        .mobile-toggle:hover { background: var(--bg-body); }

        .topbar-title {
            font-size: 15px; font-weight: 800;
            color: var(--text-head); letter-spacing: -0.2px;
        }

        .topbar-right { display: flex; align-items: center; gap: 12px; }

        .clock-chip {
            display: flex; align-items: center; gap: 6px;
            font-size: 11.5px; font-weight: 600; color: var(--text-muted);
            background: var(--bg-body);
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid var(--card-border);
        }

        .clock-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 0 2px var(--success-light);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
        }

        /* ============================================================
           CONTENT AREA
        ============================================================ */
        .content-area {
            padding: 16px 20px;
            flex: 1;
        }

        /* ============================================================
           PAGE HEADER
        ============================================================ */
        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            gap: 16px; margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .page-header-title {
            font-size: 22px; font-weight: 800;
            color: var(--text-head); letter-spacing: -0.4px;
        }

        .page-header-sub {
            font-size: 13px; color: var(--text-muted);
            margin-top: 3px; font-weight: 500;
        }

        .page-header-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        /* ============================================================
           FLASH ALERTS
        ============================================================ */
        .flash-alert {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 13.5px; font-weight: 600;
            margin-bottom: 22px;
            animation: fadeSlideIn 0.3s ease;
        }

        .flash-alert svg { flex-shrink: 0; margin-top: 1px; }
        .flash-alert.is-success { background: var(--success-light); color: var(--success); border: 1px solid var(--success-border); }
        .flash-alert.is-error { background: var(--danger-light); color: var(--danger); border: 1px solid var(--danger-border); }

        @keyframes fadeSlideIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }

        /* ============================================================
           CARDS
        ============================================================ */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 22px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
        }

        .card-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            gap: 12px; margin-bottom: 18px;
        }

        .card-title {
            font-size: 15px; font-weight: 800;
            color: var(--text-head); letter-spacing: -0.2px;
        }

        .card-subtitle {
            font-size: 12.5px; color: var(--text-muted);
            margin-top: 3px; font-weight: 500;
        }

        /* Stat cards */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: var(--card-shadow);
            display: flex; align-items: flex-start; gap: 14px;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .stat-card:hover { box-shadow: var(--card-shadow-md); transform: translateY(-2px); }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon svg { width: 21px; height: 21px; stroke-width: 2; }

        .stat-info { flex: 1; }
        .stat-label { font-size: 11.5px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 28px; font-weight: 900; color: var(--text-head); letter-spacing: -0.5px; line-height: 1.2; margin-top: 2px; }
        .stat-sub { font-size: 11.5px; color: var(--text-faint); margin-top: 4px; font-weight: 500; }

        /* Colour variants */
        .stat-blue   .stat-icon { background: #eff6ff; color: #2563eb; }
        .stat-green  .stat-icon { background: var(--success-light); color: var(--success); }
        .stat-red    .stat-icon { background: var(--danger-light); color: var(--danger); }
        .stat-amber  .stat-icon { background: var(--warning-light); color: var(--warning); }
        .stat-purple .stat-icon { background: var(--purple-light); color: var(--purple); }
        .stat-cyan   .stat-icon { background: var(--info-light); color: var(--info); }

        /* ============================================================
           DATA TABLES
        ============================================================ */
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }

        .data-table thead tr {
            background: #f8fafc;
            border-bottom: 1px solid var(--card-border);
        }

        .data-table thead th {
            padding: 11px 14px;
            text-align: left;
            font-size: 11px; font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 0.6px;
            white-space: nowrap;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.12s ease;
        }

        .data-table tbody tr:hover { background: #fafbfd; }
        .data-table tbody tr:last-child { border-bottom: none; }

        .data-table tbody td {
            padding: 12px 14px;
            color: var(--text-body); vertical-align: middle;
        }

        .data-table tbody td.td-bold { font-weight: 700; color: var(--text-head); }
        .data-table tbody td.td-muted { color: var(--text-muted); }
        .data-table tbody td.td-mono { font-family: 'JetBrains Mono', monospace; }
        .data-table tbody td.td-primary { font-weight: 700; color: var(--primary); }

        .table-wrap { overflow-x: auto; }

        .table-empty {
            padding: 40px; text-align: center;
            color: var(--text-faint); font-size: 13.5px;
        }

        /* ============================================================
           BADGES
        ============================================================ */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .badge-green  { background: var(--success-light); color: var(--success); }
        .badge-red    { background: var(--danger-light);  color: var(--danger); }
        .badge-amber  { background: var(--warning-light); color: var(--warning); }
        .badge-blue   { background: #eff6ff;              color: #2563eb; }
        .badge-purple { background: var(--purple-light);  color: var(--purple); }
        .badge-cyan   { background: var(--info-light);    color: var(--info); }
        .badge-gray   { background: #f1f5f9;              color: #475569; }

        /* ============================================================
           BUTTONS
        ============================================================ */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 13.5px; font-weight: 700;
            text-decoration: none; cursor: pointer;
            border: 1.5px solid transparent;
            transition: all 0.18s ease;
            white-space: nowrap;
            font-family: inherit;
        }

        .btn:disabled { opacity: 0.45; cursor: not-allowed; pointer-events: none; }

        .btn svg { width: 16px; height: 16px; stroke-width: 2.5; flex-shrink: 0; }

        .btn-primary {
            background: var(--primary); color: #fff;
            border-color: var(--primary);
            box-shadow: 0 2px 8px var(--primary-glow);
        }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 14px var(--primary-glow); }

        .btn-success {
            background: var(--success); color: #fff;
            border-color: var(--success);
        }
        .btn-success:hover { background: #15803d; border-color: #15803d; transform: translateY(-1px); }

        .btn-danger {
            background: var(--danger); color: #fff;
            border-color: var(--danger);
        }
        .btn-danger:hover { background: #b91c1c; border-color: #b91c1c; transform: translateY(-1px); }

        .btn-warning {
            background: var(--warning); color: #fff;
            border-color: var(--warning);
        }
        .btn-warning:hover { background: #b45309; border-color: #b45309; transform: translateY(-1px); }

        .btn-secondary {
            background: #1e293b; color: #fff;
            border-color: #1e293b;
        }
        .btn-secondary:hover { background: #0f172a; border-color: #0f172a; transform: translateY(-1px); }

        .btn-outline {
            background: transparent; color: var(--text-body);
            border-color: var(--card-border);
        }
        .btn-outline:hover { background: var(--bg-body); border-color: #cbd5e1; }

        .btn-ghost {
            background: transparent; color: var(--text-muted);
            border-color: transparent; padding: 7px 10px;
        }
        .btn-ghost:hover { background: var(--bg-body); color: var(--text-body); }

        .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }
        .btn-sm svg { width: 13px; height: 13px; }

        .btn-lg { padding: 12px 28px; font-size: 15px; border-radius: 12px; }

        /* ============================================================
           FORM FIELDS
        ============================================================ */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 12.5px; font-weight: 700;
            color: var(--text-body); margin-bottom: 6px;
        }

        .form-label .req { color: var(--danger); margin-left: 2px; }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid var(--card-border);
            border-radius: 10px;
            font-size: 13.5px; font-family: inherit;
            color: var(--text-head);
            background: #fff;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
            outline: none;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .form-hint { font-size: 11.5px; color: var(--text-muted); margin-top: 5px; }

        /* ============================================================
           QUICK ACTION LIST
        ============================================================ */
        .quick-list { display: flex; flex-direction: column; gap: 6px; }

        .quick-item {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            border: 1px solid var(--card-border);
            text-decoration: none;
            color: var(--text-body);
            transition: all 0.18s ease;
        }

        .quick-item:hover { background: var(--bg-body); border-color: #cbd5e1; transform: translateX(3px); }

        .quick-item-icon {
            width: 34px; height: 34px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .quick-item-icon svg { width: 16px; height: 16px; stroke-width: 2; }

        .quick-item-text { flex: 1; }
        .quick-item-text strong { font-size: 13px; font-weight: 700; display: block; }
        .quick-item-text span { font-size: 11.5px; color: var(--text-muted); }

        .quick-item-arrow { color: var(--text-faint); font-size: 12px; }

        /* ============================================================
           ACTION GRID (Staff Dashboard)
        ============================================================ */
        .action-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; }

        .action-btn {
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;
            padding: 22px 16px;
            border-radius: 14px;
            border: 2px solid transparent;
            font-family: inherit; font-size: 14px; font-weight: 800;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .action-btn svg { width: 28px; height: 28px; stroke-width: 2; }

        .action-btn:disabled { opacity: 0.38; cursor: not-allowed; pointer-events: none; }

        .action-btn.ab-in  { background: #f0fdf4; border-color: #86efac; color: #15803d; }
        .action-btn.ab-out { background: #fff1f2; border-color: #fda4af; color: #be123c; }
        .action-btn.ab-brk { background: #fffbeb; border-color: #fcd34d; color: #92400e; }
        .action-btn.ab-end { background: #eff6ff; border-color: #93c5fd; color: #1d4ed8; }

        .action-btn.ab-in:not(:disabled):hover  { background: #dcfce7; box-shadow: 0 6px 18px rgba(21,128,61,0.2); transform: translateY(-3px); }
        .action-btn.ab-out:not(:disabled):hover { background: #ffe4e6; box-shadow: 0 6px 18px rgba(190,18,60,0.2); transform: translateY(-3px); }
        .action-btn.ab-brk:not(:disabled):hover { background: #fef3c7; box-shadow: 0 6px 18px rgba(146,64,14,0.2); transform: translateY(-3px); }
        .action-btn.ab-end:not(:disabled):hover { background: #dbeafe; box-shadow: 0 6px 18px rgba(29,78,216,0.2); transform: translateY(-3px); }

        /* ============================================================
           TODAY STATUS CARD (Staff)
        ============================================================ */
        .status-hero {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e40af 100%);
            border-radius: 16px;
            padding: 26px 28px;
            color: #fff;
            display: flex; align-items: center; justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .status-hero-left {}
        .status-hero-greeting { font-size: 12.5px; font-weight: 600; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.8px; }
        .status-hero-name { font-size: 26px; font-weight: 900; letter-spacing: -0.5px; margin-top: 4px; }
        .status-hero-id { font-size: 12px; opacity: 0.6; margin-top: 3px; }

        .status-hero-right { text-align: right; }
        .status-chip {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 999px;
            padding: 8px 16px;
            font-size: 14px; font-weight: 700;
        }
        .status-chip-dot { width: 8px; height: 8px; border-radius: 50%; }

        /* ============================================================
           FOOTER
        ============================================================ */
        .app-footer {
            padding: 10px 20px;
            text-align: center;
            font-size: 11px; color: var(--text-faint);
            border-top: 1px solid var(--card-border);
            background: var(--card-bg);
        }

        /* ============================================================
           SIDEBAR BACKDROP (Mobile)
        ============================================================ */
        .sidebar-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        /* ============================================================
           RESPONSIVE
        ============================================================ */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-backdrop.active { display: block; }
            .main-wrapper { margin-left: 0; width: 100%; }
            .mobile-toggle { display: flex; }
            .content-area { padding: 20px; }
            .topbar { padding: 0 20px; }
        }

        @media (max-width: 640px) {
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .action-grid { grid-template-columns: 1fr 1fr; }
            .page-header { flex-direction: column; }
            .page-header-actions { width: 100%; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- SIDEBAR BACKDROP -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">

        <!-- Brand -->
        <a href="{{ auth()->check() && auth()->user()->isAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}" class="sidebar-brand">
            <div class="brand-logo">
                <img src="{{ asset('images/tt-dental-logo.png') }}" alt="TT Dental Lab" onerror="this.style.display='none';this.parentElement.innerHTML='<svg width=\'20\' height=\'20\' fill=\'none\' stroke=\'white\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4\'></path></svg>'">
            </div>
            <div class="brand-text-wrap">
                <h2>TT Dental Lab</h2>
                <span class="portal-badge">{{ auth()->check() && auth()->user()->isAdmin() ? '⬡ Admin Portal' : '◈ Staff Portal' }}</span>
            </div>
        </a>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            @if(auth()->check() && auth()->user()->isAdmin())

                <span class="nav-section-label">Overview</span>

                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('employees.index') }}" class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Employees</span>
                </a>

                <span class="nav-section-label">Attendance</span>

                <a href="{{ route('attendance.index') }}" class="nav-item {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Manual Attendance</span>
                </a>

                <a href="{{ route('face-attendance') }}" class="nav-item {{ request()->routeIs('face-attendance*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                    <span>Face Management</span>
                </a>

                <span class="nav-section-label">Reports & Management</span>

                <a href="{{ route('reports.attendance') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Attendance Reports</span>
                </a>

                <a href="{{ route('absences.index') }}" class="nav-item {{ request()->routeIs('absences.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Absences</span>
                </a>

                @php $pendingMcCount = \App\Models\MedicalCertificate::where('status','pending')->count(); @endphp
                <a href="{{ route('admin.mc.index') }}" class="nav-item {{ request()->routeIs('admin.mc.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>MC Review</span>
                    @if($pendingMcCount > 0)
                        <span class="nav-badge">{{ $pendingMcCount }}</span>
                    @endif
                </a>

                <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Settings</span>
                </a>

            @else

                <span class="nav-section-label">My Portal</span>

                <a href="{{ route('staff.dashboard') }}" class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
                    <span>My Dashboard</span>
                </a>

                <a href="{{ route('staff.history') }}" class="nav-item {{ request()->routeIs('staff.history') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Attendance History</span>
                </a>

                <a href="{{ route('staff.mc.index') }}" class="nav-item {{ request()->routeIs('staff.mc.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Medical Certificates</span>
                </a>

                <a href="{{ route('staff.absences.index') }}" class="nav-item {{ request()->routeIs('staff.absences.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2 2 4-4"/></svg>
                    <span>Report Absence</span>
                </a>

                <span class="nav-section-label">Kiosk</span>

                <a href="{{ route('face-verification') }}" class="nav-item" target="_blank">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h3v3M21 3h-3v3M3 21h3v-3M21 21h-3v-3"/></svg>
                    <span>Face Scan Attendance</span>
                    <span class="nav-external-icon">↗</span>
                </a>

            @endif
        </nav>

        <!-- User / Logout -->
        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar" style="background: {{ auth()->check() && auth()->user()->isAdmin() ? '#4f46e5' : '#059669' }};">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="user-meta">
                    <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="user-email">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-sign-out">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>

    </aside>

    <!-- MAIN -->
    <div class="main-wrapper">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="mobile-toggle" id="mobileToggle" aria-label="Open menu">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="topbar-title">@yield('header_title', 'Dashboard')</span>
            </div>

            <div class="topbar-right">
                <div class="clock-chip">
                    <span class="clock-dot"></span>
                    <span id="topbarDate">{{ now()->format('D, d M Y') }}</span>
                    <span style="opacity:0.3;">|</span>
                    <span id="topbarTime">{{ now()->format('H:i:s') }}</span>
                </div>
                @yield('topbar_actions')
            </div>
        </header>

        <!-- CONTENT -->
        <main class="content-area">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="flash-alert is-success">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="flash-alert is-error">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="flash-alert is-error">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong>Please fix the following:</strong>
                        <ul style="margin-top:4px; padding-left:16px; font-size:12.5px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="app-footer">
            © {{ date('Y') }} TT Dental Laboratory Sdn Bhd — Professional Biometric Attendance System
        </footer>

    </div>

    <script>
        // Live clock
        function updateClock() {
            const now = new Date();
            const t = document.getElementById('topbarTime');
            if (t) t.textContent = now.toLocaleTimeString('en-MY', { hour12: false });
        }
        setInterval(updateClock, 1000);

        // Mobile sidebar toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (mobileToggle && sidebar && backdrop) {
            const open = () => { sidebar.classList.add('open'); backdrop.classList.add('active'); };
            const close = () => { sidebar.classList.remove('open'); backdrop.classList.remove('active'); };
            mobileToggle.addEventListener('click', open);
            backdrop.addEventListener('click', close);
        }
    </script>
    @stack('scripts')
</body>
</html>
