<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHealthProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vitals',
        'body_composition',
        'lifestyle',
        'nutrition_stats',
        'medical_history',
        'lab_results',
        'mental_health',
    ];

    protected $casts = [
        'vitals' => 'array',
        'body_composition' => 'array',
        'lifestyle' => 'array',
        'nutrition_stats' => 'array',
        'medical_history' => 'array',
        'lab_results' => 'array',
        'mental_health' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }
}
