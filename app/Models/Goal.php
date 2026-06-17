<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'calories', 'protein', 'carbs', 'fat',
        'saturated_fat', 'sugar', 'fibre', 'salt',
    ];

    protected $casts = [
        'protein'       => 'float',
        'carbs'         => 'float',
        'fat'           => 'float',
        'saturated_fat' => 'float',
        'sugar'         => 'float',
        'fibre'         => 'float',
        'salt'          => 'float',
    ];

    /**
     * Get or create the singleton goal row.
     */
    public static function current(): self
    {
        return self::firstOrCreate([], [
            'calories'      => 2000,
            'protein'       => 150,
            'carbs'         => 250,
            'fat'           => 65,
            'saturated_fat' => 20,
            'sugar'         => 30,
            'fibre'         => 30,
            'salt'          => 6,
        ]);
    }
}
