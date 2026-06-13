@extends('layouts.layout')
@section('title', __('messages.jobs_page_title'))
@section('content')

    <main class="flex-grow bg-stone-50">
        <!-- Hero -->
        <section class="relative overflow-hidden bg-deep-green">
            <div class="absolute inset-0 opacity-20"
                style="background-image:radial-gradient(circle at 20% 20%, rgba(255,255,255,.4) 0, transparent 40%),radial-gradient(circle at 80% 0%, rgba(170,221,90,.5) 0, transparent 35%);">
            </div>
            <div class="relative max-w-5xl mx-auto px-6 pt-20 pb-24 text-center">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-[11px] font-bold uppercase tracking-[0.18em] backdrop-blur-sm mb-6">
                    <span class="material-symbols-outlined text-sm">diversity_3</span>
                    {{ __('messages.jobs_hero_badge') }}
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif text-white leading-[1.1] mb-5">
                    {{ __('messages.jobs_hero_title') }}
                </h1>
                <p class="text-lg text-white/80 max-w-2xl mx-auto mb-10">
                    {{ __('messages.jobs_hero_subtitle') }}
                </p>

                {{-- Keyword search --}}
                <form method="GET" action="{{ route('careers') }}" role="search"
                    class="jobs-search-form relative max-w-xl mx-auto">
                    <span class="material-symbols-outlined absolute start-5 top-1/2 -translate-y-1/2 text-deep-green/40 text-xl pointer-events-none">search</span>
                    <input type="search" id="job-search" name="q" value="{{ $search }}" autocomplete="off"
                        placeholder="{{ __('messages.jobs_search_placeholder') }}"
                        class="w-full bg-white rounded-full ps-14 pe-32 py-4 text-deep-green placeholder:text-charcoal/40 shadow-xl shadow-black/10 focus:ring-4 focus:ring-white/30 focus:outline-none transition-all">
                    <button type="submit"
                        class="absolute end-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-1.5 px-5 py-2.5 bg-deep-green text-white text-sm font-bold rounded-full hover:bg-deep-green/90 transition-colors">
                        <span class="material-symbols-outlined text-base">search</span>
                        <span class="hidden sm:inline">{{ __('messages.jobs_search_btn') }}</span>
                    </button>
                </form>

                <p class="mt-6 text-sm text-white/70">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-vibrant-lime opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-vibrant-lime"></span>
                        </span>
                        {{ trans_choice('messages.jobs_open_positions', $totalOpen, ['count' => $totalOpen]) }}
                    </span>
                </p>
            </div>
        </section>

        <!-- Filters + results -->
        <section class="max-w-4xl mx-auto px-6 -mt-10 pb-20">
            <div class="bg-white rounded-2xl border border-deep-green/10 shadow-lg p-4 sm:p-5 mb-8">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute start-3 top-1/2 -translate-y-1/2 text-deep-green/40 text-lg pointer-events-none">work</span>
                        <select id="filter-type"
                            class="w-full appearance-none bg-stone-50 border border-deep-green/10 rounded-xl ps-10 pe-9 py-2.5 text-sm text-deep-green focus:border-deep-green/40 focus:ring-0 focus:outline-none cursor-pointer">
                            <option value="">{{ __('messages.jobs_filter_all_types') }}</option>
                            @foreach (\App\Models\JobPosting::EMPLOYMENT_TYPES as $key => $label)
                                <option value="{{ $key }}" @selected($type === $key)>{{ __('messages.jobs_type_' . $key) }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute end-2 top-1/2 -translate-y-1/2 text-deep-green/40 text-lg pointer-events-none">expand_more</span>
                    </div>

                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute start-3 top-1/2 -translate-y-1/2 text-deep-green/40 text-lg pointer-events-none">category</span>
                        <select id="filter-category"
                            class="w-full appearance-none bg-stone-50 border border-deep-green/10 rounded-xl ps-10 pe-9 py-2.5 text-sm text-deep-green focus:border-deep-green/40 focus:ring-0 focus:outline-none cursor-pointer">
                            <option value="">{{ __('messages.jobs_filter_all_categories') }}</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}" @selected($category === $cat->slug)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute end-2 top-1/2 -translate-y-1/2 text-deep-green/40 text-lg pointer-events-none">expand_more</span>
                    </div>
                </div>
            </div>

            <div id="jobs-results">
                @include('partials.jobs-list')
            </div>
        </section>
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const results = document.getElementById('jobs-results');
                const search = document.getElementById('job-search');
                const filterType = document.getElementById('filter-type');
                const filterCat = document.getElementById('filter-category');
                if (!results) return;

                const baseUrl = @json(route('careers'));
                let seq = 0;
                let debounce = null;

                function buildUrl(forShare) {
                    const params = new URLSearchParams();
                    const q = (search && search.value.trim()) || '';
                    const t = (filterType && filterType.value) || '';
                    const c = (filterCat && filterCat.value) || '';
                    if (q) params.set('q', q);
                    if (t) params.set('type', t);
                    if (c) params.set('category', c);
                    if (!forShare) params.set('ajax', '1');
                    const qs = params.toString();
                    return baseUrl + (qs ? ('?' + qs) : '');
                }

                function load(scroll) {
                    const mySeq = ++seq;
                    results.style.opacity = '0.5';
                    results.style.pointerEvents = 'none';

                    fetch(buildUrl(false), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                            credentials: 'same-origin'
                        })
                        .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
                        .then(function(html) {
                            if (mySeq !== seq) return;
                            results.innerHTML = html;
                            results.style.opacity = '1';
                            results.style.pointerEvents = '';
                            history.replaceState({}, '', buildUrl(true));
                            if (scroll) results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        })
                        .catch(function() {
                            results.style.opacity = '1';
                            results.style.pointerEvents = '';
                            window.location.href = buildUrl(true);
                        });
                }

                if (search) {
                    search.addEventListener('input', function() {
                        clearTimeout(debounce);
                        debounce = setTimeout(function() { load(false); }, 350);
                    });
                }
                [filterType, filterCat].forEach(function(el) {
                    if (el) el.addEventListener('change', function() { load(false); });
                });

                // Keyword form submit (Enter) — no full reload
                document.addEventListener('submit', function(e) {
                    if (!e.target.closest || !e.target.closest('.jobs-search-form')) return;
                    e.preventDefault();
                    clearTimeout(debounce);
                    load(false);
                });

                // Pagination links (delegated — survive innerHTML swaps)
                results.addEventListener('click', function(e) {
                    const pager = e.target.closest('#pagination-container');
                    if (!pager) return;
                    const link = e.target.closest('a[href]');
                    if (!link) return;
                    e.preventDefault();
                    const url = link.href + (link.href.indexOf('?') === -1 ? '?' : '&') + 'ajax=1';
                    const mySeq = ++seq;
                    results.style.opacity = '0.5';
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                        .then(function(r) { return r.text(); })
                        .then(function(html) {
                            if (mySeq !== seq) return;
                            results.innerHTML = html;
                            results.style.opacity = '1';
                            history.replaceState({}, '', link.href);
                            results.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        })
                        .catch(function() { window.location.href = link.href; });
                });
            });
        </script>
    @endpush
@endsection
