<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityDeposit extends Model
{
    use HasFactory;

    protected $primaryKey = 'security_deposit_id';

    protected $fillable = [
        'booking_id',
        'invoice_id',
        'amount_required',
        'amount_paid',
        'amount_deducted',
        'amount_refunded',
        'status',
        'deduction_reason',
        'notes',
        'refunded_at',
        'processed_by_user_id',
    ];

    protected $casts = [
        'amount_required' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_deducted' => 'decimal:2',
        'amount_refunded' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'Pending';
    const STATUS_HELD = 'Held';
    const STATUS_DEPLETED = 'Depleted';
    const STATUS_PARTIALLY_REFUNDED = 'Partially Refunded';
    const STATUS_REFUNDED = 'Refunded';
    const STATUS_FORFEITED = 'Forfeited';

    /**
     * Deduction category constants
     */
    const DEDUCTION_UNPAID_RENT_UTILITIES = 'Unpaid Rent/Utilities';
    const DEDUCTION_UNPAID_ELECTRICITY = 'Unpaid Electricity';
    const DEDUCTION_DAMAGES = 'Damages';
    const DEDUCTION_CLEANING_FEE = 'Cleaning Fee';
    const DEDUCTION_OTHER = 'Other';

    public static function getDeductionCategories(): array
    {
        return [
            self::DEDUCTION_UNPAID_RENT_UTILITIES,
            self::DEDUCTION_UNPAID_ELECTRICITY,
            self::DEDUCTION_DAMAGES,
            self::DEDUCTION_CLEANING_FEE,
            self::DEDUCTION_OTHER,
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_HELD,
            self::STATUS_DEPLETED,
            self::STATUS_PARTIALLY_REFUNDED,
            self::STATUS_REFUNDED,
            self::STATUS_FORFEITED,
        ];
    }

    /**
     * Relationships
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id', 'user_id');
    }

    /**
     * Calculate the refundable amount
     * (Amount paid minus any deductions already applied)
     */
    public function calculateRefundable(): float
    {
        return max(0, $this->amount_paid - $this->amount_deducted - $this->amount_refunded);
    }

    /**
     * Get the held balance (what's currently being held)
     */
    public function getHeldBalance(): float
    {
        return $this->amount_paid - $this->amount_deducted - $this->amount_refunded;
    }

    /**
     * Check if deposit is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->amount_paid >= $this->amount_required;
    }

    /**
     * Get payment percentage
     */
    public function getPaymentPercentage(): float
    {
        if ($this->amount_required <= 0) {
            return 100;
        }
        return min(100, ($this->amount_paid / $this->amount_required) * 100);
    }

    /**
     * Apply a deduction to the security deposit
     */
    public function applyDeduction(float $amount, string $category, string $description = null): bool
    {
        $refundable = $this->calculateRefundable();

        if ($amount > $refundable) {
            return false; // Cannot deduct more than available
        }

        // Build deduction reason entry
        $deductions = $this->deduction_reason ? json_decode($this->deduction_reason, true) : [];
        $deductions[] = [
            'category' => $category,
            'amount' => $amount,
            'description' => $description,
            'date' => now()->toDateTimeString(),
        ];

        $this->amount_deducted += $amount;
        $this->deduction_reason = json_encode($deductions);

        // Update status if needed - Depleted (not Forfeited) when balance is zero from deductions
        // Forfeited is only used when tenant explicitly forfeits/loses the deposit
        if ($this->getHeldBalance() <= 0) {
            $this->status = self::STATUS_DEPLETED;
        }

        return $this->save();
    }

    /**
     * Process a refund to the tenant
     */
    public function processRefund(float $amount = null, int $processedByUserId = null): bool
    {
        $refundable = $this->calculateRefundable();

        // If no amount specified, refund the entire refundable balance
        if ($amount === null) {
            $amount = $refundable;
        }

        if ($amount > $refundable) {
            return false; // Cannot refund more than available
        }

        $this->amount_refunded += $amount;
        $this->refunded_at = now();
        $this->processed_by_user_id = $processedByUserId;

        // Update status based on refund
        $remainingBalance = $this->getHeldBalance();
        if ($remainingBalance <= 0) {
            $this->status = $this->amount_deducted > 0 ? self::STATUS_PARTIALLY_REFUNDED : self::STATUS_REFUNDED;
        } else {
            $this->status = self::STATUS_PARTIALLY_REFUNDED;
        }

        return $this->save();
    }

    /**
     * Forfeit the entire deposit (e.g., tenant abandons)
     */
    public function forfeit(string $reason = null, int $processedByUserId = null): bool
    {
        $remaining = $this->calculateRefundable();

        if ($remaining > 0) {
            // Apply the remaining as a deduction
            $this->amount_deducted += $remaining;

            $deductions = $this->deduction_reason ? json_decode($this->deduction_reason, true) : [];
            $deductions[] = [
                'category' => 'Forfeiture',
                'amount' => $remaining,
                'description' => $reason ?? 'Deposit forfeited',
                'date' => now()->toDateTimeString(),
            ];
            $this->deduction_reason = json_encode($deductions);
        }

        $this->status = self::STATUS_FORFEITED;
        $this->processed_by_user_id = $processedByUserId;
        $this->refunded_at = now();

        return $this->save();
    }

    /**
     * Update status based on payment received
     */
    public function updateStatusFromPayment(): void
    {
        if ($this->amount_paid > 0 && $this->status === self::STATUS_PENDING) {
            $this->status = self::STATUS_HELD;
            $this->save();
        }
    }

    /**
     * Get parsed deductions as array
     */
    public function getDeductionsArray(): array
    {
        if (!$this->deduction_reason) {
            return [];
        }
        return json_decode($this->deduction_reason, true) ?? [];
    }

    /**
     * Roll over deposit to a new booking (renewal)
     */
    public function rolloverToBooking(int $newBookingId, float $newRequiredAmount = null): SecurityDeposit
    {
        $heldBalance = $this->getHeldBalance();

        // Mark current deposit as refunded (rolled over)
        $this->status = self::STATUS_REFUNDED;
        $this->amount_refunded = $heldBalance;
        $this->refunded_at = now();
        $this->notes = ($this->notes ? $this->notes . "\n" : '') . "Rolled over to booking #{$newBookingId}";
        $this->save();

        // Create new deposit record
        $newDeposit = new SecurityDeposit();
        $newDeposit->booking_id = $newBookingId;
        $newDeposit->amount_required = $newRequiredAmount ?? $this->amount_required;
        $newDeposit->amount_paid = min($heldBalance, $newDeposit->amount_required);
        $newDeposit->status = $newDeposit->amount_paid > 0 ? self::STATUS_HELD : self::STATUS_PENDING;
        $newDeposit->notes = "Rolled over from booking #{$this->booking_id}";
        $newDeposit->save();

        return $newDeposit;
    }
}
