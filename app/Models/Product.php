<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'pack',
        'benefits',
        'category_id',
        'rating',
        'calories',
        'nutrition',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'nutrition' => 'array',
        'benefits' => 'array', // Assuming benefits is also stored as JSON or similar if needed, check migration
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
