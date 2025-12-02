<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Carbon\Carbon;

class Invoice extends Model
{
    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'booking_id',
        'date_generated',
        'due_date',
        'rent_subtotal',
        'utility_electricity_fee',
        'total_due',
        'penalty_amount',
        'days_overdue',
        'is_paid',
    ];

    protected $casts = [
        'date_generated' => 'date',
        'due_date' => 'date',
        'rent_subtotal' => 'decimal:2',
        'utility_electricity_fee' => 'decimal:2',
        'total_due' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'days_overdue' => 'integer',
        'is_paid' => 'boolean',
    ];

    /**
     * Boot method to automatically set due_date if not provided
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->due_date)) {
                $dueDays = (int) Setting::get('invoice_due_days', 15);
                $dateGenerated = $invoice->date_generated
                    ? Carbon::parse($invoice->date_generated)
                    : Carbon::now();
                $invoice->due_date = $dateGenerated->addDays($dueDays);
            }
        });
    }

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

    /**
     * Get the total amount with penalty
     */
    public function getTotalWithPenaltyAttribute()
    {
        return $this->total_due + ($this->penalty_amount ?? 0);
    }

    /**
     * Get the remaining balance including penalty
     */
    public function getRemainingBalanceWithPenaltyAttribute()
    {
        return max(0, $this->total_with_penalty - $this->total_paid);
    }

    /**
     * Check if invoice is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->is_paid || !$this->due_date) {
            return false;
        }

        // Check if remaining balance exists
        if ($this->remaining_balance <= 0) {
            return false;
        }

        return Carbon::now()->startOfDay()->gt($this->due_date);
    }

    /**
     * Get days overdue (calculated dynamically)
     */
    public function getCalculatedDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue || !$this->due_date) {
            return 0;
        }

        return Carbon::now()->startOfDay()->diffInDays($this->due_date);
    }

    /**
     * Calculate penalty based on settings
     * Returns the penalty amount that should be applied
     */
    public function calculatePenalty(): float
    {
        if (!$this->is_overdue) {
            return 0;
        }

        // Get penalty settings
        $penaltyRate = (float) Setting::get('late_penalty_rate', 5);
        $penaltyType = Setting::get('late_penalty_type', 'percentage');
        $graceDays = (int) Setting::get('late_penalty_grace_days', 7);
        $frequency = Setting::get('late_penalty_frequency', 'once');

        $daysOverdue = $this->calculated_days_overdue;

        // If still within grace period, no penalty
        if ($daysOverdue <= $graceDays) {
            return 0;
        }

        $effectiveDaysOverdue = $daysOverdue - $graceDays;

        // Calculate base penalty
        if ($penaltyType === 'fixed') {
            $basePenalty = $penaltyRate;
        } else {
            // Percentage of total_due
            $basePenalty = ($this->total_due * $penaltyRate) / 100;
        }

        // Apply frequency multiplier
        switch ($frequency) {
            case 'daily':
                return $basePenalty * $effectiveDaysOverdue;
            case 'weekly':
                $weeks = ceil($effectiveDaysOverdue / 7);
                return $basePenalty * $weeks;
            case 'monthly':
                $months = ceil($effectiveDaysOverdue / 30);
                return $basePenalty * $months;
            case 'once':
            default:
                return $basePenalty;
        }
    }

    /**
     * Apply penalty to this invoice
     */
    public function applyPenalty(): bool
    {
        $penalty = $this->calculatePenalty();
        $daysOverdue = $this->calculated_days_overdue;

        if ($penalty > 0) {
            $this->penalty_amount = $penalty;
            $this->days_overdue = $daysOverdue;
            return $this->save();
        }

        return false;
    }

    /**
     * Get overdue status label
     */
    public function getOverdueStatusAttribute(): string
    {
        if (!$this->due_date) {
            return 'No Due Date';
        }

        if ($this->is_paid || $this->remaining_balance <= 0) {
            return 'Paid';
        }

        $daysUntilDue = Carbon::now()->startOfDay()->diffInDays($this->due_date, false);

        if ($daysUntilDue < 0) {
            $daysOverdue = abs($daysUntilDue);
            return "Overdue by {$daysOverdue} day" . ($daysOverdue > 1 ? 's' : '');
        } elseif ($daysUntilDue == 0) {
            return 'Due Today';
        } elseif ($daysUntilDue <= 3) {
            return "Due in {$daysUntilDue} day" . ($daysUntilDue > 1 ? 's' : '');
        } else {
            return 'Not Yet Due';
        }
    }
}
