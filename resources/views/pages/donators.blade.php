@extends('layouts.layout')
@section('title', 'Donators Page')
@section('content')

    <main>
        <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img alt="Sun-drenched hills of Kabul" class="w-full h-full object-cover scale-105"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                <div class="absolute inset-0 bg-gradient-to-t from-deep-green/60 via-deep-green/20 to-transparent">
                </div>
                <div class="absolute inset-0 bg-black/20"></div>
            </div>
            <div class="relative z-10 text-center space-y-8 negative-space-container pt-20">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20">
                    <span class="material-symbols-outlined text-gold-accent text-sm">verified</span>
                    <span class="text-white text-[9px] font-bold uppercase tracking-[0.25em]">Transparency &amp;
                        Recognition</span>
                </div>
                <h1 class="text-white text-6xl md:text-9xl font-serif font-bold tracking-tight drop-shadow-2xl">
                    Meet the <span class="italic font-light text-gold-accent">Guardians</span>
                </h1>
                <p class="text-white/90 text-lg md:text-2xl font-light max-w-2xl mx-auto leading-relaxed drop-shadow-lg">
                    Celebrating the individuals and families who fuel the restoration of Kabul's green heritage through
                    dedicated tree planting and environmental stewardship.
                </p>
                <div class="pt-8">
                    <span
                        class="material-symbols-outlined text-white animate-bounce text-3xl">keyboard_double_arrow_down</span>
                </div>
            </div>
        </section>

        <section class="py-32 px-6 md:px-12 lg:px-24 bg-white relative botanical-pattern">
            <div class="negative-space-container">
                <div class="text-center mb-20 space-y-4">
                    <h4 class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">Institutional Support
                    </h4>
                    <h2 class="text-5xl font-serif text-deep-green">Our Visionary Sponsors</h2>
                    <div class="w-24 h-1 bg-gold-accent/30 mx-auto mt-6"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                    @forelse($featuredDonators->take(3) as $donator)
                        <div
                            class="md:col-span-{{ $loop->first ? '12 bg-stone-50 lg:col-span-8' : '6 bg-stone-50 lg:col-span-4' }} group h-full">
                            <div
                                class="glass-sponsor rounded-[2.5rem] p-10 {{ $loop->first ? 'lg:p-16 flex flex-col md:flex-row' : '' }} items-center gap-12 relative">
                                @if ($loop->first)
                                    <div class="absolute top-8 right-8 flex items-center gap-2">
                                        <span
                                            class="px-4 py-1.5 rounded-full bg-gold-accent/10 border border-gold-accent/20 text-gold-accent text-[9px] font-bold uppercase tracking-widest">Lead
                                            Partner</span>
                                    </div>
                                @endif

                                <div class="space-y-6">
                                    <div class="flex justify-between items-start">
                                        @if ($donator->profile_image)
                                            <img alt="{{ $donator->full_name }} Profile"
                                                class="max-h-12 w-auto rounded-full object-cover"
                                                src="{{ asset('storage/' . $donator->profile_image) }}" />
                                        @else
                                            <div class="bg-deep-green/5 p-2 rounded-lg">
                                                <span
                                                    class="material-symbols-outlined text-deep-green text-3xl">person</span>
                                            </div>
                                        @endif

                                        <span
                                            class="px-3 py-1 rounded-full bg-stone-100 text-charcoal/60 text-[8px] font-bold uppercase tracking-widest">
                                            {{ $donator->location_type ?? 'Supporter' }}
                                        </span>
                                    </div>
                                    <div class="space-y-3">
                                        <h3 class="text-xl font-serif text-deep-green">{{ $donator->full_name }}</h3>
                                        <p class="text-charcoal/60 text-sm font-light leading-relaxed">
                                            {{ $donator->impact ?? 'Dedicated supporter of Kabul\'s green restoration and environmental stewardship initiatives.' }}
                                        </p>
                                        @if ($donator->financial_support)
                                            <p class="text-gold-accent font-semibold">
                                                ${{ number_format($donator->financial_support, 2) }} •
                                                {{ $donator->trees_sponsored }} Trees
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if (!$loop->first)
                                    <div class="mt-8">
                                        <span class="text-gold-accent font-bold text-[9px] uppercase tracking-widest">
                                            {{ $donator->location ?? 'International Support' }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-12 text-center py-12">
                            <p class="text-charcoal/60">No featured donators yet. Check back soon!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="py-24 px-6 md:px-12 lg:px-24 bg-white relative overflow-hidden">
            <div class="negative-space-container">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="relative group">
                        <div
                            class="absolute -inset-4 bg-gold-accent/5 rounded-[3rem] -z-10 group-hover:scale-105 transition-transform duration-700">
                        </div>
                        @forelse($featuredDonators->take(1) as $featuredDonator)
                            @if ($featuredDonator->profile_image)
                                <img alt="{{ $featuredDonator->full_name }} Portrait"
                                    class="w-full h-[600px] object-cover rounded-[2.5rem] shadow-2xl grayscale-[0.2] hover:grayscale-0 transition-all duration-700"
                                    src="{{ asset('storage/' . $featuredDonator->profile_image) }}" />
                            @else
                                <div
                                    class="w-full h-[600px] bg-gradient-to-br from-deep-green/20 to-gold-accent/20 rounded-[2.5rem] shadow-2xl flex items-center justify-center">
                                    <span class="material-symbols-outlined text-6xl text-deep-green">person</span>
                                </div>
                            @endif
                        @empty
                            <div
                                class="w-full h-[600px] bg-gradient-to-br from-deep-green/20 to-gold-accent/20 rounded-[2.5rem] shadow-2xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-6xl text-deep-green">person</span>
                            </div>
                        @endforelse
                    </div>
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <h4 class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">Individual Impact
                            </h4>
                            <h2 class="text-5xl font-serif text-deep-green">Making a Difference</h2>
                            <div class="w-24 h-1 bg-gold-accent/30"></div>
                        </div>
                        @if ($topSupporter)
                            <div class="space-y-6">
                                <h3 class="text-3xl font-serif text-deep-green">{{ $topSupporter->full_name }}</h3>
                                <p class="text-charcoal/70 text-lg leading-relaxed font-light">
                                    {{ $topSupporter->impact ?? 'A passionate advocate for environmental conservation and sustainable urban development in Kabul.' }}
                                </p>
                                <div class="grid grid-cols-3 gap-8 py-8">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-gold-accent">
                                            {{ $topSupporter->trees_sponsored ?? 0 }}</div>
                                        <div class="text-sm text-charcoal/60 uppercase tracking-wider">Trees Planted</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-gold-accent">
                                            ${{ number_format($topSupporter->financial_support ?? 0, 0) }}</div>
                                        <div class="text-sm text-charcoal/60 uppercase tracking-wider">Support</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-gold-accent">
                                            {{ $topSupporter->location ?? 'Kabul' }}</div>
                                        <div class="text-sm text-charcoal/60 uppercase tracking-wider">Location</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="space-y-6">
                                <h3 class="text-3xl font-serif text-deep-green">Join Our Mission</h3>
                                <p class="text-charcoal/70 text-lg leading-relaxed font-light">
                                    Become part of our growing community of environmental stewards who are making a real
                                    difference in Kabul's green future.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 px-6 md:px-12 lg:px-24 bg-stone-50 relative">
            <div class="negative-space-container">
                <div class="text-center mb-16 space-y-4">
                    <h4 class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">Community of Support</h4>
                    <h2 class="text-5xl font-serif text-deep-green">All Our Donators</h2>
                    <div class="w-24 h-1 bg-gold-accent/30 mx-auto mt-6"></div>
                    <div class="flex justify-center gap-6 md:gap-8 lg:gap-12 text-deep-green flex-wrap">
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold">{{ $totalDonators }}</div>
                            <div class="text-xs md:text-sm uppercase tracking-wider">Donators</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold">{{ number_format($totalTrees) }}</div>
                            <div class="text-xs md:text-sm uppercase tracking-wider">Trees</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold">${{ number_format($totalFinancial, 0) }}</div>
                            <div class="text-xs md:text-sm uppercase tracking-wider">Total Support</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden">
                    @include('partials.donators-table', ['verifiedDonators' => $verifiedDonators])
                </div>
            </div>
        </section>
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const paginationContainer = document.getElementById('pagination-container');
                const tableContainer = document.querySelector('.overflow-x-auto').parentElement;

                if (paginationContainer) {
                    paginationContainer.addEventListener('click', function(e) {
                        const link = e.target.closest('a');
                        if (link && link.href) {
                            e.preventDefault();
                            loadPage(link.href);
                        }
                    });
                }

                function loadPage(url) {
                    // Show loading state
                    tableContainer.style.opacity = '0.5';

                    fetch(url + '?ajax=1', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');

                            // Update table
                            const newTable = doc.querySelector('.overflow-x-auto');
                            if (newTable && tableContainer) {
                                const currentTable = tableContainer.querySelector('.overflow-x-auto');
                                if (currentTable) {
                                    currentTable.replaceWith(newTable);
                                }
                            }

                            // Update pagination
                            const newPagination = doc.querySelector('#pagination-container');
                            if (newPagination && paginationContainer) {
                                paginationContainer.innerHTML = newPagination.innerHTML;
                            }

                            // Update URL without reload
                            history.pushState({}, '', url);

                            // Remove loading state
                            tableContainer.style.opacity = '1';

                            // Scroll to top of table
                            tableContainer.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        })
                        .catch(error => {
                            console.error('Error loading page:', error);
                            tableContainer.style.opacity = '1';
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
