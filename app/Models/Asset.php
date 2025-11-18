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

    /**
     * Get the location display string
     */
    public function getLocationAttribute(): string
    {
        if ($this->room_id && $this->room) {
            return "Room {$this->room->room_num}";
        }
        return "Storage";
    }
}
