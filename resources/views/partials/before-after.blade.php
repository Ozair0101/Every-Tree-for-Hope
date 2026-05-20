{{-- ============================================================== --}}
{{-- BEFORE & AFTER — Editorial transformation slider                --}}
{{-- Swap the images below with the real "before" and "after" shots. --}}
{{-- ============================================================== --}}
@php
    $beforeImage = $beforeImage ?? asset('images/73.jpeg');
    $afterImage  = $afterImage  ?? asset('images/72.jpeg');
@endphp

<section class="relative w-full overflow-hidden" style="background:#fafaf5;">
    {{-- Paper grain --}}
    <div class="absolute inset-0 pointer-events-none opacity-[0.5]"
         style="background-image: radial-gradient(rgba(6,46,34,0.04) 1px, transparent 1px); background-size: 14px 14px;">
    </div>

    {{-- Soft top gradient that hands off from the hero band --}}
    <div class="absolute inset-x-0 top-0 h-24 pointer-events-none"
         style="background: linear-gradient(180deg, rgba(6,46,34,0.06) 0%, rgba(6,46,34,0) 100%);">
    </div>

    {{-- Floating botanical accents --}}
    <div class="absolute inset-0 pointer-events-none select-none">
        <span class="material-symbols-outlined absolute text-vibrant-lime/15 floating"
              style="top:8%; {{ $is_rtl ? 'right' : 'left' }}:4%; font-size:84px;">park</span>
        <span class="material-symbols-outlined absolute text-gold-accent/15 floating"
              style="bottom:10%; {{ $is_rtl ? 'left' : 'right' }}:5%; font-size:72px; animation-delay:1.2s;">forest</span>
        <span class="material-symbols-outlined absolute text-deep-green/10 floating"
              style="top:42%; {{ $is_rtl ? 'left' : 'right' }}:12%; font-size:48px; animation-delay:2.4s;">eco</span>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 py-20 lg:py-28">

        {{-- Editorial header ------------------------------------------------ --}}
        <div class="text-center max-w-3xl mx-auto mb-14">
            <p class="font-handwriting text-vibrant-lime text-2xl md:text-3xl mb-3 leading-none">
                ~ {{ __('messages.before_after_eyebrow') }} ~
            </p>
            <div class="inline-flex items-center gap-3 mb-5">
                <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                <span class="text-vibrant-lime font-bold tracking-[0.3em] text-[11px] uppercase">
                    {{ __('messages.before_after_kicker') }}
                </span>
                <div class="h-[1px] w-10 bg-vibrant-lime"></div>
            </div>
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-deep-green leading-[1.05] mb-5">
                {!! __('messages.before_after_title') !!}
            </h2>
            <div class="flex justify-center mb-6">
                <svg width="220" height="14" viewBox="0 0 220 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2,9 Q55,2 110,6 T218,5" fill="none" stroke="#84cc16" stroke-width="3" stroke-linecap="round" />
                </svg>
            </div>
            <p class="text-charcoal/70 text-base md:text-lg leading-relaxed">
                {{ __('messages.before_after_desc') }}
            </p>
        </div>

        {{-- Polaroid frame around the slider ------------------------------- --}}
        <div class="relative mx-auto max-w-5xl">

            {{-- Masking-tape strips (decorative) --}}
            <div class="absolute -top-4 left-12 w-24 h-7 rotate-[-6deg] z-30 pointer-events-none"
                 style="background: rgba(212,175,55,0.55); box-shadow: 0 2px 6px rgba(0,0,0,0.08);"></div>
            <div class="absolute -top-4 right-12 w-24 h-7 rotate-[5deg] z-30 pointer-events-none"
                 style="background: rgba(132,204,22,0.45); box-shadow: 0 2px 6px rgba(0,0,0,0.08);"></div>

            {{-- Polaroid card --}}
            <div class="relative bg-white rounded-sm p-4 md:p-6 pb-12 md:pb-16"
                 style="box-shadow: 0 30px 60px -20px rgba(6,46,34,0.25), 0 8px 24px -8px rgba(0,0,0,0.12);">

                {{-- Top labels row --}}
                <div class="flex items-center justify-between mb-3 px-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2 h-2 rounded-full bg-gold-accent"></span>
                        <span class="font-bold tracking-[0.3em] text-[10px] uppercase text-charcoal/60">
                            {{ __('messages.before_label') }}
                        </span>
                        <span class="font-handwriting text-charcoal/40 text-base leading-none ml-1">
                            {{ __('messages.before_year') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-handwriting text-charcoal/40 text-base leading-none mr-1">
                            {{ __('messages.after_year') }}
                        </span>
                        <span class="font-bold tracking-[0.3em] text-[10px] uppercase text-charcoal/60">
                            {{ __('messages.after_label') }}
                        </span>
                        <span class="inline-block w-2 h-2 rounded-full bg-vibrant-lime"></span>
                    </div>
                </div>

                {{-- ===== The slider ===== --}}
                <div id="ba-slider"
                     class="relative w-full aspect-[16/10] overflow-hidden select-none cursor-ew-resize rounded-sm"
                     style="touch-action: none;">

                    {{-- AFTER image (base layer, fully visible) --}}
                    <img src="{{ $afterImage }}"
                         alt="{{ __('messages.after_label') }} — {{ __('messages.after_caption') }}"
                         class="absolute inset-0 w-full h-full object-cover pointer-events-none"
                         draggable="false">

                    {{-- BEFORE image (clipped to reveal left portion) --}}
                    <img id="ba-before-img"
                         src="{{ $beforeImage }}"
                         alt="{{ __('messages.before_label') }} — {{ __('messages.before_caption') }}"
                         class="absolute inset-0 w-full h-full object-cover pointer-events-none"
                         style="clip-path: inset(0 50% 0 0); -webkit-clip-path: inset(0 50% 0 0);"
                         draggable="false">

                    {{-- Corner ribbons (sit ABOVE the images) --}}
                    <div class="absolute top-4 left-4 z-10 pointer-events-none">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full"
                             style="background: rgba(255,255,255,0.92); backdrop-filter: blur(6px); box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold-accent"></span>
                            <span class="text-deep-green font-bold tracking-[0.25em] text-[10px] uppercase">
                                {{ __('messages.before_label') }}
                            </span>
                        </div>
                    </div>
                    <div class="absolute top-4 right-4 z-10 pointer-events-none">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full"
                             style="background: rgba(6,46,34,0.92); backdrop-filter: blur(6px); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                            <span class="text-white font-bold tracking-[0.25em] text-[10px] uppercase">
                                {{ __('messages.after_label') }}
                            </span>
                            <span class="w-1.5 h-1.5 rounded-full bg-vibrant-lime"></span>
                        </div>
                    </div>

                    {{-- Gold thread divider + handle --}}
                    <div id="ba-divider"
                         class="absolute top-0 bottom-0 w-[2px] pointer-events-none z-20"
                         style="left: 50%; transform: translateX(-50%); background: linear-gradient(180deg, rgba(212,175,55,0.95) 0%, rgba(255,255,255,0.95) 50%, rgba(132,204,22,0.95) 100%); box-shadow: 0 0 12px rgba(255,255,255,0.6);">
                    </div>
                    <button id="ba-handle"
                            type="button"
                            aria-label="{{ __('messages.slider_hint') }}"
                            class="absolute top-1/2 z-30 -translate-y-1/2 -translate-x-1/2 w-12 h-12 rounded-full flex items-center justify-center transition-transform duration-150 hover:scale-110 focus:scale-110 focus:outline-none focus:ring-4 focus:ring-vibrant-lime/40"
                            style="left: 50%; background: #fafaf5; border: 3px solid #064e3b; box-shadow: 0 6px 20px rgba(6,46,34,0.35), 0 0 0 4px rgba(250,250,245,0.6);">
                        <span class="flex items-center text-deep-green leading-none">
                            <span class="material-symbols-outlined text-[18px] -mr-1">chevron_left</span>
                            <span class="material-symbols-outlined text-[18px] -ml-1">chevron_right</span>
                        </span>
                    </button>

                    {{-- "Drag to compare" hint (fades out on first interaction) --}}
                    <div id="ba-hint"
                         class="absolute left-1/2 z-20 pointer-events-none transition-opacity duration-500"
                         style="bottom: 18px; transform: translateX(-50%);">
                        <div class="flex items-center gap-2 px-3 py-1 rounded-full"
                             style="background: rgba(250,250,245,0.92); box-shadow: 0 4px 14px rgba(0,0,0,0.15);">
                            <span class="material-symbols-outlined text-deep-green text-sm animate-pulse">swap_horiz</span>
                            <span class="font-handwriting text-deep-green text-base leading-none">
                                {{ __('messages.slider_hint') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Captions row beneath the polaroid window --}}
                <div class="grid grid-cols-2 gap-6 mt-5 px-1">
                    <div>
                        <p class="font-handwriting text-deep-green text-lg md:text-xl leading-snug">
                            {{ __('messages.before_caption') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-handwriting text-deep-green text-lg md:text-xl leading-snug">
                            {{ __('messages.after_caption') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- "Stamp" mark sitting in the bottom-right of the polaroid --}}
            <div class="absolute -bottom-4 right-6 rotate-[-8deg] z-20 pointer-events-none">
                <div class="px-3 py-1 border-2 border-gold-accent/70 rounded-sm"
                     style="background: rgba(250,250,245,0.9);">
                    <span class="font-bold tracking-[0.25em] text-[10px] uppercase text-gold-accent">
                        {{ __('messages.before_after_stamp') }}
                    </span>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
    (function () {
        const slider = document.getElementById('ba-slider');
        if (!slider) return;
        const beforeImg = document.getElementById('ba-before-img');
        const divider   = document.getElementById('ba-divider');
        const handle    = document.getElementById('ba-handle');
        const hint      = document.getElementById('ba-hint');
        let dragging = false;
        let hintHidden = false;

        function setPosition(percent) {
            percent = Math.max(0, Math.min(100, percent));
            const clipValue = `inset(0 ${100 - percent}% 0 0)`;
            beforeImg.style.clipPath = clipValue;
            beforeImg.style.webkitClipPath = clipValue;
            divider.style.left = percent + '%';
            handle.style.left  = percent + '%';
            if (!hintHidden) {
                hint.style.opacity = '0';
                hintHidden = true;
            }
        }

        function pointerToPercent(clientX) {
            const rect = slider.getBoundingClientRect();
            return ((clientX - rect.left) / rect.width) * 100;
        }

        function onMove(e) {
            if (!dragging) return;
            const x = e.touches ? e.touches[0].clientX : e.clientX;
            setPosition(pointerToPercent(x));
            e.preventDefault();
        }
        function onUp() { dragging = false; document.body.style.userSelect = ''; }
        function onDown(e) {
            dragging = true;
            document.body.style.userSelect = 'none';
            const x = e.touches ? e.touches[0].clientX : e.clientX;
            setPosition(pointerToPercent(x));
        }

        slider.addEventListener('mousedown',  onDown);
        slider.addEventListener('touchstart', onDown, { passive: true });
        window.addEventListener('mousemove',  onMove);
        window.addEventListener('touchmove',  onMove, { passive: false });
        window.addEventListener('mouseup',    onUp);
        window.addEventListener('touchend',   onUp);

        // Keyboard accessibility on the handle
        handle.addEventListener('keydown', function (e) {
            const current = parseFloat(handle.style.left) || 50;
            if (e.key === 'ArrowLeft')  { setPosition(current - 4); e.preventDefault(); }
            if (e.key === 'ArrowRight') { setPosition(current + 4); e.preventDefault(); }
            if (e.key === 'Home')       { setPosition(0);   e.preventDefault(); }
            if (e.key === 'End')        { setPosition(100); e.preventDefault(); }
        });

        // Gentle one-time "wiggle" so users notice it's interactive
        let wiggled = false;
        const wiggleObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && !wiggled) {
                    wiggled = true;
                    setTimeout(() => setPosition(58), 600);
                    setTimeout(() => setPosition(42), 1100);
                    setTimeout(() => setPosition(50), 1600);
                }
            });
        }, { threshold: 0.4 });
        wiggleObserver.observe(slider);
    })();
</script>
