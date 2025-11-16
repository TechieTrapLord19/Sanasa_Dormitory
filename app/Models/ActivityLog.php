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
     * Get the user (caretaker) who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the caretaker's full name
     */
    public function getCaretakerNameAttribute()
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
}

