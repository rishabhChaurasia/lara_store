@extends('layouts.app')

@section('title', 'Shopping Cart')
@section('header', 'Your Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            {{ session('error') }}
        </div>
    @endif
    
    @if(!$cartItems->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="cart-items">
                        @php
                            $cartTotal = 0;
                        @endphp
                        @foreach($cartItems as $item)
                            @php
                                $price = $item->product ? $item->product->price : $item->price;
                                $subtotal = $price * $item->quantity;
                                $cartTotal += $subtotal;
                            @endphp
                            <tr data-product-id="{{ $item->product ? $item->product->id : $item->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($item->product && $item->product->image_path)
                                            <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="h-16 w-16 object-cover">
                                        @else
                                            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 flex items-center justify-center text-gray-500 text-xs">
                                                No Image
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->product ? $item->product->name : $item->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                SKU: {{ $item->product ? $item->product->sku : $item->sku }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($price / 100, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center">
                                        <form method="POST" action="{{ route('cart.update') }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="product_id" value="{{ $item->product ? $item->product->id : ($item->product_id ?? $item->id) }}">
                                            <div class="flex">
                                                <button type="submit" name="quantity_change" value="-1" class="bg-gray-200 hover:bg-gray-300 h-8 w-8 rounded-l text-lg">
                                                    -
                                                </button>
                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    min="1"
                                                    max="10"
                                                    value="{{ $item->quantity ?? $item->quantity }}"
                                                    class="quantity-input border-t border-b border-gray-300 h-8 w-16 text-center"
                                                    onchange="this.form.submit()"
                                                >
                                                <button type="submit" name="quantity_change" value="1" class="bg-gray-200 hover:bg-gray-300 h-8 w-8 rounded-r text-lg">
                                                    +
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($subtotal / 100, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <form 
                                        action="{{ route('cart.remove') }}" 
                                        method="POST" 
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to remove this item from your cart?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="product_id" value="{{ $item->product ? $item->product->id : $item->id }}">
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-8 flex flex-col items-end">
                <div class="w-full md:w-1/3">
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between mb-1">
                            <span>Subtotal</span>
                            <span id="cart-subtotal">${{ number_format($cartTotal / 100, 2) }}</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span>Shipping</span>
                            <span>$0.00</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span>Tax</span>
                            <span>$0.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span id="cart-total">${{ number_format($cartTotal / 100, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('shop.index') }}" class="px-6 py-3 border border-gray-300 rounded-md text-center text-gray-700 hover:bg-gray-50">
                            Continue Shopping
                        </a>
                        <a href="{{ route('checkout.index') ?? '#' }}" class="px-6 py-3 bg-blue-600 text-white rounded-md text-center hover:bg-blue-700">
                            Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h3>
            <p class="mt-1 text-gray-500">Start adding some products to your cart</p>
            <div class="mt-6">
                <a href="{{ route('shop.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                    Start Shopping
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Function to submit quantity form
    function submitQuantity(input) {
        input.form.submit();
    }

    // Function to update quantity via buttons
    function updateQuantity(button, change) {
        const form = button.form;
        const quantityInput = form.querySelector('input[name="quantity"]');
        let currentQuantity = parseInt(quantityInput.value) || 1;

        if (change === 1) {
            currentQuantity = Math.min(currentQuantity + 1, 10);
        } else if (change === -1) {
            currentQuantity = Math.max(currentQuantity - 1, 1);
        }

        quantityInput.value = currentQuantity;
        form.submit();
    }

    // Update cart badge in header after operations
    document.addEventListener('DOMContentLoaded', function() {
        // Count total items in cart
        let totalItems = 0;
        const quantityInputs = document.querySelectorAll('.quantity-input[name="quantity"]');
        quantityInputs.forEach(input => {
            totalItems += parseInt(input.value) || 0;
        });

        // Update cart badge in header
        updateCartBadge(totalItems);
    });

    function updateCartBadge(count) {
        // Update desktop cart badge
        const cartBadgeDesktop = document.querySelector('a[href$="/cart"] span.absolute');
        if (cartBadgeDesktop) {
            if (count > 0) {
                cartBadgeDesktop.textContent = count;
                cartBadgeDesktop.classList.remove('hidden');
            } else {
                cartBadgeDesktop.classList.add('hidden');
            }
        }

        // Update mobile cart badge
        const cartBadgeMobile = document.querySelector('.mobile-menu a[href$="/cart"] span.absolute');
        if (cartBadgeMobile) {
            if (count > 0) {
                cartBadgeMobile.textContent = count;
                cartBadgeMobile.classList.remove('hidden');
            } else {
                cartBadgeMobile.classList.add('hidden');
            }
        }
    }
</script>
@endpush
@endsection