<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Meal extends Model
{
    protected $fillable = [
        'name',
        'image_path',
        'calories',
        'protein',
        'carbs',
        'fat',
        'eaten_at',
    ];
 
    protected $casts = [
        'eaten_at' => 'datetime',
        'protein'  => 'float',
        'carbs'    => 'float',
        'fat'      => 'float',
    ];
}
 
