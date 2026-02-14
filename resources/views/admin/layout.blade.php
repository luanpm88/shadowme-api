<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Shadow Me Admin')</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    />
    <style>
        :root {
            color-scheme: light;
            --bg: #f7f2ec;
            --surface: #ffffff;
            --card: #f1e9e1;
            --text: #15161a;
            --muted: #857b6f;
            --border: #ded5cb;
            --tint: #e4572e;
            --accent: #2f6f6d;
            --bs-body-bg: var(--bg);
            --bs-body-color: var(--text);
            --bs-primary: var(--tint);
            --bs-primary-rgb: 228, 87, 46;
            --bs-secondary: var(--accent);
            --bs-border-color: var(--border);
            --bs-link-color: var(--accent);
            --bs-link-hover-color: #245452;
            --bs-body-font-family: "Avenir Next", "Iowan Old Style", system-ui, -apple-system, sans-serif;
        }
        body {
            font-family: "Iowan Old Style", "Avenir Next", system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        .admin-navbar {
            background: linear-gradient(135deg, rgba(228,87,46,0.08), rgba(47,111,109,0.08));
            border-bottom: 1px solid var(--border);
        }
        .card {
            border-radius: 16px;
            border-color: var(--border);
            box-shadow: 0 14px 30px rgba(17, 24, 39, 0.06);
        }
        .btn { border-radius: 999px; font-weight: 600; }
        .btn-primary {
            background-color: var(--tint);
            border-color: var(--tint);
        }
        .btn-primary:hover {
            background-color: #d94f2b;
            border-color: #d94f2b;
        }
        .btn-outline-secondary {
            color: var(--tint);
            border-color: rgba(228, 87, 46, 0.6);
        }
        .btn-outline-secondary:hover {
            background: rgba(228, 87, 46, 0.12);
            color: var(--tint);
        }
        .btn-outline-primary {
            color: var(--tint);
            border-color: rgba(228, 87, 46, 0.6);
        }
        .btn-outline-primary:hover {
            background: rgba(228, 87, 46, 0.12);
            color: var(--tint);
        }
        .btn-link { color: var(--accent); }
        .muted { color: var(--muted); font-size: 13px; }
        .badge-soft {
            background: var(--card);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .table-shell {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }
        .table-video thead th {
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.08em;
            color: var(--muted);
            border-bottom-color: var(--border);
        }
        .table-video tbody tr {
            border-bottom: 1px solid var(--border);
        }
        .table-video tbody tr:last-child {
            border-bottom: none;
        }
        .table-video td, .table-video th {
            padding: 16px 18px;
        }
        .notice {
            background: rgba(228,87,46,0.08);
            border: 1px solid rgba(228,87,46,0.3);
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            color: var(--text);
        }
        .notice.error {
            background: rgba(150, 30, 30, 0.08);
            border-color: rgba(150, 30, 30, 0.3);
        }
        .form-control,
        .form-select {
            border-radius: 12px;
            border-color: var(--border);
        }
        textarea.form-control { min-height: 110px; }
        .segment-row {
            display: grid;
            grid-template-columns: 100px 100px 1fr auto;
            gap: 16px;
            align-items: start;
            padding: 14px;
            border-bottom: 1px dashed var(--border);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .segment-row:hover {
            background: rgba(228, 87, 46, 0.04);
        }
        .segment-row.segment-active {
            background: rgba(228, 87, 46, 0.1);
            border: 1px solid rgba(228, 87, 46, 0.3);
            border-bottom: 1px solid rgba(228, 87, 46, 0.3);
            box-shadow: 0 2px 8px rgba(228, 87, 46, 0.15);
        }
        .segment-row:last-child { border-bottom: none; }
        .segment-actions {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .btn-action {
            width: 28px;
            height: 28px;
            padding: 0;
            border: 1px solid var(--border);
            background: var(--surface);
            border-radius: 6px;
            font-size: 13px;
            line-height: 1;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-action:hover {
            background: var(--tint);
            color: white;
            border-color: var(--tint);
            transform: scale(1.1);
        }
        .btn-action:active {
            transform: scale(0.95);
        }
        .segments-list { 
            margin-top: 14px;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 8px;
        }
        .segments-list::-webkit-scrollbar {
            width: 8px;
        }
        .segments-list::-webkit-scrollbar-track {
            background: var(--card);
            border-radius: 4px;
        }
        .segments-list::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }
        .segments-list::-webkit-scrollbar-thumb:hover {
            background: var(--muted);
        }
        .video-title-editor {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .video-title-input {
            font-weight: 600;
            font-size: 16px;
            padding: 8px 12px;
            border: 2px solid rgba(228, 87, 46, 0.1);
            border-radius: 8px;
            width: 100%;
            background: transparent;
            color: var(--text);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .video-title-input:hover {
            background: rgba(228, 87, 46, 0.06);
            border-color: rgba(228, 87, 46, 0.2);
        }
        .video-title-input:focus {
            outline: none;
            background: var(--surface);
            border-color: var(--tint);
            box-shadow: 0 0 0 3px rgba(228, 87, 46, 0.1);
        }
        .video-title-edit-hint {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: var(--muted);
            opacity: 0;
            transition: opacity 0.2s ease;
            pointer-events: none;
        }
        .video-title-input:hover ~ .video-title-edit-hint {
            opacity: 1;
        }
        .video-placeholder {
            width: 100%;
            height: 280px;
            border-radius: 12px;
            background-size: cover;
            background-position: center;
            background-color: var(--surface);
        }
        .video-placeholder-container {
            width: 100%;
            height: 280px;
            border-radius: 12px;
            background-size: cover;
            background-position: center;
            background-color: var(--surface);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .video-placeholder-container:hover {
            filter: brightness(0.85);
        }
        .play-button {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }
        .play-button:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }
        .play-button:active {
            transform: scale(0.98);
        }
        .play-button::after {
            content: '▶';
            font-size: 32px;
            color: var(--tint);
            margin-left: 4px;
        }
        .video-thumb-small {
            width: 80px;
            height: 45px;
            border-radius: 6px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .title-status {
            font-size: 12px;
            margin-top: 4px;
            min-height: 16px;
            transition: all 0.2s ease;
            color: var(--muted);
        }
        .title-status.success {
            color: #2f6f6d;
        }
        .title-status.error {
            color: #d93c32;
        }
    </style>
</head>
<body>
    <nav class="navbar admin-navbar">
        <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h5 mb-0">Shadow Me Admin</h1>
                <div class="muted">Manage uploads, transcripts, and segments.</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="/admin/videos" class="btn btn-outline-secondary">Videos</a>
                <a href="/admin/users" class="btn btn-outline-secondary">Users</a>
                <a href="/admin/videos/create" class="btn btn-primary">Upload</a>
                <form method="POST" action="/logout" class="m-0">
                    @csrf
                    <button class="btn btn-outline-primary" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="container py-4">
        @yield('content')
    </main>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
    ></script>
</body>
</html>
