<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\StrongPassword;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', new StrongPassword(12, true, true, true, true)],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Merge guest cart with user cart after registration
        $this->mergeCart();

        return redirect(route('dashboard', absolute: false));
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
}
