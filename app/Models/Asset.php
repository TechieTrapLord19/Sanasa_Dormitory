<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $primaryKey = 'asset_id';
    
    protected $fillable = [
        'room_id',
        'name',
        'condition',
        'date_acquired',
    ];

    protected $casts = [
        'date_acquired' => 'date',
    ];

    /**
     * Get the room that owns this asset
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }
}
