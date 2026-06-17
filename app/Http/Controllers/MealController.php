<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    public function index()
    {
        $mealsByDate = Meal::orderBy('eaten_at', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(fn($meal) => $meal->eaten_at->format('Y-m-d'));

        $goals = Goal::current();

        return view('meals.index', compact('mealsByDate', 'goals'));
    }

    public function timeline()
    {
        $meals = Meal::orderBy('eaten_at')
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'            => $m->id,
                'name'          => $m->name,
                'eaten_at'      => $m->eaten_at->format('Y-m-d'),
                'eaten_at_time' => $m->eaten_at->format('H:i'),
                'eaten_at_hour' => $m->eaten_at->format('H'),
                'calories'      => (int) $m->calories,
                'protein'       => (float) $m->protein,
                'carbs'         => (float) $m->carbs,
                'fat'           => (float) $m->fat,
                'saturated_fat' => (float) $m->saturated_fat,
                'sugar'         => (float) $m->sugar,
                'fibre'         => (float) $m->fibre,
                'salt'          => (float) $m->salt,
                'image_url'     => $m->image_path ? Storage::url($m->image_path) : null,
            ]);

        return view('meals.timeline', compact('meals'));
    }


    public function create()
    {
        return view('meals.create', [
            'today' => now()->format('Y-m-d'),
            'meal'  => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'eaten_at'      => 'required|date',
            'calories'      => 'required|integer|min:0|max:9999',
            'protein'       => 'required|numeric|min:0|max:999',
            'carbs'         => 'required|numeric|min:0|max:999',
            'fat'           => 'required|numeric|min:0|max:999',
            'saturated_fat' => 'nullable|numeric|min:0|max:999',
            'sugar'         => 'nullable|numeric|min:0|max:999',
            'fibre'         => 'nullable|numeric|min:0|max:999',
            'salt'          => 'nullable|numeric|min:0|max:99',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        // Default optional fields to 0
        $validated['saturated_fat'] = $validated['saturated_fat'] ?? 0;
        $validated['sugar']         = $validated['sugar'] ?? 0;
        $validated['fibre']         = $validated['fibre'] ?? 0;
        $validated['salt']          = $validated['salt'] ?? 0;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('meals', 'public');
        }

        Meal::create(array_merge($validated, ['image_path' => $imagePath]));

        return redirect()->route('meals.index')->with('success', 'Meal logged!');
    }

    public function edit(Meal $meal)
    {
        return view('meals.create', [
            'today' => now()->format('Y-m-d'),
            'meal'  => $meal,
        ]);
    }

    public function update(Request $request, Meal $meal)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'eaten_at'      => 'required|date',
            'calories'      => 'required|integer|min:0|max:9999',
            'protein'       => 'required|numeric|min:0|max:999',
            'carbs'         => 'required|numeric|min:0|max:999',
            'fat'           => 'required|numeric|min:0|max:999',
            'saturated_fat' => 'nullable|numeric|min:0|max:999',
            'sugar'         => 'nullable|numeric|min:0|max:999',
            'fibre'         => 'nullable|numeric|min:0|max:999',
            'salt'          => 'nullable|numeric|min:0|max:99',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        $validated['saturated_fat'] = $validated['saturated_fat'] ?? 0;
        $validated['sugar']         = $validated['sugar'] ?? 0;
        $validated['fibre']         = $validated['fibre'] ?? 0;
        $validated['salt']          = $validated['salt'] ?? 0;

        if ($request->hasFile('image')) {
            if ($meal->image_path) {
                Storage::disk('public')->delete($meal->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('meals', 'public');
        }

        $meal->update($validated);

        return redirect()->route('meals.index')->with('success', 'Meal updated!');
    }

    public function destroy(Meal $meal)
    {
        if ($meal->image_path) {
            Storage::disk('public')->delete($meal->image_path);
        }

        $meal->delete();

        return redirect()->route('meals.index')->with('success', 'Meal removed.');
    }
}
