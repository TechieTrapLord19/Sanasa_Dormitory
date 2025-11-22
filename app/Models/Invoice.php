<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Invoice extends Model
{
    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'booking_id',
        'date_generated',
        'rent_subtotal',
        'utility_electricity_fee',
        'total_due',
        'is_paid',
    ];

    protected $casts = [
        'date_generated' => 'date',
        'rent_subtotal' => 'decimal:2',
        'utility_electricity_fee' => 'decimal:2',
        'total_due' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    /**
     * Get the booking this invoice belongs to
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get all utilities for this invoice
     */
    public function invoiceUtilities(): HasMany
    {
        return $this->hasMany(InvoiceUtility::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Get all payments for this invoice
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Get the total amount paid for this invoice
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Get the remaining balance for this invoice
     */
    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total_due - $this->total_paid);
    }

    /**
     * Get all refunds for this invoice (through payments)
     */
    public function refunds(): HasManyThrough
    {
        return $this->hasManyThrough(
            Refund::class,
            Payment::class,
            'invoice_id', // Foreign key on payments table (links to invoices)
            'payment_id', // Foreign key on refunds table (links to payments)
            'invoice_id', // Local key on invoices table
            'payment_id'  // Local key on payments table
        );
    }

    /**
     * Get the total amount refunded for this invoice
     */
    public function getTotalRefundedAttribute()
    {
        return $this->refunds()->sum('refund_amount');
    }

    /**
     * Check if invoice can be refunded
     * An invoice can be refunded if:
     * - Booking is cancelled
     * - Booking hasn't been checked in
     * - Invoice has payments
     */
    public function canBeRefunded(): bool
    {
        $booking = $this->booking;
        if (!$booking) {
            return false;
        }

        return $booking->canBeRefunded() && $this->payments()->exists();
    }
}