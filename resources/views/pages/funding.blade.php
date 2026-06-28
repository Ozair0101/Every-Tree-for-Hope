@extends('layouts.layout')
@section('title', __('messages.fund_page_title'))
@section('content')

    {{--
        FUNDING MODEL — donor-facing summary of the Investment Framework.
        Captures the core idea of the PDF: the 50/30/20 allocation, the
        cost-per-tree engine, the two site tiers, and the transparency principles.
    --}}

    <main class="flex-grow bg-[#fafaf5] dark:bg-background-dark">

        {{-- ───────────────────────── Hero ───────────────────────── --}}
        <section class="relative overflow-hidden bg-deep-green">
            <div class="absolute inset-0 opacity-25"
                style="background-image:radial-gradient(circle at 15% 20%, rgba(132,204,22,.45) 0, transparent 38%),radial-gradient(circle at 85% 10%, rgba(212,175,55,.35) 0, transparent 40%);">
            </div>
            <div class="relative max-w-5xl mx-auto px-6 pt-20 pb-24 text-center">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-[11px] font-bold uppercase tracking-[0.18em] backdrop-blur-sm mb-6">
                    <span class="material-symbols-outlined text-sm">account_balance</span>
                    {{ __('messages.fund_hero_badge') }}
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif text-white leading-[1.1] mb-5">
                    {{ __('messages.fund_hero_title') }}
                </h1>
                <p class="text-lg text-white/80 max-w-2xl mx-auto mb-12">
                    {{ __('messages.fund_hero_subtitle') }}
                </p>

                {{-- Empirical foundation stats --}}
                <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-vibrant-lime mb-5">{{ __('messages.fund_empirical_label') }}</p>
                <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-4 text-white">
                    <div class="text-center">
                        <div class="text-3xl font-extrabold">51</div>
                        <div class="text-xs uppercase tracking-wider text-white/60">{{ __('messages.fund_stat_events') }}</div>
                    </div>
                    <div class="hidden sm:block h-10 w-px bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-3xl font-extrabold">2,500+</div>
                        <div class="text-xs uppercase tracking-wider text-white/60">{{ __('messages.fund_stat_trees') }}</div>
                    </div>
                    <div class="hidden sm:block h-10 w-px bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-3xl font-extrabold">$3</div>
                        <div class="text-xs uppercase tracking-wider text-white/60">{{ __('messages.fund_stat_base') }}</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ─────────────── Principles ─────────────── --}}
        <section class="max-w-6xl mx-auto px-6 py-20">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <div class="inline-flex items-center gap-3 mb-5">
                    <div class="h-px w-10 bg-vibrant-lime"></div>
                    <span class="text-vibrant-lime font-bold tracking-[0.3em] text-xs uppercase">{{ __('messages.fund_principles_title') }}</span>
                    <div class="h-px w-10 bg-vibrant-lime"></div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([
                    ['rule', 'fund_principle_1_title', 'fund_principle_1_desc'],
                    ['water_drop', 'fund_principle_2_title', 'fund_principle_2_desc'],
                    ['handshake', 'fund_principle_3_title', 'fund_principle_3_desc'],
                ] as [$icon, $title, $desc])
                    <div class="bg-white dark:bg-white/5 rounded-2xl p-7 border border-black/5 shadow-sm">
                        <div class="h-12 w-12 rounded-xl bg-deep-green/10 flex items-center justify-center mb-5">
                            <span class="material-symbols-outlined text-deep-green text-2xl">{{ $icon }}</span>
                        </div>
                        <h3 class="font-serif text-xl text-deep-green mb-2">{{ __('messages.' . $title) }}</h3>
                        <p class="text-sm text-charcoal/70 leading-relaxed">{{ __('messages.' . $desc) }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ─────────────── 50 / 30 / 20 Allocation ─────────────── --}}
        <section class="bg-white dark:bg-white/[0.03] border-y border-black/5 py-20">
            <div class="max-w-6xl mx-auto px-6">
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-deep-green mb-3">{{ __('messages.fund_allocation_title') }}</h2>
                    <p class="text-charcoal/70">{{ __('messages.fund_allocation_subtitle') }}</p>
                </div>

                {{-- proportional bar --}}
                <div class="flex h-4 w-full rounded-full overflow-hidden mb-10 shadow-inner">
                    <div class="bg-deep-green" style="width:50%"></div>
                    <div class="bg-gold-accent" style="width:30%"></div>
                    <div class="bg-vibrant-lime" style="width:20%"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ([
                        ['50%', 'bg-deep-green', 'forest', 'fund_alloc_1_title', 'fund_alloc_1_desc'],
                        ['30%', 'bg-gold-accent', 'water_drop', 'fund_alloc_2_title', 'fund_alloc_2_desc'],
                        ['20%', 'bg-vibrant-lime', 'diversity_3', 'fund_alloc_3_title', 'fund_alloc_3_desc'],
                    ] as [$pct, $bg, $icon, $title, $desc])
                        <div class="relative bg-[#fafaf5] dark:bg-white/5 rounded-2xl p-7 border border-black/5 overflow-hidden">
                            <div class="absolute top-0 left-0 h-1 w-full {{ $bg }}"></div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="material-symbols-outlined text-3xl text-deep-green/70">{{ $icon }}</span>
                                <span class="text-4xl font-black text-deep-green tabular-nums">{{ $pct }}</span>
                            </div>
                            <h3 class="font-serif text-xl text-deep-green mb-2">{{ __('messages.' . $title) }}</h3>
                            <p class="text-sm text-charcoal/70 leading-relaxed">{{ __('messages.' . $desc) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ─────────────── Cost estimation engine ─────────────── --}}
        <section class="max-w-5xl mx-auto px-6 py-20">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-deep-green mb-3">{{ __('messages.fund_engine_title') }}</h2>
                <p class="text-charcoal/70">{{ __('messages.fund_engine_subtitle') }}</p>
            </div>

            <div class="bg-deep-green rounded-3xl p-10 text-center text-white mb-10 shadow-xl">
                <div class="font-serif text-3xl md:text-4xl mb-6 flex items-center justify-center gap-3 flex-wrap">
                    <span class="italic">C<sub class="text-lg">total</sub></span>
                    <span class="text-vibrant-lime">=</span>
                    <span class="italic">P<sub class="text-lg">base</sub></span>
                    <span class="text-vibrant-lime">×</span>
                    <span class="italic">F<sub class="text-lg">site</sub></span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm max-w-2xl mx-auto">
                    <div class="bg-white/10 rounded-xl px-4 py-3"><span class="font-bold">C<sub>total</sub></span> — {{ __('messages.fund_engine_ctotal') }}</div>
                    <div class="bg-white/10 rounded-xl px-4 py-3"><span class="font-bold">P<sub>base</sub></span> — {{ __('messages.fund_engine_pbase') }} ($3.00)</div>
                    <div class="bg-white/10 rounded-xl px-4 py-3"><span class="font-bold">F<sub>site</sub></span> — {{ __('messages.fund_engine_fsite') }}</div>
                </div>
            </div>

            {{-- Two tier cards --}}
            <h3 class="text-center font-serif text-2xl text-deep-green mb-8">{{ __('messages.fund_tiers_title') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-white/5 rounded-2xl p-7 border border-black/5 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-block px-3 py-1 rounded-full bg-deep-green/10 text-deep-green text-xs font-bold uppercase tracking-wide">{{ __('messages.fund_tier1_factor') }}</span>
                        <div class="text-right">
                            <span class="text-3xl font-black text-deep-green">$3.00</span>
                            <span class="block text-[11px] text-charcoal/50 uppercase tracking-wide">{{ __('messages.fund_per_tree') }}</span>
                        </div>
                    </div>
                    <h4 class="font-serif text-xl text-deep-green mb-2">{{ __('messages.fund_tier1_name') }}</h4>
                    <p class="text-sm text-charcoal/70 leading-relaxed">{{ __('messages.fund_tier1_desc') }}</p>
                </div>
                <div class="bg-white dark:bg-white/5 rounded-2xl p-7 border-2 border-gold-accent/40 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-block px-3 py-1 rounded-full bg-gold-accent/15 text-gold-accent text-xs font-bold uppercase tracking-wide">{{ __('messages.fund_tier2_factor') }}</span>
                        <div class="text-right">
                            <span class="text-3xl font-black text-gold-accent">$6.00</span>
                            <span class="block text-[11px] text-charcoal/50 uppercase tracking-wide">{{ __('messages.fund_per_tree') }}</span>
                        </div>
                    </div>
                    <h4 class="font-serif text-xl text-deep-green mb-2">{{ __('messages.fund_tier2_name') }}</h4>
                    <p class="text-sm text-charcoal/70 leading-relaxed">{{ __('messages.fund_tier2_desc') }}</p>
                </div>
            </div>
        </section>

        {{-- ─────────────── Itemized breakdown table ─────────────── --}}
        <section class="bg-white dark:bg-white/[0.03] border-y border-black/5 py-20">
            <div class="max-w-4xl mx-auto px-6">
                <h2 class="text-center text-3xl md:text-4xl font-serif font-bold text-deep-green mb-10">{{ __('messages.fund_breakdown_title') }}</h2>
                <div class="overflow-x-auto rounded-2xl border border-black/5 shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-deep-green text-white text-start">
                                <th class="px-5 py-4 text-start font-bold">{{ __('messages.fund_th_category') }}</th>
                                <th class="px-5 py-4 text-end font-bold">{{ __('messages.fund_th_tier1') }}</th>
                                <th class="px-5 py-4 text-end font-bold">{{ __('messages.fund_th_tier2') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/5">
                            <tr class="bg-white dark:bg-transparent">
                                <td class="px-5 py-4 font-bold text-deep-green">{{ __('messages.fund_row1') }}</td>
                                <td class="px-5 py-4 text-end tabular-nums">$1.50</td>
                                <td class="px-5 py-4 text-end tabular-nums">$3.00 <span class="text-charcoal/40 text-xs">({{ __('messages.fund_note_soil') }})</span></td>
                            </tr>
                            <tr class="bg-[#fafaf5] dark:bg-white/5">
                                <td class="px-5 py-4 font-bold text-deep-green">{{ __('messages.fund_row2') }}</td>
                                <td class="px-5 py-4 text-end tabular-nums">$0.90 <span class="text-charcoal/40 text-xs">({{ __('messages.fund_note_local') }})</span></td>
                                <td class="px-5 py-4 text-end tabular-nums">$1.80 <span class="text-charcoal/40 text-xs">({{ __('messages.fund_note_tanker') }})</span></td>
                            </tr>
                            <tr class="bg-white dark:bg-transparent">
                                <td class="px-5 py-4 font-bold text-deep-green">{{ __('messages.fund_row3') }}</td>
                                <td class="px-5 py-4 text-end tabular-nums">$0.60</td>
                                <td class="px-5 py-4 text-end tabular-nums">$1.20 <span class="text-charcoal/40 text-xs">({{ __('messages.fund_note_labor') }})</span></td>
                            </tr>
                            <tr class="bg-deep-green/5 font-black text-deep-green">
                                <td class="px-5 py-4">{{ __('messages.fund_total') }}</td>
                                <td class="px-5 py-4 text-end tabular-nums">$3.00</td>
                                <td class="px-5 py-4 text-end tabular-nums">$6.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-start gap-3 mt-8 rounded-2xl bg-vibrant-lime/10 border border-vibrant-lime/30 px-5 py-4">
                    <span class="material-symbols-outlined text-deep-green">verified</span>
                    <p class="text-sm text-charcoal/80">{{ __('messages.fund_trust') }}</p>
                </div>
            </div>
        </section>

        {{-- ─────────────── CTA ─────────────── --}}
        <section class="max-w-4xl mx-auto px-6 py-20 text-center">
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-deep-green mb-4">{{ __('messages.fund_cta_title') }}</h2>
            <p class="text-charcoal/70 max-w-xl mx-auto mb-9">{{ __('messages.fund_cta_text') }}</p>
            <div class="flex justify-center">
                @include('partials.donate-button', ['label' => __('messages.donate_cta_support')])
            </div>
        </section>
    </main>
@endsection
