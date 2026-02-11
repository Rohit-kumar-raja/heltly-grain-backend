<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'calories',
        'protein',
        'carbs',
        'fats',
        'type',
        'image',
    ];

    protected $casts = [
        'calories' => 'integer',
        'protein' => 'float',
        'carbs' => 'float',
        'fats' => 'float',
    ];
}
