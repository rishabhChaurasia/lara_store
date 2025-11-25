@extends('layouts.app')

@section('title', 'Review Your Order - Checkout')

@section('content')
<main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    @php
        $shipping = 500; // $5.00 in cents
        $tax = (int)($finalTotal * 0.0825); // 8.25% tax (using final total after coupon)
        $grandTotal = $finalTotal + $shipping + $tax;
    @endphp
    
    <div class="flex flex-col gap-8">
        <!-- Breadcrumbs & Page Heading -->
        <div class="flex flex-col gap-4">
            <nav class="flex flex-wrap gap-2">
                <a class="text-gray-500 dark:text-gray-400 text-sm md:text-base font-medium leading-normal hover:text-gray-700 dark:hover:text-gray-300" href="{{ route('home') }}">Home</a>
                <span class="text-gray-500 dark:text-gray-400 text-sm md:text-base font-medium leading-normal">/</span>
                <a class="text-gray-500 dark:text-gray-400 text-sm md:text-base font-medium leading-normal hover:text-gray-700 dark:hover:text-gray-300" href="{{ route('shop.index') }}">Shop</a>
                <span class="text-gray-500 dark:text-gray-400 text-sm md:text-base font-medium leading-normal">/</span>
                <a class="text-gray-500 dark:text-gray-400 text-sm md:text-base font-medium leading-normal hover:text-gray-700 dark:hover:text-gray-300" href="{{ route('cart.index') }}">Cart</a>
                <span class="text-gray-500 dark:text-gray-400 text-sm md:text-base font-medium leading-normal">/</span>
                <span class="text-slate-800 dark:text-white text-sm md:text-base font-medium leading-normal">Review Order</span>
            </nav>
            <p class="text-slate-800 dark:text-white text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">Review Your Order</p>
        </div>

        @if(!$cartItems->isEmpty())
            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                <!-- Left Column -->
                <div class="lg:col-span-2 flex flex-col gap-8">
                    <!-- Shipping Details -->
                    <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 shadow-sm max-w-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-slate-800 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em]">Shipping Address</h2>
                            <a class="text-[#020202] dark:text-white text-sm font-semibold hover:underline" href="{{ route('checkout.shipping') }}">Edit</a>
                        </div>
                        <div class="space-y-1 text-sm text-gray-500 dark:text-gray-400">
                            <p class="text-slate-800 dark:text-gray-200 font-medium">{{ auth()->user()->name ?? 'Guest User' }}</p>
                            <p>123 Market St</p>
                            <p>San Francisco, CA 94103</p>
                            <p>USA</p>
                            <p>(123) 456-7890</p>
                        </div>
                    </div>

                    <!-- Order Items Section -->
                    <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm">
                        <h2 class="text-slate-800 dark:text-white text-xl font-bold leading-tight tracking-[-0.015em] px-6 pb-3 pt-5">
                            Your Items ({{ $cartItems->sum('quantity') }})
                        </h2>
                        <div class="flow-root">
                            <ul class="divide-y divide-gray-200 dark:divide-zinc-800" role="list">
                                @foreach($cartItems as $item)
                                    @php
                                        $price = $item->product ? $item->product->price : $item->price;
                                        $subtotal = $price * $item->quantity;
                                    @endphp
                                    <li class="flex py-6 px-6">
                                        <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-2xl border border-gray-200 dark:border-zinc-700">
                                            @if($item->product && $item->product->image_path)
                                                <img class="h-full w-full object-cover object-center" src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}"/>
                                            @else
                                                <div class="h-full w-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                                    <span class="text-gray-400 dark:text-gray-500 text-xs">No Image</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex flex-1 flex-col">
                                            <div>
                                                <div class="flex justify-between text-base font-medium text-slate-800 dark:text-white">
                                                    <h3>
                                                        <a href="{{ route('product.show', $item->product->slug ?? '#') }}" class="hover:text-zinc-600 dark:hover:text-zinc-300">
                                                            {{ $item->product ? $item->product->name : $item->name }}
                                                        </a>
                                                    </h3>
                                                    <p class="ml-4">${{ number_format($price / 100, 2) }}</p>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    SKU: {{ $item->product ? $item->product->sku : $item->sku }}
                                                </p>
                                            </div>
                                            <div class="flex flex-1 items-end justify-between text-sm">
                                                <p class="text-gray-500 dark:text-gray-400">Qty {{ $item->quantity }}</p>
                                                @if($item->quantity > 1)
                                                    <p class="text-slate-800 dark:text-white font-medium">${{ number_format($subtotal / 100, 2) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Sticky Order Summary) -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24 bg-white dark:bg-zinc-900 rounded-3xl shadow-sm p-6">
                        <h2 class="text-slate-800 dark:text-white text-lg font-bold leading-tight tracking-[-0.015em] border-b border-gray-200 dark:border-zinc-800 pb-4 mb-4">
                            Order Summary
                        </h2>
                        <div class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <p class="text-gray-500 dark:text-gray-400">Subtotal</p>
                                <p class="text-slate-800 dark:text-gray-200 font-medium">${{ number_format($cartTotal / 100, 2) }}</p>
                            </div>
                            @if($appliedCoupon)
                                <div class="flex justify-between text-sm text-green-600 dark:text-green-400">
                                    <p>Coupon ({{ $appliedCoupon->code }})</p>
                                    <p class="font-medium">-${{ number_format($discountAmount / 100, 2) }}</p>
                                </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <p class="text-gray-500 dark:text-gray-400">Shipping</p>
                                <p class="text-slate-800 dark:text-gray-200 font-medium">${{ number_format($shipping / 100, 2) }}</p>
                            </div>
                            <div class="flex justify-between text-sm">
                                <p class="text-gray-500 dark:text-gray-400">Taxes</p>
                                <p class="text-slate-800 dark:text-gray-200 font-medium">${{ number_format($tax / 100, 2) }}</p>
                            </div>
                            <div class="border-t border-gray-200 dark:border-zinc-800 pt-4 mt-4 flex justify-between text-base font-bold text-slate-800 dark:text-white">
                                <p>Grand Total</p>
                                <p>${{ number_format($grandTotal / 100, 2) }}</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <a href="{{ route('checkout.payment') }}" class="block w-full text-center bg-[#020202] dark:bg-white text-white dark:text-[#020202] font-bold py-3 px-4 rounded-xl hover:opacity-90 transition-opacity">
                                Place Your Order
                            </a>
                        </div>

                        <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Secure Checkout</span>
                        </div>

                        <p class="mt-4 text-xs text-gray-500 dark:text-gray-500 text-center">
                            By placing your order, you agree to our 
                            <a class="text-[#020202] dark:text-white hover:underline" href="#">Terms &amp; Conditions</a> 
                            and 
                            <a class="text-[#020202] dark:text-white hover:underline" href="#">Privacy Policy</a>.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm p-12 text-center max-w-lg mx-auto">
                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                <h3 class="mt-6 text-xl font-bold text-slate-800 dark:text-white">Your cart is empty</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Add some products to continue with checkout</p>
                <div class="mt-8">
                    <a href="{{ route('shop.index') }}" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white dark:text-[#020202] bg-[#020202] dark:bg-white hover:opacity-90 transition-opacity">
                        Start Shopping
                    </a>
                </div>
            </div>
        @endif
    </div>
</main>
@endsection