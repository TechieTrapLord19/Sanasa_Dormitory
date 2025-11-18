<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $primaryKey = 'refund_id';

    protected $fillable = [
        'payment_id',
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
     * Get the booking this refund belongs to (through payment)
     * Uses accessor since hasOneThrough doesn't work in reverse direction
     */
    public function getBookingAttribute()
    {
        if (!$this->relationLoaded('payment')) {
            $this->load('payment');
        }
        return $this->payment->booking ?? null;
    }

    /**
     * Get the payment this refund is for
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the invoice this refund is for (through payment)
     * Uses accessor since hasOneThrough doesn't work in reverse direction
     */
    public function getInvoiceAttribute()
    {
        if (!$this->relationLoaded('payment')) {
            $this->load('payment');
        }
        return $this->payment->invoice ?? null;
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

