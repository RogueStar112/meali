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

    /* ── Day columns track ── */
    .day-track {
        display: flex;
        gap: 0;
        overflow-x: auto;
        flex: 1;
        align-items: flex-start;
        padding: 32px 28px 32px;
        scroll-snap-type: x mandatory;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }
    .day-track::-webkit-scrollbar { height: 5px; }
    .day-track::-webkit-scrollbar-track { background: transparent; }
    .day-track::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

    /* ── Single day column ── */
    .day-col {
        flex: none;
        width: 268px;
        scroll-snap-align: start;
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding-left: 24px;
        padding-right: 24px;
        border-right: 1px solid var(--border-light);
        /* margin-right: 24px; */
        animation: colIn 0.35s ease both;
    }
    .day-col:last-child { border-right: none; }

/* 
    .first-day-col {
        padding-left: 24px;
    } */

    @keyframes colIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .day-header {
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
        /* position: sticky;
        top: 0; */
        /* background: var(--bg); */
        z-index: 10;
        padding-top: 2px;
    }
    .day-name {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1;
    }
    .day-date {
        font-size: 0.72rem;
        font-weight: 400;
        color: var(--text-muted);
        margin-top: 3px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .day-kcal {
        font-size: 0.7rem;
        color: var(--cal);
        font-weight: 500;
        margin-top: 4px;
    }

    /* ── Meal card ── */
    .meal-card {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: box-shadow 0.2s, transform 0.2s;
        position: relative;
    }
    .meal-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .meal-img-wrap {
        aspect-ratio: 4 / 3;
        background: var(--border-light);
        overflow: hidden;
        position: relative;
    }
    .meal-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
    }
    .meal-card:hover .meal-img-wrap img { transform: scale(1.04); }

    .meal-img-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
    }

    .meal-body {
        padding: 12px 14px 14px;
    }

    .meal-name-and-time {
        display: flex;
        justify-content: space-between;
        font-size: 0.88rem;
    }

    .meal-time {
        color: var(--text-muted);
    }


    .meal-name {
        font-size: 0.88rem;
        font-weight: 500;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 8px;
    }
    .meal-macros {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    /* ── Delete button ── */
    .btn-delete {
        position: absolute;
        top: 8px; right: 8px;
        width: 28px; height: 28px;
        background: rgba(255,255,255,0.92);
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        opacity: 0;
        transition: opacity 0.2s, background 0.15s;
        backdrop-filter: blur(4px);
                z-index: 9999;
    }
    .meal-card:hover .btn-delete { opacity: 1; }
    .btn-delete:hover { background: #FEE2E2; }
    .btn-delete svg { stroke: var(--fat); }

    /* ── Daily total bar ── */
    .daily-totals {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        margin-top: 2px;
    }

    /* ── Empty state ── */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        gap: 14px;
        color: var(--text-muted);
        text-align: center;
        padding: 40px;
    }
    .empty-icon {
        width: 56px; height: 56px;
        border: 2px dashed var(--border);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-muted);
    }
    .empty-state h2 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.4rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    .empty-state p {
        font-size: 0.82rem;
        color: var(--text-muted);
        max-width: 220px;
        line-height: 1.55;
    }

    .meal-name-container {
        display: flex;
        justify-content: space-between;
    }
</style>
@endpush

@section('content')
<div class="gallery-wrap">

    @if($mealsByDate->isEmpty())
        {{-- Empty state --}}
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/>
                </svg>
            </div>
            <h2>No meals yet</h2>
            <p>Start logging your meals to see your daily gallery here.</p>
            <a class="btn-primary" href="{{ route('meals.create') }}">Log your first meal</a>
        </div>

    @else
        
        <div class="day-track ">
            

            @foreach($mealsByDate as $dateStr => $meals)

                {{-- @php
                    dd($mealsByDate);
                @endphp --}}

        
                @php
                    $date       = \Carbon\Carbon::parse($dateStr);
                    $isToday    = $date->isToday();
                    $isYesterday= $date->isYesterday();
                    $dayLabel   = $isToday ? 'Today' : ($isYesterday ? 'Yesterday' : $date->format('l'));
                    $dateLabel  = $date->format('M j, Y');
                    $totalCal   = $meals->sum('calories');
                    $totalPro   = $meals->sum('protein');
                    $totalCarb  = $meals->sum('carbs');
                    $totalFat   = $meals->sum('fat');
                @endphp

                @if($loop->first)
                    <div class="day-col first-day-col">
                @else
                    <div class="day-col">
                @endif
                    {{-- Day header --}}
                    <div class="day-header">
                        <div class="day-name">{{ $dayLabel }}</div>
                        <div class="day-date">{{ $dateLabel }}</div>
                        <div class="daily-totals" style="margin-top:8px;">
                            <span class="macro macro-cal">{{ $totalCal }} kcal</span>
                            <span class="macro macro-pro">{{ round($totalPro) }}g P</span>
                            <span class="macro macro-carb">{{ round($totalCarb) }}g C</span>
                            <span class="macro macro-fat">{{ round($totalFat) }}g F</span>
                        </div>
                    </div>

                    {{-- Meal cards --}}
                   @foreach($meals as $meal)
                        <div class="meal-card">
                            <a href="{{ route('meals.edit', ['id', $meal->id]) }}">               </a>
                            {{-- Delete button --}}
                            <form method="POST" action="{{ route('meals.destroy', $meal) }}" x-data 
                            @submit.prevent="if(confirm('Remove this meal?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" title="Remove meal">
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
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="meal-body">

                                <div class="meal-name-container">
                                    <div class="meal-name" title="{{ $meal->name }}">{{ $meal->name }}</div>
                                    <div>{{date('H:i', strtotime($meal->eaten_at))}}</div>
                                </div>
                                <div class="meal-macros">
                                    <span class="macro macro-cal">{{ $meal->calories }} kcal</span>
                                    <span class="macro macro-pro">{{ $meal->protein }}g P</span>
                                    <span class="macro macro-carb">{{ $meal->carbs }}g C</span>
                                    <span class="macro macro-fat">{{ $meal->fat }}g F</span>
                                </div>
                            </div>
             
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection