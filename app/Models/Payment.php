<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    
    protected $fillable = [
        'invoice_id',
        'collected_by_user_id',
        'amount',
        'payment_method',
        'date_received',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date_received' => 'date',
    ];

    /**
     * Get the invoice this payment belongs to
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
