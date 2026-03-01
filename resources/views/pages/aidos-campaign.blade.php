{{--
    Example: AidOS Campaign Embed Page
    
    This page demonstrates how to use the <x-aidos-embed /> component
    with various configurations and styling options.
--}}

@extends('layouts.layout')

@section('title', 'AidOS Campaign - Every Tree for Hope')

@section('content')
    {{-- Hero Section --}}
    <section class="py-16 px-8 bg-gradient-to-b from-green-50 to-white dark:from-green-900/20 dark:to-background-dark">
        <div class="max-w-6xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('messages.support_our_campaign') }}
            </h1>
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                {{ __('messages.support_our_campaign_desc') }}
            </p>
        </div>
    </section>

    {{-- Main Campaign Embed Section --}}
    <section class="py-8 px-4 md:px-8 lg:px-12">
        <div class="max-w-5xl mx-auto">
            {{-- 
                Basic Usage
                Just pass the AidOS campaign URL to the src prop
            --}}
            <x-aidos-embed src="https://aidos.com/campaign/your-campaign-slug" />
        </div>
    </section>

    {{-- Additional Information Section --}}
    <section class="py-16 px-8 bg-white dark:bg-background-dark">
        <div class="max-w-4xl mx-auto">
            <div class="grid md:grid-cols-2 gap-8">
                {{-- How to Help --}}
                <div class="p-6 rounded-xl bg-gray-50 dark:bg-gray-800">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        {{ __('messages.how_you_can_help') }}
                    </h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('messages.donate_to_initiatives') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('messages.share_campaign') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>{{ __('messages.volunteer_events') }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Impact Stats --}}
                <div class="p-6 rounded-xl bg-green-50 dark:bg-green-900/20">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                        {{ __('messages.your_impact') }}
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">50K+</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Trees Planted</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">2,000+</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Volunteers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">15+</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Provinces</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">100+</div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Events</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Alternative Embed Examples (Hidden by default, for reference) --}}
    <section class="py-8 px-8 bg-gray-100 dark:bg-gray-900 hidden">
        <div class="max-w-6xl mx-auto space-y-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                Component Usage Examples
            </h2>

            {{-- Example 1: Custom Height --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    Custom Height
                </h3>
                <x-aidos-embed src="https://aidos.com/campaign/example" height="h-[400px]" />
            </div>

            {{-- Example 2: Custom Title --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    Custom Title (Accessibility)
                </h3>
                <x-aidos-embed src="https://aidos.com/campaign/example" title="Tree Planting Fundraiser 2024" />
            </div>

            {{-- Example 3: Additional Classes --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">
                    With Additional Styling
                </h3>
                <x-aidos-embed src="https://aidos.com/campaign/example" class="border-2 border-green-500 shadow-xl" />
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- Alpine.js is required for the loading spinner functionality --}}
    @if (!app()->environment('production'))
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif
@endpush
