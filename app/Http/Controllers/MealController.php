<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    public function index()
    {
        // Group meals by date descending (newest day on left), meals within a day ascending by creation
        $mealsByDate = Meal::orderBy('eaten_at', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(fn($meal) => $meal->eaten_at->format('Y-m-d'));

        return view('meals.index', compact('mealsByDate'));
    }

    public function create()
    {
        return view('meals.create', [
            'today' => now()->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'eaten_at' => 'required|date',
            'calories' => 'required|integer|min:0|max:9999',
            'protein'  => 'required|numeric|min:0|max:999',
            'carbs'    => 'required|numeric|min:0|max:999',
            'fat'      => 'required|numeric|min:0|max:999',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('meals', 'public');
        }

        Meal::create(array_merge($validated, ['image_path' => $imagePath]));

        return redirect()->route('meals.index')->with('success', 'Meal logged!');
    }

    public function edit($id)
    {   

        $meal = Meal::find($id);

        return view('meals.edit', [
            'today' => now()->format('Y-m-d'),
            'meal' => $meal,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'eaten_at' => 'required|date_format:Y-m-d H:i:s',
            'calories' => 'required|integer|min:0|max:9999',
            'protein'  => 'required|numeric|min:0|max:999',
            'carbs'    => 'required|numeric|min:0|max:999',
            'fat'      => 'required|numeric|min:0|max:999',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('meals', 'public');
        }

        $meal = Meal::find($id);

        $meal->update(array_merge($validated, ['image_path' => $imagePath]));

        $meal_name = $meal->name;

        return redirect()->route('meals.index')->with('success', "Meal $meal_name ($id) updated!");
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