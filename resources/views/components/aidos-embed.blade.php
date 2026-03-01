{{--
    AidOS Campaign Embed Component
    
    Usage:
    <x-aidos-embed src="https://aidos.com/campaign/your-campaign" />
    
    Props:
    - src (required): The AidOS campaign URL to embed
    - height (optional): Custom height class (default: h-[600px] md:h-[700px] lg:h-[800px])
    - title (optional): Iframe title for accessibility (default: "AidOS Campaign")
    - class (optional): Additional CSS classes
--}}

@props(['src', 'height' => 'h-[600px] md:h-[700px] lg:h-[800px]', 'title' => 'AidOS Campaign', 'class' => ''])

<div class="aidos-embed-container relative w-full {{ $height }} rounded-2xl overflow-hidden shadow-lg bg-gray-100 dark:bg-gray-800 {{ $class }}"
    x-data="{ loaded: false }" x-init="setTimeout(() => { $refs.iframe.addEventListener('load', () => loaded = true) }, 100)">
    {{-- Loading Spinner --}}
    <div x-show="!loaded" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0"
        class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-800 z-10">
        <div class="flex flex-col items-center gap-3">
            {{-- Animated Spinner --}}
            <div class="relative">
                <div class="w-12 h-12 rounded-full border-4 border-gray-200 dark:border-gray-600"></div>
                <div
                    class="absolute top-0 left-0 w-12 h-12 rounded-full border-4 border-green-500 border-t-transparent animate-spin">
                </div>
            </div>
            {{-- Loading Text --}}
            <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                Loading campaign...
            </span>
        </div>
    </div>

    {{-- Iframe --}}
    <iframe x-ref="iframe" src="{{ $src }}" title="{{ $title }}" class="w-full h-full border-0"
        loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        sandbox="allow-scripts allow-same-origin allow-popups allow-forms"
        referrerpolicy="strict-origin-when-cross-origin" frameborder="0" scrolling="auto"></iframe>
</div>
