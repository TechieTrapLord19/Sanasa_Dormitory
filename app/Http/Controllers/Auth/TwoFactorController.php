<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;
use PragmaRX\Google2FALaravel\Google2FA;
use PragmaRX\Google2FA\Google2FA as BaseGoogle2FA;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA setup page with QR code.
     */
    public function show()
    {
        $user = Auth::user();

        $google2fa = new BaseGoogle2FA();

        // Read the raw DB value to handle legacy plaintext secrets
        $rawSecret = $user->getRawOriginal('two_factor_secret');

        if (! $rawSecret) {
            // No secret yet — generate a new one (will be stored encrypted via cast)
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => $secret]);
        } else {
            // Try to read via the encrypted cast; if it fails, the value is legacy plaintext
            try {
                $secret = $user->two_factor_secret;
            } catch (DecryptException $e) {
                // Legacy plaintext — re-encrypt directly via DB to bypass model cast issues
                $secret = $rawSecret;
                DB::table('users')
                    ->where('user_id', $user->user_id)
                    ->update(['two_factor_secret' => Crypt::encryptString($secret)]);
                $user->refresh();
            }
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generate inline QR code image as SVG
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCodeSvg = base64_encode($writer->writeString($qrCodeUrl));

        return view('auth.two-factor-setup', [
            'secret'     => $secret,
            'qrCodeSvg'  => $qrCodeSvg,
            'enabled'    => $user->two_factor_enabled,
        ]);
    }

    /**
     * Enable 2FA after the user confirms their OTP works.
     */
    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = Auth::user();
        $google2fa = new BaseGoogle2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (! $valid) {
            return back()->withErrors(['code' => 'The code is incorrect. Please try again.']);
        }

        $user->update(['two_factor_enabled' => true]);

        return redirect()->route('two-factor.setup')
            ->with('success', '2FA has been enabled! You will be asked for a code each time you log in.');
    }

    /**
     * Disable 2FA for the current user.
     */
    public function disable(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = Auth::user();
        $google2fa = new BaseGoogle2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (! $valid) {
            return back()->withErrors(['code' => 'The code is incorrect. Cannot disable 2FA.']);
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret'  => null,
        ]);

        return redirect()->route('two-factor.setup')
            ->with('success', '2FA has been disabled.');
    }
}
