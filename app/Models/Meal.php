<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $fillable = [
        'name', 'image_path', 'calories',
        'protein', 'carbs', 'fat',
        'saturated_fat', 'sugar', 'fibre', 'salt',
        'eaten_at',
    ];

    protected $casts = [
        'eaten_at'      => 'datetime',
        'protein'       => 'float',
        'carbs'         => 'float',
        'fat'           => 'float',
        'saturated_fat' => 'float',
        'sugar'         => 'float',
        'fibre'         => 'float',
        'salt'          => 'float',
    ];

    /**
     * Return an array of positive/negative food notification strings.
     */
    public function getNotificationsAttribute(): array
    {
        $notes = [];

        // Positive
        if ($this->protein >= 20)         $notes[] = ['type' => 'good', 'label' => 'High protein'];
        if ($this->fibre >= 5)            $notes[] = ['type' => 'good', 'label' => 'High fibre'];
        if (($this->sugar > 0 && $this->sugar <= 25) && $this->calories > 0)
                                          $notes[] = ['type' => 'good', 'label' => 'Low sugar'];
        if (($this->salt > 0 && $this->salt <= 0.3) && $this->calories > 0)
                                          $notes[] = ['type' => 'good', 'label' => 'Low salt'];
        if ($this->saturated_fat <= 1.5 && $this->fat > 0)
                                          $notes[] = ['type' => 'good', 'label' => 'Low sat fat'];
        if ($this->calories > 0 && $this->calories <= 250)
                                          $notes[] = ['type' => 'good', 'label' => 'Light meal'];

        // Negative
        if ($this->sugar >= 15)           $notes[] = ['type' => 'warn', 'label' => 'High sugar'];
        if ($this->salt >= 1.5)           $notes[] = ['type' => 'warn', 'label' => 'High salt'];
        if ($this->saturated_fat >= 5)    $notes[] = ['type' => 'warn', 'label' => 'High sat fat'];
        if ($this->calories >= 800)       $notes[] = ['type' => 'warn', 'label' => 'Calorie dense'];

        return $notes;
    }
}
