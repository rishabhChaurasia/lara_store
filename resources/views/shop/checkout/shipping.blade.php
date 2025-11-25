@extends('layouts.app')

@section('title', 'Shipping Information - Checkout')

@section('content')
<div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
    <div class="layout-container flex h-full grow flex-col">
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">
                <div class="lg:col-span-3">
                    <div class="flex flex-col gap-8">
                        <div class="flex flex-wrap items-center gap-2">
                            <a class="text-[#616b89] text-sm font-medium leading-normal hover:text-black dark:hover:text-white transition-colors" href="{{ route('cart.index') }}">Cart</a>
                            <span class="text-[#616b89] text-sm font-medium leading-normal">/</span>
                            <span class="text-gray-900 dark:text-white text-sm font-bold leading-normal">Shipping</span>
                            <span class="text-[#616b89] text-sm font-medium leading-normal">/</span>
                            <span class="text-[#616b89] text-sm font-medium leading-normal">Payment</span>
                        </div>
                        <p class="text-gray-900 dark:text-white text-3xl lg:text-4xl font-black leading-tight tracking-[-0.033em]">Shipping Information</p>
                        
                        <form method="POST" action="{{ route('checkout.store.shipping') }}" id="shipping-form">
                            @csrf
                            <div class="space-y-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <label class="flex flex-col">
                                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal pb-2">First Name</p>
                                        <input 
                                            name="first_name" 
                                            type="text" 
                                            value="{{ old('first_name', $user->name ? explode(' ', $user->name)[0] : '') }}"
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-primary h-14 placeholder:text-gray-400 dark:placeholder-gray-500 p-[15px] text-base font-normal leading-normal" 
                                            placeholder="Enter your first name" 
                                            required
                                        />
                                        @error('first_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </label>
                                    <label class="flex flex-col">
                                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal pb-2">Last Name</p>
                                        <input 
                                            name="last_name" 
                                            type="text" 
                                            value="{{ old('last_name', $user->name && count(explode(' ', $user->name)) > 1 ? implode(' ', array_slice(explode(' ', $user->name), 1)) : '') }}"
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-primary h-14 placeholder:text-gray-400 dark:placeholder-gray-500 p-[15px] text-base font-normal leading-normal" 
                                            placeholder="Enter your last name" 
                                            required
                                        />
                                        @error('last_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-5 gap-6">
                                    <label class="flex flex-col sm:col-span-3">
                                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal pb-2">Street Address</p>
                                        <input 
                                            name="address" 
                                            type="text" 
                                            value="{{ old('address') }}"
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-primary h-14 placeholder:text-gray-400 dark:placeholder-gray-500 p-[15px] text-base font-normal leading-normal" 
                                            placeholder="e.g., 123 Market St" 
                                            required
                                        />
                                        @error('address')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </label>
                                    <label class="flex flex-col sm:col-span-2">
                                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal pb-2">Apt, suite, etc. <span class="text-gray-500">(Optional)</span></p>
                                        <input 
                                            name="apartment" 
                                            type="text" 
                                            value="{{ old('apartment') }}"
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-primary h-14 placeholder:text-gray-400 dark:placeholder-gray-500 p-[15px] text-base font-normal leading-normal" 
                                            placeholder="e.g., Apt 12B" 
                                        />
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <label class="flex flex-col">
                                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal pb-2">Country</p>
                                        <select 
                                            name="country" 
                                            class="form-select flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-primary h-14 p-[15px] text-base font-normal leading-normal"
                                            required
                                        >
                                            <option value="United States" {{ old('country') == 'United States' ? 'selected' : '' }}>United States</option>
                                            <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                                            <option value="Mexico" {{ old('country') == 'Mexico' ? 'selected' : '' }}>Mexico</option>
                                            <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                                        </select>
                                        @error('country')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </label>
                                    <label class="flex flex-col">
                                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal pb-2">City</p>
                                        <input 
                                            name="city" 
                                            type="text" 
                                            value="{{ old('city') }}"
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-primary h-14 placeholder:text-gray-400 dark:placeholder-gray-500 p-[15px] text-base font-normal leading-normal" 
                                            placeholder="e.g., San Francisco" 
                                            required
                                        />
                                        @error('city')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <label class="flex flex-col">
                                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal pb-2">State / Province</p>
                                        <input 
                                            name="state" 
                                            type="text" 
                                            value="{{ old('state') }}"
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-primary h-14 placeholder:text-gray-400 dark:placeholder-gray-500 p-[15px] text-base font-normal leading-normal" 
                                            placeholder="e.g., California" 
                                            required
                                        />
                                        @error('state')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </label>
                                    <label class="flex flex-col">
                                        <p class="text-gray-900 dark:text-white text-base font-medium leading-normal pb-2">ZIP / Postal Code</p>
                                        <input 
                                            name="zipcode" 
                                            type="text" 
                                            value="{{ old('zipcode') }}"
                                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-900 dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:border-primary h-14 placeholder:text-gray-400 dark:placeholder-gray-500 p-[15px] text-base font-normal leading-normal" 
                                            placeholder="e.g., 94103" 
                                            required
                                        />
                                        @error('zipcode')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </label>
                                </div>

                                <div class="flex items-center gap-3 pt-2">
                                    <input 
                                        class="form-checkbox h-5 w-5 rounded-md border-gray-300 dark:border-gray-600 text-primary focus:ring-primary/50 bg-gray-100 dark:bg-gray-700" 
                                        id="save-info" 
                                        type="checkbox"
                                    />
                                    <label class="text-gray-700 dark:text-gray-300 text-base font-medium" for="save-info">Save this information for a faster checkout next time</label>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-8 mt-6">
                                <a class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors" href="{{ route('cart.index') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                                    </svg>
                                    <span>Return to Cart</span>
                                </a>
                                <button type="submit" class="flex w-full sm:w-auto cursor-pointer items-center justify-center overflow-hidden rounded-lg  bg-[#020202] dark:bg-white text-white dark:text-[#020202] gap-2 text-md font-bold leading-normal tracking-[0.015em] py-3 px-6 hover:opacity-90 transition-opacity">
                                    Continue to Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm p-6 lg:p-8 space-y-6 sticky top-24">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Order Summary</h3>
                        <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                            @foreach($cartItems as $item)
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 bg-cover bg-center rounded-lg flex-shrink-0 border border-gray-100 dark:border-zinc-800" 
                                         style="background-image: url('{{ $item->product && $item->product->image_path ? asset('storage/' . $item->product->image_path) : '' }}'); background-color: #f3f4f6;">
                                        @if(!$item->product || !$item->product->image_path)
                                            <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Image</div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800 dark:text-gray-200 line-clamp-1">{{ $item->product ? $item->product->name : $item->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        ${{ number_format(($item->product ? $item->product->price : $item->price) * $item->quantity / 100, 2) }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        
                        @php
                            $shipping = 500; // $5.00
                            $tax = $finalTotal * 0.08; // 8% tax (using final total after coupon)
                            $total = $finalTotal + $shipping + $tax;
                        @endphp

                        <div class="border-t border-gray-200 dark:border-zinc-800 pt-6 space-y-3">
                            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                <span>Subtotal</span>
                                <span>${{ number_format($cartTotal / 100, 2) }}</span>
                            </div>
                            @if($appliedCoupon)
                                <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                    <span>Coupon ({{ $appliedCoupon->code }})</span>
                                    <span class="text-green-600 dark:text-green-400">-${{ number_format($discountAmount / 100, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                <span>Shipping</span>
                                <span>${{ number_format($shipping / 100, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600 dark:text-gray-300">
                                <span>Taxes</span>
                                <span>${{ number_format($tax / 100, 2) }}</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 dark:border-zinc-800 pt-6">
                            <div class="flex justify-between font-bold text-lg text-gray-900 dark:text-white">
                                <span>Total</span>
                                <span>${{ number_format($total / 100, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection