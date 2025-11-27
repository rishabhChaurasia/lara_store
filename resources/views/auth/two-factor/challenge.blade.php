<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your authentication code to continue.') }}
    </div>

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf

        <!-- Code -->
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700">{{ __('Code') }}</label>
            <input id="code" 
                   type="text" 
                   name="code" 
                   inputmode="numeric" 
                   required 
                   autocomplete="one-time-code" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
            @error('code')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
               href="{{ route('two-factor.recovery') }}">
                {{ __('Use a recovery code') }}
            </a>

            <button type="submit" 
                    class="ml-4 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
</x-guest-layout>