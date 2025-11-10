<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'room_id',
        'tenant_id',
        'rate_id',
        'recorded_by_user_id',
        'checkin_date',
        'checkout_date',
        'total_calculated_fee',
        'status',
    ];

    protected $casts = [
        'checkin_date' => 'date',
        'checkout_date' => 'date',
        'total_calculated_fee' => 'decimal:2',
    ];

    /**
     * Get the room that this booking is for
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    /**
     * Get the tenant for this booking
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the rate for this booking
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class, 'rate_id', 'rate_id');
    }

    /**
     * Get the user who recorded this booking
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id', 'user_id');
    }

    /**
     * Get all invoices for this booking
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'booking_id', 'booking_id');
    }

    /**
     * Check if booking dates overlap with given dates
     */
    public static function hasOverlap($roomId, $checkinDate, $checkoutDate, $excludeBookingId = null)
    {
        $query = self::where('room_id', $roomId)
            ->where('status', '!=', 'Canceled')
            ->where(function($q) use ($checkinDate, $checkoutDate) {
                $q->where(function($q2) use ($checkinDate, $checkoutDate) {
                    // New booking starts before existing ends AND new booking ends after existing starts
                    $q2->where('checkin_date', '<', $checkoutDate)
                       ->where('checkout_date', '>', $checkinDate);
                });
            });

        if ($excludeBookingId) {
            $query->where('booking_id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }
    public function activeBooking()
    {
        return $this->hasOne(\App\Models\Booking::class, 'room_id', 'room_id')->where('status', 'active');
    }
}
