<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MealController;
use App\Models\Meal;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/meal/get/{id}', function (Request $request, $id) {
   $meal_to_find = Meal::find($id);

   return $meal_to_find;
})->name('api.get_meal');