<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    
    protected $fillable = [
        'booking_id',
        'invoice_id',
        'collected_by_user_id',
        'payment_type',
        'amount',
        'payment_method',
        'reference_number',
        'date_received',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date_received' => 'date',
    ];

    /**
     * Get the booking this payment belongs to
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the invoice this payment belongs to (nullable for Security Deposit payments)
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Get the user who collected this payment
     */
    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by_user_id', 'user_id');
    }
}
