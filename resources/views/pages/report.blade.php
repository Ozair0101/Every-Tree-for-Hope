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
                            // First, build the events query with filters
                            $events = \App\Models\Event::active();

                            // Apply year filter
                            if (request('year') && request('year') !== 'all') {
                                $events = $events->whereYear('date', request('year'));
                            }

                            // Apply region filter
                            if (request('region') && request('region') !== 'all') {
                                $region = request('region');
                                $events = $events->where('province', $region);
                            }

                            // Get the events for statistics (before pagination)
                            $eventsForStats = clone $events;
                            $eventsForStats = $eventsForStats->orderBy('date', 'desc')->get();

                            // Calculate dynamic statistics for filtered events
                            $filteredTrees = $eventsForStats->sum('trees_planted');
                            $filteredVolunteers = $eventsForStats->sum('volunteers');
                            $filteredEvents = $eventsForStats->count();
                            $filteredCO2 = $filteredTrees * 0.021; // 21kg CO2 per tree
                            $survivalRate = 92; // Default survival rate percentage

                            // Now paginate for display
                            $events = $events->orderBy('date', 'desc')->paginate(6);

                            // Get years for filter dropdown
                            $years = \App\Models\Event::active()
                                ->orderBy('date', 'desc')
                                ->distinct()
                                ->pluck('date')
                                ->map(function ($date) {
                                    return \Carbon\Carbon::parse($date)->format('Y');
                                })
                                ->unique()
                                ->sort()
                                ->reverse();
                        @endphp
                        <h4 class="text-gold-accent font-bold tracking-[0.3em] text-xs uppercase">
                            @if (request('year') && request('year') !== 'all')
                                {{ request('year') }}
                            @elseif(request('region') && request('region') !== 'all')
                                {{ request('region') }}
                            @else
                                All Time
                            @endif
                            Report
                        </h4>
                        <h2 class="text-4xl md:text-6xl font-serif text-deep-green leading-tight">Yearly Growth
                            <br /><span class="italic font-light">By The Numbers</span>
                        </h2>
                        <p class="text-charcoal/70 text-lg leading-relaxed">
                            @if (request('year') && request('year') !== 'all')
                                Showing results for {{ request('year') }}. We've expanded our efforts and achieved
                                remarkable results across all our planting initiatives.
                            @elseif(request('region') && request('region') !== 'all')
                                Showing results for {{ request('region') }} province. Our regional teams have made
                                significant impact in local communities.
                            @else
                                Our most successful year yet. We've expanded our nursery capacity by 40% and achieved a
                                record survival rate for our native almond and pine saplings in the northern ridges.
                            @endif
                        </p>
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
                            <p class="text-5xl font-serif text-deep-green mb-2">{{ number_format($filteredTrees) }}</p>
                            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest">Trees Planted
                            </p>
                        </div>
                        <div class="p-8 border-l border-gold-accent/20">
                            <p class="text-5xl font-serif text-deep-green mb-2">{{ number_format($filteredVolunteers) }}</p>
                            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest">Volunteers
                            </p>
                        </div>
                        <div class="p-8 border-l border-gold-accent/20">
                            <p class="text-5xl font-serif text-deep-green mb-2">{{ number_format($filteredEvents) }}</p>
                            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest">Events Completed
                            </p>
                        </div>
                        <div class="p-8 border-l border-gold-accent/20">
                            <p class="text-5xl font-serif text-deep-green mb-2">{{ number_format($filteredCO2, 1) }}</p>
                            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest">Tons CO₂ Offset
                            </p>
                        </div>
                        <div class="p-8 border-l border-gold-accent/20">
                            <p class="text-5xl font-serif text-deep-green mb-2">
                                {{ number_format(($filteredTrees * $survivalRate) / 100) }}</p>
                            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-widest">Trees Surviving
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="relative bg-green-50 flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-visible">
            <div class="layout-container flex h-full grow flex-col">
                <div class="flex flex-1 justify-center py-5 sm:px-10 lg:px-20 xl:px-40">
                    <div class="layout-content-container flex flex-col w-full flex-1">
                        <!-- TopNavBar -->
                        <main class="flex-grow px-4 py-8 sm:py-12">
                            <!-- PageHeading -->
                            <div class="flex flex-wrap justify-between gap-3 p-4">
                                <h1
                                    class="text-[#111714] dark:text-[#f6f8f7] text-4xl sm:text-5xl font-black leading-tight tracking-[-0.033em] w-full text-center">
                                    Our Impact Report</h1>
                            </div>
                            <!-- BodyText -->
                            <p
                                class="text-center text-base font-normal leading-normal pb-6 pt-2 px-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 max-w-3xl mx-auto">
                                This table shows the cumulative results of community efforts, events, and donations. Thank
                                you for helping us green our neighborhoods. Total: {{ number_format($filteredTrees) }}
                                trees
                                planted by {{ number_format($filteredVolunteers) }} volunteers across
                                {{ $filteredEvents }}
                                events.
                            </p>
                            <!-- Chips -->
                            <div
                                class="flex flex-wrap items-center justify-center gap-3 p-3 overflow-x-visible relative z-[100]">
                                <!-- Year Filter Dropdown -->
                                <div class="relative">
                                    <button onclick="toggleDropdown('year-dropdown')"
                                        class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-primary/20 dark:bg-primary/30 px-4 hover:bg-primary/30 dark:hover:bg-primary/40 transition-colors">
                                        <p class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal">
                                            Filter
                                            by Year</p>
                                        <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                            style="font-size: 20px;">keyboard_arrow_down</span>
                                    </button>
                                    <div id="year-dropdown"
                                        class="absolute top-full left-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-primary/20 dark:border-primary/30 hidden z-[9999] shadow-2xl">
                                        <button onclick="filterByYear('all')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary/20 transition-colors">All
                                            Years</button>
                                        @foreach ($years as $year)
                                            <button onclick="filterByYear('{{ $year }}')"
                                                class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary/20 transition-colors">{{ $year }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Region Filter Dropdown -->
                                <div class="relative">
                                    <button onclick="toggleDropdown('region-dropdown')"
                                        class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg bg-primary/20 dark:bg-primary/30 px-4 hover:bg-primary/30 dark:hover:bg-primary/40 transition-colors">
                                        <p class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal">
                                            Filter
                                            by Region</p>
                                        <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                            style="font-size: 20px;">keyboard_arrow_down</span>
                                    </button>
                                    <div id="region-dropdown"
                                        class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-primary/20 dark:border-primary/30 hidden z-[9999] max-h-64 overflow-y-auto shadow-2xl">
                                        <button onclick="filterByRegion('all')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary/20 transition-colors">All
                                            Regions</button>
                                        <button onclick="filterByRegion('Badakhshan')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary/20 transition-colors">Badakhshan</button>
                                        <button onclick="filterByRegion('Badghis')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary/20 transition-colors">Badghis</button>
                                        <button onclick="filterByRegion('Baghlan')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Baghlan</button>
                                        <button onclick="filterByRegion('Balkh')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Balkh</button>
                                        <button onclick="filterByRegion('Bamyan')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Bamyan</button>
                                        <button onclick="filterByRegion('Daykundi')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Daykundi</button>
                                        <button onclick="filterByRegion('Farah')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Farah</button>
                                        <button onclick="filterByRegion('Faryab')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Faryab</button>
                                        <button onclick="filterByRegion('Ghazni')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Ghazni</button>
                                        <button onclick="filterByRegion('Ghor')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Ghor</button>
                                        <button onclick="filterByRegion('Helmand')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Helmand</button>
                                        <button onclick="filterByRegion('Herat')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Herat</button>
                                        <button onclick="filterByRegion('Jowzjan')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Jowzjan</button>
                                        <button onclick="filterByRegion('Kabul')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Kabul</button>
                                        <button onclick="filterByRegion('Kandahar')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Kandahar</button>
                                        <button onclick="filterByRegion('Kapisa')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Kapisa</button>
                                        <button onclick="filterByRegion('Khost')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Khost</button>
                                        <button onclick="filterByRegion('Kunar')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Kunar</button>
                                        <button onclick="filterByRegion('Kunduz')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Kunduz</button>
                                        <button onclick="filterByRegion('Laghman')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Laghman</button>
                                        <button onclick="filterByRegion('Logar')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Logar</button>
                                        <button onclick="filterByRegion('Maimana')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Maimana</button>
                                        <button onclick="filterByRegion('Nangarhar')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Nangarhar</button>
                                        <button onclick="filterByRegion('Nimruz')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Nimruz</button>
                                        <button onclick="filterByRegion('Nuristan')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Nuristan</button>
                                        <button onclick="filterByRegion('Paktia')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Paktia</button>
                                        <button onclick="filterByRegion('Paktika')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Paktika</button>
                                        <button onclick="filterByRegion('Panjshir')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Panjshir</button>
                                        <button onclick="filterByRegion('Parwan')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Parwan</button>
                                        <button onclick="filterByRegion('Samangan')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Samangan</button>
                                        <button onclick="filterByRegion('Sar-e Pol')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Sar-e
                                            Pol</button>
                                        <button onclick="filterByRegion('Takhar')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Takhar</button>
                                        <button onclick="filterByRegion('Urozgan')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Urozgan</button>
                                        <button onclick="filterByRegion('Wardak')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Wardak</button>
                                        <button onclick="filterByRegion('Zabul')"
                                            class="w-full text-left px-4 py-2 text-sm hover:bg-primary/10 dark:hover:bg-primary-20 transition-colors">Zabul</button>
                                    </div>
                                </div>

                                <!-- Clear Filters Button -->
                                <button onclick="clearFilters()"
                                    class="flex h-9 shrink-0 items-center justify-center gap-x-2 rounded-lg border border-primary/20 dark:border-primary/30 px-4 hover:bg-primary/10 dark:hover:bg-primary/20 transition-colors">
                                    <p class="text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal">Clear
                                        Filters</p>
                                    <span class="material-symbols-outlined text-[#111714] dark:text-[#f6f8f7]"
                                        style="font-size: 20px;">clear</span>
                                </button>
                            </div>
                            <!-- Table -->
                            <div id="events-table-container" class="px-4 py-6">
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
                                                        scope="col">Province</th>
                                                    <th class="px-6 py-4 text-left text-[#111714] dark:text-[#f6f8f7] text-sm font-medium leading-normal"
                                                        scope="col">Tree Species</th>
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
                                                            data-label="Province">{{ $event->province ?? 'N/A' }}</td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Tree Species">
                                                            @if ($event->all_tree_species && count($event->all_tree_species) > 0)
                                                                {{ implode(', ', array_slice($event->all_tree_species, 0, 3)) }}
                                                                @if (count($event->all_tree_species) > 3)
                                                                    +{{ count($event->all_tree_species) - 3 }}
                                                                @endif
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Date">{{ $event->formatted_date }}</td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Trees Planted">
                                                            {{ number_format($event->trees_planted) }}</td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal md:text-right before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Volunteers">
                                                            {{ number_format($event->volunteers) }}
                                                        </td>
                                                        <td class="block md:table-cell px-2 md:px-6 py-2 md:py-4 text-[#111714]/80 dark:text-[#f6f8f7]/80 text-sm font-normal leading-normal before:content-[attr(data-label)] before:font-bold before:mr-2 before:md:hidden"
                                                            data-label="Sponsor/Partner">
                                                            {{ $event->sponsor_partner ?? 'N/A' }}</td>
                                                    </tr>
                                                    @php $counter++; @endphp
                                                @empty
                                                    <tr>
                                                        <td colspan="9"
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
                @php
                    // Calculate monthly statistics for current year
                    $monthlyStats = \App\Models\Event::active()
                        ->whereYear('date', date('Y'))
                        ->get()
                        ->groupBy(function ($event) {
                            return \Carbon\Carbon::parse($event->date)->format('M');
                        })
                        ->map(function ($monthEvents, $month) {
                            return [
                                'month' => $month,
                                'total_trees' => $monthEvents->sum('trees_planted'),
                                'total_volunteers' => $monthEvents->sum('volunteers'),
                                'total_events' => $monthEvents->count(),
                            ];
                        })
                        ->sortBy(function ($stat) {
                            return date('m', strtotime($stat['month']));
                        })
                        ->values();

                    // Calculate species distribution (mock data for now - could be added to events table)
                    $speciesDistribution = [
                        'Almond' => 45,
                        'Pine' => 30,
                        'Pomegranate' => 25,
                    ];

                    // Calculate monthly CO2 sequestration
                    $monthlyCO2Data = $monthlyStats->map(function ($stat) {
                        return [
                            'month' => $stat['month'],
                            'trees' => $stat['total_trees'],
                            'co2' => $stat['total_trees'] * 0.021,
                        ];
                    });
                @endphp
                <div class="text-center space-y-4">
                    <h4 class="text-gold-accent font-bold tracking-[0.3em] text-xs uppercase">Progress Visualized</h4>
                    <h2 class="text-4xl md:text-5xl font-serif text-deep-green">Monthly Analysis {{ date('Y') }}</h2>
                    <p class="text-charcoal/60 max-w-2xl mx-auto">
                        Tracking {{ number_format($filteredTrees) }} trees planted across {{ $monthlyStats->count() }}
                        months in {{ date('Y') }} with {{ number_format($filteredVolunteers) }} volunteers
                    </p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                    <div class="space-y-6">
                        <h5 class="text-deep-green font-bold text-lg px-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">nature</span>
                            Trees Planted by Month
                        </h5>
                        <div class="h-64 border-b border-l border-charcoal/10 relative p-4">
                            @php
                                $maxMonthlyTrees = $monthlyStats->max('total_trees') ?: 1;
                                $monthlyChartPoints = [];
                                $monthlyXStep = 400 / max(1, $monthlyStats->count() - 1);

                                foreach ($monthlyCO2Data as $index => $data) {
                                    $x = $index * $monthlyXStep;
                                    $y = 200 - ($data['trees'] / $maxMonthlyTrees) * 180;
                                    $monthlyChartPoints[] = $index == 0 ? "M{$x},{$y}" : "L{$x},{$y}";
                                }

                                $monthlyPathData = implode(' ', $monthlyChartPoints);
                            @endphp
                            <svg class="w-full h-full" viewBox="0 0 400 200">
                                <path class="chart-line" d="{{ $monthlyPathData }}" fill="none" stroke="#064e3b"
                                    stroke-width="3"></path>
                                @foreach ($monthlyCO2Data as $index => $data)
                                    @php
                                        $x = $index * $monthlyXStep;
                                        $y = 200 - ($data['trees'] / $maxMonthlyTrees) * 180;
                                    @endphp
                                    <circle cx="{{ $x }}" cy="{{ $y }}" fill="#d4af37" r="4">
                                    </circle>
                                @endforeach
                            </svg>
                            <div
                                class="flex justify-between mt-4 text-[10px] font-bold text-charcoal/40 uppercase tracking-tighter">
                                @foreach ($monthlyCO2Data as $data)
                                    <span>{{ $data['month'] }}</span>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-charcoal/60 text-sm font-light italic leading-relaxed">
                            Monthly tree planting progress for {{ date('Y') }}.
                            {{ $monthlyStats->count() }} active months with
                            {{ number_format($monthlyStats->sum('total_trees')) }} trees planted.
                        </p>
                    </div>
                    <div class="space-y-6">
                        <h5 class="text-deep-green font-bold text-lg px-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">pie_chart</span>
                            Species Distribution {{ date('Y') }}
                        </h5>
                        <div class="flex items-center gap-12">
                            <div class="relative w-48 h-48">
                                @php
                                    $totalPercentage = array_sum($speciesDistribution);
                                    $dashOffset = 0;
                                @endphp
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                    @foreach ($speciesDistribution as $species => $percentage)
                                        @php
                                            $dashArray = $percentage . ' ' . (100 - $percentage);
                                        @endphp
                                        <circle cx="18" cy="18" fill="transparent" r="15.9"
                                            @if ($species == 'Almond') stroke="#064e3b"
                                            @elseif($species == 'Pine') stroke="#84cc16" 
                                            @else stroke="#d4af37" @endif
                                            stroke-dasharray="{{ $dashArray }}"
                                            stroke-dashoffset="-{{ $dashOffset }}" stroke-width="3.5"></circle>
                                        @php
                                            $dashOffset += $percentage;
                                        @endphp
                                    @endforeach
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span
                                        class="material-symbols-outlined text-deep-green opacity-20 text-5xl">forest</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                @foreach ($speciesDistribution as $species => $percentage)
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-3 h-3 
                                            @if ($species == 'Almond') bg-deep-green 
                                            @elseif($species == 'Pine') bg-vibrant-lime 
                                            @else bg-gold-accent @endif 
                                            rounded-full">
                                        </div>
                                        <span
                                            class="text-xs font-bold text-charcoal/80 uppercase tracking-widest">{{ $species }}
                                            ({{ $percentage }}%)
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-charcoal/60 text-sm font-light italic leading-relaxed">
                            Species distribution optimized for local climate conditions and community needs.
                            {{ number_format($filteredTrees) }} total trees planted across all species.
                        </p>
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
                    <a href="{{ route('contact') }}"
                        class="w-full sm:w-auto px-10 py-5 border border-gold-accent text-deep-green rounded-xl font-bold text-xs uppercase tracking-[0.2em] hover:bg-gold-accent/5 transition-colors">Contact
                        Us</a>
                </div>
            </div>
        </section>
    </main>

    <script>
        let currentYear = 'all';
        let currentRegion = 'all';

        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const allDropdowns = ['year-dropdown', 'region-dropdown'];

            allDropdowns.forEach(id => {
                if (id !== dropdownId) {
                    document.getElementById(id).classList.add('hidden');
                }
            });

            dropdown.classList.toggle('hidden');
        }

        function filterByYear(year) {
            currentYear = year;
            document.getElementById('year-dropdown').classList.add('hidden');
            applyFilters();
        }

        function filterByRegion(region) {
            currentRegion = region;
            document.getElementById('region-dropdown').classList.add('hidden');
            applyFilters();
        }

        function clearFilters() {
            currentYear = 'all';
            currentRegion = 'all';
            applyFilters();
        }

        function applyFilters() {
            const container = document.getElementById('events-table-container');
            container.style.opacity = '0.5';

            const url = `{{ route('report') }}?page=1&year=${currentYear}&region=${currentRegion}`;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    const newContent = tempDiv.querySelector('#events-table-container');
                    if (newContent) {
                        container.innerHTML = newContent.innerHTML;
                    }

                    const newPagination = tempDiv.querySelector('#pagination-container');
                    if (newPagination) {
                        document.getElementById('pagination-container').innerHTML = newPagination.innerHTML;
                    }

                    container.style.opacity = '1';
                    container.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                })
                .catch(error => {
                    console.error('Error applying filters:', error);
                    container.style.opacity = '1';
                });
        }

        function loadPage(page) {
            const container = document.getElementById('events-table-container');
            container.style.opacity = '0.5';

            const url = `{{ route('report') }}?page=${page}&year=${currentYear}&region=${currentRegion}`;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    const newContent = tempDiv.querySelector('#events-table-container');
                    if (newContent) {
                        container.innerHTML = newContent.innerHTML;
                    }

                    const newPagination = tempDiv.querySelector('#pagination-container');
                    if (newPagination) {
                        document.getElementById('pagination-container').innerHTML = newPagination.innerHTML;
                    }

                    container.style.opacity = '1';
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

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                document.getElementById('year-dropdown').classList.add('hidden');
                document.getElementById('region-dropdown').classList.add('hidden');
            }
        });

        // Prevent form submissions from reloading the page
        document.addEventListener('click', function(e) {
            if (e.target.closest('button[onclick*="loadPage"]') ||
                e.target.closest('button[onclick*="filterBy"]') ||
                e.target.closest('button[onclick*="clearFilters"]')) {
                e.preventDefault();
            }
        });
    </script>
@endsection
