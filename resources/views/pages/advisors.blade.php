@extends('layouts.layout')
@section('title', __('messages.advisors_page_title'))
@section('content')

@php
    $totalAdvisors = $advisors->count();
@endphp

<main>
    {{-- ===== HERO — Reverent "Council Chamber" ===== --}}
    <section class="relative w-full overflow-hidden py-20 md:py-28"
             style="background: linear-gradient(180deg, #1a3d2e 0%, #0a2a1d 100%);">

        {{-- Marble/stone texture overlay --}}
        <div class="absolute inset-0 pointer-events-none opacity-[0.07]"
             style="background-image:
                radial-gradient(circle at 18% 28%, #d4af37 0%, transparent 55%),
                radial-gradient(circle at 82% 70%, #84cc16 0%, transparent 55%),
                repeating-linear-gradient(45deg, transparent, transparent 80px, rgba(255,255,255,0.02) 80px, rgba(255,255,255,0.02) 81px);">
        </div>

        {{-- Subtle gold dot pattern --}}
        <div class="absolute inset-0 pointer-events-none opacity-[0.05]"
             style="background-image: radial-gradient(rgba(212,175,55,0.6) 1px, transparent 1px); background-size: 26px 26px;"></div>

        {{-- Classical ornaments --}}
        <span class="material-symbols-outlined absolute top-10 left-[4%] text-gold-accent/[0.10] text-[180px] rotate-12 select-none pointer-events-none">auto_stories</span>
        <span class="material-symbols-outlined absolute bottom-10 right-[4%] text-gold-accent/[0.10] text-[200px] -rotate-12 select-none pointer-events-none">psychology</span>

        <div class="relative z-10 max-w-6xl mx-auto px-6">

            {{-- Top brass plaque label --}}
            <div class="flex flex-col items-center mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-[1px] w-12 bg-gold-accent/40"></div>
                    <span class="material-symbols-outlined text-gold-accent text-base">verified</span>
                    <span class="text-gold-accent text-[10px] font-bold uppercase tracking-[0.5em]">
                        {{ __('messages.advisors_plaque_label') }}
                    </span>
                    <span class="material-symbols-outlined text-gold-accent text-base">verified</span>
                    <div class="h-[1px] w-12 bg-gold-accent/40"></div>
                </div>
            </div>

            {{-- Massive headline --}}
            <div class="text-center mb-10">
                <p class="text-gold-accent/60 text-[11px] font-bold uppercase tracking-[0.6em] mb-3 font-serif">
                    {{ __('messages.advisors_overline') }}
                </p>
                <h1 class="font-serif text-white leading-[0.92] tracking-tight"
                    style="font-size: clamp(3.25rem, 10vw, 8rem);">
                    <span class="font-bold">{{ __('messages.advisors_title_part1') }}</span>
                    <span class="block italic text-gold-accent font-black mt-1 relative inline-block">
                        <span class="relative z-10">{{ __('messages.advisors_title_part2') }}</span>
                        <svg class="absolute -bottom-3 left-0 w-full h-4 z-0" preserveAspectRatio="none" viewBox="0 0 220 16">
                            <path d="M5,11 Q60,2 110,8 T215,7" fill="none" stroke="#d4af37" stroke-width="2" stroke-linecap="round" opacity="0.7"/>
                            <circle cx="5" cy="11" r="2.5" fill="#d4af37" opacity="0.7"/>
                            <circle cx="215" cy="7" r="2.5" fill="#d4af37" opacity="0.7"/>
                        </svg>
                    </span>
                </h1>
                <p class="text-white/65 text-base md:text-lg max-w-2xl mx-auto mt-8 leading-relaxed font-light italic">
                    {{ __('messages.advisors_description') }}
                </p>
            </div>

            {{-- Brass plaque with count --}}
            <div class="max-w-md mx-auto">
                <div class="relative rounded-2xl overflow-hidden"
                     style="background: linear-gradient(135deg, rgba(212,175,55,0.15) 0%, rgba(212,175,55,0.05) 50%, rgba(212,175,55,0.15) 100%);
                            border: 1px solid rgba(212,175,55,0.25);
                            box-shadow: 0 0 40px rgba(212,175,55,0.08), inset 0 1px 0 rgba(212,175,55,0.2);">
                    {{-- Corner ornaments --}}
                    <span class="absolute top-3 left-3 text-gold-accent/35 text-2xl select-none">❦</span>
                    <span class="absolute top-3 right-3 text-gold-accent/35 text-2xl select-none">❦</span>
                    <span class="absolute bottom-3 left-3 text-gold-accent/35 text-2xl select-none">❦</span>
                    <span class="absolute bottom-3 right-3 text-gold-accent/35 text-2xl select-none">❦</span>

                    <div class="px-8 py-8">
                        <p class="text-center text-gold-accent text-[10px] font-bold uppercase tracking-[0.4em] mb-3">
                            — {{ __('messages.advisors_count_label') }} —
                        </p>
                        <p class="text-center text-white font-serif font-black tabular-nums leading-none"
                           style="font-size: clamp(3rem, 8vw, 5rem);">
                            {{ $totalAdvisors }}
                        </p>
                        <p class="text-center text-gold-accent/70 text-[10px] font-bold uppercase tracking-[0.4em] mt-2">
                            {{ trans_choice('messages.advisors_count_word', $totalAdvisors) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Scroll hint --}}
            @if ($totalAdvisors > 0)
                <div class="mt-12 flex justify-center">
                    <a href="#council" class="group flex flex-col items-center gap-2 text-white/40 hover:text-gold-accent transition-colors">
                        <span class="text-[10px] font-bold uppercase tracking-[0.3em]">{{ __('messages.advisors_meet_them') }}</span>
                        <span class="material-symbols-outlined text-2xl group-hover:translate-y-1 transition-transform">expand_more</span>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== THE COUNCIL — pull-quote portrait cards ===== --}}
    <section id="council" class="relative py-24 md:py-32 px-6 scroll-mt-20" style="background: #fafaf5;">
        <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
             style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;"></div>

        <span class="material-symbols-outlined absolute top-10 right-6 text-gold-accent/10 text-[140px] floating pointer-events-none select-none hidden md:block">format_quote</span>
        <span class="material-symbols-outlined absolute bottom-10 left-6 text-vibrant-lime/10 text-[120px] floating pointer-events-none select-none hidden md:block" style="animation-delay: 2.5s;">eco</span>

        <div class="relative z-10 max-w-6xl mx-auto">

            {{-- Editorial header --}}
            <div class="text-center max-w-2xl mx-auto mb-16">
                <p class="font-handwriting text-vibrant-lime text-2xl md:text-3xl mb-2 leading-none">
                    ~ {{ __('messages.advisors_council_eyebrow') }} ~
                </p>
                <div class="inline-flex items-center gap-3 mb-4">
                    <div class="h-[1px] w-10 bg-gold-accent/60"></div>
                    <span class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">
                        {{ __('messages.advisors_council_kicker') }}
                    </span>
                    <div class="h-[1px] w-10 bg-gold-accent/60"></div>
                </div>
                <h2 class="text-3xl md:text-5xl font-serif font-bold text-deep-green leading-tight">
                    {!! __('messages.advisors_council_title') !!}
                </h2>
            </div>

            @if ($totalAdvisors > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-12">
                    @foreach ($advisors as $advisor)
                        <article class="group relative">

                            {{-- Outer gold thread frame --}}
                            <div class="relative bg-white rounded-[2rem] p-8 md:p-10 pb-12
                                        shadow-[0_15px_40px_rgba(6,46,34,0.10)]
                                        hover:shadow-[0_25px_60px_rgba(6,46,34,0.18)]
                                        hover:-translate-y-1.5 transition-all duration-500
                                        border border-deep-green/10 overflow-hidden">

                                {{-- Top accent: gradient line --}}
                                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-gold-accent via-vibrant-lime to-gold-accent"></div>

                                {{-- Decorative quote mark, top-left --}}
                                <div class="absolute top-4 left-4 text-gold-accent/15 leading-none select-none pointer-events-none"
                                     style="font-family: 'Playfair Display', serif; font-size: 7rem; line-height: 0.8;">
                                    “
                                </div>

                                {{-- Decorative quote mark, bottom-right --}}
                                <div class="absolute bottom-2 right-4 text-gold-accent/15 leading-none select-none pointer-events-none rotate-180"
                                     style="font-family: 'Playfair Display', serif; font-size: 7rem; line-height: 0.8;">
                                    “
                                </div>

                                <div class="relative z-10 flex flex-col items-center text-center">

                                    {{-- Portrait medallion --}}
                                    <div class="relative mb-5">
                                        <div class="absolute -inset-2 rounded-full"
                                             style="background: radial-gradient(circle, rgba(212,175,55,0.25) 0%, transparent 70%);"></div>
                                        <div class="relative w-28 h-28 md:w-32 md:h-32 rounded-full bg-white border-[3px] border-gold-accent flex items-center justify-center overflow-hidden"
                                             style="box-shadow: 0 8px 22px rgba(6,46,34,0.15), inset 0 0 0 4px rgba(250,250,245,1);">
                                            <img src="{{ $advisor->full_logo_url }}"
                                                 alt="{{ $advisor->company_name }}"
                                                 class="w-3/4 h-3/4 object-contain"
                                                 loading="lazy">
                                        </div>
                                        {{-- Laurel sprigs --}}
                                        <span class="absolute -left-2 top-1/2 -translate-y-1/2 material-symbols-outlined text-vibrant-lime/60 text-2xl rotate-[-25deg]">eco</span>
                                        <span class="absolute -right-2 top-1/2 -translate-y-1/2 material-symbols-outlined text-vibrant-lime/60 text-2xl rotate-[25deg] scale-x-[-1]">eco</span>
                                    </div>

                                    {{-- Type badge --}}
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black tracking-[0.35em] uppercase mb-3"
                                          style="background: rgba(6,46,34,0.92); color: #d4af37;">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gold-accent"></span>
                                        {{ __('messages.advisors_badge') }}
                                    </span>

                                    {{-- Name --}}
                                    <h3 class="font-serif text-2xl md:text-3xl font-bold text-deep-green leading-tight mb-2">
                                        {{ $advisor->company_name }}
                                    </h3>

                                    {{-- Code line --}}
                                    @if ($advisor->code)
                                        <p class="text-charcoal/45 text-[10px] font-bold tracking-[0.35em] uppercase mb-4">
                                            {{ $advisor->code }}
                                        </p>
                                    @endif

                                    {{-- Hand-drawn divider --}}
                                    <svg width="80" height="8" viewBox="0 0 80 8" class="mb-4">
                                        <path d="M2,5 Q20,1 40,3 T78,3" fill="none" stroke="#d4af37" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/>
                                        <circle cx="40" cy="3.5" r="1.5" fill="#d4af37" opacity="0.7"/>
                                    </svg>

                                    {{-- Description as pull-quote --}}
                                    @if ($advisor->description)
                                        <blockquote class="text-charcoal/75 text-base md:text-lg leading-relaxed italic font-serif max-w-md">
                                            “{{ $advisor->description }}”
                                        </blockquote>
                                    @else
                                        <p class="font-handwriting text-deep-green text-lg leading-snug max-w-md">
                                            {{ __('messages.advisors_default_caption') }}
                                        </p>
                                    @endif

                                    {{-- Signature line --}}
                                    <div class="mt-6 flex items-center gap-2">
                                        <div class="h-[1px] w-8 bg-gold-accent/40"></div>
                                        <span class="font-handwriting text-deep-green/60 text-sm">
                                            {{ __('messages.advisors_signature_line') }}
                                        </span>
                                        <div class="h-[1px] w-8 bg-gold-accent/40"></div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <span class="material-symbols-outlined text-deep-green/20 text-7xl">psychology</span>
                    <p class="font-handwriting text-deep-green text-2xl mt-4">
                        {{ __('messages.advisors_empty_title') }}
                    </p>
                    <p class="text-charcoal/60 max-w-md mx-auto mt-2">
                        {{ __('messages.advisors_empty_desc') }}
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== CTA — Join the council ===== --}}
    <section class="relative py-20 px-6 overflow-hidden" style="background: linear-gradient(135deg, #1a3d2e 0%, #0a2a1d 100%);">
        <div class="absolute inset-0 pointer-events-none opacity-[0.06]"
             style="background-image: radial-gradient(rgba(212,175,55,0.6) 1px, transparent 1px); background-size: 24px 24px;"></div>
        <span class="material-symbols-outlined absolute -top-6 left-6 text-gold-accent/10 text-[200px] select-none pointer-events-none rotate-12">workspace_premium</span>
        <span class="material-symbols-outlined absolute -bottom-10 right-4 text-vibrant-lime/10 text-[220px] select-none pointer-events-none -rotate-12">eco</span>

        <div class="relative z-10 max-w-3xl mx-auto text-center">
            <p class="font-handwriting text-gold-accent text-2xl md:text-3xl mb-3">
                ~ {{ __('messages.advisors_cta_eyebrow') }} ~
            </p>
            <h2 class="text-3xl md:text-5xl font-serif font-bold text-white leading-tight mb-5">
                {!! __('messages.advisors_cta_title') !!}
            </h2>
            <p class="text-white/70 text-base md:text-lg leading-relaxed mb-8">
                {{ __('messages.advisors_cta_desc') }}
            </p>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center gap-2 px-7 py-4 rounded-full bg-gold-accent text-deep-green font-black text-xs tracking-[0.35em] uppercase shadow-lg hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                <span class="material-symbols-outlined text-base">psychology</span>
                {{ __('messages.advisors_cta_button') }}
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>
    </section>
</main>
@endsection
