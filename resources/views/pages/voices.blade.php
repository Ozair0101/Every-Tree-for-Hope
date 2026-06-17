@extends('layouts.layout')
@section('title', __('messages.voices_page_title'))
@section('content')

    {{--
        VOICES OF NATURE — a community wall.
        The block design here is intentionally different from the rest of the site:
        instead of a uniform card grid, voices are pinned to a board like hand-written
        field notes — staggered masonry columns, a strip of "tape", slight rotation.
        Each card is clickable (stretched link) yet carries its own AJAX like button.
    --}}

    <main class="flex-grow bg-[#f7f6f1] dark:bg-background-dark">

        {{-- ───────────────────────── Hero ───────────────────────── --}}
        <section class="relative overflow-hidden bg-deep-green">
            <div class="absolute inset-0 opacity-25"
                style="background-image:radial-gradient(circle at 15% 20%, rgba(132,204,22,.45) 0, transparent 38%),radial-gradient(circle at 85% 10%, rgba(255,255,255,.35) 0, transparent 40%),radial-gradient(circle at 70% 90%, rgba(20,184,166,.4) 0, transparent 45%);">
            </div>
            <div class="absolute inset-0 opacity-[0.06]"
                style="background-image:url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2280%22><text y=%2255%22 font-size=%2255%22>🌿</text></svg>');background-size:90px;">
            </div>

            <div class="relative max-w-6xl mx-auto px-6 pt-20 pb-24 text-center">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-[11px] font-bold uppercase tracking-[0.18em] backdrop-blur-sm mb-6">
                    <span class="material-symbols-outlined text-sm">public</span>
                    {{ __('messages.voices_hero_badge') }}
                </span>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif text-white leading-[1.1] mb-4">
                    {{ __('messages.voices_hero_title') }}
                </h1>
                <p class="text-lg text-white/80 max-w-2xl mx-auto mb-9">
                    {{ __('messages.voices_hero_subtitle') }}
                </p>

                <a href="{{ route('voices.create') }}"
                    class="inline-flex items-center gap-2 px-7 py-3.5 bg-vibrant-lime text-deep-green text-sm font-extrabold rounded-full shadow-xl shadow-black/20 hover:bg-white hover:scale-[1.03] transition-all">
                    <span class="material-symbols-outlined text-lg">edit_note</span>
                    {{ __('messages.voices_share_btn') }}
                </a>

                <div class="mt-12 flex flex-wrap items-center justify-center gap-x-10 gap-y-4 text-white">
                    <div class="text-center">
                        <div class="text-3xl font-extrabold">{{ number_format($stats['total']) }}</div>
                        <div class="text-xs uppercase tracking-wider text-white/60">{{ __('messages.voices_stat_shared') }}</div>
                    </div>
                    <div class="hidden sm:block h-10 w-px bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-3xl font-extrabold">{{ number_format($stats['countries']) }}</div>
                        <div class="text-xs uppercase tracking-wider text-white/60">{{ __('messages.voices_stat_countries') }}</div>
                    </div>
                    <div class="hidden sm:block h-10 w-px bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-3xl font-extrabold">{{ number_format($stats['likes']) }}</div>
                        <div class="text-xs uppercase tracking-wider text-white/60">{{ __('messages.voices_stat_hearts') }}</div>
                    </div>
                </div>
            </div>

            <div class="absolute -bottom-px left-0 right-0 h-6 bg-[#f7f6f1] dark:bg-background-dark"
                style="clip-path:polygon(0 60%,4% 40%,8% 60%,12% 35%,16% 60%,20% 45%,24% 60%,28% 38%,32% 60%,36% 42%,40% 60%,44% 36%,48% 60%,52% 44%,56% 60%,60% 38%,64% 60%,68% 42%,72% 60%,76% 36%,80% 60%,84% 44%,88% 60%,92% 38%,96% 60%,100% 42%,100% 100%,0 100%);">
            </div>
        </section>

        {{-- ─────────────── Filters / search toolbar ─────────────── --}}
        <section class="max-w-6xl mx-auto px-6 pt-10">
            @if (session('voice_submitted'))
                <div class="mb-8 flex items-start gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-800">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('voices.index', array_filter(['q' => $search])) }}"
                        class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ $category === '' ? 'bg-deep-green text-white' : 'bg-white text-charcoal hover:bg-deep-green/10 border border-black/5' }}">
                        {{ __('messages.voices_filter_all') }}
                    </a>
                    @foreach ($categories as $key => $label)
                        <a href="{{ route('voices.index', array_filter(['category' => $key, 'q' => $search])) }}"
                            class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ $category === $key ? 'bg-deep-green text-white' : 'bg-white text-charcoal hover:bg-deep-green/10 border border-black/5' }}">
                            {{ __('messages.voices_cat_' . $key) }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('voices.index') }}" class="relative w-full lg:w-72">
                    @if ($category)<input type="hidden" name="category" value="{{ $category }}">@endif
                    <span class="material-symbols-outlined absolute start-4 top-1/2 -translate-y-1/2 text-charcoal/40 text-xl pointer-events-none">search</span>
                    <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('messages.voices_search_placeholder') }}"
                        class="w-full bg-white rounded-full ps-12 pe-4 py-3 text-sm text-charcoal placeholder:text-charcoal/40 border border-black/5 shadow-sm focus:ring-2 focus:ring-deep-green/30 focus:outline-none">
                </form>
            </div>
        </section>

        {{-- ──────────────────── The wall ──────────────────── --}}
        <section class="max-w-6xl mx-auto px-6 py-12">
            @if ($voices->isEmpty())
                <div class="text-center py-24">
                    <div class="text-6xl mb-4">🌱</div>
                    <h3 class="text-2xl font-serif text-deep-green mb-2">{{ __('messages.voices_empty_title') }}</h3>
                    <p class="text-charcoal/60 mb-6">{{ __('messages.voices_empty_subtitle') }}</p>
                    <a href="{{ route('voices.create') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-deep-green text-white text-sm font-bold rounded-full hover:bg-deep-green/90 transition-colors">
                        <span class="material-symbols-outlined text-lg">edit_note</span>
                        {{ __('messages.voices_share_btn') }}
                    </a>
                </div>
            @else
                <div class="columns-1 sm:columns-2 lg:columns-3 gap-6">
                    @foreach ($voices as $voice)
                        @php
                            $tilt = [-2, 1.5, -1, 2, -1.5, 1][$loop->index % 6];
                            $liked = in_array($voice->id, $likedIds, true);
                        @endphp
                        <div class="voice-note group relative mb-6 break-inside-avoid bg-white rounded-[14px] border border-black/5 shadow-[0_10px_30px_-12px_rgba(0,0,0,0.25)] overflow-hidden hover:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.35)] transition-all duration-300"
                            style="--tilt:{{ $tilt }}deg;">

                            {{-- tape --}}
                            <span class="pointer-events-none absolute left-1/2 -top-2 z-20 h-5 w-20 -translate-x-1/2 -rotate-2 bg-vibrant-lime/40 backdrop-blur-sm rounded-[2px] shadow-sm"></span>

                            @if ($voice->image_url)
                                <div class="relative aspect-[4/3] overflow-hidden bg-stone-100">
                                    <img src="{{ $voice->image_url }}" alt="{{ $voice->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-deep-green/90 text-white text-[10px] font-bold uppercase tracking-wide backdrop-blur-sm">{{ __('messages.voices_cat_' . $voice->category) }}</span>
                                </div>
                            @endif

                            <div class="p-5">
                                @unless ($voice->image_url)
                                    <span class="inline-block mb-3 px-2.5 py-1 rounded-full bg-deep-green/10 text-deep-green text-[10px] font-bold uppercase tracking-wide">{{ __('messages.voices_cat_' . $voice->category) }}</span>
                                @endunless

                                <h3 class="font-serif text-lg leading-snug text-charcoal group-hover:text-deep-green transition-colors mb-2">
                                    {{ $voice->title }}
                                </h3>
                                <p class="text-sm text-charcoal/70 leading-relaxed mb-5">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($voice->body), 150) }}
                                </p>

                                {{-- author footer --}}
                                <div class="flex items-center justify-between pt-4 border-t border-dashed border-black/10">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-white text-[11px] font-bold"
                                            style="background:{{ $voice->author_color }}">{{ $voice->author_initials }}</span>
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-charcoal truncate">{{ $voice->author_name }}</div>
                                            @if ($voice->country)
                                                <div class="text-[11px] text-charcoal/50 truncate flex items-center gap-0.5">
                                                    <span class="material-symbols-outlined text-[13px]">location_on</span>{{ $voice->country }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        {{-- AJAX like button — sits above the stretched link (z-10) --}}
                                        <button type="button" data-like-btn data-url="{{ route('voices.like', $voice) }}"
                                            aria-pressed="{{ $liked ? 'true' : 'false' }}"
                                            class="like-btn like-chip relative z-10 inline-flex items-center gap-1 px-2 py-1 rounded-full text-charcoal/60 text-xs hover:bg-rose-50 hover:text-rose-500 transition-colors {{ $liked ? 'is-liked' : '' }}">
                                            <span class="material-symbols-outlined like-icon text-[18px]">favorite</span>
                                            <span data-like-count>{{ $voice->likes_count }}</span>
                                        </button>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-charcoal/50 text-xs">
                                            <span class="material-symbols-outlined text-[16px]">chat_bubble</span>{{ $voice->comments_count }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- stretched link: whole card navigates to the watch page --}}
                            <a href="{{ route('voices.show', $voice) }}" class="absolute inset-0 z-0" aria-label="{{ $voice->title }}"></a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $voices->links() }}
                </div>
            @endif
        </section>
    </main>

    <style>
        .voice-note { transform: rotate(var(--tilt)); }
        .voice-note:hover { transform: rotate(0deg) translateY(-4px); }
        @media (max-width: 640px) { .voice-note { transform: none !important; } }
    </style>

    @include('partials.voice-like')
@endsection
