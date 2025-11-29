<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
        'secondary_tenant_id',
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
     * Get the primary tenant for this booking
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get the secondary tenant for this booking
     */
    public function secondaryTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'secondary_tenant_id', 'tenant_id');
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
     * Get the security deposit for this booking
     */
    public function securityDeposit()
    {
        return $this->hasOne(SecurityDeposit::class, 'booking_id', 'booking_id');
    }

    /**
     * Get all refunds for this booking (through payments)
     */
    public function refunds(): HasManyThrough
    {
        return $this->hasManyThrough(
            Refund::class,
            Payment::class,
            'booking_id', // Foreign key on payments table (links to bookings)
            'payment_id', // Foreign key on refunds table (links to payments)
            'booking_id', // Local key on bookings table
            'payment_id'  // Local key on payments table
        );
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
    // If booking is in a final state, return it
    if (in_array($this->status, ['Completed', 'Canceled'])) {
        return $this->status;
    }

    // If booking has been checked in, treat it as Active regardless of invoice aggregation
    if ($this->status === 'Active') {
        return 'Active';
    }

    // Load invoices with payments
    $invoices = $this->invoices()->with('payments')->get();

    // If no invoices, return booking status
    if ($invoices->isEmpty()) {
        return $this->status === 'Active' ? 'Active' : 'Pending Payment';
    }

    $totalDue = $invoices->sum('total_due');
    $totalPaid = 0;

    foreach ($invoices as $invoice) {
        $invoicePaid = $invoice->payments->sum('amount');
        $totalPaid += $invoicePaid;
    }

    // No payments made yet
    if ($totalPaid == 0) {
        return 'Pending Payment';
    }

    // Fully paid
    if ($totalPaid >= $totalDue) {
        return 'Paid Payment';
    }

    // Partially paid
    return 'Partial Payment';
}

    /**
     * Get all tenant full names associated with the booking
     */
    public function getTenantNamesAttribute(): array
    {
        return collect([
            optional($this->tenant)->full_name,
            optional($this->secondaryTenant)->full_name,
        ])->filter()->values()->toArray();
    }

    /**
     * Get tenant names as a single string
     */
    public function getTenantSummaryAttribute()
    {
        $tenants = collect();

        // Add primary tenant
        if ($this->tenant) {
            $tenants->push($this->formatTenantName($this->tenant));
        }

        // Add secondary tenant
        if ($this->secondaryTenant) {
            $tenants->push($this->formatTenantName($this->secondaryTenant));
        }

        if ($tenants->isEmpty()) {
            return 'No tenants';
        } elseif ($tenants->count() === 1) {
            return $tenants->first();
        } else {
            return $tenants->implode("<br>");
        }
    }

    /**
     * Format tenant name as "LastName, FirstName MiddleInitial."
     */
    private function formatTenantName($tenant): string
    {
        // Use the individual name fields directly instead of parsing full_name
        $lastName = $tenant->last_name;
        $firstName = $tenant->first_name;
        $middleName = $tenant->middle_name;

        if (!$lastName || !$firstName) {
            return $tenant->full_name;
        }

        // If there's a middle name, add the initial with a period
        if ($middleName) {
            return $lastName . ', ' . $firstName . ' ' . $middleName[0] . '.';
        }

        // Only first and last name
        return $lastName . ', ' . $firstName;
    }    /**
     * List conflicting tenant names who already have active/pending bookings
     */
    public static function conflictingTenantNames(array $tenantIds, ?int $excludeBookingId = null): array
    {
        if (empty($tenantIds)) {
            return [];
        }

        $tenantIds = array_filter(array_unique($tenantIds));

        $conflicts = static::query()
            ->whereIn('status', ['Pending Payment', 'Active'])
            ->when($excludeBookingId, function ($query) use ($excludeBookingId) {
                $query->where('booking_id', '!=', $excludeBookingId);
            })
            ->where(function ($query) use ($tenantIds) {
                $query->whereIn('tenant_id', $tenantIds)
                      ->orWhereIn('secondary_tenant_id', $tenantIds);
            })
            ->with(['tenant', 'secondaryTenant', 'room'])
            ->get();

        return $conflicts->flatMap(function ($booking) use ($tenantIds) {
            $names = collect();
            if ($booking->tenant && in_array($booking->tenant_id, $tenantIds, true)) {
                $names->push($booking->tenant->full_name);
            }
            if ($booking->secondaryTenant && in_array($booking->secondary_tenant_id, $tenantIds, true)) {
                $names->push($booking->secondaryTenant->full_name);
            }
            return $names;
        })->unique()->values()->toArray();
    }
}
