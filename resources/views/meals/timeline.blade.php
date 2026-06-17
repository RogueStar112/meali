@extends('layouts.app')
@section('title', 'Timeline — Plated')

@push('styles')
<style>
    /* ── Page shell ── */
    .tl-page {
        height: calc(100vh - var(--nav-h));
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .tl-date-time {
        display: flex;
        justify-content: space-between;
        /* align-items: center; */
    }

    .tl-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 22px 28px 18px;
        flex-shrink: 0;
    }
    .tt-time {
        font-size: 0.68rem;
    }

    .tl-title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.45rem;
        font-weight: 600;
        line-height: 1;
    }

    .morning {
        background-color: beige;
        padding: 4px;
        border-radius: 9999px;
    }

    .night {
        background-color: navy;
        padding: 4px;
        border-radius: 9999px;
        color: beige;
    }

    .tl-subtitle { font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; }
    .tl-controls { display: flex; gap: 8px; align-items: center; }

    .btn-ghost {
        font-family: 'Outfit', sans-serif;
        font-size: 0.78rem; font-weight: 500;
        color: var(--text-secondary);
        background: var(--surface);
        border: 1px solid var(--border);
        padding: 7px 16px;
        border-radius: 100px;
        cursor: pointer;
        transition: border-color 0.15s, color 0.15s;
    }
    .btn-ghost:hover { border-color: var(--text-primary); color: var(--text-primary); }

    /* ── Legend ── */
    .tl-legend {
        display: flex; gap: 18px; align-items: center;
        padding: 0 28px 14px; flex-shrink: 0;
    }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.7rem; color: var(--text-muted); }
    .legend-line { height: 14px; border-radius: 1px; }

    /* ── Scroll container ── */
    .tl-outer {
        flex: 1;
        overflow-x: auto; overflow-y: hidden;
        position: relative;
        border-top: 1px solid var(--border);
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
        cursor: grab;
        user-select: none;
    }
    .tl-outer:active { cursor: grabbing; }
    .tl-outer::-webkit-scrollbar { height: 5px; }
    .tl-outer::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

    /* ── Inner canvas ── */
    .tl-inner { position: relative; height: 100%; }

    /* ── Sticky label bar ── */
    .tl-label-bar {
        position: sticky;
        top: 0; left: 0;
        z-index: 20;
        background: var(--bg);
        border-bottom: 1px solid var(--border);
        pointer-events: none;
    }
    .tl-year-row  { height: 28px; position: relative; }
    .tl-month-row { height: 22px; position: relative; }

    /* Day row — shows DOW + date number per column */
    .tl-day-row   { height: 38px; position: relative; overflow: hidden; }

    .tl-day-cell {
        position: absolute;
        top: 0; height: 38px;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 1px;
        border-left: 1px solid var(--border-light);
    }
    .tl-day-cell--today  { background: rgba(37,99,235,0.06); }
    .tl-day-cell--weekend .tl-day-dow,
    .tl-day-cell--weekend .tl-day-num { opacity: 0.4; }

    .tl-day-dow {
        font-size: 0.55rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: var(--text-muted);
        line-height: 1;
    }
    .tl-day-num {
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--text-secondary);
        line-height: 1;
    }
    .tl-day-cell--today .tl-day-dow,
    .tl-day-cell--today .tl-day-num { color: #2563EB; font-weight: 700; }

    .tl-lbl-year {
        position: absolute; top: 0; height: 28px; line-height: 28px;
        padding-left: 8px;
        font-family: 'Cormorant Garamond', serif;
        font-size: 0.92rem; font-weight: 600;
        color: var(--text-secondary);
        white-space: nowrap; overflow: hidden;
        border-left: 2px solid var(--text-primary);
    }
    .tl-lbl-month {
        position: absolute; top: 0; height: 22px; line-height: 22px;
        padding-left: 6px;
        font-size: 0.62rem; font-weight: 600;
        letter-spacing: 0.06em; text-transform: uppercase;
        color: var(--text-muted);
        white-space: nowrap; overflow: hidden;
    }

    /* ── Track ── */
    .tl-track { position: absolute; left: 0; }

    /* ── Grid lines ── */
    .tl-vline { position: absolute; top: 0; bottom: 0; pointer-events: none; }
    .tl-vline--day   { width: 1px; background: rgba(28,25,23,0.055); }
    .tl-vline--week  { width: 1px; background: rgba(28,25,23,0.14); }
    .tl-vline--month { width: 2px; background: rgba(28,25,23,0.25); }
    .tl-vline--year  { width: 2px; background: rgba(28,25,23,0.55); }
    .tl-vline--today { width: 2px; background: #2563EB; }

    /* ── Today label ── */
    .tl-today-lbl {
        position: absolute; top: 6px;
        font-size: 0.6rem; font-weight: 700;
        color: #2563EB; letter-spacing: 0.08em;
        text-transform: uppercase; pointer-events: none; white-space: nowrap;
    }

    /* ── Day click zone ── */
    .tl-day-zone {
        position: absolute; top: 0; bottom: 0;
        cursor: pointer; z-index: 1;
        transition: background 0.15s;
    }
    .tl-day-zone:hover { background: rgba(28,25,23,0.025); }
    .tl-day-zone.has-meals:hover { background: rgba(28,25,23,0.04); }

    /* ── Meal cards ── */
    .tl-meal {
        position: absolute;
        border-radius: 8px; overflow: hidden;
        box-shadow: var(--shadow);
        cursor: pointer;
        transition: transform 0.18s, box-shadow 0.18s;
        background: var(--surface);
        z-index: 5;
    }
    .tl-meal:hover { transform: scale(1.12); box-shadow: var(--shadow-lg); z-index: 30; }
    .tl-meal img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .tl-meal-init {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.15rem; font-weight: 600;
        color: var(--text-secondary); background: var(--border-light);
    }

    /* ── Meal hover tooltip ── */
    .tl-tooltip {
        position: fixed; z-index: 200;
        background: var(--surface);
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.13), 0 0 0 1px rgba(0,0,0,0.05);
        width: 210px; padding: 14px;
        pointer-events: none;
        opacity: 0; transform: translateY(6px) scale(0.97);
        transition: opacity 0.18s, transform 0.18s;
    }
    .tl-tooltip.show { opacity: 1; transform: translateY(0) scale(1); }
    .tt-img {
        width: 100%; aspect-ratio: 4/3; object-fit: cover;
        border-radius: 7px; margin-bottom: 10px;
        background: var(--border-light); display: block;
    }
    .tt-name { font-size: 0.86rem; font-weight: 500; color: var(--text-primary); margin-bottom: 3px; }
    .tt-date { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 9px; }
    .tt-macros { display: flex; flex-wrap: wrap; gap: 4px; }

    /* ── Day totals popover (click) ── */
    .tl-day-pop {
        position: fixed; z-index: 190;
        background: var(--surface);
        border-radius: 14px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.14), 0 0 0 1px rgba(0,0,0,0.05);
        width: 230px; padding: 18px 18px 16px;
        opacity: 0; transform: translateY(8px) scale(0.97);
        transition: opacity 0.2s, transform 0.2s;
        pointer-events: none;
    }
    .tl-day-pop.show { opacity: 1; transform: translateY(0) scale(1); pointer-events: auto; }

    .dp-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 10px;
    }
    .dp-date {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.08rem; font-weight: 600;
        color: var(--text-primary); line-height: 1.2;
    }
    .dp-count { font-size: 0.7rem; color: var(--text-muted); margin-top: 3px; }
    .dp-close {
        flex-shrink: 0;
        width: 22px; height: 22px;
        border: none; background: none; cursor: pointer;
        color: var(--text-muted);
        display: flex; align-items: center; justify-content: center;
        border-radius: 5px;
        transition: background 0.15s, color 0.15s;
        margin-left: 8px; margin-top: -2px;
    }
    .dp-close:hover { background: var(--border-light); color: var(--text-primary); }

    .dp-divider { border: none; border-top: 1px solid var(--border-light); margin: 10px 0; }

    .dp-macros { display: flex; flex-wrap: wrap; gap: 5px; }
    .dp-macros .macro { font-size: 0.75rem; padding: 4px 10px; }

    .dp-rows { display: flex; flex-direction: column; gap: 6px; margin-top: 12px; }
    .dp-row {
        display: flex; align-items: center;
        justify-content: space-between;
        font-size: 0.75rem;
    }
    .dp-row-label { color: var(--text-muted); }
    .dp-row-val   { font-weight: 500; color: var(--text-primary); }

    /* ── Empty ── */
    .tl-empty {
        flex: 1; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        gap: 14px; color: var(--text-muted);
    }
    .tl-empty h2 { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:500; color:var(--text-secondary); }
    .tl-empty p  { font-size:.82rem; max-width:220px; text-align:center; line-height:1.55; }
</style>
@endpush

@section('content')
<div class="tl-page">

    @if(empty($meals))
        <div class="tl-empty">
            <div class="empty-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <h2>No meals yet</h2>
            <p>Log your first meal to see it appear on the timeline.</p>
            <a class="btn-primary" href="{{ route('meals.create') }}">Log a meal</a>
        </div>

    @else

        <div class="tl-topbar">
            <div>
                <div class="tl-title">Timeline</div>
                <div class="tl-subtitle">Drag to pan · hover a card · click a day for totals</div>
            </div>
            <div class="tl-controls">
                <button class="btn-ghost" id="btnZoomOut">− Zoom</button>
                <button class="btn-ghost" id="btnZoomIn">+ Zoom</button>
                <button class="btn-ghost" id="btnToday">Today</button>
            </div>
        </div>

        <div class="tl-legend">
            <div class="legend-item"><div class="legend-line" style="width:1px;background:rgba(28,25,23,0.055);"></div> Day</div>
            <div class="legend-item"><div class="legend-line" style="width:1px;background:rgba(28,25,23,0.15);"></div> Week</div>
            <div class="legend-item"><div class="legend-line" style="width:2px;background:rgba(28,25,23,0.28);"></div> Month</div>
            <div class="legend-item"><div class="legend-line" style="width:2px;background:rgba(28,25,23,0.58);"></div> Year</div>
            <div class="legend-item"><div class="legend-line" style="width:2px;background:#2563EB;"></div> Today</div>
        </div>

        <div class="tl-outer" id="tlOuter">
            <div class="tl-inner" id="tlInner">
                {{-- Sticky label bar: year / month / day rows --}}
                <div class="tl-label-bar" id="tlLabelBar">
                    <div class="tl-year-row"  id="yearRow"></div>
                    <div class="tl-month-row" id="monthRow"></div>
                    <div class="tl-day-row"   id="dayRow"></div>
                </div>
                <div class="tl-track" id="tlTrack"></div>
            </div>
        </div>

        {{-- Meal hover tooltip --}}
        <div class="tl-tooltip" id="tlTooltip">
            <img id="ttImg" class="tt-img" src="" alt="">

                <div id="ttName" class="tt-name"></div>

            <div class="tl-date-time">
                <div id="ttDate" class="tt-date"></div>
                 <div id="ttTime" class="tt-time"></div>
            </div>
            <div id="ttMacros" class="tt-macros"></div>
        </div>

        {{-- Day click popover --}}
        <div class="tl-day-pop" id="tlDayPop">
            <div class="dp-header">
                <div>
                    <div class="dp-date"  id="dpDate"></div>
                    <div class="dp-count" id="dpCount"></div>
                </div>
                <button class="dp-close" id="dpClose" title="Close">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <hr class="dp-divider">
            <div class="dp-macros" id="dpMacros"></div>
            <div class="dp-rows"   id="dpRows"></div>
        </div>

    @endif
</div>

<script>
(function () {
    const meals = @json($meals);
    if (!meals || meals.length === 0) return;

    /* ── Config ── */
    const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const DAYS_SHORT   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

    const LABEL_H       = 88;   // year(28) + month(22) + day(38)
    const CARD          = 50;
    const CARD_GAP      = 8;
    const TRACK_PAD_TOP = 20;
    const TRACK_PAD_BOT = 24;

    let DAY_W = 64;

    /* ── DOM refs ── */
    const outer    = document.getElementById('tlOuter');
    const tlInner  = document.getElementById('tlInner');
    const labelBar = document.getElementById('tlLabelBar');
    const yearRow  = document.getElementById('yearRow');
    const monthRow = document.getElementById('monthRow');
    const dayRow   = document.getElementById('dayRow');
    const tlTrack  = document.getElementById('tlTrack');

    const tooltip  = document.getElementById('tlTooltip');
    const ttImg    = document.getElementById('ttImg');
    const ttName   = document.getElementById('ttName');
    const ttTime   = document.getElementById('ttTime');
    const ttDate   = document.getElementById('ttDate');
    const ttMacros = document.getElementById('ttMacros');

    const dayPop  = document.getElementById('tlDayPop');
    const dpDate  = document.getElementById('dpDate');
    const dpCount = document.getElementById('dpCount');
    const dpMacros= document.getElementById('dpMacros');
    const dpRows  = document.getElementById('dpRows');

    document.getElementById('dpClose').addEventListener('click', hideDayPop);

    /* ── Date helpers ── */
    function toStr(d) {
        return d.getFullYear() + '-' +
            String(d.getMonth() + 1).padStart(2, '0') + '-' +
            String(d.getDate()).padStart(2, '0');
    }
    function parseDate(s) {
        const [y, m, d] = s.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    /* ── Date range ── */
    const today     = new Date(); today.setHours(0,0,0,0);
    const allEaten  = meals.map(m => parseDate(m.eaten_at));
    const minDate   = new Date(Math.min(...allEaten));
    const startDate = new Date(minDate.getFullYear(), minDate.getMonth(), 1);
    const todayStr  = toStr(today);

    function buildDayList() {
        const days = [];
        const d = new Date(startDate);
        while (d <= today) { days.push(new Date(d)); d.setDate(d.getDate() + 1); }
        return days;
    }

    /* ── Meals by date ── */
    const mealsByDate = {};
    meals.forEach(m => {
        (mealsByDate[m.eaten_at] = mealsByDate[m.eaten_at] || []).push(m);
    });

    /* ── Track height ── */
    const maxPerDay = Math.max(...Object.values(mealsByDate).map(ms => ms.length), 1);
    const trackH    = TRACK_PAD_TOP + maxPerDay * (CARD + CARD_GAP) + TRACK_PAD_BOT;

    /* ══════════════════════════════════
       RENDER
    ══════════════════════════════════ */
    let todayX = null;

    function render() {
        const allDays = buildDayList();
        const totalW  = allDays.length * DAY_W;

        yearRow.innerHTML  = '';
        monthRow.innerHTML = '';
        dayRow.innerHTML   = '';
        tlTrack.innerHTML  = '';

        labelBar.style.width = totalW + 'px';
        tlInner.style.width  = totalW + 'px';
        tlInner.style.height = (LABEL_H + trackH) + 'px';
        tlTrack.style.width  = totalW + 'px';
        tlTrack.style.height = trackH + 'px';
        tlTrack.style.top    = LABEL_H + 'px';

        todayX = null;

        /* — Year & Month labels — */
        let lastYearKey = null, lastMonthKey = null;

        allDays.forEach((day, i) => {
            const x  = i * DAY_W;
            const y  = day.getFullYear();
            const mo = day.getMonth();
            const yk = '' + y;
            const mk = y + '-' + mo;

            if (yk !== lastYearKey) {
                const el = document.createElement('div');
                el.className  = 'tl-lbl-year';
                el.style.left = x + 'px';
                el.style.maxWidth = (totalW - x) + 'px';
                el.textContent = y;
                yearRow.appendChild(el);
                lastYearKey = yk;
            }
            if (mk !== lastMonthKey) {
                const el = document.createElement('div');
                el.className  = 'tl-lbl-month';
                el.style.left = x + 'px';
                el.style.maxWidth = (totalW - x) + 'px';
                el.textContent = DAY_W >= 36 ? MONTHS_SHORT[mo] : MONTHS_SHORT[mo].charAt(0);
                monthRow.appendChild(el);
                lastMonthKey = mk;
            }
        });

        /* — Day cells (DOW + day number) — */
        allDays.forEach((day, i) => {
            const x      = i * DAY_W;
            const dow    = day.getDay();
            const dom    = day.getDate();
            const isWknd = (dow === 0 || dow === 6);
            const isTd   = toStr(day) === todayStr;

            const cell = document.createElement('div');
            cell.className = 'tl-day-cell' +
                (isTd   ? ' tl-day-cell--today'   : '') +
                (isWknd ? ' tl-day-cell--weekend' : '');
            cell.style.left  = x + 'px';
            cell.style.width = DAY_W + 'px';

            // Always show day number; only show DOW text if column is wide enough
            if (DAY_W >= 32) {
                const dow_el = document.createElement('div');
                dow_el.className   = 'tl-day-dow';
                dow_el.textContent = DAYS_SHORT[dow];
                cell.appendChild(dow_el);
            }

            const num_el = document.createElement('div');
            num_el.className   = 'tl-day-num';
            num_el.textContent = dom;
            cell.appendChild(num_el);

            dayRow.appendChild(cell);
        });

        /* — Grid lines, click zones & meal cards — */
        allDays.forEach((day, i) => {
            const x       = i * DAY_W;
            const dateStr = toStr(day);
            const isToday = dateStr === todayStr;
            const dow     = day.getDay();
            const dom     = day.getDate();
            const mo      = day.getMonth();

            const isYear  = dom === 1 && mo === 0;
            const isMonth = dom === 1;
            const isWeek  = dow === 1;

            /* Vertical line */
            const line = document.createElement('div');
            line.className = 'tl-vline ' + (
                isToday ? 'tl-vline--today' :
                isYear  ? 'tl-vline--year'  :
                isMonth ? 'tl-vline--month' :
                isWeek  ? 'tl-vline--week'  : 'tl-vline--day'
            );
            line.style.left = x + 'px';
            tlTrack.appendChild(line);

            if (isToday) {
                todayX = x;
                const lbl = document.createElement('div');
                lbl.className  = 'tl-today-lbl';
                lbl.style.left = (x + 4) + 'px';
                lbl.textContent = 'Today';
                tlTrack.appendChild(lbl);
            }

            /* Click zone (covers full day column) */
            const dayMeals = mealsByDate[dateStr] || [];
            const zone = document.createElement('div');
            zone.className = 'tl-day-zone' + (dayMeals.length ? ' has-meals' : '');
            zone.style.left  = x + 'px';
            zone.style.width = DAY_W + 'px';
            if (dayMeals.length) {
                zone.addEventListener('click', e => {
                    // Don't fire if clicking on a meal card
                    if (e.target.closest('.tl-meal')) return;
                    showDayPop(dateStr, dayMeals, e);
                });
            }
            tlTrack.appendChild(zone);

            /* Meal cards */
            dayMeals.forEach((meal, mi) => {
                const card = document.createElement('div');
                card.className = 'tl-meal';
                card.style.width  = CARD + 'px';
                card.style.height = CARD + 'px';
                card.style.left   = (x + Math.floor((DAY_W - CARD) / 2)) + 'px';
                card.style.top    = (TRACK_PAD_TOP + mi * (CARD + CARD_GAP)) + 'px';

                if (meal.image_url) {
                    const img = document.createElement('img');
                    img.src = meal.image_url; img.alt = meal.name;
                    card.appendChild(img);
                } else {
                    const init = document.createElement('div');
                    init.className   = 'tl-meal-init';
                    init.textContent = meal.name.charAt(0).toUpperCase();
                    card.appendChild(init);
                }

                card.addEventListener('mouseenter', e => showTip(meal, e));
                card.addEventListener('mouseleave', hideTip);
                tlTrack.appendChild(card);
            });
        });

        scrollToToday(false);
    }

    /* ── Scroll to today ── */
    function scrollToToday(smooth = true) {
        if (todayX === null) return;
        const target = Math.max(0, todayX - outer.clientWidth * 0.72);
        outer.scrollTo({ left: target, behavior: smooth ? 'smooth' : 'instant' });
    }

    /* ── Zoom ── */
    document.getElementById('btnZoomIn').addEventListener('click', () => {
        if (DAY_W >= 120) return;
        DAY_W = Math.min(120, DAY_W + 12);
        render();
    });
    document.getElementById('btnZoomOut').addEventListener('click', () => {
        if (DAY_W <= 20) return;
        DAY_W = Math.max(20, DAY_W - 12);
        render();
    });
    document.getElementById('btnToday').addEventListener('click', () => scrollToToday(true));

    /* ── Drag-to-pan ── */
    let dragging = false, dragStartX = 0, scrollStart = 0, didDrag = false;
    outer.addEventListener('mousedown', e => {
        dragging = true; didDrag = false;
        dragStartX = e.pageX; scrollStart = outer.scrollLeft;
        outer.style.userSelect = 'none';
    });
    window.addEventListener('mouseup', () => { dragging = false; outer.style.userSelect = ''; });
    window.addEventListener('mousemove', e => {
        if (!dragging) return;
        const dx = e.pageX - dragStartX;
        if (Math.abs(dx) > 4) didDrag = true;
        outer.scrollLeft = scrollStart - dx;
    });

    /* ── Meal hover tooltip ── */
    let tipTimeout;
    function showTip(meal, e) {
        hideDayPop();
        clearTimeout(tipTimeout);
        if (meal.image_url) { ttImg.src = meal.image_url; ttImg.style.display = 'block'; }
        else                { ttImg.style.display = 'none'; }
        ttName.textContent = meal.name;
        const d = parseDate(meal.eaten_at);
        
        // const d_time = new Date('en-GB', meal.eaten_at_time);
        ttDate.textContent = d.toLocaleDateString('en-GB', { weekday:'short', day:'numeric', month:'long', year:'numeric' });

        const ttHour = meal.eaten_at_hour
        
        if (ttHour >= 18 && ttHour <= 25 || ttHour >= 0 && ttHour < 6) {
               ttTime.textContent = `🌙 ` + meal.eaten_at_time;
               ttTime.classList.remove("morning");
               ttTime.classList.add("night");
        // } if (ttHour >= 13 && ttHour <= 17) {
        //     ttTime.textContent = `🥪 ` + meal.eaten_at_time;  
        } else {
               ttTime.textContent = `☀️ ` + meal.eaten_at_time;
               ttTime.classList.remove("night");
               ttTime.classList.add("morning");
        }
    



        ttMacros.innerHTML =
            `<span class="macro macro-cal">${meal.calories} kcal</span>` +
            `<span class="macro macro-pro">${meal.protein}g P</span>` +
            `<span class="macro macro-carb">${meal.carbs}g C</span>` +
            `<span class="macro macro-fat">${meal.fat}g F</span>`;
        positionEl(tooltip, e, 210, 220);
        tooltip.classList.add('show');
    }
    function hideTip() {
        tipTimeout = setTimeout(() => tooltip.classList.remove('show'), 80);
    }

    /* ── Day totals popover ── */
    function showDayPop(dateStr, dayMeals, e) {
        if (didDrag) return; // don't open after a drag
        hideTip();

        const d = parseDate(dateStr);
        dpDate.textContent  = d.toLocaleDateString('en-GB', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
        dpCount.textContent = dayMeals.length + ' meal' + (dayMeals.length !== 1 ? 's' : '');

        const tot = dayMeals.reduce((acc, m) => ({
            cal:  acc.cal  + m.calories,
            pro:  acc.pro  + m.protein,
            carb: acc.carb + m.carbs,
            fat:  acc.fat  + m.fat,
        }), { cal: 0, pro: 0, carb: 0, fat: 0 });

        dpMacros.innerHTML =
            `<span class="macro macro-cal">${tot.cal} kcal</span>` +
            `<span class="macro macro-pro">${tot.pro.toFixed(1)}g P</span>` +
            `<span class="macro macro-carb">${tot.carb.toFixed(1)}g C</span>` +
            `<span class="macro macro-fat">${tot.fat.toFixed(1)}g F</span>`;

        dpRows.innerHTML =
            `<div class="dp-row"><span class="dp-row-label">Calories</span><span class="dp-row-val">${tot.cal} kcal</span></div>` +
            `<div class="dp-row"><span class="dp-row-label">Protein</span><span class="dp-row-val">${tot.pro.toFixed(1)} g</span></div>` +
            `<div class="dp-row"><span class="dp-row-label">Carbs</span><span class="dp-row-val">${tot.carb.toFixed(1)} g</span></div>` +
            `<div class="dp-row"><span class="dp-row-label">Fat</span><span class="dp-row-val">${tot.fat.toFixed(1)} g</span></div>`;

        positionEl(dayPop, e, 230, 220);
        dayPop.classList.add('show');
    }
    function hideDayPop() { dayPop.classList.remove('show'); }

    // Close on outside click
    document.addEventListener('click', e => {
        if (!dayPop.contains(e.target) && !e.target.closest('.tl-day-zone')) {
            hideDayPop();
        }
    });

    /* ── Shared positioning helper ── */
    function positionEl(el, e, w, h) {
        const pad = 14;
        let x = e.clientX + 16;
        let y = e.clientY - h / 2;
        if (x + w > window.innerWidth - pad)  x = e.clientX - w - 16;
        if (y < pad)                           y = pad;
        if (y + h > window.innerHeight - pad)  y = window.innerHeight - h - pad;
        el.style.left = x + 'px';
        el.style.top  = y + 'px';
    }

    /* ── Init ── */
    render();
})();
</script>
@endsection