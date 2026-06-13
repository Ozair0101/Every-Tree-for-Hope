@extends('layouts.layout')
@section('title', 'Donators Page')
@section('content')

    <main>
        <!-- ===== HERO — Honor Roll / Gratitude Wall ===== -->
        <section class="relative w-full overflow-hidden py-20 md:py-28"
            style="background: linear-gradient(180deg, #1a3d2e 0%, #0a2a1d 100%);">

            <!-- Marble/stone texture overlay -->
            <div class="absolute inset-0 pointer-events-none opacity-[0.06]"
                style="background-image:
                    radial-gradient(circle at 20% 30%, #d4af37 0%, transparent 50%),
                    radial-gradient(circle at 80% 60%, #d4af37 0%, transparent 50%),
                    repeating-linear-gradient(45deg, transparent, transparent 80px, rgba(255,255,255,0.02) 80px, rgba(255,255,255,0.02) 81px);">
            </div>

            <!-- Subtle dot pattern -->
            <div class="absolute inset-0 pointer-events-none opacity-[0.04]"
                style="background-image: radial-gradient(rgba(212,175,55,0.6) 1px, transparent 1px); background-size: 24px 24px;">
            </div>

            <!-- Decorative classical ornaments -->
            <span class="material-symbols-outlined absolute top-12 left-[5%] text-gold-accent/[0.08] text-[180px] select-none pointer-events-none rotate-12">workspace_premium</span>
            <span class="material-symbols-outlined absolute bottom-12 right-[5%] text-gold-accent/[0.08] text-[200px] select-none pointer-events-none -rotate-12">military_tech</span>

            <div class="relative z-10 max-w-7xl mx-auto px-6">

                <!-- Top brass plaque label -->
                <div class="flex flex-col items-center mb-10 donor-fade-in">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-[1px] w-12 bg-gold-accent/40"></div>
                        <span class="material-symbols-outlined text-gold-accent text-base">verified</span>
                        <span class="text-gold-accent text-[10px] font-bold uppercase tracking-[0.5em]">
                            {{ __('messages.transparency_recognition') }}
                        </span>
                        <span class="material-symbols-outlined text-gold-accent text-base">verified</span>
                        <div class="h-[1px] w-12 bg-gold-accent/40"></div>
                    </div>
                </div>

                <!-- Massive "WITH GRATITUDE" heading -->
                <div class="text-center mb-10 donor-fade-in" style="animation-delay: 0.1s;">
                    <p class="text-gold-accent/60 text-[11px] font-bold uppercase tracking-[0.6em] mb-3 font-serif">
                        {{ __('messages.donors_overline') }}
                    </p>
                    <h1 class="font-serif text-white leading-[0.9] tracking-tight"
                        style="font-size: clamp(3.5rem, 11vw, 9rem);">
                        <span class="font-bold">{{ __('messages.donors_with') }}</span>
                        <span class="block italic text-gold-accent font-black mt-1 relative inline-block">
                            <span class="relative z-10">{{ __('messages.donors_gratitude') }}</span>
                            <!-- Gold flourish underline -->
                            <svg class="absolute -bottom-3 left-0 w-full h-4 z-0" preserveAspectRatio="none" viewBox="0 0 220 16">
                                <path d="M5,11 Q60,2 110,8 T215,7" fill="none" stroke="#d4af37" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                                <circle cx="5" cy="11" r="2.5" fill="#d4af37" opacity="0.7"/>
                                <circle cx="215" cy="7" r="2.5" fill="#d4af37" opacity="0.7"/>
                            </svg>
                        </span>
                    </h1>
                    <p class="text-white/60 text-base md:text-lg max-w-2xl mx-auto mt-8 leading-relaxed font-light italic">
                        {{ __('messages.guardians_description') }}
                    </p>
                </div>

                <!-- Engraved brass plaque with stats -->
                <div class="max-w-3xl mx-auto donor-fade-in" style="animation-delay: 0.25s;">
                    <div class="relative rounded-2xl overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(212,175,55,0.15) 0%, rgba(212,175,55,0.05) 50%, rgba(212,175,55,0.15) 100%);
                               border: 1px solid rgba(212,175,55,0.25);
                               box-shadow: 0 0 40px rgba(212,175,55,0.08), inset 0 1px 0 rgba(212,175,55,0.2);">

                        <!-- Top corner ornaments -->
                        <span class="absolute top-3 left-3 text-gold-accent/30 text-2xl select-none">❦</span>
                        <span class="absolute top-3 right-3 text-gold-accent/30 text-2xl select-none">❦</span>
                        <span class="absolute bottom-3 left-3 text-gold-accent/30 text-2xl select-none">❦</span>
                        <span class="absolute bottom-3 right-3 text-gold-accent/30 text-2xl select-none">❦</span>

                        <div class="px-8 py-10 md:px-12 md:py-12">
                            <p class="text-center text-gold-accent text-[10px] font-bold uppercase tracking-[0.4em] mb-5">
                                — {{ __('messages.donors_plaque_label') }} —
                            </p>

                            <div class="text-center">
                                <p class="text-white font-serif font-black tabular-nums leading-none"
                                    style="font-size: clamp(3rem, 9vw, 6rem);">
                                    {{ number_format($totalDonators ?? 0) }}
                                </p>
                                <p class="text-gold-accent/70 text-[10px] md:text-xs font-bold uppercase tracking-[0.4em] mt-3">
                                    {{ __('messages.donators') }}
                                </p>
                            </div>

                            <!-- Bottom inscription -->
                            <p class="text-center text-white/40 text-[10px] italic mt-6 font-serif">
                                {{ __('messages.donors_inscription') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Floating supporter name strips (decorative) -->
                @if($verifiedDonators && $verifiedDonators->count() > 0)
                    <div class="mt-14 donor-fade-in" style="animation-delay: 0.4s;">
                        <p class="text-center text-gold-accent/40 text-[9px] font-bold uppercase tracking-[0.5em] mb-5">
                            ✦ {{ __('messages.donors_recognition_label') }} ✦
                        </p>
                        <div class="flex flex-wrap justify-center gap-2 max-w-5xl mx-auto">
                            @foreach($verifiedDonators->take(15) as $donator)
                                <span class="px-3 py-1.5 rounded-full bg-white/[0.04] border border-gold-accent/15 text-white/70 text-xs font-serif italic hover:bg-gold-accent/10 hover:border-gold-accent/40 hover:text-white transition-all"
                                    style="animation: gentleSway {{ 6 + ($loop->index % 3) }}s ease-in-out {{ $loop->index * 0.1 }}s infinite;">
                                    {{ $donator->full_name }}
                                </span>
                            @endforeach
                            @if($verifiedDonators->count() > 15)
                                <span class="px-3 py-1.5 rounded-full bg-gold-accent/10 border border-gold-accent/30 text-gold-accent text-xs font-bold">
                                    + {{ $verifiedDonators->count() - 15 }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Bottom scroll hint -->
                <div class="mt-14 flex justify-center donor-fade-in" style="animation-delay: 0.55s;">
                    <a href="#sponsors" class="group flex flex-col items-center gap-2 text-white/40 hover:text-gold-accent transition-colors">
                        <span class="text-[10px] font-bold uppercase tracking-[0.3em]">{{ __('messages.donors_meet_them') }}</span>
                        <span class="material-symbols-outlined text-2xl group-hover:translate-y-1 transition-transform">expand_more</span>
                    </a>
                </div>
            </div>
        </section>
        <!-- ===== END HERO ===== -->

        @php
            $sponsorPackages = \App\Models\SponsorPackage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('price')
                ->get();
            $toneClasses = [
                'green' => ['bar' => 'bg-deep-green', 'dot' => 'bg-deep-green', 'text' => 'text-deep-green'],
                'lime' => ['bar' => 'bg-vibrant-lime', 'dot' => 'bg-vibrant-lime', 'text' => 'text-vibrant-lime'],
                'gold' => ['bar' => 'bg-gold-accent', 'dot' => 'bg-gold-accent', 'text' => 'text-gold-accent'],
                'charcoal' => ['bar' => 'bg-charcoal', 'dot' => 'bg-charcoal', 'text' => 'text-charcoal'],
            ];
        @endphp

        @if ($sponsorPackages->count() > 0)
            {{-- ===== SPONSOR PACKAGES ===== --}}
            <section id="sponsor-packages" class="relative py-24 md:py-32 px-6 md:px-12 lg:px-24 overflow-hidden scroll-mt-20" style="background: #fafaf5;">
                {{-- Paper grain --}}
                <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
                    style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;">
                </div>
                <div class="absolute top-12 right-10 text-vibrant-lime/10 select-none pointer-events-none floating hidden md:block">
                    <span class="material-symbols-outlined" style="font-size: 9rem;">redeem</span>
                </div>

                <div class="relative z-10 max-w-7xl mx-auto">

                    {{-- Editorial header --}}
                    <div class="text-center max-w-3xl mx-auto mb-14 md:mb-20">
                        <p class="font-handwriting text-2xl md:text-3xl text-vibrant-lime mb-2 leading-none">
                            ~ {{ __('messages.packages_eyebrow') }} ~
                        </p>
                        <div class="inline-flex items-center gap-3 mb-5">
                            <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                            <span class="text-vibrant-lime font-bold tracking-[0.4em] text-[10px] uppercase">
                                {{ __('messages.packages_kicker') }}
                            </span>
                            <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                        </div>
                        <h2 class="text-4xl sm:text-5xl md:text-6xl font-serif text-deep-green leading-[1.05] mb-5">
                            {{ __('messages.packages_title_part1') }}
                            <span class="italic font-black relative inline-block">
                                <span class="relative z-10">{{ __('messages.packages_title_part2') }}</span>
                                <svg class="absolute -bottom-2 left-0 w-full h-3 z-0" preserveAspectRatio="none" viewBox="0 0 200 10">
                                    <path d="M2,8 Q50,2 100,5 T198,4" fill="none" stroke="#84cc16" stroke-width="3" stroke-linecap="round" />
                                </svg>
                            </span>
                        </h2>
                        <p class="text-charcoal/70 text-base md:text-lg font-light leading-relaxed">
                            {{ __('messages.packages_description') }}
                        </p>
                    </div>

                    {{-- Packages grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min($sponsorPackages->count(), 3) }} gap-7 md:gap-8">

                        @foreach ($sponsorPackages as $pkg)
                            @php
                                $allocations = is_array($pkg->allocations) ? $pkg->allocations : [];
                                $featured = (bool) $pkg->is_featured;
                            @endphp

                            <article class="group relative {{ $featured ? 'md:scale-[1.03] z-10' : '' }} transition-transform duration-500">

                                {{-- Decorative ribbon for badge --}}
                                @if (!empty($pkg->badge_label))
                                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-20 inline-flex items-center gap-1.5 px-4 py-1.5 {{ $featured ? 'bg-gold-accent text-deep-green' : 'bg-deep-green text-white' }} text-[10px] font-black tracking-[0.3em] uppercase shadow-lg whitespace-nowrap">
                                        <span class="material-symbols-outlined text-xs">workspace_premium</span>
                                        {{ $pkg->badge_label }}
                                    </div>
                                @endif

                                <div class="relative bg-white {{ $featured ? 'border-[3px] border-deep-green' : 'border border-deep-green/15' }} rounded-[2rem] p-8 md:p-10 h-full flex flex-col shadow-[0_15px_40px_rgba(6,46,34,0.08)] hover:shadow-[0_25px_60px_rgba(6,46,34,0.15)] hover:-translate-y-1 transition-all duration-500 overflow-hidden">

                                    {{-- Top corner ornament --}}
                                    <div class="absolute top-5 end-5 text-gold-accent/25">
                                        <span class="material-symbols-outlined text-3xl">eco</span>
                                    </div>

                                    {{-- Title --}}
                                    <p class="text-[10px] font-bold tracking-[0.35em] uppercase text-charcoal/55 mb-2">
                                        {{ __('messages.packages_tier_label') }}
                                    </p>
                                    <h3 class="font-serif text-2xl md:text-3xl font-bold text-deep-green leading-tight mb-6">
                                        {{ $pkg->title }}
                                    </h3>

                                    {{-- Price + trees --}}
                                    <div class="mb-7">
                                        <div class="flex items-baseline gap-2 mb-1">
                                            <span class="font-serif font-black text-deep-green leading-none" style="font-size: clamp(3rem, 7vw, 5rem);">
                                                {{ $pkg->formatted_price }}
                                            </span>
                                            <span class="text-charcoal/50 text-sm font-medium">{{ __('messages.packages_per_donation') }}</span>
                                        </div>
                                        <div class="inline-flex items-center gap-2 mt-2 px-4 py-1.5 bg-vibrant-lime/12 border border-vibrant-lime/30 rounded-full">
                                            <span class="material-symbols-outlined text-vibrant-lime text-base">park</span>
                                            <span class="font-serif font-bold text-deep-green">
                                                {{ __('messages.packages_sponsors') }} <span class="font-black">{{ number_format($pkg->trees_count) }}</span> {{ __('messages.packages_trees') }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Description --}}
                                    @if (!empty($pkg->description))
                                        <p class="text-charcoal/75 text-sm md:text-base leading-relaxed mb-7">
                                            {{ $pkg->description }}
                                        </p>
                                    @endif

                                    {{-- Allocations --}}
                                    @if (count($allocations) > 0)
                                        <div class="mb-7 mt-auto">
                                            <p class="text-[10px] font-bold tracking-[0.35em] uppercase text-charcoal/55 mb-3 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-base text-gold-accent">pie_chart</span>
                                                {{ __('messages.packages_how_spent') }}
                                            </p>

                                            {{-- Stacked horizontal bar --}}
                                            <div class="relative h-3 rounded-full overflow-hidden bg-stone-100 flex mb-4">
                                                @foreach ($allocations as $alloc)
                                                    @php
                                                        $pct = (float) ($alloc['percentage'] ?? 0);
                                                        $tone = $alloc['tone'] ?? 'green';
                                                        $cls = $toneClasses[$tone] ?? $toneClasses['green'];
                                                    @endphp
                                                    @if ($pct > 0)
                                                        <div class="h-full {{ $cls['bar'] }}" style="width: {{ $pct }}%;" title="{{ $alloc['label'] ?? '' }} — {{ $pct }}%"></div>
                                                    @endif
                                                @endforeach
                                            </div>

                                            {{-- Legend --}}
                                            <ul class="space-y-2.5">
                                                @foreach ($allocations as $alloc)
                                                    @php
                                                        $tone = $alloc['tone'] ?? 'green';
                                                        $cls = $toneClasses[$tone] ?? $toneClasses['green'];
                                                    @endphp
                                                    <li class="flex items-center justify-between gap-3 text-sm">
                                                        <span class="flex items-center gap-2 flex-1 min-w-0">
                                                            <span class="w-2.5 h-2.5 rounded-full {{ $cls['dot'] }} flex-shrink-0"></span>
                                                            <span class="text-charcoal/85 truncate">{{ $alloc['label'] ?? '' }}</span>
                                                        </span>
                                                        <span class="font-serif font-bold {{ $cls['text'] }}">
                                                            {{ (int) ($alloc['percentage'] ?? 0) }}%
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- CTA --}}
                                    <a href="#?campaign=camp_01KT1CQZWEEX5SMARBECPAAA3T"
                                        class="group/btn inline-flex items-center justify-center gap-2 mt-auto w-full px-6 py-4 {{ $featured ? 'bg-deep-green text-white hover:bg-deep-green/90' : 'bg-white text-deep-green border-2 border-deep-green hover:bg-deep-green hover:text-white' }} font-black text-xs tracking-[0.35em] uppercase rounded-full transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                                        <span class="material-symbols-outlined text-base {{ $featured ? 'text-vibrant-lime' : 'group-hover/btn:text-vibrant-lime' }}">favorite</span>
                                        {{ __('messages.packages_sponsor_btn') }}
                                        <span class="material-symbols-outlined text-base group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                                    </a>
                                </div>
                            </article>
                        @endforeach

                    </div>

                    {{-- Handwritten subline --}}
                    <p class="text-center mt-10 md:mt-14 font-handwriting text-xl md:text-2xl text-charcoal/55">
                        ~ {{ __('messages.packages_subline') }} ~
                    </p>
                </div>
            </section>
        @endif

        {{-- ===== WALL OF SUPPORTERS ===== --}}
        <section id="sponsors" class="relative py-24 md:py-32 px-6 md:px-12 lg:px-24 overflow-hidden scroll-mt-20"
            style="background: #fafaf5;">

            {{-- Paper grain --}}
            <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
                style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;">
            </div>
            <span class="material-symbols-outlined absolute top-12 right-8 text-vibrant-lime/10 text-[10rem] select-none pointer-events-none floating hidden md:block">volunteer_activism</span>
            <span class="material-symbols-outlined absolute bottom-16 left-8 text-gold-accent/10 text-[8rem] select-none pointer-events-none floating hidden lg:block" style="animation-delay: 3s;">forest</span>

            <div class="relative z-10 max-w-7xl mx-auto">

                {{-- Editorial header --}}
                <div class="text-center max-w-3xl mx-auto mb-14 md:mb-20">
                    <p class="font-handwriting text-2xl md:text-3xl text-vibrant-lime mb-2 leading-none">
                        ~ {{ __('messages.transparency_recognition') }} ~
                    </p>
                    <div class="inline-flex items-center gap-3 mb-5">
                        <div class="h-[1px] w-10 bg-gold-accent/50"></div>
                        <span class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">{{ __('messages.community_of_support') }}</span>
                        <div class="h-[1px] w-10 bg-gold-accent/50"></div>
                    </div>
                    <h2 class="text-4xl sm:text-5xl md:text-6xl font-serif text-deep-green leading-[1.05]">
                        {{ __('messages.all_our_donators') }}
                    </h2>
                </div>

                @if ($verifiedDonators->count() > 0)
                    {{-- Supporter cards: photo · name · impact --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                        @foreach ($verifiedDonators as $donator)
                            <article class="group relative bg-white border border-deep-green/10 rounded-[1.75rem] p-8 text-center shadow-[0_10px_30px_rgba(6,46,34,0.06)] hover:shadow-[0_22px_55px_rgba(6,46,34,0.14)] hover:-translate-y-1.5 transition-all duration-500 overflow-hidden">

                                {{-- Top accent --}}
                                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-vibrant-lime via-deep-green to-gold-accent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                {{-- Portrait --}}
                                <div class="relative mx-auto mb-6 w-24 h-24">
                                    <div class="absolute -inset-1.5 rounded-full bg-gradient-to-br from-gold-accent/30 to-vibrant-lime/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    @php
                                        $fallbackAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($donator->full_name)
                                            . '&size=200&background=064e3b&color=ffffff&bold=true&font-size=0.4';
                                        $avatarSrc = $donator->profile_image
                                            ? asset('storage/' . ltrim($donator->profile_image, '/'))
                                            : $fallbackAvatar;
                                    @endphp
                                    <img src="{{ $avatarSrc }}"
                                        alt="{{ $donator->full_name }}"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ $fallbackAvatar }}';"
                                        class="relative w-24 h-24 rounded-full object-cover bg-deep-green/5 ring-2 ring-gold-accent/20 group-hover:ring-gold-accent/50 transition-all duration-500">
                                    {{-- Verified seal --}}
                                    <span class="absolute -bottom-1 -end-1 w-7 h-7 rounded-full bg-vibrant-lime text-deep-green flex items-center justify-center shadow-md ring-2 ring-white">
                                        <span class="material-symbols-outlined text-base">verified</span>
                                    </span>
                                </div>

                                {{-- Name --}}
                                <h3 class="font-serif text-xl md:text-2xl font-bold text-deep-green leading-tight mb-3">
                                    {{ $donator->full_name }}
                                </h3>

                                {{-- Decorative divider --}}
                                <div class="flex items-center justify-center gap-2 mb-4 text-gold-accent/50">
                                    <span class="h-px w-6 bg-gold-accent/30"></span>
                                    <span class="material-symbols-outlined text-sm">eco</span>
                                    <span class="h-px w-6 bg-gold-accent/30"></span>
                                </div>

                                {{-- Impact --}}
                                <p class="text-charcoal/70 text-sm leading-relaxed font-light italic">
                                    {{ $donator->impact ?: __('messages.no_featured_donators') }}
                                </p>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($verifiedDonators->hasPages())
                        <div class="mt-14 flex justify-center">
                            {{ $verifiedDonators->onEachSide(1)->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-16">
                        <span class="material-symbols-outlined text-deep-green/20 text-6xl mb-3">groups</span>
                        <p class="text-charcoal/60">{{ __('messages.no_featured_donators') }}</p>
                    </div>
                @endif

                {{-- Closing note --}}
                <p class="text-center mt-14 md:mt-16 font-handwriting text-xl md:text-2xl text-charcoal/55">
                    ~ {{ __('messages.donors_inscription') }} ~
                </p>
            </div>
        </section>
    </main>

@endsection
