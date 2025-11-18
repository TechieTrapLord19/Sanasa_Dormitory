<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
    ];

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the user's full name
     */
    public function getUserNameAttribute()
    {
        if (!$this->user) {
            return 'Unknown';
        }
        
        $name = $this->user->last_name . ', ' . $this->user->first_name;
        if ($this->user->middle_name) {
            $name .= ' ' . $this->user->middle_name;
        }
        return $name;
    }

    /**
     * Get the caretaker's full name (alias for backward compatibility)
     */
    public function getCaretakerNameAttribute()
    {
        return $this->user_name;
    }

    /**
     * Get the URL to the resource that was affected by this activity
     * Returns null if no valid route exists
     */
    public function getResourceUrlAttribute()
    {
        if (!$this->model_type || !$this->model_id) {
            return null;
        }

        try {
            switch ($this->model_type) {
                case 'App\Models\Booking':
                    return route('bookings.show', $this->model_id);
                
                case 'App\Models\Tenant':
                    return route('tenants.show', $this->model_id);
                
                case 'App\Models\Room':
                    return route('rooms.show', $this->model_id);
                
                case 'App\Models\Rate':
                    return route('rates.show', $this->model_id);
                
                case 'App\Models\Invoice':
                    // No show route, redirect to index
                    return route('invoices');
                
                case 'App\Models\Payment':
                    // No show route, redirect to invoices index
                    return route('invoices');
                
                case 'App\Models\Refund':
                    // Need to get booking_id through payment relationship
                    $refund = Refund::with('payment')->find($this->model_id);
                    if ($refund && $refund->payment && $refund->payment->booking_id) {
                        return route('bookings.show', $refund->payment->booking_id);
                    }
                    return null;
                
                case 'App\Models\Asset':
                    // No show route, redirect to asset inventory index
                    return route('asset-inventory');
                
                case 'App\Models\MaintenanceLog':
                    // No show route, redirect to maintenance logs index
                    return route('maintenance-logs');
                
                default:
                    return null;
            }
        } catch (\Exception $e) {
            // If route generation fails, return null
            return null;
        }
    }
}

