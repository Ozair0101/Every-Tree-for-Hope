@extends('layouts.layout')
@section('title', __('messages.partners_page_title'))
@section('content')

@php
    use App\Enums\PartnerType;
    $typeMeta = [
        PartnerType::SPONSOR->value => [
            'label_key' => 'partner_type_sponsor',
            'ring' => 'rgba(212,175,55,0.55)',
            'pill_bg' => '#d4af37',
            'pill_text' => '#1a3d2e',
            'dot' => 'bg-gold-accent',
        ],
        PartnerType::COLLABORATOR->value => [
            'label_key' => 'partner_type_collaborator',
            'ring' => 'rgba(132,204,22,0.55)',
            'pill_bg' => '#84cc16',
            'pill_text' => '#0a2a1d',
            'dot' => 'bg-vibrant-lime',
        ],
        PartnerType::SUPPORTER->value => [
            'label_key' => 'partner_type_supporter',
            'ring' => 'rgba(59,130,246,0.55)',
            'pill_bg' => '#3b82f6',
            'pill_text' => '#ffffff',
            'dot' => 'bg-blue-500',
        ],
        PartnerType::OTHER->value => [
            'label_key' => 'partner_type_other',
            'ring' => 'rgba(107,114,128,0.5)',
            'pill_bg' => '#6b7280',
            'pill_text' => '#ffffff',
            'dot' => 'bg-gray-500',
        ],
    ];
    $totalPartners = $partners->count();
@endphp

<main>
    {{-- ===== HERO — Editorial paper-cream introduction ===== --}}
    <section class="relative w-full overflow-hidden" style="background: #fafaf5;">
        {{-- Paper grain --}}
        <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
             style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;"></div>

        {{-- Floating botanicals --}}
        <span class="material-symbols-outlined absolute text-vibrant-lime/15 floating pointer-events-none select-none"
              style="top:8%; {{ $is_rtl ? 'right' : 'left' }}:4%; font-size:120px;">forest</span>
        <span class="material-symbols-outlined absolute text-gold-accent/15 floating pointer-events-none select-none"
              style="bottom:10%; {{ $is_rtl ? 'left' : 'right' }}:6%; font-size:96px; animation-delay:1.4s;">handshake</span>
        <span class="material-symbols-outlined absolute text-deep-green/10 floating pointer-events-none select-none"
              style="top:38%; {{ $is_rtl ? 'left' : 'right' }}:10%; font-size:64px; animation-delay:2.6s;">eco</span>

        <div class="relative z-10 max-w-7xl mx-auto px-6 py-20 md:py-28">

            {{-- Editorial header --}}
            <div class="text-center max-w-3xl mx-auto">
                <p class="font-handwriting text-vibrant-lime text-2xl md:text-3xl mb-3 leading-none">
                    ~ {{ __('messages.partners_eyebrow') }} ~
                </p>
                <div class="inline-flex items-center gap-3 mb-5">
                    <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                    <span class="text-vibrant-lime font-bold tracking-[0.35em] text-[11px] uppercase">
                        {{ __('messages.partners_kicker') }}
                    </span>
                    <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-serif font-bold text-deep-green leading-[1.02] mb-5">
                    {!! __('messages.partners_title') !!}
                </h1>
                <div class="flex justify-center mb-6">
                    <svg width="240" height="14" viewBox="0 0 240 14" fill="none">
                        <path d="M3,9 Q60,2 120,6 T237,5" fill="none" stroke="#84cc16" stroke-width="3" stroke-linecap="round" />
                    </svg>
                </div>
                <p class="text-charcoal/70 text-base md:text-lg leading-relaxed">
                    {{ __('messages.partners_description') }}
                </p>
            </div>

            {{-- Stats strip --}}
            <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 max-w-4xl mx-auto">
                @foreach ($typeMeta as $typeValue => $meta)
                    @php
                        $key = strtolower(class_basename(PartnerType::class)) . '_' . $typeValue;
                        $count = $countsByType[strtolower($typeValue)] ?? 0;
                    @endphp
                    <div class="relative bg-white border border-deep-green/10 rounded-2xl px-4 py-5 text-center shadow-[0_8px_24px_rgba(6,46,34,0.05)]">
                        <span class="absolute top-3 left-3 inline-block w-2 h-2 rounded-full" style="background: {{ $meta['pill_bg'] }};"></span>
                        <p class="font-serif font-black text-deep-green tabular-nums leading-none" style="font-size: clamp(1.75rem, 4vw, 2.5rem);">
                            {{ $count }}
                        </p>
                        <p class="text-charcoal/55 text-[10px] md:text-xs font-bold uppercase tracking-[0.25em] mt-2">
                            {{ __('messages.' . $meta['label_key']) }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Filter pills --}}
            @if ($totalPartners > 0)
                <div class="mt-10 flex flex-wrap items-center justify-center gap-2 partner-filters">
                    <button type="button" data-filter="all"
                        class="partner-filter-btn is-active px-4 py-2 rounded-full text-xs font-bold uppercase tracking-[0.25em] transition-all bg-deep-green text-white shadow-md">
                        {{ __('messages.partners_filter_all') }} · {{ $totalPartners }}
                    </button>
                    @foreach ($typeMeta as $typeValue => $meta)
                        @php $c = $countsByType[strtolower($typeValue)] ?? 0; @endphp
                        @if ($c > 0)
                            <button type="button" data-filter="{{ $typeValue }}"
                                class="partner-filter-btn px-4 py-2 rounded-full text-xs font-bold uppercase tracking-[0.25em] transition-all bg-white text-deep-green border border-deep-green/15 hover:border-deep-green/40">
                                <span class="inline-block w-1.5 h-1.5 rounded-full me-1.5 align-middle" style="background: {{ $meta['pill_bg'] }};"></span>
                                {{ __('messages.' . $meta['label_key']) }} · {{ $c }}
                            </button>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ===== FEATURED PARTNER SPOTLIGHT ===== --}}
    @if ($featured)
        <section class="relative py-16 md:py-20 px-6" style="background: linear-gradient(180deg, #fafaf5 0%, #f0f4ea 100%);">
            <div class="absolute inset-0 pointer-events-none opacity-[0.4]"
                 style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;"></div>

            <div class="relative z-10 max-w-6xl mx-auto">
                <p class="text-center font-handwriting text-vibrant-lime text-xl md:text-2xl mb-2">
                    ~ {{ __('messages.partners_spotlight_eyebrow') }} ~
                </p>

                @php
                    $meta = $typeMeta[$featured->type instanceof PartnerType ? $featured->type->value : $featured->type] ?? $typeMeta[PartnerType::SPONSOR->value];
                @endphp

                <div class="relative mx-auto max-w-4xl mt-6">
                    {{-- Tape strips --}}
                    <div class="absolute -top-4 left-10 w-28 h-7 rotate-[-6deg] z-30 pointer-events-none"
                         style="background: {{ $meta['ring'] }}; box-shadow: 0 2px 6px rgba(0,0,0,0.08);"></div>
                    <div class="absolute -top-4 right-10 w-28 h-7 rotate-[5deg] z-30 pointer-events-none"
                         style="background: rgba(132,204,22,0.45); box-shadow: 0 2px 6px rgba(0,0,0,0.08);"></div>

                    <div class="relative bg-white rounded-sm p-6 md:p-10 grid grid-cols-1 md:grid-cols-12 gap-8 items-center"
                         style="box-shadow: 0 30px 60px -20px rgba(6,46,34,0.25), 0 8px 24px -8px rgba(0,0,0,0.12);">

                        {{-- Logo medallion --}}
                        <div class="md:col-span-4 flex justify-center">
                            <div class="relative">
                                <div class="absolute -inset-3 rounded-full"
                                     style="background: radial-gradient(circle, {{ $meta['ring'] }} 0%, transparent 70%);"></div>
                                <div class="relative w-40 h-40 md:w-48 md:h-48 rounded-full bg-white border-4 flex items-center justify-center overflow-hidden"
                                     style="border-color: {{ $meta['pill_bg'] }}; box-shadow: 0 10px 30px rgba(6,46,34,0.12);">
                                    <img src="{{ $featured->full_logo_url }}"
                                         alt="{{ $featured->company_name }}"
                                         class="w-3/4 h-3/4 object-contain"
                                         loading="lazy">
                                </div>
                                {{-- "Featured" stamp --}}
                                <div class="absolute -bottom-3 -right-2 rotate-[-8deg] z-10">
                                    <div class="px-2.5 py-1 border-2 border-gold-accent/70 rounded-sm bg-paper-cream"
                                         style="background: rgba(250,250,245,0.95);">
                                        <span class="font-bold tracking-[0.25em] text-[9px] uppercase text-gold-accent">
                                            {{ __('messages.partners_featured_stamp') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="md:col-span-8">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black tracking-[0.3em] uppercase"
                                      style="background: {{ $meta['pill_bg'] }}; color: {{ $meta['pill_text'] }};">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $meta['pill_text'] }};"></span>
                                    {{ __('messages.' . $meta['label_key']) }}
                                </span>
                                @if ($featured->code)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold tracking-[0.25em] uppercase text-charcoal/55 bg-stone-100 border border-stone-200">
                                        <span class="material-symbols-outlined text-[12px]">qr_code_2</span>
                                        {{ $featured->code }}
                                    </span>
                                @endif
                            </div>
                            <h2 class="font-serif text-3xl md:text-4xl font-bold text-deep-green leading-tight mb-3">
                                {{ $featured->company_name }}
                            </h2>
                            @if ($featured->description)
                                <p class="text-charcoal/75 text-base leading-relaxed">
                                    {{ $featured->description }}
                                </p>
                            @endif
                            <div class="mt-5 flex items-center gap-2 text-charcoal/40">
                                <span class="material-symbols-outlined text-base text-vibrant-lime">eco</span>
                                <span class="font-handwriting text-deep-green text-lg leading-none">
                                    {{ __('messages.partners_spotlight_caption') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ===== THE ROSTER ===== --}}
    <section class="relative py-20 md:py-28 px-6" style="background: #fafaf5;">
        <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
             style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;"></div>

        <div class="relative z-10 max-w-7xl mx-auto">

            <div class="text-center max-w-2xl mx-auto mb-12">
                <div class="inline-flex items-center gap-3 mb-4">
                    <div class="h-[1px] w-10 bg-gold-accent/60"></div>
                    <span class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">{{ __('messages.partners_roster_kicker') }}</span>
                    <div class="h-[1px] w-10 bg-gold-accent/60"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-deep-green leading-tight">
                    {{ __('messages.partners_roster_title') }}
                </h2>
            </div>

            @if ($totalPartners > 0)
                <div id="partner-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
                    @foreach ($partners as $partner)
                        @php
                            $typeValue = $partner->type instanceof PartnerType ? $partner->type->value : $partner->type;
                            $meta = $typeMeta[$typeValue] ?? $typeMeta[PartnerType::SPONSOR->value];
                            $tiltAngles = [-2.5, 1.8, -1.2, 2.4, -0.8, 1.6];
                            $tilt = $tiltAngles[$loop->index % count($tiltAngles)];
                        @endphp
                        <article class="partner-card group relative" data-type="{{ $typeValue }}"
                                 style="transform: rotate({{ $tilt }}deg); transition: transform .35s ease;">
                            <div class="relative bg-white rounded-sm pt-5 px-5 pb-12 flex flex-col items-center
                                        shadow-[0_12px_30px_rgba(6,46,34,0.08)]
                                        group-hover:shadow-[0_22px_50px_rgba(6,46,34,0.18)]
                                        transition-shadow duration-500"
                                 style="border-top: 3px solid {{ $meta['pill_bg'] }};">

                                {{-- Tape strip on hover --}}
                                <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-20 h-5 rotate-[-3deg] opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
                                     style="background: {{ $meta['ring'] }}; box-shadow: 0 2px 4px rgba(0,0,0,0.08);"></div>

                                {{-- Type pill, top-right --}}
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-black tracking-[0.25em] uppercase"
                                          style="background: {{ $meta['pill_bg'] }}; color: {{ $meta['pill_text'] }};">
                                        {{ __('messages.' . $meta['label_key']) }}
                                    </span>
                                </div>

                                {{-- Logo well --}}
                                <div class="w-28 h-28 md:w-32 md:h-32 my-3 flex items-center justify-center">
                                    <img src="{{ $partner->full_logo_url }}"
                                         alt="{{ $partner->company_name }}"
                                         class="max-w-full max-h-full object-contain transition-transform duration-500 group-hover:scale-105"
                                         loading="lazy">
                                </div>

                                {{-- Hand-drawn separator --}}
                                <svg width="60" height="6" viewBox="0 0 60 6" class="mb-2">
                                    <path d="M2,4 Q15,1 30,3 T58,2" fill="none" stroke="{{ $meta['pill_bg'] }}" stroke-width="1.5" stroke-linecap="round" opacity="0.5" />
                                </svg>

                                {{-- Name --}}
                                <h3 class="font-serif font-bold text-deep-green text-center text-base md:text-lg leading-snug mb-2 px-1">
                                    {{ $partner->company_name }}
                                </h3>

                                {{-- Code (if any) --}}
                                @if ($partner->code)
                                    <p class="text-charcoal/40 text-[10px] font-bold tracking-[0.25em] uppercase">
                                        {{ $partner->code }}
                                    </p>
                                @endif

                                {{-- Description (reveal on hover) --}}
                                @if ($partner->description)
                                    <div class="absolute inset-x-0 bottom-0 px-5 py-4 bg-gradient-to-t from-deep-green via-deep-green/95 to-deep-green/0
                                                rounded-b-sm opacity-0 group-hover:opacity-100 transition-opacity duration-400 pointer-events-none">
                                        <p class="text-white/95 text-xs leading-relaxed font-light line-clamp-3">
                                            {{ $partner->description }}
                                        </p>
                                    </div>
                                @endif

                                {{-- Bottom handwritten signature line --}}
                                <span class="absolute bottom-2 left-1/2 -translate-x-1/2 font-handwriting text-charcoal/30 text-xs whitespace-nowrap pointer-events-none group-hover:opacity-0 transition-opacity">
                                    {{ __('messages.partners_card_signature') }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Empty filter state --}}
                <div id="partner-empty" class="hidden text-center py-12">
                    <span class="material-symbols-outlined text-charcoal/30 text-5xl">search_off</span>
                    <p class="font-handwriting text-deep-green text-xl mt-3">
                        {{ __('messages.partners_filter_empty') }}
                    </p>
                </div>
            @else
                {{-- No partners yet --}}
                <div class="text-center py-20">
                    <span class="material-symbols-outlined text-deep-green/20 text-7xl">handshake</span>
                    <p class="font-handwriting text-deep-green text-2xl mt-4">
                        {{ __('messages.partners_empty_title') }}
                    </p>
                    <p class="text-charcoal/60 max-w-md mx-auto mt-2">
                        {{ __('messages.partners_empty_desc') }}
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== CTA — Become a partner ===== --}}
    <section class="relative py-20 px-6 overflow-hidden" style="background: linear-gradient(135deg, #064e3b 0%, #0a2a1d 100%);">
        <div class="absolute inset-0 pointer-events-none opacity-[0.06]"
             style="background-image: radial-gradient(rgba(212,175,55,0.6) 1px, transparent 1px); background-size: 24px 24px;"></div>
        <span class="material-symbols-outlined absolute -top-6 left-6 text-gold-accent/10 text-[200px] select-none pointer-events-none">park</span>
        <span class="material-symbols-outlined absolute -bottom-10 right-4 text-vibrant-lime/10 text-[220px] select-none pointer-events-none">forest</span>

        <div class="relative z-10 max-w-3xl mx-auto text-center">
            <p class="font-handwriting text-gold-accent text-2xl md:text-3xl mb-3">
                ~ {{ __('messages.partners_cta_eyebrow') }} ~
            </p>
            <h2 class="text-3xl md:text-5xl font-serif font-bold text-white leading-tight mb-5">
                {!! __('messages.partners_cta_title') !!}
            </h2>
            <p class="text-white/70 text-base md:text-lg leading-relaxed mb-8">
                {{ __('messages.partners_cta_desc') }}
            </p>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center gap-2 px-7 py-4 rounded-full bg-vibrant-lime text-deep-green font-black text-xs tracking-[0.35em] uppercase shadow-lg hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                <span class="material-symbols-outlined text-base">handshake</span>
                {{ __('messages.partners_cta_button') }}
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>
    </section>
</main>

<style>
    .partner-filter-btn.is-active {
        background: #064e3b !important;
        color: #ffffff !important;
        border-color: #064e3b !important;
    }
</style>

<script>
    (function () {
        const buttons = document.querySelectorAll('.partner-filter-btn');
        const cards = document.querySelectorAll('.partner-card');
        const emptyState = document.getElementById('partner-empty');
        if (!buttons.length || !cards.length) return;

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;
                buttons.forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');

                let visible = 0;
                cards.forEach(card => {
                    const show = (filter === 'all') || (card.dataset.type === filter);
                    if (show) {
                        card.style.display = '';
                        visible++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                if (emptyState) emptyState.classList.toggle('hidden', visible !== 0);
            });
        });
    })();
</script>
@endsection
