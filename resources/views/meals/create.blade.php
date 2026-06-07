@extends('layouts.app')
@section('title', 'Log a Meal — Mealli')


@push('styles')
<style>
    .create-page {
        min-height: calc(100vh - var(--nav-h));
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 44px 24px 60px;
    }

    .field-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        color: var(--text-secondary);
        
    }

    .form-card {
        background: var(--surface);
        border-radius: 16px;
        box-shadow: var(--shadow-lg);
        width: 100%;
        max-width: 520px;
        padding: 40px 40px 44px;
        animation: cardIn 0.3s ease both;
    }
    @keyframes cardIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .form-title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.9rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1;
        margin-bottom: 4px;
    }
    .form-subtitle {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 32px;
    }

    /* ── Image upload zone ── */
    .upload-zone {
        aspect-ratio: 16 / 9;
        border: 2px dashed var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        cursor: pointer;
        position: relative;
        background: var(--bg);
        transition: border-color 0.2s, background 0.2s;
        margin-bottom: 28px;
    }
    .upload-zone:hover { border-color: var(--text-muted); background: var(--border-light); }
    .upload-zone.has-preview { border-style: solid; border-color: var(--border); }

    .upload-prompt {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .upload-prompt-label {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--text-secondary);
    }
    .upload-prompt-sub {
        font-size: 0.72rem;
        color: var(--text-muted);
    }

    .upload-preview {
        position: absolute;
        inset: 0;
    }
    .upload-preview img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
    }
    .upload-preview-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .upload-zone:hover .upload-preview-overlay {
        background: rgba(0,0,0,0.35);
    }
    .upload-preview-overlay span {
        color: white;
        font-size: 0.78rem;
        font-weight: 500;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .upload-zone:hover .upload-preview-overlay span { opacity: 1; }

    input[type="file"] { display: none; }

    /* ── Form fields ── */
    .field { margin-bottom: 20px; }
    .field:last-of-type { margin-bottom: 0; }

    label {
        display: block;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }

    input[type="text"],
    input[type="date"],
    input[type="datetime-local"],
    input[type="number"], .previous-meal-gallery  {
        width: 100%;
        font-family: 'Outfit', sans-serif;
        font-size: 0.88rem;
        color: var(--text-primary);
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 14px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        -webkit-appearance: none;
    }

    #api-fill {
        font-family: 'Outfit', sans-serif;
        font-size: 0.88rem;
        color: var(--text-primary);
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 14px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        -webkit-appearance: none;
        opacity: 0.3;
    }

    input:focus {
        border-color: var(--text-primary);
        box-shadow: 0 0 0 3px rgba(28,25,23,0.07);
    }
    input.is-invalid { border-color: var(--fat); }

    .error {
        font-size: 0.72rem;
        color: var(--fat);
        margin-top: 5px;
    }

    /* ── Macro row ── */
    .macro-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 20px;
    }

    /* ── Divider ── */
    .divider {
        border: none;
        border-top: 1px solid var(--border-light);
        margin: 28px 0;
    }

    /* ── Submit ── */
    .form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .btn-back {
        font-size: 0.8rem;
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.15s;
    }
    .btn-back:hover { color: var(--text-primary); }

    .btn-submit {
        flex: 1;
        max-width: 200px;
        background: var(--accent);
        color: #fff;
        font-family: 'Outfit', sans-serif;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 12px 24px;
        border-radius: 100px;
        border: none;
        cursor: pointer;
        transition: opacity 0.15s, transform 0.15s;
    }
    .btn-submit:hover { opacity: 0.8; transform: translateY(-1px); }
    .btn-submit:active { transform: translateY(0); }

    /* ── Macro preview strip ── */
    .macro-preview {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 12px;
        min-height: 22px;
    }

    .previous-meal-img {
        border-radius: 9999px;
        object-fit: cover;
        opacity: 1;
    }

    .previous-meal-img:hover {
        transition: opacity 0.15s;
        opacity: 0.4;
    }

    .previous-meal-gallery {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 3px;
        /* justify-content: space-around; */
    }
    
    .previous-meal-img-container {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        background:rgba(0,0,0,0.6);
        border-radius: 9999px;
    }

    .previous-meal-img-container:hover .previous-meal-img-title {
        opacity: 1;
        transition: 0.15s;
        visibility: visible;
    }
    

    .previous-meal-img-title {
        position: absolute;
        text-align: center;
        font-size: 75%;
        opacity: 0;
        user-select: none !important;
        visibility: none;
    }

    .previous-meal-img-title {
        color: white;
    }

    .previous-meals-title {
        margin-bottom: 10px;
        
    }

    .field-name-container {
        display: flex;
        justify-content: space-between;
        gap: 5px;
    }

    .api-fill-active {
        background-color: green !important;
        color: white !important;
        opacity: 1 !important;
        transition: 0.15s;
        cursor: pointer;
    }

    .cursor-clickable {
        cursor: pointer;
    }
    


</style>
@endpush

@section('content')
<div class="create-page">
    <div class="form-card"
         x-data="{
            preview: null,
            calories: '',
            protein: '',
            carbs: '',
            fat: '',
            setImage(e) {
                const f = e.target.files[0];
                if (f) this.preview = URL.createObjectURL(f);
            }
         }">

        <div class="form-title">Log a meal</div>
        <div class="form-subtitle">Track what you ate and your macros.</div>

        {{-- {{ dd($previous_meals) }} --}}

        <form method="POST" action="{{ route('meals.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Image upload --}}
            <label>Photo <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-muted);">(optional)</span></label>
            <div class="upload-zone" :class="{ 'has-preview': preview }"
                 @click="$refs.fileInput.click()">

                {{-- Placeholder --}}
                <div class="upload-prompt" x-show="!preview">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span class="upload-prompt-label">Click to add a photo</span>
                    <span class="upload-prompt-sub">JPG, PNG, WEBP — up to 8 MB</span>
                </div>

                {{-- Preview --}}
                <div class="upload-preview" x-show="preview">
                    <img :src="preview" alt="Preview">
                    <div class="upload-preview-overlay">
                        <span>Change photo</span>
                    </div>
                </div>

                <input type="file" name="image" accept="image/*" x-ref="fileInput" @change="setImage($event)">
            </div>
            @error('image') <div class="error">{{ $message }}</div> @enderror

            <div class="field previous-meals">
                
                <p class="field-title previous-meals-title">Previous meals</p>

                <div class="previous-meal-gallery">
                @foreach($previous_meals as $previous_meal)

                <div class="previous-meal-img-container cursor-clickable">
                    
                    <p class="previous-meal-img-title">{{$previous_meal->name}}</p>

                    <img class="previous-meal-img" src="{{ Storage::url($previous_meal->image_path) }}" width="64" height="64" /> 


                </div>



                @endforeach
                </div>

            </div>
            {{-- Meal name --}}
            <div class="field">
                <label for="name">Meal name</label>

                <div class="field-name-container">
                    <input type="text" id="name" name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g. Grilled chicken & rice"
                        class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                        autocomplete="off">
                    @error('name') <div class="error">{{ $message }}</div> @enderror
                    <button id="api-fill">API Fill</button>
                </div>

            </div>

            {{-- Date --}}
            <div class="field">
                <label for="eaten_at">Date & Time</label>
                <input type="datetime-local" id="eaten_at" name="eaten_at"
                       value="{{ old('eaten_at', $today) }}"
                       class="{{ $errors->has('eaten_at') ? 'is-invalid' : '' }}">
                @error('eaten_at') <div class="error">{{ $message }}</div> @enderror
            </div>

            <hr class="divider">

            {{-- Macros label --}}
            <label style="margin-bottom:14px;display:block;">Macros</label>

            {{-- Calories (full width) --}}
            <div class="field">
                <label for="calories">Calories (kcal)</label>
                <input type="number" id="calories" name="calories"
                       x-model="calories"
                       value="{{ old('calories') }}"
                       placeholder="0" min="0" max="9999"
                       class="{{ $errors->has('calories') ? 'is-invalid' : '' }}">
                @error('calories') <div class="error">{{ $message }}</div> @enderror
            </div>

            {{-- P / C / F --}}
            <div class="macro-row">
                <div>
                    <label for="protein">Protein (g)</label>
                    <input type="number" id="protein" name="protein"
                           x-model="protein"
                           value="{{ old('protein') }}"
                           placeholder="0" min="0" max="999" step="0.1"
                           class="{{ $errors->has('protein') ? 'is-invalid' : '' }}">
                    @error('protein') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="carbs">Carbs (g)</label>
                    <input type="number" id="carbs" name="carbs"
                           x-model="carbs"
                           value="{{ old('carbs') }}"
                           placeholder="0" min="0" max="999" step="0.1"
                           class="{{ $errors->has('carbs') ? 'is-invalid' : '' }}">
                    @error('carbs') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="fat">Fat (g)</label>
                    <input type="number" id="fat" name="fat"
                           x-model="fat"
                           value="{{ old('fat') }}"
                           placeholder="0" min="0" max="999" step="0.1"
                           class="{{ $errors->has('fat') ? 'is-invalid' : '' }}">
                    @error('fat') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Live macro preview --}}
            <div class="macro-preview">
                <template x-if="calories">
                    <span class="macro macro-cal" x-text="calories + ' kcal'"></span>
                </template>
                <template x-if="protein">
                    <span class="macro macro-pro" x-text="protein + 'g P'"></span>
                </template>
                <template x-if="carbs">
                    <span class="macro macro-carb" x-text="carbs + 'g C'"></span>
                </template>
                <template x-if="fat">
                    <span class="macro macro-fat" x-text="fat + 'g F'"></span>
                </template>
            </div>

            <hr class="divider">

            <div class="form-footer">
                <a class="btn-back" href="{{ route('meals.index') }}">← Back</a>
                <button type="submit" class="btn-submit">Save meal</button>
            </div>
        </form>
    </div>
</div>

<script>


    $('#name').on('change', function() {

        console.log('this is working');

        if($('#name').val()) {
            $('#api-fill').addClass('api-fill-active');
            $('#api-fill').removeAttr('disabled');
        
        } else {
            $('#api-fill').removeClass('api-fill-active');
            $('#api-fill').attr('disabled');
        }

    });

</script>
@endsection


