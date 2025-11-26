@extends('layouts.admin')

@section('title', 'Abandoned Cart Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Abandoned Cart Settings</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.marketing.abandoned-carts.settings.update') }}" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-6">
                <div>
                    <label for="first_reminder_hours" class="block text-sm font-medium text-gray-700 mb-1">
                        First Reminder (Hours)
                    </label>
                    <input type="number" name="first_reminder_hours" id="first_reminder_hours"
                           value="{{ old('first_reminder_hours', $settings['first_reminder_hours']) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Hours after cart abandonment to send first reminder</p>
                </div>

                <div>
                    <label for="second_reminder_hours" class="block text-sm font-medium text-gray-700 mb-1">
                        Second Reminder (Hours)
                    </label>
                    <input type="number" name="second_reminder_hours" id="second_reminder_hours"
                           value="{{ old('second_reminder_hours', $settings['second_reminder_hours']) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Hours after first reminder to send second reminder</p>
                </div>

                <div>
                    <label for="third_reminder_days" class="block text-sm font-medium text-gray-700 mb-1">
                        Third Reminder (Days)
                    </label>
                    <input type="number" name="third_reminder_days" id="third_reminder_days"
                           value="{{ old('third_reminder_days', $settings['third_reminder_days']) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Days after second reminder to send third reminder</p>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label for="abandoned_threshold_minutes" class="block text-sm font-medium text-gray-700 mb-1">
                        Abandoned Threshold (Minutes)
                    </label>
                    <input type="number" name="abandoned_threshold_minutes" id="abandoned_threshold_minutes"
                           value="{{ old('abandoned_threshold_minutes', $settings['abandoned_threshold_minutes']) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Minutes of inactivity before cart is marked as abandoned</p>
                </div>

                <div>
                    <label for="second_reminder_discount" class="block text-sm font-medium text-gray-700 mb-1">
                        Second Reminder Discount (%)
                    </label>
                    <input type="number" name="second_reminder_discount" id="second_reminder_discount"
                           value="{{ old('second_reminder_discount', $settings['second_reminder_discount']) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Discount percentage for second reminder emails</p>
                </div>

                <div>
                    <label for="third_reminder_discount" class="block text-sm font-medium text-gray-700 mb-1">
                        Third Reminder Discount (%)
                    </label>
                    <input type="number" name="third_reminder_discount" id="third_reminder_discount"
                           value="{{ old('third_reminder_discount', $settings['third_reminder_discount']) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Discount percentage for third reminder emails</p>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Settings
            </button>
        </div>
    </form>

    <!-- Email Template Customization -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Email Template Customization</h2>

        <form method="POST" action="{{ route('admin.marketing.abandoned-carts.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- First Reminder Template -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">First Reminder Email</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="first_reminder_subject" class="block text-sm font-medium text-gray-700 mb-1">
                                Subject
                            </label>
                            <input type="text" name="first_reminder_subject" id="first_reminder_subject"
                                   value="{{ old('first_reminder_subject', $emailTemplates['first_reminder_subject']) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="first_reminder_body" class="block text-sm font-medium text-gray-700 mb-1">
                                Body
                            </label>
                            <textarea name="first_reminder_body" id="first_reminder_body" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('first_reminder_body', $emailTemplates['first_reminder_body']) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Second Reminder Template -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Second Reminder Email</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="second_reminder_subject" class="block text-sm font-medium text-gray-700 mb-1">
                                Subject
                            </label>
                            <input type="text" name="second_reminder_subject" id="second_reminder_subject"
                                   value="{{ old('second_reminder_subject', $emailTemplates['second_reminder_subject']) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="second_reminder_body" class="block text-sm font-medium text-gray-700 mb-1">
                                Body
                            </label>
                            <textarea name="second_reminder_body" id="second_reminder_body" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('second_reminder_body', $emailTemplates['second_reminder_body']) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Third Reminder Template -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Third Reminder Email</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label for="third_reminder_subject" class="block text-sm font-medium text-gray-700 mb-1">
                                Subject
                            </label>
                            <input type="text" name="third_reminder_subject" id="third_reminder_subject"
                                   value="{{ old('third_reminder_subject', $emailTemplates['third_reminder_subject']) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="third_reminder_body" class="block text-sm font-medium text-gray-700 mb-1">
                                Body
                            </label>
                            <textarea name="third_reminder_body" id="third_reminder_body" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('third_reminder_body', $emailTemplates['third_reminder_body']) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Email Templates
                </button>
            </div>
        </form>
    </div>
</div>
@endsection