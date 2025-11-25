<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display the shopping cart page.
     */
    public function index()
    {
        $cartItems = collect();
        $cartTotal = 0;
        $cartCount = 0;
        $appliedCoupon = null;
        $discountAmount = 0;
        $finalTotal = 0;

        if (Auth::check()) {
            // Authenticated user - get from database
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ], [
                'session_id' => session()->getId(),
                'expires_at' => now()->addDays(30)
            ]);

            $cartItems = $cart->items()->with('product')->get();
        } else {
            // Guest user - get from session
            $cartItems = collect(session()->get('cart', []));
        }

        // Calculate totals
        foreach ($cartItems as $item) {
            $price = $item->product ? $item->product->price : $item['price'];
            $quantity = $item->quantity ?? $item['quantity'];
            $cartTotal += $price * $quantity;
            $cartCount += $quantity;
        }

        // Apply coupon if exists
        $appliedCouponCode = session('applied_coupon');
        if ($appliedCouponCode) {
            $result = $this->applyCouponDiscount($cartTotal, $appliedCouponCode);
            $finalTotal = $result['total'];
            $discountAmount = $result['discount'];
            $appliedCoupon = $result['coupon'] ?? null;
        } else {
            $finalTotal = $cartTotal;
        }

        return view('shop.cart', compact(
            'cartItems',
            'cartTotal',
            'cartCount',
            'finalTotal',
            'discountAmount',
            'appliedCoupon'
        ));
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active || $product->stock_quantity < $request->quantity) {
            return back()->with('error', 'Product is not available or insufficient stock.');
        }

        if (Auth::check()) {
            // Authenticated user - use database cart
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ], [
                'session_id' => session()->getId(),
                'expires_at' => now()->addDays(30)
            ]);

            $cartItem = $cart->items()->firstOrNew([
                'product_id' => $product->id
            ]);

            if ($cartItem->exists) {
                $newQuantity = $cartItem->quantity + $request->quantity;
                if ($newQuantity > $product->stock_quantity) {
                    return back()->with('error', 'Not enough stock available.');
                }
                $cartItem->quantity = $newQuantity;
            } else {
                $cartItem->quantity = $request->quantity;
                $cart->items()->save($cartItem);
            }

            $cartItem->save();
        } else {
            // Guest user - use session cart
            $cart = session()->get('cart', []);

            $productId = $product->id;
            if (isset($cart[$productId])) {
                $newQuantity = $cart[$productId]['quantity'] + $request->quantity;
                if ($newQuantity > $product->stock_quantity) {
                    return back()->with('error', 'Not enough stock available.');
                }
                $cart[$productId]['quantity'] = $newQuantity;
            } else {
                $cart[$productId] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image_path' => $product->image_path,
                    'quantity' => $request->quantity,
                    'sku' => $product->sku
                ];
            }

            session()->put('cart', $cart);
        }

        return back()->with('success', $product->name . ' added to cart successfully!');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
            ]);

            $product = Product::findOrFail($request->product_id);

            // Handle quantity change from form submission
            $newQuantity = $request->input('quantity');

            // If quantity_change is provided (for + and - buttons), calculate new quantity
            if ($request->has('quantity_change')) {
                $cartItems = $this->getCartItems();
                $cartItem = null;

                foreach ($cartItems as $item) {
                    $itemId = $item->product ? $item->product->id : ($item->product_id ?? $item->id);
                    if ($itemId == $request->product_id) {
                        $cartItem = $item;
                        break;
                    }
                }

                if ($cartItem) {
                    $currentQuantity = $cartItem->quantity ?? $cartItem['quantity'];
                    $change = (int)$request->quantity_change;
                    $newQuantity = $currentQuantity + $change;
                    $newQuantity = max(1, $newQuantity); // Minimum quantity is 1
                    $newQuantity = min(10, $newQuantity); // Maximum quantity is 10
                } else {
                    return back()->with('error', 'Cart item not found.');
                }
            }

            // Validate quantity range
            $newQuantity = max(1, min(10, (int)$newQuantity));

            \Log::info('Cart update request started', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user() ? Auth::user()->email : 'guest',
                'product_id' => $request->product_id,
                'new_quantity' => $newQuantity,
                'request_all' => $request->all()
            ]);

            if (Auth::check()) {
                // Authenticated user - update database cart
                $cart = Cart::firstOrCreate([
                    'user_id' => Auth::id()
                ]);

                \Log::info('Found cart for user', ['cart_id' => $cart->id]);

                $cartItem = $cart->items()->where('product_id', $product->id)->first();
                \Log::info('Cart item query result', [
                    'product_id' => $product->id,
                    'cart_item_exists' => $cartItem ? true : false,
                    'cart_item_data' => $cartItem ? $cartItem->toArray() : null
                ]);

                if ($cartItem) {
                    \Log::info('Updating existing cart item', [
                        'old_quantity' => $cartItem->quantity,
                        'new_quantity' => $newQuantity
                    ]);

                    if ($newQuantity <= 0) {
                        $cartItem->delete();
                        \Log::info('Cart item deleted');
                    } else {
                        if ($newQuantity > $product->stock_quantity) {
                            return back()->with('error', 'Not enough stock available. Only ' . $product->stock_quantity . ' items available.');
                        }
                        $cartItem->quantity = $newQuantity;
                        $cartItem->save();
                        \Log::info('Cart item updated', ['new_quantity' => $cartItem->quantity]);
                    }
                } else {
                    \Log::warning('Cart item not found for product', ['product_id' => $product->id]);
                    return back()->with('error', 'Cart item not found for the specified product.');
                }
            } else {
                // Guest user - update session cart
                $cart = session()->get('cart', []);

                \Log::info('Guest cart contents before update', [
                    'cart' => $cart,
                    'product_id_to_update' => $request->product_id
                ]);

                $productId = $request->product_id; // Use the product_id from the request
                if (isset($cart[$productId])) {
                    if ($newQuantity <= 0) {
                        unset($cart[$productId]);
                        \Log::info('Removed item from guest cart');
                    } else {
                        if ($newQuantity > $product->stock_quantity) {
                            return back()->with('error', 'Not enough stock available. Only ' . $product->stock_quantity . ' items available.');
                        }
                        $cart[$productId]['quantity'] = $newQuantity;
                        \Log::info('Updated guest cart item', ['new_quantity' => $cart[$productId]['quantity']]);
                    }
                    session()->put('cart', $cart);
                } else {
                    \Log::warning('Product not found in guest cart', ['product_id' => $productId, 'cart_contents' => array_keys($cart)]);
                    return back()->with('error', 'Item not found in your cart.');
                }
            }

            return back()->with('success', 'Cart updated successfully');

        } catch (\Exception $e) {
            \Log::error('Error in cart update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'An error occurred while updating the cart: ' . $e->getMessage());
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        if (Auth::check()) {
            // Authenticated user - remove from database cart
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);

            $cart->items()->where('product_id', $request->product_id)->delete();
        } else {
            // Guest user - remove from session cart
            $cart = session()->get('cart', []);
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Item removed from cart');
    }

    /**
     * Helper method to get cart items.
     */
    private function getCartItems()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);
            return $cart->items()->with('product')->get();
        } else {
            $cartData = session()->get('cart', []);
            $cartItems = collect();
            foreach ($cartData as $item) {
                $cartItems->push((object) $item);
            }
            return $cartItems;
        }
    }

    /**
     * Apply a coupon to the cart.
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:255'
        ]);

        $couponCode = strtoupper(trim($request->coupon_code));

        // Find the coupon
        $coupon = \App\Models\Coupon::where('code', $couponCode)->first();

        if (!$coupon) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['errors' => ['coupon_code' => ['Invalid coupon code.']]], 422);
            }
            return back()->with('error', 'Invalid coupon code.');
        }

        if (!$coupon->isValid()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['errors' => ['coupon_code' => ['This coupon is not valid or has expired.']]], 422);
            }
            return back()->with('error', 'This coupon is not valid or has expired.');
        }

        // Calculate cart total to check minimum amount requirement
        $cartItems = $this->getCartItems();
        $cartTotal = $this->getCartTotal($cartItems);

        if ($coupon->min_amount && $cartTotal < $coupon->min_amount) {
            $errorMessage = 'Coupon requires a minimum order amount of $' . number_format($coupon->min_amount / 100, 2) . '.';
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['errors' => ['coupon_code' => [$errorMessage]]], 422);
            }
            return back()->with('error', $errorMessage);
        }

        // Store coupon in session
        session(['applied_coupon' => $coupon->code]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => 'Coupon applied successfully! You received a discount.']);
        }
        return back()->with('success', 'Coupon applied successfully! You received a discount.');
    }

    /**
     * Remove coupon from the cart.
     */
    public function removeCoupon(Request $request)
    {
        session()->forget('applied_coupon');

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => 'Coupon removed from cart.']);
        }
        return back()->with('success', 'Coupon removed from cart.');
    }

    /**
     * Apply coupon discount to cart total.
     */
    private function applyCouponDiscount($cartTotal, $couponCode)
    {
        if (!$couponCode) {
            return ['total' => $cartTotal, 'discount' => 0];
        }

        $coupon = \App\Models\Coupon::where('code', $couponCode)->first();

        if (!$coupon || !$coupon->isValid()) {
            session()->forget('applied_coupon');
            return ['total' => $cartTotal, 'discount' => 0];
        }

        $discount = 0;

        if ($coupon->type === 'percentage') {
            $discount = ($cartTotal * $coupon->value) / 100;
        } elseif ($coupon->type === 'fixed') {
            $discount = min($coupon->value, $cartTotal); // Can't discount more than the total
        }

        $discount = min($discount, $cartTotal); // Ensure discount doesn't exceed total
        $newTotal = $cartTotal - $discount;

        // Update coupon usage count
        if (Auth::check()) {
            $coupon->increment('usage_count');
        }

        return [
            'total' => $newTotal,
            'discount' => $discount,
            'coupon' => $coupon
        ];
    }

    /**
     * Helper method to calculate cart total.
     */
    private function getCartTotal($cartItems)
    {
        $total = 0;
        foreach ($cartItems as $item) {
            $price = $item->product ? $item->product->price : $item->price;
            $quantity = $item->quantity ?? $item->quantity;
            $total += $price * $quantity;
        }
        return $total;
    }
}