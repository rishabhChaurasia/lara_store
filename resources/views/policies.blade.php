@extends('layouts.app')

@section('title', 'Legal Center - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-white dark:bg-dark-bg py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <aside class="w-full md:w-64 lg:w-72 flex-shrink-0">
                <div class="sticky top-24">
                    <div class="bg-white dark:bg-[#101010] p-6 rounded-2xl border border-gray-200 dark:border-transparent">
                        <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-800">
                            <h1 class="text-xl font-bold text-black dark:text-white">Legal</h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Policies & Terms</p>
                        </div>
                        <nav class="flex flex-col gap-2">
                            <a href="{{ route('policies.show', 'privacy') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ $activePolicy === 'privacy' ? 'bg-black dark:bg-white text-white dark:text-black' : 'hover:bg-gray-100 dark:hover:bg-[#161616] text-gray-700 dark:text-gray-300' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-sm font-medium">Privacy Policy</span>
                            </a>
                            <a href="{{ route('policies.show', 'terms') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ $activePolicy === 'terms' ? 'bg-black dark:bg-white text-white dark:text-black' : 'hover:bg-gray-100 dark:hover:bg-[#161616] text-gray-700 dark:text-gray-300' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm font-medium">Terms of Service</span>
                            </a>
                            <a href="{{ route('policies.show', 'shipping') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ $activePolicy === 'shipping' ? 'bg-black dark:bg-white text-white dark:text-black' : 'hover:bg-gray-100 dark:hover:bg-[#161616] text-gray-700 dark:text-gray-300' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                                <span class="text-sm font-medium">Shipping Policy</span>
                            </a>
                            <a href="{{ route('policies.show', 'refund') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ $activePolicy === 'refund' ? 'bg-black dark:bg-white text-white dark:text-black' : 'hover:bg-gray-100 dark:hover:bg-[#161616] text-gray-700 dark:text-gray-300' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="text-sm font-medium">Refund Policy</span>
                            </a>
                        </nav>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 min-w-0">
                <div class="bg-white dark:bg-[#101010] p-8 lg:p-12 rounded-2xl border border-gray-200 dark:border-transparent">
                    @if($activePolicy === 'privacy')
                        @include('policies.privacy')
                    @elseif($activePolicy === 'terms')
                        @include('policies.terms')
                    @elseif($activePolicy === 'shipping')
                        @include('policies.shipping')
                    @elseif($activePolicy === 'refund')
                        @include('policies.refund')
                    @endif
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
