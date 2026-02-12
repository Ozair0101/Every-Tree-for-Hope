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

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($events as $event)
                    <div
                        class="event-card group bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300">
                        <!-- Event Image -->
                        <div class="relative aspect-video bg-stone-100 overflow-hidden">
                            @forelse($event->images->take(1) as $image)
                                <img alt="{{ $event->title }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    src="{{ $image->full_image_url }}" />
                            @empty
                                <img alt="{{ $event->title }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                    src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" />
                            @endforelse

                            <!-- Date Badge -->
                            <div
                                class="absolute top-4 left-4 bg-white/90 backdrop-blur rounded-full px-3 py-1 border border-gold-accent/20">
                                <span class="text-xs font-bold text-deep-green">
                                    {{ $event->date->format('M d, Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Event Content -->
                        <div class="p-6">
                            <h3
                                class="text-xl font-serif text-deep-green mb-2 group-hover:text-gold-accent transition-colors">
                                {{ $event->title }}
                            </h3>

                            <p class="text-charcoal/60 text-sm mb-4 line-clamp-3">
                                {{ Str::limit($event->description, 120) }}
                            </p>

                            <!-- Event Details -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-stone-50 rounded-lg">
                                    <div class="flex items-center justify-center gap-1 text-gold-accent mb-1">
                                        <span class="material-symbols-outlined text-sm">park</span>
                                        <span class="text-xs font-bold uppercase">Trees</span>
                                    </div>
                                    <p class="text-lg font-bold text-deep-green">{{ $event->trees_planted ?? 0 }}</p>
                                </div>
                                <div class="text-center p-3 bg-stone-50 rounded-lg">
                                    <div class="flex items-center justify-center gap-1 text-gold-accent mb-1">
                                        <span class="material-symbols-outlined text-sm">groups</span>
                                        <span class="text-xs font-bold uppercase">Volunteers</span>
                                    </div>
                                    <p class="text-lg font-bold text-deep-green">{{ $event->volunteers ?? 0 }}</p>
                                </div>
                            </div>

                            <!-- Location -->
                            @if ($event->location)
                                <div class="flex items-center gap-2 text-charcoal/60 text-sm mb-4">
                                    <span class="material-symbols-outlined text-base">location_on</span>
                                    <span>{{ $event->location }}</span>
                                </div>
                            @endif

                            <!-- View Details Button -->
                            <button
                                class="w-full bg-deep-green hover:bg-gold-accent text-white py-3 rounded-lg font-bold text-sm transition-all">
                                View Event Details
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="bg-stone-50 rounded-xl p-8 max-w-md mx-auto">
                            <span class="material-symbols-outlined text-4xl text-charcoal/40 mb-4">event_available</span>
                            <h3 class="text-xl font-serif text-deep-green mb-2">No Events Yet</h3>
                            <p class="text-charcoal/60 mb-4">
                                We're busy planning our next tree planting events. Check back soon!
                            </p>
                            <a href="{{ route('contact') }}"
                                class="inline-flex items-center gap-2 bg-gold-accent text-deep-green px-6 py-3 rounded-lg font-bold text-sm transition-all hover:bg-gold-accent/90">
                                <span class="material-symbols-outlined">notifications</span>
                                Get Notified
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($events->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $events->links() }}
                </div>
            @endif
        </section>
    </main>

    @push('styles')
        <style>
            .event-card:hover img {
                transform: scale(1.05);
            }

            .line-clamp-3 {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    @endpush
@endsection
