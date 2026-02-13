<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shadow Me Admin</title>
    <style>
        body { font-family: ui-sans-serif, system-ui; background: #0f1114; color: #f4ede5; margin: 0; }
        header { padding: 32px; border-bottom: 1px solid #2c3138; }
        main { padding: 32px; }
        .card { background: #171a1f; border-radius: 16px; padding: 24px; border: 1px solid #2c3138; }
        .grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        button { background: #f4ede5; color: #0f1114; border: none; padding: 10px 16px; border-radius: 999px; font-weight: 600; cursor: pointer; }
        small { color: #b3a89d; }
    </style>
</head>
<body>
    <header>
        <h1>Shadow Me Admin</h1>
        <p>Manage videos, transcripts, and segments. Connect this panel to the API endpoints as needed.</p>
    </header>
    <main>
        <div class="card">
            <h2>Quick Actions</h2>
            <div class="grid">
                <button>Create Video</button>
                <button>Upload Transcript</button>
                <button>Review Segments</button>
            </div>
            <p><small>Tip: Use the API endpoints under <code>/api/v1</code> with an admin token.</small></p>
        </div>
    </main>
</body>
</html>
