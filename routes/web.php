<?php

use App\Http\Controllers\MealController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MealController::class, 'index'])->name('meals.index');
Route::get('/meals/create', [MealController::class, 'create'])->name('meals.create');
Route::get('/meals/edit/{id}', [MealController::class, 'edit'])->name('meals.edit');

Route::get('/meals/barcode_test', [MealController::class, 'barcode_test'])->name('meals.barcode_test');

Route::get('/meal/search/{query}', [MealController::class, 'api_search_v2'])->name('api.meal_query');

Route::put('/meals/edit/{id}', [MealController::class, 'update'])->name('meals.update');
Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');