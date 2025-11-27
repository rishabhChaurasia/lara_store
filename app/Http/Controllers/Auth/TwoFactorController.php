<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = app(Google2FA::class);
    }

    /**
     * Show the two-factor authentication setup page.
     */
    public function showSetupForm(): View
    {
        $user = Auth::user();

        // Generate a new secret key if one doesn't exist
        if (!$user->two_factor_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => encrypt($secret)]);
        } else {
            $secret = decrypt($user->two_factor_secret);
        }

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generate recovery codes if they don't exist
        if (empty($user->getRecoveryCodes())) {
            $user->generateRecoveryCodes();
        }

        return view('auth.two-factor.setup', [
            'qrCode' => $qrCodeUrl,
            'secret' => $secret,
            'recoveryCodes' => $user->getRecoveryCodes()
        ]);
    }

    /**
     * Enable two-factor authentication.
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            throw ValidationException::withMessages([
                'code' => ['Two-factor authentication is not properly configured.']
            ]);
        }

        $secret = decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code, null, 4);

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['The two-factor authentication code is invalid.']
            ]);
        }

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        return redirect()->route('profile.edit')->with('status', 'two-factor-authentication-enabled');
    }

    /**
     * Disable two-factor authentication.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        // Prevent admin users from disabling 2FA (making it mandatory for admins)
        if ($user->role === 'admin') {
            throw ValidationException::withMessages([
                'code' => ['Administrators cannot disable two-factor authentication.']
            ]);
        }

        if (!$user->two_factor_secret) {
            throw ValidationException::withMessages([
                'code' => ['Two-factor authentication is not properly configured.']
            ]);
        }

        $secret = decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code, null, 4);

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['The two-factor authentication code is invalid.']
            ]);
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return redirect()->route('profile.edit')->with('status', 'two-factor-authentication-disabled');
    }

    /**
     * Show the two-factor authentication challenge page.
     */
    public function showChallengeForm(Request $request): View
    {
        return view('auth.two-factor.challenge');
    }

    /**
     * Verify the two-factor authentication code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->session()->get('login.user');

        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('login');
        }

        $secret = decrypt($user->two_factor_secret);
        // Verify the code with a larger window to accommodate time differences
        $valid = $this->google2fa->verifyKey($secret, $request->code, null, 4);

        if (!$valid) {
            // Check recovery codes if TOTP fails
            $recoveryCodes = $user->getRecoveryCodes();
            $valid = in_array($request->code, $recoveryCodes);

            if ($valid) {
                // Remove used recovery code
                $user->replaceRecoveryCode($request->code);
            }
        }

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['The two-factor authentication code is invalid.']
            ]);
        }

        // Login the user
        Auth::login($user);

        // Clear the session
        $request->session()->forget('login.user');
        $request->session()->regenerate();

        // Merge guest cart with user cart
        $this->mergeCart();

        // Redirect based on user role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended('/');
    }

    /**
     * Show the recovery form when user cannot access two-factor device.
     */
    public function showRecoveryForm(): View
    {
        return view('auth.two-factor.recovery');
    }

    /**
     * Verify using a recovery code.
     */
    public function verifyRecovery(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->session()->get('login.user');

        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('login');
        }

        $recoveryCodes = $user->getRecoveryCodes();
        $valid = in_array($request->code, $recoveryCodes);

        if (!$valid) {
            throw ValidationException::withMessages([
                'code' => ['The recovery code is invalid.']
            ]);
        }

        // Remove used recovery code
        $user->replaceRecoveryCode($request->code);

        // Login the user
        Auth::login($user);

        // Clear the session
        $request->session()->forget('login.user');
        $request->session()->regenerate();

        // Merge guest cart with user cart
        $this->mergeCart();

        // Redirect based on user role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended('/');
    }

    /**
     * Merge guest cart with user cart.
     */
    private function mergeCart()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Get guest cart items from session
        $guestCartItems = session()->get('cart', []);

        if (empty($guestCartItems)) {
            return; // No items to merge
        }

        // Get or create user's database cart
        $userCart = \App\Models\Cart::firstOrCreate([
            'user_id' => $user->id
        ], [
            'session_id' => session()->getId(),
            'expires_at' => now()->addDays(30)
        ]);

        // Merge guest cart items into user cart
        foreach ($guestCartItems as $productId => $itemData) {
            $existingItem = $userCart->items()->where('product_id', $productId)->first();

            if ($existingItem) {
                // If item exists in user cart, update quantity
                $newQuantity = $existingItem->quantity + $itemData['quantity'];

                // Ensure we don't exceed stock
                $product = \App\Models\Product::find($productId);
                if ($product) {
                    $newQuantity = min($newQuantity, $product->stock_quantity);
                }

                $existingItem->update(['quantity' => $newQuantity]);
            } else {
                // If item doesn't exist in user cart, create new cart item
                $product = \App\Models\Product::find($productId);
                if ($product) {
                    // Ensure quantity doesn't exceed stock
                    $quantity = min($itemData['quantity'], $product->stock_quantity);

                    $userCart->items()->create([
                        'product_id' => $productId,
                        'quantity' => $quantity
                    ]);
                }
            }
        }

        // Clear the guest cart session
        session()->forget('cart');
    }
}