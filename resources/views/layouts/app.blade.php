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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
        <style>
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
        </style>

        <!-- Scripts -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-dark-bg dark:text-gray-100" 
          x-data="{ 
              mobileMenuOpen: false, 
              searchOpen: false,
              theme: localStorage.theme || 'system',
              setTheme(val) {
                  this.theme = val;
                  if (val === 'dark') {
                      document.documentElement.classList.add('dark');
                      localStorage.theme = 'dark';
                  } else if (val === 'light') {
                      document.documentElement.classList.remove('dark');
                      localStorage.theme = 'light';
                  } else {
                      localStorage.removeItem('theme');
                      if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                          document.documentElement.classList.add('dark');
                      } else {
                          document.documentElement.classList.remove('dark');
                      }
                  }
              }
          }">


        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <!-- Navigation -->
            <nav class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-gray-200 supports-[backdrop-filter]:bg-white/60 dark:bg-dark-bg/80 dark:border-gray-800 dark:supports-[backdrop-filter]:bg-dark-bg/60 transition-colors duration-300">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        <!-- Left Section: Logo & Desktop Nav -->
                        <div class="flex items-center gap-8">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('home') }}" class="text-xl font-bold text-black tracking-tight dark:text-white transition-colors">
                                    {{ config('app.name', 'LaraStore') }}
                                </a>
                            </div>

                            <!-- Desktop Nav Links -->
                            <div class="hidden md:flex space-x-6">
                                <a href="{{ route('home') }}" class="text-sm font-medium text-gray-500 hover:text-black transition-colors {{ request()->routeIs('home') ? 'text-black dark:text-white' : 'dark:text-gray-400 dark:hover:text-white' }}">
                                    Home
                                </a>
                                <a href="{{ route('shop.index') }}" class="text-sm font-medium text-gray-500 hover:text-black transition-colors {{ request()->routeIs('shop.*') ? 'text-black dark:text-white' : 'dark:text-gray-400 dark:hover:text-white' }}">
                                    Shop
                                </a>
                                <a href="{{ route('faq') }}" class="text-sm font-medium text-gray-500 hover:text-black transition-colors {{ request()->routeIs('faq') ? 'text-black dark:text-white' : 'dark:text-gray-400 dark:hover:text-white' }}">
                                    FAQ
                                </a>
                            </div>
                        </div>

                        <!-- Right Section: Search, Cart, Auth -->
                        <div class="flex items-center gap-4">

                            <!-- Cart -->
                            <a href="{{ route('cart.index') }}" class="group relative text-gray-500 hover:text-black transition dark:text-gray-400 dark:hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
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
                                    <span class="absolute -top-1.5 -right-1.5 bg-black text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border border-white dark:bg-white dark:text-black dark:border-black">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>

                            <!-- Auth Dropdown -->
                            @auth
                                <div class="relative ml-2" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-black transition focus:outline-none dark:text-gray-300 dark:hover:text-white">
                                        <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                                        <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center text-black font-bold uppercase border border-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                        <svg class="h-4 w-4 text-gray-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 border border-gray-200 z-50 origin-top-right dark:bg-dark-bg dark:border-gray-800" style="display: none;">
                                        
                                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-800">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Signed in as</p>
                                            <p class="text-sm font-medium text-black truncate dark:text-white">{{ Auth::user()->email }}</p>
                                        </div>
                                        
                                        <a href="{{ route('account.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-white">Account</a>
                                        
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition dark:hover:bg-red-900/20">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="hidden sm:flex items-center gap-4">
                                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-black transition dark:text-gray-300 dark:hover:text-white">Log in</a>
                                    <a href="{{ route('register') }}" class="text-sm font-medium bg-black text-white px-4 py-2 rounded-md hover:bg-gray-800 transition shadow-sm dark:bg-white dark:text-black dark:hover:bg-gray-200">Sign up</a>
                                </div>
                            @endauth

                            <!-- Mobile Menu Button -->
                            <div class="flex items-center md:hidden">
                                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 hover:text-black focus:outline-none p-2 dark:text-gray-400 dark:hover:text-white">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    

                </div>

                <!-- Mobile Menu -->
                <div x-show="mobileMenuOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="md:hidden bg-white border-b border-gray-200 absolute w-full left-0 shadow-lg dark:bg-dark-bg dark:border-gray-800" style="display: none;">
                    <div class="pt-2 pb-4 space-y-1 px-4">
                        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-black hover:bg-gray-50 transition {{ request()->routeIs('home') ? 'bg-gray-50 text-black dark:bg-gray-900 dark:text-white' : 'dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-900' }}">
                            Home
                        </a>
                        <a href="{{ route('shop.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-black hover:bg-gray-50 transition {{ request()->routeIs('shop.*') ? 'bg-gray-50 text-black dark:bg-gray-900 dark:text-white' : 'dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-900' }}">
                            Shop
                        </a>
                        <a href="{{ route('faq') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-black hover:bg-gray-50 transition {{ request()->routeIs('faq') ? 'bg-gray-50 text-black dark:bg-gray-900 dark:text-white' : 'dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-900' }}">
                            FAQ
                        </a>

                        @auth
                            <div class="border-t border-gray-100 mt-4 pt-4 dark:border-gray-800">
                                <div class="flex items-center px-3 mb-3">
                                    <div class="shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-black font-bold uppercase border border-gray-200 dark:bg-gray-800 dark:text-white dark:border-gray-700">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-base font-medium text-gray-800 dark:text-white">{{ Auth::user()->name }}</div>
                                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('account.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-black hover:bg-gray-50 transition dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-900">
                                    Account
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50 transition dark:hover:bg-red-900/20">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="border-t border-gray-100 mt-4 pt-4 grid grid-cols-2 gap-4 dark:border-gray-800">
                                <a href="{{ route('login') }}" class="block text-center px-4 py-2 border border-gray-200 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 transition dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                    Log in
                                </a>
                                <a href="{{ route('register') }}" class="block text-center px-4 py-2 bg-black border border-transparent rounded-md text-base font-medium text-white hover:bg-gray-800 transition dark:bg-white dark:text-black dark:hover:bg-gray-200">
                                    Sign up
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                            @yield('header')
                        </h2>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-grow">
                @yield('content')
            </main>
            
            <!-- Footer -->
            <!-- Footer -->
            <footer class="bg-[#f9fafb] dark:bg-dark-bg mt-auto py-4 md:py-6">
                <div class="w-full px-2 md:px-4">
                    <div class="bg-[#eeeeee] dark:bg-[#101010] rounded-2xl md:rounded-[2rem] text-gray-900 dark:text-white p-6 md:p-12 lg:p-16 relative overflow-hidden">
                        <!-- Label -->
                        <div class="flex items-center gap-2 mb-8 md:mb-16">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <span class="text-xs font-bold tracking-widest text-gray-500 dark:text-gray-400">FOOTER</span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 lg:gap-24">
                            <!-- Left Side: Logo (Bottom aligned in desktop) -->
                            <div class="flex flex-col justify-between h-full min-h-0 lg:min-h-[200px]">
                                <div class="hidden lg:block"></div> <!-- Spacer -->
                                <div>
                                    <a href="{{ route('home') }}" class="text-2xl md:text-3xl font-bold tracking-tighter flex items-center gap-2">
                                        {{ config('app.name', 'LaraStore') }}
                                    </a>
                                </div>
                            </div>

                            <!-- Right Side: Links & Info -->
                            <div class="flex flex-col gap-8 md:gap-12">
                                <!-- Links Grid -->
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
                                    <!-- Column 1 -->
                                    <div class="flex flex-col gap-3 md:gap-4">
                                        <h4 class="font-bold text-gray-700 dark:text-gray-200 text-sm md:text-base">Shop</h4>
                                        <a href="{{ route('shop.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">All Products</a>
                                        <a href="{{ route('shop.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">New Arrivals</a>
                                        <a href="{{ route('shop.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">Best Sellers</a>
                                        <a href="{{ route('shop.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">Sale</a>
                                    </div>

                                    <!-- Column 2 -->
                                    <div class="flex flex-col gap-3 md:gap-4">
                                        <h4 class="font-bold text-gray-700 dark:text-gray-200 text-sm md:text-base">Customer Service</h4>
                                        <a href="{{ route('contact') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">Contact Us</a>
                                        <a href="{{ route('account.orders') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">Track Order</a>
                                        <a href="{{ route('account.orders') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">Returns</a>
                                        <a href="{{ route('faq') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">FAQ</a>
                                    </div>

                                    <!-- Column 3 -->
                                    <div class="flex flex-col gap-3 md:gap-4 col-span-2 md:col-span-1">
                                        <h4 class="font-bold text-gray-700 dark:text-gray-200 text-sm md:text-base">Policies</h4>
                                        <a href="{{ route('policies.show', 'privacy') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">Privacy Policy</a>
                                        <a href="{{ route('policies.show', 'terms') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">Terms of Service</a>
                                        <a href="{{ route('policies.show', 'shipping') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">Shipping Policy</a>
                                        <a href="{{ route('policies.show', 'refund') }}" class="text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white transition text-xs md:text-sm">Refund Policy</a>
                                    </div>
                                </div>

                                <!-- Bottom Actions -->
                                <div class="flex flex-col md:flex-row justify-between items-center md:items-end gap-6 md:gap-8 mt-6 md:mt-8 border-t border-gray-300 dark:border-gray-800 pt-6 md:pt-8">
                                    <!-- Theme Toggle -->
                                    <div class="bg-gray-200 dark:bg-[#020303] rounded-full p-1 flex items-center border border-gray-300 dark:border-gray-800">
                                        <!-- Light Mode -->
                                        <button @click="setTheme('light')" 
                                                :class="theme === 'light' ? 'bg-white text-black shadow-sm px-3 py-1.5' : 'text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white p-2'"
                                                class="rounded-full transition-all duration-200 flex items-center gap-2">
                                            <svg class="w-4 h-4" :class="theme === 'light' ? 'w-3 h-3' : 'w-4 h-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            <span x-show="theme === 'light'" class="text-xs font-bold">LIGHT</span>
                                        </button>
                                        
                                        <!-- Dark Mode -->
                                        <button @click="setTheme('dark')" 
                                                :class="theme === 'dark' ? 'bg-white text-black shadow-sm px-3 py-1.5' : 'text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white p-2'"
                                                class="rounded-full transition-all duration-200 flex items-center gap-2">
                                            <svg class="w-4 h-4" :class="theme === 'dark' ? 'w-3 h-3' : 'w-4 h-4'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                                            <span x-show="theme === 'dark'" class="text-xs font-bold">DARK</span>
                                        </button>

                                        <!-- System Mode -->
                                        <button @click="setTheme('system')" 
                                                :class="theme === 'system' ? 'bg-white text-black shadow-sm px-3 py-1.5' : 'text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white p-2'"
                                                class="rounded-full transition-all duration-200 flex items-center gap-2">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            <span x-show="theme === 'system'" class="text-xs font-bold">SYSTEM</span>
                                        </button>
                                    </div>

                                    <!-- Copyright & Socials -->
                                    <div class="text-center md:text-right w-full md:w-auto">
                                        <div class="flex gap-3 md:gap-4 justify-center md:justify-end mb-2 text-gray-600 dark:text-gray-400 text-xs md:text-sm">
                                            <a href="#" class="hover:text-black dark:hover:text-white transition">X (Twitter)</a>
                                            <a href="#" class="hover:text-black dark:hover:text-white transition">LinkedIn</a>
                                            <a href="#" class="hover:text-black dark:hover:text-white transition">GitHub</a>
                                        </div>
                                        <p class="text-gray-500 text-xs md:text-sm">@ {{ date('Y') }} {{ config('app.name', 'LaraStore') }}. All rights reserved.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        @stack('scripts')
    </body>
</html>
