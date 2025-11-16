<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait ChecksRole
{
    /**
     * Check if the current user is an owner (admin)
     */
    protected function isOwner(): bool
    {
        $user = Auth::user();
        return $user && strtolower($user->role) === 'owner';
    }

    /**
     * Check if the current user is a caretaker
     */
    protected function isCaretaker(): bool
    {
        $user = Auth::user();
        return $user && strtolower($user->role) === 'caretaker';
    }

    /**
     * Require owner access, abort if not owner
     */
    protected function requireOwner(): void
    {
        if (!$this->isOwner()) {
            abort(403, 'Unauthorized. Only admins can access this resource.');
        }
    }
}

