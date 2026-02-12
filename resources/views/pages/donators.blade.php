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
                        @forelse($featuredDonators->take(1) as $featuredDonator)
                            <div class="space-y-6">
                                <h3 class="text-3xl font-serif text-deep-green">{{ $featuredDonator->full_name }}</h3>
                                <p class="text-charcoal/70 text-lg leading-relaxed font-light">
                                    {{ $featuredDonator->impact ?? 'A passionate advocate for environmental conservation and sustainable urban development in Kabul.' }}
                                </p>
                                <div class="grid grid-cols-3 gap-8 py-8">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-gold-accent">
                                            {{ $featuredDonator->trees_sponsored ?? 0 }}</div>
                                        <div class="text-sm text-charcoal/60 uppercase tracking-wider">Trees Planted</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-gold-accent">
                                            ${{ number_format($featuredDonator->financial_support ?? 0, 0) }}</div>
                                        <div class="text-sm text-charcoal/60 uppercase tracking-wider">Support</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-gold-accent">
                                            {{ $featuredDonator->location ?? 'Kabul' }}</div>
                                        <div class="text-sm text-charcoal/60 uppercase tracking-wider">Location</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="space-y-6">
                                <h3 class="text-3xl font-serif text-deep-green">Join Our Mission</h3>
                                <p class="text-charcoal/70 text-lg leading-relaxed font-light">
                                    Become part of our growing community of environmental stewards who are making a real
                                    difference in Kabul's green future.
                                </p>
                            </div>
                        @endforelse
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
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[600px]">
                            <thead class="bg-deep-green text-white">
                                <tr>
                                    <th class="text-left py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                                        Donator</th>
                                    <th class="text-left py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                                        Impact</th>
                                    <th class="text-left py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                                        Location</th>
                                    <th class="text-right py-4 px-4 md:py-6 md:px-8 font-semibold text-sm md:text-base">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @forelse($verifiedDonators as $donator)
                                    <tr class="donor-row transition-colors cursor-pointer group hover:bg-stone-50">
                                        <td class="py-4 px-4 md:py-6 md:px-8">
                                            <div class="flex items-center gap-3 md:gap-4">
                                                <div
                                                    class="w-10 h-10 md:w-12 md:h-12 rounded-full overflow-hidden border-2 border-stone-100 group-hover:border-gold-accent transition-colors flex-shrink-0">
                                                    @if ($donator->profile_image)
                                                        <img alt="{{ $donator->full_name }} Profile"
                                                            class="w-full h-full object-cover"
                                                            src="{{ asset('storage/' . $donator->profile_image) }}" />
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-deep-green/10 flex items-center justify-center">
                                                            <span
                                                                class="material-symbols-outlined text-deep-green text-sm md:text-base">person</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <span
                                                    class="text-deep-green font-serif text-lg md:text-xl group-hover:text-gold-accent transition-colors truncate">
                                                    {{ $donator->full_name }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 md:py-6 md:px-8">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-sage-green text-sm">park</span>
                                                <span
                                                    class="text-charcoal font-medium text-sm md:text-base">{{ $donator->trees_sponsored }}
                                                    Trees</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 md:py-6 md:px-8">
                                            <span
                                                class="text-charcoal/60 text-sm md:text-base truncate block max-w-[120px] md:max-w-none">
                                                {{ $donator->location ?? 'International' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 md:py-6 md:px-8 text-right">
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 md:gap-1.5 md:px-3 md:py-1 bg-deep-green/5 rounded-full">
                                                <span
                                                    class="material-symbols-outlined text-[12px] md:text-[14px] text-deep-green">verified</span>
                                                <span
                                                    class="text-[8px] md:text-[9px] font-bold text-deep-green uppercase tracking-widest hidden sm:inline">Verified</span>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="py-12 px-4 md:px-8 text-center text-charcoal/60 text-sm md:text-base">
                                            No verified donators yet. Be the first to support our mission!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

@endsection
