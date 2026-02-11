<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressLog extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'weight',
        'calories',
        'protein',
        'carbs',
        'fats',
    ];

    protected $casts = [
        'date' => 'date',
        'weight' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }
}
