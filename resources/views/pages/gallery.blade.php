@extends('layouts.layout')
@section('title', 'Gallery - Our Events')
@section('content')

    <main class="flex-grow">
        <section class="relative h-[600px] flex items-center overflow-hidden bg-neutral-900">
            <div class="absolute right-0 top-0 w-full md:w-2/3 h-full">

                <img alt="Planting event" class="w-full h-full object-cover opacity-70" src="{{ asset('images/74.jpeg') }}" />

                {{-- <img alt="Sun-drenched forest canopy" class="w-full h-full object-cover opacity-70"
                    src="{{ asset('images/74.jpeg') }}" /> --}}

                <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
            </div>
            <div class="relative w-full h-full max-w-7xl mx-auto px-6 flex items-center">
                <div class="relative z-10 max-w-2xl">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-accent/20 border border-gold-accent/40 text-white text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-xs">event</span>
                        {{ __('messages.event_gallery') }}
                    </div>
                    <h1 class="text-5xl md:text-6xl font-serif text-white mb-6 leading-[1.1] drop-shadow-2xl">
                        {{ __('messages.our_events') }}</h1>
                    <p class="text-lg md:text-xl text-gray-200 font-medium leading-relaxed max-w-lg mb-8 drop-shadow-lg">
                        {{ __('messages.gallery_description') }}</p>
                    <div class="flex gap-4">
                        <a href="{{ route('donators') }}"
                            class="bg-gold-accent hover:bg-gold-accent/90 text-deep-green px-8 py-4 rounded-lg text-sm font-bold transition-all uppercase tracking-wider shadow-lg hover:shadow-xl">
                            {{ __('messages.support_our_work') }}
                        </a>
                        <a href="{{ route('contact') }}"
                            class="flex items-center gap-2 px-6 py-4 bg-white/10 backdrop-blur-sm text-white border border-white/20 hover:bg-white/20 transition-all rounded-lg text-sm font-bold uppercase tracking-wider">
                            <span class="material-symbols-outlined">volunteer_activism</span>
                            {{ __('messages.get_involved') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        @include('partials.future-events', ['limit' => 6])

        <!-- Events Grid -->
        <section class="py-16 max-w-7xl mx-auto px-6 scroll-mt-24">

            {{-- Section toolbar: eyebrow label on the left, search on the right.
                 The search is kept OUTSIDE #sponsor-results so it keeps focus
                 and its value while the results below are swapped via AJAX. --}}
            <style>
                /* Hide the browser's native search "x" — we use our own button. */
                #event-search::-webkit-search-cancel-button { -webkit-appearance: none; appearance: none; }
            </style>
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between mb-12">
                <span class="inline-flex items-center gap-2 self-center sm:self-auto px-3.5 py-1.5 rounded-full bg-deep-green/5 border border-deep-green/10 text-deep-green text-[11px] font-bold uppercase tracking-[0.18em]">
                    <span class="material-symbols-outlined text-sm">photo_library</span>
                    {{ __('messages.event_gallery') }}
                </span>

                <form method="GET" action="{{ route('gallery') }}" role="search"
                    class="event-search-form group relative w-full sm:w-80 md:w-[26rem]">
                    <span class="material-symbols-outlined absolute start-4 top-1/2 -translate-y-1/2 text-deep-green/40 text-xl pointer-events-none transition-colors group-focus-within:text-deep-green">search</span>
                    <input type="search" id="event-search" name="q" value="{{ $searchQuery ?? '' }}"
                        placeholder="{{ __('messages.event_search_placeholder') }}" autocomplete="off"
                        title="{{ __('messages.event_search_hint') }}"
                        class="w-full bg-white border border-deep-green/15 rounded-full ps-12 pe-11 py-3.5 text-sm text-deep-green placeholder:text-charcoal/40 shadow-sm shadow-deep-green/5 focus:border-deep-green/60 focus:ring-4 focus:ring-deep-green/10 focus:outline-none transition-all">
                    <button type="button" data-search-clear aria-label="{{ __('messages.sponsor_lookup_view_all') }}"
                        class="@if (empty($searchQuery)) hidden @endif absolute end-3.5 top-1/2 -translate-y-1/2 flex text-charcoal/40 hover:text-deep-green transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                    <span data-search-spinner
                        class="hidden absolute end-3.5 top-1/2 -translate-y-1/2 h-4 w-4 rounded-full border-2 border-deep-green/20 border-t-deep-green animate-spin"></span>
                </form>
            </div>

            <div id="sponsor-results" class="scroll-mt-24">
                <div class="text-center mb-12">
                    @if (!empty($sponsor))
                        <span class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 rounded-full bg-vibrant-lime/15 border border-vibrant-lime/30 text-deep-green text-[11px] font-bold uppercase tracking-widest">
                            <span class="material-symbols-outlined text-sm">verified</span>
                            {{ __('messages.sponsor_lookup_showing', ['name' => $sponsorName]) }}
                        </span>
                        <h2 class="text-4xl md:text-5xl font-serif text-deep-green mb-4">
                            {{ __('messages.sponsor_lookup_their_events') }}
                        </h2>
                        <a href="{{ route('gallery') }}" data-view-all
                            class="inline-flex items-center gap-1 text-sm font-bold text-deep-green/70 hover:text-deep-green underline decoration-gold-accent decoration-2 underline-offset-4">
                            <span class="material-symbols-outlined text-base">arrow_back</span>
                            {{ __('messages.sponsor_lookup_view_all') }}
                        </a>
                    @elseif (!empty($searchQuery))
                        <h2 class="text-4xl md:text-5xl font-serif text-deep-green mb-4">
                            {{ __('messages.event_search_results_for', ['term' => $searchQuery]) }}
                        </h2>
                        <a href="{{ route('gallery') }}" data-view-all
                            class="inline-flex items-center gap-1 text-sm font-bold text-deep-green/70 hover:text-deep-green underline decoration-gold-accent decoration-2 underline-offset-4">
                            <span class="material-symbols-outlined text-base">arrow_back</span>
                            {{ __('messages.sponsor_lookup_view_all') }}
                        </a>
                    @else
                        <h2 class="text-4xl md:text-5xl font-serif text-deep-green mb-4">{{ __('messages.all_our_events') }}</h2>
                        <p class="text-lg text-charcoal/70 max-w-2xl mx-auto">{{ __('messages.browse_events_description') }}</p>
                    @endif
                </div>

                @if (!empty($sponsor) && $events->isEmpty())
                    <div class="text-center py-16">
                        <span class="material-symbols-outlined text-deep-green/20 text-6xl mb-3">park</span>
                        <p class="text-charcoal/60">{{ __('messages.sponsor_lookup_no_events') }}</p>
                    </div>
                @else
                    @include('partials.events-grid', ['events' => $events])
                @endif
            </div>
        </section>

        @include('partials.little-guardians')
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const results = document.getElementById('sponsor-results');
                const searchInput = document.getElementById('event-search');
                if (!results) return;

                const galleryUrl = @json(route('gallery'));
                let seq = 0;
                let debounceTimer = null;

                // On a direct/shared search URL, jump straight to the results.
                try {
                    const q = (new URLSearchParams(window.location.search).get('q') || '').trim();
                    if (q !== '') {
                        setTimeout(function() {
                            results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 150);
                    }
                } catch (e) { /* no-op */ }

                const spinnerEl = document.querySelector('[data-search-spinner]');
                const clearEl = document.querySelector('[data-search-clear]');
                let busy = false;

                function spinner(on) {
                    busy = on;
                    if (spinnerEl) spinnerEl.classList.toggle('hidden', !on);
                    syncClear();
                }

                // Clear button shows only when there's a query and we're idle.
                function syncClear() {
                    if (!clearEl) return;
                    const hasText = !!(searchInput && searchInput.value.trim());
                    clearEl.classList.toggle('hidden', busy || !hasText);
                }

                function buildUrl(q) {
                    return galleryUrl + (q ? ('?q=' + encodeURIComponent(q)) : '');
                }

                // Fetch the full page and swap in only the #sponsor-results
                // section (banner, heading, grid, pagination). The search box
                // lives outside this element, so it keeps focus and its value.
                function loadResults(url, opts) {
                    opts = opts || {};
                    const push = opts.push !== false;
                    const scroll = opts.scroll === true;
                    const mySeq = ++seq;

                    results.style.opacity = '0.5';
                    results.style.pointerEvents = 'none';
                    spinner(true);

                    fetch(url, {
                            headers: { 'Accept': 'text/html' },
                            credentials: 'same-origin'
                        })
                        .then(function(r) {
                            if (!r.ok) throw new Error('HTTP ' + r.status);
                            return r.text();
                        })
                        .then(function(html) {
                            // Ignore stale responses if a newer request started.
                            if (mySeq !== seq) return;
                            const doc = new DOMParser().parseFromString(html, 'text/html');
                            const fresh = doc.getElementById('sponsor-results');
                            if (!fresh) {
                                window.location.href = url;
                                return;
                            }
                            results.innerHTML = fresh.innerHTML;
                            if (push) history.pushState({ url: url }, '', url);
                            results.style.opacity = '1';
                            results.style.pointerEvents = '';
                            spinner(false);
                            if (scroll) results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        })
                        .catch(function() {
                            if (mySeq !== seq) return;
                            // Graceful fallback to a normal navigation
                            window.location.href = url;
                        });
                }

                // 1. Combined live search (debounced) — title OR sponsor code.
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const q = this.value.trim();
                        syncClear();
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(function() {
                            loadResults(buildUrl(q), { push: true, scroll: false });
                        }, 350);
                    });
                }

                // Clear (×) button: reset the search and reload all events.
                if (clearEl) {
                    clearEl.addEventListener('click', function() {
                        if (!searchInput) return;
                        searchInput.value = '';
                        syncClear();
                        searchInput.focus();
                        clearTimeout(debounceTimer);
                        loadResults(buildUrl(''), { push: true, scroll: false });
                    });
                }

                // 2. Enter key: search immediately, no full reload.
                document.addEventListener('submit', function(e) {
                    const form = e.target.closest ? e.target.closest('.event-search-form') : null;
                    if (!form) return;
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    loadResults(buildUrl(searchInput ? searchInput.value.trim() : ''), {
                        push: true,
                        scroll: false
                    });
                });

                // 3. Pagination + "view all" links (delegated — survive swaps).
                results.addEventListener('click', function(e) {
                    const viewAll = e.target.closest('a[data-view-all]');
                    if (viewAll) {
                        e.preventDefault();
                        if (searchInput) searchInput.value = '';
                        syncClear();
                        loadResults(buildUrl(''), { push: true, scroll: false });
                        return;
                    }

                    const pager = e.target.closest('#pagination-container');
                    if (!pager) return;
                    const link = e.target.closest('a[href]');
                    if (!link) return;
                    e.preventDefault();
                    loadResults(link.href, { push: true, scroll: true });
                });

                // 4. Browser back/forward — keep the box in sync with the URL.
                window.addEventListener('popstate', function() {
                    try {
                        const q = new URLSearchParams(window.location.search).get('q') || '';
                        if (searchInput) searchInput.value = q;
                        syncClear();
                    } catch (e) { /* no-op */ }
                    loadResults(window.location.href, { push: false, scroll: false });
                });
            });
        </script>
    @endpush
@endsection
