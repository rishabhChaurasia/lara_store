@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-sm">
            <div>
                <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Login to your account
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-medium text-gray-900 dark:text-white hover:underline">
                        Sign up
                    </a>
                </p>
            </div>

            <div class="mt-8">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/20 p-3">
                        <p class="text-sm text-green-800 dark:text-green-400">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium text-gray-900 dark:text-white">
                            Email
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-zinc-950 text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-400 dark:focus:ring-gray-600 focus:border-gray-400 dark:focus:border-gray-600"
                        />
                        @error('email')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-sm font-medium text-gray-900 dark:text-white">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-gray-900 dark:text-white hover:underline">
                                    Forgot password?
                                </a>
                            @endif
                        </div>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-zinc-950 text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-400 dark:focus:ring-gray-600 focus:border-gray-400 dark:focus:border-gray-600"
                        />
                        @error('password')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input
                            id="remember_me"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white focus:ring-gray-400 dark:focus:ring-gray-600"
                        />
                        <label for="remember_me" class="ml-2 text-sm text-gray-900 dark:text-white">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center rounded-md bg-gray-900 dark:bg-white px-4 py-2 text-sm font-medium text-white dark:text-gray-900 transition-colors hover:bg-gray-800 dark:hover:bg-gray-100"
                    >
                        Login
                    </button>
                </form>

                @if ($errors->any() && $errors->get('email') && in_array(trans('auth.two_factor_required'), $errors->get('email')))
                    <script>
                        // Redirect to 2FA challenge page
                        window.location.href = "{{ route('two-factor.challenge') }}";
                    </script>
                @endif
            </div>
        </div>
    </div>
@endsection
