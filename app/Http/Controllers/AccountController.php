<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Traits\LogsActivity;

class AccountController extends Controller
{
    use LogsActivity;

    /**
     * Display the user's account/profile page.
     */
    public function show()
    {
        $user = Auth::user();
        return view('contents.account', compact('user'));
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(12)
                    ->mixedCase()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Prevent reusing the same password
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The new password must be different from your current password.']);
        }

        $user->password = $request->password;
        $user->save();

        $this->logActivity('Changed own password', 'User changed their account password');

        return back()->with('success', 'Password updated successfully.');
    }
}
