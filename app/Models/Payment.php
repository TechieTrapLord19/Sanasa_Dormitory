<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'date_received' => 'datetime',
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

    /**
     * Get all refunds for this payment
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the total amount refunded for this payment
     */
    public function getTotalRefundedAttribute()
    {
        return $this->refunds()->sum('refund_amount');
    }

    /**
     * Check if payment can be refunded (not fully refunded yet)
     */
    public function canBeRefunded(): bool
    {
        return $this->total_refunded < $this->amount;
    }

    /**
     * Get the remaining refundable amount
     */
    public function getRemainingRefundableAmountAttribute()
    {
        return max(0, $this->amount - $this->total_refunded);
    }
}
