@extends('layouts.layout')
@section('title', __('messages.history_page_title'))
@section('content')

    <!-- ===== HERO — Journey Timeline (Visual Story Path) ===== -->
    <header class="relative w-full overflow-hidden py-16 md:py-20"
        style="background: linear-gradient(180deg, #fefdf8 0%, #f5f1e3 100%);">

        <!-- Hand-drawn paper texture -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.4]"
            style="background-image: radial-gradient(rgba(6,46,34,0.06) 1px, transparent 1px); background-size: 16px 16px;"></div>

        <!-- Decorative botanical borders -->
        <span class="material-symbols-outlined absolute top-[-30px] right-[-20px] text-vibrant-lime/[0.06] text-[300px] select-none pointer-events-none rotate-12">forest</span>
        <span class="material-symbols-outlined absolute bottom-[-40px] left-[-30px] text-deep-green/[0.05] text-[280px] select-none pointer-events-none -rotate-12">park</span>

        <!-- Watermark "HISTORY" diagonal text -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
            <span class="text-deep-green/[0.025] font-serif font-black tracking-widest uppercase whitespace-nowrap"
                style="font-size: clamp(8rem, 18vw, 20rem); transform: rotate(-8deg);">
                {{ __('messages.history_watermark') }}
            </span>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6">

            <!-- Top status / chapter marker bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-12 history-fade-in">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-vibrant-lime">auto_stories</span>
                    <div class="flex items-center gap-2">
                        <span class="text-deep-green/40 text-[10px] font-bold uppercase tracking-[0.4em]">
                            {{ __('messages.history_badge') }}
                        </span>
                        <div class="h-[1px] w-8 bg-deep-green/20"></div>
                        <span class="text-deep-green text-[10px] font-bold uppercase tracking-[0.4em]">
                            {{ __('messages.history_volume_label') }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-deep-green/40 text-[10px] font-bold uppercase tracking-[0.3em]">
                    <span>2025</span>
                    <span class="material-symbols-outlined text-vibrant-lime/60 text-base">arrow_right_alt</span>
                    <span>{{ date('Y') }}</span>
                </div>
            </div>

            <!-- Headline + subtitle -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-16 history-fade-in" style="animation-delay: 0.15s;">
                <div class="lg:col-span-7">
                    <h1 class="font-serif font-bold text-deep-green leading-[0.95] tracking-tight"
                        style="font-size: clamp(3rem, 7vw, 6.5rem);">
                        {{ __('messages.history_hero_title_1') }}
                        <span class="block italic font-black relative inline-block text-vibrant-lime">
                            <span class="relative z-10">{{ __('messages.history_hero_title_2') }}</span>
                            <!-- Hand-drawn squiggle -->
                            <svg class="absolute -bottom-2 left-0 w-full h-3 z-0" preserveAspectRatio="none" viewBox="0 0 200 10">
                                <path d="M2,8 Q50,2 100,5 T198,4" fill="none" stroke="#84cc16" stroke-width="3" stroke-linecap="round" />
                            </svg>
                        </span>
                    </h1>
                </div>
                <div class="lg:col-span-5 lg:pl-8 lg:border-l border-deep-green/10 flex items-end">
                    <p class="text-charcoal/70 text-base md:text-lg leading-[1.7] font-medium">
                        {{ __('messages.history_hero_subtitle') }}
                    </p>
                </div>
            </div>

            <!-- ===== JOURNEY TIMELINE STRIP ===== -->
            <div class="relative history-fade-in" style="animation-delay: 0.3s;">

                <!-- Curved dashed connecting line (SVG) — visible only on md+ -->
                <svg class="absolute top-[60px] left-0 w-full h-20 hidden md:block pointer-events-none z-0"
                    preserveAspectRatio="none" viewBox="0 0 1200 80">
                    <path d="M 60,40 Q 350,5 550,40 T 1140,40"
                        fill="none" stroke="#064e3b" stroke-width="2"
                        stroke-dasharray="4 6" stroke-linecap="round" opacity="0.25" />
                </svg>

                <!-- Milestones grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-4 relative z-10">

                    <!-- Milestone 1 — 2025 / The Spark -->
                    <div class="text-center group">
                        <!-- Polaroid-style image -->
                        <div class="relative inline-block bg-white p-1.5 pb-7 shadow-xl transform -rotate-[3deg] group-hover:rotate-0 transition-transform duration-500"
                            style="animation: gentleSway 8s ease-in-out infinite;">
                            <div class="w-full aspect-square overflow-hidden bg-stone-100">
                                <img src="{{ asset('images/22.jpeg') }}" alt="The Spark"
                                    class="w-full h-full object-cover" style="filter: sepia(0.15);" />
                            </div>
                            <p class="absolute bottom-0.5 left-0 right-0 text-center text-charcoal/50 text-[8px] font-handwriting">
                                {{ __('messages.history_milestone_1_caption') }}
                            </p>
                        </div>
                        <!-- Big number -->
                        <div class="mt-4">
                            <p class="text-deep-green text-3xl md:text-4xl font-serif font-black leading-none">
                                12
                            </p>
                            <p class="text-vibrant-lime text-[9px] font-bold uppercase tracking-widest mt-1">
                                2025
                            </p>
                            <p class="text-charcoal/60 text-xs font-bold mt-2">
                                {{ __('messages.history_milestone_1_title') }}
                            </p>
                        </div>
                    </div>

                    <!-- Milestone 2 — 142 trees / First campaigns -->
                    <div class="text-center group md:mt-12">
                        <div class="relative inline-block bg-white p-1.5 pb-7 shadow-xl transform rotate-[2deg] group-hover:rotate-0 transition-transform duration-500"
                            style="animation: gentleSway 9s ease-in-out 0.5s infinite reverse;">
                            <div class="w-full aspect-square overflow-hidden bg-stone-100">
                                <img src="{{ asset('images/4.jpeg') }}" alt="First Campaigns"
                                    class="w-full h-full object-cover" style="filter: sepia(0.1);" />
                            </div>
                            <p class="absolute bottom-0.5 left-0 right-0 text-center text-charcoal/50 text-[8px] font-handwriting">
                                {{ __('messages.history_milestone_2_caption') }}
                            </p>
                        </div>
                        <div class="mt-4">
                            <p class="text-deep-green text-3xl md:text-4xl font-serif font-black leading-none">
                                142
                            </p>
                            <p class="text-vibrant-lime text-[9px] font-bold uppercase tracking-widest mt-1">
                                {{ __('messages.history_milestone_2_period') }}
                            </p>
                            <p class="text-charcoal/60 text-xs font-bold mt-2">
                                {{ __('messages.history_milestone_2_title') }}
                            </p>
                        </div>
                    </div>

                    <!-- Milestone 3 — 1000+ / The Movement -->
                    <div class="text-center group">
                        <div class="relative inline-block bg-white p-1.5 pb-7 shadow-xl transform -rotate-[2deg] group-hover:rotate-0 transition-transform duration-500"
                            style="animation: gentleSway 7s ease-in-out 1s infinite;">
                            <div class="w-full aspect-square overflow-hidden bg-stone-100">
                                <img src="{{ asset('images/10.jpeg') }}" alt="The Movement"
                                    class="w-full h-full object-cover" />
                            </div>
                            <p class="absolute bottom-0.5 left-0 right-0 text-center text-charcoal/50 text-[8px] font-handwriting">
                                {{ __('messages.history_milestone_3_caption') }}
                            </p>
                        </div>
                        <div class="mt-4">
                            <p class="text-deep-green text-3xl md:text-4xl font-serif font-black leading-none">
                                1,000<span class="text-vibrant-lime">+</span>
                            </p>
                            <p class="text-vibrant-lime text-[9px] font-bold uppercase tracking-widest mt-1">
                                {{ date('Y') }}
                            </p>
                            <p class="text-charcoal/60 text-xs font-bold mt-2">
                                {{ __('messages.history_milestone_3_title') }}
                            </p>
                        </div>
                    </div>

                    <!-- Milestone 4 — Future / Vision -->
                    <div class="text-center group md:mt-12">
                        <div class="relative inline-block bg-deep-green p-1.5 pb-7 shadow-xl transform rotate-[3deg] group-hover:rotate-0 transition-transform duration-500"
                            style="animation: gentleSway 8s ease-in-out 1.5s infinite reverse;">
                            <div class="w-full aspect-square overflow-hidden bg-deep-green flex items-center justify-center">
                                <span class="material-symbols-outlined text-vibrant-lime" style="font-size: 80px;">
                                    rocket_launch
                                </span>
                            </div>
                            <p class="absolute bottom-0.5 left-0 right-0 text-center text-vibrant-lime/70 text-[8px] font-handwriting">
                                {{ __('messages.history_milestone_4_caption') }}
                            </p>
                        </div>
                        <div class="mt-4">
                            <p class="text-deep-green text-3xl md:text-4xl font-serif font-black italic leading-none">
                                ∞
                            </p>
                            <p class="text-vibrant-lime text-[9px] font-bold uppercase tracking-widest mt-1">
                                {{ __('messages.history_milestone_4_period') }}
                            </p>
                            <p class="text-charcoal/60 text-xs font-bold mt-2">
                                {{ __('messages.history_milestone_4_title') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom CTA -->
            <div class="mt-16 flex flex-col sm:flex-row items-center justify-center gap-4 history-fade-in" style="animation-delay: 0.5s;">
                <a href="#chapter-1"
                    class="group inline-flex items-center gap-3 px-7 py-4 bg-deep-green text-white text-sm font-extrabold uppercase tracking-wider rounded-full shadow-xl shadow-deep-green/20 hover:bg-deep-green/90 hover:-translate-y-0.5 transition-all">
                    <span class="material-symbols-outlined text-vibrant-lime text-base">menu_book</span>
                    {{ __('messages.history_read_chapters') }}
                    <span class="material-symbols-outlined text-base group-hover:translate-y-0.5 transition-transform">arrow_downward</span>
                </a>
                <span class="text-charcoal/40 text-xs italic">
                    {{ __('messages.history_chapters_count', ['count' => 5]) }}
                </span>
            </div>
        </div>
    </header>
    <!-- ===== END HERO ===== -->

    <!-- ===== CHAPTER 1: THE SPARK ===== -->
    <section id="chapter-1" class="relative py-24 md:py-36 px-6 bg-white overflow-hidden scroll-mt-20">
        <!-- Decorative -->
        <div class="absolute top-0 left-0 w-full h-[2px]"
            style="background: linear-gradient(90deg, transparent, #84cc16 50%, transparent)"></div>
        <span
            class="material-symbols-outlined absolute top-16 right-[5%] text-primary/[0.04] text-[250px] select-none pointer-events-none">local_fire_department</span>

        <div class="max-w-5xl mx-auto">
            <!-- Chapter marker -->
            <div class="flex items-center gap-4 mb-12">
                <span class="text-vibrant-lime/40 text-7xl md:text-8xl font-serif italic leading-none select-none">01</span>
                <div>
                    <p class="text-vibrant-lime text-xs font-bold uppercase tracking-[0.3em]">{{ __('messages.history_chapter') }}</p>
                    <h2 class="text-4xl md:text-5xl font-serif font-bold text-deep-green">{{ __('messages.history_ch1_title') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-start">
                <!-- Text column -->
                <div class="lg:col-span-7 space-y-6">
                    <p class="text-charcoal/80 text-lg leading-[1.9] first-letter:text-5xl first-letter:font-serif first-letter:text-deep-green first-letter:float-left first-letter:mr-3 first-letter:mt-1 first-letter:leading-none">
                        {{ __('messages.history_ch1_p1') }}
                    </p>
                    <p class="text-charcoal/70 text-lg leading-[1.9]">
                        {{ __('messages.history_ch1_p2') }}
                    </p>

                    <!-- Pull Quote -->
                    <blockquote class="relative my-10 py-8 px-8 border-l-4 border-vibrant-lime bg-gradient-to-r from-primary/[0.03] to-transparent rounded-r-xl">
                        <span class="absolute -top-3 left-4 text-5xl text-vibrant-lime/30 font-serif select-none">"</span>
                        <p class="text-deep-green text-xl md:text-2xl font-serif italic leading-relaxed relative z-10">
                            {{ __('messages.history_ch1_quote') }}
                        </p>
                    </blockquote>

                    <p class="text-charcoal/70 text-lg leading-[1.9]">
                        {{ __('messages.history_ch1_p3') }}
                    </p>
                </div>

                <!-- Sticky image column -->
                <div class="lg:col-span-5 lg:sticky lg:top-24">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white transform rotate-1 hover:rotate-0 transition-transform duration-700">
                        <img src="{{ asset('images/22.jpeg') }}" alt="First planting"
                            class="w-full h-[400px] object-cover" />
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-charcoal/40 text-xs font-medium italic">{{ __('messages.history_ch1_img_caption') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== HIGHLIGHT STAT BAR ===== -->
    <section class="relative py-16 bg-deep-green overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0"
                style="background-image: radial-gradient(rgba(255,255,255,0.3) 1px, transparent 1px); background-size: 24px 24px;">
            </div>
        </div>
        <div class="max-w-5xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <p class="text-vibrant-lime text-4xl md:text-5xl font-extrabold">12</p>
                    <p class="text-white/50 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">{{ __('messages.history_stat_first_trees') }}</p>
                </div>
                <div>
                    <p class="text-white text-4xl md:text-5xl font-extrabold">1,000+</p>
                    <p class="text-white/50 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">{{ __('messages.history_stat_total_trees') }}</p>
                </div>
                <div>
                    <p class="text-vibrant-lime text-4xl md:text-5xl font-extrabold">10+</p>
                    <p class="text-white/50 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">{{ __('messages.history_stat_core_members') }}</p>
                </div>
                <div>
                    <p class="text-white text-4xl md:text-5xl font-extrabold">20+</p>
                    <p class="text-white/50 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">{{ __('messages.history_stat_volunteers') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CHAPTER 2: THE WHY ===== -->
    <section class="relative py-24 md:py-36 px-6 overflow-hidden"
        style="background: linear-gradient(180deg, #f7fce8 0%, #ffffff 100%);">
        <span
            class="material-symbols-outlined absolute bottom-16 left-[3%] text-vibrant-lime/[0.04] text-[200px] select-none pointer-events-none">eco</span>

        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-4 mb-12">
                <span class="text-vibrant-lime/40 text-7xl md:text-8xl font-serif italic leading-none select-none">02</span>
                <div>
                    <p class="text-vibrant-lime text-xs font-bold uppercase tracking-[0.3em]">{{ __('messages.history_chapter') }}</p>
                    <h2 class="text-4xl md:text-5xl font-serif font-bold text-deep-green">{{ __('messages.history_ch2_title') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-start">
                <!-- Image first on desktop -->
                <div class="lg:col-span-5 lg:sticky lg:top-24 order-2 lg:order-1">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white transform -rotate-1 hover:rotate-0 transition-transform duration-700">
                        <img src="{{ asset('images/37.jpeg') }}" alt="Planting by the lake"
                            class="w-full h-[400px] object-cover" />
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-charcoal/40 text-xs font-medium italic">{{ __('messages.history_ch2_img_caption') }}</p>
                    </div>
                </div>

                <!-- Text -->
                <div class="lg:col-span-7 space-y-6 order-1 lg:order-2">
                    <p class="text-charcoal/80 text-lg leading-[1.9] first-letter:text-5xl first-letter:font-serif first-letter:text-deep-green first-letter:float-left first-letter:mr-3 first-letter:mt-1 first-letter:leading-none">
                        {{ __('messages.history_ch2_p1') }}
                    </p>
                    <p class="text-charcoal/70 text-lg leading-[1.9]">
                        {{ __('messages.history_ch2_p2') }}
                    </p>

                    <!-- Icon list of challenges -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-8">
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-white shadow-sm border border-gray-100">
                            <span class="material-symbols-outlined text-red-400 text-xl mt-0.5">air</span>
                            <div>
                                <p class="font-bold text-deep-green text-sm">{{ __('messages.history_challenge_pollution') }}</p>
                                <p class="text-charcoal/60 text-xs mt-0.5">{{ __('messages.history_challenge_pollution_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-white shadow-sm border border-gray-100">
                            <span class="material-symbols-outlined text-amber-500 text-xl mt-0.5">thermostat</span>
                            <div>
                                <p class="font-bold text-deep-green text-sm">{{ __('messages.history_challenge_temp') }}</p>
                                <p class="text-charcoal/60 text-xs mt-0.5">{{ __('messages.history_challenge_temp_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-white shadow-sm border border-gray-100">
                            <span class="material-symbols-outlined text-blue-400 text-xl mt-0.5">water_drop</span>
                            <div>
                                <p class="font-bold text-deep-green text-sm">{{ __('messages.history_challenge_water') }}</p>
                                <p class="text-charcoal/60 text-xs mt-0.5">{{ __('messages.history_challenge_water_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-white shadow-sm border border-gray-100">
                            <span class="material-symbols-outlined text-emerald-500 text-xl mt-0.5">forest</span>
                            <div>
                                <p class="font-bold text-deep-green text-sm">{{ __('messages.history_challenge_green') }}</p>
                                <p class="text-charcoal/60 text-xs mt-0.5">{{ __('messages.history_challenge_green_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-charcoal/70 text-lg leading-[1.9]">
                        {{ __('messages.history_ch2_p3') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== PARALLAX IMAGE BREAK ===== -->
    <section class="relative h-[50vh] overflow-hidden">
        <img src="{{ asset('images/5.jpeg') }}" alt="Team photo"
            class="absolute inset-0 w-full h-full object-cover" style="object-position: center 25%;" />
        <div class="absolute inset-0 bg-deep-green/60"></div>
        <div class="relative z-10 h-full flex items-center justify-center text-center px-6">
            <div class="max-w-3xl">
                <blockquote class="text-white text-2xl md:text-4xl font-serif italic leading-snug">
                    {{ __('messages.history_midquote') }}
                </blockquote>
                <p class="text-white/50 text-xs font-bold uppercase tracking-widest mt-6">
                    — {{ __('messages.history_midquote_author') }}
                </p>
            </div>
        </div>
    </section>

    <!-- ===== CHAPTER 3: THE GROWTH ===== -->
    <section class="relative py-24 md:py-36 px-6 bg-white overflow-hidden">
        <span
            class="material-symbols-outlined absolute top-20 right-[4%] text-primary/[0.04] text-[220px] select-none pointer-events-none">trending_up</span>

        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-4 mb-12">
                <span class="text-vibrant-lime/40 text-7xl md:text-8xl font-serif italic leading-none select-none">03</span>
                <div>
                    <p class="text-vibrant-lime text-xs font-bold uppercase tracking-[0.3em]">{{ __('messages.history_chapter') }}</p>
                    <h2 class="text-4xl md:text-5xl font-serif font-bold text-deep-green">{{ __('messages.history_ch3_title') }}</h2>
                </div>
            </div>

            <div class="space-y-8">
                <p class="text-charcoal/80 text-lg leading-[1.9] max-w-3xl first-letter:text-5xl first-letter:font-serif first-letter:text-deep-green first-letter:float-left first-letter:mr-3 first-letter:mt-1 first-letter:leading-none">
                    {{ __('messages.history_ch3_p1') }}
                </p>

                <!-- Location cards -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 my-12">
                    @php
                        $locations = [
                            ['icon' => 'terrain', 'name' => __('messages.history_loc_haji_nabi')],
                            ['icon' => 'water', 'name' => __('messages.history_loc_qargha')],
                            ['icon' => 'mosque', 'name' => __('messages.history_loc_sakhi')],
                            ['icon' => 'location_city', 'name' => __('messages.history_loc_karte_se')],
                            ['icon' => 'school', 'name' => __('messages.history_loc_community')],
                        ];
                    @endphp
                    @foreach ($locations as $loc)
                        <div class="group p-4 rounded-xl border border-gray-100 bg-gradient-to-b from-white to-gray-50 text-center hover:border-vibrant-lime/30 hover:shadow-md transition-all duration-300">
                            <span class="material-symbols-outlined text-deep-green/30 text-2xl group-hover:text-vibrant-lime transition-colors">{{ $loc['icon'] }}</span>
                            <p class="text-deep-green text-xs font-bold mt-2">{{ $loc['name'] }}</p>
                        </div>
                    @endforeach
                </div>

                <p class="text-charcoal/70 text-lg leading-[1.9] max-w-3xl">
                    {{ __('messages.history_ch3_p2') }}
                </p>

                <!-- Dual images -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-12">
                    <div class="rounded-2xl overflow-hidden shadow-xl">
                        <img src="{{ asset('images/10.jpeg') }}" alt="Planting work"
                            class="w-full h-64 object-cover hover:scale-105 transition-transform duration-700" />
                    </div>
                    <div class="rounded-2xl overflow-hidden shadow-xl">
                        <img src="{{ asset('images/39.jpeg') }}" alt="Community effort"
                            class="w-full h-64 object-cover hover:scale-105 transition-transform duration-700" />
                    </div>
                </div>

                <p class="text-charcoal/70 text-lg leading-[1.9] max-w-3xl">
                    {{ __('messages.history_ch3_p3') }}
                </p>
            </div>
        </div>
    </section>

    <!-- ===== CHAPTER 4: SUSTAINABILITY ===== -->
    <section class="relative py-24 md:py-36 px-6 overflow-hidden"
        style="background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);">
        <span
            class="material-symbols-outlined absolute bottom-10 right-[4%] text-vibrant-lime/[0.04] text-[200px] select-none pointer-events-none">recycling</span>

        <div class="max-w-5xl mx-auto">
            <div class="flex items-center gap-4 mb-12">
                <span class="text-vibrant-lime/40 text-7xl md:text-8xl font-serif italic leading-none select-none">04</span>
                <div>
                    <p class="text-vibrant-lime text-xs font-bold uppercase tracking-[0.3em]">{{ __('messages.history_chapter') }}</p>
                    <h2 class="text-4xl md:text-5xl font-serif font-bold text-deep-green">{{ __('messages.history_ch4_title') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-start">
                <div class="lg:col-span-7 space-y-6">
                    <p class="text-charcoal/80 text-lg leading-[1.9] first-letter:text-5xl first-letter:font-serif first-letter:text-deep-green first-letter:float-left first-letter:mr-3 first-letter:mt-1 first-letter:leading-none">
                        {{ __('messages.history_ch4_p1') }}
                    </p>

                    <!-- Method cards -->
                    <div class="space-y-4 my-8">
                        <div class="flex items-start gap-4 p-5 rounded-xl bg-white shadow-sm border border-gray-100">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-blue-500 text-lg">water_drop</span>
                            </div>
                            <div>
                                <p class="font-bold text-deep-green">{{ __('messages.history_method_irrigation') }}</p>
                                <p class="text-charcoal/60 text-sm mt-1">{{ __('messages.history_method_irrigation_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-5 rounded-xl bg-white shadow-sm border border-gray-100">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-emerald-500 text-lg">groups</span>
                            </div>
                            <div>
                                <p class="font-bold text-deep-green">{{ __('messages.history_method_community') }}</p>
                                <p class="text-charcoal/60 text-sm mt-1">{{ __('messages.history_method_community_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-5 rounded-xl bg-white shadow-sm border border-gray-100">
                            <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-amber-500 text-lg">nature</span>
                            </div>
                            <div>
                                <p class="font-bold text-deep-green">{{ __('messages.history_method_native') }}</p>
                                <p class="text-charcoal/60 text-sm mt-1">{{ __('messages.history_method_native_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-charcoal/70 text-lg leading-[1.9]">
                        {{ __('messages.history_ch4_p2') }}
                    </p>
                </div>

                <div class="lg:col-span-5 lg:sticky lg:top-24">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white transform rotate-1 hover:rotate-0 transition-transform duration-700">
                        <img src="{{ asset('images/16.jpeg') }}" alt="Watering trees"
                            class="w-full h-[400px] object-cover" />
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-charcoal/40 text-xs font-medium italic">{{ __('messages.history_ch4_img_caption') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CHAPTER 5: THE VISION ===== -->
    <section class="relative py-24 md:py-36 px-6 bg-deep-green overflow-hidden">
        <div class="absolute inset-0 opacity-[0.05]"
            style="background-image: radial-gradient(rgba(255,255,255,0.4) 1px, transparent 1px); background-size: 24px 24px;">
        </div>
        <span
            class="material-symbols-outlined absolute top-16 left-[4%] text-white/[0.04] text-[250px] select-none pointer-events-none">rocket_launch</span>

        <div class="max-w-5xl mx-auto relative z-10">
            <div class="flex items-center gap-4 mb-12">
                <span class="text-vibrant-lime/30 text-7xl md:text-8xl font-serif italic leading-none select-none">05</span>
                <div>
                    <p class="text-vibrant-lime text-xs font-bold uppercase tracking-[0.3em]">{{ __('messages.history_chapter') }}</p>
                    <h2 class="text-4xl md:text-5xl font-serif font-bold text-white">{{ __('messages.history_ch5_title') }}</h2>
                </div>
            </div>

            <div class="space-y-8 max-w-3xl">
                <p class="text-white/80 text-lg leading-[1.9] first-letter:text-5xl first-letter:font-serif first-letter:text-vibrant-lime first-letter:float-left first-letter:mr-3 first-letter:mt-1 first-letter:leading-none">
                    {{ __('messages.history_ch5_p1') }}
                </p>

                <!-- Vision highlight -->
                <div class="relative my-12 p-8 md:p-10 rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-vibrant-lime text-3xl mb-4">emoji_objects</span>
                    <p class="text-vibrant-lime text-xl md:text-2xl font-serif font-bold italic leading-relaxed">
                        {{ __('messages.history_ch5_vision_highlight') }}
                    </p>
                </div>

                <p class="text-white/70 text-lg leading-[1.9]">
                    {{ __('messages.history_ch5_p2') }}
                </p>
                <p class="text-white/70 text-lg leading-[1.9]">
                    {{ __('messages.history_ch5_p3') }}
                </p>
            </div>
        </div>
    </section>

    <!-- ===== CLOSING CTA ===== -->
    <section class="relative py-24 md:py-32 px-6 bg-white text-center overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-[2px]"
            style="background: linear-gradient(90deg, transparent, #84cc16 50%, transparent)"></div>

        <div class="max-w-3xl mx-auto">
            <span class="material-symbols-outlined text-vibrant-lime text-5xl mb-6">volunteer_activism</span>
            <h2 class="text-4xl md:text-5xl font-serif font-bold text-deep-green leading-tight mb-6">
                {{ __('messages.history_cta_title') }}
            </h2>
            <p class="text-charcoal/60 text-lg leading-relaxed mb-10">
                {{ __('messages.history_cta_desc') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('donate') }}"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-vibrant-lime text-deep-green font-extrabold rounded-full hover:bg-vibrant-lime/90 transition-all shadow-xl shadow-vibrant-lime/20 hover:-translate-y-0.5 text-lg">
                    <span class="material-symbols-outlined">park</span>
                    {{ __('messages.plant_a_tree_today') }}
                </a>
                <a href="{{ route('gallery') }}"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 border-2 border-deep-green/20 text-deep-green font-bold rounded-full hover:bg-deep-green/5 transition-all hover:-translate-y-0.5 text-lg">
                    {{ __('messages.view_our_projects') }}
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>
        </div>
    </section>

@endsection
