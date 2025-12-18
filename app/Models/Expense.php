<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $primaryKey = 'expense_id';

    protected $fillable = [
        'category',
        'asset_type',
        'description',
        'amount',
        'expense_date',
        'receipt_number',
        'recorded_by_user_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Expense category constants
     */
    const CATEGORY_WATER = 'Water';
    const CATEGORY_WIFI = 'Wifi';
    const CATEGORY_ELECTRICITY = 'Electricity';
    const CATEGORY_MAINTENANCE = 'Maintenance & Repairs';
    const CATEGORY_SUPPLIES = 'Supplies';
    const CATEGORY_SALARIES = 'Salaries';
    const CATEGORY_ASSET = 'Asset';
    const CATEGORY_OTHER = 'Other';

    /**
     * Get all expense categories
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_WATER,
            self::CATEGORY_WIFI,
            self::CATEGORY_ELECTRICITY,
            self::CATEGORY_MAINTENANCE,
            self::CATEGORY_SUPPLIES,
            self::CATEGORY_SALARIES,
            self::CATEGORY_ASSET,
            self::CATEGORY_OTHER,
        ];
    }

    /**
     * Relationships
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id', 'user_id');
    }
}
