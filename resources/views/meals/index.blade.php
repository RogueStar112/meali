@extends('layouts.app')
@section('title', 'Plated — Gallery')

@push('styles')
<style>
    /* ── Gallery shell ── */
    .gallery-wrap {
        height: calc(100vh - var(--nav-h));
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* ── Top bar (view toggle) ── */
    .gallery-toolbar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 14px 28px 0;
        flex-shrink: 0;
        gap: 6px;
    }
    .view-toggle {
        display: flex;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 2px;
        gap: 2px;
    }
    .view-btn {
        font-family: 'Outfit', sans-serif;
        font-size: 0.7rem; font-weight: 500;
        color: var(--text-muted);
        padding: 5px 12px; border-radius: 6px;
        cursor: pointer; transition: all 0.15s;
        white-space: nowrap;
    }
    .view-btn:hover { color: var(--text-primary); }
    .view-btn.active {
        color: var(--text-primary);
        background: var(--surface);
        box-shadow: var(--shadow);
    }

    /* ── Day columns track ── */
    .day-track {
        display: flex;
        overflow-x: auto;
        flex: 1;
        align-items: flex-start;
        padding: 18px 28px 32px;
        scroll-snap-type: x mandatory;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }
    .day-track::-webkit-scrollbar { height: 5px; }
    .day-track::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

    /* ── Single day column ── */
    .day-col {
        flex: none;
        width: 290px;
        scroll-snap-align: start;
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding-left: 24px;
        padding-right: 24px;
        border-right: 1px solid var(--border-light);
        animation: colIn 0.35s ease both;
    }
    .day-col:last-child { border-right: none; }
    @keyframes colIn { from{opacity:0;transform:translateY(10px);} to{opacity:1;transform:translateY(0);} }

    /* ── Day header ── */
    .day-header { padding-bottom: 12px; border-bottom: 1px solid var(--border); padding-top: 2px; }
    .day-name {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.15rem; font-weight: 600;
        color: var(--text-primary); line-height: 1;
    }
    .day-date { font-size: 0.72rem; color: var(--text-muted); margin-top: 3px; letter-spacing: .04em; text-transform: uppercase; }
    .daily-totals { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 8px; }

    /* ── Day progress bars ── */
    .day-progress { display: flex; flex-direction: column; gap: 5px; margin-top: 10px; }
    .dp-row { display: flex; align-items: center; gap: 8px; }
    .dp-label { font-size: 0.62rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; width: 28px; flex-shrink: 0; }
    .dp-label-cal  { color: var(--cal); }
    .dp-label-pro  { color: var(--pro); }
    .dp-label-carb { color: var(--carb); }
    .dp-label-fat  { color: var(--fat); }
    .dp-track { flex: 1; }
    .dp-pct {
        font-size: 0.6rem; font-weight: 600;
        width: 32px; text-align: right; flex-shrink: 0;
        color: var(--text-secondary);
    }
    .dp-pct.over { color: var(--warn); }
    .fill-cal  { background: var(--cal); }
    .fill-pro  { background: var(--pro); }
    .fill-carb { background: var(--carb); }
    .fill-fat  { background: var(--fat); }

    /* ── Overall % badge ── */
    .day-pct-overall {
        font-size: 0.68rem; font-weight: 600;
        color: var(--text-secondary);
        margin-top: 4px;
    }
    .day-pct-overall .pct-val { font-size: 1rem; font-weight: 700; }
    .pct-good { color: var(--good); }
    .pct-warn { color: var(--warn); }
    .pct-over { color: var(--fat); }

    .meal-img-wrap {
        height: 120px;
    }

    /* ── Meal card ── */
    .meal-card {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.15s;
        height: 250px;
    }
    .meal-card:hover { box-shadow: var(--shadow-lg); }

    .meal-img-wrap {
        aspect-ratio: 4/3;
        background: var(--border-light);
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
    }
    .meal-img-wrap img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .4s; }
    .meal-card:hover .meal-img-wrap img { transform:scale(1.04); }
    .meal-img-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-muted); }

    .meal-body { display:flex; flex-direction:column; height: 100%; }
    .meal-name-row {
        display: flex; align-items: center;
        padding: 8px 10px;
        gap: 6px;
    }
    .meal-name {
        font-size: 0.76rem; font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        flex: 1;
    }
    .meal-time { font-size: 0.66rem; color: var(--text-muted); flex-shrink:0; }

    /* ── Macro table row in card ── */
    .meal-macros-row {
        display: flex;
        border-top: 1px solid var(--border-light);
    }
    .meal-macro-cell {
        flex: 1;
        text-align: center;
        padding: 4px 2px 6px;
        border-right: 1px solid var(--border-light);
    }
    .meal-macro-cell:last-child { border-right: none; }
    .mmc-label { font-size: 0.56rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
    .mmc-value { font-size: 0.72rem; font-weight: 600; }
    .mmc-pct {
        font-size: 0.54rem; font-weight: 600;
        margin-top: 1px;
        color: var(--text-muted);
    }

    /* ── Meal progress mini bars ── */
    .meal-prog-strip { display:flex; gap:3px; padding:4px 10px 6px; }
    .meal-prog-item { flex:1; }
    .mp-label { font-size:0.52rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:2px; text-align:center; }
    .mp-track { height:3px; border-radius:2px; background:var(--border-light); overflow:hidden; }
    .mp-fill { height:100%; border-radius:2px; }

    /* ── Food notification badges ── */
    .meal-notes { display:flex; flex-wrap:wrap; gap:3px; padding:0 10px 8px; }

    /* ── Delete/edit ── */
    .btn-delete {
        position:absolute; top:8px; right:8px;
        width:28px; height:28px;
        background:rgba(255,255,255,0.92);
        border:none; border-radius:50%; cursor:pointer;
        display:flex; align-items:center; justify-content:center;
        opacity:0; transition:opacity .2s, background .15s;
        backdrop-filter:blur(4px); z-index:10;
    }
    html.dark .btn-delete { background:rgba(30,30,30,0.88); }
    .meal-card:hover .btn-delete { opacity:1; }
    .btn-delete:hover { background:var(--fat-bg); }
    .btn-delete svg { stroke: var(--fat); }

    .btn-edit {
        position:absolute; top:8px; left:8px;
        width:28px; height:28px;
        background:rgba(255,255,255,0.92);
        border:none; border-radius:50%; cursor:pointer;
        display:flex; align-items:center; justify-content:center;
        opacity:0; transition:opacity .2s;
        backdrop-filter:blur(4px); z-index:10;
        text-decoration:none; color:var(--text-secondary);
    }
    html.dark .btn-edit { background:rgba(30,30,30,0.88); }
    .meal-card:hover .btn-edit { opacity:1; }
    .btn-edit:hover { background:var(--pro-bg); color:var(--pro); }

    /* ── View: compact ── */
    .view-compact .meal-img-wrap { aspect-ratio: 5/3; }
    .view-compact .meal-macros-row,
    .view-compact .meal-prog-strip,
    .view-compact .meal-notes { display: none; }

    /* ── View: progress ── */
    .view-progress .meal-macros-row { display: none; }

    /* ── View: detailed ── */
    .view-detailed .meal-prog-strip { display: none; }

    /* ── Empty state ── */
    .empty-state { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:14px; color:var(--text-muted); text-align:center; padding:40px; }
    .empty-icon { width:56px; height:56px; border:2px dashed var(--border); border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .empty-state h2 { font-family:'Cormorant Garamond',serif; font-size:1.4rem; color:var(--text-secondary); font-weight:500; }
    .empty-state p  { font-size:.82rem; color:var(--text-muted); max-width:220px; line-height:1.55; }
</style>
@endpush

@section('content')
<div class="gallery-wrap" x-data="{ viewMode: localStorage.getItem('viewMode') || 'detailed' }" x-init="$watch('viewMode', v => localStorage.setItem('viewMode', v))">

    @if($mealsByDate->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                    <circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/>
                </svg>
            </div>
            <h2>No meals yet</h2>
            <p>Start logging your meals to see your daily gallery here.</p>
            <a class="btn-primary" href="{{ route('meals.create') }}">Log your first meal</a>
        </div>
    @else

        {{-- View toggle --}}
        <div class="gallery-toolbar">
            <div class="view-toggle">
                <button class="view-btn" :class="viewMode==='compact' && 'active'" @click="viewMode='compact'">Compact</button>
                <button class="view-btn" :class="viewMode==='detailed' && 'active'" @click="viewMode='detailed'">Detailed</button>
                <button class="view-btn" :class="viewMode==='progress' && 'active'" @click="viewMode='progress'">Progress</button>
            </div>
        </div>

        <div class="day-track" :class="'view-' + viewMode">
            @foreach($mealsByDate as $dateStr => $meals)
                @php
                    $date       = \Carbon\Carbon::parse($dateStr);
                    $dayLabel   = $date->isToday() ? 'Today' : ($date->isYesterday() ? 'Yesterday' : $date->format('l'));
                    $totalCal   = $meals->sum('calories');
                    $totalPro   = $meals->sum('protein');
                    $totalCarb  = $meals->sum('carbs');
                    $totalFat   = $meals->sum('fat');

                    // % of daily goal
                    $pctCal  = $goals->calories > 0 ? round($totalCal  / $goals->calories * 100) : 0;
                    $pctPro  = $goals->protein  > 0 ? round($totalPro  / $goals->protein  * 100) : 0;
                    $pctCarb = $goals->carbs    > 0 ? round($totalCarb / $goals->carbs    * 100) : 0;
                    $pctFat  = $goals->fat      > 0 ? round($totalFat  / $goals->fat      * 100) : 0;

                    // Overall = average of the 4 macro %s, capped display at 100 for "met"
                    $pctOverall = round(($pctCal + $pctPro + $pctCarb + $pctFat) / 4);
                @endphp

                <div class="day-col">
                    {{-- Day header --}}
                    <div class="day-header">
                        <div class="day-name">{{ $dayLabel }}</div>
                        <div class="day-date">{{ $date->format('M j, Y') }}</div>
                        <div class="daily-totals">
                            <span class="macro macro-cal">{{ $totalCal }} kcal</span>
                            <span class="macro macro-pro">{{ round($totalPro) }}g P</span>
                            <span class="macro macro-carb">{{ round($totalCarb) }}g C</span>
                            <span class="macro macro-fat">{{ round($totalFat) }}g F</span>
                        </div>

                        {{-- Overall % --}}
                        <div class="day-pct-overall">
                            <span class="pct-val {{ $pctOverall >= 90 && $pctOverall <= 110 ? 'pct-good' : ($pctOverall > 110 ? 'pct-over' : '') }}">{{ $pctOverall }}%</span>
                            <span>of daily goal</span>
                        </div>

                        {{-- Progress bars --}}
                        <div class="day-progress">
                            @foreach([
                                ['Cal', 'cal', $pctCal],
                                ['Pro', 'pro', $pctPro],
                                ['Crb', 'carb', $pctCarb],
                                ['Fat', 'fat', $pctFat],
                            ] as [$lbl, $key, $pct])
                                <div class="dp-row">
                                    <span class="dp-label dp-label-{{ $key }}">{{ $lbl }}</span>
                                    <div class="dp-track">
                                        <div class="prog-bar-track">
                                            <div class="prog-bar-fill fill-{{ $key }}" style="width:{{ min($pct, 100) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="dp-pct {{ $pct > 110 ? 'over' : '' }}">{{ $pct }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Meal cards --}}
                    @foreach($meals as $meal)
                        @php
                            $mPctCal  = $goals->calories > 0 ? round($meal->calories / $goals->calories * 100) : 0;
                            $mPctPro  = $goals->protein  > 0 ? round($meal->protein  / $goals->protein  * 100) : 0;
                            $mPctCarb = $goals->carbs    > 0 ? round($meal->carbs    / $goals->carbs    * 100) : 0;
                            $mPctFat  = $goals->fat      > 0 ? round($meal->fat      / $goals->fat      * 100) : 0;
                            $notes    = $meal->notifications;
                        @endphp
                        <div class="meal-card">
                            {{-- Edit --}}
                            <a href="{{ route('meals.edit', $meal) }}" class="btn-edit" title="Edit">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('meals.destroy', $meal) }}"
                                  x-data @submit.prevent="if(confirm('Remove this meal?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" title="Remove">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round">
                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </form>

                            {{-- Image --}}
                            <div class="meal-img-wrap">
                                @if($meal->image_path)
                                    <img src="{{ Storage::url($meal->image_path) }}" alt="{{ $meal->name }}" loading="lazy">
                                @else
                                    <div class="meal-img-placeholder">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="meal-body">
                                <div class="meal-name-row">
                                    <div class="meal-name" title="{{ $meal->name }}">{{ $meal->name }}</div>
                                    <div class="meal-time">{{ $meal->eaten_at->format('H:i') }}</div>
                                </div>

                                {{-- Detailed view: macro table with % --}}
                                <div class="meal-macros-row">
                                    @foreach([
                                        ['Calories', $meal->calories . ' kcal', $mPctCal, 'cal'],
                                        ['Protein', $meal->protein . 'g', $mPctPro, 'pro'],
                                        ['Carbs', $meal->carbs . 'g', $mPctCarb, 'carb'],
                                        ['Fat', $meal->fat . 'g', $mPctFat, 'fat'],
                                    ] as [$label, $val, $pct, $key])
                                        <div class="meal-macro-cell">
                                            <div class="mmc-label">{{ $label }}</div>
                                            <div class="mmc-value" style="color:var(--{{ $key }})">{{ $val }}</div>
                                            <div class="mmc-pct">{{ $pct }}%</div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Progress view: mini progress bars --}}
                                <div class="meal-prog-strip">
                                    @foreach([
                                        ['Cal', $meal->calories, $goals->calories, $mPctCal, 'cal'],
                                        ['Pro', $meal->protein, $goals->protein, $mPctPro, 'pro'],
                                        ['Crb', $meal->carbs, $goals->carbs, $mPctCarb, 'carb'],
                                        ['Fat', $meal->fat, $goals->fat, $mPctFat, 'fat'],
                                    ] as [$label, $m_macro, $g_macro, $pct, $key])
                                        <div class="meal-prog-item">
                                            <div class="mp-label">{{ $label }}</div>
                                            <div class="mp-label">{{ $pct }}%</div>
                                            <div class="mp-label"> {{$m_macro}}/{{ $g_macro }}</div>
                                            <div class="mp-track">
                                                <div class="mp-fill fill-{{ $key }}" style="width:{{ min($pct, 100) }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Food notifications --}}
                                @if(count($notes))
                                    <div class="meal-notes">
                                        @foreach($notes as $note)
                                            <span class="note-badge note-{{ $note['type'] }}">
                                                {{ $note['type'] === 'good' ? '✓' : '⚠' }}
                                                {{ $note['label'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
