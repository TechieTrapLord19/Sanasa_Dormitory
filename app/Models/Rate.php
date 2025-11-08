<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $primaryKey = 'rate_id';
    
    protected $fillable = [
        'duration_type',
        'base_price',
        'inclusion',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];
}
