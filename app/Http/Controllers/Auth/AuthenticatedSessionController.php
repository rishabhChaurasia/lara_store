<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // If the user needs to do 2FA, the LoginRequest will have set the 'login.user' session
        if (session()->has('login.user')) {
            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        // Merge guest cart with user cart
        $this->mergeCart();

        // Redirect based on user role
        $user = auth()->user();
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Regular users go to the main homepage after login, ignoring any intended redirect
        return redirect('/');
    }

    /**
     * Merge guest cart with user cart.
     */
    private function mergeCart()
    {
        $user = auth()->user();
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

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // If the user is logged in, invalidate all their sessions across devices
        if ($user) {
            // Clear remember me token to log out from remember me sessions
            $user->update(['remember_token' => null]);

            // Delete all sessions for this user from the sessions table
            \DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

        return redirect('/');
    }
}
