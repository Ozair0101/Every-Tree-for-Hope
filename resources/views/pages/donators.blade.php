@extends('layouts.layout')
@section('title', 'Donators Page')
@section('content')

    <main>
        <!-- ===== HERO — Honor Roll / Gratitude Wall ===== -->
        <section class="relative w-full overflow-hidden py-20 md:py-28"
            style="background: linear-gradient(180deg, #1a3d2e 0%, #0a2a1d 100%);">

            <!-- Marble/stone texture overlay -->
            <div class="absolute inset-0 pointer-events-none opacity-[0.06]"
                style="background-image:
                    radial-gradient(circle at 20% 30%, #d4af37 0%, transparent 50%),
                    radial-gradient(circle at 80% 60%, #d4af37 0%, transparent 50%),
                    repeating-linear-gradient(45deg, transparent, transparent 80px, rgba(255,255,255,0.02) 80px, rgba(255,255,255,0.02) 81px);">
            </div>

            <!-- Subtle dot pattern -->
            <div class="absolute inset-0 pointer-events-none opacity-[0.04]"
                style="background-image: radial-gradient(rgba(212,175,55,0.6) 1px, transparent 1px); background-size: 24px 24px;">
            </div>

            <!-- Decorative classical ornaments -->
            <span class="material-symbols-outlined absolute top-12 left-[5%] text-gold-accent/[0.08] text-[180px] select-none pointer-events-none rotate-12">workspace_premium</span>
            <span class="material-symbols-outlined absolute bottom-12 right-[5%] text-gold-accent/[0.08] text-[200px] select-none pointer-events-none -rotate-12">military_tech</span>

            <div class="relative z-10 max-w-7xl mx-auto px-6">

                <!-- Top brass plaque label -->
                <div class="flex flex-col items-center mb-10 donor-fade-in">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-[1px] w-12 bg-gold-accent/40"></div>
                        <span class="material-symbols-outlined text-gold-accent text-base">verified</span>
                        <span class="text-gold-accent text-[10px] font-bold uppercase tracking-[0.5em]">
                            {{ __('messages.transparency_recognition') }}
                        </span>
                        <span class="material-symbols-outlined text-gold-accent text-base">verified</span>
                        <div class="h-[1px] w-12 bg-gold-accent/40"></div>
                    </div>
                </div>

                <!-- Massive "WITH GRATITUDE" heading -->
                <div class="text-center mb-10 donor-fade-in" style="animation-delay: 0.1s;">
                    <p class="text-gold-accent/60 text-[11px] font-bold uppercase tracking-[0.6em] mb-3 font-serif">
                        {{ __('messages.donors_overline') }}
                    </p>
                    <h1 class="font-serif text-white leading-[0.9] tracking-tight"
                        style="font-size: clamp(3.5rem, 11vw, 9rem);">
                        <span class="font-bold">{{ __('messages.donors_with') }}</span>
                        <span class="block italic text-gold-accent font-black mt-1 relative inline-block">
                            <span class="relative z-10">{{ __('messages.donors_gratitude') }}</span>
                            <!-- Gold flourish underline -->
                            <svg class="absolute -bottom-3 left-0 w-full h-4 z-0" preserveAspectRatio="none" viewBox="0 0 220 16">
                                <path d="M5,11 Q60,2 110,8 T215,7" fill="none" stroke="#d4af37" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                                <circle cx="5" cy="11" r="2.5" fill="#d4af37" opacity="0.7"/>
                                <circle cx="215" cy="7" r="2.5" fill="#d4af37" opacity="0.7"/>
                            </svg>
                        </span>
                    </h1>
                    <p class="text-white/60 text-base md:text-lg max-w-2xl mx-auto mt-8 leading-relaxed font-light italic">
                        {{ __('messages.guardians_description') }}
                    </p>
                </div>

                <!-- Engraved brass plaque with stats -->
                <div class="max-w-3xl mx-auto donor-fade-in" style="animation-delay: 0.25s;">
                    <div class="relative rounded-2xl overflow-hidden"
                        style="background: linear-gradient(135deg, rgba(212,175,55,0.15) 0%, rgba(212,175,55,0.05) 50%, rgba(212,175,55,0.15) 100%);
                               border: 1px solid rgba(212,175,55,0.25);
                               box-shadow: 0 0 40px rgba(212,175,55,0.08), inset 0 1px 0 rgba(212,175,55,0.2);">

                        <!-- Top corner ornaments -->
                        <span class="absolute top-3 left-3 text-gold-accent/30 text-2xl select-none">❦</span>
                        <span class="absolute top-3 right-3 text-gold-accent/30 text-2xl select-none">❦</span>
                        <span class="absolute bottom-3 left-3 text-gold-accent/30 text-2xl select-none">❦</span>
                        <span class="absolute bottom-3 right-3 text-gold-accent/30 text-2xl select-none">❦</span>

                        <div class="px-8 py-8 md:px-12 md:py-10">
                            <p class="text-center text-gold-accent text-[10px] font-bold uppercase tracking-[0.4em] mb-6">
                                — {{ __('messages.donors_plaque_label') }} —
                            </p>

                            <div class="grid grid-cols-3 gap-4 md:gap-8">
                                <!-- Stat 1: Donators -->
                                <div class="text-center">
                                    <p class="text-white text-3xl md:text-5xl font-serif font-black tabular-nums leading-none">
                                        {{ $totalDonators ?? 0 }}
                                    </p>
                                    <p class="text-gold-accent/70 text-[9px] md:text-[10px] font-bold uppercase tracking-[0.25em] mt-2">
                                        {{ __('messages.donators') }}
                                    </p>
                                </div>
                                <!-- Vertical divider -->
                                <div class="text-center border-x border-gold-accent/20">
                                    <p class="text-white text-3xl md:text-5xl font-serif font-black tabular-nums leading-none">
                                        {{ number_format($totalTrees ?? 0) }}
                                    </p>
                                    <p class="text-gold-accent/70 text-[9px] md:text-[10px] font-bold uppercase tracking-[0.25em] mt-2">
                                        {{ __('messages.trees') }}
                                    </p>
                                </div>
                                <!-- Stat 3: Total support -->
                                <div class="text-center">
                                    <p class="text-white text-3xl md:text-5xl font-serif font-black tabular-nums leading-none">
                                        ${{ number_format($totalFinancial ?? 0, 0) }}
                                    </p>
                                    <p class="text-gold-accent/70 text-[9px] md:text-[10px] font-bold uppercase tracking-[0.25em] mt-2">
                                        {{ __('messages.total_support') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Bottom inscription -->
                            <p class="text-center text-white/40 text-[10px] italic mt-6 font-serif">
                                {{ __('messages.donors_inscription') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Floating supporter name strips (decorative) -->
                @if($verifiedDonators && $verifiedDonators->count() > 0)
                    <div class="mt-14 donor-fade-in" style="animation-delay: 0.4s;">
                        <p class="text-center text-gold-accent/40 text-[9px] font-bold uppercase tracking-[0.5em] mb-5">
                            ✦ {{ __('messages.donors_recognition_label') }} ✦
                        </p>
                        <div class="flex flex-wrap justify-center gap-2 max-w-5xl mx-auto">
                            @foreach($verifiedDonators->take(15) as $donator)
                                <span class="px-3 py-1.5 rounded-full bg-white/[0.04] border border-gold-accent/15 text-white/70 text-xs font-serif italic hover:bg-gold-accent/10 hover:border-gold-accent/40 hover:text-white transition-all"
                                    style="animation: gentleSway {{ 6 + ($loop->index % 3) }}s ease-in-out {{ $loop->index * 0.1 }}s infinite;">
                                    {{ $donator->full_name }}
                                </span>
                            @endforeach
                            @if($verifiedDonators->count() > 15)
                                <span class="px-3 py-1.5 rounded-full bg-gold-accent/10 border border-gold-accent/30 text-gold-accent text-xs font-bold">
                                    + {{ $verifiedDonators->count() - 15 }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Bottom scroll hint -->
                <div class="mt-14 flex justify-center donor-fade-in" style="animation-delay: 0.55s;">
                    <a href="#sponsors" class="group flex flex-col items-center gap-2 text-white/40 hover:text-gold-accent transition-colors">
                        <span class="text-[10px] font-bold uppercase tracking-[0.3em]">{{ __('messages.donors_meet_them') }}</span>
                        <span class="material-symbols-outlined text-2xl group-hover:translate-y-1 transition-transform">expand_more</span>
                    </a>
                </div>
            </div>
        </section>
        <!-- ===== END HERO ===== -->

        <section id="sponsors" class="py-32 px-6 md:px-12 lg:px-24 bg-white relative botanical-pattern scroll-mt-20">
            <div class="negative-space-container">
                <div class="text-center mb-20 space-y-4">
                    <h4 class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">
                        {{ __('messages.institutional_support') }}</h4>
                    <h2 class="text-5xl font-serif text-deep-green">{{ __('messages.our_visionary_sponsors') }}</h2>
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
                                            class="px-4 py-1.5 rounded-full bg-gold-accent/10 border border-gold-accent/20 text-gold-accent text-[9px] font-bold uppercase tracking-widest">{{ __('messages.lead_partner') }}</span>
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
                                            {{ $donator->location_type ?? __('messages.supporter') }}
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
                                            {{ $donator->location ?? __('messages.international_support') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-12 text-center py-12">
                            <p class="text-charcoal/60">{{ __('messages.no_featured_donators') }}</p>
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
                            <h4 class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">
                                {{ __('messages.individual_impact') }}</h4>
                            <h2 class="text-5xl font-serif text-deep-green">{{ __('messages.making_a_difference') }}</h2>
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
                    <h4 class="text-gold-accent font-bold tracking-[0.4em] text-[10px] uppercase">
                        {{ __('messages.community_of_support') }}</h4>
                    <h2 class="text-5xl font-serif text-deep-green">{{ __('messages.all_our_donators') }}</h2>
                    <div class="w-24 h-1 bg-gold-accent/30 mx-auto mt-6"></div>
                    <div class="flex justify-center gap-6 md:gap-8 lg:gap-12 text-deep-green flex-wrap">
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold">{{ $totalDonators }}</div>
                            <div class="text-xs md:text-sm uppercase tracking-wider">{{ __('messages.donators') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold">{{ number_format($totalTrees) }}</div>
                            <div class="text-xs md:text-sm uppercase tracking-wider">{{ __('messages.trees') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold">${{ number_format($totalFinancial, 0) }}</div>
                            <div class="text-xs md:text-sm uppercase tracking-wider">{{ __('messages.total_support') }}
                            </div>
                        </div>
                    </div>
                </div>


                @include('partials.donators-table', ['verifiedDonators' => $verifiedDonators])

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
                    if (!tableContainer) {
                        console.error('Table container not found');
                        window.location.href = url;
                        return;
                    }

                    // Show loading state
                    tableContainer.style.opacity = '0.5';

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

                            // Update table
                            const newTable = doc.querySelector('.overflow-x-auto');
                            console.log('Found new table:', newTable);

                            if (newTable && tableContainer) {
                                const currentTable = tableContainer.querySelector('.overflow-x-auto');
                                if (currentTable) {
                                    currentTable.replaceWith(newTable);
                                    console.log('Table updated successfully');
                                } else {
                                    console.error('Could not find current table');
                                }
                            } else {
                                console.error('Could not find new table, falling back to page reload');
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
                    // Only load page if this is a genuine back/forward navigation
                    if (e.state !== null) {
                        loadPage(window.location.href);
                    }
                });
            });
        </script>
    @endpush
@endsection
