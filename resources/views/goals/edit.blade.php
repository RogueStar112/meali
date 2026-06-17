@extends('layouts.app')
@section('title', 'Daily Goals — Plated')

@push('styles')
<style>
    .goals-page { min-height:calc(100vh - var(--nav-h)); display:flex; align-items:flex-start; justify-content:center; padding:44px 24px 60px; }
    .form-card { background:var(--surface); border-radius:16px; box-shadow:var(--shadow-lg); width:100%; max-width:540px; padding:40px 40px 44px; animation:cardIn .3s ease both; }
    @keyframes cardIn { from{opacity:0;transform:translateY(12px);} to{opacity:1;transform:translateY(0);} }
    .form-title { font-family:'Cormorant Garamond',serif; font-size:1.9rem; font-weight:600; line-height:1; margin-bottom:4px; }
    .form-subtitle { font-size:.8rem; color:var(--text-muted); margin-bottom:32px; }

    .field { margin-bottom:20px; }
    .field-label { display:block; font-size:.72rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--text-secondary); margin-bottom:6px; }
    input[type="number"] { width:100%; font-family:'Outfit',sans-serif; font-size:.88rem; color:var(--text-primary); background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:10px 14px; outline:none; transition:border-color .2s,box-shadow .2s; -webkit-appearance:none; }
    input:focus { border-color:var(--text-primary); box-shadow:0 0 0 3px rgba(128,128,128,.1); }
    input.is-invalid { border-color:var(--fat); }
    .error { font-size:.72rem; color:var(--fat); margin-top:5px; }

    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px; }
    .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:20px; }
    .section-label { font-size:.72rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--text-secondary); margin-bottom:16px; display:block; }
    .divider { border:none; border-top:1px solid var(--border-light); margin:28px 0; }

    .btn-submit { width:100%; background:var(--accent); color:var(--accent-fg); font-family:'Outfit',sans-serif; font-size:.85rem; font-weight:500; padding:12px 24px; border-radius:100px; border:none; cursor:pointer; transition:opacity .15s,transform .15s; }
    .btn-submit:hover { opacity:.8; transform:translateY(-1px); }

    .hint { font-size:.68rem; color:var(--text-muted); margin-top:4px; }

    .preset-row { display:flex; gap:8px; margin-bottom:24px; flex-wrap:wrap; }
    .preset-btn {
        font-family:'Outfit',sans-serif; font-size:.72rem; font-weight:500;
        color:var(--text-secondary); background:var(--bg);
        border:1px solid var(--border); padding:6px 14px;
        border-radius:100px; cursor:pointer;
        transition:border-color .15s, color .15s;
    }
    .preset-btn:hover { border-color:var(--text-primary); color:var(--text-primary); }
</style>
@endpush

@section('content')
<div class="goals-page">
    <div class="form-card" x-data="{
        cal: {{ $goal->calories }},
        pro: {{ $goal->protein }},
        carb: {{ $goal->carbs }},
        fat: {{ $goal->fat }},
        sfat: {{ $goal->saturated_fat }},
        sug: {{ $goal->sugar }},
        fib: {{ $goal->fibre }},
        salt: {{ $goal->salt }},
        applyPreset(name) {
            const presets = {
                cut:        { cal:1800, pro:180, carb:150, fat:55, sfat:15, sug:25, fib:30, salt:5 },
                maintain:   { cal:2200, pro:150, carb:250, fat:70, sfat:20, sug:35, fib:30, salt:6 },
                bulk:       { cal:2800, pro:200, carb:350, fat:85, sfat:25, sug:40, fib:30, salt:6 },
                lowcarb:    { cal:1900, pro:160, carb:80,  fat:120,sfat:25, sug:15, fib:25, salt:5 },
            };
            const p = presets[name];
            this.cal=p.cal; this.pro=p.pro; this.carb=p.carb; this.fat=p.fat;
            this.sfat=p.sfat; this.sug=p.sug; this.fib=p.fib; this.salt=p.salt;
        }
    }">
        <div class="form-title">Daily goals</div>
        <div class="form-subtitle">Set your target macros and nutrients. The gallery will track your progress against these.</div>

        {{-- Presets --}}
        <div class="preset-row">
            <button type="button" class="preset-btn" @click="applyPreset('cut')">🔥 Cutting</button>
            <button type="button" class="preset-btn" @click="applyPreset('maintain')">⚖️ Maintain</button>
            <button type="button" class="preset-btn" @click="applyPreset('bulk')">💪 Bulking</button>
            <button type="button" class="preset-btn" @click="applyPreset('lowcarb')">🥑 Low-carb</button>
        </div>

        <form method="POST" action="{{ route('goals.update') }}">
            @csrf
            @method('PUT')

            <span class="section-label">Core macros</span>

            <div class="field">
                <label class="field-label" for="calories">Calories (kcal)</label>
                <input type="number" id="calories" name="calories" x-model="cal" min="0" max="9999" class="{{ $errors->has('calories') ? 'is-invalid' : '' }}">
                @error('calories') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="grid-3">
                <div>
                    <label class="field-label" for="protein">Protein (g)</label>
                    <input type="number" id="protein" name="protein" x-model="pro" min="0" max="999" step="0.1">
                    @error('protein') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="carbs">Carbs (g)</label>
                    <input type="number" id="carbs" name="carbs" x-model="carb" min="0" max="999" step="0.1">
                    @error('carbs') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="fat">Fat (g)</label>
                    <input type="number" id="fat" name="fat" x-model="fat" min="0" max="999" step="0.1">
                    @error('fat') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr class="divider">

            <span class="section-label">Micronutrient limits</span>
            <p class="hint" style="margin-top:-10px;margin-bottom:18px;">Sugar, sat fat and salt are upper limits. Fibre is a minimum target.</p>

            <div class="grid-2">
                <div>
                    <label class="field-label" for="saturated_fat">Sat. fat max (g)</label>
                    <input type="number" id="saturated_fat" name="saturated_fat" x-model="sfat" min="0" max="999" step="0.1">
                    @error('saturated_fat') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="sugar">Sugar max (g)</label>
                    <input type="number" id="sugar" name="sugar" x-model="sug" min="0" max="999" step="0.1">
                    @error('sugar') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="fibre">Fibre min (g)</label>
                    <input type="number" id="fibre" name="fibre" x-model="fib" min="0" max="999" step="0.1">
                    @error('fibre') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="salt">Salt max (g)</label>
                    <input type="number" id="salt" name="salt" x-model="salt" min="0" max="99" step="0.01">
                    @error('salt') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr class="divider">

            <button type="submit" class="btn-submit">Save goals</button>
        </form>
    </div>
</div>
@endsection
