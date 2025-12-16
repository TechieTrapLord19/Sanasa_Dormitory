<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Tenant extends Model
{
    use HasFactory;
    protected $primaryKey = 'tenant_id';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'address',
        'birth_date',
        'id_document',
        'contact_num',
        'emer_contact_num',
        'emer_contact_name',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Get the tenant's full name
     */
    public function getFullNameAttribute()
    {
        $name = $this->last_name . ', ' . $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        return $name;
    }

    /**
     * Get the contact info (email or phone)
     */
    public function getContactInfoAttribute()
    {
        return $this->email ?? $this->contact_num ?? 'N/A';
    }

    /**
     * Get all bookings for this tenant
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the current active booking for this tenant (as primary or secondary tenant)
     */
    public function currentBooking()
    {
        return $this->hasOne(Booking::class, 'tenant_id', 'tenant_id')
                    ->whereIn('status', ['Active', 'Pending Payment'])
                    ->with('room')
                    ->latest('checkin_date');
    }

    /**
     * Get the assigned room number for this tenant
     */
    public function getAssignedRoomNumberAttribute()
    {
        $booking = $this->currentBooking;
        return $booking && $booking->room ? $booking->room->room_num : null;
    }

    /**
     * Get all payments for this tenant across all bookings
     */
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Booking::class,
            'tenant_id',
            'booking_id',
            'tenant_id',
            'booking_id'
        );
    }

    /**
     * Get the tenant's age
     */
    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }
        return $this->birth_date->age;
    }
}
