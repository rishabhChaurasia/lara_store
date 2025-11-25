@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-6" role="alert">
            {{ session('error') }}
        </div>
    @endif
    
    @if(!$cartItems->isEmpty())
        <div class="flex flex-col gap-8">
            <!-- Breadcrumbs -->
            <nav class="flex flex-wrap gap-2">
                <a class="text-gray-500 dark:text-gray-400 text-sm font-medium leading-normal hover:text-gray-700 dark:hover:text-gray-300" href="{{ route('home') }}">Home</a>
                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium leading-normal">/</span>
                <a class="text-gray-500 dark:text-gray-400 text-sm font-medium leading-normal hover:text-gray-700 dark:hover:text-gray-300" href="{{ route('shop.index') }}">Shop</a>
                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium leading-normal">/</span>
                <span class="text-slate-800 dark:text-white text-sm font-medium leading-normal">Your Shopping Cart</span>
            </nav>
            
            <!-- Page Heading -->
            <div class="flex flex-wrap justify-between gap-3">
                <div class="flex min-w-72 flex-col gap-2">
                    <h1 class="text-slate-800 dark:text-white text-4xl font-black leading-tight tracking-[-0.033em]">My Cart</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-base font-normal leading-normal">
                        You have {{ $cartItems->sum('quantity') }} item{{ $cartItems->sum('quantity') != 1 ? 's' : '' }} in your cart.
                    </p>
                </div>
            </div>
            
            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                <!-- Left Column: Cart Items -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    @php
                        $cartTotal = 0;
                    @endphp
                    @foreach($cartItems as $item)
                        @php
                            $price = $item->product ? $item->product->price : $item->price;
                            $subtotal = $price * $item->quantity;
                            $cartTotal += $subtotal;
                        @endphp
                        
                        <!-- Cart Item Card -->
                        <div class="flex flex-col sm:flex-row gap-6 bg-white dark:bg-zinc-900 p-6 rounded-3xl shadow-sm">
                            <!-- Product Image -->
                            <a href="{{ route('product.show', $item->product->slug ?? '#') }}" class="bg-center bg-no-repeat bg-cover rounded-2xl h-48 sm:h-auto sm:w-40 flex-shrink-0 {{ !($item->product && $item->product->image_path) ? 'bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center' : '' }}" 
                               @if($item->product && $item->product->image_path) 
                               style="background-image: url('{{ asset('storage/' . $item->product->image_path) }}');"
                               @endif
                            >
                                @if(!($item->product && $item->product->image_path))
                                    <span class="text-gray-400 dark:text-gray-500 text-sm">No Image</span>
                                @endif
                            </a>
                            
                            <!-- Product Details -->
                            <div class="flex-grow flex flex-col">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <a href="{{ route('product.show', $item->product->slug ?? '#') }}">
                                            <p class="text-slate-800 dark:text-white text-lg font-semibold leading-normal hover:text-zinc-600 dark:hover:text-zinc-300">
                                                {{ $item->product ? $item->product->name : $item->name }}
                                            </p>
                                        </a>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm font-normal leading-normal">
                                            SKU: {{ $item->product ? $item->product->sku : $item->sku }}
                                        </p>
                                    </div>
                                    <p class="text-slate-800 dark:text-white text-lg font-semibold leading-normal">
                                        ${{ number_format($price / 100, 2) }}
                                    </p>
                                </div>
                                
                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-4 mt-auto">
                                    <form method="POST" action="{{ route('cart.update') }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="product_id" value="{{ $item->product ? $item->product->id : ($item->product_id ?? $item->id) }}">
                                        <div class="flex items-center gap-2 text-slate-800 dark:text-white">
                                            <button type="submit" name="quantity_change" value="-1" class="text-base font-medium leading-normal flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 cursor-pointer transition-colors">-</button>
                                            <input 
                                                class="text-base font-medium leading-normal w-12 p-0 text-center bg-transparent focus:outline-none focus:ring-0 focus:border-none border-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none" 
                                                type="number" 
                                                name="quantity"
                                                min="1"
                                                max="10"
                                                value="{{ $item->quantity }}"
                                                onchange="this.form.submit()"
                                            />
                                            <button type="submit" name="quantity_change" value="1" class="text-base font-medium leading-normal flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-zinc-800 hover:bg-gray-200 dark:hover:bg-zinc-700 cursor-pointer transition-colors">+</button>
                                        </div>
                                    </form>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                                        Subtotal: <span class="font-semibold text-slate-800 dark:text-white">${{ number_format($subtotal / 100, 2) }}</span>
                                    </p>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex items-center gap-4 bg-transparent min-h-14 justify-between mt-4 border-t border-gray-200 dark:border-zinc-800 pt-4">
                                    @auth
                                        <form action="{{ route('wishlist.toggle', $item->product) }}" method="POST" class="inline" onsubmit="return moveToWishlist(event, {{ $item->product->id }})">
                                            @csrf
                                            <button type="submit" class="flex items-center gap-2 text-slate-800 dark:text-white hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                </svg>
                                                <p class="text-sm font-medium leading-normal">Move to Wishlist</p>
                                            </button>
                                        </form>
                                        <form id="remove-form-{{ $item->product->id }}" action="{{ route('cart.remove') }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" class="flex items-center gap-2 text-slate-800 dark:text-white hover:text-zinc-600 dark:hover:text-zinc-400 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                            </svg>
                                            <p class="text-sm font-medium leading-normal">Move to Wishlist</p>
                                        </a>
                                    @endauth
                                    
                                    <form 
                                        action="{{ route('cart.remove') }}" 
                                        method="POST" 
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to remove this item from your cart?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="product_id" value="{{ $item->product ? $item->product->id : $item->id }}">
                                        <button type="submit" class="shrink-0 cursor-pointer text-slate-800 dark:text-white hover:text-red-600 dark:hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="bg-white dark:bg-zinc-900 p-6 rounded-3xl shadow-sm flex flex-col gap-6">
                            <h3 class="text-slate-800 dark:text-white text-xl font-bold">Order Summary</h3>
                            
                            <div class="flex flex-col gap-3 border-b border-gray-200 dark:border-zinc-800 pb-4">
                                <div class="flex justify-between">
                                    <p class="text-gray-500 dark:text-gray-400">Subtotal</p>
                                    <p class="text-slate-800 dark:text-white font-medium">${{ number_format($cartTotal / 100, 2) }}</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-gray-500 dark:text-gray-400">Shipping Estimate</p>
                                    <p class="text-slate-800 dark:text-white font-medium">$0.00</p>
                                </div>
                                <div class="flex justify-between">
                                    <p class="text-gray-500 dark:text-gray-400">Tax Estimate</p>
                                    <p class="text-slate-800 dark:text-white font-medium">$0.00</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-between">
                                <p class="text-slate-800 dark:text-white text-lg font-bold">Total</p>
                                <p class="text-slate-800 dark:text-white text-lg font-bold">${{ number_format($cartTotal / 100, 2) }}</p>
                            </div>
                            
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400" for="promo-code">Promo Code</label>
                                <div class="flex gap-2">
                                    <input 
                                        class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-slate-800 dark:text-white focus:outline-0 focus:ring-2 focus:ring-[#020202]/50 dark:focus:ring-white/50 border border-gray-300 dark:border-zinc-700 bg-transparent h-12 placeholder:text-gray-500 dark:placeholder:text-gray-400 px-4 text-base font-normal leading-normal" 
                                        id="promo-code" 
                                        placeholder="Enter code" 
                                        type="text"
                                    />
                                    <button class="flex items-center justify-center rounded-xl h-12 bg-gray-200 dark:bg-zinc-800 hover:bg-gray-300 dark:hover:bg-zinc-700 text-slate-800 dark:text-white text-sm font-bold px-4 transition-colors whitespace-nowrap">Apply</button>
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-3 pt-4">
                                <a href="{{ route('checkout.index') ?? '#' }}" class="flex w-full items-center justify-center overflow-hidden rounded-xl h-12 bg-[#020202] dark:bg-white text-white dark:text-[#020202] gap-2 text-base font-bold leading-normal tracking-[0.015em] hover:opacity-90 transition-opacity">
                                    Proceed to Checkout
                                </a>
                                <a href="{{ route('shop.index') }}" class="flex w-full items-center justify-center overflow-hidden rounded-xl h-12 text-[#020202] dark:text-white border border-gray-300 dark:border-zinc-700 gap-2 text-base font-bold leading-normal tracking-[0.015em] hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                    Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart State -->
        <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm p-12 text-center max-w-lg mx-auto">
            <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684  2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
            <h3 class="mt-6 text-xl font-bold text-slate-800 dark:text-white">Your cart is empty</h3>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Start adding some amazing products to your cart</p>
            <div class="mt-8">
                <a href="{{ route('shop.index') }}" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white dark:text-[#020202] bg-[#020202] dark:bg-white hover:opacity-90 transition-opacity">
                    Start Shopping
                </a>
            </div>
        </div>
    @endif
</div>

<script>
function moveToWishlist(event, productId) {
    event.preventDefault();
    const wishlistForm = event.target;
    const removeForm = document.getElementById('remove-form-' + productId);
    
    // Submit wishlist form
    fetch(wishlistForm.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // Submit remove form to remove from cart
            removeForm.submit();
        }
    })
    .catch(err => console.error('Error:', err));
    
    return false;
}
</script>
@endsection