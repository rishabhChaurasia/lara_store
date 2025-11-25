@extends('layouts.app')

@section('title', 'Order Confirmed - ' . config('app.name'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-black">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-zinc-900 p-8 rounded-2xl border border-gray-100 dark:border-zinc-800 shadow-sm">
        <div class="text-center">
            <!-- Success Animation/Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-6">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mb-2">
                Order Confirmed!
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Thank you for your purchase. We've received your order and will begin processing it right away.
            </p>
        </div>

        <div class="border-t border-b border-gray-100 dark:border-zinc-800 py-6 space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Order Number</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white font-mono">#{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Date</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('M d, Y') }}</span>
            </div>
            @if($order->coupon_code)
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Coupon Used</span>
                    <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ $order->coupon_code }}</span>
                </div>
            @endif
            @if($order->discount_amount > 0)
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Discount Amount</span>
                    <span class="text-sm font-bold text-green-600 dark:text-green-400">-${{ number_format($order->discount_amount / 100, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format($order->grand_total / 100, 2) }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Payment Method</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">
                    {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Credit Card' }}
                </span>
            </div>
        </div>

        <div class="space-y-3">
            <a href="{{ route('account.orders') }}" 
               class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#020202] hover:bg-[#020202]/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#020202] dark:bg-white dark:text-black dark:hover:bg-gray-100 transition-all">
                View Order Details
            </a>
            <a href="{{ route('shop.index') }}" 
               class="w-full flex justify-center py-3 px-4 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-sm text-sm font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all">
                Continue Shopping
            </a>
        </div>

        <p class="text-center text-xs text-gray-400 dark:text-gray-500 mt-6">
            A confirmation email has been sent to {{ Auth::user()->email }}
        </p>
    </div>
</div>
@endsection