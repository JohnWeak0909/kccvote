<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Student Management — Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('css/theme-unified.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('css/burger-menu.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <script src="<?= base_url('js/burger-menu.js') ?>" defer></script>
    <script src="<?= base_url('js/main.js') ?>" defer></script>
    <style>
        :root {
            color-scheme: dark;
            --bg-page: #071A36;
            --bg-card: #102F55;
            --bg-table: #0D2948;
            --bg-table-header: #18518A;
            --btn-yellow: #FFD43B;
            --text-white: #FFFFFF;
            --text-secondary: #B8C7DA;
            --border: #28527A;
            --success: #22C55E;
            --danger: #EF4444;
            --shadow: 0 24px 60px rgba(0, 0, 0, 0.22);
            --radius: 20px;
        }

        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-page);
            color: var(--text-white);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body.dashboard-page {
            background: var(--bg-page);
            overflow: visible !important;
        }

        html, body {
            min-height: 100%;
            overflow: visible !important;
        }

        .admin-shell {
            width: 100%;
            min-height: 100vh;
            max-height: calc(100vh - 16px);
            padding: 24px 28px 32px;
            margin-left: 0;
            display: flex;
            justify-content: center;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch;
        }

        .student-dashboard {
            width: 100%;
            max-width: 1480px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            padding: 24px 28px;
            border-radius: var(--radius);
            background: rgba(16, 47, 85, 0.96);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .page-title-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 0;
        }

        .page-title {
            margin: 0;
            font-size: 2.25rem;
            line-height: 1.05;
            color: var(--text-white);
            letter-spacing: -0.02em;
        }

        .page-copy {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.95rem;
            max-width: 700px;
            line-height: 1.7;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.18s ease, background 0.18s ease;
            white-space: nowrap;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            padding: 0 20px;
            min-height: 46px;
            background: var(--btn-yellow);
            color: #0F172A;
            box-shadow: 0 14px 30px rgba(255, 212, 59, 0.18);
        }

        .btn-primary:hover {
            background: #FACC15;
        }

        .btn-secondary {
            padding: 0 18px;
            min-height: 44px;
            background: rgba(255, 255, 255, 0.08);
            color: var(--text-white);
            border: 1px solid rgba(255,255,255,0.14);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        .btn-danger {
            padding: 0 18px;
            min-height: 44px;
            background: var(--danger);
            color: #FFFFFF;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .summary-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 20px;
            border-radius: var(--radius);
            background: rgba(16, 47, 85, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.16);
        }

        .summary-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255, 212, 59, 0.15);
            color: var(--btn-yellow);
            display: grid;
            place-items: center;
            font-size: 1.15rem;
        }

        .summary-copy {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .summary-label {
            color: var(--text-secondary);
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            font-weight: 700;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--text-white);
            line-height: 1;
        }

        .summary-desc {
            color: var(--text-secondary);
            font-size: 0.92rem;
        }

        .filter-panel {
            padding: 18px 20px;
            border-radius: var(--radius);
            background: rgba(16, 47, 85, 0.96);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .filter-row {
            display: grid;
            grid-template-columns: 1.6fr 1.2fr 1fr 1fr 1fr 1fr 0.9fr;
            gap: 14px;
            align-items: end;
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-field label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .filter-field input,
        .filter-field select {
            width: 100%;
            height: 44px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: #0f1f35;
            color: var(--text-white);
            padding: 0 14px;
            font-size: 0.95rem;
        }

        .filter-field input::placeholder {
            color: rgba(184, 199, 218, 0.68);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .bulk-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            padding: 18px 20px;
            border-radius: var(--radius);
            background: rgba(16, 47, 85, 0.96);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .bulk-left {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-secondary);
            font-weight: 600;
            white-space: nowrap;
        }

        .bulk-left input {
            width: 18px;
            height: 18px;
            accent: var(--btn-yellow);
        }

        .bulk-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-card {
            border-radius: var(--radius);
            background: rgba(16, 47, 85, 0.96);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: visible;
            min-height: 220px;
            padding-bottom: 18px;
        }

        .table-inner {
            overflow-x: auto;
            overflow-y: visible;
            width: 100%;
        }

        .student-table {
            width: 100%;
            min-width: 1080px;
            border-collapse: separate;
            border-spacing: 0;
            background: var(--bg-table);
        }

        .student-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: var(--bg-table-header);
            color: var(--text-white);
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.14);
            white-space: nowrap;
        }

        .student-table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .student-table tbody tr:hover {
            background: transparent;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.06);
        }

        .student-table td {
            padding: 12px 12px;
            color: var(--text-white);
            font-size: 0.92rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        .student-table td.photo-col {
            width: 64px;
        }

        .password-cell {
            min-width: 260px;
            max-width: 420px;
            width: 260px;
        }

        .password-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            max-width: 100%;
        }

        .password-text {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
            letter-spacing: 0.06em;
            overflow: visible;
            text-overflow: clip;
            white-space: nowrap;
            color: var(--text-white);
            flex: 1 1 auto;
        }

        .password-actions {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .password-actions .btn-icon-sm {
            min-width: 32px;
            min-height: 32px;
            padding: 0;
        }

        .password-cell .btn-icon-sm {
            min-width: 32px;
            min-height: 32px;
        }

        .student-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255,255,255,0.16);
            background: rgba(255,255,255,0.08);
        }

        .default-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.72);
            font-size: 1rem;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-pill.success { background: rgba(34,197,94,0.16); color: var(--success); }
        .status-pill.danger { background: rgba(239,68,68,0.16); color: var(--danger); }
        .status-pill.warning { background: rgba(255,212,59,0.18); color: #FBBF24; }
        .status-pill.muted { background: rgba(255,255,255,0.08); color: var(--text-secondary); }

        .register-modal {
            max-width: 1180px;
            width: 100%;
            background: #04152b;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.45);
            border-radius: 28px;
            overflow: hidden;
        }

        .register-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            padding: 28px 32px;
            background: #0B2748;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .register-header .modal-title {
            margin: 0;
            color: #FFFFFF;
            font-size: 1.95rem;
            letter-spacing: -0.03em;
        }

        .register-header .modal-subtitle {
            margin: 10px 0 0;
            color: #B8C7D9;
            font-size: 0.96rem;
            max-width: 620px;
            line-height: 1.65;
        }

        .register-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.04);
            color: #FFFFFF;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .register-back:hover {
            background: rgba(255,255,255,0.08);
        }

        .register-form {
            display: flex;
            flex-direction: column;
            gap: 24px;
            padding: 24px 32px 32px;
        }

        .register-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(360px, 0.8fr);
            gap: 24px;
        }

        .card {
            background: #0b2748;
            border: 1px solid #24476b;
            border-radius: 24px;
            padding: 24px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
        }

        .card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #FFFFFF;
        }

        .card-subtitle {
            color: #B8C7D9;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .form-grid {
            display: grid;
            gap: 18px;
        }

        .form-grid.two-col {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-group.full-width {
            grid-column: 1 / -1;
        }

        .field-label {
            color: #B8C7D9;
            font-size: 0.88rem;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            min-height: 48px;
            border-radius: 14px;
            border: 1px solid #24476b;
            background: #102f52;
            color: #FFFFFF;
            padding: 14px 16px;
            font-size: 0.95rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.48);
        }

        .form-control:focus {
            outline: none;
            border-color: #1677FF;
            box-shadow: 0 0 0 4px rgba(22,119,255,0.12);
            background: #112d4f;
        }

        .field-help,
        .field-note {
            color: #B8C7D9;
            font-size: 0.88rem;
            line-height: 1.5;
        }

        .field-note {
            margin-bottom: 16px;
        }

        .status-control {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .toggle-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .status-toggle {
            min-width: 132px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.04);
            color: #FFFFFF;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
        }

        .status-toggle.active {
            background: rgba(22,119,255,0.16);
            border-color: #1677FF;
            color: #FFFFFF;
        }

        .status-toggle.inactive {
            background: rgba(239,68,68,0.14);
            border-color: #ea5455;
        }

        .face-card {
            display: grid;
            gap: 18px;
        }

        .face-status-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .face-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 0.92rem;
            color: #FFFFFF;
            min-height: 44px;
            border: 1px solid rgba(255,255,255,0.08);
        }

        .face-status-not-registered { background: rgba(255,255,255,0.04); color: #B8C7D9; }
        .face-status-detected { background: rgba(22,119,255,0.16); color: #D1E5FF; border-color: #1677FF; }
        .face-status-registered { background: rgba(34,197,94,0.16); color: #D8F6E2; border-color: rgba(34,197,94,0.35); }

        .face-preview {
            position: relative;
            border-radius: 22px;
            overflow: hidden;
            background: #061a33;
            min-height: 260px;
        }

        .face-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            min-height: 260px;
            background: #061a33;
        }

        .face-overlay {
            pointer-events: none;
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .face-overlay::before {
            content: '';
            width: 70%;
            max-width: 260px;
            height: 70%;
            border: 2px solid rgba(255,255,255,0.38);
            border-radius: 50%;
            box-shadow: 0 0 0 9999px rgba(6,26,51,0.55);
        }

        .face-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .password-card {
            display: grid;
            gap: 18px;
        }

        .password-field {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .password-field input {
            flex: 1;
            border-radius: 14px;
            padding-right: 50px;
        }

        .password-field .btn-icon {
            position: absolute;
            right: 12px;
            border: none;
            background: transparent;
            color: #B8C7D9;
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .password-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.12);
            color: #FFFFFF;
        }

        .btn-primary {
            background: #1677FF;
            border-color: #1677FF;
            color: #FFFFFF;
        }

        .btn-primary:hover {
            background: #2188FF;
            border-color: #2188FF;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.07);
            border-color: rgba(255,255,255,0.08);
            color: #FFFFFF;
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.12);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .form-error {
            border: 1px solid rgba(239,68,68,0.24);
            background: rgba(239,68,68,0.08);
            color: #FECACA;
            border-radius: 16px;
            padding: 14px 18px;
            display: none;
            font-size: 0.95rem;
        }

        .form-error.active {
            display: block;
        }

        @media (max-width: 1120px) {
            .register-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 820px) {
            .register-header { flex-direction: column; }
            .face-actions { grid-template-columns: 1fr; }
            .form-actions { justify-content: stretch; }
            .field-group.full-width { grid-column: 1 / -1; }
        }

        @media (max-width: 640px) {
            .register-modal { border-radius: 18px; }
            .register-form { padding: 20px 20px 28px; }
            .register-header { padding: 22px 20px; }
        }

        .action-button {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.12);
            background: rgba(255,255,255,0.07);
            color: var(--text-white);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.18s ease, transform 0.18s ease;
        }

        .action-button:hover {
            background: rgba(255,255,255,0.16);
            transform: translateY(-1px);
        }

        .pagination-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.02);
        }

        .pagination-info {
            color: var(--text-secondary);
            font-size: 0.92rem;
        }

        .pagination-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pagination-list button {
            min-width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.14);
            background: transparent;
            color: var(--text-white);
            font-weight: 600;
            cursor: pointer;
            padding: 0 12px;
        }

        .pagination-list button.active {
            background: var(--btn-yellow);
            color: #0F172A;
            border-color: rgba(255,255,255,0.22);
        }

        .pagination-list button:hover {
            background: rgba(255,255,255,0.1);
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 1280px) {
            .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 1000px) {
            .page-header { flex-direction: column; align-items: flex-start; }
            .filter-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .filter-actions { justify-content: flex-start; width: 100%; }
        }

        @media (max-width: 760px) {
            .admin-shell { padding: 16px; }
            .summary-grid { grid-template-columns: 1fr; }
            .filter-row { grid-template-columns: 1fr; }
            .bulk-panel { flex-direction: column; align-items: stretch; }
            .table-inner { overflow-x: auto; }
            .student-table { min-width: 900px; }
        }

        @media (max-width: 640px) {
            .page-header { padding: 18px 18px; }
            .page-title { font-size: 1.85rem; }
            .filter-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="dashboard-page">
    <?= view('admin/_sidebar') ?>
    <div class="admin-shell">
        <main class="student-dashboard">
            <section class="page-header">
                <div class="page-title-group">
                    <h1 class="page-title">Student Management</h1>
                    <p class="page-copy">Manage and monitor all registered students.</p>
                </div>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <button id="openRegisterBtn" class="btn btn-primary" type="button"><i class="bi bi-person-plus-fill"></i> Register Student</button>
                    <button id="refreshBtn" class="btn btn-secondary" type="button"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
                    <button id="importCurrent" class="btn btn-secondary" type="button"><i class="bi bi-upload"></i> Import</button>
                    <input id="importFileInput" type="file" accept=".csv,.xlsx,.xls" style="display:none" />
                </div>
            </section>

            <section class="summary-grid">
                <article class="summary-card">
                    <span class="summary-icon"><i class="bi bi-people-fill"></i></span>
                    <div class="summary-copy">
                        <span class="summary-label">Total Students</span>
                        <span id="totalStudentsCount" class="summary-value">0</span>
                        <span class="summary-desc">All registered students</span>
                    </div>
                </article>
                <article class="summary-card">
                    <span class="summary-icon"><i class="bi bi-person-check-fill"></i></span>
                    <div class="summary-copy">
                        <span class="summary-label">Active Students</span>
                        <span id="activeStudentsCount" class="summary-value">0</span>
                        <span class="summary-desc">Active accounts</span>
                    </div>
                </article>
                <article class="summary-card">
                    <span class="summary-icon"><i class="bi bi-person-x-fill"></i></span>
                    <div class="summary-copy">
                        <span class="summary-label">Inactive Students</span>
                        <span id="inactiveStudentsCount" class="summary-value">0</span>
                        <span class="summary-desc">Inactive accounts</span>
                    </div>
                </article>
                <article class="summary-card">
                    <span class="summary-icon"><i class="bi bi-check-circle-fill"></i></span>
                    <div class="summary-copy">
                        <span class="summary-label">Face Registered</span>
                        <span id="faceRegisteredCount" class="summary-value">0</span>
                        <span class="summary-desc">Students with face data</span>
                    </div>
                </article>
            </section>

            <section class="filter-panel">
                <div class="filter-row">
                    <div class="filter-field">
                        <label for="searchInput">Search Student</label>
                        <input id="searchInput" type="search" placeholder="Search by name, ID, department..." />
                    </div>
                    <div class="filter-field">
                        <label for="filterDepartment">Department</label>
                        <select id="filterDepartment"><option value="">All Departments</option></select>
                    </div>
                    <div class="filter-field">
                        <label for="filterCourse">Course</label>
                        <select id="filterCourse"><option value="">All Courses</option></select>
                    </div>
                    <div class="filter-field">
                        <label for="filterYear">Year Level</label>
                        <select id="filterYear"><option value="">All Year Levels</option></select>
                    </div>
                    <div class="filter-field">
                        <label for="filterSection">Section</label>
                        <select id="filterSection"><option value="">All Sections</option></select>
                    </div>
                    <div class="filter-field">
                        <label for="filterActive">Status</label>
                        <select id="filterActive"><option value="">All Status</option><option value="1">Active</option><option value="0">Inactive</option></select>
                    </div>
                    <div class="filter-field">
                        <label for="filterFace">Face Registration</label>
                        <select id="filterFace"><option value="">All Face Status</option><option value="1">Registered</option><option value="0">Not Registered</option></select>
                    </div>
                    <div class="filter-actions">
                        <button id="filterBtn" class="btn btn-primary">Filter</button>
                        <button id="resetFilters" class="btn btn-secondary">Reset</button>
                        <label for="perPageSelect" style="margin:0 0 0 12px;color:#fff;font-size:0.95rem;align-self:center;">Rows</label>
                        <select id="perPageSelect" class="form-control" style="width:110px; margin-left:8px;">
                            <option value="10">10</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="bulk-panel">
                <div class="bulk-left">
                    <input type="checkbox" id="selectAll" />
                    <label for="selectAll">Select All</label>
                </div>
                <div class="bulk-actions">
                    <button id="bulkActivate" class="btn btn-secondary">Activate Selected</button>
                    <button id="bulkDeactivate" class="btn btn-secondary">Deactivate Selected</button>
                    <button id="bulkDelete" class="btn btn-danger">Delete Selected</button>
                    <button id="bulkExport" class="btn btn-secondary">Export Selected</button>
                    <button id="bulkRegisterFace" class="btn btn-secondary">Register Face</button>
                </div>
            </section>

            <section class="bulk-summary" id="bulkSummary" style="padding:0 0 16px 0;color:#fff;font-size:0.95rem;">
                <span id="selectedCount">0 students selected</span>
            </section>

            <section class="table-card">
                <div class="table-inner">
                    <table class="student-table" id="studentsTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="tableSelectAll" aria-label="Select all"/></th>
                                <th>Photo</th>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th>Department</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Section</th>
                                <th>Password</th>
                                <th>Face Registered</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentsBody">
                            <!-- rows populated by JS -->
                        </tbody>
                    </table>
                </div>
                <div class="pagination-bar">
                    <div id="tableInfo" class="pagination-info">Showing 0 students</div>
                    <div class="pagination-list" id="pagination"></div>
                </div>
            </section>
        </main>
    </div>

<!-- Register Modal -->
<div id="registerModal" class="modal" style="display:none;">
    <div class="modal-content register-modal">
        <div class="register-header">
            <button id="registerBackBtn" type="button" class="register-back"><i class="bi bi-arrow-left"></i> Back to Student Management</button>
            <div>
                <h3 class="modal-title">Register Student</h3>
                <p class="modal-subtitle">Create a student account and optionally register their face for face verification login.</p>
            </div>
        </div>
        <form id="registerForm" class="register-form">
            <div class="register-grid">
                <div>
                    <section class="card card-section">
                        <div class="card-header">
                            <div>
                                <span class="card-title">Personal Information</span>
                                <p class="card-subtitle">Required fields are marked with *</p>
                            </div>
                        </div>
                        <div class="card-body form-grid two-col">
                            <div class="field-group full-width">
                                <label class="field-label" for="student_id">Student ID Number *</label>
                                <input id="student_id" name="student_id" required class="form-control" placeholder="Enter student ID number" inputmode="numeric" pattern="[0-9]+" min="1" type="number" oninput="this.value = this.value.replace(/[^0-9]/g,'');" />
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="first_name">First Name *</label>
                                <input id="first_name" name="first_name" required class="form-control" placeholder="Enter first name" />
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="last_name">Last Name *</label>
                                <input id="last_name" name="last_name" required class="form-control" placeholder="Enter last name" />
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="middle_name">Middle Initial</label>
                                <input id="middle_name" name="middle_name" class="form-control" placeholder="Enter middle initial" />
                            </div>
                            <div class="field-group full-width">
                                <label class="field-label" for="email">Email</label>
                                <input id="email" name="email" type="email" class="form-control" placeholder="Enter email address" />
                            </div>
                        </div>
                    </section>

                    <section class="card card-section">
                        <div class="card-header">
                            <span class="card-title">Academic Information</span>
                        </div>
                        <div class="card-body form-grid two-col">
                            <div class="field-group">
                                <label class="field-label" for="department_id">Department *</label>
                                <select id="department_id" name="department_id" class="form-control" required>
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="course_id">Course *</label>
                                <select id="course_id" name="course_id" class="form-control" required>
                                    <option value="">Select Department First</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="year_level">Year Level *</label>
                                <input id="year_level" name="year_level" class="form-control" placeholder="Enter year level" />
                            </div>
                            <div class="field-group">
                                <label class="field-label" for="section">Section *</label>
                                <input id="section" name="section" class="form-control" placeholder="Enter section" />
                            </div>
                            <div class="field-group full-width">
                                <label class="field-label" for="school_year">Academic Year *</label>
                                <input id="school_year" name="school_year" class="form-control" placeholder="e.g. 2025-2026" />
                            </div>
                        </div>
                    </section>

                    <section class="card card-section">
                        <div class="card-header">
                            <span class="card-title">Account Status</span>
                        </div>
                        <div class="card-body">
                            <div class="status-control">
                                <p class="field-note">Inactive students cannot log in to the student portal.</p>
                                <div class="toggle-group">
                                    <button type="button" id="statusActiveBtn" class="status-toggle active" data-value="1">Active</button>
                                    <button type="button" id="statusInactiveBtn" class="status-toggle" data-value="0">Inactive</button>
                                </div>
                                <select id="is_active" name="is_active" class="form-control" style="display:none;">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </section>
                </div>

                <div>
                    <section id="faceRegistrationBlock" class="card card-section">
                        <div class="card-header">
                            <span class="card-title">Face Registration</span>
                        </div>
                        <div class="card-body face-card">
                            <p class="field-note">Face Verification (Optional)</p>
                            <div class="face-status-row">
                                <span id="faceStatusTag" class="face-status face-status-not-registered"><i class="bi bi-person-x"></i> Not Registered</span>
                            </div>
                            <div class="face-preview">
                                <video id="video" autoplay muted playsinline></video>
                                <div class="face-overlay"></div>
                            </div>
                            <div class="face-actions">
                                <button id="startCameraBtn" type="button" class="btn btn-secondary"><i class="bi bi-camera-video"></i> Open Camera</button>
                                <button id="captureBtn" type="button" class="btn btn-primary">Capture Face</button>
                                <button id="retakeBtn" type="button" class="btn btn-outline">Retake</button>
                                <button id="removeFaceBtn" type="button" class="btn btn-outline">Remove Face</button>
                            </div>
                            <div id="faceStatusText" class="field-help">Register the student's face so they can use face verification when logging in instead of entering their password.</div>
                            <div id="faceQuality" style="display:flex;gap:6px;align-items:center;">Quality: <strong id="qualityVal">—</strong></div>
                            <div id="captures" class="face-captures" style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:12px;"></div>
                        </div>
                    </section>

                    <section id="passwordBlock" class="card card-section">
                        <div class="card-header">
                            <span class="card-title">Account Credentials</span>
                        </div>
                        <div class="card-body password-card">
                            <p class="field-note">Auto-Generated Password</p>
                            <div class="password-field">
                                <input id="generatedPassword" type="password" readonly class="form-control" placeholder="••••••••" />
                                <button id="togglePasswordVisibility" type="button" class="btn btn-icon" aria-label="Show password"><i class="bi bi-eye"></i></button>
                            </div>
                            <div class="password-actions">
                                <button id="regenPwd" type="button" class="btn btn-outline">Regenerate Password</button>
                                <button id="copyPwd" type="button" class="btn btn-secondary">Copy Password</button>
                            </div>
                            <p class="field-help">The password will be saved automatically when the student is registered. Click the eye icon to reveal it.</p>
                            <input id="editingStudentId" type="hidden" name="editing_student_id" />
                            <input id="passwordInput" type="hidden" name="password" />
                        </div>
                    </section>
                </div>
            </div>

            <div id="registerFormError" class="form-error"></div>
            <div class="form-actions">
                <button type="button" id="cancelRegister" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Register Student</button>
            </div>
        </form>
    </div>
</div>

<div id="viewModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="viewModalTitle" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="viewModalTitle">Student Details</h3>
            <button id="viewCloseBtn" class="modal-close" type="button" aria-label="Close">×</button>
        </div>
        <div class="modal-body" style="color:#fff;">
            <div style="display:grid;grid-template-columns:120px 1fr;gap:16px;align-items:start;">
                <div><img id="viewStudentPhoto" src="" alt="Student photo" style="width:120px;height:120px;border-radius:12px;object-fit:cover;background:#000;" /></div>
                <div style="display:grid;gap:8px;">
                    <p><strong>Student ID:</strong> <span id="viewStudentId"></span></p>
                    <p><strong>Full Name:</strong> <span id="viewFullName"></span></p>
                    <p><strong>Email:</strong> <span id="viewEmail"></span></p>
                    <p><strong>Department:</strong> <span id="viewDepartment"></span></p>
                    <p><strong>Course:</strong> <span id="viewCourse"></span></p>
                    <p><strong>Year Level:</strong> <span id="viewYear"></span></p>
                    <p><strong>Section:</strong> <span id="viewSection"></span></p>
                    <p><strong>Face Registration:</strong> <span id="viewFaceStatus"></span></p>
                    <p><strong>Account Status:</strong> <span id="viewAccountStatus"></span></p>
                    <p><strong>Created Date:</strong> <span id="viewCreated"></span></p>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:8px;">
            <button id="viewModalClose" class="btn btn-secondary" type="button">Close</button>
        </div>
    </div>
</div>

<script>
// Load departments for filters and registration form
async function loadDepartments() {
    try {
        const res = await fetch('<?= base_url('admin/departments/list') ?>');
        const deps = await res.json();
        const filterDept = document.getElementById('filterDepartment');
        const regDept = document.getElementById('department_id');
        if (filterDept) {
            filterDept.innerHTML = '<option value="">All Departments</option>' + deps.map(d => `<option value="${d.id}">${d.department_code} - ${d.department_name}</option>`).join('');
        }
        if (regDept) {
            regDept.innerHTML = '<option value="">Select Department</option>' + deps.map(d => `<option value="${d.id}">${d.department_code} - ${d.department_name}</option>`).join('');
        }
    } catch (err) {
        console.error('Failed to load departments', err);
    }
}

async function loadCoursesFor(departmentId, targetSelectId, includePlaceholder=true) {
    const sel = document.getElementById(targetSelectId);
    if (!sel) return;
    sel.innerHTML = '<option>Loading courses...</option>';
    if (!departmentId) {
        sel.innerHTML = '<option value="">Select Department First</option>';
        return;
    }
    try {
        const res = await fetch(`<?= base_url('courses/by-department') ?>/${departmentId}`);
        const courses = await res.json();
        if (!Array.isArray(courses) || courses.length === 0) {
            sel.innerHTML = '<option value="">No active courses</option>';
            return;
        }
        sel.innerHTML = (includePlaceholder ? '<option value="">Select Course</option>' : '') + courses.map(c => `<option value="${c.id}">${c.course_code} - ${c.course_name}</option>`).join('');
    } catch (err) {
        console.error('Failed to load courses', err);
        sel.innerHTML = '<option value="">Failed to load courses</option>';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadDepartments();

    const regDept = document.getElementById('department_id');
    const regCourse = document.getElementById('course_id');
    const filterDept = document.getElementById('filterDepartment');
    const filterCourse = document.getElementById('filterCourse');

    if (regDept) {
        regDept.addEventListener('change', function() {
            const id = this.value;
            loadCoursesFor(id, 'course_id');
        });
    }

    if (filterDept) {
        filterDept.addEventListener('change', function() {
            const id = this.value;
            if (filterCourse) {
                loadCoursesFor(id, 'filterCourse', true);
            }
        });
    }
});
</script>

<div id="confirmModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="confirmModalTitle">Confirm Action</h3>
            <button id="confirmCloseBtn" class="modal-close" type="button" aria-label="Close">×</button>
        </div>
        <div class="modal-body" style="color:#fff;">
            <p id="confirmMessage">Are you sure?</p>
        </div>
        <div class="modal-footer" style="display:flex;justify-content:flex-end;gap:8px;">
            <button id="confirmCancelBtn" class="btn btn-secondary" type="button">Cancel</button>
            <button id="confirmActionBtn" class="btn btn-danger" type="button">Confirm</button>
        </div>
    </div>
</div>

<div id="toastContainer" style="position:fixed;bottom:16px;right:16px;display:flex;flex-direction:column;gap:10px;z-index:9999;"></div>

<script>
const api = '<?= base_url('/admin/students') ?>';
const baseUrl = '<?= base_url() ?>';
const registerModal = document.getElementById('registerModal');
const temporaryPasswords = {};
const visiblePasswordIds = new Set();
let captures = [];
let cameraStream = null;

function updateFaceStatus(hasFace, registered = false) {
    const tag = document.getElementById('faceStatusTag');
    const statusText = document.getElementById('faceStatusText');
    if (!tag || !statusText) return;
    if (registered) {
        tag.className = 'face-status face-status-registered';
        tag.innerHTML = '<i class="bi bi-check-circle-fill"></i> Face Registered Successfully';
        statusText.textContent = 'Face registration is complete and ready for verification login.';
        return;
    }
    if (hasFace) {
        tag.className = 'face-status face-status-detected';
        tag.innerHTML = '<i class="bi bi-camera-reels"></i> Face Detected';
        statusText.textContent = 'Face capture detected. Retake or register it now.';
        return;
    }
    tag.className = 'face-status face-status-not-registered';
    tag.innerHTML = '<i class="bi bi-person-x"></i> Not Registered';
    statusText.textContent = 'Register the student\'s face so they can use face verification when logging in instead of entering their password.';
}

function updateFaceStatus(hasFace, registered = false) {
    const tag = document.getElementById('faceStatusTag');
    const statusText = document.getElementById('faceStatusText');
    if (!tag || !statusText) return;
    if (registered) {
        tag.className = 'face-status face-status-registered';
        tag.innerHTML = '<i class="bi bi-check-circle-fill"></i> Face Registered Successfully';
        statusText.textContent = 'Face registration is complete and ready for verification login.';
        return;
    }
    if (hasFace) {
        tag.className = 'face-status face-status-detected';
        tag.innerHTML = '<i class="bi bi-camera-reels"></i> Face Detected';
        statusText.textContent = 'Face capture detected. Retake or register it now.';
        return;
    }
    tag.className = 'face-status face-status-not-registered';
    tag.innerHTML = '<i class="bi bi-person-x"></i> Not Registered';
    statusText.textContent = 'Register the student\'s face so they can use face verification when logging in instead of entering their password.';
}

function startCamera() {
    const video = document.getElementById('video');
    if (!video || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return;
    if (cameraStream) {
        video.srcObject = cameraStream;
        return;
    }
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
        .then(stream => {
            cameraStream = stream;
            video.srcObject = stream;
        })
        .catch(() => {
            alert('Unable to access camera. Please allow camera permissions.');
        });
}

function stopCamera() {
    const video = document.getElementById('video');
    if (video) video.srcObject = null;
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.student-select:checked').length;
    const label = document.getElementById('selectedCount');
    if (label) label.textContent = `${count} student${count === 1 ? '' : 's'} selected`;
}

async function fetchList(opts = {}){
    const q = document.getElementById('searchInput').value;
    const params = new URLSearchParams({ q, page: opts.page || 1, per_page: opts.per_page || 10 });
    // filters
    ['filterDepartment','filterCourse','filterYear','filterSection','filterActive','filterFace'].forEach(id=>{
        const el = document.getElementById(id);
        if(el && el.value) params.set(id.replace('filter','').toLowerCase(), el.value);
    });
    document.getElementById('tableInfo').textContent = 'Loading...';
    const res = await fetch(api + '/list?' + params.toString());
    const data = await res.json();
    currentRows = data.data || [];
    populateFilters(data.filters || {});
    updateSummaryCards(data.summary || {}, data.total || 0, currentRows);
    renderRows(currentRows);
    document.getElementById('tableInfo').textContent = `Showing 1–${currentRows.length} of ${data.total || 0} students`;
    renderPagination(data.page || 1, data.per_page || 10, data.total || 0);
}

function updateSummaryCards(summary, total, rows) {
    document.getElementById('totalStudentsCount').textContent = summary.total ?? total ?? 0;
    document.getElementById('activeStudentsCount').textContent = summary.active ?? 0;
    document.getElementById('inactiveStudentsCount').textContent = summary.inactive ?? 0;
    document.getElementById('faceRegisteredCount').textContent = summary.face_registered ?? 0;
}

function renderRows(rows){
    const tbody = document.getElementById('studentsBody');
    tbody.innerHTML = '';
    rows.forEach((s, idx) => {
        const tr = document.createElement('tr');
        tr.dataset.studentId = s.id || '';
        tr.dataset.firstName = s.first_name || '';
        tr.dataset.middleName = s.middle_name || '';
        tr.dataset.lastName = s.last_name || '';
        tr.dataset.email = s.email || '';
        tr.dataset.department = s.department || s.department_name || '';
        tr.dataset.course = s.course || s.course_name || '';
        tr.dataset.yearLevel = s.year_level || '';
        tr.dataset.section = s.section || s.section_name || '';
        tr.dataset.isActive = s.is_active ?? '';
        tr.dataset.faceRegistered = s.face_registered ?? '';
        const isActive = Number(s.is_active ?? 0) === 1 || String(s.is_active) === '1' || String(s.status || '').toLowerCase() === 'active';
        const faceRegistered = Number(s.face_registered ?? 0) === 1 || String(s.face_registered) === '1' || String(s.face_registered) === 'true';
        const defaultAvatar = `${baseUrl}/images/candidates/default.jpg`;
        const photoFile = s.id_photo || '';
        const photoSrc = photoFile ? `${baseUrl}/uploads/${encodeURIComponent(photoFile)}` : defaultAvatar;
        const photo = `<img src="${photoSrc}" alt="" class="student-photo ${photoFile ? '' : 'default-avatar'}">`;
        const fullName = s.full_name || `${s.first_name || ''} ${s.last_name || ''}`.trim();
        const department = s.department_name || s.department || '';
        const course = s.course_name || s.course || '';
        const section = s.section_name || s.section || '';

        tr.innerHTML = `
                <td><input type="checkbox" class="student-select" value="${escapeHtml(s.id)}" /></td>
                <td class="photo-col">${photo}</td>
                ${buildTextCell((s.student_id || '').toString().replace(/\D+/g, ''), 'Student ID')}
                ${buildTextCell(fullName, 'Full Name')}
                ${buildTextCell(department, 'Department')}
                ${buildTextCell(course, 'Course')}
                ${buildTextCell(s.year_level || '', 'Year Level')}
                ${buildTextCell(section, 'Section')}
                ${buildPasswordCell(s.id)}
                <td title="${faceRegistered ? 'Registered' : 'Not Registered'}">${faceRegistered ? '<span class="badge badge-success">✓ Registered</span>' : '<span class="badge badge-warning">⚠ Not Registered</span>'}</td>
                <td title="${isActive ? 'Active' : 'Inactive'}">${isActive ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-gray">Inactive</span>'}</td>
                ${buildTextCell(s.created_at || '', 'Created')}
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-icon btn-icon-sm" title="View"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-icon btn-icon-sm" title="Edit"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-icon btn-icon-sm" title="Delete" data-id="${escapeHtml(s.id)}"><i class="bi bi-trash-fill"></i></button>
                        <button class="btn btn-icon btn-icon-sm" title="Activate"><i class="bi bi-check-circle"></i></button>
                        <button class="btn btn-icon btn-icon-sm" title="Register Face"><i class="bi bi-camera-fill"></i></button>
                        <button class="btn btn-icon btn-icon-sm" title="Reset Password" data-action="reset-password" data-id="${escapeHtml(s.id)}"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>
                </td>
            `;
        tbody.appendChild(tr);
    });
    updateSelectAllState();
    updateSelectedCount();
}

function buildPasswordCell(studentId) {
    const pwd = temporaryPasswords[studentId] || '';
    const hasTemp = Boolean(pwd);
    const visible = hasTemp && visiblePasswordIds.has(studentId);
    const displayValue = hasTemp ? (visible ? escapeHtml(pwd) : '••••••••') : '••••••••';
    const iconClass = visible ? 'bi-eye-slash' : 'bi-eye';
    const ariaLabel = hasTemp ? (visible ? 'Hide password' : 'Show password') : 'Generate and show temporary password';
    return `
        <td class="password-cell" data-student-id="${escapeHtml(studentId)}">
            <div class="password-wrapper">
                <span class="password-text">${displayValue}</span>
                <div class="password-actions">
                    <button type="button" class="btn btn-icon btn-icon-sm password-toggle" aria-label="${ariaLabel}" data-action="toggle-password" data-student-id="${escapeHtml(studentId)}">
                        <i class="bi ${iconClass}"></i>
                    </button>
                    <button type="button" class="btn btn-icon btn-icon-sm" aria-label="Copy password" data-action="copy-password" data-student-id="${escapeHtml(studentId)}">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
            </div>
        </td>
    `;
}

function buildTextCell(value, fieldName) {
    const safe = escapeHtml(value || '');
    return `<td class="text-truncate" title="${safe}" data-text="${escapeHtml(fieldName)}">${safe}</td>`;
}

function populateFilters(filters){
    if(filters.departments){
        const sel = document.getElementById('filterDepartment'); sel.innerHTML = '<option value="">Department</option>';
        filters.departments.forEach(d=>{ const o = document.createElement('option'); o.value=d; o.textContent=d; sel.appendChild(o); });
    }
    if(filters.courses){
        const sel = document.getElementById('filterCourse'); sel.innerHTML = '<option value="">Course</option>';
        filters.courses.forEach(d=>{ const o = document.createElement('option'); o.value=d; o.textContent=d; sel.appendChild(o); });
    }
    if(filters.years){
        const sel = document.getElementById('filterYear'); sel.innerHTML = '<option value="">Year Level</option>';
        filters.years.forEach(d=>{ const o = document.createElement('option'); o.value=d; o.textContent=d; sel.appendChild(o); });
    }
    if(filters.sections){
        const sel = document.getElementById('filterSection'); sel.innerHTML = '<option value="">Section</option>';
        filters.sections.forEach(d=>{ const o = document.createElement('option'); o.value=d; o.textContent=d; sel.appendChild(o); });
    }
}

function renderPagination(page, perPage, total){
    const container = document.getElementById('pagination'); container.innerHTML='';
    const totalPages = Math.ceil(total / perPage) || 1;
    for(let p=1;p<=totalPages;p++){
        const btn = document.createElement('button'); btn.textContent=p; btn.className='btn-outline';
        if(p===page) btn.style.fontWeight='700';
        btn.addEventListener('click', ()=> fetchList({page:p, per_page:perPage}));
        container.appendChild(btn);
    }
}

function escapeHtml(str){ if(!str) return ''; return String(str).replace(/[&<>"']/g, function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m];}); }

document.getElementById('searchInput').addEventListener('input', debounce(()=>fetchList({page:1}), 300));
['filterDepartment','filterCourse','filterYear','filterSection','filterActive','filterFace'].forEach(id=>{
    const el = document.getElementById(id); if(el) el.addEventListener('change', ()=>fetchList({page:1}));
});

const filterBtn = document.getElementById('filterBtn');
if (filterBtn) {
    filterBtn.addEventListener('click', () => fetchList({page:1}));
}

const resetFiltersBtn = document.getElementById('resetFilters');
if (resetFiltersBtn) {
    resetFiltersBtn.addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        ['filterDepartment','filterCourse','filterYear','filterSection','filterActive','filterFace'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        fetchList({page:1});
    });
}

const refreshBtn = document.getElementById('refreshBtn');
if (refreshBtn) {
    refreshBtn.addEventListener('click', () => fetchList({page:1}));
}

const importFileInput = document.getElementById('importFileInput');
const importCurrentBtn = document.getElementById('importCurrent');
if (importCurrentBtn) {
    importCurrentBtn.addEventListener('click', () => importFileInput?.click());
}
if (importFileInput) {
    importFileInput.addEventListener('change', async (event) => {
        const file = event.target.files?.[0];
        if (!file) return;
        if (!confirm(`Import students from ${file.name}?`)) {
            event.target.value = '';
            return;
        }
        const formData = new FormData();
        formData.append('student_file', file);
        const res = await fetch(api + '/import', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            alert(`Imported ${data.imported} students${data.updated ? `, updated ${data.updated}` : ''}.`);
            fetchList({page:1});
        } else {
            alert('Import failed.' + (data.message ? '\n' + data.message : ''));
        }
        event.target.value = '';
    });
}

const bulkActivateBtn = document.getElementById('bulkActivate');
const bulkDeactivateBtn = document.getElementById('bulkDeactivate');
const bulkDeleteBtn = document.getElementById('bulkDelete');
const bulkExportBtn = document.getElementById('bulkExport');
const bulkRegisterFaceBtn = document.getElementById('bulkRegisterFace');

if (bulkActivateBtn) bulkActivateBtn.addEventListener('click', () => performBulkAction('activate', 'Selected students activated.'));
if (bulkDeactivateBtn) bulkDeactivateBtn.addEventListener('click', () => performBulkAction('deactivate', 'Selected students deactivated.'));
if (bulkDeleteBtn) bulkDeleteBtn.addEventListener('click', () => performBulkAction('delete', 'Selected students deleted.'));
if (bulkExportBtn) bulkExportBtn.addEventListener('click', exportSelectedStudents);
if (bulkRegisterFaceBtn) bulkRegisterFaceBtn.addEventListener('click', () => {
    const selected = getSelectedStudentIds();
    if (!selected.length) { alert('Please select at least one student first.'); return; }
    alert('Face registration is only available per student. Please use the Register Face button on each row.');
});

function getSelectedStudentIds() {
    return Array.from(document.querySelectorAll('.student-select:checked')).map(cb => cb.value);
}

function updateSelectAllState() {
    const allCheckboxes = Array.from(document.querySelectorAll('.student-select'));
    const checkedCheckboxes = allCheckboxes.filter(cb => cb.checked);
    const tableSelectAll = document.getElementById('tableSelectAll');
    const selectAll = document.getElementById('selectAll');
    const allChecked = allCheckboxes.length > 0 && checkedCheckboxes.length === allCheckboxes.length;
    if (tableSelectAll) tableSelectAll.checked = allChecked;
    if (selectAll) selectAll.checked = allChecked;
}

function toggleAllStudentCheckboxes(checked) {
    document.querySelectorAll('.student-select').forEach(cb => cb.checked = checked);
    updateSelectAllState();
    updateSelectedCount();
}

function getStudentDataFromRow(row) {
    return {
        id: row.dataset.studentId,
        student_id: row.querySelector('td[data-text="Student ID"]').textContent.trim(),
        first_name: row.dataset.firstName,
        middle_name: row.dataset.middleName,
        last_name: row.dataset.lastName,
        full_name: row.querySelector('td[data-text="Full Name"]').textContent.trim(),
        email: row.dataset.email,
        department: row.dataset.department,
        course: row.dataset.course,
        year_level: row.dataset.yearLevel,
        section: row.dataset.section,
        is_active: row.dataset.isActive,
        face_registered: row.dataset.faceRegistered,
        created_at: row.querySelector('td[data-text="Created"]').textContent.trim(),
        status: row.querySelector('td:nth-child(10)').textContent.trim(),
    };
}

function setRegisterModalMode(mode, student = null) {
    if (registerModal) registerModal.dataset.mode = mode;
    const hiddenId = document.getElementById('editingStudentId');
    const title = registerModal ? registerModal.querySelector('.modal-title') : null;
    const submitBtn = registerModal ? registerModal.querySelector('button[type="submit"]') : null;
    const passwordBlock = document.getElementById('passwordBlock');
    const faceBlock = document.getElementById('faceRegistrationBlock');
    if (mode === 'edit') {
        title.textContent = 'Edit Student';
        submitBtn.textContent = 'Update Student';
        hiddenId.value = student.id || '';
        ['student_id','first_name','middle_name','last_name','email','department','course','year_level','section','is_active'].forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.value = student[name] || '';
        });
        if (passwordBlock) passwordBlock.style.display = 'none';
        if (passwordBlock) {
            const pwdField = document.getElementById('generatedPassword');
            const pwdInput = document.getElementById('passwordInput');
            if (pwdField) pwdField.value = '';
            if (pwdInput) pwdInput.value = '';
        }
        if (faceBlock) faceBlock.style.display = 'block';
    } else if (mode === 'face') {
        title.textContent = 'Register Face';
        submitBtn.textContent = 'Upload Face';
        hiddenId.value = student.id || '';
        ['student_id','first_name','middle_name','last_name','email','department','course','year_level','section','is_active'].forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.value = '';
        });
        if (passwordBlock) passwordBlock.style.display = 'none';
        if (faceBlock) faceBlock.style.display = 'block';
    } else {
        title.textContent = 'Register Student';
        submitBtn.textContent = 'Register Student';
        hiddenId.value = '';
        ['student_id','first_name','middle_name','last_name','email','department','course','year_level','section','is_active'].forEach(name => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.value = '';
        });
        if (passwordBlock) passwordBlock.style.display = 'block';
        if (faceBlock) faceBlock.style.display = 'block';
        resetPasswordVisibility();
        generateLocalPassword();
    }
}

function resetPasswordVisibility() {
    const pwdField = document.getElementById('generatedPassword');
    const toggleBtn = document.getElementById('togglePasswordVisibility');
    if (pwdField) pwdField.type = 'password';
    if (toggleBtn) {
        toggleBtn.textContent = '👁️';
        toggleBtn.setAttribute('aria-label', 'Show password');
    }
}

function resetRegisterModal() {
    if (registerModal) {
        registerModal.classList.remove('active');
        registerModal.style.display = 'none';
        // restore any floating styles
        try {
            const content = registerModal.querySelector('.register-modal');
            if (content) {
                content.style.position = '';
                content.style.right = '';
                content.style.top = '';
                content.style.left = '';
                content.style.maxWidth = '';
                content.style.width = '';
                content.style.margin = '';
                content.style.boxShadow = '';
                content.style.borderRadius = '';
                content.style.zIndex = '';
            }
            registerModal.style.background = '';
            registerModal.style.zIndex = '';
        } catch (e) { console.warn('resetRegisterModal restore failed', e); }
    }
    setRegisterModalMode('create');
    stopCamera();
    captures = [];
    const container = document.getElementById('captures');
    if (container) container.innerHTML = '';
}

function handleRowAction(event) {
    const button = event.target.closest('button');
    if (!button) return;
    const action = button.getAttribute('title');
    const row = button.closest('tr');
    const student = getStudentDataFromRow(row);
    if (action === 'View') {
        alert(`Student:\n${student.student_id} - ${student.full_name}\n${student.department} / ${student.course} / ${student.year_level} / ${student.section}\nStatus: ${student.status}\nFace: ${student.face_registered}`);
        return;
    }
    if (action === 'Edit') {
        setRegisterModalMode('edit', student);
        if (registerModal) registerModal.classList.add('active');
        startCamera();
        return;
    }
    if (action === 'Delete') {
        if (!confirm('Delete this student?')) return;
        fetch(api + '/delete/' + student.id, { method: 'POST' }).then(r => r.json()).then(data => {
            if (data.success) {
                alert('Student deleted.');
                fetchList({page:1});
            } else {
                alert('Delete failed.');
            }
        });
        return;
    }
    if (action === 'Activate') {
        performBulkAction('activate', 'Student activated.', [student.id]);
        return;
    }
    if (action === 'Register Face') {
        setRegisterModalMode('face', student);
        if (registerModal) registerModal.classList.add('active');
        startCamera();
        return;
    }
    if (action === 'Reset Password') {
        confirmResetPassword(student.id);
        return;
    }
}

function attachTableListeners() {
    const tableSelectAll = document.getElementById('tableSelectAll');
    const selectAll = document.getElementById('selectAll');
    const tbody = document.getElementById('studentsBody');
    if (tableSelectAll) tableSelectAll.addEventListener('change', () => toggleAllStudentCheckboxes(tableSelectAll.checked));
    if (selectAll) selectAll.addEventListener('change', () => toggleAllStudentCheckboxes(selectAll.checked));
    if (tbody) {
        tbody.addEventListener('change', event => {
            if (event.target.classList.contains('student-select')) {
                updateSelectAllState();
                updateSelectedCount();
            }
        });
        tbody.addEventListener('click', event => {
            const toggleBtn = event.target.closest('button[data-action="toggle-password"]');
            if (toggleBtn) {
                const studentId = toggleBtn.dataset.studentId;
                if (!studentId) return;
                if (temporaryPasswords[studentId]) {
                    togglePasswordVisibility(studentId);
                } else {
                    resetStudentPassword(studentId);
                }
                return;
            }
            const copyBtn = event.target.closest('button[data-action="copy-password"]');
            if (copyBtn) {
                const studentId = copyBtn.dataset.studentId;
                if (!studentId) return;
                if (temporaryPasswords[studentId]) {
                    copyTemporaryPassword(studentId);
                } else {
                    resetStudentPassword(studentId);
                }
                return;
            }
            handleRowAction(event);
        });
    }
}

async function performBulkAction(action, successMessage, ids = null) {
    const selectedIds = ids || getSelectedStudentIds();
    if (!selectedIds.length) { alert('Please select at least one student first.'); return; }
    if (!confirm(`Are you sure you want to ${action} ${selectedIds.length} selected student(s)?`)) return;
    const body = new URLSearchParams();
    body.append('action', action);
    selectedIds.forEach(id => body.append('ids[]', id));
    const res = await fetch(api + '/bulk', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body.toString() });
    const data = await res.json();
    if (data.success) {
        if (data.passwords) {
            Object.entries(data.passwords).forEach(([id, pwd]) => {
                temporaryPasswords[id] = pwd;
                visiblePasswordIds.add(id);
            });
        }
        showToast(successMessage);
        fetchList({page:1});
    } else {
        alert('Bulk action failed.' + (data.message ? '\n' + data.message : ''));
    }
}

function exportSelectedStudents() {
    const rows = Array.from(document.querySelectorAll('.student-select:checked')).map(cb => cb.closest('tr'));
    if (!rows.length) { alert('Please select at least one student to export.'); return; }
    const headers = ['Student ID','Full Name','Department','Course','Year','Section','Face Registered','Status','Created'];
    const csv = [headers.join(',')];
    rows.forEach(row => {
        const values = [
            row.querySelector('td[data-text="Student ID"]').textContent.trim(),
            row.querySelector('td[data-text="Full Name"]').textContent.trim(),
            row.querySelector('td[data-text="Department"]').textContent.trim(),
            row.querySelector('td[data-text="Course"]').textContent.trim(),
            row.querySelector('td[data-text="Year Level"]').textContent.trim(),
            row.querySelector('td[data-text="Section"]').textContent.trim(),
            row.querySelector('td:nth-child(9)').textContent.trim(),
            row.querySelector('td:nth-child(10)').textContent.trim(),
            row.querySelector('td[data-text="Created"]').textContent.trim(),
        ];
        csv.push(values.map(v => '"' + v.replace(/"/g, '""') + '"').join(','));
    });
    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'students_export.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function openRegisterModal() {
    setRegisterModalMode('create');
    if (registerModal) {
        registerModal.classList.add('active');
        registerModal.style.display = 'block';
    }
    if (typeof startCamera === 'function') startCamera();
    updateSelectedCount();
    // make the register modal a floating window
    try { enableFloatingRegister(); } catch (e) { console.warn('enableFloatingRegister error', e); }
}

function confirmResetPassword(studentId) {
    resetStudentPassword(studentId);
}

async function resetStudentPassword(studentId) {
    const res = await fetch(api + '/reset-password/' + studentId, { method: 'POST' });
    const data = await res.json();
    if (!data.success) {
        alert('Failed to reset password.');
        return;
    }
    temporaryPasswords[studentId] = data.password;
    visiblePasswordIds.add(studentId);
    renderRows(Array.from(document.querySelectorAll('#studentsBody tr')).map(row => getStudentDataFromRow(row)));
    showToast('Temporary password generated.');
}

function togglePasswordVisibility(studentId) {
    const pwd = temporaryPasswords[studentId] || '';
    if (!pwd) return;
    if (visiblePasswordIds.has(studentId)) {
        visiblePasswordIds.delete(studentId);
    } else {
        visiblePasswordIds.add(studentId);
    }
    renderRows(Array.from(document.querySelectorAll('#studentsBody tr')).map(row => getStudentDataFromRow(row)));
}

function copyTemporaryPassword(studentId) {
    const pwd = temporaryPasswords[studentId] || '';
    if (!pwd) {
        alert('No temporary password available to copy. Reset the password first.');
        return;
    }
    navigator.clipboard.writeText(pwd).then(() => {
        showToast('Temporary password copied to clipboard.');
    }).catch(() => {
        alert('Unable to copy password to clipboard.');
    });
}

function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.background = type === 'success' ? 'rgba(16, 185, 129, 0.95)' : 'rgba(239, 68, 68, 0.95)';
    toast.style.color = '#fff';
    toast.style.padding = '12px 16px';
    toast.style.borderRadius = '12px';
    toast.style.boxShadow = '0 8px 24px rgba(0, 0, 0, 0.24)';
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 200ms ease-in-out';
    toastContainer.appendChild(toast);
    requestAnimationFrame(() => toast.style.opacity = '1');
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 200);
    }, 3500);
}

const openRegisterBtn = document.getElementById('openRegisterBtn');
if (openRegisterBtn) openRegisterBtn.addEventListener('click', openRegisterModal);
const cancelRegisterBtn = document.getElementById('cancelRegister');
if (cancelRegisterBtn) cancelRegisterBtn.addEventListener('click', () => { stopCamera(); resetRegisterModal(); });

document.getElementById('captureBtn').addEventListener('click', ()=>{
    const video = document.getElementById('video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const dataUrl = canvas.toDataURL('image/png');
    captures.push(dataUrl);
    const img = document.createElement('img'); img.src = dataUrl; img.className='w-full rounded';
    const container = document.getElementById('captures'); container.appendChild(img);
    updateFaceStatus(true);
});

document.getElementById('retakeBtn').addEventListener('click', ()=>{ captures = []; const container = document.getElementById('captures'); if (container) container.innerHTML=''; updateFaceStatus(false); alert('Face captures cleared.'); });

const startCameraBtn = document.getElementById('startCameraBtn');
if (startCameraBtn) startCameraBtn.addEventListener('click', startCamera);
const removeFaceBtn = document.getElementById('removeFaceBtn');
if (removeFaceBtn) removeFaceBtn.addEventListener('click', ()=>{ captures = []; const container = document.getElementById('captures'); if (container) container.innerHTML=''; updateFaceStatus(false); alert('Face capture removed.'); });

const registerBackBtn = document.getElementById('registerBackBtn');
if (registerBackBtn) registerBackBtn.addEventListener('click', ()=>{ resetRegisterModal(); });

const statusActiveBtn = document.getElementById('statusActiveBtn');
const statusInactiveBtn = document.getElementById('statusInactiveBtn');
const statusInput = document.getElementById('is_active');
if (statusActiveBtn && statusInactiveBtn && statusInput) {
    const setStatus = value => {
        statusInput.value = value;
        statusActiveBtn.classList.toggle('active', value === '1');
        statusActiveBtn.classList.toggle('inactive', value !== '1');
        statusInactiveBtn.classList.toggle('active', value === '0');
        statusInactiveBtn.classList.toggle('inactive', value !== '0');
    };
    statusActiveBtn.addEventListener('click', ()=> setStatus('1'));
    statusInactiveBtn.addEventListener('click', ()=> setStatus('0'));
    setStatus(statusInput.value || '1');
}

document.getElementById('registerForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const mode = registerModal.dataset.mode || 'create';
    const form = e.target;
    const fd = new FormData(form);

    // Ensure full_name is sent (Model requires full_name). Build from first/middle/last.
    if (!fd.has('full_name')) {
        const first = (document.getElementById('first_name') || {}).value || '';
        const middle = (document.getElementById('middle_name') || {}).value || '';
        const last = (document.getElementById('last_name') || {}).value || '';
        const full = [first, middle, last].map(s => (s||'').trim()).filter(Boolean).join(' ').trim();
        if (full) fd.append('full_name', full);
    }
    const editingId = document.getElementById('editingStudentId').value;
    if (mode === 'face') {
        if (!captures.length) { setRegisterFormError('Please capture at least one face image first.'); return; }
        const body = new URLSearchParams();
        captures.forEach(c => body.append('faceImages[]', c));
        const res = await fetch(api + '/upload-face/' + editingId, { method: 'POST', body });
        const data = await res.json();
        if (data.success) { alert('Face data uploaded successfully.'); resetRegisterModal(); fetchList({page:1}); } else { setRegisterFormError('Face upload failed.'); }
        return;
    }
    if (mode === 'edit' && editingId) {
        fd.delete('editing_student_id');
        const res = await fetch(api + '/update/' + editingId, { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) { alert('Student updated successfully.'); resetRegisterModal(); fetchList({page:1}); } else { alert('Update failed.'); }
        return;
    }
    captures.forEach(c => fd.append('faceImages[]', c));
    const res = await fetch(api + '/store', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        setRegisterFormError('');
        if (captures.length) {
            const uploadBody = new URLSearchParams();
            captures.forEach(c => uploadBody.append('faceImages[]', c));
            await fetch(api + '/upload-face/' + data.id, { method: 'POST', body: uploadBody });
        }
        alert('Student account created successfully. Password: ' + data.password);
        resetRegisterModal();
        fetchList({page:1});
    } else {
        setRegisterFormError('Failed to create student. Please check the fields and try again.');
    }
});

// password generator preview in modal
function generateLocalPassword(){
    const letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const digits = '0123456789';
    const chars = letters + digits;

    const pwd = [
        letters[Math.floor(Math.random() * letters.length)],
        digits[Math.floor(Math.random() * digits.length)]
    ];

    for (let i = 2; i < 10; i++) {
        pwd.push(chars[Math.floor(Math.random() * chars.length)]);
    }

    for (let i = pwd.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [pwd[i], pwd[j]] = [pwd[j], pwd[i]];
    }

    const result = pwd.join('');
    document.getElementById('generatedPassword').value = result;
    document.getElementById('passwordInput').value = result;
}

const regenPwdBtn = document.getElementById('regenPwd');
if (regenPwdBtn) regenPwdBtn.addEventListener('click', generateLocalPassword);

const copyPwdBtn = document.getElementById('copyPwd');
if (copyPwdBtn) copyPwdBtn.addEventListener('click', ()=>{
    const pwd = document.getElementById('generatedPassword').value;
    navigator.clipboard.writeText(pwd);
    alert('Password copied');
});

const togglePasswordVisibilityBtn = document.getElementById('togglePasswordVisibility');
if (togglePasswordVisibilityBtn) togglePasswordVisibilityBtn.addEventListener('click', ()=>{
    const pwdField = document.getElementById('generatedPassword');
    const toggleBtn = document.getElementById('togglePasswordVisibility');
    if (!pwdField || !toggleBtn) return;
    const icon = toggleBtn.querySelector('i');
    if (pwdField.type === 'password') {
        pwdField.type = 'text';
        if (icon) {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            toggleBtn.textContent = '';
            toggleBtn.innerHTML = '<i class="bi bi-eye-slash"></i>';
        }
        toggleBtn.setAttribute('aria-label', 'Hide password');
    } else {
        pwdField.type = 'password';
        if (icon) {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            toggleBtn.textContent = '';
            toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';
        }
        toggleBtn.setAttribute('aria-label', 'Show password');
    }
});

function setRegisterFormError(message) {
    const errorBox = document.getElementById('registerFormError');
    if (!errorBox) return;
    if (!message) {
        errorBox.textContent = '';
        errorBox.classList.remove('active');
        return;
    }
    errorBox.textContent = message;
    errorBox.classList.add('active');
}

function debounce(fn, wait){ let t; return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); }}

attachTableListeners();
// Initial load
fetchList();

// Fallback: ensure Register Student button accessible if header button hidden
(function ensureRegisterButtonVisible(){
    try {
        const orig = document.getElementById('openRegisterBtn');
        const hidden = !orig || orig.offsetParent === null || window.getComputedStyle(orig).display === 'none' || window.getComputedStyle(orig).visibility === 'hidden';
        if (hidden) {
            const fb = document.createElement('button');
            fb.id = 'openRegisterBtnFallback';
            fb.className = 'btn btn-primary';
            fb.style.position = 'fixed';
            fb.style.right = '20px';
            fb.style.bottom = '20px';
            fb.style.zIndex = '99999';
            fb.style.boxShadow = '0 12px 32px rgba(0,0,0,0.4)';
            fb.innerHTML = '<i class="bi bi-person-plus-fill"></i> Register Student';
            fb.addEventListener('click', openRegisterModal);
            document.body.appendChild(fb);
        }
    } catch (e) { console.warn('ensureRegisterButtonVisible failed', e); }
})();

// Floating register modal: enable dragging and floating style
function enableFloatingRegister(){
    const modal = document.getElementById('registerModal');
    if (!modal) return;
    const content = modal.querySelector('.register-modal');
    if (!content) return;

    // Apply floating styles
    content.style.position = 'fixed';
    content.style.right = '40px';
    content.style.top = '80px';
    content.style.maxWidth = '920px';
    content.style.width = 'min(92%, 1180px)';
    content.style.margin = '0';
    content.style.boxShadow = '0 40px 120px rgba(2,8,23,0.7)';
    content.style.borderRadius = '18px';
    // place modal above all page elements
    modal.style.background = 'rgba(0,0,0,0.55)';
    modal.style.zIndex = '2147483646';
    content.style.zIndex = '2147483647';

    // Make header the drag handle
    const handle = content.querySelector('.register-header');
    if (!handle) return;

    let isDragging = false;
    let startX = 0, startY = 0, origX = 0, origY = 0;

    handle.style.cursor = 'grab';

    function onMouseDown(e){
        isDragging = true;
        handle.style.cursor = 'grabbing';
        startX = e.clientX;
        startY = e.clientY;
        const rect = content.getBoundingClientRect();
        origX = rect.left;
        origY = rect.top;
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
        e.preventDefault();
    }
    function onMouseMove(e){
        if (!isDragging) return;
        const dx = e.clientX - startX;
        const dy = e.clientY - startY;
        let left = origX + dx;
        let top = origY + dy;
        // keep within viewport
        const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
        const vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
        left = Math.max(8, Math.min(left, vw - content.offsetWidth - 8));
        top = Math.max(8, Math.min(top, vh - content.offsetHeight - 8));
        content.style.left = left + 'px';
        content.style.top = top + 'px';
        content.style.right = 'auto';
    }
    function onMouseUp(){
        isDragging = false;
        handle.style.cursor = 'grab';
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
    }

    // attach once
    handle.removeEventListener('mousedown', onMouseDown);
    handle.addEventListener('mousedown', onMouseDown);
}
</script>
</body>
</html>
