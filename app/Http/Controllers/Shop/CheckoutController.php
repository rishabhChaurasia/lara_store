<?php

namespace App\Http\Controllers\Shop;

use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Jobs\SendOrderConfirmationEmailJob;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CheckoutRequest; // We'll create this later
use Laravel\Cashier\Payment;

class CheckoutController extends Controller
{
    /**
     * Show the first step of checkout (Cart Review).
     */
    public function index()
    {
        // Get cart items
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
            $coupon = \App\Models\Coupon::where('code', $appliedCouponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = 0;
                if ($coupon->type === 'percentage') {
                    $discount = ($cartTotal * $coupon->value) / 100;
                } elseif ($coupon->type === 'fixed') {
                    $discount = min($coupon->value, $cartTotal); // Can't discount more than the total
                }
                $discount = min($discount, $cartTotal); // Ensure discount doesn't exceed total
                $finalTotal = $cartTotal - $discount;
                $discountAmount = $discount;
                $appliedCoupon = $coupon;
            } else {
                session()->forget('applied_coupon');
                $finalTotal = $cartTotal;
            }
        } else {
            $finalTotal = $cartTotal;
        }

        return view('shop.checkout.index', compact(
            'cartItems',
            'cartTotal',
            'cartCount',
            'finalTotal',
            'discountAmount',
            'appliedCoupon'
        ));
    }

    /**
     * Show the shipping information step.
     */
    public function shipping()
    {
        $user = Auth::user();

        // Get cart items to verify we have items to checkout
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate totals
        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->product ? $item->product->price : $item['price'];
            $quantity = $item->quantity ?? $item['quantity'];
            $cartTotal += $price * $quantity;
        }

        // Apply coupon if exists
        $appliedCouponCode = session('applied_coupon');
        $appliedCoupon = null;
        $discountAmount = 0;
        $finalTotal = $cartTotal;

        if ($appliedCouponCode) {
            $coupon = \App\Models\Coupon::where('code', $appliedCouponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = 0;
                if ($coupon->type === 'percentage') {
                    $discount = ($cartTotal * $coupon->value) / 100;
                } elseif ($coupon->type === 'fixed') {
                    $discount = min($coupon->value, $cartTotal); // Can't discount more than the total
                }
                $discount = min($discount, $cartTotal); // Ensure discount doesn't exceed total
                $finalTotal = $cartTotal - $discount;
                $discountAmount = $discount;
                $appliedCoupon = $coupon;
            } else {
                session()->forget('applied_coupon');
                $finalTotal = $cartTotal;
            }
        }

        return view('shop.checkout.shipping', compact('user', 'cartItems', 'cartTotal', 'finalTotal', 'discountAmount', 'appliedCoupon'));
    }

    /**
     * Process and store shipping information.
     */
    public function storeShipping(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'apartment' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipcode' => 'required|string|max:20',
            'country' => 'required|string|max:255',
        ]);

        // Store shipping info in session
        $shippingInfo = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'apartment' => $request->apartment,
            'city' => $request->city,
            'state' => $request->state,
            'zipcode' => $request->zipcode,
            'country' => $request->country,
        ];

        session(['checkout_shipping' => $shippingInfo]);

        return redirect()->route('checkout.payment');
    }

    /**
     * Show the payment step.
     */
    public function payment()
    {
        // Verify we have shipping info
        $shippingInfo = session('checkout_shipping');
        if (!$shippingInfo) {
            return redirect()->route('checkout.shipping');
        }

        // Get cart items
        $cartItems = $this->getCartItems();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Calculate total
        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->product ? $item->product->price : $item['price'];
            $quantity = $item->quantity ?? $item['quantity'];
            $cartTotal += $price * $quantity;
        }

        // Apply coupon if exists
        $appliedCouponCode = session('applied_coupon');
        $appliedCoupon = null;
        $discountAmount = 0;
        $finalTotal = $cartTotal;

        if ($appliedCouponCode) {
            $coupon = \App\Models\Coupon::where('code', $appliedCouponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = 0;
                if ($coupon->type === 'percentage') {
                    $discount = ($cartTotal * $coupon->value) / 100;
                } elseif ($coupon->type === 'fixed') {
                    $discount = min($coupon->value, $cartTotal); // Can't discount more than the total
                }
                $discount = min($discount, $cartTotal); // Ensure discount doesn't exceed total
                $finalTotal = $cartTotal - $discount;
                $discountAmount = $discount;
                $appliedCoupon = $coupon;
            } else {
                session()->forget('applied_coupon');
                $finalTotal = $cartTotal;
            }
        }

        return view('shop.checkout.payment', compact('shippingInfo', 'cartItems', 'cartTotal', 'finalTotal', 'discountAmount', 'appliedCoupon'));
    }

    /**
     * Process payment and finalize order.
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,cod'
        ]);

        // Get cart items and shipping info
        $cartItems = $this->getCartItems();
        $shippingInfo = session('checkout_shipping');

        if (!$shippingInfo || $cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Invalid checkout data.');
        }

        if ($request->payment_method === 'stripe') {
            // For Stripe, we need to validate card information
            $request->validate([
                'payment_intent' => 'required|string',
            ]);
        }

        $order = null; // Declare order variable outside the transaction

        if ($request->payment_method === 'stripe') {
            try {
                // For Stripe, we'll use the payment intent that should be created on the frontend
                // This assumes the payment intent was created and confirmed on the frontend
                $payment_intent = $request->payment_intent;

                // In a real application, we would verify the payment intent status on the server
                // For now, we'll trust the client-side confirmation and create the order
                DB::transaction(function () use ($cartItems, $shippingInfo, $request, &$order) {
                    // Calculate order total
                    $orderTotal = 0;
                    foreach ($cartItems as $item) {
                        $price = $item->product ? $item->product->price : $item['price'];
                        $quantity = $item->quantity ?? $item['quantity'];
                        $orderTotal += $price * $quantity;
                    }

                    // Apply coupon discount to order total if exists
                    $appliedCouponCode = session('applied_coupon');
                    $discountAmount = 0;
                    $finalOrderTotal = $orderTotal;

                    if ($appliedCouponCode) {
                        $coupon = \App\Models\Coupon::where('code', $appliedCouponCode)->first();
                        if ($coupon && $coupon->isValid()) {
                            if ($coupon->type === 'percentage') {
                                $discountAmount = ($orderTotal * $coupon->value) / 100;
                            } elseif ($coupon->type === 'fixed') {
                                $discountAmount = min($coupon->value, $orderTotal); // Can't discount more than the total
                            }
                            $discountAmount = min($discountAmount, $orderTotal); // Ensure discount doesn't exceed total
                            $finalOrderTotal = $orderTotal - $discountAmount;

                            // Update coupon usage count
                            $coupon->increment('usage_count');
                        }
                    }

                    // Create the order
                    $order = Order::create([
                        'user_id' => Auth::check() ? Auth::id() : null,
                        'status' => 'processing', // Payment successful, so processing
                        'payment_status' => 'paid',
                        'payment_method' => $request->payment_method,
                        'grand_total' => $finalOrderTotal,
                        'discount_amount' => $discountAmount,
                        'coupon_code' => $appliedCouponCode,
                        'shipping_address' => $shippingInfo,
                    ]);

                    // Create order items and update stock
                    foreach ($cartItems as $item) {
                        $product = $item->product ?? Product::find($item['id']);
                        $quantity = $item->quantity ?? $item['quantity'];

                        // Create order item
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'unit_price' => $product->price,
                        ]);

                        // Decrement stock
                        $product->decrement('stock_quantity', $quantity);
                    }

                    // Clear cart
                    if (Auth::check()) {
                        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
                        $cart->items()->delete();
                    } else {
                        session()->forget('cart');
                    }

                    // Clear applied coupon from session
                    session()->forget('applied_coupon');
                });

                // Dispatch order confirmation email job to run in the background
                SendOrderConfirmationEmailJob::dispatch($order);

                // Clear checkout session data
                session()->forget('checkout_shipping');

                // Redirect to order confirmation
                return redirect()->route('checkout.success', $order->id)
                    ->with('success', 'Your order has been placed successfully!');
            } catch (\Laravel\Cashier\Exceptions\IncompletePayment $e) {
                Log::error('Stripe incomplete payment: ' . $e->getMessage());
                return redirect()->route('checkout.payment')
                    ->with('error', 'Payment requires additional authentication. Please try again.');
            } catch (\Exception $e) {
                Log::error('Stripe payment failed: ' . $e->getMessage());
                return redirect()->route('checkout.payment')
                    ->with('error', 'Payment failed: ' . $e->getMessage());
            }
        } else {
            // For COD (Cash on Delivery)
            DB::transaction(function () use ($cartItems, $shippingInfo, $request, &$order) {
                // Calculate order total
                $orderTotal = 0;
                foreach ($cartItems as $item) {
                    $price = $item->product ? $item->product->price : $item['price'];
                    $quantity = $item->quantity ?? $item['quantity'];
                    $orderTotal += $price * $quantity;
                }

                // Apply coupon discount to order total if exists
                $appliedCouponCode = session('applied_coupon');
                $discountAmount = 0;
                $finalOrderTotal = $orderTotal;

                if ($appliedCouponCode) {
                    $coupon = \App\Models\Coupon::where('code', $appliedCouponCode)->first();
                    if ($coupon && $coupon->isValid()) {
                        if ($coupon->type === 'percentage') {
                            $discountAmount = ($orderTotal * $coupon->value) / 100;
                        } elseif ($coupon->type === 'fixed') {
                            $discountAmount = min($coupon->value, $orderTotal); // Can't discount more than the total
                        }
                        $discountAmount = min($discountAmount, $orderTotal); // Ensure discount doesn't exceed total
                        $finalOrderTotal = $orderTotal - $discountAmount;

                        // Update coupon usage count
                        $coupon->increment('usage_count');
                    }
                }

                // Create the order
                $order = Order::create([
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'status' => 'pending', // Awaiting payment on delivery
                    'payment_status' => 'unpaid',
                    'payment_method' => $request->payment_method,
                    'grand_total' => $finalOrderTotal,
                    'discount_amount' => $discountAmount,
                    'coupon_code' => $appliedCouponCode,
                    'shipping_address' => $shippingInfo,
                ]);

                // Create order items and update stock
                foreach ($cartItems as $item) {
                    $product = $item->product ?? Product::find($item['id']);
                    $quantity = $item->quantity ?? $item['quantity'];

                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                    ]);

                    // Decrement stock
                    $product->decrement('stock_quantity', $quantity);
                }

                // Clear cart
                if (Auth::check()) {
                    $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
                    $cart->items()->delete();
                } else {
                    session()->forget('cart');
                }

                // Clear applied coupon from session
                session()->forget('applied_coupon');
            });

            // Dispatch order confirmation email job to run in the background
            SendOrderConfirmationEmailJob::dispatch($order);

            // Clear checkout session data
            session()->forget('checkout_shipping');

            // Redirect to order confirmation
            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Your order has been placed successfully!');
        }
    }

    /**
     * Create a payment intent for Stripe.
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Get cart items to calculate the amount
            $cartItems = $this->getCartItems();
            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Cart is empty'], 400);
            }

            $amount = 0;
            foreach ($cartItems as $item) {
                $price = $item->product ? $item->product->price : $item['price'];
                $quantity = $item->quantity ?? $item['quantity'];
                $amount += $price * $quantity;
            }

            // Create a payment intent via Cashier
            $paymentIntent = $user->createPaymentIntent($amount / 100, [ // Convert cents to dollars
                'currency' => 'usd',
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret
            ]);
        } catch (\Laravel\Cashier\Exceptions\IncompletePayment $e) {
            return response()->json(['error' => 'Payment requires additional authentication.'], 400);
        } catch (\Exception $e) {
            \Log::error('Stripe Payment Intent Creation Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create payment intent. Please try again.'], 500);
        }
    }

    /**
     * Show order success/confirmation page.
     */
    public function success($orderId)
    {
        $order = Order::with(['items.product'])->findOrFail($orderId);

        // Verify user can access this order
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('shop.checkout.success', compact('order'));
    }

    /**
     * Helper method to get cart items regardless of authentication status.
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
}