<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Navigation would go here if needed -->
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                                    {{ config('app.name', 'Laravel') }}
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <a href="{{ route('shop.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    Shop
                                </a>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                            <!-- Cart -->
                            <a href="{{ route('cart.index') }}" class="text-gray-500 hover:text-gray-700 relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                @php
                                    $cartCount = 0;
                                    if(Auth::check()) {
                                        $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                                        $cartCount = $cart ? $cart->items->count() : 0;
                                    } else {
                                        $cartCount = session('cart') ? count(session('cart')) : 0;
                                    }
                                @endphp
                                @if($cartCount > 0)
                                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>

                            <!-- Auth Links -->
                            @auth
                                <a href="{{ route('account.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                                    {{ Auth::user()->name }}
                                </a>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="text-gray-500 hover:text-gray-700">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700">Login</a>
                                <a href="{{ route('register') }}" class="text-gray-500 hover:text-gray-700">Register</a>
                            @endauth
                        </div>

                        <!-- Mobile menu button -->
                        <div class="flex items-center sm:hidden">
                            <!-- Cart with badge on mobile -->
                            <a href="{{ route('cart.index') }}" class="text-gray-500 hover:text-gray-700 relative mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                @php
                                    $cartCount = 0;
                                    if(Auth::check()) {
                                        $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                                        $cartCount = $cart ? $cart->items->count() : 0;
                                    } else {
                                        $cartCount = session('cart') ? count(session('cart')) : 0;
                                    }
                                @endphp
                                @if($cartCount > 0)
                                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>

                            <!-- Mobile menu button -->
                            <button type="button" class="mobile-menu-button text-gray-500 hover:text-gray-700" aria-label="Toggle menu">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div class="mobile-menu hidden bg-white border-t border-gray-200">
                    <div class="pt-2 pb-3 space-y-1 px-4">
                        <a href="{{ route('shop.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            Shop
                        </a>

                        @auth
                            <a href="{{ route('account.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                {{ Auth::user()->name }}
                            </a>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                               class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                Logout
                            </a>
                            <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Login</a>
                            <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Register</a>
                        @endauth
                    </div>
                </div>
            </nav>

            <script>
                // Mobile menu toggle functionality
                document.addEventListener('DOMContentLoaded', function() {
                    const mobileMenuButton = document.querySelector('.mobile-menu-button');
                    const mobileMenu = document.querySelector('.mobile-menu');

                    if (mobileMenuButton && mobileMenu) {
                        mobileMenuButton.addEventListener('click', function() {
                            mobileMenu.classList.toggle('hidden');
                        });
                    }
                });
            </script>

            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            @yield('header')
                        </h2>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </body>
</html>
