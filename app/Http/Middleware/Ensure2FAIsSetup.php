<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Ensure2FAIsSetup
{
    /**
     * Redirect authenticated users to the 2FA setup page
     * if they haven't enabled Two-Factor Authentication yet.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->two_factor_enabled) {
            return redirect()->route('two-factor.setup')
                ->with('force_2fa', true);
        }

        return $next($request);
    }
}
