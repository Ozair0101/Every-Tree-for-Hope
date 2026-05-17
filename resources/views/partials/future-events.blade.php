@php
    $futureEvents = \App\Models\UpcomingEvent::query()
        ->where('is_active', true)
        ->whereDate('date', '>=', \Carbon\Carbon::today())
        ->orderBy('date', 'asc')
        ->take($limit ?? 6)
        ->get();
@endphp

@if ($futureEvents->count() > 0)
    {{-- ===== FUTURE EVENT PLAN — Field Dispatches (with inline registration) ===== --}}
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
            <div class="mb-12 md:mb-16 max-w-3xl">
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

            {{-- Dispatches stack --}}
            <div class="space-y-6 md:space-y-8">

                @foreach ($futureEvents as $idx => $event)
                    @php
                        $daysAway = (int) \Carbon\Carbon::today()->diffInDays($event->date, false);
                        if ($daysAway === 0) {
                            $awayLabel = __('messages.future_today');
                            $awayIcon = 'today';
                            $awayTone = 'gold';
                        } elseif ($daysAway === 1) {
                            $awayLabel = __('messages.future_tomorrow');
                            $awayIcon = 'event_upcoming';
                            $awayTone = 'gold';
                        } else {
                            $awayLabel = __('messages.future_in_days', ['count' => $daysAway]);
                            $awayIcon = 'schedule';
                            $awayTone = 'lime';
                        }
                        $tilt = $idx % 2 === 0 ? 'lg:hover:-rotate-[0.4deg]' : 'lg:hover:rotate-[0.4deg]';
                        $images = is_array($event->images) ? array_values($event->images) : [];
                        $heroImage = $images[0] ?? null;
                        $extraImagesCount = max(0, count($images) - 1);
                    @endphp

                    <article id="event-{{ $event->id }}"
                        class="future-event-card group relative bg-white border border-deep-green/10 shadow-[0_10px_30px_rgba(6,46,34,0.06)] hover:shadow-[0_20px_50px_rgba(6,46,34,0.14)] transition-all duration-500 hover:-translate-y-1 {{ $tilt }} overflow-hidden rounded-sm"
                        data-event-id="{{ $event->id }}">

                        {{-- ── Hero image (if any) ── --}}
                        @if ($heroImage)
                            <div class="relative h-44 md:h-52 overflow-hidden bg-deep-green/5">
                                <img src="{{ asset('storage/' . ltrim($heroImage, '/')) }}"
                                    alt="{{ $event->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                                {{-- Subtle bottom gradient for legibility --}}
                                <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/20 to-transparent pointer-events-none"></div>

                                @if ($extraImagesCount > 0)
                                    <span class="absolute top-3 end-3 inline-flex items-center gap-1 px-3 py-1 rounded-full bg-black/55 backdrop-blur-sm text-white text-[10px] font-bold tracking-widest uppercase">
                                        <span class="material-symbols-outlined text-xs">photo_library</span>
                                        +{{ $extraImagesCount }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Top accent strip --}}
                        <div class="h-1 bg-gradient-to-r from-vibrant-lime via-deep-green to-gold-accent"></div>

                        <div class="grid grid-cols-1 md:grid-cols-[150px_1fr] lg:grid-cols-[180px_1fr]">

                            {{-- ── LEFT: Date stamp ── --}}
                            <div class="relative p-6 md:p-7 lg:p-8 bg-[#fcfbf6] md:border-e md:border-dashed md:border-deep-green/25 border-b md:border-b-0 flex flex-col items-center justify-center text-center">
                                {{-- Hole-punches along the perforation --}}
                                <div class="hidden md:flex absolute top-0 bottom-0 end-0 translate-x-1/2 flex-col items-center justify-around py-4 pointer-events-none">
                                    @for ($i = 0; $i < 8; $i++)
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#fafaf5] border border-deep-green/15"></span>
                                    @endfor
                                </div>

                                <p class="text-gold-accent text-[10px] font-bold tracking-[0.35em] uppercase mb-1">
                                    {{ $event->date->format('M') }}
                                </p>
                                <p class="font-serif font-black text-deep-green leading-none mb-1" style="font-size: clamp(3rem, 8vw, 4.5rem);">
                                    {{ $event->date->format('d') }}
                                </p>
                                <p class="text-charcoal/60 text-xs font-bold tracking-widest uppercase">
                                    {{ $event->date->format('l') }}
                                </p>
                                <p class="text-charcoal/35 text-[10px] font-bold tracking-widest mt-1">
                                    {{ $event->date->format('Y') }}
                                </p>
                            </div>

                            {{-- ── RIGHT: Dispatch content ── --}}
                            <div class="p-6 md:p-7 lg:p-9">

                                {{-- Days-away pill --}}
                                @if ($awayTone === 'gold')
                                    <span class="inline-flex items-center gap-1.5 mb-4 px-3 py-1 bg-gold-accent/15 border border-gold-accent/30 text-gold-accent text-[10px] font-bold uppercase tracking-[0.2em] rounded-full">
                                        <span class="material-symbols-outlined text-xs">{{ $awayIcon }}</span>
                                        {{ $awayLabel }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 mb-4 px-3 py-1 bg-vibrant-lime/15 border border-vibrant-lime/30 text-deep-green text-[10px] font-bold uppercase tracking-[0.2em] rounded-full">
                                        <span class="material-symbols-outlined text-xs">{{ $awayIcon }}</span>
                                        {{ $awayLabel }}
                                    </span>
                                @endif

                                {{-- Title --}}
                                <h3 class="font-serif text-2xl md:text-3xl font-bold text-deep-green leading-tight mb-3">
                                    {{ $event->title }}
                                </h3>

                                {{-- Location --}}
                                <div class="flex items-start gap-2 text-charcoal/75 mb-4">
                                    <span class="material-symbols-outlined text-base text-gold-accent mt-0.5 flex-shrink-0">location_on</span>
                                    <p class="text-sm md:text-base font-medium">
                                        {{ $event->location }}@if ($event->province)<span class="text-charcoal/50">, {{ $event->province }}</span>@endif
                                    </p>
                                </div>

                                {{-- Description --}}
                                @if ($event->description)
                                    <p class="text-charcoal/70 text-sm md:text-[15px] leading-relaxed mb-5">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($event->description), 220) }}
                                    </p>
                                @endif

                                {{-- Dashed divider --}}
                                <div class="border-t border-dashed border-charcoal/15 mb-5"></div>

                                {{-- CTA row --}}
                                <div class="future-register-cta flex flex-wrap items-center gap-3">
                                    <button type="button"
                                        class="future-register-toggle group/btn inline-flex items-center gap-2 px-6 py-3 bg-deep-green text-white text-xs font-extrabold tracking-[0.2em] uppercase rounded-full shadow-md shadow-deep-green/20 hover:bg-deep-green/90 hover:-translate-y-0.5 transition-all">
                                        <span class="material-symbols-outlined text-base text-vibrant-lime">how_to_reg</span>
                                        <span class="toggle-label-open">{{ __('messages.future_register_btn') }}</span>
                                        <span class="toggle-label-close hidden">{{ __('messages.cancel') }}</span>
                                        <span class="material-symbols-outlined text-base toggle-chevron transition-transform">expand_more</span>
                                    </button>

                                    {{-- Share / copy deep link --}}
                                    <button type="button"
                                        class="future-share-btn group/sh inline-flex items-center gap-2 px-5 py-3 border border-deep-green/25 text-deep-green text-xs font-extrabold tracking-[0.2em] uppercase rounded-full hover:bg-deep-green/5 hover:border-deep-green/50 transition-all"
                                        data-share-url="{{ route('gallery') }}#event-{{ $event->id }}"
                                        aria-label="{{ __('messages.future_share_btn') }}">
                                        <span class="material-symbols-outlined text-base share-icon">link</span>
                                        <span class="share-label-default">{{ __('messages.future_share_btn') }}</span>
                                        <span class="share-label-copied hidden text-vibrant-lime">{{ __('messages.future_share_copied') }}</span>
                                    </button>
                                </div>

                                {{-- Inline registration form (hidden by default) --}}
                                <div class="future-register-panel hidden mt-7 pt-6 border-t border-dashed border-deep-green/20">
                                    <p class="font-handwriting text-xl md:text-2xl text-vibrant-lime mb-1 leading-none">
                                        ~ {{ __('messages.future_register_eyebrow') }} ~
                                    </p>
                                    <p class="text-xs text-charcoal/55 italic mb-5">
                                        {{ __('messages.future_register_subtitle') }}
                                    </p>

                                    <form class="future-register-form space-y-5" method="POST" action="{{ route('involvement.store') }}">
                                        @csrf
                                        <input type="hidden" name="type" value="volunteer">
                                        <input type="hidden" name="upcoming_event_id" value="{{ $event->id }}">

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-[10px] font-bold uppercase tracking-widest text-charcoal/55 mb-1">
                                                    {{ __('messages.your_name') }}
                                                </label>
                                                <input name="name" type="text" required
                                                    placeholder="{{ __('messages.your_name') }}"
                                                    class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-2 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/35 placeholder:font-light text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold uppercase tracking-widest text-charcoal/55 mb-1">
                                                    {{ __('messages.email_address') }}
                                                </label>
                                                <input name="email" type="email" required
                                                    placeholder="{{ __('messages.email_address') }}"
                                                    class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-2 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/35 placeholder:font-light text-sm">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold uppercase tracking-widest text-charcoal/55 mb-1">
                                                {{ __('messages.phone_number') }}
                                            </label>
                                            <input name="phone" type="text" required
                                                placeholder="{{ __('messages.phone_placeholder') }}"
                                                class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-2 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/35 placeholder:font-light text-sm">
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold uppercase tracking-widest text-charcoal/55 mb-1">
                                                {{ __('messages.future_register_note_label') }}
                                            </label>
                                            <textarea name="message" rows="2"
                                                placeholder="{{ __('messages.future_register_note_placeholder') }}"
                                                class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-2 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/35 placeholder:font-light text-sm resize-none"></textarea>
                                        </div>

                                        {{-- Inline error block --}}
                                        <div class="future-register-errors hidden bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                                            <p class="text-xs font-bold uppercase tracking-widest text-red-700 mb-1">
                                                {{ __('messages.future_register_error_title') }}
                                            </p>
                                            <ul class="text-sm text-red-700 list-disc list-inside space-y-1 future-register-errors-list"></ul>
                                        </div>

                                        <div class="flex items-center gap-4 flex-wrap pt-2">
                                            <button type="submit"
                                                class="future-register-submit group/btn inline-flex items-center gap-2 px-7 py-3.5 bg-deep-green text-white text-xs font-extrabold tracking-[0.2em] uppercase rounded-full shadow-md shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-wait transition-all">
                                                <span class="material-symbols-outlined text-base text-vibrant-lime submit-icon">how_to_reg</span>
                                                <span class="material-symbols-outlined text-base text-vibrant-lime submit-spinner hidden animate-spin">progress_activity</span>
                                                <span class="submit-label">{{ __('messages.future_confirm_spot') }}</span>
                                            </button>
                                            <p class="font-handwriting text-base md:text-lg text-charcoal/55">
                                                {{ __('messages.future_register_promise') }}
                                            </p>
                                        </div>
                                    </form>

                                    {{-- Success state --}}
                                    <div class="future-register-success hidden">
                                        <div class="flex items-start gap-3 bg-vibrant-lime/12 border border-vibrant-lime/40 rounded-xl px-5 py-5">
                                            <span class="material-symbols-outlined text-vibrant-lime text-3xl mt-0.5 flex-shrink-0">check_circle</span>
                                            <div>
                                                <p class="font-serif text-lg md:text-xl font-bold text-deep-green leading-tight">
                                                    {{ __('messages.future_register_success_title') }}
                                                </p>
                                                <p class="text-charcoal/75 text-sm mt-2 leading-relaxed">
                                                    {{ __('messages.future_register_success') }}
                                                </p>
                                                <p class="font-handwriting text-vibrant-lime text-lg mt-3">
                                                    ~ {{ __('messages.future_register_success_handwritten') }} ~
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Hand-drawn closing note --}}
            <p class="text-center mt-10 md:mt-14 font-handwriting text-xl md:text-2xl text-charcoal/55">
                ~ {{ __('messages.future_closing_note') }} ~
            </p>
        </div>

        {{-- Behavior: toggle the inline registration form and submit via fetch --}}
        <script>
            (function () {
                const cards = document.querySelectorAll('.future-event-card');

                cards.forEach(function (card) {
                    // ── Share / copy deep link ──
                    const shareBtn = card.querySelector('.future-share-btn');
                    if (shareBtn) {
                        shareBtn.addEventListener('click', function () {
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
                                // Fallback for non-secure contexts (http on LAN, etc.)
                                const ta = document.createElement('textarea');
                                ta.value = url;
                                ta.style.position = 'fixed';
                                ta.style.opacity = '0';
                                document.body.appendChild(ta);
                                ta.select();
                                try { document.execCommand('copy'); showCopied(); }
                                catch (e) { window.prompt(@json(__('messages.future_share_btn')), url); }
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

                // ── Deep link: when opened via #event-ID, scroll to & highlight that card ──
                function focusHashedEvent() {
                    const hash = window.location.hash;
                    if (!hash || hash.indexOf('#event-') !== 0) return;
                    const target = document.getElementById(hash.slice(1));
                    if (!target || !target.classList.contains('future-event-card')) return;

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
