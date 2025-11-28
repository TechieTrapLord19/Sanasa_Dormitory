<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $primaryKey = 'rate_id';

    protected $fillable = [
        'rate_name',
        'duration_type',
        'base_price',
        'description',
        'status',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    public function utilities()
    {
        return $this->hasMany(Utility::class, 'rate_id');
    }
}
