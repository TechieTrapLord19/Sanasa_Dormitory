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
}
