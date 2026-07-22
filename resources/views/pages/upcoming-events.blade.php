@extends('layouts.layout')
@section('title', __('messages.nav_upcoming_events'))
@section('content')

    <main class="flex-grow" style="background: #fafaf5;">

        {{-- ===== Hero ===== --}}
        <section class="relative overflow-hidden bg-deep-green">
            {{-- Background photo --}}
            <img src="{{ asset('images/60.jpeg') }}" alt="" aria-hidden="true"
                class="absolute inset-0 w-full h-full object-cover select-none pointer-events-none">
            {{-- Readability overlay --}}
            <div class="absolute inset-0 bg-gradient-to-b from-deep-green/85 via-deep-green/80 to-deep-green/95"></div>
            <div class="absolute inset-0 opacity-30"
                style="background-image:radial-gradient(circle at 85% 10%, rgba(170,221,90,.45) 0, transparent 45%);"></div>
            <span class="material-symbols-outlined absolute -bottom-10 -end-6 text-white/5 select-none pointer-events-none"
                style="font-size: 16rem;">edit_calendar</span>

            <div class="relative max-w-4xl mx-auto px-6 pt-16 pb-20 text-center">
                <p class="font-handwriting text-2xl md:text-3xl text-vibrant-lime mb-2 leading-none">
                    ~ {{ __('messages.future_eyebrow') }} ~
                </p>
                <div class="inline-flex items-center gap-3 mb-5">
                    <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                    <span class="text-vibrant-lime font-bold tracking-[0.4em] text-[10px] uppercase">
                        {{ __('messages.future_kicker') }}
                    </span>
                    <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                </div>
                <h1 class="text-3xl md:text-5xl font-serif font-bold text-white leading-tight mb-4 text-shadow">
                    {{ __('messages.nav_upcoming_events') }}
                </h1>
                <p class="text-white/80 max-w-2xl mx-auto leading-relaxed mb-9">
                    {{ __('messages.future_description') }}
                </p>

                {{-- Keyword search --}}
                <form method="GET" action="{{ route('upcoming-events.index') }}" role="search"
                    class="relative max-w-xl mx-auto">
                    <span class="material-symbols-outlined absolute start-5 top-1/2 -translate-y-1/2 text-deep-green/40 text-xl pointer-events-none">search</span>
                    <input type="search" name="q" value="{{ $search }}" autocomplete="off"
                        placeholder="{{ __('messages.upcoming_search_placeholder') }}"
                        class="w-full bg-white rounded-full ps-14 pe-32 py-4 text-deep-green placeholder:text-charcoal/40 shadow-xl shadow-black/10 focus:ring-4 focus:ring-white/30 focus:outline-none transition-all">
                    <button type="submit"
                        class="absolute end-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-1.5 px-5 py-2.5 bg-deep-green text-white text-sm font-bold rounded-full hover:bg-deep-green/90 transition-colors">
                        <span class="material-symbols-outlined text-base">search</span>
                        <span class="hidden sm:inline">{{ __('messages.upcoming_search_btn') }}</span>
                    </button>
                </form>

                <p class="mt-6 text-sm text-white/70">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full rounded-full bg-vibrant-lime opacity-60 animate-ping"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-vibrant-lime"></span>
                        </span>
                        {{ trans_choice('messages.upcoming_count', $totalUpcoming, ['count' => $totalUpcoming]) }}
                    </span>
                </p>
            </div>
        </section>

        {{-- ===== Grid ===== --}}
        <section class="max-w-6xl mx-auto px-6 py-12 md:py-16">
            @if ($search !== '')
                <div class="flex flex-wrap items-center justify-between gap-3 mb-8 pb-5 border-b border-deep-green/10">
                    <p class="text-charcoal/70 text-sm">
                        {!! __('messages.upcoming_results_for', ['count' => $events->total(), 'term' => e($search)]) !!}
                    </p>
                    <a href="{{ route('upcoming-events.index') }}"
                        class="inline-flex items-center gap-1.5 text-deep-green text-xs font-bold uppercase tracking-widest hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-base">close</span>
                        {{ __('messages.upcoming_clear_search') }}
                    </a>
                </div>
            @endif

            @if ($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @foreach ($events as $event)
                        @php
                            $imgs = is_array($event->images) ? array_values($event->images) : [];
                            $hero = $imgs[0] ?? null;
                            $species = $event->all_tree_species;

                            $d = (int) \Carbon\Carbon::today()->diffInDays($event->date, false);
                            if ($d === 0) {
                                $away = ['label' => __('messages.future_today'), 'tone' => 'gold'];
                            } elseif ($d === 1) {
                                $away = ['label' => __('messages.future_tomorrow'), 'tone' => 'gold'];
                            } else {
                                $away = ['label' => __('messages.future_in_days', ['count' => $d]), 'tone' => 'lime'];
                            }
                        @endphp

                        <a href="{{ route('upcoming-events.show', $event) }}"
                            class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-deep-green/10 shadow-[0_10px_30px_rgba(6,46,34,0.07)] hover:shadow-[0_20px_50px_rgba(6,46,34,0.14)] hover:-translate-y-1 transition-all duration-500">

                            {{-- Image --}}
                            <div class="relative h-48 bg-deep-green/5 overflow-hidden flex-shrink-0">
                                @if ($hero)
                                    {{-- Blurred fill so the photo shows complete, uncropped --}}
                                    <img src="{{ asset('storage/' . ltrim($hero, '/')) }}" alt="" aria-hidden="true"
                                        class="absolute inset-0 w-full h-full object-cover scale-110 blur-xl opacity-40 select-none pointer-events-none">
                                    <img src="{{ asset('storage/' . ltrim($hero, '/')) }}" alt="{{ $event->title }}"
                                        class="relative w-full h-full object-contain">
                                @else
                                    <span class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-deep-green/15"
                                        style="font-size: 5rem;">forest</span>
                                @endif

                                {{-- Date stamp --}}
                                <div class="absolute top-4 start-4 bg-white/95 backdrop-blur-sm rounded-xl px-3 py-2 text-center shadow-lg">
                                    <p class="text-gold-accent text-[9px] font-bold tracking-[0.3em] uppercase">
                                        {{ $event->date->translatedFormat('M') }}
                                    </p>
                                    <p class="font-serif font-black text-deep-green text-2xl leading-none">
                                        {{ $event->date->translatedFormat('d') }}
                                    </p>
                                </div>

                                {{-- Days away --}}
                                <span class="absolute bottom-3 end-3 inline-flex items-center gap-1 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest
                                    {{ $away['tone'] === 'gold' ? 'bg-gold-accent text-deep-green' : 'bg-vibrant-lime text-deep-green' }}">
                                    {{ $away['label'] }}
                                </span>
                            </div>

                            {{-- Body --}}
                            <div class="p-6 flex flex-col flex-1">
                                <h2 class="font-serif text-xl text-deep-green leading-snug mb-2 group-hover:text-primary transition-colors">
                                    {{ $event->title }}
                                </h2>

                                <p class="text-charcoal/55 text-xs flex items-center gap-1.5 mb-4">
                                    <span class="material-symbols-outlined text-sm text-vibrant-lime">place</span>
                                    {{ $event->location }} · {{ $event->province }}
                                </p>

                                @if ($event->description)
                                    <p class="text-charcoal/70 text-sm leading-relaxed mb-4">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($event->description), 110) }}
                                    </p>
                                @endif

                                @if (count($species))
                                    <div class="flex flex-wrap gap-1.5 mb-4">
                                        @foreach (array_slice($species, 0, 3) as $name)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-vibrant-lime/10 border border-vibrant-lime/25 text-deep-green text-[10px] font-bold">
                                                <span class="material-symbols-outlined text-[13px] text-vibrant-lime">park</span>
                                                {{ $name }}
                                            </span>
                                        @endforeach
                                        @if (count($species) > 3)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-deep-green/5 text-deep-green/70 text-[10px] font-bold">
                                                +{{ count($species) - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <span class="mt-auto inline-flex items-center gap-1.5 text-deep-green text-[11px] font-extrabold tracking-[0.2em] uppercase group-hover:gap-2.5 transition-all">
                                    {{ __('messages.future_details_btn') }}
                                    <span class="material-symbols-outlined text-base rtl:rotate-180">arrow_forward</span>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($events->hasPages())
                    <div class="mt-12">
                        {{ $events->links() }}
                    </div>
                @endif
            @elseif ($search !== '')
                {{-- No search results --}}
                <div class="max-w-md mx-auto text-center bg-white border border-deep-green/10 rounded-2xl shadow-sm p-10">
                    <span class="material-symbols-outlined text-deep-green/20 text-6xl mb-4">search_off</span>
                    <h2 class="text-xl font-serif text-deep-green mb-2">{{ __('messages.upcoming_no_results_title') }}</h2>
                    <p class="text-charcoal/60 text-sm mb-6">{{ __('messages.upcoming_no_results_desc') }}</p>
                    <a href="{{ route('upcoming-events.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-deep-green text-white text-sm font-bold rounded-full hover:bg-deep-green/90 transition-colors">
                        <span class="material-symbols-outlined text-base">restart_alt</span>
                        {{ __('messages.upcoming_clear_search') }}
                    </a>
                </div>
            @else
                {{-- Nothing scheduled at all --}}
                <div class="max-w-md mx-auto text-center bg-white border border-deep-green/10 rounded-2xl shadow-sm p-10">
                    <span class="material-symbols-outlined text-deep-green/20 text-6xl mb-4">event_busy</span>
                    <h2 class="text-xl font-serif text-deep-green mb-2">{{ __('messages.future_none_title') }}</h2>
                    <p class="text-charcoal/60 text-sm mb-6">{{ __('messages.future_none_desc') }}</p>
                    <a href="{{ route('gallery') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-deep-green text-white text-sm font-bold rounded-full hover:bg-deep-green/90 transition-colors">
                        <span class="material-symbols-outlined text-base">photo_library</span>
                        {{ __('messages.works') }}
                    </a>
                </div>
            @endif

            <p class="text-center mt-12 font-handwriting text-xl md:text-2xl text-charcoal/55">
                ~ {{ __('messages.future_closing_note') }} ~
            </p>
        </section>
    </main>
@endsection
