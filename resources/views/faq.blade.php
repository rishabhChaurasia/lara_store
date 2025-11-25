@extends('layouts.app')

@section('title', 'Frequently Asked Questions - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-white dark:bg-dark-bg transition-colors duration-300">
    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden">
        <canvas id="particles-canvas" class="absolute inset-0 -z-10 h-full w-full"></canvas>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative z-10">
            <!-- Label -->
            <div class="flex items-center gap-2 mb-6 justify-center">
                <div class="w-2 h-2 rounded-full bg-black dark:bg-white"></div>
                <span class="text-xs font-bold tracking-widest text-gray-500 dark:text-gray-400">SUPPORT</span>
            </div>

            <!-- Heading -->
            <h1 class="text-4xl md:text-6xl font-bold text-center text-black dark:text-white mb-4 tracking-tight">
                Frequently Asked Questions
            </h1>
            <p class="text-center text-gray-600 dark:text-gray-400 text-lg md:text-xl max-w-2xl mx-auto">
                Everything you need to know about our products and services
            </p>
        </div>
    </div>

    <!-- FAQ Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="space-y-3" x-data="{ 
            activeTab: null,
            toggle(index) {
                this.activeTab = this.activeTab === index ? null : index;
            }
        }">
            @foreach($faqItems as $index => $item)
                @php $itemIndex = $index + 1; @endphp
                <!-- FAQ Item {{ $itemIndex }} -->
                <div class="group rounded-2xl overflow-hidden transition-all duration-300 dark:hover:bg-[#101010]"
                     :class="activeTab === {{ $itemIndex }} ? 'bg-gray-50 dark:bg-[#101010]' : 'bg-white dark:bg-dark-bg'">
                    <button @click="toggle({{ $itemIndex }})" 
                            class="w-full px-6 md:px-8 py-6 flex items-center justify-between text-left group">
                        <span class="text-lg md:text-xl font-semibold text-black dark:text-white pr-8">
                            {{ $item['question'] }}
                        </span>
                        <div class="shrink-0 w-8 h-8 rounded-full bg-black dark:bg-white flex items-center justify-center transition-transform duration-300"
                             :class="activeTab === {{ $itemIndex }} ? 'rotate-180' : ''">
                            <svg class="w-4 h-4 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div x-show="activeTab === {{ $itemIndex }}"
                         x-transition:enter="transition-all ease-out duration-300"
                         x-transition:enter-start="opacity-0 max-h-0"
                         x-transition:enter-end="opacity-100 {{ isset($item['list']) ? 'max-h-[500px]' : 'max-h-96' }}"
                         x-transition:leave="transition-all ease-in duration-200"
                         x-transition:leave-start="opacity-100 {{ isset($item['list']) ? 'max-h-[500px]' : 'max-h-96' }}"
                         x-transition:leave-end="opacity-0 max-h-0"
                         class="px-6 md:px-8 pb-6 overflow-hidden"
                         style="display: none;">
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed {{ isset($item['list']) ? 'mb-4' : '' }}">
                            {{ $item['answer'] }}
                        </p>
                        @if(isset($item['list']))
                            <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                                @foreach($item['list'] as $listItem)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-black dark:text-white mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $listItem }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Still Have Questions CTA -->
        <div class="mt-16 md:mt-24">
            <div class="relative overflow-hidden rounded-3xl dark:bg-[#101010] p-8 md:p-12 text-center">
                <div class="absolute inset-0 hidden dark:block bg-gradient-to-br from-[#1a1a1a] to-[#101010] opacity-90"></div>
                <div class="relative z-10">
                    <h2 class="text-2xl md:text-3xl font-bold text-black dark:text-white mb-4">
                        Still have questions?
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-8 text-lg max-w-xl mx-auto">
                        Can't find the answer you're looking for? Our friendly customer support team is ready to help.
                    </p>
                    <a href="mailto:support{{ '@' }}{{ strtolower(config('app.name')) }}.com" 
                       class="inline-flex items-center gap-2 bg-black dark:bg-white text-white dark:text-black px-8 py-4  font-medium transition-colors duration-200 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
