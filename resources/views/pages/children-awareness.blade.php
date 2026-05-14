@extends('layouts.layout')
@section('title', __('messages.baren_page_title'))
@section('content')

    {{-- ===================================================================
         HERO — Editorial book launch
    =================================================================== --}}
    <section class="relative w-full overflow-hidden" style="background: #fafaf5;">
        <!-- paper grain -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
            style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;">
        </div>
        <!-- soft floating botanicals -->
        <div class="absolute top-16 left-6 text-vibrant-lime/15 select-none pointer-events-none floating hidden md:block">
            <span class="material-symbols-outlined" style="font-size: 9rem;">eco</span>
        </div>
        <div class="absolute bottom-10 left-1/3 text-gold-accent/10 select-none pointer-events-none floating hidden lg:block"
            style="animation-delay: 3s;">
            <span class="material-symbols-outlined" style="font-size: 7rem;">spa</span>
        </div>
        <div class="absolute top-1/3 right-6 text-deep-green/[0.07] select-none pointer-events-none floating hidden md:block"
            style="animation-delay: 1.5s;">
            <span class="material-symbols-outlined" style="font-size: 8rem;">forest</span>
        </div>

        <!-- top crumb -->
        <div class="relative z-10 border-b border-deep-green/10">
            <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between text-[10px] font-bold tracking-[0.25em] uppercase">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-deep-green/70 hover:text-deep-green transition-colors">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>{{ __('messages.baren_back_home') }}</span>
                </a>
                <div class="hidden md:flex items-center gap-2 text-charcoal/40">
                    <span class="material-symbols-outlined text-vibrant-lime text-sm">menu_book</span>
                    <span>{{ __('messages.baren_crumb_chapter') }}</span>
                </div>
                <div class="flex items-center gap-2 text-charcoal/40">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-vibrant-lime opacity-60 animate-ping"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-vibrant-lime"></span>
                    </span>
                    <span>{{ __('messages.baren_crumb_now_reading') }}</span>
                </div>
            </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 py-16 md:py-24 lg:py-28">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                <!-- LEFT — title -->
                <div class="lg:col-span-7 hero-fade-in">
                    <p class="font-handwriting text-3xl md:text-4xl text-vibrant-lime mb-2 leading-none">
                        ~ {{ __('messages.baren_eyebrow_present') }} ~
                    </p>
                    <div class="inline-flex items-center gap-3 mb-7">
                        <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                        <span class="text-vibrant-lime font-bold tracking-[0.4em] text-[10px] uppercase">
                            {{ __('messages.baren_eyebrow_kicker') }}
                        </span>
                    </div>

                    <h1 class="font-serif font-bold text-deep-green leading-[0.92] tracking-tight mb-8"
                        style="font-size: clamp(2.75rem, 7vw, 6.25rem);">
                        {!! __('messages.baren_title') !!}
                        <span class="italic font-black relative inline-block">
                            <span class="relative z-10">{{ __('messages.baren_title_accent') }}</span>
                            <svg class="absolute -bottom-3 left-0 w-full h-3 z-0" preserveAspectRatio="none" viewBox="0 0 200 10">
                                <path d="M2,8 Q50,2 100,5 T198,4" fill="none" stroke="#84cc16" stroke-width="3" stroke-linecap="round" />
                            </svg>
                        </span>
                    </h1>

                    <p class="text-charcoal/70 text-lg md:text-xl font-medium leading-[1.6] max-w-xl mb-8">
                        {{ __('messages.baren_subtitle') }}
                    </p>

                    <!-- Meta chips -->
                    <div class="flex flex-wrap items-center gap-3 mb-10">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-deep-green/8 border border-deep-green/15 rounded-full text-deep-green text-xs font-bold uppercase tracking-widest">
                            <span class="material-symbols-outlined text-base">description</span>
                            {{ __('messages.baren_meta_pages') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gold-accent/10 border border-gold-accent/25 rounded-full text-gold-accent text-xs font-bold uppercase tracking-widest">
                            <span class="material-symbols-outlined text-base">palette</span>
                            {{ __('messages.baren_meta_format') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-vibrant-lime/10 border border-vibrant-lime/25 rounded-full text-vibrant-lime text-xs font-bold uppercase tracking-widest">
                            <span class="material-symbols-outlined text-base">family_restroom</span>
                            {{ __('messages.baren_meta_age') }}
                        </span>
                    </div>

                    <!-- Hero CTAs -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <a href="{{ asset('storybook.pdf') }}" target="_blank" rel="noopener"
                            class="group inline-flex items-center gap-2 px-7 py-4 bg-deep-green text-white font-extrabold text-sm tracking-wide uppercase rounded-full shadow-lg shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 transition-all">
                            <span class="material-symbols-outlined text-vibrant-lime text-base">menu_book</span>
                            {{ __('messages.baren_read_btn') }}
                            <span class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">open_in_new</span>
                        </a>
                        <a href="{{ asset('storybook.pdf') }}" download
                            class="inline-flex items-center gap-2 px-7 py-4 border-2 border-deep-green text-deep-green font-extrabold text-sm tracking-wide uppercase rounded-full hover:bg-deep-green hover:text-white transition-all">
                            <span class="material-symbols-outlined text-base">download</span>
                            {{ __('messages.baren_download_btn') }}
                        </a>
                    </div>
                </div>

                <!-- RIGHT — book cover, large + theatrical -->
                <div class="lg:col-span-5 relative hero-images-animate">
                    <div class="relative max-w-md mx-auto">
                        <!-- Hand-drawn arrow pointing at the book -->
                        <div class="absolute -top-8 -left-4 md:-left-10 hidden md:block z-20">
                            <p class="font-handwriting text-2xl md:text-3xl text-deep-green/80 -rotate-6 leading-tight">
                                {{ __('messages.baren_arrow_label') }}
                            </p>
                            <svg class="w-16 h-16 text-vibrant-lime mt-1" viewBox="0 0 64 64" fill="none">
                                <path d="M8 12 C 22 22, 32 38, 38 54" stroke="currentColor" stroke-width="3" stroke-linecap="round" fill="none" />
                                <path d="M30 50 L 40 56 L 36 46" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                            </svg>
                        </div>

                        <!-- Floor shadow -->
                        <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-[80%] h-6 bg-deep-green/20 blur-xl rounded-full"></div>

                        <!-- Tilted, gently swaying book -->
                        @php
                            // Use the real book cover image if available, otherwise show a styled mock
                            $coverExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                            $realCover = null;
                            foreach ($coverExtensions as $ext) {
                                if (file_exists(public_path('storybook-cover.' . $ext))) {
                                    $realCover = 'storybook-cover.' . $ext;
                                    break;
                                }
                            }
                        @endphp

                        <div class="relative" style="animation: gentleSway 6s ease-in-out infinite; --rot: -4deg;">
                            <!-- Page thickness -->
                            <div class="absolute top-2 -right-2 bottom-2 w-3 bg-gradient-to-b from-gray-100 via-white to-gray-300 rounded-r-sm shadow-md"></div>
                            <div class="absolute top-3 -right-1 bottom-3 w-1.5 bg-gradient-to-b from-gray-200 to-gray-400 rounded-r-sm"></div>

                            <!-- Cover -->
                            <div class="relative aspect-[3/4] w-full overflow-hidden rounded-sm shadow-[0_40px_80px_rgba(6,46,34,0.35)]">

                                @if ($realCover)
                                    {{-- Real book cover image --}}
                                    <img src="{{ asset($realCover) }}"
                                        alt="{{ __('messages.baren_cover_alt') }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    {{-- Fallback styled mock cover (yellow title in the spirit of the real book) --}}
                                    <div class="absolute inset-0 bg-gradient-to-b from-[#7fb8d4] via-[#a7c98b] to-[#5a8c4a]"></div>
                                    {{-- Mountain silhouettes --}}
                                    <svg class="absolute bottom-1/3 left-0 right-0 w-full" viewBox="0 0 200 60" preserveAspectRatio="none">
                                        <path d="M0,60 L0,40 L25,15 L40,35 L60,10 L80,30 L100,20 L120,40 L145,15 L170,35 L200,25 L200,60 Z" fill="#6b7e6c" opacity="0.8"/>
                                        <path d="M0,60 L0,50 L30,30 L50,45 L75,25 L100,40 L130,30 L160,45 L185,35 L200,45 L200,60 Z" fill="#516554" opacity="0.7"/>
                                    </svg>
                                    {{-- Grass --}}
                                    <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-[#3d6b3b] to-transparent"></div>
                                    {{-- Brand badge top-left --}}
                                    <div class="absolute top-4 left-4 bg-white/90 rounded-md px-2 py-1.5 shadow-md flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-deep-green" style="font-size: 1.25rem;">park</span>
                                        <span class="text-[8px] font-bold leading-tight text-deep-green">Every Tree<br>for a Hope</span>
                                    </div>
                                    {{-- Cover content --}}
                                    <div class="absolute inset-0 flex flex-col items-center justify-between p-6 text-center">
                                        <div class="pt-8">
                                            <h2 class="font-serif text-7xl md:text-[5.5rem] font-black leading-none drop-shadow-2xl" style="color: #f5d24a; text-shadow: 2px 3px 0 rgba(0,0,0,0.25);">
                                                {{ __('messages.baren_cover_title_l1') }}
                                            </h2>
                                            <p class="font-serif text-white text-lg md:text-xl font-bold mt-1 drop-shadow-lg">
                                                {{ __('messages.baren_cover_title_l2') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-white/90 text-[10px] font-bold tracking-[0.3em] uppercase drop-shadow">{{ __('messages.baren_cover_byline') }}</p>
                                            <p class="text-white text-sm font-bold tracking-wider mt-1 drop-shadow">{{ __('messages.baren_makers_writer_name') }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Bookmark ribbon -->
                                <div class="absolute top-0 right-10 w-3 h-16 bg-gold-accent shadow-md"
                                    style="clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================================================================
         SYNOPSIS — What's the story
    =================================================================== --}}
    <section class="relative bg-white py-24 lg:py-32 overflow-hidden">
        <!-- Decorative tree icon, very faint -->
        <div class="absolute -bottom-10 -right-10 text-deep-green/[0.04] select-none pointer-events-none">
            <span class="material-symbols-outlined" style="font-size: 24rem;">forest</span>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-start">
            <div class="lg:col-span-5">
                <div class="inline-flex items-center gap-3 mb-5">
                    <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                    <span class="text-vibrant-lime font-bold tracking-[0.3em] text-xs uppercase">{{ __('messages.baren_story_eyebrow') }}</span>
                </div>
                <h2 class="font-serif text-4xl md:text-5xl font-bold text-deep-green leading-tight mb-6">
                    {!! __('messages.baren_story_title') !!}
                </h2>
                <!-- Stylized "page from the book" preview card -->
                <div class="relative bg-[#fafaf5] border border-deep-green/15 rounded-sm p-6 md:p-8 -rotate-1 shadow-[0_15px_35px_rgba(6,46,34,0.08)]">
                    <!-- corner page-fold -->
                    <div class="absolute top-0 right-0 w-10 h-10 bg-gradient-to-br from-deep-green/10 to-transparent"
                         style="clip-path: polygon(100% 0, 0 0, 100% 100%);"></div>
                    <p class="font-handwriting text-deep-green/60 text-sm mb-3">~ chapter one ~</p>
                    <p class="font-serif italic text-deep-green text-xl md:text-2xl leading-relaxed">
                        {{ __('messages.baren_story_pullquote') }}
                    </p>
                    <p class="text-charcoal/40 text-[10px] font-bold tracking-widest uppercase text-end mt-4">— page 01</p>
                </div>
            </div>

            <div class="lg:col-span-7 lg:pt-12">
                <p class="text-charcoal/80 text-lg md:text-xl leading-[1.85] mb-6">
                    {{ __('messages.baren_story_p1') }}
                </p>
                <p class="text-charcoal/75 text-base md:text-lg leading-[1.8]">
                    {{ __('messages.baren_story_p2') }}
                </p>

                <!-- "What it teaches" mini list -->
                <div class="mt-10 space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-vibrant-lime/15 flex items-center justify-center mt-1">
                            <span class="material-symbols-outlined text-vibrant-lime text-xl">favorite</span>
                        </div>
                        <div>
                            <h4 class="font-serif font-bold text-deep-green text-lg mb-1">{{ __('messages.baren_lesson1_title') }}</h4>
                            <p class="text-charcoal/70 text-sm leading-relaxed">{{ __('messages.baren_lesson1_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gold-accent/15 flex items-center justify-center mt-1">
                            <span class="material-symbols-outlined text-gold-accent text-xl">water_drop</span>
                        </div>
                        <div>
                            <h4 class="font-serif font-bold text-deep-green text-lg mb-1">{{ __('messages.baren_lesson2_title') }}</h4>
                            <p class="text-charcoal/70 text-sm leading-relaxed">{{ __('messages.baren_lesson2_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-deep-green/15 flex items-center justify-center mt-1">
                            <span class="material-symbols-outlined text-deep-green text-xl">groups</span>
                        </div>
                        <div>
                            <h4 class="font-serif font-bold text-deep-green text-lg mb-1">{{ __('messages.baren_lesson3_title') }}</h4>
                            <p class="text-charcoal/70 text-sm leading-relaxed">{{ __('messages.baren_lesson3_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================================================================
         12 PAGES — comic strip preview
    =================================================================== --}}
    <section class="relative py-24 lg:py-28 overflow-hidden" style="background: linear-gradient(180deg, #fafaf5 0%, #f3f6ed 100%);">
        <div class="relative z-10 max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-3 mb-5">
                    <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                    <span class="text-vibrant-lime font-bold tracking-[0.3em] text-xs uppercase">{{ __('messages.baren_pages_eyebrow') }}</span>
                    <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                </div>
                <h2 class="font-serif text-4xl md:text-5xl font-bold text-deep-green leading-tight mb-4">
                    {{ __('messages.baren_pages_title') }}
                </h2>
                <p class="text-charcoal/70 text-base md:text-lg leading-relaxed">
                    {{ __('messages.baren_pages_caption') }}
                </p>
            </div>

            <!-- 12 page chips, comic-strip style grid -->
            @php
                $pageMoments = [
                    ['icon' => 'spa',           'tone' => 'lime',  'label' => 'baren_page_01'],
                    ['icon' => 'pets',          'tone' => 'gold',  'label' => 'baren_page_02'],
                    ['icon' => 'water_drop',    'tone' => 'green', 'label' => 'baren_page_03'],
                    ['icon' => 'wb_sunny',      'tone' => 'gold',  'label' => 'baren_page_04'],
                    ['icon' => 'cloud',         'tone' => 'lime',  'label' => 'baren_page_05'],
                    ['icon' => 'park',          'tone' => 'green', 'label' => 'baren_page_06'],
                    ['icon' => 'auto_awesome',  'tone' => 'gold',  'label' => 'baren_page_07'],
                    ['icon' => 'forest',        'tone' => 'green', 'label' => 'baren_page_08'],
                    ['icon' => 'volunteer_activism','tone' => 'lime', 'label' => 'baren_page_09'],
                    ['icon' => 'groups',        'tone' => 'gold',  'label' => 'baren_page_10'],
                    ['icon' => 'eco',           'tone' => 'lime',  'label' => 'baren_page_11'],
                    ['icon' => 'favorite',      'tone' => 'green', 'label' => 'baren_page_12'],
                ];
                $toneMap = [
                    'lime'  => ['bg' => 'bg-vibrant-lime/8',  'border' => 'border-vibrant-lime/30',  'text' => 'text-vibrant-lime'],
                    'gold'  => ['bg' => 'bg-gold-accent/8',   'border' => 'border-gold-accent/30',   'text' => 'text-gold-accent'],
                    'green' => ['bg' => 'bg-deep-green/8',    'border' => 'border-deep-green/25',    'text' => 'text-deep-green'],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-5">
                @foreach($pageMoments as $i => $p)
                    @php
                        $tone = $toneMap[$p['tone']];
                        $rot = ($i % 3 === 0) ? '-rotate-2' : (($i % 3 === 1) ? 'rotate-1' : '-rotate-1');
                    @endphp
                    <div class="group relative bg-white border {{ $tone['border'] }} rounded-sm p-3 pb-5 {{ $rot }} hover:rotate-0 hover:-translate-y-2 hover:shadow-xl transition-all duration-500 shadow-[0_8px_20px_rgba(6,46,34,0.06)]">
                        <div class="absolute top-2 right-2 font-handwriting text-xl {{ $tone['text'] }} leading-none opacity-70">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="aspect-square w-full {{ $tone['bg'] }} rounded-sm flex items-center justify-center mb-2">
                            <span class="material-symbols-outlined {{ $tone['text'] }}" style="font-size: 2.5rem;">{{ $p['icon'] }}</span>
                        </div>
                        <p class="font-handwriting text-base text-deep-green text-center leading-tight">{{ __('messages.' . $p['label']) }}</p>
                    </div>
                @endforeach
            </div>

            <p class="text-center mt-10 font-handwriting text-2xl md:text-3xl text-charcoal/70">
                ~ {{ __('messages.baren_pages_footer') }} ~
            </p>
        </div>
    </section>

    {{-- ===================================================================
         THE MAKERS — film-credit style
    =================================================================== --}}
    <section class="relative py-24 lg:py-32 overflow-hidden bg-deep-green">
        <!-- Topographic decorative -->
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute top-10 right-10 text-white">
                <span class="material-symbols-outlined" style="font-size: 12rem;">auto_stories</span>
            </div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="font-handwriting text-3xl text-vibrant-lime mb-2">~ {{ __('messages.baren_makers_eyebrow') }} ~</p>
                <h2 class="font-serif text-4xl md:text-5xl font-bold text-white leading-tight">
                    {!! __('messages.baren_makers_title') !!}
                </h2>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <div class="h-[1px] w-16 bg-gold-accent/40"></div>
                    <span class="material-symbols-outlined text-gold-accent text-sm">star</span>
                    <div class="h-[1px] w-16 bg-gold-accent/40"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
                <!-- Writer -->
                <div class="relative bg-white/5 border border-white/10 rounded-2xl p-8 lg:p-10 backdrop-blur-sm hover:bg-white/10 transition-colors">
                    <div class="absolute -top-4 left-8 px-4 py-1 bg-gold-accent text-deep-green text-[10px] font-extrabold tracking-[0.35em] uppercase rounded-full">
                        {{ __('messages.baren_makers_writer_label') }}
                    </div>
                    <div class="flex items-start gap-5">
                        <div class="flex-shrink-0 w-16 h-16 rounded-full bg-gold-accent/20 border-2 border-gold-accent/40 flex items-center justify-center">
                            <span class="material-symbols-outlined text-gold-accent text-2xl">edit_note</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-serif text-2xl md:text-3xl font-bold text-white mb-1">{{ __('messages.baren_makers_writer_name') }}</h3>
                            <p class="text-gold-accent text-xs font-bold tracking-[0.25em] uppercase mb-4">{{ __('messages.baren_makers_writer_role') }}</p>
                            <p class="text-white/75 leading-relaxed">{{ __('messages.baren_makers_writer_note') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Supporter -->
                <div class="relative bg-white/5 border border-white/10 rounded-2xl p-8 lg:p-10 backdrop-blur-sm hover:bg-white/10 transition-colors">
                    <div class="absolute -top-4 left-8 px-4 py-1 bg-vibrant-lime text-deep-green text-[10px] font-extrabold tracking-[0.35em] uppercase rounded-full">
                        {{ __('messages.baren_makers_supporter_label') }}
                    </div>
                    <div class="flex items-start gap-5">
                        <div class="flex-shrink-0 w-16 h-16 rounded-full bg-vibrant-lime/20 border-2 border-vibrant-lime/40 flex items-center justify-center">
                            <span class="material-symbols-outlined text-vibrant-lime text-2xl">volunteer_activism</span>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-serif text-2xl md:text-3xl font-bold text-white mb-1">{{ __('messages.baren_makers_supporter_name') }}</h3>
                            <p class="text-vibrant-lime text-xs font-bold tracking-[0.25em] uppercase mb-4">{{ __('messages.baren_makers_supporter_role') }}</p>
                            <p class="text-white/75 leading-relaxed">{{ __('messages.baren_makers_supporter_note') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats strip -->
            <div class="mt-16 grid grid-cols-3 gap-4 md:gap-8 max-w-3xl mx-auto">
                <div class="text-center">
                    <p class="font-serif font-black text-vibrant-lime text-5xl md:text-6xl leading-none mb-2">12</p>
                    <p class="text-white/60 text-xs font-bold tracking-[0.25em] uppercase">{{ __('messages.baren_stats_pages') }}</p>
                </div>
                <div class="text-center border-x border-white/10">
                    <p class="font-serif font-black text-gold-accent text-5xl md:text-6xl leading-none mb-2">100</p>
                    <p class="text-white/60 text-xs font-bold tracking-[0.25em] uppercase">{{ __('messages.baren_stats_copies') }}</p>
                </div>
                <div class="text-center">
                    <p class="font-serif font-black text-white text-5xl md:text-6xl leading-none mb-2">∞</p>
                    <p class="text-white/60 text-xs font-bold tracking-[0.25em] uppercase">{{ __('messages.baren_stats_classrooms') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================================================================
         FINAL CTA — open the book
    =================================================================== --}}
    <section class="relative py-24 lg:py-32 overflow-hidden" style="background: #fafaf5;">
        <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
            style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;">
        </div>

        <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
            <p class="font-handwriting text-2xl md:text-3xl text-vibrant-lime mb-3">~ {{ __('messages.baren_cta_eyebrow') }} ~</p>
            <h2 class="font-serif text-4xl md:text-5xl lg:text-6xl font-bold text-deep-green leading-tight mb-6">
                {!! __('messages.baren_cta_title') !!}
            </h2>
            <p class="text-charcoal/75 text-lg md:text-xl leading-relaxed mb-10 max-w-2xl mx-auto">
                {{ __('messages.baren_cta_subtitle') }}
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8">
                <a href="{{ asset('storybook.pdf') }}" target="_blank" rel="noopener"
                    class="group inline-flex items-center gap-2 px-8 py-4 bg-deep-green text-white font-extrabold text-sm tracking-wide uppercase rounded-full shadow-lg shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 transition-all">
                    <span class="material-symbols-outlined text-vibrant-lime text-base">menu_book</span>
                    {{ __('messages.baren_read_btn') }}
                </a>
                <a href="{{ asset('storybook.pdf') }}" download
                    class="inline-flex items-center gap-2 px-8 py-4 border-2 border-deep-green text-deep-green font-extrabold text-sm tracking-wide uppercase rounded-full hover:bg-deep-green hover:text-white transition-all">
                    <span class="material-symbols-outlined text-base">download</span>
                    {{ __('messages.baren_download_btn') }}
                </a>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-x-8 gap-y-3 text-sm">
                <a href="{{ route('donate') }}" class="inline-flex items-center gap-2 text-deep-green font-bold underline decoration-vibrant-lime decoration-2 underline-offset-4 hover:decoration-[3px] transition-all">
                    <span class="material-symbols-outlined text-base">favorite</span>
                    {{ __('messages.baren_cta_sponsor_btn') }}
                </a>
                <span class="hidden sm:inline text-charcoal/30">·</span>
                <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 text-deep-green font-bold underline decoration-gold-accent decoration-2 underline-offset-4 hover:decoration-[3px] transition-all">
                    <span class="material-symbols-outlined text-base">school</span>
                    {{ __('messages.baren_cta_school_btn') }}
                </a>
            </div>
        </div>
    </section>

@endsection
