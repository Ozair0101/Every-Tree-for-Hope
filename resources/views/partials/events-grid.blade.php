<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-8">
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
                <h3 class="text-xl font-serif text-deep-green mb-2 group-hover:text-gold-accent transition-colors">
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
                <a href="{{ route('events.show', $event->id) }}"
                    class="w-full bg-deep-green hover:bg-gold-accent text-white py-3 rounded-lg font-bold text-sm transition-all text-center block">
                    View Event Details
                </a>
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
    <div class="mt-12 flex justify-center" id="pagination-container">
        {{ $events->links() }}
    </div>
@endif
