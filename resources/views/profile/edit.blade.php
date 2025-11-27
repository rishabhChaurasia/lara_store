@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="relative flex w-full" style="font-family: 'Inter', sans-serif;">
    <!-- Fixed Vertical Sidebar -->
    <aside class="sticky top-0 flex h-screen w-20 flex-col items-center bg-white py-8 dark:border-gray-700/50 dark:bg-[#101010] lg:w-64 lg:items-stretch lg:px-6 rounded-2xl mt-5 ml-5">
        <div class="flex flex-col gap-8 lg:gap-6">
            <div class="flex items-center gap-3 justify-center lg:justify-start">
                <span class="material-symbols-outlined text-gray-800 dark:text-white text-3xl">storefront</span>
                <span class="hidden text-xl font-bold text-gray-800 dark:text-white lg:inline">LUXE</span>
            </div>
            <div class="flex items-center gap-4 justify-center lg:justify-start">
                <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-12 flex items-center justify-center" style="background-color: rgba(192, 161, 114, 0.3);">
                    <span class="material-symbols-outlined text-2xl" style="color: #C0A172;">person</span>
                </div>
                <div class="hidden lg:flex lg:flex-col">
                    <h1 class="text-gray-800 dark:text-white text-base font-medium leading-normal">{{ Auth::user()->name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 text-sm font-normal leading-normal">Welcome</p>
                </div>
            </div>
            <nav class="flex flex-col gap-2">
                <a class="group flex items-center gap-3 rounded-lg px-3 py-2.5 dark:bg-opacity-20" style="background-color: rgba(192, 161, 114, 0.1);" href="{{ route('account.dashboard') }}">
                    <span class="material-symbols-outlined" style="color: #C0A172;">person</span>
                    <p class="hidden text-sm font-medium leading-normal lg:inline" style="color: #C0A172;">Profile</p>
                </a>
                <a class="group flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/50" href="{{ route('account.orders') }}">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">receipt_long</span>
                    <p class="hidden text-gray-600 dark:text-gray-400 text-sm font-medium leading-normal lg:inline group-hover:text-primary-gold">Orders</p>
                </a>
                <a class="group flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800/50" href="{{ route('account.wishlist') }}">
                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-400">favorite</span>
                    <p class="hidden text-gray-600 dark:text-gray-400 text-sm font-medium leading-normal lg:inline group-hover:text-primary-gold">Wishlist</p>
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 lg:p-10 bg-gray-50 dark:bg-[#020202]">
        <div class="mx-auto max-w-4xl">
            <!-- Page Heading -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Settings</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Manage your account settings and preferences.</p>
            </div>

            <div class="space-y-6">
                <!-- Update Profile Information -->
                <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-zinc-950">
                    <div class="p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password -->
                <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-zinc-950">
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Two-Factor Authentication -->
                <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-zinc-950">
                    <div class="p-6">
                        @include('profile.partials.two-factor-authentication-form')
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-zinc-950">
                    <div class="p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
