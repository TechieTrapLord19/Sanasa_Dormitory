<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    /**
     * Show the 2FA code entry page.
     */
    public function show(Request $request)
    {
        // Must have a pending 2FA login in session
        if (! $request->session()->has('2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the submitted OTP and complete login.
     */
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $userId = $request->session()->get('2fa.user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user || ! $user->two_factor_secret) {
            $request->session()->forget('2fa.user_id');
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (! $valid) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        // Clear the pending 2FA flag and actually log in
        $request->session()->forget('2fa.user_id');
        $request->session()->put('2fa.verified', true);

        Auth::login($user, $request->session()->get('2fa.remember', false));
        $request->session()->forget('2fa.remember');
        $request->session()->regenerate();

        ActivityLog::create([
            'user_id'    => $user->user_id,
            'action'     => 'Login Success (2FA)',
            'description'=> "Logged in with 2FA verification. IP: {$request->ip()}",
            'model_type' => null,
            'model_id'   => null,
        ]);

        return redirect()->intended('/dashboard');
    }
}
