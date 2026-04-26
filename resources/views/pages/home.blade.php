@extends('layouts.layout')
@section('title', 'Home Page')
@section('content')
    <!-- ===== HERO SECTION — Editorial / Magazine Style ===== -->
    @php
        $totalEventTrees = App\Models\Event::sum('trees_planted');
        $totalTrees = $totalEventTrees;
        $co2Tons = $totalTrees * 0.021;
        $eventCount = App\Models\Event::count();
    @endphp

    <section class="relative w-full overflow-hidden" style="background: #fafaf5;">

        <!-- Subtle paper grain -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
            style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;">
        </div>

        <!-- Top editorial bar -->
        <div class="relative z-10 border-b border-deep-green/10">
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between text-[10px] font-bold tracking-[0.25em] uppercase">
                <div class="flex items-center gap-2 text-deep-green">
                    <span class="material-symbols-outlined text-vibrant-lime text-sm">eco</span>
                    <span>{{ __('messages.hero_issue_no') }}</span>
                </div>
                <div class="hidden md:flex items-center gap-6 text-charcoal/40">
                    <span>{{ __('messages.kabul_afghanistan') }}</span>
                </div>
                <div class="flex items-center gap-2 text-charcoal/40">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-primary opacity-60 animate-ping"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-primary"></span>
                    </span>
                    <span>{{ __('messages.hero_live') }}</span>
                </div>
            </div>
        </div>

        <!-- Main editorial content -->
        <div class="relative z-10 max-w-7xl mx-auto px-6 py-12 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                <!-- LEFT COLUMN — Text/typography 7/12 -->
                <div class="lg:col-span-7 relative hero-fade-in">

                    <!-- Issue meta line -->
                    <div class="flex items-center gap-3 mb-8">
                        <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                        <span class="text-vibrant-lime text-[10px] font-bold uppercase tracking-[0.4em]">
                            {{ __('messages.global_reforestation_mission') }}
                        </span>
                    </div>

                    <!-- Massive editorial headline -->
                    <h1 class="font-serif font-bold text-deep-green leading-[0.92] tracking-tight mb-8"
                        style="font-size: clamp(3rem, 8vw, 7.5rem);">
                        {!! __('messages.rooting_for') !!}<br />
                        <span class="italic font-black relative inline-block">
                            <span class="relative z-10">{{ __('messages.the_future') }}</span>
                            <!-- Hand-drawn underline -->
                            <svg class="absolute -bottom-3 left-0 w-full h-3 z-0" preserveAspectRatio="none" viewBox="0 0 200 10">
                                <path d="M2,8 Q50,2 100,5 T198,4" fill="none" stroke="#84cc16" stroke-width="3" stroke-linecap="round" />
                            </svg>
                        </span>
                    </h1>

                    <!-- Lead paragraph -->
                    <p class="text-charcoal/70 text-lg md:text-xl font-medium leading-[1.6] max-w-xl mb-10 hero-fade-in" style="animation-delay: 0.15s;">
                        {{ __('messages.hero_description') }}
                    </p>

                    <!-- Action row -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-10 hero-fade-in" style="animation-delay: 0.25s;">
                        <a href="#donation-section"
                            class="group inline-flex items-center gap-2 px-7 py-4 bg-deep-green text-white font-extrabold text-sm tracking-wide uppercase rounded-full shadow-lg shadow-deep-green/20 hover:bg-deep-green/90 hover:-translate-y-0.5 transition-all">
                            <span class="material-symbols-outlined text-vibrant-lime text-base">park</span>
                            {{ __('messages.plant_a_tree_today') }}
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                        <a href="#our-works"
                            class="inline-flex items-center gap-2 text-deep-green text-sm font-bold uppercase tracking-wide hover:gap-3 transition-all border-b-2 border-deep-green/20 hover:border-vibrant-lime pb-1">
                            {{ __('messages.view_our_projects') }}
                            <span class="material-symbols-outlined text-base">north_east</span>
                        </a>
                    </div>

                    <!-- Quote / pullquote -->
                    <div class="hidden md:block relative pl-8 max-w-md hero-fade-in" style="animation-delay: 0.4s;">
                        <span class="absolute left-0 top-0 text-6xl font-serif text-vibrant-lime/40 leading-none select-none">"</span>
                        <p class="text-charcoal/60 text-sm italic leading-relaxed">
                            {!! __('messages.hero_pullquote') !!}
                        </p>
                        <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest mt-3">
                            — {{ __('messages.hero_pullquote_author') }}
                        </p>
                    </div>
                </div>

                <!-- RIGHT COLUMN — Photo card 5/12 -->
                <div class="lg:col-span-5 relative hero-fade-in" style="animation-delay: 0.2s;">
                    <div class="relative">
                        <!-- Decorative back card -->
                        <div class="absolute -top-4 -right-4 w-full h-full rounded-2xl bg-vibrant-lime/20 hidden md:block"></div>
                        <div class="absolute -bottom-4 -left-4 w-full h-full rounded-2xl border-2 border-deep-green/15 hidden md:block"></div>

                        <!-- Main photo card -->
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl group">
                            <img src="{{ asset('images/1.jpeg') }}"
                                alt="Every Tree for Hope team"
                                class="w-full h-[420px] sm:h-[480px] lg:h-[560px] object-cover group-hover:scale-105 transition-transform duration-[1500ms]" />

                            <!-- Photo bottom gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                            <!-- Photo caption (top-left) -->
                            <div class="absolute top-5 left-5">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/90 backdrop-blur-md">
                                    <span class="material-symbols-outlined text-deep-green text-sm">camera_alt</span>
                                    <!-- <span class="text-deep-green text-[10px] font-extrabold uppercase tracking-widest">Field Day · Kabul</span> -->
                                </div>
                            </div>

                            <!-- Photo caption (bottom) -->
                            <!-- <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                                <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-vibrant-lime mb-1">Photograph No. 01</p>
                                <p class="text-sm font-medium">The team that planted hope into the hills of Haji Nabi</p>
                            </div> -->
                        </div>

                        <!-- Floating stat badge — top-right -->
                        <div class="absolute -top-5 -right-5 sm:-right-8 z-20 bg-white rounded-2xl shadow-xl px-5 py-3 border border-deep-green/10 hidden sm:flex flex-col items-end"
                            style="animation: float 6s ease-in-out infinite;">
                            <p class="text-deep-green text-2xl font-black tabular-nums leading-none">
                                {{ number_format($totalTrees) }}
                            </p>
                            <p class="text-charcoal/50 text-[9px] font-bold uppercase tracking-widest mt-1">
                                {{ __('messages.trees_planted') }}
                            </p>
                        </div>

                        <!-- Floating signature — bottom-left -->
                        <div class="absolute -bottom-6 -left-4 sm:-left-8 z-20 bg-deep-green rounded-2xl shadow-xl px-5 py-3 hidden sm:block"
                            style="animation: float 7s ease-in-out 1s infinite reverse;">
                            <p class="text-vibrant-lime text-[9px] font-bold uppercase tracking-widest">{{ __('messages.hero_est_year') }}</p>
                            <p class="text-white text-base font-serif italic leading-tight">{!! __('messages.hero_brand_signature') !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom impact ticker bar -->
        <div class="relative z-10 border-t border-deep-green/10 bg-deep-green/[0.02]">
            <div class="max-w-7xl mx-auto px-6 py-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-0 md:divide-x md:divide-deep-green/10 text-center md:text-left">
                    <!-- Stat 1 -->
                    <div class="md:px-6 first:md:pl-0 flex md:items-center gap-3 justify-center md:justify-start">
                        <span class="material-symbols-outlined text-vibrant-lime text-2xl">park</span>
                        <div>
                            <p class="text-deep-green text-xl md:text-2xl font-black tabular-nums leading-none">
                                {{ number_format($totalTrees) }}
                            </p>
                            <p class="text-charcoal/40 text-[9px] font-bold uppercase tracking-widest mt-1">
                                {{ __('messages.trees_planted') }}
                            </p>
                        </div>
                    </div>
                    <!-- Stat 2 -->
                    <div class="md:px-6 flex md:items-center gap-3 justify-center md:justify-start">
                        <span class="material-symbols-outlined text-vibrant-lime text-2xl">co2</span>
                        <div>
                            <p class="text-deep-green text-xl md:text-2xl font-black tabular-nums leading-none">
                                {{ $co2Tons >= 10 ? number_format($co2Tons) : number_format($co2Tons, 1) }}
                            </p>
                            <p class="text-charcoal/40 text-[9px] font-bold uppercase tracking-widest mt-1">
                                {{ __('messages.tons_co2_offset') }}
                            </p>
                        </div>
                    </div>
                    <!-- Stat 3 -->
                    <div class="md:px-6 flex md:items-center gap-3 justify-center md:justify-start">
                        <span class="material-symbols-outlined text-vibrant-lime text-2xl">groups</span>
                        <div>
                            <p class="text-deep-green text-xl md:text-2xl font-black tabular-nums leading-none">30+</p>
                            <p class="text-charcoal/40 text-[9px] font-bold uppercase tracking-widest mt-1">
                                {{ __('messages.volunteers') }}
                            </p>
                        </div>
                    </div>
                    <!-- Stat 4 -->
                    <div class="md:px-6 flex md:items-center gap-3 justify-center md:justify-start">
                        <span class="material-symbols-outlined text-vibrant-lime text-2xl">flag</span>
                        <div>
                            <p class="text-deep-green text-xl md:text-2xl font-black tabular-nums leading-none">
                                {{ number_format($eventCount) }}
                            </p>
                            <p class="text-charcoal/40 text-[9px] font-bold uppercase tracking-widest mt-1">
                                {{ __('messages.events_completed') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ===== END HERO SECTION ===== -->
    <section class="relative bg-white pb-18 pt-28 px-6 botanical-bg overflow-hidden" id="media-stories">
        <div class="absolute top-0 right-0 w-96 h-96 opacity-[0.03] pointer-events-none">
            <span class="material-symbols-outlined text-[300px] text-deep-green">potted_plant</span>
         </div>
        <div class="absolute bottom-0 left-0 w-96 h-96 opacity-[0.03] pointer-events-none translate-y-20 -translate-x-20">
            <span class="material-symbols-outlined text-[350px] text-gold-accent">forest</span>
        </div>
        <div class="max-w-7xl mx-auto">
            <div class="text-center space-y-6 mb-20 max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-3">
                    <div class="h-[1px] w-8 bg-gold-accent"></div>
                    <span
                        class="text-gold-accent font-bold tracking-[0.3em] text-xs uppercase">{{ __('messages.documenting_the_change') }}</span>
                    <div class="h-[1px] w-8 bg-gold-accent"></div>
                </div>
                <h2 class="text-5xl md:text-6xl font-serif font-bold text-deep-green leading-tight">
                    {!! __('messages.watch_our_mission_in_action') !!}
                </h2>
                <p class="text-charcoal/70 text-lg leading-relaxed font-medium">
                    {{ __('messages.media_description') }}
                </p>
            </div>
            <div class="relative">
                <!-- Scroll buttons -->
                <button id="scrollLeft"
                    class="absolute left-0 top-1/2 -translate-y-1/2 z-20 bg-white/90 backdrop-blur-sm rounded-full p-3 shadow-lg hover:bg-white transition-all -translate-x-4">
                    <span class="material-symbols-outlined text-deep-green text-2xl">chevron_left</span>
                </button>
                <button id="scrollRight"
                    class="absolute right-0 top-1/2 -translate-y-1/2 z-20 bg-white/90 backdrop-blur-sm rounded-full p-3 shadow-lg hover:bg-white transition-all translate-x-4">
                    <span class="material-symbols-outlined text-deep-green text-2xl">chevron_right</span>
                </button>

                <div class="relative">
                    <div id="videoContainer" class="flex gap-6 overflow-x-hidden pb-8 px-4 -mx-4">
                        @php
                            $mediaItems = \App\Models\Media::active()->ordered()->take(10)->get();
                        @endphp
                        @forelse($mediaItems as $index => $media)
                            @php
                                $videoId = $media->youtube_video_id;
                                $isShort = $media->is_short;
                            @endphp
                            @if ($videoId)
                                <div class="flex-shrink-0 w-80 group cursor-pointer"
                                    data-video-id="{{ $videoId }}"
                                    data-is-short="{{ $isShort ? 'true' : 'false' }}"
                                    data-title="{{ $media->title }}"
                                    data-date="{{ $media->date->format('M d, Y') }}">
                                    <div
                                        class="bg-white rounded-2xl overflow-hidden shadow-lg border border-white p-2 transition-all duration-300 group-hover:-translate-y-2 group-hover:shadow-xl">
                                        <div class="relative aspect-[16/10] rounded-xl overflow-hidden mb-4">
                                            <img alt="{{ $media->title }}" class="w-full h-full object-cover"
                                                src="{{ $media->thumbnail_url ?? 'https://via.placeholder.com/320x180/1a4a2e/ffffff?text=Video' }}" />
                                            <div
                                                class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <span class="material-symbols-outlined text-white text-4xl">play_circle</span>
                                            </div>
                                            @if ($isShort)
                                                <div class="absolute top-2 left-2 flex items-center gap-1 bg-black/70 backdrop-blur-sm px-2 py-0.5 rounded-full">
                                                    <svg class="w-3 h-3 fill-red-500" viewBox="0 0 24 24"><path d="M10 9.8 15.2 12 10 14.2V9.8zM17 3H7C4.8 3 3 4.8 3 7v10c0 2.2 1.8 4 4 4h10c2.2 0 4-1.8 4-4V7c0-2.2-1.8-4-4-4z"/></svg>
                                                    <span class="text-white text-[9px] font-bold uppercase tracking-wide">Shorts</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="px-2 pb-2">
                                            <span class="text-[10px] font-bold text-gold-accent uppercase tracking-widest">
                                                {{ $media->date->format('M d, Y') }}
                                            </span>
                                            <h4 class="text-deep-green font-bold text-lg mt-1 group-hover:text-primary transition-colors">
                                                {{ Str::limit($media->title, 30) }}
                                            </h4>
                                            @if ($media->description)
                                                <p class="text-charcoal/60 text-sm mt-2 line-clamp-2">
                                                    {{ Str::limit($media->description, 80) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="flex-shrink-0 w-80">
                                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-white p-2">
                                    <div
                                        class="relative aspect-[16/10] rounded-xl overflow-hidden mb-4 bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-400 text-center">No media items available</span>
                                    </div>
                                    <div class="px-2 pb-2">
                                        <h4 class="text-deep-green font-bold text-lg">
                                            {{ __('messages.no_media_available') }}</h4>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Modal -->
    <div id="videoModal"
        class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div id="videoModalInner" class="relative w-full max-w-4xl transition-all duration-300">
            <button id="closeModal" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                <span class="material-symbols-outlined text-4xl">close</span>
            </button>
            <div id="videoModalAspect" class="relative aspect-video bg-black rounded-lg overflow-hidden">
                <iframe id="modalIframe" class="w-full h-full" frameborder="0" allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div>
        </div>
    </div>

    <main class="flex-grow">
        <section class="relative bg-white py-32 md:py-48 px-6 overflow-hidden" id="mission-vision">
            <span
                class="material-symbols-outlined absolute top-20 left-[10%] text-primary/20 text-5xl animate-pulse">eco</span>
            <span
                class="material-symbols-outlined absolute top-1/2 right-[5%] text-vibrant-lime/20 text-4xl animate-pulse">spa</span>
            <span
                class="material-symbols-outlined absolute bottom-20 left-[15%] text-primary/10 text-6xl animate-pulse">psychology_alt</span>
            <div class="max-w-7xl mx-auto relative">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <div class="lg:col-span-7 relative">
                        <!-- Vertical stacked images with style -->
                        <div class="space-y-6">
                            <!-- Top image -->
                            <div class="relative group">
                                <div
                                    class="rounded-3xl overflow-hidden shadow-2xl border-[8px] border-white transition-all duration-700 hover:scale-[1.02] hover:shadow-3xl">
                                    <img alt="Wide forest vision" class="w-full h-[300px] md:h-[400px] object-cover"
                                        src="{{ asset('images/16.jpeg') }}" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-deep-green/20 to-transparent"></div>
                                </div>
                                <div
                                    class="absolute -bottom-3 -right-3 w-20 h-20 border-r-2 border-b-2 border-vibrant-lime/40 rounded-br-3xl">
                                </div>
                            </div>

                            <!-- Bottom image with content -->
                            <div class="relative group">
                                <div
                                    class="rounded-3xl overflow-hidden shadow-2xl border-[8px] border-white transition-all duration-700 hover:scale-[1.02] hover:shadow-3xl">
                                    <img alt="Sprout mission" class="w-full h-[250px] md:h-[350px] object-cover"
                                        src="{{ asset('images/22.jpeg') }}" />
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/30 to-transparent">
                                    </div>
                                    <div class="absolute bottom-0 left-0 w-full p-6 md:p-8">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span
                                                class="material-symbols-outlined text-white bg-primary/90 p-2 rounded-full">favorite</span>
                                            <h3 class="font-serif text-2xl md:text-3xl font-bold text-white">
                                                {{ __('messages.mission_label') }}</h3>
                                        </div>
                                        <p class="text-white/90 font-light leading-relaxed text-sm md:text-base">
                                            {{ __('messages.mission_text') }}
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="absolute -top-3 -left-3 w-20 h-20 border-l-2 border-t-2 border-primary/40 rounded-tl-3xl">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-5 space-y-12 lg:pl-12">
                        <div class="space-y-4">
                            <h4 class="text-primary font-bold tracking-[0.3em] text-xs uppercase">
                                {{ __('messages.our_core_purpose') }}
                            </h4>
                            <div class="relative">
                                <h2 class="text-5xl md:text-7xl font-serif font-light text-deep-green leading-tight">
                                    {!! __('messages.growing_intentionally') !!}
                                </h2>
                                <div class="w-24 h-[1px] bg-vibrant-lime mt-8"></div>
                            </div>
                        </div>
                        <div class="space-y-10">
                            <div class="group">
                                <div class="flex items-center gap-6 mb-4">
                                    <span
                                        class="text-vibrant-lime/40 text-4xl font-serif italic group-hover:text-vibrant-lime transition-colors">01.</span>
                                    <h5 class="text-xl font-bold text-deep-green uppercase tracking-wider">
                                        {{ __('messages.the_mission') }}</h5>
                                </div>
                                <p
                                    class="text-charcoal/60 text-lg font-light leading-relaxed {{ $is_rtl ? 'pr-16' : 'pl-16' }}">
                                    {{ __('messages.mission_description') }}
                                </p>
                            </div>
                            <div class="group">
                                <div class="flex items-center gap-6 mb-4">
                                    <span
                                        class="text-vibrant-lime/40 text-4xl font-serif italic group-hover:text-vibrant-lime transition-colors">02.</span>
                                    <h5 class="text-xl font-bold text-deep-green uppercase tracking-wider">
                                        {{ __('messages.the_vision') }}</h5>
                                </div>
                                <p
                                    class="text-charcoal/60 text-lg font-light leading-relaxed {{ $is_rtl ? 'pr-16' : 'pl-16' }}">
                                    {{ __('messages.vision_description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 sm:py-24 bg-background-light/60">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="relative overflow-hidden rounded-3xl border border-gray-200/70 dark:border-gray-800 bg-white/80 dark:bg-background-dark/80 backdrop-blur-sm shadow-sm">
                    <div
                        class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 rounded-full bg-primary/15 blur-3xl">
                    </div>
                    <div
                        class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-emerald-500/10 blur-3xl">
                    </div>

                    <div class="relative p-8 sm:p-12">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                            <div class="max-w-3xl">
                                <p
                                    class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-sm font-semibold text-primary">
                                    {{ __('messages.our_mission') }}
                                </p>
                                <h2 class="mt-4 text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white">
                                    {{ __('messages.turning_barren_land_into_green_hope') }}
                                </h2>
                                <p class="mt-4 text-lg text-gray-700 dark:text-gray-300">
                                    {{ __('messages.about_description') }}
                                </p>
                                <div
                                    class="mt-6 rounded-2xl bg-background-light dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800">
                                    <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                                        {{ __('messages.transform_barren_land') }}
                                    </p>
                                </div>
                                <p class="mt-6 text-lg text-gray-700 dark:text-gray-300">
                                    {{ __('messages.community_description') }}
                                </p>
                            </div>

                            <div class="relative">
                                <div
                                    class="absolute -inset-2 rounded-3xl bg-gradient-to-br from-primary/20 via-emerald-500/10 to-transparent blur-2xl">
                                </div>
                                <div
                                    class="relative overflow-hidden rounded-3xl border border-gray-200/70 dark:border-gray-800 bg-white/60 dark:bg-background-dark/60 shadow-sm">
                                    <div class="aspect-[4/3]">
                                        <img alt="Planting drought-tolerant trees in the hills surrounding Kabul"
                                            class="h-full w-full object-cover" src="{{ asset('images/4.jpeg') }}" />
                                    </div>
                                    <div class="p-5 sm:p-6">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ __('messages.kabul_afghanistan') }}
                                        </p>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('messages.small_weekly_action') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                class="rounded-2xl bg-white dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 3v18m9-9H3" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ __('messages.six_trees_every_week') }}</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('messages.consistent_action') }}</p>
                            </div>
                            <div
                                class="rounded-2xl bg-white dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ __('messages.kabuls_hills') }}</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('messages.restoring_land') }}</p>
                            </div>
                            <div
                                class="rounded-2xl bg-white dark:bg-background-dark p-6 border border-gray-200/70 dark:border-gray-800 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V6m0 12v2" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                        {{ __('messages.drought_tolerant') }}</h3>
                                </div>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('messages.choosing_trees') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== OUR WORKS IMAGE GALLERY SECTION ===== -->
        <section class="relative py-24 md:py-36 px-6 overflow-hidden" id="our-works"
            style="background: linear-gradient(160deg, #f0fdf4 0%, #ffffff 40%, #f7fce8 100%);">

            <!-- Decorative background blobs -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full opacity-[0.06]"
                    style="background: radial-gradient(circle, #22c55e, transparent 70%)"></div>
                <div class="absolute -bottom-32 -right-32 w-[600px] h-[600px] rounded-full opacity-[0.05]"
                    style="background: radial-gradient(circle, #84cc16, transparent 70%)"></div>
                <span
                    class="material-symbols-outlined absolute top-16 right-[4%] text-primary/[0.06] text-[280px] select-none leading-none">forest</span>
                <span
                    class="material-symbols-outlined absolute bottom-16 left-[2%] text-vibrant-lime/[0.06] text-[200px] select-none leading-none">park</span>
            </div>

            <!-- Top decorative line -->
            <div class="absolute top-0 left-0 w-full h-[2px]"
                style="background: linear-gradient(90deg, transparent, #84cc16 30%, #22c55e 50%, #84cc16 70%, transparent)">
            </div>

            <div class="max-w-7xl mx-auto relative z-10">

                <!-- Section Header -->
                <div class="text-center space-y-6 mb-20 max-w-3xl mx-auto">
                    <div class="inline-flex items-center gap-3">
                        <div class="h-[1px] w-10 bg-gradient-to-r from-transparent to-vibrant-lime"></div>
                        <div
                            class="flex items-center gap-2 bg-deep-green/5 border border-deep-green/10 px-4 py-2 rounded-full">
                            <span class="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                            <span
                                class="text-deep-green font-bold tracking-[0.25em] text-[10px] uppercase">{{ __('messages.gallery_section_badge') }}</span>
                        </div>
                        <div class="h-[1px] w-10 bg-gradient-to-l from-transparent to-vibrant-lime"></div>
                    </div>

                    <h2 class="text-5xl md:text-6xl lg:text-7xl font-serif font-bold text-deep-green leading-tight">
                        {{ __('messages.gallery_section_title_1') }}<br />
                        <span
                            class="text-vibrant-lime italic font-black">{{ __('messages.gallery_section_title_2') }}</span>
                    </h2>

                    <p class="text-charcoal/70 text-lg leading-relaxed font-medium max-w-2xl mx-auto">
                        {{ __('messages.gallery_section_description') }}
                    </p>
                </div>

                <!-- Masonry-style Image Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4" style="grid-auto-rows: 200px;">

                    <!-- HERO image: 2 cols × 2 rows -->
                    <div class="col-span-2 row-span-2 group relative overflow-hidden rounded-3xl shadow-2xl cursor-pointer gallery-item"
                        onclick="openLightbox('{{ asset('images/1.jpeg') }}', '{{ __('messages.gallery_hero_title_line1') }} {{ __('messages.gallery_hero_title_line2') }}', '{{ __('messages.gallery_hero_desc') }}')">
                        <img src="{{ asset('images/1.jpeg') }}" alt="Restoring Life"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-all duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.85) 0%, rgba(6,78,59,0.3) 50%, transparent 100%)">
                        </div>
                        <!-- Always-visible gradient at bottom -->
                        <div class="absolute bottom-0 left-0 w-full h-40"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.6), transparent)"></div>
                        <!-- Label -->
                        <div class="absolute bottom-0 left-0 p-6 md:p-8">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="flex h-2 w-2 rounded-full bg-vibrant-lime animate-pulse"></span>
                                <span
                                    class="text-vibrant-lime text-[10px] font-bold uppercase tracking-[0.25em]">{{ __('messages.gallery_featured_moment') }}</span>
                            </div>
                            <h3 class="text-white font-serif text-2xl md:text-3xl font-bold leading-snug drop-shadow-lg">
                                {{ __('messages.gallery_hero_title_line1') }}<br />{{ __('messages.gallery_hero_title_line2') }}
                            </h3>
                        </div>
                        <!-- Hover expand icon -->
                        <div
                            class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500 border border-white/30">
                            <span class="material-symbols-outlined text-white text-base">open_in_full</span>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item"
                        onclick="openLightbox('{{ asset('images/5.jpeg') }}', '{{ __('messages.gallery_img_community') }}', '{{ __('messages.gallery_img_community_desc') }}')">
                        <img src="{{ asset('images/5.jpeg') }}" alt="Community Planting"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">{{ __('messages.gallery_img_community') }}
                            </p>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item"
                        onclick="openLightbox('{{ asset('images/7.jpeg') }}', '{{ __('messages.gallery_img_growth') }}', '{{ __('messages.gallery_img_growth_desc') }}')">
                        <img src="{{ asset('images/7.jpeg') }}" alt="Seedling Care"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">{{ __('messages.gallery_img_growth') }}
                            </p>
                        </div>
                    </div>

                    <!-- Image: 2 cols × 1 row (wide) -->
                    <div class="col-span-2 group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item"
                        onclick="openLightbox('{{ asset('images/27.jpeg') }}', '{{ __('messages.gallery_img_landscape') }}', '{{ __('messages.gallery_img_landscape_desc') }}')">
                        <img src="{{ asset('images/27.jpeg') }}" alt="Kabul Hills"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 60%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-5 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <span
                                class="text-vibrant-lime text-[10px] font-bold uppercase tracking-widest">{{ __('messages.gallery_img_kabul_hills') }}</span>
                            <p class="text-white font-serif font-bold mt-1">{{ __('messages.gallery_img_landscape') }}</p>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item"
                        onclick="openLightbox('{{ asset('images/3.jpeg') }}', '{{ __('messages.gallery_img_together') }}', '{{ __('messages.gallery_img_together_desc') }}')">
                        <img src="{{ asset('images/3.jpeg') }}" alt="Teamwork"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">{{ __('messages.gallery_img_together') }}
                            </p>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item"
                        onclick="openLightbox('{{ asset('images/14.jpeg') }}', '{{ __('messages.gallery_img_roots') }}', '{{ __('messages.gallery_img_roots_desc') }}')">
                        <img src="{{ asset('images/14.jpeg') }}" alt="Planting Roots"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">{{ __('messages.gallery_img_roots') }}</p>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item"
                        onclick="openLightbox('{{ asset('images/38.jpeg') }}', '{{ __('messages.gallery_img_seeds') }}', '{{ __('messages.gallery_img_seeds_desc') }}')">
                        <img src="{{ asset('images/38.jpeg') }}" alt="Young Trees"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">{{ __('messages.gallery_img_seeds') }}</p>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item"
                        onclick="openLightbox('{{ asset('images/21.jpeg') }}', '{{ __('messages.gallery_img_united') }}', '{{ __('messages.gallery_img_united_desc') }}')">
                        <img src="{{ asset('images/21.jpeg') }}" alt="Collaboration"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">{{ __('messages.gallery_img_united') }}
                            </p>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item hidden md:block"
                        onclick="openLightbox('{{ asset('images/11.jpeg') }}', '{{ __('messages.gallery_img_beginnings') }}', '{{ __('messages.gallery_img_beginnings_desc') }}')">
                        <img src="{{ asset('images/11.jpeg') }}" alt="Green Beginnings"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">
                                {{ __('messages.gallery_img_beginnings') }}</p>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item hidden md:block"
                        onclick="openLightbox('{{ asset('images/23.jpeg') }}', '{{ __('messages.gallery_img_hands') }}', '{{ __('messages.gallery_img_hands_desc') }}')">
                        <img src="{{ asset('images/23.jpeg') }}" alt="A Forest of Hands"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">{{ __('messages.gallery_img_hands') }}
                            </p>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item hidden md:block"
                        onclick="openLightbox('{{ asset('images/22.jpeg') }}', '{{ __('messages.gallery_img_hands') }}', '{{ __('messages.gallery_img_hands_desc') }}')">
                        <img src="{{ asset('images/22.jpeg') }}" alt="A Forest of Hands"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">{{ __('messages.gallery_img_hands') }}
                            </p>
                        </div>
                    </div>

                    <!-- Image: 1 col × 1 row -->
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg cursor-pointer gallery-item hidden md:block"
                        onclick="openLightbox('{{ asset('images/18.jpeg') }}', '{{ __('messages.gallery_img_hands') }}', '{{ __('messages.gallery_img_hands_desc') }}')">
                        <img src="{{ asset('images/18.jpeg') }}" alt="A Forest of Hands"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                            style="background: linear-gradient(to top, rgba(6,78,59,0.8), transparent 70%)"></div>
                        <div
                            class="absolute bottom-0 left-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                            <p class="text-white font-bold text-sm drop-shadow">{{ __('messages.gallery_img_hands') }}
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Inspiring quote + CTA -->
                <div class="mt-20 flex flex-col items-center gap-8 text-center">
                    <div class="relative max-w-2xl">
                        <span
                            class="absolute -top-6 -left-4 text-6xl text-vibrant-lime/20 font-serif leading-none select-none">"</span>
                        <p class="text-charcoal/60 text-lg font-medium italic leading-relaxed relative z-10 px-6">
                            {{ __('messages.gallery_quote') }}
                        </p>
                        <span
                            class="absolute -bottom-8 -right-4 text-6xl text-vibrant-lime/20 font-serif leading-none select-none">"</span>
                        <p class="text-charcoal/40 text-xs font-bold uppercase tracking-widest mt-4">
                            {{ __('messages.gallery_quote_attribution') }}</p>
                    </div>

                    <a href="{{ route('gallery') }}"
                        class="group inline-flex items-center gap-3 px-8 py-4 bg-deep-green text-white font-bold rounded-full hover:bg-deep-green/90 transition-all shadow-xl shadow-deep-green/20 hover:shadow-2xl hover:shadow-deep-green/30 hover:-translate-y-0.5">
                        <span class="material-symbols-outlined text-vibrant-lime text-xl">photo_library</span>
                        {{ __('messages.gallery_view_full') }}
                        <span
                            class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Lightbox Modal -->
        <div id="galleryLightbox"
            class="fixed inset-0 z-[100] hidden bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
            onclick="closeLightbox(event)">
            <div class="relative w-full max-w-5xl mx-auto" onclick="event.stopPropagation()">
                <!-- Close button -->
                <button onclick="closeLightboxBtn()"
                    class="absolute -top-14 right-0 text-white/70 hover:text-white transition-colors flex items-center gap-2">
                    <span class="text-sm font-medium">Close</span>
                    <span class="material-symbols-outlined text-3xl">close</span>
                </button>
                <!-- Image -->
                <div class="rounded-2xl overflow-hidden shadow-2xl">
                    <img id="lightboxImage" src="" alt=""
                        class="w-full max-h-[70vh] object-contain bg-black" />
                </div>
                <!-- Caption -->
                <div class="mt-4 text-center space-y-2 px-4">
                    <h3 id="lightboxTitle" class="text-white font-serif text-xl font-bold"></h3>
                    <p id="lightboxDesc" class="text-white/60 text-sm font-medium leading-relaxed max-w-2xl mx-auto">
                    </p>
                </div>
            </div>
        </div>
        <!-- ===== END OUR WORKS GALLERY SECTION ===== -->

        <section class="relative py-24 md:py-24 px-6 overflow-hidden bg-background-light" id="donation-section">
            <div class="absolute inset-0 z-0">
                <div class="w-full h-full bg-cover bg-center"
                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs');">
                </div>
                <div class="absolute inset-0 bg-white/95"></div>
            </div>
            <div class="relative max-w-4xl mx-auto text-center mb-20">
                <h4 class="text-sage-green font-bold tracking-[0.4em] text-xs uppercase mb-4">
                    {{ __('messages.support_the_canopy') }}</h4>
                <h2 class="text-5xl md:text-7xl font-serif text-deep-green leading-tight">
                    {!! __('messages.your_contribution_roots_our_change') !!}
                </h2>
                <div class="w-24 h-[1px] bg-gold-accent mx-auto mt-8 opacity-40"></div>
            </div>

            <div class="relative grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                <span
                    class="material-symbols-outlined absolute -top-12 -left-12 text-sage-green/20 text-7xl floating parallax-leaf">eco</span>
                <span
                    class="material-symbols-outlined absolute -bottom-12 -right-12 text-gold-accent/20 text-8xl floating parallax-leaf"
                    style="animation-delay: 2s;">spa</span>
                <span
                    class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-primary/5 text-[300px] parallax-leaf">potted_plant</span>

                <div
                    class="glass-panel rounded-[2rem] p-10 flex flex-col h-full relative group hover:-translate-y-2 transition-transform duration-500 overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-10">
                        <span class="material-symbols-outlined text-6xl text-gold-accent">location_on</span>
                    </div>
                    <div class="mb-8">
                        <span class="material-symbols-outlined text-gold-accent text-3xl mb-4">distance</span>
                        <h3 class="font-serif text-2xl font-bold text-deep-green mb-2">
                            {{ __('messages.local_handover') }}
                        </h3>
                        <p class="text-sage-green text-xs font-bold uppercase tracking-widest">
                            {{ __('messages.kabul_afghanistan_location') }}</p>
                    </div>
                    <div class="gold-line-art mb-8"></div>
                    <div class="space-y-6 flex-grow">
                        <div>
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                                {{ __('messages.primary_location') }}</p>
                            <p class="font-serif text-lg text-charcoal/80">{{ __('messages.haidari_market') }}</p>
                            <p class="text-sm text-charcoal/60 italic">{{ __('messages.district_4_kabul') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                                {{ __('messages.exchange_office') }}</p>
                            <p class="font-serif text-lg text-charcoal/80">{{ __('messages.saray_shazada') }}</p>
                            <p class="text-sm text-charcoal/60">{{ __('messages.main_financial_hub') }}</p>
                        </div>
                    </div>
                    <div class="mt-10 pt-6 border-t border-charcoal/5">
                        <button
                            class="w-full py-3 rounded-full border border-gold-accent/30 text-gold-accent text-sm font-bold hover:bg-gold-accent hover:text-white transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">map</span>
                            {{ __('messages.get_directions') }}
                        </button>
                    </div>
                </div>

                <div
                    class="glass-panel rounded-[2rem] p-10 flex flex-col h-full relative group hover:-translate-y-2 transition-transform duration-500 overflow-hidden border-sage-green/20">
                    <div class="absolute top-0 right-0 p-6 opacity-10">
                        <span class="material-symbols-outlined text-6xl text-sage-green">account_balance_wallet</span>
                    </div>
                    <div class="mb-8">
                        <span class="material-symbols-outlined text-sage-green text-3xl mb-4">payments</span>
                        <h3 class="font-serif text-2xl font-bold text-deep-green mb-2">{{ __('messages.moneygram_wu') }}
                        </h3>
                        <p class="text-sage-green text-xs font-bold uppercase tracking-widest">
                            {{ __('messages.global_remittance') }}</p>
                    </div>
                    <div class="gold-line-art mb-8"></div>
                    <div class="space-y-8 flex-grow">
                        <div>
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-3">
                                {{ __('messages.receiver_name') }}</p>
                            <p class="font-serif text-3xl font-bold text-deep-green leading-tight">
                                {{ __('messages.mohammad_iqbal_alimyar') }}</p>
                            <p class="text-xs text-sage-green mt-2 font-medium">{{ __('messages.verify_spelling') }}</p>
                        </div>
                        <div class="bg-sage-green/5 p-4 rounded-xl border border-sage-green/10">
                            <p class="text-[10px] text-sage-green font-bold uppercase tracking-widest mb-2">
                                {{ __('messages.instructions') }}
                            </p>
                            <p class="text-xs text-charcoal/60 leading-relaxed">{{ __('messages.share_mtcn') }}</p>
                        </div>
                    </div>
                    <div class="mt-10 pt-6">
                        <button
                            class="w-full py-4 rounded-full bg-deep-green text-white text-sm font-bold hover:bg-deep-green/90 transition-all shadow-lg shadow-deep-green/20 flex items-center justify-center gap-2 js-copy-trigger"
                            data-copy-text="{{ __('messages.mohammad_iqbal_alimyar') }}">
                            <span class="material-symbols-outlined text-lg">content_copy</span>
                            {{ __('messages.copy_full_name') }}
                        </button>
                    </div>
                </div>

                <div
                    class="glass-panel rounded-[2rem] p-10 flex flex-col h-full relative group hover:-translate-y-2 transition-transform duration-500 overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-10">
                        <span class="material-symbols-outlined text-6xl text-charcoal">account_balance</span>
                    </div>
                    <div class="mb-8">
                        <span class="material-symbols-outlined text-charcoal/70 text-3xl mb-4">assured_workload</span>
                        <h3 class="font-serif text-2xl font-bold text-deep-green mb-2">{{ __('messages.bank_transfer') }}
                        </h3>
                        <p class="text-sage-green text-xs font-bold uppercase tracking-widest">
                            {{ __('messages.international_ziraat_bank') }}</p>
                    </div>
                    <div class="gold-line-art mb-8"></div>
                    <div class="space-y-5 flex-grow">
                        <div class="relative">
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                                {{ __('messages.iban_number') }}</p>
                            <div
                                class="flex items-center justify-between bg-white/40 p-3 rounded-lg border border-charcoal/5">
                                <p class="font-mono text-xs text-charcoal font-bold">TR76 0001 0021 2193 4812 5001</p>
                                <span
                                    class="material-symbols-outlined text-lg text-sage-green cursor-pointer hover:scale-110 transition-transform js-copy-trigger"
                                    data-copy-text="TR76 0001 0021 2193 4812 5001">content_copy</span>
                            </div>
                        </div>
                        <div class="relative">
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                                {{ __('messages.swift_bic_code') }}</p>
                            <div
                                class="flex items-center justify-between bg-white/40 p-3 rounded-lg border border-charcoal/5">
                                <p class="font-mono text-xs text-charcoal font-bold">TCZRTRA2</p>
                                <span
                                    class="material-symbols-outlined text-lg text-sage-green cursor-pointer hover:scale-110 transition-transform js-copy-trigger"
                                    data-copy-text="TCZRTRA2">content_copy</span>
                            </div>
                        </div>
                        <div class="relative">
                            <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                                {{ __('messages.account_holder') }}</p>
                            <p class="font-serif text-sm text-charcoal/80">EverGreen Conservation Fund</p>
                        </div>
                    </div>
                    <div class="mt-10 pt-6">
                        <button
                            class="w-full py-3 rounded-full bg-white border border-charcoal/10 text-charcoal text-sm font-bold hover:bg-charcoal/5 transition-all flex items-center justify-center gap-2 js-copy-all-bank"
                            type="button">
                            <span class="material-symbols-outlined text-lg">content_copy</span>
                            Copy All Bank Details
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-20 flex flex-col items-center gap-4 text-center">
                <div class="flex -space-x-2">
                    <div
                        class="w-10 h-10 rounded-full border-2 border-white bg-sage-green flex items-center justify-center text-white text-[10px] font-bold">
                        99+
                    </div>
                    <div
                        class="w-10 h-10 rounded-full border-2 border-white bg-gold-accent flex items-center justify-center text-white text-xs">
                        <span class="material-symbols-outlined text-sm">shield_with_heart</span>
                    </div>
                </div>
                <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-[0.3em]">Fully Encrypted &amp;
                    Secure Transfers</p>
            </div>
        </section>

    </main>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var copyTriggers = document.querySelectorAll('.js-copy-trigger[data-copy-text]');

                function showCheckMark(target) {
                    // Remove existing marker on this target if any
                    var existing = target.parentElement.querySelector('.js-copy-check');
                    if (existing) {
                        existing.remove();
                    }

                    var mark = document.createElement('span');
                    mark.className =
                        'js-copy-check inline-flex items-center justify-center ml-2 text-primary text-xs font-bold';
                    mark.textContent = '✓';
                    target.parentElement.appendChild(mark);

                    setTimeout(function() {
                        if (mark && mark.parentElement) {
                            mark.parentElement.removeChild(mark);
                        }
                    }, 1000);
                }

                copyTriggers.forEach(function(el) {
                    el.addEventListener('click', function() {
                        var text = el.getAttribute('data-copy-text');
                        if (!text) return;

                        var onSuccess = function() {
                            showCheckMark(el);
                        };

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(text).then(onSuccess).catch(function(err) {
                                console.error('Copy failed', err);
                            });
                        } else {
                            var textarea = document.createElement('textarea');
                            textarea.value = text;
                            textarea.style.position = 'fixed';
                            textarea.style.left = '-9999px';
                            document.body.appendChild(textarea);
                            textarea.select();
                            try {
                                document.execCommand('copy');
                                onSuccess();
                            } catch (e) {
                                console.error('Copy failed', e);
                            }
                            document.body.removeChild(textarea);
                        }
                    });
                });

                // Copy all bank details in one click
                var copyAllBankBtn = document.querySelector('.js-copy-all-bank');
                if (copyAllBankBtn) {
                    copyAllBankBtn.addEventListener('click', function() {
                        var iban = 'TR76 0001 0021 2193 4812 5001';
                        var swift = 'TCZRTRA2';
                        var holder = 'EverGreen Conservation Fund';
                        var allText = 'IBAN: ' + iban + '\nSWIFT/BIC: ' + swift + '\nAccount Holder: ' + holder;

                        var onSuccess = function() {
                            showCheckMark(copyAllBankBtn);
                        };

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(allText).then(onSuccess).catch(function(err) {
                                console.error('Copy failed', err);
                            });
                        } else {
                            var textarea = document.createElement('textarea');
                            textarea.value = allText;
                            textarea.style.position = 'fixed';
                            textarea.style.left = '-9999px';
                            document.body.appendChild(textarea);
                            textarea.select();
                            try {
                                document.execCommand('copy');
                                onSuccess();
                            } catch (e) {
                                console.error('Copy failed', e);
                            }
                            document.body.removeChild(textarea);
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection

@push('scripts')
    <script>
        // Gallery Lightbox
        function openLightbox(src, title, desc) {
            document.getElementById('lightboxImage').src = src;
            document.getElementById('lightboxTitle').textContent = title;
            document.getElementById('lightboxDesc').textContent = desc;
            const lb = document.getElementById('galleryLightbox');
            lb.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeLightboxBtn() {
            const lb = document.getElementById('galleryLightbox');
            lb.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function closeLightbox(event) {
            if (event.target === document.getElementById('galleryLightbox')) {
                closeLightboxBtn();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightboxBtn();
        });

        // Gallery item entrance animation on scroll
        const galleryObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, i * 80);
                    galleryObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.gallery-item').forEach((el, idx) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = `opacity 0.6s ease ${idx * 0.07}s, transform 0.6s ease ${idx * 0.07}s`;
            galleryObserver.observe(el);
        });
    </script>
    <script>
        // Video carousel scroll functionality
        document.addEventListener('DOMContentLoaded', function() {
            const videoContainer = document.getElementById('videoContainer');
            const scrollLeftBtn = document.getElementById('scrollLeft');
            const scrollRightBtn = document.getElementById('scrollRight');

            if (videoContainer && scrollLeftBtn && scrollRightBtn) {
                const scrollAmount = 340; // Width of one video card + gap

                scrollLeftBtn.addEventListener('click', function() {
                    videoContainer.scrollBy({
                        left: -scrollAmount,
                        behavior: 'smooth'
                    });
                });

                scrollRightBtn.addEventListener('click', function() {
                    videoContainer.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                });

                // Update button visibility based on scroll position
                function updateButtonVisibility() {
                    const maxScroll = videoContainer.scrollWidth - videoContainer.clientWidth;

                    if (videoContainer.scrollLeft <= 0) {
                        scrollLeftBtn.style.opacity = '0.3';
                        scrollLeftBtn.style.pointerEvents = 'none';
                    } else {
                        scrollLeftBtn.style.opacity = '1';
                        scrollLeftBtn.style.pointerEvents = 'auto';
                    }

                    if (videoContainer.scrollLeft >= maxScroll - 10) {
                        scrollRightBtn.style.opacity = '0.3';
                        scrollRightBtn.style.pointerEvents = 'none';
                    } else {
                        scrollRightBtn.style.opacity = '1';
                        scrollRightBtn.style.pointerEvents = 'auto';
                    }
                }

                videoContainer.addEventListener('scroll', updateButtonVisibility);
                updateButtonVisibility(); // Initial check
            }

            // YouTube video functionality
            const videoCards = document.querySelectorAll('[data-video-id]');
            const videoModal = document.getElementById('videoModal');
            const modalIframe = document.getElementById('modalIframe');
            const closeModal = document.getElementById('closeModal');

            const videoModalInner = document.getElementById('videoModalInner');
            const videoModalAspect = document.getElementById('videoModalAspect');

            videoCards.forEach(function(card) {
                card.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-video-id');
                    const isShort = this.getAttribute('data-is-short') === 'true';

                    if (videoId) {
                        // Shorts use /shorts/ embed path; regular videos use /embed/
                        const embedBase = isShort
                            ? 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0'
                            : 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0';

                        modalIframe.setAttribute('src', embedBase);

                        // Switch modal size: portrait for Shorts, landscape for videos
                        if (isShort) {
                            videoModalInner.className = 'relative w-full max-w-sm mx-auto transition-all duration-300';
                            videoModalAspect.className = 'relative bg-black rounded-lg overflow-hidden';
                            videoModalAspect.style.aspectRatio = '9/16';
                        } else {
                            videoModalInner.className = 'relative w-full max-w-4xl transition-all duration-300';
                            videoModalAspect.className = 'relative aspect-video bg-black rounded-lg overflow-hidden';
                            videoModalAspect.style.aspectRatio = '';
                        }

                        videoModal.classList.remove('hidden');
                        videoModal.classList.add('flex');
                        document.body.style.overflow = 'hidden';
                    }
                });
            });

            // Close modal when clicking close button
            closeModal.addEventListener('click', function() {
                closeVideoModal();
            });

            // Close modal when clicking outside
            videoModal.addEventListener('click', function(e) {
                if (e.target === videoModal) {
                    closeVideoModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !videoModal.classList.contains('hidden')) {
                    closeVideoModal();
                }
            });

            function closeVideoModal() {
                // Hide modal
                videoModal.classList.add('hidden');
                videoModal.classList.remove('flex');

                // Clear iframe src to stop video
                modalIframe.setAttribute('src', '');

                // Restore body scroll
                document.body.style.overflow = '';
            }
        });
    </script>
@endpush
