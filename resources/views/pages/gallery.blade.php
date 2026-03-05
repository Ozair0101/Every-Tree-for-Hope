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

        <!-- Events Grid -->
        <section class="py-16 max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-serif text-deep-green mb-4">{{ __('messages.all_our_events') }}</h2>
                <p class="text-lg text-charcoal/70 max-w-2xl mx-auto">{{ __('messages.browse_events_description') }}</p>
            </div>

            @include('partials.events-grid', ['events' => $events])
        </section>
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const paginationContainer = document.getElementById('pagination-container');
                const eventsGrid = document.querySelector('.grid.grid-cols-1');

                console.log('Pagination container:', paginationContainer);
                console.log('Events grid:', eventsGrid);

                if (paginationContainer) {
                    paginationContainer.addEventListener('click', function(e) {
                        // Find the closest anchor tag, checking both the target and its parents
                        let target = e.target;
                        let link = null;

                        // Traverse up to find the link
                        while (target && target !== paginationContainer) {
                            if (target.tagName === 'A' && target.href) {
                                link = target;
                                break;
                            }
                            target = target.parentElement;
                        }

                        if (link && link.href) {
                            e.preventDefault();
                            e.stopPropagation();
                            console.log('Loading page:', link.href);
                            loadPage(link.href);
                        }
                    });
                }

                function loadPage(url) {
                    if (!eventsGrid) {
                        console.error('Events grid not found');
                        window.location.href = url;
                        return;
                    }

                    // Show loading state
                    eventsGrid.style.opacity = '0.5';

                    const ajaxUrl = url + (url.includes('?') ? '&' : '?') + 'ajax=1';
                    console.log('Fetching:', ajaxUrl);

                    fetch(ajaxUrl, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error('HTTP error! status: ' + response.status);
                            }
                            return response.text();
                        })
                        .then(html => {
                            console.log('Received HTML length:', html.length);
                            console.log('HTML preview:', html.substring(0, 500));

                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            // Try multiple selectors to find the grid
                            let newEventsGrid = doc.querySelector('.grid.grid-cols-1');
                            if (!newEventsGrid) {
                                newEventsGrid = doc.querySelector('[class*="grid-cols-1"]');
                            }
                            if (!newEventsGrid) {
                                newEventsGrid = doc.querySelector('.grid');
                            }

                            console.log('Found new events grid:', newEventsGrid);

                            if (newEventsGrid && eventsGrid) {
                                eventsGrid.innerHTML = newEventsGrid.innerHTML;
                                console.log('Events grid updated successfully');
                            } else {
                                console.error('Could not find new events grid, falling back to page reload');
                                window.location.href = url;
                                return;
                            }

                            // Update pagination
                            const newPagination = doc.querySelector('#pagination-container');
                            console.log('Found new pagination:', newPagination);

                            if (newPagination && paginationContainer) {
                                paginationContainer.innerHTML = newPagination.innerHTML;
                                console.log('Pagination updated successfully');
                            } else {
                                console.error('Could not find new pagination');
                            }

                            // Update URL without reload
                            history.pushState({}, '', url);

                            // Remove loading state
                            eventsGrid.style.opacity = '1';

                            // Scroll to top of events section
                            eventsGrid.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        })
                        .catch(error => {
                            console.error('Error loading page:', error);
                            eventsGrid.style.opacity = '1';
                            // Fallback: reload the page
                            window.location.href = url;
                        });
                }

                // Handle browser back/forward buttons
                window.addEventListener('popstate', function(e) {
                    // Only load page if this is a genuine back/forward navigation
                    if (e.state !== null) {
                        loadPage(window.location.href);
                    }
                });
            });
        </script>
    @endpush
@endsection
