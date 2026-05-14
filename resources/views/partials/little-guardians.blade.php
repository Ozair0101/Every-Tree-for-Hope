<!-- ===== LITTLE GUARDIANS OF THE GREEN — Children's Section ===== -->
<section class="relative overflow-hidden py-24 lg:py-32" style="background: #fafaf5;">
    <!-- Subtle paper grain matching hero -->
    <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
        style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;">
    </div>

    <!-- Floating decorative botanical icons -->
    <div class="absolute top-12 left-8 text-vibrant-lime/15 select-none pointer-events-none floating hidden md:block">
        <span class="material-symbols-outlined" style="font-size: 8rem;">eco</span>
    </div>
    <div class="absolute bottom-16 right-10 text-gold-accent/15 select-none pointer-events-none floating hidden md:block"
        style="animation-delay: 2s;">
        <span class="material-symbols-outlined" style="font-size: 9rem;">spa</span>
    </div>
    <div class="absolute top-1/3 right-1/4 text-deep-green/[0.06] select-none pointer-events-none floating hidden lg:block"
        style="animation-delay: 4s;">
        <span class="material-symbols-outlined" style="font-size: 6rem;">child_care</span>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6">

        <!-- Header -->
        {{-- <div class="text-center max-w-3xl mx-auto mb-16">
            <p class="font-handwriting text-2xl md:text-3xl text-vibrant-lime mb-2">
                ~ {{ __('messages.guardians_eyebrow') }} ~
            </p>

            <div class="inline-flex items-center gap-3 mb-5">
                <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                <span
                    class="text-vibrant-lime font-bold tracking-[0.3em] text-xs uppercase">{{ __('messages.guardians_kicker') }}</span>
                <div class="h-[1px] w-10 bg-vibrant-lime"></div>
            </div>

            <h2 class="font-serif font-bold text-deep-green leading-[0.95] tracking-tight mb-6"
                style="font-size: clamp(2.25rem, 5vw, 4.25rem);">
                {{ __('messages.guardians_title_part1') }}
                <span class="italic font-black relative inline-block">
                    <span class="relative z-10">{{ __('messages.guardians_title_part2') }}</span>
                    <svg class="absolute -bottom-2 left-0 w-full h-3 z-0" preserveAspectRatio="none"
                        viewBox="0 0 200 10">
                        <path d="M2,8 Q50,2 100,5 T198,4" fill="none" stroke="#84cc16" stroke-width="3"
                            stroke-linecap="round" />
                    </svg>
                </span>
            </h2>

            <p class="text-charcoal/70 text-lg md:text-xl leading-relaxed">
                {{ __('messages.guardians_subtitle') }}
            </p>
        </div> --}}

        <!-- Polaroid Cards -->
        {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8 lg:gap-10 mb-16">

            <!-- Card 1: Plant -->
            <div
                class="group relative bg-white p-3 pb-8 shadow-[0_15px_40px_rgba(6,46,34,0.08)] transition-all duration-500 -rotate-2 hover:rotate-0 hover:-translate-y-3 hover:shadow-[0_25px_60px_rgba(6,46,34,0.18)]">
                <!-- Tape strip -->
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-24 h-6 bg-gold-accent/30 border border-gold-accent/40 -rotate-3 z-20"
                    style="backdrop-filter: blur(4px);"></div>
                <!-- Big handwritten number -->
                <div class="absolute top-3 right-4 z-20 font-handwriting text-5xl text-vibrant-lime leading-none">1.
                </div>
                <!-- Photo -->
                <div class="relative aspect-square overflow-hidden bg-deep-green/5">
                    <img src="{{ asset('images/5.jpeg') }}" alt="{{ __('messages.guardians_step1_label') }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                </div>
                <!-- Caption -->
                <div class="pt-5 px-2 text-center">
                    <p class="font-handwriting text-3xl md:text-4xl text-deep-green mb-2 leading-none">
                        {{ __('messages.guardians_step1_label') }}</p>
                    <p class="text-sm text-charcoal/70 leading-relaxed">
                        {{ __('messages.guardians_step1_caption') }}</p>
                </div>
            </div>

            <!-- Card 2: Care -->
            <div
                class="group relative bg-white p-3 pb-8 shadow-[0_15px_40px_rgba(6,46,34,0.08)] transition-all duration-500 rotate-1 hover:rotate-0 hover:-translate-y-3 hover:shadow-[0_25px_60px_rgba(6,46,34,0.18)]">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-24 h-6 bg-vibrant-lime/30 border border-vibrant-lime/40 rotate-2 z-20"
                    style="backdrop-filter: blur(4px);"></div>
                <div class="absolute top-3 right-4 z-20 font-handwriting text-5xl text-gold-accent leading-none">2.
                </div>
                <div class="relative aspect-square overflow-hidden bg-deep-green/5">
                    <img src="{{ asset('images/12.jpeg') }}" alt="{{ __('messages.guardians_step2_label') }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                </div>
                <div class="pt-5 px-2 text-center">
                    <p class="font-handwriting text-3xl md:text-4xl text-deep-green mb-2 leading-none">
                        {{ __('messages.guardians_step2_label') }}</p>
                    <p class="text-sm text-charcoal/70 leading-relaxed">
                        {{ __('messages.guardians_step2_caption') }}</p>
                </div>
            </div>

            <!-- Card 3: Grow -->
            <div
                class="group relative bg-white p-3 pb-8 shadow-[0_15px_40px_rgba(6,46,34,0.08)] transition-all duration-500 -rotate-1 hover:rotate-0 hover:-translate-y-3 hover:shadow-[0_25px_60px_rgba(6,46,34,0.18)]">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-24 h-6 bg-deep-green/25 border border-deep-green/30 -rotate-2 z-20"
                    style="backdrop-filter: blur(4px);"></div>
                <div class="absolute top-3 right-4 z-20 font-handwriting text-5xl text-deep-green/80 leading-none">3.
                </div>
                <div class="relative aspect-square overflow-hidden bg-deep-green/5">
                    <img src="{{ asset('images/22.jpeg') }}" alt="{{ __('messages.guardians_step3_label') }}"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                </div>
                <div class="pt-5 px-2 text-center">
                    <p class="font-handwriting text-3xl md:text-4xl text-deep-green mb-2 leading-none">
                        {{ __('messages.guardians_step3_label') }}</p>
                    <p class="text-sm text-charcoal/70 leading-relaxed">
                        {{ __('messages.guardians_step3_caption') }}</p>
                </div>
            </div>
        </div> --}}

        <!-- ===== Storybook spotlight ===== -->
        <div class="relative bg-white/70 border border-deep-green/10 rounded-[2rem] p-6 md:p-10 lg:p-14 mb-16 shadow-[0_15px_40px_rgba(6,46,34,0.06)]"
            style="backdrop-filter: blur(8px);">
            <!-- Decorative corner sparkle -->
            <div
                class="absolute -top-3 -right-3 w-12 h-12 rounded-full bg-gold-accent/15 flex items-center justify-center">
                <span class="material-symbols-outlined text-gold-accent text-xl">auto_stories</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-8 md:gap-12 items-center">

                <!-- Book cover (left, 2/5) -->
                <div class="md:col-span-2 flex justify-center">
                    <div class="group relative">
                        <!-- Shelf shadow underneath -->
                        <div
                            class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-[85%] h-4 bg-deep-green/15 blur-md rounded-full">
                        </div>
                        <!-- Book wrapper with tilt -->
                        <div class="relative -rotate-3 group-hover:rotate-0 transition-transform duration-500">
                            <!-- Page thickness on right edge -->
                            <div
                                class="absolute top-1 -right-1 bottom-1 w-2 bg-gradient-to-b from-gray-100 via-white to-gray-200 rounded-r-sm">
                            </div>
                            <div
                                class="absolute top-2 -right-[3px] bottom-2 w-1 bg-gradient-to-b from-gray-200 via-gray-100 to-gray-300 rounded-r-sm">
                            </div>
                            <!-- Cover image -->
                            <div
                                class="relative aspect-[3/4] w-48 sm:w-56 md:w-full md:max-w-xs overflow-hidden rounded-sm shadow-[0_25px_45px_rgba(6,46,34,0.25)] bg-deep-green">
                                <img src="{{ asset('everytree.png') }}" alt="{{ __('messages.guardians_book_title') }}"
                                    class="w-full h-full object-contain p-8 bg-gradient-to-br from-deep-green to-deep-green/80">
                                <!-- Bookmark ribbon -->
                                <div class="absolute top-0 right-6 w-3 h-12 bg-gold-accent shadow-md"
                                    style="clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 80%, 0 100%);"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content (right, 3/5) -->
                <div class="md:col-span-3 text-center md:text-start">
                    <p class="font-handwriting text-2xl md:text-3xl text-vibrant-lime mb-3">
                        ~ {{ __('messages.guardians_book_eyebrow') }} ~
                    </p>
                    <div class="inline-flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-gold-accent text-base">menu_book</span>
                        <span class="text-gold-accent font-bold tracking-[0.3em] text-xs uppercase">
                            {{ __('messages.guardians_book_kicker') }}
                        </span>
                    </div>
                    <h3
                        class="font-serif text-3xl md:text-4xl lg:text-5xl font-bold text-deep-green leading-tight mb-3">
                        {{ __('messages.guardians_book_title') }}
                    </h3>
                    <p class="text-charcoal/60 italic text-sm md:text-base mb-5">
                        {{ __('messages.guardians_book_byline') }}
                    </p>
                    <p class="text-charcoal/75 leading-relaxed mb-7 max-w-xl md:max-w-none mx-auto md:mx-0">
                        {{ __('messages.guardians_book_description') }}
                    </p>

                    <!-- Meta line -->
                    <div
                        class="flex items-center justify-center md:justify-start gap-6 text-xs text-charcoal/50 uppercase tracking-widest font-bold mb-7">
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-deep-green/60">description</span>
                            {{ __('messages.guardians_book_pages_meta') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-deep-green/60">family_restroom</span>
                            {{ __('messages.guardians_book_age_meta') }}
                        </span>
                    </div>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center md:justify-start gap-3">
                        <a href="{{ route('awareness') }}"
                            class="group inline-flex items-center gap-2 px-6 py-3.5 bg-deep-green text-white font-extrabold text-sm tracking-wide uppercase rounded-full shadow-lg shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 transition-all">
                            <span class="material-symbols-outlined text-vibrant-lime text-base">menu_book</span>
                            {{ __('messages.guardians_book_visit_btn') }}
                            <span
                                class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                        <a href="{{ asset('storybook.pdf') }}" download
                            class="inline-flex items-center gap-2 px-6 py-3.5 border-2 border-deep-green text-deep-green font-extrabold text-sm tracking-wide uppercase rounded-full hover:bg-deep-green hover:text-white transition-all">
                            <span class="material-symbols-outlined text-base">download</span>
                            {{ __('messages.guardians_book_download_btn') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ===== END Storybook spotlight ===== -->

        <!-- Inspirational counter strip -->
        <div class="text-center mb-10">
            <p class="font-handwriting text-2xl md:text-3xl text-charcoal/80 leading-snug">
                ~ {{ __('messages.guardians_count_intro') }}
                <span class="text-vibrant-lime font-bold">{{ __('messages.guardians_count_outro') }}</span> ~
            </p>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-6">
            <a href="{{ route('contact') }}#get-involved"
                class="group inline-flex items-center gap-2 px-7 py-4 bg-deep-green text-white font-extrabold text-sm tracking-wide uppercase rounded-full shadow-lg shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 transition-all">
                <span class="material-symbols-outlined text-vibrant-lime text-base">favorite</span>
                {{ __('messages.guardians_pledge_btn') }}
                <span
                    class="material-symbols-outlined text-base group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
            <a href="#"
                class="inline-flex items-center gap-2 px-7 py-4 border-2 border-deep-green text-deep-green font-extrabold text-sm tracking-wide uppercase rounded-full hover:bg-deep-green hover:text-white transition-all">
                <span class="material-symbols-outlined text-base">download</span>
                {{ __('messages.guardians_activity_btn') }}
            </a>
        </div>

        <!-- Schools micro-link -->
        <div class="text-center">
            <p class="text-sm text-charcoal/60">
                {{ __('messages.guardians_for_schools') }}
                <a href="{{ route('contact') }}"
                    class="text-deep-green font-bold underline decoration-vibrant-lime decoration-2 underline-offset-4 hover:decoration-[3px] transition-all">{{ __('messages.guardians_school_link') }}</a>
            </p>
        </div>
    </div>
</section>
<!-- ===== END LITTLE GUARDIANS SECTION ===== -->
