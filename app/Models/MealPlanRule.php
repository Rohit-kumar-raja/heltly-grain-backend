<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPlanRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'condition_field',
        'operator',
        'condition_value',
        'action_type',
        'action_value',
        'product_id',
        'priority',
    ];

    protected $casts = [
        'action_value' => 'array',
        'priority' => 'integer',
        'product_id' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
