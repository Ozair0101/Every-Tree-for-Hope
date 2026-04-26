@extends('layouts.layout')
@section('title', 'About Page')
@section('content')
    <!-- ===== HERO — Personal Letter from the Founder ===== -->
    <header class="relative w-full overflow-hidden py-20 md:py-28"
        style="background: #f5f1e8;">

        <!-- Paper texture overlay -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
            style="background-image: radial-gradient(rgba(6,46,34,0.06) 1px, transparent 1px); background-size: 18px 18px;"></div>

        <!-- Decorative botanical illustrations -->
        <span class="material-symbols-outlined absolute top-10 right-[3%] text-deep-green/[0.06] text-[280px] select-none pointer-events-none">forest</span>
        <span class="material-symbols-outlined absolute bottom-10 left-[2%] text-vibrant-lime/[0.08] text-[200px] select-none pointer-events-none rotate-12">eco</span>

        <div class="relative z-10 max-w-7xl mx-auto px-6">
            <!-- Top label / breadcrumb -->
            <div class="flex items-center gap-3 mb-12 about-fade-in">
                <div class="h-[1px] w-10 bg-deep-green/30"></div>
                <span class="text-deep-green/50 text-[10px] font-bold uppercase tracking-[0.4em]">
                    {{ __('messages.our_legacy') }}
                </span>
                <div class="h-[1px] w-16 bg-deep-green/30"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

                <!-- LEFT: The Letter -->
                <div class="lg:col-span-7 relative about-fade-in" style="animation-delay: 0.1s;">

                    <!-- "Dear visitor" salutation -->
                    <p class="text-deep-green/60 text-base md:text-lg font-serif italic mb-3">
                        {{ __('messages.about_letter_greeting') }}
                    </p>

                    <!-- Massive headline -->
                    <h1 class="font-serif font-bold text-deep-green leading-[0.95] tracking-tight mb-6"
                        style="font-size: clamp(3rem, 7vw, 6rem);">
                        {!! __('messages.the_heart_behind_the_green') !!}
                    </h1>

                    <!-- Subhead -->
                    <p class="text-charcoal/70 text-lg md:text-xl font-medium leading-relaxed max-w-xl mb-8">
                        {{ __('messages.restoring_the_lungs_of_kabul') }}
                    </p>

                    <!-- Letter body — personal note -->
                    <div class="relative max-w-xl pl-6 border-l-2 border-vibrant-lime/40 mb-10">
                        <p class="text-charcoal/65 text-base leading-[1.8] font-medium">
                            {{ __('messages.about_letter_body') }}
                        </p>
                    </div>

                    <!-- Signature row -->
                    <div class="flex items-center gap-4">
                        <!-- Hand-drawn squiggle "signature" -->
                        <svg class="w-32 h-12" viewBox="0 0 130 50" fill="none">
                            <path d="M5,30 C15,15 25,40 35,25 C50,5 65,40 80,20 C95,5 110,30 125,18"
                                stroke="#064e3b" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                            <path d="M40,42 L80,42" stroke="#064e3b" stroke-width="1" stroke-linecap="round" opacity="0.4"/>
                        </svg>
                        <div>
                            <p class="text-deep-green text-sm font-bold">{{ __('messages.about_signature_name') }}</p>
                            <p class="text-charcoal/50 text-[11px] font-medium italic">{{ __('messages.about_signature_title') }}</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Polaroid wall -->
                <div class="lg:col-span-5 relative h-[500px] sm:h-[560px] hidden md:block about-fade-in" style="animation-delay: 0.3s;">

                    <!-- Polaroid 1 — top-left (rotated -8deg) -->
                    <div class="absolute top-0 left-[8%] w-44 sm:w-52 bg-white p-2 pb-10 shadow-2xl transform -rotate-[8deg] hover:rotate-0 hover:scale-105 transition-all duration-500 z-20"
                        style="animation: gentleSway 8s ease-in-out infinite;">
                        <div class="aspect-[4/5] overflow-hidden bg-stone-100">
                            <img src="{{ asset('images/1.jpeg') }}" alt="Founder & team"
                                class="w-full h-full object-cover" />
                        </div>
                        <p class="absolute bottom-1 left-0 right-0 text-center text-charcoal/60 text-[9px] font-handwriting italic">
                            — {{ __('messages.about_polaroid_1') }}
                        </p>
                    </div>

                    <!-- Polaroid 2 — center-right (rotated +6deg) -->
                    <div class="absolute top-[15%] right-[5%] w-44 sm:w-52 bg-white p-2 pb-10 shadow-2xl transform rotate-[6deg] hover:rotate-0 hover:scale-105 transition-all duration-500 z-30"
                        style="animation: gentleSway 9s ease-in-out 1s infinite reverse;">
                        <div class="aspect-[4/5] overflow-hidden bg-stone-100">
                            <img src="{{ asset('images/4.jpeg') }}" alt="Team in field"
                                class="w-full h-full object-cover" />
                        </div>
                        <p class="absolute bottom-1 left-0 right-0 text-center text-charcoal/60 text-[9px] font-handwriting italic">
                            — {{ __('messages.about_polaroid_2') }}
                        </p>
                    </div>

                    <!-- Polaroid 3 — bottom-left (rotated +4deg) -->
                    <div class="absolute bottom-[5%] left-[15%] w-40 sm:w-48 bg-white p-2 pb-10 shadow-2xl transform rotate-[4deg] hover:rotate-0 hover:scale-105 transition-all duration-500 z-10"
                        style="animation: gentleSway 7s ease-in-out 2s infinite;">
                        <div class="aspect-[4/5] overflow-hidden bg-stone-100">
                            <img src="{{ asset('images/10.jpeg') }}" alt="Planting"
                                class="w-full h-full object-cover" />
                        </div>
                        <p class="absolute bottom-1 left-0 right-0 text-center text-charcoal/60 text-[9px] font-handwriting italic">
                            — {{ __('messages.about_polaroid_3') }}
                        </p>
                    </div>

                    <!-- Decorative tape pieces -->
                    <div class="absolute top-[-8px] left-[20%] w-16 h-5 bg-vibrant-lime/30 rotate-[-12deg] z-40"
                        style="border-radius: 2px;"></div>
                    <div class="absolute top-[10%] right-[18%] w-14 h-4 bg-gold-accent/30 rotate-[8deg] z-40"
                        style="border-radius: 2px;"></div>
                    <div class="absolute bottom-[28%] left-[25%] w-12 h-4 bg-deep-green/20 rotate-[-5deg] z-40"
                        style="border-radius: 2px;"></div>
                </div>

                <!-- Mobile polaroid (only visible on mobile) -->
                <div class="md:hidden flex justify-center about-fade-in" style="animation-delay: 0.3s;">
                    <div class="relative w-56 bg-white p-3 pb-12 shadow-2xl transform rotate-[-3deg]">
                        <div class="aspect-[4/5] overflow-hidden bg-stone-100">
                            <img src="{{ asset('images/1.jpeg') }}" alt="Founder & team"
                                class="w-full h-full object-cover" />
                        </div>
                        <p class="absolute bottom-2 left-0 right-0 text-center text-charcoal/60 text-[10px] font-handwriting italic">
                            — {{ __('messages.about_polaroid_1') }}
                        </p>
                        <div class="absolute top-[-8px] left-1/2 -translate-x-1/2 w-16 h-5 bg-vibrant-lime/30 rotate-[-2deg]"
                            style="border-radius: 2px;"></div>
                    </div>
                </div>
            </div>

            <!-- Bottom journey indicator -->
            <div class="mt-16 md:mt-24 flex items-center justify-center gap-3 about-fade-in" style="animation-delay: 0.5s;">
                <div class="h-[1px] w-16 bg-deep-green/20"></div>
                <a href="#our-story" class="flex items-center gap-2 text-deep-green/50 text-xs font-bold uppercase tracking-[0.3em] hover:text-deep-green transition-colors group">
                    {{ __('messages.about_continue_reading') }}
                    <span class="material-symbols-outlined text-base group-hover:translate-y-0.5 transition-transform">arrow_downward</span>
                </a>
                <div class="h-[1px] w-16 bg-deep-green/20"></div>
            </div>
        </div>
    </header>
    <!-- ===== END HERO ===== -->
    <!-- Our Story Section -->
    <section id="our-story" class="py-32 px-8 max-w-7xl mx-auto botanical-pattern scroll-mt-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            <div class="lg:col-span-5 relative">
                <div class="aspect-[3/4] rounded-xl overflow-hidden shadow-2xl">
                    <img alt="Tree Nursery" class="w-full h-full object-cover"
                        data-alt="Lush green saplings in a modern nursery" src="{{ asset('images/1.jpeg') }}" />
                </div>
                <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-accent-gold/10 rounded-full -z-10 blur-3xl">
                </div>
            </div>
            <div class="lg:col-span-7">
                <span
                    class="text-accent-gold font-bold tracking-widest uppercase text-sm mb-4 block">{{ __('messages.our_origin_story') }}</span>
                <h2 class="text-5xl md:text-6xl font-display mb-8 text-stone-900 dark:text-white">
                    {{ __('messages.a_grassroots_movement_reimagined') }}</h2>
                <div class="space-y-6 text-lg text-stone-600 dark:text-stone-300 leading-relaxed font-light">
                    <p>
                        {{ __('messages.origin_story_paragraph_1') }}
                    </p>
                    <p>
                        {{ __('messages.origin_story_paragraph_2') }}
                    </p>
                    <div class="flex items-center gap-6 pt-6">
                        <div class="h-px flex-1 bg-accent-gold/30"></div>
                        <span
                            class="text-accent-gold italic font-display text-2xl">{{ __('messages.rooted_in_resilience') }}</span>
                        <div class="h-px flex-1 bg-accent-gold/30"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Mission & Vision Section -->
    <section class="py-24 bg-stone-100 dark:bg-stone-900/50">
        <div class="max-w-7xl mx-auto px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Mission Card -->
                <div
                    class="glass-card p-12 rounded-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                    <div
                        class="absolute top-0 right-0 p-8 text-accent-gold/20 group-hover:text-accent-gold/40 transition-colors">
                        <span class="material-symbols-outlined text-8xl">landscape</span>
                    </div>
                    <h3 class="text-3xl font-display mb-6 text-stone-900 dark:text-white">
                        {{ __('messages.our_mission_title') }}</h3>
                    <p class="text-xl text-stone-600 dark:text-stone-300 leading-relaxed font-light relative z-10">
                        {{ __('messages.mission_statement') }}
                    </p>
                    <div class="mt-8 flex items-center gap-2 text-accent-gold">
                        <div class="w-8 h-px bg-accent-gold"></div>
                        <span class="uppercase tracking-widest text-xs font-bold">{{ __('messages.the_promise') }}</span>
                    </div>
                </div>
                <!-- Vision Card -->
                <div
                    class="glass-card p-12 rounded-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                    <div class="absolute top-0 right-0 p-8 text-primary/20 group-hover:text-primary/40 transition-colors">
                        <span class="material-symbols-outlined text-8xl">visibility</span>
                    </div>
                    <h3 class="text-3xl font-display mb-6 text-stone-900 dark:text-white">
                        {{ __('messages.our_vision_title') }}</h3>
                    <p class="text-xl text-stone-600 dark:text-stone-300 leading-relaxed font-light relative z-10">
                        {{ __('messages.vision_statement') }}
                    </p>
                    <div class="mt-8 flex items-center gap-2 text-primary">
                        <div class="w-8 h-px bg-primary"></div>
                        <span class="uppercase tracking-widest text-xs font-bold">{{ __('messages.the_horizon') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Founder's Message -->
    <section class="py-32 px-8 bg-white dark:bg-background-dark">
        <div class="max-w-5xl mx-auto">
            @php
                $founder = \App\Models\Team::active()->where('position', 'Founder Project Leader')->first();
            @endphp
            @if ($founder)
                <div class="flex flex-col md:flex-row items-center gap-16">
                    <div class="w-full md:w-1/3">
                        <div class="aspect-square rounded-full border-8 border-background-light overflow-hidden shadow-xl">
                            <img alt="{{ $founder->name }}" class="w-full h-full object-cover"
                                data-alt="Portrait of {{ $founder->name }}" src="{{ $founder->full_image_url }}" />
                        </div>
                    </div>
                    <div class="w-full md:w-2/3">
                        <span class="text-primary font-bold uppercase tracking-tighter text-4xl mb-6 block">"</span>
                        <blockquote
                            class="text-3xl md:text-4xl font-display italic text-stone-800 dark:text-stone-200 mb-8 leading-snug">
                            @if ($founder->message)
                                {{ $founder->message }}
                            @else
                                Every tree planted is a promise kept to the future generations of Kabul. We aren't just
                                planting
                                wood and leaves; we are planting hope.
                            @endif
                        </blockquote>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold text-stone-900 dark:text-white">{{ $founder->name }}</span>
                            <span class="text-stone-500 uppercase tracking-widest text-sm">{{ $founder->position }}</span>
                            <div class="mt-4 signature-font text-4xl text-accent-gold">
                                {{ $founder->name }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-stone-500">Founder & CEO information not available.</p>
                </div>
            @endif
        </div>
    </section>
    <!-- Our Team Section -->
    <section id="team-section" class="py-32 bg-background-light dark:bg-stone-950 px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-5xl font-display mb-4">Our Team</h2>
                <p class="text-stone-500 tracking-widest uppercase text-sm">A dedicated collective of ecologists and
                    activists</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-12">
                @php
                    $teamMembers = \App\Models\Team::active()->where('position', '!=', 'Founder Project Leader')->get();
                @endphp
                @forelse($teamMembers as $member)
                    <div class="group text-center">
                        <div
                            class="relative mb-6 mx-auto w-48 h-48 sm:w-64 sm:h-64 rounded-full overflow-hidden border-4 border-transparent group-hover:border-primary transition-all duration-500 p-2">
                            <img alt="{{ $member->name }}"
                                class="w-full h-full object-cover rounded-full filter group-hover:grayscale-0 transition-all duration-500"
                                data-alt="Professional portrait of {{ $member->name }}"
                                src="{{ $member->full_image_url }}" />
                            @if ($member->social_media_url || $member->email)
                                <div
                                    class="absolute inset-0 bg-primary/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <div class="flex gap-2 sm:gap-4">
                                        @if ($member->social_media_url)
                                            <a class="w-8 h-8 sm:w-10 sm:h-10 bg-white rounded-full flex items-center justify-center text-primary"
                                                href="{{ $member->social_media_url }}" target="_blank"><span
                                                    class="material-symbols-outlined text-xs sm:text-sm">link</span></a>
                                        @endif
                                        @if ($member->email)
                                            <a class="w-8 h-8 sm:w-10 sm:h-10 bg-white rounded-full flex items-center justify-center text-primary"
                                                href="mailto:{{ $member->email }}"><span
                                                    class="material-symbols-outlined text-xs sm:text-sm">mail</span></a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <h4 class="text-lg sm:text-xl font-bold text-stone-900 dark:text-white">{{ $member->name }}</h4>
                        <p class="text-sm sm:text-base text-stone-500 font-light">{{ $member->position }}</p>
                        @if ($member->message)
                            <p class="text-stone-600 italic text-xs sm:text-sm mt-3 max-w-xs mx-auto">
                                "{{ $member->message }}"</p>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-stone-500">No team members available at the moment.</p>
                    </div>
                @endforelse
            </div>

            <!-- Scroll to Team Button -->
            <div class="text-center mt-16">
                <a href="#team-section"
                    class="inline-flex items-center gap-2 bg-primary text-white px-8 py-4 rounded-full font-bold hover:bg-primary/90 transition-colors duration-300 shadow-lg hover:shadow-xl">
                    <span>Meet Our Team</span>
                    <span class="material-symbols-outlined animate-bounce">arrow_downward</span>
                </a>
            </div>
        </div>
    </section>
    <!-- Partners Section -->
    <section class="py-16 bg-white dark:bg-stone-900">
        <div class="max-w-7xl mx-auto px-8">
            <h2 class="text-center text-2xl font-serif text-deep-green dark:text-white mb-12">
                Global Partners & Allies
            </h2>

            <!-- Partners Grid - 5 per row -->
            <div class="grid grid-cols-5 gap-8 mb-12">
                @php
                    $partners = \App\Models\Partner::active()->ordered()->get();
                @endphp
                @forelse($partners as $partner)
                    <div class="text-center group">
                        @if ($partner->full_logo_url)
                            <img src="{{ $partner->full_logo_url }}" alt="{{ $partner->company_name }}"
                                class="h-8 w-auto mx-auto mb-2 opacity-60 group-hover:opacity-100 transition-opacity duration-200 filter grayscale group-hover:grayscale-0">
                        @else
                            <span class="material-symbols-outlined text-2xl text-charcoal/40 mb-2">business</span>
                        @endif
                        <p
                            class="text-xs text-charcoal/60 dark:text-stone-400 group-hover:text-deep-green dark:group-hover:text-white transition-colors duration-200">
                            {{ $partner->company_name }}
                        </p>
                    </div>
                @empty
                    <div class="col-span-5 text-center py-8">
                        <p class="text-charcoal/40 dark:text-stone-500 text-sm">Partners coming soon</p>
                    </div>
                @endforelse
            </div>

            <!-- Simple Call to Action -->
            <div class="text-center">
                <a href="{{ route('contact') }}"
                    class="text-sm text-gold-accent hover:text-deep-green dark:hover:text-gold-accent transition-colors duration-200 underline">
                    Become a Partner →
                </a>
            </div>
        </div>
    </section>
@endsection
