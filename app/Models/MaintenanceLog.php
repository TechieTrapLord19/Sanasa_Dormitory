<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceLog extends Model
{
    protected $primaryKey = 'log_id';

    protected $fillable = [
        'asset_id',
        'logged_by_user_id',
        'description',
        'date_reported',
        'status',
        'date_completed',
        'notes',
    ];

    protected $casts = [
        'date_reported' => 'date',
        'date_completed' => 'date',
    ];

    /**
     * Get the asset associated with this maintenance log
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'asset_id');
    }

    /**
     * Get the user who logged this maintenance issue
     */
    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by_user_id', 'user_id');
    }

    /**
     * Get the asset location display string
     * Returns "Asset Name - Room XXX" or "Asset Name - Storage" or "General Issue"
     */
    public function getAssetLocationAttribute(): string
    {
        if ($this->asset_id && $this->asset) {
            $location = $this->asset->location; // Uses Asset's getLocationAttribute accessor
            return "{$this->asset->name} - {$location}";
        }
        return "General Issue";
    }

    /**
     * Get the reporter's full name
     */
    public function getReporterNameAttribute(): string
    {
        if ($this->loggedBy) {
            return "{$this->loggedBy->first_name} {$this->loggedBy->last_name}";
        }
        return 'Unknown';
    }
}
