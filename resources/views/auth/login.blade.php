<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Malaysia Airlines OTA System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #07111f;
            font-family: 'DM Sans', sans-serif;
        }

        .mh-card {
            display: flex;
            width: 900px;
            min-height: 560px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5);
        }

        /* ── LEFT PANEL ─────────────────────────── */
        .mh-left {
            width: 42%;
            background: #c8102e;
            padding: 52px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .mh-left::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            pointer-events: none;
        }
        .mh-left::after {
            content: '';
            position: absolute;
            bottom: -70px; left: -70px;
            width: 240px; height: 240px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            pointer-events: none;
        }

        .mh-logo-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 36px;
            position: relative; z-index: 2;
        }
        .mh-logo-row img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }
        .mh-logo-placeholder {
            width: 48px; height: 48px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .mh-logo-placeholder span {
            font-family: 'Playfair Display', serif;
            color: #fff; font-size: 16px; font-weight: 700;
        }
        .mh-brand-text { color: #fff; }
        .mh-brand-text strong {
            display: block;
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            font-weight: 500;
        }
        .mh-brand-text small {
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.6);
            font-weight: 300;
        }

        .mh-tagline { position: relative; z-index: 2; color: #fff; }
        .mh-tagline h1 {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 14px;
        }
        .mh-tagline p {
            font-size: 13px;
            color: rgba(255,255,255,0.6);
            line-height: 1.75;
            font-weight: 300;
        }

        .mh-stripes {
            display: flex; gap: 6px; margin-top: 36px;
            position: relative; z-index: 2;
        }
        .mh-stripes span {
            height: 3px; border-radius: 2px; flex: 1;
            background: rgba(255,255,255,0.18);
        }
        .mh-stripes span:first-child { flex: 2; background: rgba(255,255,255,0.85); }

        .mh-copyright {
            font-size: 11px;
            color: rgba(255,255,255,0.35);
            letter-spacing: 0.5px;
            position: relative; z-index: 2;
        }

        /* ── RIGHT PANEL ────────────────────────── */
        .mh-right {
            flex: 1;
            background: #0f1e35;
            padding: 52px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .mh-form-title { margin-bottom: 36px; }
        .mh-form-title h2 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 500;
            color: #fff;
            margin-bottom: 6px;
        }
        .mh-form-title p {
            font-size: 13px;
            color: rgba(255,255,255,0.38);
            font-weight: 300;
        }

        .mh-field { margin-bottom: 22px; }
        .mh-field label {
            display: block;
            font-size: 10px;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            margin-bottom: 8px;
            font-weight: 500;
        }
        .mh-field input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 13px 16px;
            color: #fff;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
        }
        .mh-field input:focus {
            border-color: #c8102e;
            background: rgba(200,16,46,0.08);
        }
        .mh-field input::placeholder { color: rgba(255,255,255,0.18); }

        @if($errors->has('email'))
        .mh-field input[name="email"] { border-color: #e55; }
        @endif

        .mh-error {
            background: rgba(200,16,46,0.12);
            border: 1px solid rgba(200,16,46,0.3);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #ff8a97;
            margin-bottom: 20px;
        }

        .mh-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }
        .mh-remember {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: rgba(255,255,255,0.4);
            cursor: pointer; user-select: none;
        }
        .mh-remember input[type="checkbox"] {
            width: 14px; height: 14px;
            accent-color: #c8102e; cursor: pointer;
        }
        .mh-forgot {
            font-size: 12px;
            color: #c8102e;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .mh-forgot:hover { opacity: 0.7; }

        .mh-btn {
            width: 100%;
            background: #c8102e;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            letter-spacing: 0.4px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .mh-btn:hover { background: #a50d25; }
        .mh-btn:active { transform: scale(0.99); }

        .mh-divider {
            display: flex; align-items: center; gap: 12px;
            margin-top: 28px;
        }
        .mh-divider hr { flex: 1; border: none; border-top: 1px solid rgba(255,255,255,0.07); }
        .mh-divider span { font-size: 11px; color: rgba(255,255,255,0.2); white-space: nowrap; }

        @media (max-width: 700px) {
            .mh-card { flex-direction: column; width: 100%; min-height: auto; border-radius: 0; }
            .mh-left { width: 100%; padding: 36px 28px; }
            .mh-right { padding: 36px 28px; }
        }
    </style>
</head>
<body>

<div class="mh-card">

    {{-- LEFT PANEL --}}
    <div class="mh-left">
        <div>
            <div class="mh-logo-row">
                {{-- Ganti src dengan path logo MH jika ada --}}
                {{-- <img src="{{ asset('images/mh-logo.png') }}" alt="Malaysia Airlines"> --}}
                <div class="mh-logo-placeholder"><span>MH</span></div>
                <div class="mh-brand-text">
                    <strong>Malaysia Airlines</strong>
                    <small>OTA Management System</small>
                </div>
            </div>
            <div class="mh-tagline">
                <h1>Flight Operations Dashboard</h1>
                <p>Monitor on-time performance across all stations with real-time delay tracking and reporting.</p>
                <div class="mh-stripes">
                    <span></span><span></span><span></span><span></span>
                </div>
            </div>
        </div>
        <div class="mh-copyright">Malaysia Airlines Berhad &copy; {{ date('Y') }}</div>
    </div>

    {{-- RIGHT PANEL --}}
    <div class="mh-right">
        <div class="mh-form-title">
            <h2>Sign in</h2>
            <p>Enter your credentials to access the system</p>
        </div>

        {{-- Error message --}}
        @if($errors->any())
            <div class="mh-error">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('status'))
            <div class="mh-error" style="color:#6ee7b7; background: rgba(16,185,129,0.1); border-color: rgba(16,185,129,0.3);">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mh-field">
                <label for="email">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="name@malaysiaairlines.com"
                    required
                    autofocus
                    autocomplete="email"
                >
            </div>

            <div class="mh-field">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="mh-row">
                <label class="mh-remember">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="mh-forgot">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="mh-btn">
                Sign in
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 12h14M13 6l6 6-6 6"/>
                </svg>
            </button>
        </form>

        <div class="mh-divider">
            <hr><span>OTA Reporting System v1.0 — Authorized personnel only</span><hr>
        </div>
    </div>

</div>

</body>
</html>