<?php

use App\Http\Controllers\MealController;
use App\Http\Controllers\GoalController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [MealController::class, 'index'])->name('meals.index');
// Route::get('/meals/create', [MealController::class, 'create'])->name('meals.create');
// Route::get('/meals/{meal}/edit', [MealController::class, 'edit'])->name('meals.edit');
// Route::put('/meals/{meal}', [MealController::class, 'update'])->name('meals.update');

// Route::get('/meals/barcode_test', [MealController::class, 'barcode_test'])->name('meals.barcode_test');

// Route::get('/meal/search/{query}', [MealController::class, 'api_search_v2'])->name('api.meal_query');

// Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
// Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');

// Route::get('/timeline', [MealController::class, 'timeline'])->name('meals.timeline');

// Route::get('/goals', [GoalController::class, 'edit'])->name('goals.edit');
// Route::put('/goals', [GoalController::class, 'update'])->name('goals.update');

Route::get('/', [MealController::class, 'index'])->name('meals.index');
Route::get('/timeline', [MealController::class, 'timeline'])->name('meals.timeline');
Route::get('/meals/create', [MealController::class, 'create'])->name('meals.create');
Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
Route::get('/meals/{meal}/edit', [MealController::class, 'edit'])->name('meals.edit');
Route::put('/meals/{meal}', [MealController::class, 'update'])->name('meals.update');
Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');

Route::get('/goals', [GoalController::class, 'edit'])->name('goals.edit');
Route::put('/goals', [GoalController::class, 'update'])->name('goals.update');
