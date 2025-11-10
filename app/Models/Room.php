<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    protected $primaryKey = 'room_id';
    protected $fillable = [
        'room_num',
        'floor',
        'capacity',
        'status',
    ];
       public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class, 'room_id', 'room_id')
                    ->where('status', 'Active')
                    ->latest();
    }

    /**
     * Get current tenant through active booking
     */
    public function currentTenant()
    {
        return $this->hasOneThrough(
            Tenant::class,
            Booking::class,
            'room_id', // Foreign key on bookings table
            'tenant_id', // Foreign key on tenants table
            'room_id', // Local key on rooms table
            'tenant_id' // Local key on bookings table
        )->whereHas('bookings', function($query) {
            $query->where('status', 'Active');
        });
    }

    /**
     * Get all bookings for this room
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'room_id', 'room_id');
    }
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id', 'tenant_id');
    }
     public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class, 'rate_id', 'rate_id');
    }
}
