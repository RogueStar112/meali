<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function edit()
    {
        $goal = Goal::current();
        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'calories'      => 'required|integer|min:0|max:9999',
            'protein'       => 'required|numeric|min:0|max:999',
            'carbs'         => 'required|numeric|min:0|max:999',
            'fat'           => 'required|numeric|min:0|max:999',
            'saturated_fat' => 'required|numeric|min:0|max:999',
            'sugar'         => 'required|numeric|min:0|max:999',
            'fibre'         => 'required|numeric|min:0|max:999',
            'salt'          => 'required|numeric|min:0|max:99',
        ]);

        $goal = Goal::current();
        $goal->update($validated);

        return redirect()->route('goals.edit')->with('success', 'Goals saved!');
    }
}
