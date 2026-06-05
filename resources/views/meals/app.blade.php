<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Plated')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:          #F8F6F3;
            --surface:     #FFFFFF;
            --border:      #E8E4DF;
            --border-light:#F0EDE9;
            --text-primary:#1C1917;
            --text-secondary:#78716C;
            --text-muted:  #A8A29E;
            --accent:      #1C1917;
            --cal:         #D97706;
            --cal-bg:      #FEF3C7;
            --pro:         #2563EB;
            --pro-bg:      #DBEAFE;
            --carb:        #059669;
            --carb-bg:     #D1FAE5;
            --fat:         #DC2626;
            --fat-bg:      #FEE2E2;
            --radius:      10px;
            --shadow:      0 1px 4px rgba(0,0,0,0.07), 0 0 0 1px rgba(0,0,0,0.03);
            --shadow-lg:   0 4px 20px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.04);
            --nav-h:       58px;
        }

        html { font-size: 15px; }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Nav ── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--nav-h);
            background: rgba(248, 246, 243, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 100;
        }
        .nav-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.55rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            color: var(--text-primary);
            text-decoration: none;
        }
        .nav-brand em {
            font-style: italic;
            font-weight: 400;
            color: var(--text-secondary);
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--accent);
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.02em;
            padding: 8px 18px;
            border-radius: 100px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.15s, transform 0.15s;
        }
        .btn-primary:hover { opacity: 0.8; transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        /* ── Flash ── */
        .flash {
            position: fixed;
            top: calc(var(--nav-h) + 12px);
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-primary);
            color: #fff;
            font-size: 0.8rem;
            padding: 9px 20px;
            border-radius: 100px;
            z-index: 200;
            animation: fadeSlide 0.3s ease both;
        }
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateX(-50%) translateY(-6px); }
            to   { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        /* ── Page wrapper ── */
        .page { padding-top: var(--nav-h); }

        /* ── Macro badge ── */
        .macro {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 0.7rem;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 100px;
        }
        .macro-cal  { color: var(--cal);  background: var(--cal-bg);  }
        .macro-pro  { color: var(--pro);  background: var(--pro-bg);  }
        .macro-carb { color: var(--carb); background: var(--carb-bg); }
        .macro-fat  { color: var(--fat);  background: var(--fat-bg);  }
    </style>

    @stack('styles')
</head>
<body>

<nav>
    <a class="nav-brand" href="{{ route('meals.index') }}">Plat<em>ed</em></a>
    <a class="btn-primary" href="{{ route('meals.create') }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Log Meal
    </a>
</nav>

@if(session('success'))
    <div class="flash" x-data x-init="setTimeout(() => $el.remove(), 3000)">
        {{ session('success') }}
    </div>
@endif

<main class="page">
    @yield('content')
</main>

</body>
</html>