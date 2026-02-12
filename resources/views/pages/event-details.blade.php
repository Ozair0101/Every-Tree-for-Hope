@extends('layouts.layout')
@section('title', $event->title . ' - Event Details')
@section('content')

    <main class="flex-grow">
        <!-- Hero Section with Event Image -->
        <section class="relative h-[400px] md:h-[500px] overflow-hidden">
            @forelse($event->images->take(1) as $image)
                <img alt="{{ $event->title }}" class="w-full h-full object-cover" src="{{ $image->full_image_url }}" />
            @empty
                <img alt="{{ $event->title }}" class="w-full h-full object-cover"
                    src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" />
            @endforelse

            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>

            <!-- Back Button -->
            <div class="absolute top-4 left-4 z-10">
                <a href="{{ route('gallery') }}"
                    class="flex items-center gap-2 bg-white/10 backdrop-blur-sm text-white border border-white/20 hover:bg-white/20 transition-all rounded-lg px-4 py-2">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span class="text-sm font-medium">Back to Gallery</span>
                </a>
            </div>

            <!-- Event Title Overlay -->
            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12">
                <div class="max-w-4xl mx-auto">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-accent/20 border border-gold-accent/40 text-white text-xs font-bold uppercase tracking-widest mb-4 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-xs">event</span>
                        Event Details
                    </div>
                    <h1 class="text-4xl md:text-6xl font-serif text-white mb-4 leading-tight">
                        {{ $event->title }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-4 text-white/90">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined">calendar_today</span>
                            <span>{{ $event->date->format('F j, Y') }}</span>
                        </div>
                        @if ($event->location)
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined">location_on</span>
                                <span>{{ $event->location }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Event Content -->
        <section class="py-16 max-w-6xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Description -->
                    <div>
                        <h2 class="text-3xl font-serif text-deep-green mb-6">About This Event</h2>
                        <div class="prose prose-lg max-w-none text-charcoal/80 leading-relaxed">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    <!-- Event Gallery -->
                    @if ($event->images->count() > 1)
                        <div>
                            <h3 class="text-2xl font-serif text-deep-green mb-6">Event Gallery</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($event->images->skip(1) as $image)
                                    <div class="relative aspect-video bg-stone-100 rounded-lg overflow-hidden">
                                        <img alt="{{ $image->caption ?? $event->title }}"
                                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300 cursor-pointer"
                                            src="{{ $image->full_image_url }}" />
                                        @if ($image->caption)
                                            <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white p-3">
                                                <p class="text-sm">{{ $image->caption }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Tree Species -->
                    @if ($event->all_tree_species)
                        <div>
                            <h3 class="text-2xl font-serif text-deep-green mb-6">Tree Species Planted</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($event->all_tree_species as $species)
                                    <span
                                        class="px-3 py-2 bg-gold-accent/10 text-deep-green rounded-full text-sm font-medium border border-gold-accent/20">
                                        {{ $species }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Impact Stats -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-serif text-deep-green mb-6">Event Impact</h3>
                        <div class="space-y-4">
                            <div class="text-center p-4 bg-stone-50 rounded-lg">
                                <div class="flex items-center justify-center gap-2 text-gold-accent mb-2">
                                    <span class="material-symbols-outlined">park</span>
                                    <span class="text-sm font-bold uppercase">Trees Planted</span>
                                </div>
                                <p class="text-3xl font-bold text-deep-green">{{ $event->trees_planted ?? 0 }}</p>
                            </div>

                            <div class="text-center p-4 bg-stone-50 rounded-lg">
                                <div class="flex items-center justify-center gap-2 text-gold-accent mb-2">
                                    <span class="material-symbols-outlined">groups</span>
                                    <span class="text-sm font-bold uppercase">Volunteers</span>
                                </div>
                                <p class="text-3xl font-bold text-deep-green">{{ $event->volunteers ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Event Details -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-serif text-deep-green mb-6">Event Details</h3>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-gold-accent mt-1">calendar_today</span>
                                <div>
                                    <p class="font-medium text-deep-green">Date</p>
                                    <p class="text-charcoal/60">{{ $event->date->format('l, F j, Y') }}</p>
                                </div>
                            </div>

                            @if ($event->location)
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-gold-accent mt-1">location_on</span>
                                    <div>
                                        <p class="font-medium text-deep-green">Location</p>
                                        <p class="text-charcoal/60">{{ $event->location }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($event->province)
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-gold-accent mt-1">public</span>
                                    <div>
                                        <p class="font-medium text-deep-green">Province</p>
                                        <p class="text-charcoal/60">{{ $event->province }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($event->sponsor_partner)
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-gold-accent mt-1">handshake</span>
                                    <div>
                                        <p class="font-medium text-deep-green">Partner</p>
                                        <p class="text-charcoal/60">{{ $event->sponsor_partner }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="bg-gradient-to-br from-deep-green to-gold-accent rounded-xl p-6 text-white text-center">
                        <h3 class="text-xl font-serif mb-4">Support Our Mission</h3>
                        <p class="mb-6 text-white/90">Help us plant more trees and create a greener future for Kabul.</p>
                        <a href="{{ route('donators') }}"
                            class="inline-flex items-center gap-2 bg-white text-deep-green px-6 py-3 rounded-lg font-bold hover:bg-white/90 transition-all">
                            <span class="material-symbols-outlined">volunteer_activism</span>
                            Become a Donator
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related Events -->
        @if ($relatedEvents->count() > 0)
            <section class="py-16 bg-stone-50">
                <div class="max-w-6xl mx-auto px-6">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-serif text-deep-green mb-4">Related Events</h2>
                        <p class="text-lg text-charcoal/70">Explore more of our tree planting initiatives</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($relatedEvents as $relatedEvent)
                            <div
                                class="group bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300">
                                <div class="relative aspect-video bg-stone-100 overflow-hidden">
                                    @forelse($relatedEvent->images->take(1) as $image)
                                        <img alt="{{ $relatedEvent->title }}"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                            src="{{ $image->full_image_url }}" />
                                    @empty
                                        <img alt="{{ $relatedEvent->title }}"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                            src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" />
                                    @endforelse

                                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur rounded-full px-3 py-1">
                                        <span class="text-xs font-bold text-deep-green">
                                            {{ $relatedEvent->date->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <h4
                                        class="text-lg font-serif text-deep-green mb-2 group-hover:text-gold-accent transition-colors">
                                        {{ $relatedEvent->title }}
                                    </h4>
                                    <p class="text-charcoal/60 text-sm mb-3 line-clamp-2">
                                        {{ Str::limit($relatedEvent->description, 80) }}
                                    </p>
                                    <a href="{{ route('events.show', $relatedEvent) }}"
                                        class="inline-flex items-center gap-1 text-gold-accent font-medium text-sm hover:gap-2 transition-all">
                                        <span>View Details</span>
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>

    @push('styles')
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .prose {
                color: #4b5563;
            }

            .prose p {
                margin-bottom: 1rem;
            }
        </style>
    @endpush
@endsection
