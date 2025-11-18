<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    protected $primaryKey = 'utilities_id';
    
    protected $fillable = [
        'rate_id',
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function rate()
    {
        return $this->belongsTo(Rate::class, 'rate_id');
    }
}
