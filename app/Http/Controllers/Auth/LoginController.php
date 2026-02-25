<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Rate limiter key — per email + IP so different IPs don't share limits.
     */
    private function throttleKey(Request $request): string
    {
        return 'login.' . mb_strtolower($request->email) . '|' . $request->ip();
    }

    /**
     * Strike key — tracks how many 5-attempt batches have been exhausted.
     */
    private function strikeKey(Request $request): string
    {
        return 'login_strikes.' . sha1(mb_strtolower($request->email));
    }

    /**
     * Write an auth event to the activity log.
     * Only logged when a matching user record exists (user_id is required).
     */
    private function logAuth(int $userId, string $action, string $description): void
    {
        ActivityLog::create([
            'user_id'    => $userId,
            'action'     => $action,
            'description'=> $description,
            'model_type' => null,
            'model_id'   => null,
        ]);
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = $this->throttleKey($request);
        $strikeKey   = $this->strikeKey($request);
        $ip          = $request->ip();

        // If currently in a 5-minute cooldown, block and show countdown.
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('email'))
                ->with('throttle_seconds', $seconds);
        }

        // Attempt authentication.
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();

            // Account archived / deactivated.
            if ($user->status === 'archived') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $this->logAuth($user->user_id, 'Login Blocked', "Blocked login attempt for archived account. IP: {$ip}");

                return back()
                    ->withInput($request->only('email'))
                    ->with('account_locked', true);
            }

            // Successful login — clear all throttle/strike data.
            RateLimiter::clear($throttleKey);
            Cache::forget($strikeKey);

            // If 2FA is enabled, don't fully log in yet — redirect to challenge.
            if ($user->two_factor_enabled) {
                Auth::logout(); // undo the auth so the session stays clean
                $request->session()->put('2fa.user_id', $user->user_id);
                $request->session()->put('2fa.remember', $request->boolean('remember'));

                $this->logAuth($user->user_id, 'Login 2FA Required', "2FA challenge initiated. IP: {$ip}");

                return redirect()->route('two-factor.challenge');
            }

            $request->session()->regenerate();

            $this->logAuth($user->user_id, 'Login Success', "Logged in successfully. IP: {$ip}");

            return redirect()->intended('/dashboard');
        }

        // Wrong credentials — record the failed attempt (5-minute decay window).
        RateLimiter::hit($throttleKey, 300);

        // Try to find the user by email to log the attempt against their record.
        $user = User::where('email', $request->email)->first();

        // Check if the 5th wrong attempt was just made (batch complete).
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $strikes = Cache::get($strikeKey, 0) + 1;
            Cache::put($strikeKey, $strikes, now()->addHours(24));

            // Second batch exhausted — permanently lock the account.
            if ($strikes >= 2) {
                if ($user && $user->status !== 'archived') {
                    $user->update(['status' => 'archived']);
                    $this->logAuth($user->user_id, 'Account Locked', "Account permanently locked after repeated failed login attempts. IP: {$ip}");
                }

                RateLimiter::clear($throttleKey);

                return back()
                    ->withInput($request->only('email'))
                    ->with('account_locked', true);
            }

            // First batch exhausted — start 5-minute countdown.
            if ($user) {
                $this->logAuth($user->user_id, 'Login Blocked', "Account temporarily blocked for 5 minutes after 5 failed attempts. IP: {$ip}");
            }

            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('email'))
                ->with('throttle_seconds', $seconds);
        }

        // Still within first 5 attempts — log the failure and show remaining count.
        $attemptsLeft = 5 - RateLimiter::attempts($throttleKey);

        if ($user) {
            $this->logAuth($user->user_id, 'Login Failed', "Failed login attempt. {$attemptsLeft} attempt(s) remaining. IP: {$ip}");
        }

        throw ValidationException::withMessages([
            'email' => ["Incorrect email or password. {$attemptsLeft} attempt(s) left before a temporary lockout."],
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $this->logAuth($user->user_id, 'Logout', "Logged out. IP: {$request->ip()}");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

