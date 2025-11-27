<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Two-Factor Authentication') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Add additional security to your account using two-factor authentication.") }}
        </p>
    </header>

    <div>
        @if (Auth::user()->hasEnabledTwoFactor())
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                {{ __('You have enabled two-factor authentication.') }}
            </p>

            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <span class="bg-green-500 w-3 h-3 rounded-full mr-2"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Enabled</span>
                </div>
                
                <form method="GET" action="{{ route('two-factor.setup') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-100 focus:bg-gray-700 dark:focus:bg-gray-100 active:bg-gray-900 dark:active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Manage') }}
                    </button>
                </form>
                
                @if(Auth::user()->role !== 'admin')
                    <form method="DELETE" action="{{ route('two-factor.disable') }}"
                          onsubmit="return confirm('Are you sure you want to disable two-factor authentication?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 dark:hover:bg-red-600 focus:bg-red-700 dark:focus:bg-red-600 active:bg-red-900 dark:active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Disable') }}
                        </button>
                    </form>
                @else
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Two-factor authentication is mandatory for administrators.') }}
                    </div>
                @endif
            </div>
        @else
            @if (Auth::user()->role === 'admin')
                <p class="text-sm text-red-600 dark:text-red-400 mb-4">
                    {{ __('Two-factor authentication is mandatory for administrators. Please enable it now.') }}
                </p>
            @else
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('You have not enabled two-factor authentication.') }}
                </p>
            @endif

            @if (Auth::user()->two_factor_secret)
                <!-- User started setup but didn't complete it -->
                <form method="GET" action="{{ route('two-factor.setup') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-100 focus:bg-gray-700 dark:focus:bg-gray-100 active:bg-gray-900 dark:active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Complete Setup') }}
                    </button>
                </form>
            @else
                <form method="GET" action="{{ route('two-factor.setup') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-100 focus:bg-gray-700 dark:focus:bg-gray-100 active:bg-gray-900 dark:active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Enable') }}
                    </button>
                </form>
            @endif
        @endif
    </div>
</section>