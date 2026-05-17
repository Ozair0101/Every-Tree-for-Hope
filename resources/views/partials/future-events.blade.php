@php
    $futureEvents = \App\Models\UpcomingEvent::query()
        ->where('is_active', true)
        ->whereDate('date', '>=', \Carbon\Carbon::today())
        ->orderBy('date', 'asc')
        ->take($limit ?? 6)
        ->get();

    $fmtDaysAway = function ($event) {
        $d = (int) \Carbon\Carbon::today()->diffInDays($event->date, false);
        if ($d === 0) {
            return ['label' => __('messages.future_today'), 'icon' => 'today', 'tone' => 'gold'];
        }
        if ($d === 1) {
            return ['label' => __('messages.future_tomorrow'), 'icon' => 'event_upcoming', 'tone' => 'gold'];
        }
        return ['label' => __('messages.future_in_days', ['count' => $d]), 'icon' => 'schedule', 'tone' => 'lime'];
    };
@endphp

@if ($futureEvents->count() > 0)
    @php
        $featured = $futureEvents->first();
        $rest = $futureEvents->slice(1)->values();
        $fImages = is_array($featured->images) ? array_values($featured->images) : [];
        $fHero = $fImages[0] ?? null;
        $fExtra = max(0, count($fImages) - 1);
        $fAway = $fmtDaysAway($featured);
    @endphp

    {{-- ===== UPCOMING EVENTS — Featured + Agenda ===== --}}
    <section class="relative overflow-hidden py-20 md:py-28 lg:py-32" style="background: #fafaf5;">

        {{-- Paper grain --}}
        <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
            style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;">
        </div>

        {{-- Floating botanical decoration --}}
        <div class="absolute top-12 right-8 text-vibrant-lime/12 select-none pointer-events-none floating hidden md:block">
            <span class="material-symbols-outlined" style="font-size: 9rem;">edit_calendar</span>
        </div>
        <div class="absolute bottom-16 left-10 text-gold-accent/10 select-none pointer-events-none floating hidden lg:block"
            style="animation-delay: 3s;">
            <span class="material-symbols-outlined" style="font-size: 7rem;">forest</span>
        </div>

        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6">

            {{-- Editorial header --}}
            <div class="mb-10 md:mb-14 max-w-3xl">
                <p class="font-handwriting text-2xl md:text-3xl text-vibrant-lime mb-2 leading-none">
                    ~ {{ __('messages.future_eyebrow') }} ~
                </p>
                <div class="inline-flex items-center gap-3 mb-5">
                    <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                    <span class="text-vibrant-lime font-bold tracking-[0.4em] text-[10px] uppercase">{{ __('messages.future_kicker') }}</span>
                </div>
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-serif text-deep-green leading-[1.05] mb-5">
                    {{ __('messages.future_title_part1') }}
                    <span class="italic font-black relative inline-block">
                        <span class="relative z-10">{{ __('messages.future_title_part2') }}</span>
                        <svg class="absolute -bottom-2 left-0 w-full h-3 z-0" preserveAspectRatio="none" viewBox="0 0 200 10">
                            <path d="M2,8 Q50,2 100,5 T198,4" fill="none" stroke="#84cc16" stroke-width="3" stroke-linecap="round" />
                        </svg>
                    </span>
                </h2>
                <p class="text-charcoal/70 text-base md:text-lg font-light leading-relaxed">
                    {{ __('messages.future_description') }}
                </p>
            </div>

            {{-- ───────── NEXT UP — featured event ───────── --}}
            <p class="text-deep-green/50 text-[10px] font-bold tracking-[0.45em] uppercase mb-4 flex items-center gap-3">
                <span class="material-symbols-outlined text-sm text-vibrant-lime">bolt</span>
                {{ __('messages.future_next_up') }}
            </p>

            <article id="event-{{ $featured->id }}"
                class="future-event-card group relative bg-white border border-deep-green/10 rounded-2xl overflow-hidden shadow-[0_15px_45px_rgba(6,46,34,0.10)] hover:shadow-[0_25px_60px_rgba(6,46,34,0.16)] transition-all duration-500 mb-14 md:mb-16">

                <div class="grid grid-cols-1 md:grid-cols-2">

                    {{-- Visual side --}}
                    <div class="relative min-h-[220px] md:min-h-full overflow-hidden bg-deep-green/5">
                        @if ($fHero)
                            <img src="{{ asset('storage/' . ltrim($fHero, '/')) }}" alt="{{ $featured->title }}"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-[1500ms]">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent"></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-deep-green via-deep-green to-[#0a3d2c]"></div>
                            <span class="material-symbols-outlined absolute -bottom-6 -right-4 text-white/10 select-none" style="font-size: 13rem;">forest</span>
                        @endif

                        {{-- Big date stamp --}}
                        <div class="absolute top-5 start-5 bg-white/95 backdrop-blur-sm rounded-xl px-4 py-3 text-center shadow-lg">
                            <p class="text-gold-accent text-[10px] font-bold tracking-[0.3em] uppercase">{{ $featured->date->format('M') }}</p>
                            <p class="font-serif font-black text-deep-green text-3xl leading-none">{{ $featured->date->format('d') }}</p>
                            <p class="text-charcoal/55 text-[9px] font-bold tracking-widest uppercase">{{ $featured->date->format('D') }}</p>
                        </div>

                        @if ($fExtra > 0)
                            <span class="absolute bottom-4 end-4 inline-flex items-center gap-1 px-3 py-1 rounded-full bg-black/55 backdrop-blur-sm text-white text-[10px] font-bold tracking-widest uppercase">
                                <span class="material-symbols-outlined text-xs">photo_library</span>
                                +{{ $fExtra }}
                            </span>
                        @endif
                    </div>

                    {{-- Content side --}}
                    <div class="p-6 md:p-8 lg:p-10 flex flex-col">
                        @if ($fAway['tone'] === 'gold')
                            <span class="self-start inline-flex items-center gap-1.5 mb-4 px-3 py-1 bg-gold-accent/15 border border-gold-accent/30 text-gold-accent text-[10px] font-bold uppercase tracking-[0.2em] rounded-full">
                                <span class="material-symbols-outlined text-xs">{{ $fAway['icon'] }}</span>
                                {{ $fAway['label'] }}
                            </span>
                        @else
                            <span class="self-start inline-flex items-center gap-1.5 mb-4 px-3 py-1 bg-vibrant-lime/15 border border-vibrant-lime/30 text-deep-green text-[10px] font-bold uppercase tracking-[0.2em] rounded-full">
                                <span class="material-symbols-outlined text-xs">{{ $fAway['icon'] }}</span>
                                {{ $fAway['label'] }}
                            </span>
                        @endif

                        <h3 class="font-serif text-2xl md:text-3xl lg:text-4xl font-bold text-deep-green leading-tight mb-3">
                            {{ $featured->title }}
                        </h3>

                        <div class="flex items-start gap-2 text-charcoal/75 mb-4">
                            <span class="material-symbols-outlined text-base text-gold-accent mt-0.5 flex-shrink-0">location_on</span>
                            <p class="text-sm md:text-base font-medium">
                                {{ $featured->location }}@if ($featured->province)<span class="text-charcoal/50">, {{ $featured->province }}</span>@endif
                            </p>
                        </div>

                        @if ($featured->description)
                            <p class="text-charcoal/70 text-sm md:text-[15px] leading-relaxed mb-6">
                                {{ \Illuminate\Support\Str::limit(strip_tags($featured->description), 240) }}
                            </p>
                        @endif

                        <div class="border-t border-dashed border-charcoal/15 mb-5 mt-auto"></div>

                        @include('partials._future-event-register', ['event' => $featured])
                    </div>
                </div>
            </article>

            {{-- ───────── MORE DATES — compact agenda list ───────── --}}
            @if ($rest->count() > 0)
                <p class="text-deep-green/50 text-[10px] font-bold tracking-[0.45em] uppercase mb-4 flex items-center gap-3">
                    <span class="material-symbols-outlined text-sm text-gold-accent">calendar_month</span>
                    {{ __('messages.future_more_dates') }}
                </p>

                <div class="bg-white border border-deep-green/10 rounded-2xl divide-y divide-deep-green/10 overflow-hidden shadow-[0_10px_30px_rgba(6,46,34,0.06)]">
                    @foreach ($rest as $event)
                        @php
                            $a = $fmtDaysAway($event);
                            $aImages = is_array($event->images) ? array_values($event->images) : [];
                            $aHero = $aImages[0] ?? null;
                            $aExtra = max(0, count($aImages) - 1);
                        @endphp
                        <div id="event-{{ $event->id }}" class="future-event-card future-agenda-item">

                            {{-- Row head (always visible, slim) --}}
                            <button type="button"
                                class="future-agenda-head w-full text-start flex items-center gap-4 md:gap-5 p-4 md:p-5 hover:bg-deep-green/[0.025] transition-colors">
                                {{-- Compact date chip --}}
                                <span class="flex-shrink-0 w-14 md:w-16 text-center rounded-xl border border-deep-green/15 bg-[#fcfbf6] py-2">
                                    <span class="block text-gold-accent text-[9px] font-bold tracking-[0.25em] uppercase">{{ $event->date->format('M') }}</span>
                                    <span class="block font-serif font-black text-deep-green text-xl leading-none">{{ $event->date->format('d') }}</span>
                                </span>

                                {{-- Title + location --}}
                                <span class="flex-1 min-w-0">
                                    <span class="block font-serif text-base md:text-lg font-bold text-deep-green leading-tight truncate">
                                        {{ $event->title }}
                                    </span>
                                    <span class="flex items-center gap-1 text-charcoal/55 text-xs md:text-sm mt-0.5 truncate">
                                        <span class="material-symbols-outlined text-sm text-gold-accent">location_on</span>
                                        {{ $event->location }}@if ($event->province), {{ $event->province }}@endif
                                    </span>
                                </span>

                                {{-- Days-away pill (hidden on small) --}}
                                <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest flex-shrink-0
                                    {{ $a['tone'] === 'gold' ? 'bg-gold-accent/15 text-gold-accent' : 'bg-vibrant-lime/15 text-deep-green' }}">
                                    {{ $a['label'] }}
                                </span>

                                {{-- Chevron --}}
                                <span class="material-symbols-outlined text-deep-green/40 flex-shrink-0 agenda-chevron transition-transform duration-300">expand_more</span>
                            </button>

                            {{-- Row body (expands) --}}
                            <div class="future-agenda-body hidden px-4 md:px-5 pb-6 pt-1">

                                {{-- Event image (like the featured card) --}}
                                @if ($aHero)
                                    <div class="relative h-44 md:h-56 rounded-xl overflow-hidden bg-deep-green/5 mb-5">
                                        <img src="{{ asset('storage/' . ltrim($aHero, '/')) }}"
                                            alt="{{ $event->title }}"
                                            class="w-full h-full object-cover">
                                        <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                                        @if ($aExtra > 0)
                                            <span class="absolute top-3 end-3 inline-flex items-center gap-1 px-3 py-1 rounded-full bg-black/55 backdrop-blur-sm text-white text-[10px] font-bold tracking-widest uppercase">
                                                <span class="material-symbols-outlined text-xs">photo_library</span>
                                                +{{ $aExtra }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                @if ($event->description)
                                    <p class="text-charcoal/70 text-sm leading-relaxed mb-5 max-w-2xl">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($event->description), 220) }}
                                    </p>
                                @endif
                                @include('partials._future-event-register', ['event' => $event])
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Hand-drawn closing note --}}
            <p class="text-center mt-10 md:mt-14 font-handwriting text-xl md:text-2xl text-charcoal/55">
                ~ {{ __('messages.future_closing_note') }} ~
            </p>
        </div>

        {{-- Behavior: agenda accordion + register toggle + AJAX submit + share + deep link --}}
        <script>
            (function () {
                const cards = document.querySelectorAll('.future-event-card');

                // ── Agenda accordion ──
                const agendaItems = document.querySelectorAll('.future-agenda-item');
                function closeAgenda(item) {
                    const body = item.querySelector('.future-agenda-body');
                    const chev = item.querySelector('.agenda-chevron');
                    if (body) body.classList.add('hidden');
                    if (chev) chev.classList.remove('rotate-180');
                }
                function openAgenda(item) {
                    const body = item.querySelector('.future-agenda-body');
                    const chev = item.querySelector('.agenda-chevron');
                    if (body) body.classList.remove('hidden');
                    if (chev) chev.classList.add('rotate-180');
                }
                agendaItems.forEach(function (item) {
                    const head = item.querySelector('.future-agenda-head');
                    if (!head) return;
                    head.addEventListener('click', function () {
                        const body = item.querySelector('.future-agenda-body');
                        const isOpen = body && !body.classList.contains('hidden');
                        // accordion: close all, then open this one if it was closed
                        agendaItems.forEach(closeAgenda);
                        if (!isOpen) {
                            openAgenda(item);
                            setTimeout(() => item.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 60);
                        }
                    });
                });

                cards.forEach(function (card) {
                    // ── Share / copy deep link ──
                    const shareBtn = card.querySelector('.future-share-btn');
                    if (shareBtn) {
                        shareBtn.addEventListener('click', function (e) {
                            e.stopPropagation();
                            const url = shareBtn.getAttribute('data-share-url');
                            const labelDefault = shareBtn.querySelector('.share-label-default');
                            const labelCopied = shareBtn.querySelector('.share-label-copied');
                            const icon = shareBtn.querySelector('.share-icon');

                            const showCopied = function () {
                                if (labelDefault) labelDefault.classList.add('hidden');
                                if (labelCopied) labelCopied.classList.remove('hidden');
                                if (icon) icon.textContent = 'check';
                                shareBtn.classList.add('border-vibrant-lime', 'bg-vibrant-lime/10');
                                setTimeout(function () {
                                    if (labelDefault) labelDefault.classList.remove('hidden');
                                    if (labelCopied) labelCopied.classList.add('hidden');
                                    if (icon) icon.textContent = 'link';
                                    shareBtn.classList.remove('border-vibrant-lime', 'bg-vibrant-lime/10');
                                }, 2200);
                            };

                            if (navigator.clipboard && window.isSecureContext) {
                                navigator.clipboard.writeText(url).then(showCopied).catch(function () {
                                    window.prompt(@json(__('messages.future_share_btn')), url);
                                });
                            } else {
                                const ta = document.createElement('textarea');
                                ta.value = url;
                                ta.style.position = 'fixed';
                                ta.style.opacity = '0';
                                document.body.appendChild(ta);
                                ta.select();
                                try { document.execCommand('copy'); showCopied(); }
                                catch (e2) { window.prompt(@json(__('messages.future_share_btn')), url); }
                                document.body.removeChild(ta);
                            }
                        });
                    }

                    const toggleBtn = card.querySelector('.future-register-toggle');
                    const panel = card.querySelector('.future-register-panel');
                    const form = card.querySelector('.future-register-form');
                    const successBox = card.querySelector('.future-register-success');
                    const errorsBox = card.querySelector('.future-register-errors');
                    const errorsList = card.querySelector('.future-register-errors-list');

                    if (!toggleBtn || !panel || !form) return;

                    toggleBtn.addEventListener('click', function () {
                        const willOpen = panel.classList.contains('hidden');
                        panel.classList.toggle('hidden', !willOpen);
                        toggleBtn.querySelector('.toggle-label-open').classList.toggle('hidden', willOpen);
                        toggleBtn.querySelector('.toggle-label-close').classList.toggle('hidden', !willOpen);
                        toggleBtn.querySelector('.toggle-chevron').classList.toggle('rotate-180', willOpen);
                        if (willOpen) {
                            setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
                            const firstInput = form.querySelector('input[name="name"]');
                            if (firstInput) setTimeout(() => firstInput.focus(), 350);
                        }
                    });

                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const submitBtn = form.querySelector('.future-register-submit');
                        const submitLabel = submitBtn.querySelector('.submit-label');
                        const submitIcon = submitBtn.querySelector('.submit-icon');
                        const submitSpinner = submitBtn.querySelector('.submit-spinner');

                        errorsBox.classList.add('hidden');
                        errorsList.innerHTML = '';

                        submitBtn.disabled = true;
                        submitIcon.classList.add('hidden');
                        submitSpinner.classList.remove('hidden');
                        const originalLabel = submitLabel.textContent;
                        submitLabel.textContent = @json(__('messages.future_register_sending'));

                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': formData.get('_token'),
                            },
                            credentials: 'same-origin',
                        })
                        .then(function (response) {
                            if (response.ok) {
                                return response.json().then(function () {
                                    form.classList.add('hidden');
                                    if (successBox) successBox.classList.remove('hidden');
                                    toggleBtn.classList.add('hidden');
                                });
                            }
                            return response.json().then(function (data) {
                                const errors = (data && data.errors) ? data.errors : { _generic: [data.message || @json(__('messages.future_register_error_generic'))] };
                                Object.keys(errors).forEach(function (key) {
                                    (errors[key] || []).forEach(function (msg) {
                                        const li = document.createElement('li');
                                        li.textContent = msg;
                                        errorsList.appendChild(li);
                                    });
                                });
                                errorsBox.classList.remove('hidden');
                                throw new Error('validation');
                            });
                        })
                        .catch(function (err) {
                            if (err && err.message === 'validation') return;
                            const li = document.createElement('li');
                            li.textContent = @json(__('messages.future_register_error_network'));
                            errorsList.innerHTML = '';
                            errorsList.appendChild(li);
                            errorsBox.classList.remove('hidden');
                        })
                        .finally(function () {
                            submitBtn.disabled = false;
                            submitIcon.classList.remove('hidden');
                            submitSpinner.classList.add('hidden');
                            submitLabel.textContent = originalLabel;
                        });
                    });
                });

                // ── Deep link: open via #event-ID → scroll, expand (if agenda), highlight ──
                function focusHashedEvent() {
                    const hash = window.location.hash;
                    if (!hash || hash.indexOf('#event-') !== 0) return;
                    const target = document.getElementById(hash.slice(1));
                    if (!target || !target.classList.contains('future-event-card')) return;

                    // If it's an agenda row, expand it
                    if (target.classList.contains('future-agenda-item')) {
                        agendaItems.forEach(closeAgenda);
                        openAgenda(target);
                    }

                    setTimeout(function () {
                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        target.classList.add('ring-4', 'ring-vibrant-lime', 'ring-offset-4', 'ring-offset-[#fafaf5]');
                        setTimeout(function () {
                            target.classList.remove('ring-4', 'ring-vibrant-lime', 'ring-offset-4', 'ring-offset-[#fafaf5]');
                        }, 2800);
                    }, 250);
                }

                focusHashedEvent();
                window.addEventListener('hashchange', focusHashedEvent);
            })();
        </script>
    </section>
@endif
