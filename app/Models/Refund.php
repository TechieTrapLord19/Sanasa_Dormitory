<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $primaryKey = 'refund_id';

    protected $fillable = [
        'booking_id',
        'payment_id',
        'invoice_id',
        'refunded_by_user_id',
        'refund_amount',
        'refund_method',
        'reference_number',
        'refund_date',
        'cancellation_reason',
        'status',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'refund_date' => 'date',
    ];

    /**
     * Get the booking this refund belongs to
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the payment this refund is for
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the invoice this refund is for
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Get the user who processed this refund
     */
    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by_user_id', 'user_id');
    }

    /**
     * Check if refund is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if refund is processed
     */
    public function isProcessed(): bool
    {
        return $this->status === 'Processed';
    }

    /**
     * Check if refund is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }
}

