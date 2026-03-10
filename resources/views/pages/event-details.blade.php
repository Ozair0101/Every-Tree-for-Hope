@extends('layouts.layout')
@section('title', $event->title . ' - Event Details')
@section('content')

    <main class="flex-grow">
        <!-- Hero Section with Event Image -->
        <section class="relative h-[400px] md:h-[500px] overflow-hidden">
            @forelse($event->images->take(1) as $image)
                <img alt="{{ $event->title }}"
                    class="w-full h-full object-cover cursor-pointer"
                    src="{{ $image->full_image_url }}"
                    onclick="openLightbox('{{ $image->full_image_url }}', '{{ addslashes($event->title) }}', '{{ addslashes($image->caption ?? '') }}')" />
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
                    <span class="text-sm font-medium">{{ __('messages.back_to_gallery') }}</span>
                </a>
            </div>

            <!-- Event Title Overlay -->
            <div class="absolute bottom-0 left-0 right-0 p-6 md:p-12">
                <div class="max-w-4xl mx-auto">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gold-accent/20 border border-gold-accent/40 text-white text-xs font-bold uppercase tracking-widest mb-4 backdrop-blur-sm">
                        <span class="material-symbols-outlined text-xs">event</span>
                        {{ __('messages.event_details') }}
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
                        <h2 class="text-3xl font-serif text-deep-green mb-6">{{ __('messages.about_this_event') }}</h2>
                        <div class="prose prose-lg max-w-none text-charcoal/80 leading-relaxed mb-8">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    <!-- YouTube Video -->
                    @if ($event->video_url)
                        @php
                            $videoId = null;
                            if (
                                preg_match(
                                    '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=|shorts\/)|youtu\.be\/)([^"&?\/\s]{11})/',
                                    $event->video_url,
                                    $matches,
                                )
                            ) {
                                $videoId = $matches[1];
                            }
                        @endphp

                        @if ($videoId)
                            <div class="mb-12">
                                <h3 class="text-2xl font-serif text-deep-green mb-6">{{ __('messages.watch_event_video') }}
                                </h3>
                                <div
                                    class="relative aspect-video w-full rounded-2xl overflow-hidden shadow-xl bg-stone-100">
                                    <iframe class="absolute inset-0 w-full h-full border-0"
                                        src="https://www.youtube.com/embed/{{ $videoId }}?rel=0"
                                        title="{{ $event->title }}" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        @endif
                    @endif

                    <!-- Event Gallery -->
                    @if ($event->images->count() > 1)
                        <div>
                            <h3 class="text-2xl font-serif text-deep-green mb-6">{{ __('messages.event_gallery_title') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($event->images->skip(1) as $image)
                                    <div class="group relative aspect-video bg-stone-100 rounded-2xl overflow-hidden shadow-md cursor-pointer"
                                        onclick="openLightbox('{{ $image->full_image_url }}', '{{ addslashes($event->title) }}', '{{ addslashes($image->caption ?? '') }}')">
                                        <img alt="{{ $image->caption ?? $event->title }}"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                            src="{{ $image->full_image_url }}" />
                                        <!-- Hover overlay -->
                                        <div class="absolute inset-0 transition-opacity duration-500 opacity-0 group-hover:opacity-100"
                                            style="background: linear-gradient(to top, rgba(6,78,59,0.85) 0%, rgba(6,78,59,0.3) 50%, transparent 100%)">
                                        </div>
                                        <!-- Expand icon -->
                                        <div class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500 border border-white/30">
                                            <span class="material-symbols-outlined text-white text-base">open_in_full</span>
                                        </div>
                                        <!-- Caption bar -->
                                        <div class="absolute bottom-0 left-0 right-0 p-4 translate-y-2 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-500">
                                            @if ($image->caption)
                                                <p class="text-white font-medium text-sm drop-shadow">{{ $image->caption }}</p>
                                            @else
                                                <p class="text-white/80 font-medium text-sm drop-shadow">{{ $event->title }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Tree Species -->
                    @if ($event->all_tree_species)
                        <div>
                            <h3 class="text-2xl font-serif text-deep-green mb-6">{{ __('messages.tree_species_planted') }}
                            </h3>
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
                            @if ($event->event_type)
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-gold-accent mt-1">location_on</span>
                                    <div>
                                        <p class="font-medium text-deep-green">Event</p>
                                        <p class="text-charcoal/60">{{ $event->event_type }}</p>
                                    </div>
                                </div>
                            @endif
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

    <!-- Image Lightbox Modal -->
    <div id="eventLightbox"
        class="fixed inset-0 z-[100] hidden bg-black/90 backdrop-blur-md items-center justify-center p-4"
        onclick="closeLightbox(event)">
        <div class="relative w-full max-w-5xl mx-auto" onclick="event.stopPropagation()">
            <!-- Close button -->
            <button onclick="closeLightboxBtn()"
                class="absolute -top-14 right-0 text-white/70 hover:text-white transition-colors flex items-center gap-2 group">
                <span class="text-sm font-medium group-hover:underline">Close</span>
                <span class="material-symbols-outlined text-3xl">close</span>
            </button>
            <!-- Navigation arrows -->
            <button id="lightboxPrev"
                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-14 text-white/60 hover:text-white transition-colors hidden">
                <span class="material-symbols-outlined text-4xl">chevron_left</span>
            </button>
            <button id="lightboxNext"
                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-14 text-white/60 hover:text-white transition-colors hidden">
                <span class="material-symbols-outlined text-4xl">chevron_right</span>
            </button>
            <!-- Image -->
            <div class="rounded-2xl overflow-hidden shadow-2xl bg-black">
                <img id="lightboxImage" src="" alt=""
                    class="w-full max-h-[75vh] object-contain" />
            </div>
            <!-- Caption -->
            <div class="mt-5 text-center space-y-1 px-4">
                <h3 id="lightboxTitle" class="text-white font-serif text-xl font-bold"></h3>
                <p id="lightboxDesc" class="text-white/60 text-sm font-medium leading-relaxed max-w-2xl mx-auto"></p>
                <p id="lightboxCounter" class="text-white/30 text-xs mt-2"></p>
            </div>
        </div>
    </div>

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

    @push('scripts')
        <script>
            // Collect all event images in order for navigation
            const eventImages = [
                @foreach ($event->images as $image)
                {
                    src: '{{ $image->full_image_url }}',
                    title: '{{ addslashes($event->title) }}',
                    desc: '{{ addslashes($image->caption ?? '') }}'
                },
                @endforeach
            ];

            let currentLightboxIndex = 0;

            function openLightbox(src, title, desc) {
                // Find the index of this image in the array
                const idx = eventImages.findIndex(img => img.src === src);
                currentLightboxIndex = idx >= 0 ? idx : 0;
                renderLightbox();

                const lb = document.getElementById('eventLightbox');
                lb.classList.remove('hidden');
                lb.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function renderLightbox() {
                const img = eventImages[currentLightboxIndex];
                document.getElementById('lightboxImage').src = img.src;
                document.getElementById('lightboxTitle').textContent = img.title;
                document.getElementById('lightboxDesc').textContent = img.desc;
                document.getElementById('lightboxCounter').textContent =
                    (currentLightboxIndex + 1) + ' / ' + eventImages.length;

                // Show/hide nav arrows
                const prev = document.getElementById('lightboxPrev');
                const next = document.getElementById('lightboxNext');
                if (eventImages.length > 1) {
                    prev.classList.remove('hidden');
                    next.classList.remove('hidden');
                    prev.style.opacity = currentLightboxIndex === 0 ? '0.2' : '1';
                    next.style.opacity = currentLightboxIndex === eventImages.length - 1 ? '0.2' : '1';
                }
            }

            function closeLightboxBtn() {
                const lb = document.getElementById('eventLightbox');
                lb.classList.add('hidden');
                lb.classList.remove('flex');
                document.body.style.overflow = '';
            }

            function closeLightbox(event) {
                if (event.target === document.getElementById('eventLightbox')) {
                    closeLightboxBtn();
                }
            }

            document.getElementById('lightboxPrev').addEventListener('click', function() {
                if (currentLightboxIndex > 0) {
                    currentLightboxIndex--;
                    renderLightbox();
                }
            });

            document.getElementById('lightboxNext').addEventListener('click', function() {
                if (currentLightboxIndex < eventImages.length - 1) {
                    currentLightboxIndex++;
                    renderLightbox();
                }
            });

            document.addEventListener('keydown', function(e) {
                const lb = document.getElementById('eventLightbox');
                if (lb.classList.contains('hidden')) return;
                if (e.key === 'Escape') closeLightboxBtn();
                if (e.key === 'ArrowLeft' && currentLightboxIndex > 0) { currentLightboxIndex--; renderLightbox(); }
                if (e.key === 'ArrowRight' && currentLightboxIndex < eventImages.length - 1) { currentLightboxIndex++; renderLightbox(); }
            });
        </script>
    @endpush
@endsection
