@extends('layouts.layout')
@section('title', 'Gallery - Our Events')
@section('content')

    <main class="flex-grow">
        <section class="relative h-[600px] flex items-center overflow-hidden bg-neutral-900">
            <div class="absolute right-0 top-0 w-full md:w-2/3 h-full">

                <img alt="Planting event" class="w-full h-full object-cover opacity-70" src="{{ asset('images/5.jpeg') }}" />

                {{-- <img alt="Sun-drenched forest canopy" class="w-full h-full object-cover opacity-70"
                    src="{{ asset('images/5.jpeg') }}" /> --}}

                <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
            </div>
            <div class="relative w-full h-full max-w-7xl mx-auto px-6 flex items-center">
                <div class="relative z-10 max-w-2xl">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-accent/20 border border-gold-accent/40 text-white text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-xs">event</span>
                        {{ __('messages.event_gallery') }}
                    </div>
                    <h1 class="text-6xl md:text-7xl font-serif text-white mb-6 leading-[1.1] drop-shadow-2xl">
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
        <section id="sponsor-results" class="py-16 max-w-7xl mx-auto px-6 scroll-mt-24">

            {{-- Sponsor code lookup --}}
            <div class="max-w-2xl mx-auto mb-12">
                <div class="bg-white border border-deep-green/10 rounded-2xl shadow-[0_10px_30px_rgba(6,46,34,0.06)] p-6 md:p-8">
                    <div class="text-center mb-5">
                        <span class="material-symbols-outlined text-gold-accent text-3xl">redeem</span>
                        <h3 class="font-serif text-xl md:text-2xl font-bold text-deep-green mt-1">
                            {{ __('messages.sponsor_lookup_title') }}
                        </h3>
                        <p class="text-charcoal/60 text-sm mt-1">
                            {{ __('messages.sponsor_lookup_desc') }}
                        </p>
                    </div>
                    <form method="GET" action="{{ route('gallery') }}"
                        class="sponsor-search-form flex flex-col sm:flex-row gap-3">
                        <input type="text" name="sponsor_code" value="{{ $sponsorCode ?? '' }}"
                            placeholder="{{ __('messages.sponsor_lookup_placeholder') }}"
                            class="flex-1 bg-transparent border-2 border-deep-green/15 rounded-full px-5 py-3 text-center sm:text-start uppercase tracking-widest text-deep-green font-bold placeholder:font-normal placeholder:tracking-normal placeholder:text-charcoal/35 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-7 py-3 bg-deep-green text-white text-xs font-extrabold tracking-[0.2em] uppercase rounded-full shadow-md shadow-deep-green/20 hover:bg-deep-green/90 hover:-translate-y-0.5 transition-all">
                            <span class="material-symbols-outlined text-base">search</span>
                            {{ __('messages.sponsor_lookup_btn') }}
                        </button>
                    </form>

                    @if (!empty($sponsorNotFound) && $sponsorNotFound)
                        <div class="mt-5 flex items-center gap-2 justify-center text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                            <span class="material-symbols-outlined text-base">error</span>
                            {{ __('messages.sponsor_lookup_not_found') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center mb-12">
                @if (!empty($sponsor))
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 mb-4 rounded-full bg-vibrant-lime/15 border border-vibrant-lime/30 text-deep-green text-[11px] font-bold uppercase tracking-widest">
                        <span class="material-symbols-outlined text-sm">verified</span>
                        {{ __('messages.sponsor_lookup_showing', ['name' => $sponsorName]) }}
                    </span>
                    <h2 class="text-4xl md:text-5xl font-serif text-deep-green mb-4">
                        {{ __('messages.sponsor_lookup_their_events') }}
                    </h2>
                    <a href="{{ route('gallery') }}"
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
        </section>

        @include('partials.little-guardians')
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const results = document.getElementById('sponsor-results');
                if (!results) return;

                // On a direct/shared sponsor-code URL, jump straight to the results.
                try {
                    const p = new URLSearchParams(window.location.search);
                    if (p.has('sponsor_code') && p.get('sponsor_code').trim() !== '') {
                        setTimeout(function() {
                            results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 150);
                    }
                } catch (e) { /* no-op */ }

                let loading = false;

                function loadResults(url, push) {
                    if (loading) return;
                    loading = true;
                    results.style.opacity = '0.5';
                    results.style.pointerEvents = 'none';

                    // Note: no X-Requested-With header — we want the full page
                    // so we can extract the whole #sponsor-results section
                    // (banner, heading, grid, pagination, empty/not-found states).
                    fetch(url, {
                            headers: { 'Accept': 'text/html' },
                            credentials: 'same-origin'
                        })
                        .then(function(r) {
                            if (!r.ok) throw new Error('HTTP ' + r.status);
                            return r.text();
                        })
                        .then(function(html) {
                            const doc = new DOMParser().parseFromString(html, 'text/html');
                            const fresh = doc.getElementById('sponsor-results');
                            if (!fresh) {
                                window.location.href = url;
                                return;
                            }
                            results.innerHTML = fresh.innerHTML;
                            if (push !== false) history.pushState({ url: url }, '', url);
                            results.style.opacity = '1';
                            results.style.pointerEvents = '';
                            results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        })
                        .catch(function() {
                            // Graceful fallback to a normal navigation
                            window.location.href = url;
                        })
                        .finally(function() {
                            loading = false;
                        });
                }

                // Listeners are bound to the persistent #sponsor-results element,
                // so they keep working after its innerHTML is swapped.

                // 1. Sponsor-code search submitted via AJAX
                results.addEventListener('submit', function(e) {
                    const form = e.target.closest('.sponsor-search-form');
                    if (!form) return;
                    e.preventDefault();
                    const input = form.querySelector('input[name="sponsor_code"]');
                    const value = input ? input.value.trim() : '';
                    const base = form.getAttribute('action') || window.location.pathname;
                    const url = base + (value ? ('?sponsor_code=' + encodeURIComponent(value)) : '');
                    loadResults(url, true);
                });

                // 2. Pagination links loaded via AJAX (query string preserved server-side)
                results.addEventListener('click', function(e) {
                    const pager = e.target.closest('#pagination-container');
                    if (!pager) return;
                    const link = e.target.closest('a[href]');
                    if (!link) return;
                    e.preventDefault();
                    loadResults(link.href, true);
                });

                // 3. Browser back/forward
                window.addEventListener('popstate', function() {
                    loadResults(window.location.href, false);
                });
            });
        </script>
    @endpush
@endsection
