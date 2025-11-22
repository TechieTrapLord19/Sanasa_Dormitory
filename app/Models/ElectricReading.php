<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectricReading extends Model
{
    protected $primaryKey = 'reading_id';

    protected $fillable = [
        'room_id',
        'reading_date',
        'meter_value_kwh',
        'is_billed',
    ];

    protected $casts = [
        'reading_date' => 'date',
        'meter_value_kwh' => 'decimal:2',
        'is_billed' => 'boolean',
    ];

    /**
     * Get the room this reading belongs to
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    /**
     * Get the latest reading for a room
     */
    public static function getLatestReading($roomId)
    {
        return self::where('room_id', $roomId)
            ->orderBy('reading_date', 'desc')
            ->orderBy('reading_id', 'desc')
            ->first();
    }

    /**
     * Get unbilled readings for a room
     */
    public static function getUnbilledReadings($roomId)
    {
        return self::where('room_id', $roomId)
            ->where('is_billed', false)
            ->orderBy('reading_date', 'asc')
            ->orderBy('reading_id', 'asc')
            ->get();
    }

}