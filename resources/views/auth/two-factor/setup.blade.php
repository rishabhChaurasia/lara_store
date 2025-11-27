@extends('layouts.app')

@section('title', 'Two-Factor Authentication Setup')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Setup Two-Factor Authentication</h3>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-4">Scan the QR code below with your authenticator app:</p>
                    <div class="flex justify-center mb-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCode) }}" alt="QR Code" />
                    </div>
                    <p class="text-sm text-gray-600 text-center">Or manually enter this secret key: <code class="bg-gray-100 p-1 rounded">{{ $secret }}</code></p>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">After scanning, enter the 6-digit code from your authenticator app:</p>
                    <form method="POST" action="{{ route('two-factor.enable') }}">
                        @csrf

                        <div class="mt-4">
                            <label for="code" class="block text-sm font-medium text-gray-700">Authentication Code</label>
                            <input type="text" id="code" name="code" required autocomplete="off"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                            @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Enable Two-Factor Authentication
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-8">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Recovery Codes</h4>
                    <p class="text-sm text-gray-600 mb-4">Store these recovery codes in a secure password manager. They can be used to access your account if you lose your two-factor authentication device.</p>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($recoveryCodes as $code)
                            <div class="p-3 bg-gray-50 rounded border border-gray-200 font-mono text-sm">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection