@extends('layouts.layout')
@section('title', 'Gallery - Our Events')
@section('content')

    <main class="flex-grow">
        <section class="relative h-[600px] flex items-center overflow-hidden bg-neutral-900">
            <div class="absolute right-0 top-0 w-full md:w-2/3 h-full">
                @forelse($events->take(1) as $featuredEvent)
                    @forelse($featuredEvent->images->take(1) as $image)
                        <img alt="{{ $featuredEvent->title }}" class="w-full h-full object-cover opacity-70"
                            src="{{ $image->full_image_url }}" />
                    @empty
                        <img alt="Planting event" class="w-full h-full object-cover opacity-70"
                            src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" />
                    @endforelse
                @empty
                    <img alt="Sun-drenched forest canopy" class="w-full h-full object-cover opacity-70"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCiSLJVNhgqX_rzJKgvmlDvloU-fcMmenRiN6mZ1Wqevab2hUyke18xq1TOFuMxsqoCVU8oS5wF9skWOEUP9MZePgGonhgkM6bX84t6Iv02iR2PilvhZHea3utpkTvdOoNtvq1v8eYMNuDCvGmkJejzO3YNLClwcjgDP-j-N6T3tqgEvhVO10uaCBCh9xyPNLY_dY7FJmn0WdOo_CT5uzpI1QlpJjhOlMUmQYCjmdVc6qoZyX_3kNo3kLI5c_p04yr_6WNLSMZ-im8" />
                @endforelse
                <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
            </div>
            <div class="relative w-full h-full max-w-7xl mx-auto px-6 flex items-center">
                <div class="relative z-10 max-w-2xl">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-accent/20 border border-gold-accent/40 text-white text-xs font-bold uppercase tracking-widest mb-6 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-xs">event</span>
                        Event Gallery
                    </div>
                    <h1 class="text-6xl md:text-7xl font-serif text-white mb-6 leading-[1.1] drop-shadow-2xl">
                        Our <br />
                        <span class="italic text-gold-accent">Events</span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-200 font-medium leading-relaxed max-w-lg mb-8 drop-shadow-lg">
                        Explore our collection of tree planting events and environmental initiatives. Each event represents
                        our commitment to creating a greener Kabul.
                    </p>
                    <div class="flex gap-4">
                        <a href="{{ route('donators') }}"
                            class="bg-gold-accent hover:bg-gold-accent/90 text-deep-green px-8 py-4 rounded-lg text-sm font-bold transition-all uppercase tracking-wider shadow-lg hover:shadow-xl">
                            Support Our Work
                        </a>
                        <a href="{{ route('contact') }}"
                            class="flex items-center gap-2 px-6 py-4 bg-white/10 backdrop-blur-sm text-white border border-white/20 hover:bg-white/20 transition-all rounded-lg text-sm font-bold uppercase tracking-wider">
                            <span class="material-symbols-outlined">volunteer_activism</span>
                            Get Involved
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Events Grid -->
        <section class="py-16 max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-serif text-deep-green mb-4">All Our Events</h2>
                <p class="text-lg text-charcoal/70 max-w-2xl mx-auto">
                    Browse through our complete collection of environmental events and tree planting initiatives.
                </p>
            </div>

            @include('partials.events-grid', ['events' => $events])
        </section>
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const paginationContainer = document.getElementById('pagination-container');
                const eventsGrid = document.querySelector('.grid.grid-cols-2');

                console.log('Pagination container:', paginationContainer);
                console.log('Events grid:', eventsGrid);

                if (paginationContainer) {
                    paginationContainer.addEventListener('click', function(e) {
                        const link = e.target.closest('a');
                        if (link && link.href) {
                            e.preventDefault();
                            console.log('Loading page:', link.href);
                            loadPage(link.href);
                        }
                    });
                }

                function loadPage(url) {
                    if (!eventsGrid) {
                        console.error('Events grid not found');
                        return;
                    }

                    // Show loading state
                    eventsGrid.style.opacity = '0.5';

                    fetch(url + '?ajax=1', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        })
                        .then(response => {
                            console.log('Response status:', response.status);
                            return response.text();
                        })
                        .then(html => {
                            console.log('Received HTML:', html.substring(0, 200));
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            // Update events grid
                            const newEventsGrid = doc.querySelector('.grid.grid-cols-2');
                            if (newEventsGrid && eventsGrid) {
                                eventsGrid.innerHTML = newEventsGrid.innerHTML;
                                console.log('Events grid updated');
                            } else {
                                console.error('Could not find new events grid');
                            }

                            // Update pagination
                            const newPagination = doc.querySelector('#pagination-container');
                            if (newPagination && paginationContainer) {
                                paginationContainer.innerHTML = newPagination.innerHTML;
                                console.log('Pagination updated');
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
                    loadPage(window.location.href);
                });
            });
        </script>
    @endpush
@endsection
