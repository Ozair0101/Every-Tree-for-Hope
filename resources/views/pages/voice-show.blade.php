@extends('layouts.layout')
@section('title', $voice->title . ' — ' . __('messages.voices_page_title'))
@section('content')

    {{--
        Single voice — laid out like a video "watch" page:
        a wide main column (media → title → action bar → description → comments)
        and a right rail of "more voices to explore".
    --}}

    <main class="flex-grow bg-white dark:bg-background-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 lg:py-8">

            {{-- back link --}}
            <a href="{{ route('voices.index') }}"
                class="inline-flex items-center gap-1.5 text-sm font-bold text-charcoal/60 hover:text-deep-green transition-colors mb-5">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                {{ __('messages.voices_back') }}
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- ═══════════════ Main column ═══════════════ --}}
                <div class="lg:col-span-2 min-w-0">

                    {{-- Media / hero --}}
                    @if ($voice->image_url)
                        <div class="rounded-2xl overflow-hidden bg-stone-100 mb-5">
                            <img src="{{ $voice->image_url }}" alt="{{ $voice->title }}" class="w-full max-h-[520px] object-cover">
                        </div>
                    @else
                        <div class="relative rounded-2xl overflow-hidden mb-5 aspect-[16/7] flex items-center justify-center"
                            style="background:linear-gradient(135deg,{{ $voice->author_color }} 0%, #064e3b 100%);">
                            <div class="absolute inset-0 opacity-20"
                                style="background-image:radial-gradient(circle at 20% 30%, rgba(255,255,255,.5) 0, transparent 40%);"></div>
                            <span class="text-7xl relative">
                                @switch($voice->category)
                                    @case('idea') 💡 @break
                                    @case('finding') 🔬 @break
                                    @case('question') ❓ @break
                                    @default 🌿
                                @endswitch
                            </span>
                        </div>
                    @endif

                    {{-- category + title --}}
                    <span class="inline-block mb-3 px-3 py-1 rounded-full bg-deep-green/10 text-deep-green text-[11px] font-bold uppercase tracking-wide">{{ __('messages.voices_cat_' . $voice->category) }}</span>
                    <h1 class="text-2xl md:text-3xl font-serif text-charcoal dark:text-white leading-snug mb-4">{{ $voice->title }}</h1>

                    {{-- Action bar (YouTube-style) --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-black/10 dark:border-white/10">
                        {{-- author --}}
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="flex-shrink-0 h-11 w-11 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                style="background:{{ $voice->author_color }}">{{ $voice->author_initials }}</span>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-charcoal dark:text-white truncate">{{ $voice->author_name }}</div>
                                <div class="text-xs text-charcoal/50 dark:text-white/50 flex items-center gap-2">
                                    @if ($voice->country)
                                        <span class="inline-flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">location_on</span>{{ $voice->country }}</span>
                                    @endif
                                    <span>{{ number_format($voice->views_count) }} {{ __('messages.voices_views') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- actions --}}
                        <div class="flex items-center gap-2">
                            <button type="button" data-like-btn data-url="{{ route('voices.like', $voice) }}"
                                aria-pressed="{{ $hasLiked ? 'true' : 'false' }}"
                                class="like-btn like-pill inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold bg-black/5 dark:bg-white/10 text-charcoal dark:text-white hover:bg-black/10 transition-colors {{ $hasLiked ? 'is-liked' : '' }}">
                                <span class="material-symbols-outlined like-icon text-lg">favorite</span>
                                <span data-like-count>{{ $voice->likes_count }}</span>
                            </button>

                            <button type="button" data-share-link data-url="{{ route('voices.show', $voice) }}" data-copied="{{ __('messages.voices_copied') }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-black/5 dark:bg-white/10 text-charcoal dark:text-white text-sm font-bold hover:bg-black/10 transition-colors">
                                <span class="material-symbols-outlined text-lg">share</span>
                                <span class="hidden sm:inline">{{ __('messages.voices_share_action') }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- Description box (YouTube-style) --}}
                    <div class="mt-5 rounded-2xl bg-stone-100 dark:bg-white/5 p-5">
                        <div class="text-xs font-bold text-charcoal/70 dark:text-white/70 mb-3">
                            {{ $voice->created_at->format('M j, Y') }} · {{ number_format($voice->likes_count) }} {{ __('messages.voices_hearts_label') }}
                        </div>
                        <div class="prose prose-sm max-w-none text-charcoal/90 dark:text-white/80 leading-relaxed whitespace-pre-line">{{ $voice->body }}</div>
                    </div>

                    {{-- ─────────── Comments ─────────── --}}
                    <section id="comments" class="mt-8">
                        <h2 class="text-lg font-bold text-charcoal dark:text-white mb-5">
                            <span data-comment-count>{{ number_format($voice->comments_count) }}</span> {{ __('messages.voices_comments') }}
                        </h2>

                        {{-- add comment (AJAX) --}}
                        <form id="comment-form" method="POST" action="{{ route('voices.comment', $voice) }}" class="flex gap-3 mb-8">
                            @csrf
                            <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                            <span class="flex-shrink-0 h-10 w-10 rounded-full bg-deep-green/15 text-deep-green flex items-center justify-center">
                                <span class="material-symbols-outlined">person</span>
                            </span>
                            <div class="flex-1 space-y-2.5">
                                <p data-comment-error class="hidden text-xs text-rose-600"></p>
                                <input type="text" name="author_name" required maxlength="120" placeholder="{{ __('messages.voices_comment_name') }}"
                                    class="w-full sm:w-64 rounded-lg border border-black/10 px-3 py-2 text-sm focus:ring-2 focus:ring-deep-green/30 focus:outline-none">
                                <textarea name="body" required rows="2" maxlength="2000" placeholder="{{ __('messages.voices_comment_placeholder') }}"
                                    class="w-full rounded-lg border border-black/10 px-3 py-2 text-sm focus:ring-2 focus:ring-deep-green/30 focus:outline-none"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2 bg-deep-green text-white text-sm font-bold rounded-full hover:bg-deep-green/90 transition-colors disabled:opacity-60">
                                        <span class="material-symbols-outlined text-base">send</span>{{ __('messages.voices_comment_btn') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- comment list --}}
                        <div id="comment-list" class="space-y-6">
                            @foreach ($comments as $comment)
                                <div class="flex gap-3">
                                    <span class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                        style="background:{{ $comment->author_color }}">{{ $comment->author_initials }}</span>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-bold text-charcoal dark:text-white">{{ $comment->author_name }}</span>
                                            <span class="text-xs text-charcoal/40">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-charcoal/80 dark:text-white/70 leading-relaxed whitespace-pre-line">{{ $comment->body }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p data-no-comments class="text-sm text-charcoal/50 py-6 text-center {{ $comments->isEmpty() ? '' : 'hidden' }}">{{ __('messages.voices_no_comments') }}</p>
                    </section>
                </div>

                {{-- ═══════════════ Right rail — more voices ═══════════════ --}}
                <aside class="lg:col-span-1">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-charcoal/50 mb-4">{{ __('messages.voices_more') }}</h2>
                    <div class="space-y-3">
                        @foreach ($related as $item)
                            <a href="{{ route('voices.show', $item) }}" class="group flex gap-3 p-2 -mx-2 rounded-xl hover:bg-black/[0.03] dark:hover:bg-white/5 transition-colors">
                                <div class="flex-shrink-0 w-32 aspect-video rounded-lg overflow-hidden">
                                    @if ($item->image_url)
                                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-2xl"
                                            style="background:linear-gradient(135deg,{{ $item->author_color }} 0%, #064e3b 100%);">
                                            @switch($item->category)
                                                @case('idea') 💡 @break
                                                @case('finding') 🔬 @break
                                                @case('question') ❓ @break
                                                @default 🌿
                                            @endswitch
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-charcoal dark:text-white leading-snug line-clamp-2 group-hover:text-deep-green transition-colors">{{ $item->title }}</h3>
                                    <div class="mt-1 text-xs text-charcoal/50 truncate">{{ $item->author_name }}</div>
                                    <div class="mt-0.5 text-xs text-charcoal/40 flex items-center gap-2">
                                        <span class="inline-flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">favorite</span>{{ $item->likes_count }}</span>
                                        <span class="inline-flex items-center gap-0.5"><span class="material-symbols-outlined text-[14px]">visibility</span>{{ $item->views_count }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </aside>
            </div>
        </div>
    </main>

    {{-- Like behaviour is shared with the wall page. --}}
    @include('partials.voice-like')

    <script>
        (function () {
            const token = '{{ csrf_token() }}';

            // ── Comments (AJAX, no reload) ──
            const form = document.getElementById('comment-form');
            if (form) {
                const list = document.getElementById('comment-list');
                const errorEl = form.querySelector('[data-comment-error]');
                const countEl = document.querySelector('[data-comment-count]');
                const noComments = document.querySelector('[data-no-comments]');

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const submitBtn = form.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    errorEl.classList.add('hidden');

                    try {
                        const res = await fetch(form.action, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new FormData(form),
                        });

                        if (res.status === 422) {
                            const err = await res.json();
                            const first = Object.values(err.errors || {})[0];
                            errorEl.textContent = first ? first[0] : 'Please check your input.';
                            errorEl.classList.remove('hidden');
                        } else if (res.ok) {
                            const data = await res.json();
                            const c = data.comment;

                            const el = document.createElement('div');
                            el.className = 'flex gap-3';
                            el.innerHTML =
                                '<span class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:' + c.color + '"></span>' +
                                '<div class="min-w-0"><div class="flex items-center gap-2 mb-1">' +
                                '<span class="text-sm font-bold text-charcoal dark:text-white"></span>' +
                                '<span class="text-xs text-charcoal/40">' + c.time + '</span></div>' +
                                '<p class="text-sm text-charcoal/80 dark:text-white/70 leading-relaxed" style="white-space:pre-line"></p></div>';
                            // set text safely (avoid HTML injection)
                            el.querySelector('span.rounded-full').textContent = c.initials;
                            el.querySelector('.font-bold').textContent = c.author_name;
                            el.querySelector('p').textContent = c.body;

                            list.prepend(el);
                            if (noComments) noComments.classList.add('hidden');
                            if (countEl) countEl.textContent = data.count;
                            form.reset();
                        } else {
                            throw new Error('failed');
                        }
                    } catch (err) {
                        errorEl.textContent = 'Something went wrong. Please try again.';
                        errorEl.classList.remove('hidden');
                    }

                    submitBtn.disabled = false;
                });
            }

            // ── Share ──
            const share = document.querySelector('[data-share-link]');
            if (share) {
                share.addEventListener('click', async function () {
                    const url = share.dataset.url;
                    if (navigator.share) {
                        try { await navigator.share({ title: document.title, url }); } catch (e) {}
                    } else {
                        try {
                            await navigator.clipboard.writeText(url);
                            const label = share.querySelector('span:last-child');
                            if (label) { const old = label.textContent; label.textContent = share.dataset.copied || 'Copied!'; setTimeout(() => label.textContent = old, 1500); }
                        } catch (e) {}
                    }
                });
            }
        })();
    </script>
@endsection
