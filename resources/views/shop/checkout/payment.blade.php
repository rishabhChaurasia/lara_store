@extends('layouts.app')

@section('title', 'Payment - Checkout')

@section('content')
<div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
    <div class="layout-container flex h-full grow flex-col">
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <div class="flex flex-col gap-8">
                        <!-- Breadcrumbs -->
                        <div class="flex flex-wrap items-center gap-2">
                            <a class="text-[#616b89] text-sm font-medium leading-normal hover:text-black dark:hover:text-white transition-colors" href="{{ route('cart.index') }}">Cart</a>
                            <span class="text-[#616b89] text-sm font-medium leading-normal">/</span>
                            <a class="text-[#616b89] text-sm font-medium leading-normal hover:text-black dark:hover:text-white transition-colors" href="{{ route('checkout.shipping') }}">Shipping</a>
                            <span class="text-[#616b89] text-sm font-medium leading-normal">/</span>
                            <span class="text-gray-900 dark:text-white text-sm font-bold leading-normal">Payment</span>
                        </div>

                        <p class="text-gray-900 dark:text-white text-3xl lg:text-4xl font-black leading-tight tracking-[-0.033em]">Payment Method</p>
                        
                        <!-- Shipping Info Recap -->
                        <div class="bg-gray-50 dark:bg-zinc-800/50 rounded-xl p-6 border border-gray-100 dark:border-zinc-700">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Ship to</h3>
                                    <p class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ $shippingInfo['first_name'] }} {{ $shippingInfo['last_name'] }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                        {{ $shippingInfo['address'] }}
                                        @if(!empty($shippingInfo['apartment']))
                                            , {{ $shippingInfo['apartment'] }}
                                        @endif
                                        <br>
                                        {{ $shippingInfo['city'] }}, {{ $shippingInfo['state'] }} {{ $shippingInfo['zipcode'] }}<br>
                                        {{ $shippingInfo['country'] }}
                                    </p>
                                </div>
                                <a href="{{ route('checkout.shipping') }}" class="text-sm font-medium text-primary hover:text-primary/80 transition-colors">Edit</a>
                            </div>
                        </div>

                        <form id="payment-form" method="POST" action="{{ route('checkout.process.payment') }}">
                            @csrf
                            <input type="hidden" id="payment-intent" name="payment_intent">

                            <div class="space-y-4">
                                <!-- Cash on Delivery Option -->
                                <label class="relative flex items-center p-4 rounded-xl border cursor-pointer transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600" 
                                       :class="paymentMethod === 'cod' ? 'border-primary ring-1 ring-primary bg-blue-50/10 dark:bg-blue-900/10' : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800'"
                                       x-data="{ paymentMethod: 'cod' }"
                                       @click="paymentMethod = 'cod'">
                                    <input type="radio" 
                                           id="cod" 
                                           name="payment_method" 
                                           value="cod" 
                                           class="h-5 w-5 text-primary border-gray-300 focus:ring-primary"
                                           checked
                                           @change="document.getElementById('stripe-fields').classList.add('hidden')">
                                    <div class="ml-4 flex-1">
                                        <span class="block text-base font-medium text-gray-900 dark:text-white">Cash on Delivery</span>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400 mt-1">Pay when you receive your order</span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </label>

                                <!-- Stripe Option -->
                                <label class="relative flex items-center p-4 rounded-xl border cursor-pointer transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600"
                                       :class="paymentMethod === 'stripe' ? 'border-primary ring-1 ring-primary bg-blue-50/10 dark:bg-blue-900/10' : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800'"
                                       x-data="{ paymentMethod: 'cod' }" 
                                       @click="paymentMethod = 'stripe'">
                                    <input type="radio" 
                                           id="stripe" 
                                           name="payment_method" 
                                           value="stripe" 
                                           class="h-5 w-5 text-primary border-gray-300 focus:ring-primary"
                                           @change="document.getElementById('stripe-fields').classList.remove('hidden')">
                                    <div class="ml-4 flex-1">
                                        <span class="block text-base font-medium text-gray-900 dark:text-white">Stripe</span>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400 mt-1">Secure payment via Stripe</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <svg class="h-6" viewBox="0 0 38 24" xmlns="http://www.w3.org/2000/svg" role="img"><path d="M35 0H3C1.3 0 0 1.3 0 3v18c0 1.7 1.4 3 3 3h32c1.7 0 3-1.3 3-3V3c0-1.7-1.4-3-3-3z" fill="#00579f"></path><path d="M35 1c1.1 0 2 .9 2 2v18c0 1.1-.9 2-2 2H3c-1.1 0-2-.9-2-2V3c0-1.1.9-2 2-2h32z" fill="#00579f"></path><path d="M23.5 15.6v-3.4h3.1c1.3 0 2.2.7 2.2 1.8v.1c0 .8-.5 1.3-1.1 1.6.7.2 1.3.8 1.3 1.7v.1c0 1.1-.9 1.9-2.3 1.9h-3.2v-3.8zm1.6-2.3h1.3c.5 0 .8.2.8.6v.1c0 .4-.3.6-.8.6h-1.3v-1.3zm1.6 4.9c.5 0 .9-.2.9-.7v-.1c0-.5-.4-.7-.9-.7h-1.6v1.4h1.6zM13.2 15.6v-3.4h3.1c1.3 0 2.2.7 2.2 1.8v.1c0 .8-.5 1.3-1.1 1.6.7.2 1.3.8 1.3 1.7v.1c0 1.1-.9 1.9-2.3 1.9h-3.2v-3.8zm1.6-2.3h1.3c.5 0 .8.2.8.6v.1c0 .4-.3.6-.8.6h-1.3v-1.3zm1.6 4.9c.5 0 .9-.2.9-.7v-.1c0-.5-.4-.7-.9-.7h-1.6v1.4h1.6zM10.5 15.6v-3.4h1.5v3.4h-1.5zM7.5 15.6v-3.4h1.5v3.4H7.5z" fill="#fff"></path></svg>
                                    </div>
                                </label>

                                <!-- Stripe Elements Container -->
                                <div id="stripe-fields" class="hidden mt-4 p-4 bg-gray-50 dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Card Details</label>
                                    <div id="card-element" class="p-3 bg-white dark:bg-zinc-900 border border-gray-300 dark:border-zinc-600 rounded-lg">
                                        <!-- A Stripe Element will be inserted here -->
                                    </div>
                                    <div id="card-errors" role="alert" class="text-red-600 text-sm mt-2"></div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-8 mt-6">
                                <a class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors" href="{{ route('checkout.shipping') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                                    </svg>
                                    <span>Back to Shipping</span>
                                </a>
                                <button id="submit-button" type="submit" class="flex w-full sm:w-auto cursor-pointer items-center justify-center overflow-hidden rounded-lg h-14 bg-[#020202] dark:bg-white text-white dark:text-[#020202] gap-2 text-lg font-bold leading-normal tracking-[0.015em] px-8 hover:opacity-90 transition-opacity">
                                    Place Your Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
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

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the Stripe publishable key from your config
    const stripe = Stripe({!! json_encode(config('cashier.key')) !!});

    const stripeRadio = document.getElementById('stripe');
    const codRadio = document.getElementById('cod');
    const stripeFields = document.getElementById('stripe-fields');
    const paymentForm = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const cardElementContainer = document.getElementById('card-element');
    const cardErrors = document.getElementById('card-errors');
    const paymentIntentInput = document.getElementById('payment-intent');

    // Create an instance of Elements
    const elements = stripe.elements();

    // Custom styling can be passed to parameters when creating an Element
    const style = {
        base: {
            color: '#32325d',
            fontFamily: '"Inter", sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Create an instance of the card Element
    const card = elements.create('card', {style: style});

    // Add an instance of the card Element into the `card-element` div
    card.mount(cardElementContainer);

    // Handle real-time validation errors from the card Element.
    card.on('change', function(event) {
        if (event.error) {
            cardErrors.textContent = event.error.message;
        } else {
            cardErrors.textContent = '';
        }
    });

    // Toggle Stripe fields visibility
    stripeRadio.addEventListener('change', function() {
        if (this.checked) {
            stripeFields.classList.remove('hidden');
        }
    });

    codRadio.addEventListener('change', function() {
        if (this.checked) {
            stripeFields.classList.add('hidden');
        }
    });

    // Handle form submission
    paymentForm.addEventListener('submit', function(event) {
        try {
            event.preventDefault();

            // Get the payment method
            const paymentMethodRadio = document.querySelector('input[name="payment_method"]:checked');
            if (!paymentMethodRadio) {
                cardErrors.textContent = 'Please select a payment method.';
                return;
            }
            const paymentMethod = paymentMethodRadio.value;
            console.log('Selected payment method:', paymentMethod);

            if (paymentMethod === 'stripe') {
                // Disable the submit button to prevent multiple clicks
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';

                // Verify that Stripe object is available
                if (!stripe) {
                    cardErrors.textContent = 'Stripe is not loaded properly.';
                    submitButton.disabled = false;
                    submitButton.textContent = 'Place Your Order';
                    return;
                }

                // Create a payment method
                stripe.createPaymentMethod({
                    type: 'card',
                    card: card,
                    billing_details: {
                        name: {!! json_encode($shippingInfo['first_name'] . ' ' . $shippingInfo['last_name']) !!},
                        email: {!! json_encode(Auth::check() ? Auth::user()->email : '') !!},
                        address: {
                            line1: {!! json_encode($shippingInfo['address']) !!},
                            city: {!! json_encode($shippingInfo['city']) !!},
                            state: {!! json_encode($shippingInfo['state']) !!},
                            postal_code: {!! json_encode($shippingInfo['zipcode']) !!},
                            country: {!! json_encode($shippingInfo['country']) !!},
                        },
                    },
                }).then(function(result) {
                    if (result.error) {
                        // Inform the user if there was an error
                        cardErrors.textContent = result.error.message;
                        submitButton.disabled = false;
                        submitButton.textContent = 'Place Your Order';
                    } else {
                        // Create payment intent from backend first
                        fetch({!! json_encode(route("checkout.create-payment-intent")) !!}, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({})
                        })
                        .then(function(response) {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(function(data) {
                            if (data.error) {
                                cardErrors.textContent = data.error;
                                submitButton.disabled = false;
                                submitButton.textContent = 'Place Your Order';
                            } else if (data.client_secret) {
                                // Confirm the payment with the correct amount (after coupon discount)
                                stripe.confirmCardPayment(data.client_secret, {
                                    payment_method: result.paymentMethod.id,
                                }).then(function(result) {
                                    if (result.error) {
                                        // Show error to your customer
                                        cardErrors.textContent = result.error.message;
                                        submitButton.disabled = false;
                                        submitButton.textContent = 'Place Your Order';
                                    } else {
                                        // The payment has been processed
                                        if (result.paymentIntent.status === 'succeeded') {
                                            // Set the payment intent ID in the form
                                            paymentIntentInput.value = result.paymentIntent.id;

                                            // Submit the form
                                            paymentForm.submit();
                                        }
                                    }
                                });
                            } else {
                                cardErrors.textContent = 'No payment intent was created. Please try again.';
                                submitButton.disabled = false;
                                submitButton.textContent = 'Place Your Order';
                            }
                        })
                        .catch(function(error) {
                            console.error('Error:', error);
                            cardErrors.textContent = 'An error occurred while processing your payment: ' + error.message;
                            submitButton.disabled = false;
                            submitButton.textContent = 'Place Your Order';
                        });
                    }
                }).catch(function(error) {
                    console.error('Error creating payment method:', error);
                    cardErrors.textContent = 'An error occurred while creating the payment method: ' + error.message;
                    submitButton.disabled = false;
                    submitButton.textContent = 'Place Your Order';
                });
            } else {
                // For COD, just submit the form directly
                paymentForm.submit();
            }
        } catch (error) {
            console.error('Form submission error:', error);
            cardErrors.textContent = 'An unexpected error occurred: ' + error.message;
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Place Your Order';
            }
        }
    });
});
</script>
@endpush
@endsection