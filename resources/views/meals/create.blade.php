@extends('layouts.app')
@section('title', ($meal ? 'Edit' : 'Log a') . ' Meal — Plated')

@push('styles')
<style>
    .create-page { min-height:calc(100vh - var(--nav-h)); display:flex; align-items:flex-start; justify-content:center; padding:44px 24px 60px; }
    .form-card { background:var(--surface); border-radius:16px; box-shadow:var(--shadow-lg); width:100%; max-width:540px; padding:40px 40px 44px; animation:cardIn .3s ease both; }
    @keyframes cardIn { from{opacity:0;transform:translateY(12px);} to{opacity:1;transform:translateY(0);} }
    .form-title { font-family:'Cormorant Garamond',serif; font-size:1.9rem; font-weight:600; line-height:1; margin-bottom:4px; }
    .form-subtitle { font-size:.8rem; color:var(--text-muted); margin-bottom:32px; }

    .upload-zone { aspect-ratio:16/9; border:2px dashed var(--border); border-radius:var(--radius); overflow:hidden; cursor:pointer; position:relative; background:var(--bg); transition:border-color .2s,background .2s; margin-bottom:28px; }
    .upload-zone:hover { border-color:var(--text-muted); background:var(--surface-hover); }
    .upload-zone.has-preview { border-style:solid; border-color:var(--border); }
    .upload-prompt { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; color:var(--text-muted); pointer-events:none; }
    .upload-prompt-label { font-size:.8rem; font-weight:500; color:var(--text-secondary); }
    .upload-prompt-sub { font-size:.72rem; color:var(--text-muted); }
    .upload-preview { position:absolute; inset:0; }
    .upload-preview img { width:100%; height:100%; object-fit:cover; display:block; }
    .upload-preview-overlay { position:absolute; inset:0; background:rgba(0,0,0,0); display:flex; align-items:center; justify-content:center; transition:background .2s; }
    .upload-zone:hover .upload-preview-overlay { background:rgba(0,0,0,.35); }
    .upload-preview-overlay span { color:#fff; font-size:.78rem; font-weight:500; opacity:0; transition:opacity .2s; }
    .upload-zone:hover .upload-preview-overlay span { opacity:1; }
    input[type="file"] { display:none; }

    .field { margin-bottom:20px; }
    .field-label { display:block; font-size:.72rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--text-secondary); margin-bottom:6px; }
    .field-label .optional { font-weight:400; text-transform:none; letter-spacing:0; color:var(--text-muted); }
    input[type="text"], input[type="date"], input[type="number"] { width:100%; font-family:'Outfit',sans-serif; font-size:.88rem; color:var(--text-primary); background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:10px 14px; outline:none; transition:border-color .2s,box-shadow .2s; -webkit-appearance:none; }
    input:focus { border-color:var(--text-primary); box-shadow:0 0 0 3px rgba(128,128,128,.1); }
    input.is-invalid { border-color:var(--fat); }
    .error { font-size:.72rem; color:var(--fat); margin-top:5px; }

    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px; }
    .grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:20px; }
    .section-label { font-size:.72rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--text-secondary); margin-bottom:16px; display:block; }
    .divider { border:none; border-top:1px solid var(--border-light); margin:28px 0; }

    .form-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .btn-back { font-size:.8rem; color:var(--text-muted); text-decoration:none; transition:color .15s; }
    .btn-back:hover { color:var(--text-primary); }
    .btn-submit { flex:1; max-width:200px; background:var(--accent); color:var(--accent-fg); font-family:'Outfit',sans-serif; font-size:.85rem; font-weight:500; padding:12px 24px; border-radius:100px; border:none; cursor:pointer; transition:opacity .15s,transform .15s; }
    .btn-submit:hover { opacity:.8; transform:translateY(-1px); }
    .macro-preview { display:flex; gap:6px; flex-wrap:wrap; margin-top:12px; min-height:22px; }
</style>
@endpush

@section('content')
<div class="create-page">
    <div class="form-card"
         x-data="{
            preview: {{ $meal && $meal->image_path ? "'" . Storage::url($meal->image_path) . "'" : 'null' }},
            calories: '{{ old('calories', $meal->calories ?? '') }}',
            protein:  '{{ old('protein',  $meal->protein ?? '') }}',
            carbs:    '{{ old('carbs',    $meal->carbs ?? '') }}',
            fat:      '{{ old('fat',      $meal->fat ?? '') }}',
            setImage(e) {
                const f = e.target.files[0];
                if (f) this.preview = URL.createObjectURL(f);
            }
         }">

        <div class="form-title">{{ $meal ? 'Edit meal' : 'Log a meal' }}</div>
        <div class="form-subtitle">{{ $meal ? 'Update the details below.' : 'Track what you ate and your macros.' }}</div>

        <form method="POST"
              action="{{ $meal ? route('meals.update', $meal) : route('meals.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if($meal) @method('PUT') @endif

            {{-- Image --}}
            <label class="field-label">Photo <span class="optional">(optional)</span></label>
            <div class="upload-zone" :class="{'has-preview':preview}" @click="$refs.fileInput.click()">
                <div class="upload-prompt" x-show="!preview">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span class="upload-prompt-label">Click to add a photo</span>
                    <span class="upload-prompt-sub">JPG, PNG, WEBP — up to 8 MB</span>
                </div>
                <div class="upload-preview" x-show="preview">
                    <img :src="preview" alt="Preview">
                    <div class="upload-preview-overlay"><span>Change photo</span></div>
                </div>
                <input type="file" name="image" accept="image/*" x-ref="fileInput" @change="setImage($event)">
            </div>
            @error('image') <div class="error">{{ $message }}</div> @enderror

            {{-- Name --}}
            <div class="field">
                <label class="field-label" for="name">Meal name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $meal->name ?? '') }}" placeholder="e.g. Grilled chicken & rice" class="{{ $errors->has('name') ? 'is-invalid' : '' }}" autocomplete="off">
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            {{-- Date --}}
            <div class="field">
                <label class="field-label" for="eaten_at">Date</label>
                <input type="date" id="eaten_at" name="eaten_at" value="{{ old('eaten_at', $meal ? $meal->eaten_at->format('Y-m-d') : $today) }}" class="{{ $errors->has('eaten_at') ? 'is-invalid' : '' }}">
                @error('eaten_at') <div class="error">{{ $message }}</div> @enderror
            </div>

            <hr class="divider">

            {{-- ── Core macros ── --}}
            <span class="section-label">Macros</span>

            <div class="field">
                <label class="field-label" for="calories">Calories (kcal)</label>
                <input type="number" id="calories" name="calories" x-model="calories" value="{{ old('calories', $meal->calories ?? '') }}" placeholder="0" min="0" max="9999" class="{{ $errors->has('calories') ? 'is-invalid' : '' }}">
                @error('calories') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="grid-3">
                <div>
                    <label class="field-label" for="protein">Protein (g)</label>
                    <input type="number" id="protein" name="protein" x-model="protein" value="{{ old('protein', $meal->protein ?? '') }}" placeholder="0" min="0" max="999" step="0.1" class="{{ $errors->has('protein') ? 'is-invalid' : '' }}">
                    @error('protein') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="carbs">Carbs (g)</label>
                    <input type="number" id="carbs" name="carbs" x-model="carbs" value="{{ old('carbs', $meal->carbs ?? '') }}" placeholder="0" min="0" max="999" step="0.1" class="{{ $errors->has('carbs') ? 'is-invalid' : '' }}">
                    @error('carbs') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="fat">Fat (g)</label>
                    <input type="number" id="fat" name="fat" x-model="fat" value="{{ old('fat', $meal->fat ?? '') }}" placeholder="0" min="0" max="999" step="0.1" class="{{ $errors->has('fat') ? 'is-invalid' : '' }}">
                    @error('fat') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="macro-preview">
                <template x-if="calories"><span class="macro macro-cal" x-text="calories+' kcal'"></span></template>
                <template x-if="protein"><span class="macro macro-pro" x-text="protein+'g P'"></span></template>
                <template x-if="carbs"><span class="macro macro-carb" x-text="carbs+'g C'"></span></template>
                <template x-if="fat"><span class="macro macro-fat" x-text="fat+'g F'"></span></template>
            </div>

            <hr class="divider">

            {{-- ── Additional nutrients ── --}}
            <span class="section-label">Additional <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-muted);">— optional, defaults to 0</span></span>

            <div class="grid-2">
                <div>
                    <label class="field-label" for="saturated_fat">Sat. fat (g)</label>
                    <input type="number" id="saturated_fat" name="saturated_fat" value="{{ old('saturated_fat', $meal->saturated_fat ?? '') }}" placeholder="0" min="0" max="999" step="0.1">
                    @error('saturated_fat') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="sugar">Sugar (g)</label>
                    <input type="number" id="sugar" name="sugar" value="{{ old('sugar', $meal->sugar ?? '') }}" placeholder="0" min="0" max="999" step="0.1">
                    @error('sugar') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="fibre">Fibre (g)</label>
                    <input type="number" id="fibre" name="fibre" value="{{ old('fibre', $meal->fibre ?? '') }}" placeholder="0" min="0" max="999" step="0.1">
                    @error('fibre') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="field-label" for="salt">Salt (g)</label>
                    <input type="number" id="salt" name="salt" value="{{ old('salt', $meal->salt ?? '') }}" placeholder="0" min="0" max="99" step="0.01">
                    @error('salt') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr class="divider">

            <div class="form-footer">
                <a class="btn-back" href="{{ route('meals.index') }}">← Back</a>
                <button type="submit" class="btn-submit">{{ $meal ? 'Update meal' : 'Save meal' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
