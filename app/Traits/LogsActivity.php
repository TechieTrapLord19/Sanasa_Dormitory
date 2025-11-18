<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log an activity performed by any authenticated user
     * 
     * @param string $action The action performed (e.g., "Created Booking", "Updated Tenant")
     * @param string $description Detailed description of what was done
     * @param Model|null $model The model instance that was affected (optional)
     * @return void
     */
    protected function logActivity(string $action, string $description, $model = null): void
    {
        $user = Auth::user();
        
        // Only log if user is authenticated (all roles: owner, admin, developer, caretaker)
        if (!$user) {
            return;
        }

        ActivityLog::create([
            'user_id' => $user->user_id,
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null,
        ]);
    }
}

