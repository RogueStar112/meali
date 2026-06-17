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

        /* ═══════════════════════════
           LIGHT THEME (default)
        ═══════════════════════════ */
        :root {
            --bg:            #F8F6F3;
            --bg-nav:        rgba(248,246,243,0.88);
            --surface:       #FFFFFF;
            --surface-hover: #FAFAF8;
            --border:        #E8E4DF;
            --border-light:  #F0EDE9;
            --text-primary:  #1C1917;
            --text-secondary:#78716C;
            --text-muted:    #A8A29E;
            --accent:        #1C1917;
            --accent-fg:     #FFFFFF;

            --cal:     #D97706; --cal-bg:  #FEF3C7;
            --pro:     #2563EB; --pro-bg:  #DBEAFE;
            --carb:    #059669; --carb-bg: #D1FAE5;
            --fat:     #DC2626; --fat-bg:  #FEE2E2;

            --good:    #059669; --good-bg: #D1FAE5;
            --warn:    #DC2626; --warn-bg: #FEE2E2;

            --radius:    10px;
            --shadow:    0 1px 4px rgba(0,0,0,0.07), 0 0 0 1px rgba(0,0,0,0.03);
            --shadow-lg: 0 4px 20px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.04);
            --nav-h:     58px;
        }

        /* ═══════════════════════════
           DARK THEME
        ═══════════════════════════ */
        html.dark {
            --bg:            #121212;
            --bg-nav:        rgba(18,18,18,0.92);
            --surface:       #1E1E1E;
            --surface-hover: #262626;
            --border:        #2E2E2E;
            --border-light:  #252525;
            --text-primary:  #E8E4DF;
            --text-secondary:#9A9590;
            --text-muted:    #6B6560;
            --accent:        #E8E4DF;
            --accent-fg:     #121212;

            --cal:     #F59E0B; --cal-bg:  rgba(245,158,11,0.15);
            --pro:     #60A5FA; --pro-bg:  rgba(96,165,250,0.15);
            --carb:    #34D399; --carb-bg: rgba(52,211,153,0.15);
            --fat:     #F87171; --fat-bg:  rgba(248,113,113,0.15);

            --good:    #34D399; --good-bg: rgba(52,211,153,0.12);
            --warn:    #F87171; --warn-bg: rgba(248,113,113,0.12);

            --shadow:    0 1px 4px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.04);
            --shadow-lg: 0 4px 20px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.05);
        }

        html { font-size: 15px; }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            transition: background 0.25s, color 0.25s;
        }

        /* ── Nav ── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--nav-h);
            background: var(--bg-nav);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            padding: 0 24px;
            z-index: 100;
        }
        .nav-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.55rem; font-weight: 600;
            letter-spacing: -0.01em;
            color: var(--text-primary);
            text-decoration: none;
        }
        .nav-brand em { font-style: italic; font-weight: 400; color: var(--text-secondary); }

        .nav-center {
            display: flex; align-items: center; gap: 2px;
            background: var(--bg); border: 1px solid var(--border);
            border-radius: 10px; padding: 3px;
        }
        .nav-link {
            font-size: 0.78rem; font-weight: 400;
            color: var(--text-muted); text-decoration: none;
            padding: 5px 14px; border-radius: 7px;
            transition: all 0.15s; white-space: nowrap;
        }
        .nav-link:hover { color: var(--text-primary); background: rgba(128,128,128,0.08); }
        .nav-link.active {
            color: var(--text-primary);
            background: var(--surface);
            font-weight: 500;
            box-shadow: var(--shadow);
        }

        .nav-right { display: flex; justify-content: flex-end; align-items: center; gap: 10px; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--accent); color: var(--accent-fg);
            font-family: 'Outfit', sans-serif;
            font-size: 0.8rem; font-weight: 500;
            letter-spacing: 0.02em;
            padding: 8px 18px; border-radius: 100px;
            border: none; cursor: pointer; text-decoration: none;
            transition: opacity 0.15s, transform 0.15s;
        }
        .btn-primary:hover { opacity: 0.8; transform: translateY(-1px); }
        .btn-primary:active { transform: translateY(0); }

        /* ── Dark mode toggle ── */
        .theme-toggle {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--border); border-radius: 8px;
            background: var(--surface); cursor: pointer;
            color: var(--text-muted);
            transition: border-color 0.15s, color 0.15s;
        }
        .theme-toggle:hover { border-color: var(--text-primary); color: var(--text-primary); }
        .theme-toggle .icon-sun  { display: none; }
        .theme-toggle .icon-moon { display: block; }
        html.dark .theme-toggle .icon-sun  { display: block; }
        html.dark .theme-toggle .icon-moon { display: none; }

        /* ── Flash ── */
        .flash {
            position: fixed;
            top: calc(var(--nav-h) + 12px);
            left: 50%; transform: translateX(-50%);
            background: var(--text-primary); color: var(--bg);
            font-size: 0.8rem; padding: 9px 20px;
            border-radius: 100px; z-index: 200;
            animation: fadeSlide 0.3s ease both;
        }
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateX(-50%) translateY(-6px); }
            to   { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        .page { padding-top: var(--nav-h); }

        /* ── Shared macro badge ── */
        .macro {
            display: inline-flex; align-items: center;
            gap: 3px; font-size: 0.7rem; font-weight: 500;
            padding: 3px 8px; border-radius: 100px;
        }
        .macro-cal  { color: var(--cal);  background: var(--cal-bg);  }
        .macro-pro  { color: var(--pro);  background: var(--pro-bg);  }
        .macro-carb { color: var(--carb); background: var(--carb-bg); }
        .macro-fat  { color: var(--fat);  background: var(--fat-bg);  }

        /* ── Food notification badges ── */
        .note-badge {
            display: inline-flex; align-items: center; gap: 3px;
            font-size: 0.62rem; font-weight: 600;
            padding: 2px 7px; border-radius: 100px;
            letter-spacing: 0.02em;
        }
        .note-good { color: var(--good); background: var(--good-bg); }
        .note-warn { color: var(--warn); background: var(--warn-bg); }

        /* ── Progress bar helper ── */
        .prog-bar-track {
            height: 4px; border-radius: 2px;
            background: var(--border-light);
            overflow: hidden;
        }
        .prog-bar-fill {
            height: 100%; border-radius: 2px;
            transition: width 0.4s ease;
        }

        button { border: none; background: none; font-family: inherit; }
    </style>

    @stack('styles')
</head>
<body>

<nav>
    <a class="nav-brand" href="{{ route('meals.index') }}">Plat<em>ed</em></a>

    <div class="nav-center">
        <a class="nav-link {{ request()->routeIs('meals.index')    ? 'active' : '' }}"
           href="{{ route('meals.index') }}">Gallery</a>
        <a class="nav-link {{ request()->routeIs('meals.timeline') ? 'active' : '' }}"
           href="{{ route('meals.timeline') }}">Timeline</a>
        <a class="nav-link {{ request()->routeIs('goals.*')        ? 'active' : '' }}"
           href="{{ route('goals.edit') }}">Goals</a>
    </div>

    <div class="nav-right">
        <button class="theme-toggle" id="themeToggle" title="Toggle dark mode">
            <svg class="icon-moon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
            <svg class="icon-sun" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
        </button>
        <a class="btn-primary" href="{{ route('meals.create') }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Log Meal
        </a>
    </div>
</nav>

@if(session('success'))
    <div class="flash" x-data x-init="setTimeout(() => $el.remove(), 3000)">
        {{ session('success') }}
    </div>
@endif

<main class="page">
    @yield('content')
</main>

<script>
    // Dark mode persistence
    (function() {
        const t = document.getElementById('themeToggle');
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
        t.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        });
    })();
</script>
@stack('scripts')
</body>
</html>
