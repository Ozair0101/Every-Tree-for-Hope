@extends('layouts.layout')
@section('title', $event->title)
@section('content')

    @php
        $images = is_array($event->images) ? array_values($event->images) : [];
        $hero = $images[0] ?? null;
        $rest = array_slice($images, 1);
        $species = $event->all_tree_species;

        $daysAway = (int) \Carbon\Carbon::today()->diffInDays($event->date, false);
        if ($daysAway === 0) {
            $away = ['label' => __('messages.future_today'), 'tone' => 'gold'];
        } elseif ($daysAway === 1) {
            $away = ['label' => __('messages.future_tomorrow'), 'tone' => 'gold'];
        } else {
            $away = ['label' => __('messages.future_in_days', ['count' => $daysAway]), 'tone' => 'lime'];
        }

        // Google Maps: accept an embed iframe, a share link, or "lat,lng".
        $mapEmbed = trim((string) ($event->map_embed ?? ''));
        $mapSrc = null;
        $mapLink = null;
        if ($mapEmbed !== '') {
            if (stripos($mapEmbed, '<iframe') !== false && preg_match('/src=["\']([^"\']+)["\']/i', $mapEmbed, $m)) {
                $mapSrc = $m[1];
            } elseif (preg_match('/^\s*(-?\d{1,3}(?:\.\d+)?)\s*,\s*(-?\d{1,3}(?:\.\d+)?)\s*$/', $mapEmbed, $c)) {
                $mapSrc = 'https://maps.google.com/maps?q=' . $c[1] . ',' . $c[2] . '&z=15&output=embed';
                $mapLink = 'https://www.google.com/maps?q=' . $c[1] . ',' . $c[2];
            } elseif (preg_match('#^https?://#i', $mapEmbed)) {
                if (stripos($mapEmbed, '/maps/embed') !== false) {
                    $mapSrc = $mapEmbed;
                } else {
                    $mapLink = $mapEmbed;
                }
            }
        }
    @endphp

    <main class="flex-grow bg-stone-50">

        {{-- ===== Hero ===== --}}
        <section class="relative h-[360px] md:h-[460px] overflow-hidden bg-deep-green">
            @if ($hero)
                {{-- Blurred copy fills the frame so the real image shows complete, uncropped --}}
                <img src="{{ asset('storage/' . ltrim($hero, '/')) }}" alt="" aria-hidden="true"
                    class="absolute inset-0 w-full h-full object-cover scale-110 blur-2xl opacity-40 select-none pointer-events-none">
                <img src="{{ asset('storage/' . ltrim($hero, '/')) }}" alt="{{ $event->title }}"
                    class="relative w-full h-full object-contain">
            @else
                <span class="material-symbols-outlined absolute -bottom-10 -right-6 text-white/10 select-none"
                    style="font-size: 18rem;">forest</span>
            @endif

            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/35 to-transparent"></div>

            {{-- Back link --}}
            <div class="absolute top-4 start-4 z-10">
                <a href="{{ route('upcoming-events.index') }}"
                    class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white border border-white/20 hover:bg-white/20 transition-all rounded-lg px-4 py-2">
                    <span class="material-symbols-outlined rtl:rotate-180">arrow_back</span>
                    <span class="text-sm font-medium">{{ __('messages.future_back_to_events') }}</span>
                </a>
            </div>

            {{-- Title block --}}
            <div class="absolute bottom-0 inset-x-0 p-6 md:p-12 z-10">
                <div class="max-w-6xl mx-auto">
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest
                            {{ $away['tone'] === 'gold' ? 'bg-gold-accent text-deep-green' : 'bg-vibrant-lime text-deep-green' }}">
                            <span class="material-symbols-outlined text-xs">schedule</span>
                            {{ $away['label'] }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 border border-white/20 text-white text-[10px] font-bold uppercase tracking-widest">
                            <span class="material-symbols-outlined text-xs">location_on</span>
                            {{ $event->province }}
                        </span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-serif font-bold text-white leading-tight text-shadow">
                        {{ $event->title }}
                    </h1>
                    <p class="text-white/85 mt-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-sm">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-vibrant-lime">event</span>
                            {{ $event->date->translatedFormat('l, F j, Y') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-vibrant-lime">place</span>
                            {{ $event->location }}
                        </span>
                    </p>
                </div>
            </div>
        </section>

        {{-- ===== Body ===== --}}
        <section class="max-w-6xl mx-auto px-6 py-12 md:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                {{-- Main column --}}
                <div class="lg:col-span-2 space-y-12">

                    {{-- Description --}}
                    <div>
                        <h2 class="text-2xl font-serif text-deep-green mb-4">{{ __('messages.future_about_event') }}</h2>
                        <p class="text-charcoal/75 leading-relaxed whitespace-pre-line">{{ $event->description }}</p>
                    </div>

                    {{-- Tree species --}}
                    @if (count($species))
                        <div>
                            <h2 class="text-2xl font-serif text-deep-green mb-5">{{ __('messages.future_species_title') }}</h2>
                            <div class="flex flex-wrap gap-2.5">
                                @foreach ($species as $name)
                                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-vibrant-lime/30 text-deep-green text-sm font-semibold shadow-sm">
                                        <span class="material-symbols-outlined text-base text-vibrant-lime">park</span>
                                        {{ $name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Google map --}}
                    @if ($mapSrc || $mapLink)
                        <div>
                            <h2 class="text-2xl font-serif text-deep-green mb-5">{{ __('messages.event_location_map') }}</h2>
                            @if ($mapSrc)
                                <div class="relative w-full overflow-hidden rounded-2xl shadow-lg border border-deep-green/10"
                                    style="aspect-ratio: 16 / 9;">
                                    <iframe src="{{ $mapSrc }}" class="absolute inset-0 w-full h-full" style="border:0;"
                                        allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                                        title="{{ $event->title }}"></iframe>
                                </div>
                            @endif
                            @if ($mapLink)
                                <a href="{{ $mapLink }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 mt-4 px-6 py-3 rounded-full bg-deep-green text-white text-sm font-bold hover:bg-deep-green/90 transition-all shadow-md">
                                    <span class="material-symbols-outlined text-base">location_on</span>
                                    {{ __('messages.event_open_in_maps') }}
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Extra images --}}
                    @if (count($rest))
                        <div>
                            <h2 class="text-2xl font-serif text-deep-green mb-5">{{ __('messages.event_gallery') }}</h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach ($rest as $img)
                                    @php $imgUrl = asset('storage/' . ltrim($img, '/')); @endphp
                                    <button type="button" data-upcoming-photo data-src="{{ $imgUrl }}"
                                        class="group relative h-40 rounded-xl overflow-hidden bg-deep-green/5 shadow-sm hover:shadow-lg transition-all">
                                        <img src="{{ $imgUrl }}" alt="" aria-hidden="true"
                                            class="absolute inset-0 w-full h-full object-cover scale-110 blur-xl opacity-40 select-none pointer-events-none">
                                        <img src="{{ $imgUrl }}" alt="{{ $event->title }}"
                                            class="relative w-full h-full object-contain">
                                        <span class="absolute inset-0 bg-deep-green/0 group-hover:bg-deep-green/20 transition-colors"></span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Registration (shared partial — expects $event) --}}
                    <div class="future-event-card bg-white border border-deep-green/10 rounded-2xl shadow-sm p-6 sm:p-8">
                        <h2 class="text-2xl font-serif text-deep-green mb-5">{{ __('messages.future_register_btn') }}</h2>
                        @include('partials._future-event-register', ['event' => $event])
                    </div>
                </div>

                {{-- Sidebar --}}
                <aside class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6 lg:sticky lg:top-24">
                        <h3 class="text-lg font-serif text-deep-green mb-5">{{ __('messages.event_details') }}</h3>
                        <dl class="space-y-4 text-sm">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-gold-accent">event</span>
                                <div>
                                    <dt class="text-charcoal/45 text-[10px] font-bold uppercase tracking-widest">{{ __('messages.event_field_date') }}</dt>
                                    <dd class="text-deep-green font-semibold">{{ $event->date->translatedFormat('F j, Y') }}</dd>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-gold-accent">place</span>
                                <div>
                                    <dt class="text-charcoal/45 text-[10px] font-bold uppercase tracking-widest">{{ __('messages.event_field_location') }}</dt>
                                    <dd class="text-deep-green font-semibold">{{ $event->location }}</dd>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-gold-accent">map</span>
                                <div>
                                    <dt class="text-charcoal/45 text-[10px] font-bold uppercase tracking-widest">{{ __('messages.event_field_province') }}</dt>
                                    <dd class="text-deep-green font-semibold">{{ $event->province }}</dd>
                                </div>
                            </div>
                            @if (count($species))
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-gold-accent">park</span>
                                    <div>
                                        <dt class="text-charcoal/45 text-[10px] font-bold uppercase tracking-widest">{{ __('messages.tree_species_planted') }}</dt>
                                        <dd class="text-deep-green font-semibold">{{ implode(', ', $species) }}</dd>
                                    </div>
                                </div>
                            @endif
                        </dl>
                    </div>
                </aside>
            </div>

            {{-- Related upcoming events --}}
            @if ($relatedEvents->isNotEmpty())
                <div class="mt-16">
                    <h2 class="text-2xl font-serif text-deep-green mb-6">{{ __('messages.future_related') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($relatedEvents as $rel)
                            @php
                                $relImgs = is_array($rel->images) ? array_values($rel->images) : [];
                                $relHero = $relImgs[0] ?? null;
                            @endphp
                            <a href="{{ route('upcoming-events.show', $rel) }}"
                                class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-deep-green/10 transition-all hover:-translate-y-1">
                                <div class="relative h-40 bg-deep-green/5 overflow-hidden">
                                    @if ($relHero)
                                        <img src="{{ asset('storage/' . ltrim($relHero, '/')) }}" alt="" aria-hidden="true"
                                            class="absolute inset-0 w-full h-full object-cover scale-110 blur-xl opacity-40 select-none pointer-events-none">
                                        <img src="{{ asset('storage/' . ltrim($relHero, '/')) }}" alt="{{ $rel->title }}"
                                            class="relative w-full h-full object-contain">
                                    @else
                                        <span class="material-symbols-outlined absolute inset-0 flex items-center justify-center text-deep-green/15"
                                            style="font-size: 5rem;">forest</span>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gold-accent mb-1">
                                        {{ $rel->date->translatedFormat('M j, Y') }}
                                    </p>
                                    <h3 class="font-serif text-lg text-deep-green leading-snug group-hover:text-primary transition-colors">
                                        {{ $rel->title }}
                                    </h3>
                                    <p class="text-charcoal/55 text-xs mt-1">{{ $rel->location }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    </main>

    {{-- Photo lightbox --}}
    <div id="upcoming-lightbox" class="fixed inset-0 z-[80] hidden bg-black/90 backdrop-blur-sm p-4">
        <button type="button" data-upcoming-lightbox-close
            class="absolute top-4 end-4 text-white/70 hover:text-white transition-colors z-10">
            <span class="material-symbols-outlined text-4xl">close</span>
        </button>
        <div class="absolute inset-0 flex items-center justify-center p-6">
            <img id="upcoming-lightbox-img" src="" alt=""
                class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
        </div>
    </div>

    @include('partials._future-event-scripts')

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const box = document.getElementById('upcoming-lightbox');
                const img = document.getElementById('upcoming-lightbox-img');
                if (!box || !img) return;

                const close = function() {
                    box.classList.add('hidden');
                    document.body.style.overflow = '';
                };

                document.querySelectorAll('[data-upcoming-photo]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        img.src = btn.getAttribute('data-src');
                        box.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    });
                });

                box.addEventListener('click', function(e) {
                    if (e.target === box || e.target.closest('[data-upcoming-lightbox-close]')) close();
                });
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !box.classList.contains('hidden')) close();
                });
            });
        </script>
    @endpush
@endsection
