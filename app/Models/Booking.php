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
        'cancellation_reason',
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
     * Get all refunds for this booking
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'booking_id', 'booking_id');
    }

    /**
     * Check if booking can be refunded (cancelled and not checked in)
     */
    public function canBeRefunded(): bool
    {
        return $this->status === 'Canceled' && !$this->isCheckedIn();
    }

    /**
     * Check if booking has been checked in
     */
    public function isCheckedIn(): bool
    {
        return $this->status === 'Active' && $this->checkin_date !== null;
    }

    /**
     * Check if booking dates overlap with given dates
     */
    public static function hasOverlap($roomId, $checkinDate, $checkoutDate, $excludeBookingId = null)
    {
        $query = static::where('room_id', $roomId)
            ->where('status', '!=', 'Canceled')
            ->where(function ($q) use ($checkinDate, $checkoutDate) {
                $q->whereBetween('checkin_date', [$checkinDate, $checkoutDate])
                  ->orWhereBetween('checkout_date', [$checkinDate, $checkoutDate])
                  ->orWhere(function ($q2) use ($checkinDate, $checkoutDate) {
                      $q2->where('checkin_date', '<=', $checkinDate)
                         ->where('checkout_date', '>=', $checkoutDate);
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

    /**
     * Get the effective status based on invoice payment status
     * Returns: 'Pending Payment', 'Partial Payment', 'Paid', 'Active', 'Completed', or 'Canceled'
     * Note: Even if booking is Active, it can still show Pending/Partial Payment if invoices are not fully paid
     */
    public function getEffectiveStatusAttribute(): string
    {
        // If booking is in a final state (Completed or Canceled), return it
        if (in_array($this->status, ['Completed', 'Canceled'])) {
            return $this->status;
        }

        // Load invoices with payments
        $invoices = $this->invoices()->with('payments')->get();
        
        if ($invoices->isEmpty()) {
            // If booking is Active but no invoices, still show Active
            return $this->status === 'Active' ? 'Active' : 'Pending Payment';
        }

        $totalDue = $invoices->sum('total_due');
        $totalPaid = 0;
        $hasAnyPayment = false;

        foreach ($invoices as $invoice) {
            $invoicePaid = $invoice->payments->sum('amount');
            $totalPaid += $invoicePaid;
            if ($invoicePaid > 0) {
                $hasAnyPayment = true;
            }
        }

        // If no payments at all
        if (!$hasAnyPayment) {
            // If booking is Active but no payments, show Pending Payment
            return 'Pending Payment';
        }

        // If all invoices are fully paid
        if ($totalPaid >= $totalDue) {
            // If booking is Active and fully paid, show Active
            return $this->status === 'Active' ? 'Active' : 'Paid';
        }

        // If partial payment (some payments but not fully paid)
        // Show Partial Payment even if booking is Active
        return 'Partial Payment';
    }
}
