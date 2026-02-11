@extends('layouts.layout')
@section('title', 'Report Page')
@section('content')
    <main>
        <section class="relative h-[80vh] flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img alt="Blurred Greenhouse" class="w-full h-full object-cover blur-sm brightness-95"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs" />
                <div class="absolute inset-0 bg-white/40"></div>
            </div>
            <div class="relative z-10 text-center space-y-6 max-w-4xl px-6">
                <div
                    class="inline-flex items-center gap-3 bg-white/80 px-5 py-2 rounded-full border border-gold-accent/20 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-gold-accent text-sm">auto_awesome</span>
                    <p class="text-deep-green text-[10px] font-bold uppercase tracking-[0.2em]">Accountability in every
                        petal</p>
                </div>
                <h1 class="text-deep-green text-6xl md:text-8xl font-serif font-bold leading-tight">
                    Transparency <br />
                    <span class="text-gold-accent italic font-light">in Bloom.</span>
                </h1>
                <p class="text-deep-green/80 text-lg md:text-xl font-medium max-w-2xl mx-auto">
                    A detailed chronicle of our ecological footprint and financial stewardship across the hills of
                    Kabul.
                </p>
            </div>
        </section>

        <section class="py-24 px-6 md:px-12 lg:px-24 botanical-bg">
            <div class="max-w-7xl mx-auto">
                <div
                    class="glass-panel rounded-[3rem] p-12 md:p-20 relative overflow-hidden flex flex-col lg:flex-row items-center gap-16 border-gold-accent/10">
                    <div class="flex-1 space-y-8">
                        @php
                            $totalTrees = \App\Models\Event::active()->sum('trees_planted');
                            $totalVolunteers = \App\Models\Event::active()->sum('volunteers');
                            $totalEvents = \App\Models\Event::active()->count();
                            $survivalRate = 92; // This could be a field in the future
                        @endphp
                        <h4 class="text-gold-accent font-bold tracking-[0.3em] text-xs uppercase">Fiscal Year 2024</h4>
                        <h2 class="text-4xl md:text-6xl font-serif text-deep-green leading-tight">Yearly Growth
                            <br /><span class="italic font-light">By The Numbers</span>
                        </h2>
                        <p class="text-charcoal/70 text-lg leading-relaxed">Our most successful year yet. We've
                            expanded our nursery capacity by 40% and achieved a record survival rate for our native
                            almond and pine saplings in the northern ridges.</p>
                        <button
                            class="group flex items-center gap-4 bg-deep-green text-white px-8 py-4 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-deep-green/90 transition-all">
                            <span class="material-symbols-outlined text-gold-accent">eco</span>
                            Download Full PDF Report
                        </button>
                    </div>
                    <div class="flex-1 grid grid-cols-2 gap-8 w-full">
                        <div class="p-8 border-l border-gold-accent/20">
                            <p class="text-5xl font-serif text-deep-green mb-2">{{ $survivalRate }}%</p>
                            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest">Survival Rate
                            </p>
                        </div>
                        <div class="p-8 border-l border-gold-accent/20">
                            <p class="text-5xl font-serif text-deep-green mb-2">{{ number_format($totalTrees) }}</p>
                            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest">Trees Planted
                            </p>
                        </div>
                        <div class="p-8 border-l border-gold-accent/20">
                            <p class="text-5xl font-serif text-deep-green mb-2">{{ round($totalTrees * 0.021) }}</p>
                            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest">Tons CO2 Offset
                            </p>
                        </div>
                        <div class="p-8 border-l border-gold-accent/20">
                            <p class="text-5xl font-serif text-deep-green mb-2">{{ $totalEvents }}</p>
                            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest">Events Completed
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative bg-green-50 flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
            <div class="layout-container flex h-full grow flex-col">
                <div class="flex flex-1 justify-center py-5 sm:px-10 lg:px-20 xl:px-40">
                    <div class="layout-content-container flex flex-col w-full max-w-5xl flex-1">
                        <!-- TopNavBar -->
                        <main class="flex-grow px-4 py-8 sm:py-12">
                            <!-- PageHeading -->
                            <div class="flex flex-wrap justify-between gap-3 p-4">
                                <h1
                                    class="text-[#111714] dark:text-[#f6f8f7] text-4xl sm:text-5xl font-black leading-tight tracking-[-0.033em] w-full text-center">
                                    Our Impact Report</h1>
                            </div>
                            <!-- BodyText -->
                            @php
                                $events = \App\Models\Event::active()->orderBy('date', 'desc')->paginate(6);
                            @endphp
                            <p
                                class="text-center text-base font-normal leading-normal pb-6 pt-2 px-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 max-w-3xl mx-auto">
                                This table shows the cumulative results of community efforts, events, and donations. Thank
                                you for helping us green our neighborhoods. Total: {{ number_format($totalTrees) }} trees
                                planted by {{ number_format($totalVolunteers) }} volunteers across {{ $totalEvents }}
                                events.
                            </p>
                            <!-- Chips -->
                            <div class="flex flex-wrap items-center justify-center gap-3 p-3 overflow-x-hidden">
                                <button
                                    class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-primary/20 dark:bg-primary/30 px-4">
                                    <p class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal">Filter
                                        by Year</p>
                                    <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                        style="font-size: 20px;">keyboard_arrow_down</span>
                                </button>
                                <button
                                    class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-primary/20 dark:bg-primary/30 px-4">
                                    <p class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal">Filter
                                        by Region</p>
                                    <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                        style="font-size: 20px;">keyboard_arrow_down</span>
                                </button>
                            </div>
                            <!-- Table -->
                            <div id="events-table-container" class="px-4 py-6 @container">
                                <div
                                    class="overflow-hidden rounded-xl border border-primary/20 dark:border-primary/30 bg-white dark:bg-background-dark shadow-sm">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left">
                                            <thead class="hidden md:table-header-group bg-primary/20 dark:bg-primary/30">
                                                <tr>
                                                    <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                        scope="col">#</th>
                                                    <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                        scope="col">Event/Initiative</th>
                                                    <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                        scope="col">Location</th>
                                                    <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                        scope="col">Date</th>
                                                    <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal text-right"
                                                        scope="col">Trees Planted</th>
                                                    <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal text-right"
                                                        scope="col">Volunteers</th>
                                                    <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                        scope="col">Sponsor/Partner</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $counter = ($events->currentPage() - 1) * $events->perPage() + 1;
                                            @endphp
                                            <tbody class="divide-y divide-primary/20 dark:divide-primary/30">
                                                @forelse($events as $event)
                                                    <tr
                                                        class="block md:table-row p-4 border-b border-primary/20 dark:border-primary/30 md:border-none">
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714] dark:text-[#f6f8f7] text-sm font-medium md:font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="#">#{{ $counter }}</td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714] dark:text-[#f6f8f7] text-sm font-medium md:font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Event/Initiative">{{ $event->title }}</td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Location">{{ $event->location }}</td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Date">{{ $event->formatted_date }}</td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Trees Planted">
                                                            {{ number_format($event->trees_planted) }}</td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Volunteers">{{ number_format($event->volunteers) }}
                                                        </td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Sponsor/Partner">
                                                            {{ $event->sponsor_partner ?? 'N/A' }}</td>
                                                    </tr>
                                                    @php $counter++; @endphp
                                                @empty
                                                    <tr>
                                                        <td colspan="7"
                                                            class="text-center py-8 text-[#111714]/60 dark:text-[#f6f8f7]/60">
                                                            No events found. Check back soon for updates!
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Pagination and CTA -->
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 px-4 py-8">
                                <div class="flex items-center gap-2" id="pagination-container">
                                    @if ($events->hasPages())
                                        <button onclick="loadPage({{ $events->currentPage() - 1 }})"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/20 dark:border-primary/30 hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors {{ $events->onFirstPage() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ $events->onFirstPage() ? 'disabled' : '' }}>
                                            <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                                style="font-size: 20px;">chevron_left</span>
                                        </button>

                                        @for ($i = 1; $i <= $events->lastPage(); $i++)
                                            @if ($i == $events->currentPage())
                                                <button
                                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/20 dark:border-primary/30 bg-primary/20 dark:bg-primary/30 text-sm font-bold text-primary dark:text-primary">{{ $i }}</button>
                                            @elseif(abs($i - $events->currentPage()) <= 2 || $i == 1 || $i == $events->lastPage())
                                                <button onclick="loadPage({{ $i }})"
                                                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/20 dark:border-primary/30 text-sm font-medium text-[#111714]/80 dark:text-[#f6f8f7]/80 hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors">{{ $i }}</button>
                                            @elseif(abs($i - $events->currentPage()) == 3)
                                                <span
                                                    class="flex h-9 w-9 items-center justify-center text-sm text-[#111714]/60 dark:text-[#f6f8f7]/60">...</span>
                                            @endif
                                        @endfor

                                        <button onclick="loadPage({{ $events->currentPage() + 1 }})"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-primary/20 dark:border-primary/30 hover:bg-primary/20 dark:hover:bg-primary/30 transition-colors {{ $events->hasMorePages() ? '' : 'opacity-50 cursor-not-allowed' }}"
                                            {{ $events->hasMorePages() ? '' : 'disabled' }}>
                                            <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                                style="font-size: 20px;">chevron_right</span>
                                        </button>
                                    @endif
                                </div>
                                <a href="{{ route('home') }}#events"
                                    class="flex w-full sm:w-auto min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-opacity-90 transition-opacity">
                                    <span>Join an Event Near You</span>
                                    <span class="material-symbols-outlined">arrow_forward</span>
                                </a>
                            </div>
                        </main>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 px-6 md:px-12 lg:px-24 bg-white">
            <div class="max-w-7xl mx-auto space-y-20">
                <div class="text-center space-y-4">
                    <h4 class="text-gold-accent font-bold tracking-[0.3em] text-xs uppercase">Progress Visualized</h4>
                    <h2 class="text-4xl md:text-5xl font-serif text-deep-green">Data-Driven Restoration</h2>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <div class="space-y-6">
                        <h5 class="text-deep-green font-bold text-lg px-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">trending_up</span>
                            Carbon Sequestration (Metric Tons)
                        </h5>
                        <div class="h-64 border-b border-l border-charcoal/10 relative p-4">
                            <svg class="w-full h-full" viewBox="0 0 400 200">
                                <path class="chart-line" d="M0,180 Q100,160 200,100 T400,20" fill="none"
                                    stroke="#064e3b" stroke-width="3"></path>
                                <circle cx="0" cy="180" fill="#d4af37" r="4"></circle>
                                <circle cx="100" cy="165" fill="#d4af37" r="4"></circle>
                                <circle cx="200" cy="100" fill="#d4af37" r="4"></circle>
                                <circle cx="400" cy="20" fill="#d4af37" r="4"></circle>
                            </svg>
                            <div
                                class="flex justify-between mt-4 text-[10px] font-bold text-charcoal/40 uppercase tracking-tighter">
                                <span>2020</span><span>2021</span><span>2022</span><span>2023 (Target)</span>
                            </div>
                        </div>
                        <p class="text-charcoal/60 text-sm font-light italic leading-relaxed">Cumulative data reflects
                            the maturation of our initial 2018 canopy. Carbon absorption exponentializes as trees enter
                            the five-year growth phase.</p>
                    </div>
                    <div class="space-y-6">
                        <h5 class="text-deep-green font-bold text-lg px-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">pie_chart</span>
                            Species Distribution 2023
                        </h5>
                        <div class="flex items-center gap-12">
                            <div class="relative w-48 h-48">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    <circle cx="18" cy="18" fill="transparent" r="15.9" stroke="#064e3b"
                                        stroke-dasharray="45 100" stroke-width="3.5"></circle>
                                    <circle cx="18" cy="18" fill="transparent" r="15.9" stroke="#84cc16"
                                        stroke-dasharray="30 100" stroke-dashoffset="-45" stroke-width="3.5"></circle>
                                    <circle cx="18" cy="18" fill="transparent" r="15.9" stroke="#d4af37"
                                        stroke-dasharray="25 100" stroke-dashoffset="-75" stroke-width="3.5"></circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined text-deep-green opacity-20 text-5xl">forest</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-deep-green rounded-full"></div>
                                    <span class="text-xs font-bold text-charcoal/80 uppercase tracking-widest">Almond
                                        (45%)</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-vibrant-lime rounded-full"></div>
                                    <span class="text-xs font-bold text-charcoal/80 uppercase tracking-widest">Pine
                                        (30%)</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-gold-accent rounded-full"></div>
                                    <span class="text-xs font-bold text-charcoal/80 uppercase tracking-widest">Pomegranate
                                        (25%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 px-6 md:px-12 lg:px-24 bg-background-light">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-end gap-8 mb-16">
                    <div class="space-y-4">
                        <h4 class="text-gold-accent font-bold tracking-[0.3em] text-xs uppercase">Field Updates</h4>
                        <h2 class="text-4xl md:text-5xl font-serif text-deep-green">Latest from <span
                                class="italic font-light">the Soil</span></h2>
                    </div>
                    <button
                        class="text-deep-green text-xs font-bold uppercase tracking-widest border-b border-gold-accent pb-1">Archive
                        2022</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="group cursor-pointer">
                        <div class="relative rounded-3xl overflow-hidden aspect-[4/5] mb-6">
                            <img alt="Sapling Care"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5PZeVaTDepGXwxR10OE6Feq5BCgdn9QWsVmA4IbXGKfKSs73Z3RJmcJr94tWX6qn3cwUdQkyUzzyb1rcDVCCH8xmAwSGxDZm1r3_nO-wCov2_Zkigwdde52aed5g0M6LOMOF_6QRpACjAY2NxqbwfbGtUy1D4R7vCjhXxFrvsbaSJgSVhB7z3LRSnxWBUdv5fpb6X7rSLf-Uy-cjJiEH_2VbWXYagPry5eiNyF7wx9DeU9PZTGBenuYElX7QHOixLCBDOHMz49PM" />
                            <div
                                class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-full shadow-sm">
                                <p class="text-deep-green text-[10px] font-extrabold uppercase tracking-widest">Oct 12,
                                    2023</p>
                            </div>
                        </div>
                        <h4 class="text-xl font-serif text-deep-green mb-2 group-hover:text-gold-accent transition-colors">
                            Irrigation Breakthrough in Paghman</h4>
                        <p class="text-charcoal/60 text-sm font-medium leading-relaxed">Implementation of gravity-fed
                            water systems reduced manual labor by 60%.</p>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="relative rounded-3xl overflow-hidden aspect-[4/5] mb-6">
                            <img alt="Community Team"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5PZeVaTDepGXwxR10OE6Feq5BCgdn9QWsVmA4IbXGKfKSs73Z3RJmcJr94tWX6qn3cwUdQkyUzzyb1rcDVCCH8xmAwSGxDZm1r3_nO-wCov2_Zkigwdde52aed5g0M6LOMOF_6QRpACjAY2NxqbwfbGtUy1D4R7vCjhXxFrvsbaSJgSVhB7z3LRSnxWBUdv5fpb6X7rSLf-Uy-cjJiEH_2VbWXYagPry5eiNyF7wx9DeU9PZTGBenuYElX7QHOixLCBDOHMz49PM" />
                            <div
                                class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-full shadow-sm">
                                <p class="text-deep-green text-[10px] font-extrabold uppercase tracking-widest">Sep 28,
                                    2023</p>
                            </div>
                        </div>
                        <h4 class="text-xl font-serif text-deep-green mb-2 group-hover:text-gold-accent transition-colors">
                            Women's Cooperative Expansion</h4>
                        <p class="text-charcoal/60 text-sm font-medium leading-relaxed">20 new families joined the
                            central Kabul nursery initiative this month.</p>
                    </div>
                    <div class="group cursor-pointer">
                        <div class="relative rounded-3xl overflow-hidden aspect-[4/5] mb-6">
                            <img alt="Afghan Landscape"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5PZeVaTDepGXwxR10OE6Feq5BCgdn9QWsVmA4IbXGKfKSs73Z3RJmcJr94tWX6qn3cwUdQkyUzzyb1rcDVCCH8xmAwSGxDZm1r3_nO-wCov2_Zkigwdde52aed5g0M6LOMOF_6QRpACjAY2NxqbwfbGtUy1D4R7vCjhXxFrvsbaSJgSVhB7z3LRSnxWBUdv5fpb6X7rSLf-Uy-cjJiEH_2VbWXYagPry5eiNyF7wx9DeU9PZTGBenuYElX7QHOixLCBDOHMz49PM" />
                            <div
                                class="absolute top-4 left-4 bg-white/90 backdrop-blur-md px-4 py-2 rounded-full shadow-sm">
                                <p class="text-deep-green text-[10px] font-extrabold uppercase tracking-widest">Aug 15,
                                    2023</p>
                            </div>
                        </div>
                        <h4 class="text-xl font-serif text-deep-green mb-2 group-hover:text-gold-accent transition-colors">
                            Slope Stabilization Success</h4>
                        <p class="text-charcoal/60 text-sm font-medium leading-relaxed">Root systems of the 2021
                            planting have successfully anchored the North Slope.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 px-6 md:px-12 lg:px-24 bg-white border-t border-charcoal/5">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div class="space-y-8">
                    <h4 class="text-gold-accent font-bold tracking-[0.3em] text-xs uppercase">Financial Integrity</h4>
                    <h2 class="text-4xl md:text-5xl font-serif text-deep-green leading-tight">Where your <br /><span
                            class="italic font-light">contributions go.</span></h2>
                    <p class="text-charcoal/70 text-lg leading-relaxed font-light">We maintain a lean administrative
                        structure to ensure that 85% of all funds are deployed directly into Kabul's soil through
                        seedlings and local labor.</p>
                    <div class="flex items-center gap-6 pt-4">
                        <div class="text-center">
                            <p class="text-3xl font-serif text-deep-green">85%</p>
                            <p class="text-[10px] font-bold text-charcoal/40 uppercase tracking-widest">Direct Impact
                            </p>
                        </div>
                        <div class="w-[1px] h-12 bg-gold-accent/20"></div>
                        <div class="text-center">
                            <p class="text-3xl font-serif text-deep-green">10%</p>
                            <p class="text-[10px] font-bold text-charcoal/40 uppercase tracking-widest">Maintenance</p>
                        </div>
                        <div class="w-[1px] h-12 bg-gold-accent/20"></div>
                        <div class="text-center">
                            <p class="text-3xl font-serif text-deep-green">5%</p>
                            <p class="text-[10px] font-bold text-charcoal/40 uppercase tracking-widest">Admin</p>
                        </div>
                    </div>
                </div>
                <div class="bg-background-light rounded-[2rem] p-8 md:p-12 border border-charcoal/5 shadow-sm">
                    <table class="w-full">
                        <thead class="border-b border-charcoal/10">
                            <tr>
                                <th
                                    class="text-left py-4 text-[10px] font-bold uppercase tracking-widest text-charcoal/40">
                                    Allocation</th>
                                <th
                                    class="text-right py-4 text-[10px] font-bold uppercase tracking-widest text-charcoal/40">
                                    Amount (USD)</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium">
                            <tr class="border-b border-charcoal/5">
                                <td class="py-6 flex items-center gap-3">
                                    <span class="material-symbols-outlined text-gold-accent text-sm">potted_plant</span>
                                    Seedlings &amp; Nursery
                                </td>
                                <td class="text-right py-6 text-deep-green font-bold">$124,500</td>
                            </tr>
                            <tr class="border-b border-charcoal/5">
                                <td class="py-6 flex items-center gap-3">
                                    <span class="material-symbols-outlined text-gold-accent text-sm">groups</span>
                                    Local Labor &amp; Wages
                                </td>
                                <td class="text-right py-6 text-deep-green font-bold">$98,200</td>
                            </tr>
                            <tr class="border-b border-charcoal/5">
                                <td class="py-6 flex items-center gap-3">
                                    <span class="material-symbols-outlined text-gold-accent text-sm">water_drop</span>
                                    Irrigation &amp; Tools
                                </td>
                                <td class="text-right py-6 text-deep-green font-bold">$42,100</td>
                            </tr>
                            <tr>
                                <td class="py-6 flex items-center gap-3">
                                    <span class="material-symbols-outlined text-gold-accent text-sm">hub</span>
                                    Logistics &amp; Ops
                                </td>
                                <td class="text-right py-6 text-deep-green font-bold">$15,400</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="py-32 px-6 text-center relative overflow-hidden bg-background-light">
            <span class="material-symbols-outlined absolute -top-10 -left-10 text-[15rem] text-gold-accent/5">spa</span>
            <span
                class="material-symbols-outlined absolute -bottom-10 -right-10 text-[15rem] text-gold-accent/5">leaf_spark</span>
            <div class="max-w-3xl mx-auto relative z-10 space-y-10">
                <h2 class="text-5xl font-serif text-deep-green italic">Every dollar planted today <br /><span
                        class="font-bold not-italic">is a forest for tomorrow.</span></h2>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <button
                        class="w-full sm:w-auto px-10 py-5 bg-deep-green text-white rounded-xl font-bold text-xs uppercase tracking-[0.2em] shadow-lg shadow-deep-green/20">Invest
                        in the Earth</button>
                    <button
                        class="w-full sm:w-auto px-10 py-5 border border-gold-accent text-deep-green rounded-xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-gold-accent/5 transition-colors">Become
                        a Partner</button>
                </div>
            </div>
        </section>
    </main>

    <script>
        function loadPage(page) {
            // Show loading state
            const container = document.getElementById('events-table-container');
            const originalContent = container.innerHTML;
            container.style.opacity = '0.5';

            // Create URL with page parameter
            const url = `{{ route('report') }}?page=${page}`;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Create a temporary DOM element to parse the response
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    // Extract the new table content
                    const newContent = tempDiv.querySelector('#events-table-container');
                    if (newContent) {
                        container.innerHTML = newContent.innerHTML;
                    }

                    // Extract and update pagination
                    const newPagination = tempDiv.querySelector('#pagination-container');
                    if (newPagination) {
                        document.getElementById('pagination-container').innerHTML = newPagination.innerHTML;
                    }

                    // Restore opacity
                    container.style.opacity = '1';

                    // Smooth scroll to top of table
                    container.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                })
                .catch(error => {
                    console.error('Error loading page:', error);
                    container.style.opacity = '1';
                });
        }

        // Prevent form submissions from reloading the page
        document.addEventListener('click', function(e) {
            if (e.target.closest('button[onclick*="loadPage"]')) {
                e.preventDefault();
            }
        });
    </script>
@endsection
