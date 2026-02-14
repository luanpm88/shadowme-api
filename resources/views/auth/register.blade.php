<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register · Shadow Me</title>
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
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Iowan Old Style", "Avenir Next", system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: grid;
            place-items: center;
            min-height: 100vh;
        }
        .card {
            width: min(460px, 92vw);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 16px 36px rgba(17, 24, 39, 0.08);
        }
        h1 { margin: 0 0 8px; font-size: 26px; }
        p { margin: 0 0 18px; color: var(--muted); }
        label { display: block; font-weight: 600; margin-bottom: 6px; }
        input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 14px;
            font-family: inherit;
        }
        .btn {
            width: 100%;
            border: none;
            border-radius: 999px;
            padding: 12px 16px;
            font-weight: 700;
            cursor: pointer;
            background: var(--tint);
            color: white;
        }
        .muted { color: var(--muted); font-size: 13px; }
        .link { color: var(--accent); text-decoration: none; font-weight: 600; }
        .error { color: #9a2f2f; font-size: 13px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Create account</h1>
        <p>Register to access the admin dashboard.</p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/register">
            @csrf
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required />
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required />
            <label>Password</label>
            <input type="password" name="password" required />
            <label>Confirm password</label>
            <input type="password" name="password_confirmation" required />
            <div style="margin-bottom: 16px;">
                <span class="muted">Already have an account?</span>
                <a class="link" href="/login">Login</a>
            </div>
            <button class="btn" type="submit">Create account</button>
        </form>
    </div>
</body>
</html>
