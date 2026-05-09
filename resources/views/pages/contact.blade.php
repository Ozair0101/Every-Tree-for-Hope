@extends('layouts.layout')

@section('content')
    <div class="relative min-h-screen w-full flex flex-col">
        @if (session('success'))
            <div class="relative z-10 mx-auto max-w-5xl px-6 pt-8">
                <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl flex items-center gap-3 success-message"
                    id="successMessage">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 right-[-5%] text-sage-green/5">
                <span class="material-symbols-outlined text-[30rem] select-none rotate-12">spa</span>
            </div>
            <span class="material-symbols-outlined leaf-float top-1/4 left-1/4 text-4xl">energy_savings_leaf</span>
            <span class="material-symbols-outlined leaf-float top-1/2 right-1/3 text-2xl">eco</span>
        </div>
        <main class="relative z-10 flex-1 flex flex-col items-center">
            <section
                class="w-full max-w-7xl mx-auto px-6 pt-10 sm:pt-16 pb-16 sm:pb-24 flex flex-col lg:flex-row items-center gap-10 lg:gap-20">
                <div class="w-full lg:w-5/12 text-left z-10">
                    <h4 class="text-sage-green font-bold tracking-[0.4em] text-xs uppercase mb-4 sm:mb-6">
                        {{ __('messages.connect_with_kabul') }}
                    </h4>
                    <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-serif text-charcoal leading-[1.1] mb-6 sm:mb-8">
                        {!! __('messages.lets_grow_together') !!}
                    </h1>
                    <p class="text-charcoal/70 text-lg max-w-md font-light leading-relaxed">
                        {{ __('messages.contact_description') }}
                    </p>
                    <div class="w-24 h-[1px] bg-gold-accent mt-12 opacity-60"></div>
                </div>
                <div class="w-full lg:w-7/12 flex justify-center lg:justify-end">
                    <div class="relative w-full aspect-[4/3] hill-organic-mask overflow-hidden shadow-2xl">
                        <img alt="The rugged, sun-drenched hills surrounding Kabul with new saplings"
                            class="object-cover w-full h-full scale-105 hover:scale-100 transition-transform duration-1000"
                            src="{{ asset('images/2.jpeg') }}" />
                        <div
                            class="absolute inset-0 bg-gradient-to-tr from-deep-green/10 to-transparent mix-blend-multiply">
                        </div>
                    </div>
                </div>
            </section>

            <section class="w-full max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 px-4 sm:px-6 md:px-12 lg:px-24 mb-16 md:mb-24">
                <div
                    class="glass-panel p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl text-center group hover:-translate-y-2 transition-transform duration-500">
                    <div
                        class="w-16 h-16 bg-off-white border border-gold-accent/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-gold-accent text-3xl">location_on</span>
                    </div>
                    <h3 class="font-serif text-xl text-deep-green mb-3">{{ __('messages.visit_us') }}</h3>
                    <p class="text-charcoal/70 text-sm leading-relaxed">
                        {!! __('messages.kabul_afghanistan_district_7') !!}
                    </p>
                    <p class="text-[10px] font-bold text-sage-green uppercase tracking-widest mt-4">
                        {{ __('messages.by_appointment_only') }}
                    </p>
                </div>
                <div
                    class="glass-panel p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl text-center group hover:-translate-y-2 transition-transform duration-500">
                    <div
                        class="w-16 h-16 bg-off-white border border-gold-accent/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-gold-accent text-3xl">chat</span>
                    </div>
                    <h3 class="font-serif text-xl text-deep-green mb-3">{{ __('messages.call_us') }}</h3>
                    <a class="text-charcoal/70 text-sm hover:text-deep-green transition-colors"
                        href="https://wa.me/93749290591">
                        {{ __('messages.whatsapp_number') }}
                    </a>
                    <p class="text-[10px] font-bold text-sage-green uppercase tracking-widest mt-4">
                        {{ __('messages.available_sat_thu') }}
                    </p>
                </div>
                <div
                    class="glass-panel p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl text-center group hover:-translate-y-2 transition-transform duration-500">
                    <div
                        class="w-16 h-16 bg-off-white border border-gold-accent/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-gold-accent text-3xl">mail</span>
                    </div>
                    <h3 class="font-serif text-xl text-deep-green mb-3">{{ __('messages.write_us') }}</h3>
                    <a class="text-charcoal/70 text-sm hover:text-deep-green transition-colors"
                        href="mailto:everytreeforahope@gmail.com">
                        everytreeforahope@gmail.com
                    </a>
                    <p class="text-[10px] font-bold text-sage-green uppercase tracking-widest mt-4">
                        {{ __('messages.response_time') }}</p>
                </div>
            </section>

            {{-- ===== GET INVOLVED — Editorial Index ===== --}}
            <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 mb-20 md:mb-32" id="get-involved">

                {{-- Editorial header (no card) --}}
                <div class="max-w-3xl mb-12 md:mb-20">
                    <p class="font-handwriting text-2xl md:text-3xl text-vibrant-lime mb-2 leading-none">
                        ~ {{ __('messages.involve_index_handwritten') }} ~
                    </p>
                    <div class="inline-flex items-center gap-3 mb-5">
                        <div class="h-[1px] w-10 bg-vibrant-lime"></div>
                        <span class="text-vibrant-lime font-bold tracking-[0.4em] text-[10px] uppercase">
                            {{ __('messages.get_involved_badge') }}
                        </span>
                    </div>
                    <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-serif text-deep-green leading-[1.05] mb-6">
                        {!! __('messages.get_involved_title') !!}
                    </h2>
                    <p class="text-charcoal/70 text-base md:text-lg font-light leading-relaxed max-w-xl">
                        {{ __('messages.get_involved_description') }}
                    </p>
                </div>

                {{-- Two-column editorial spread --}}
                <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">

                    {{-- Gold thread separator (desktop only) --}}
                    <div class="hidden lg:block absolute top-0 bottom-0 left-[41.666%] w-px bg-gradient-to-b from-transparent via-gold-accent/45 to-transparent pointer-events-none"></div>

                    {{-- ─── LEFT: Index of intentions ─── --}}
                    <div class="lg:col-span-5">
                        <p class="text-[10px] font-bold tracking-[0.4em] uppercase text-charcoal/40 mb-5 lg:mb-7 inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-gold-accent">menu_book</span>
                            {{ __('messages.involve_toc_label') }}
                        </p>

                        <div class="border-t border-charcoal/10" id="involveQuickLinks">
                            @php
                                $intentions = [
                                    ['tab' => 'tree',        'num' => '01', 'label' => 'tab_tree_request', 'hint' => 'tab_tree_request_hint'],
                                    ['tab' => 'volunteer',   'num' => '02', 'label' => 'tab_volunteer',    'hint' => 'tab_volunteer_hint'],
                                    ['tab' => 'donate',      'num' => '03', 'label' => 'tab_donate',       'hint' => 'tab_donate_hint'],
                                    ['tab' => 'sponsor',     'num' => '04', 'label' => 'tab_sponsor',      'hint' => 'tab_sponsor_hint'],
                                    ['tab' => 'collaborate', 'num' => '05', 'label' => 'tab_collaborate',  'hint' => 'tab_collaborate_hint'],
                                    ['tab' => 'message',     'num' => '06', 'label' => 'tab_message',      'hint' => 'tab_message_hint'],
                                ];
                            @endphp

                            @foreach ($intentions as $item)
                                <button type="button" data-tab="{{ $item['tab'] }}" data-active="false"
                                    class="involve-tab-btn group relative w-full text-start py-5 md:py-6 border-b border-charcoal/10 hover:border-deep-green/30 data-[active=true]:border-deep-green/40 transition-colors flex items-baseline gap-4 md:gap-5 cursor-pointer">
                                    <span class="font-serif font-black text-3xl md:text-4xl text-charcoal/15 group-hover:text-deep-green/70 group-data-[active=true]:!text-vibrant-lime transition-colors w-12 md:w-14 flex-shrink-0 leading-none">{{ $item['num'] }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-serif text-lg md:text-xl font-bold text-deep-green/90 group-hover:text-deep-green leading-tight">{{ __('messages.' . $item['label']) }}</p>
                                        <p class="text-xs md:text-sm text-charcoal/55 mt-1 leading-snug">{{ __('messages.' . $item['hint']) }}</p>
                                    </div>
                                    <span class="material-symbols-outlined text-deep-green/25 group-hover:text-deep-green/60 group-data-[active=true]:!text-vibrant-lime group-data-[active=true]:translate-x-0.5 transition-all flex-shrink-0">arrow_forward</span>
                                    {{-- Hand-drawn underline that slides in on active --}}
                                    <span class="absolute -bottom-px {{ $is_rtl ? 'right-16 md:right-20 left-12' : 'left-16 md:left-20 right-12' }} h-[2px] bg-vibrant-lime {{ $is_rtl ? 'origin-right' : 'origin-left' }} scale-x-0 group-data-[active=true]:scale-x-100 transition-transform duration-500"></span>
                                </button>
                            @endforeach
                        </div>

                        {{-- Subtle handwritten footnote under the index --}}
                        <p class="mt-6 text-xs text-charcoal/45 italic font-serif">
                            {{ __('messages.involve_index_footnote') }}
                        </p>
                    </div>

                    {{-- ─── RIGHT: Open spread ─── --}}
                    <div class="lg:col-span-7">

                        {{-- Sidebar copy block (no card — pure editorial) --}}
                        <div class="mb-10 md:mb-12" id="involveSidebar">
                            <p class="font-handwriting text-2xl md:text-3xl text-vibrant-lime mb-2 leading-none">
                                ~ {{ __('messages.involve_active_marker') }} ~
                            </p>
                            <h3 class="font-serif text-3xl md:text-4xl lg:text-5xl font-bold text-deep-green leading-tight mb-4" id="involveSidebarTitle"></h3>
                            <p class="text-charcoal/75 text-base md:text-lg leading-relaxed mb-6 max-w-xl" id="involveSidebarText"></p>
                            <div class="gold-line-art"></div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm text-charcoal/70 mt-6" id="involveSidebarList"></div>
                        </div>

                        {{-- Form panels — preserved structure, no card wrapper --}}
                        <div class="space-y-8">
                                        <div class="involve-tab-panel" data-panel="volunteer">
                                            @if ($errors->involvement->has('type') && old('type') === 'volunteer')
                                                <div
                                                    class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-red-800">
                                                    <ul class="text-sm list-disc list-inside space-y-1">
                                                        @foreach ($errors->involvement->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            <form action="{{ route('involvement.store') }}" method="POST"
                                                class="space-y-6">
                                                @csrf
                                                <input type="hidden" name="type" value="volunteer" />
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.your_name') }}</label>
                                                        <input
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.your_name') }}" type="text"
                                                            name="name"
                                                            value="{{ old('type') === 'volunteer' ? old('name') : '' }}"
                                                            required />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.email_address') }}</label>
                                                        <input
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.email_address') }}"
                                                            type="email" name="email"
                                                            value="{{ old('type') === 'volunteer' ? old('email') : '' }}"
                                                            required />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.phone_number') }}</label>
                                                    <input
                                                        class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                        placeholder="{{ __('messages.phone_placeholder') }}"
                                                        type="text" name="phone"
                                                        value="{{ old('type') === 'volunteer' ? old('phone') : '' }}"
                                                        required />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.message') }}</label>
                                                    <textarea
                                                        class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light resize-none"
                                                        placeholder="{{ __('messages.volunteer_message_placeholder') }}" rows="5" name="message" required>{{ old('type') === 'volunteer' ? old('message') : '' }}</textarea>
                                                </div>
                                                <button
                                                    class="group inline-flex items-center gap-2 px-8 py-4 bg-deep-green text-white font-extrabold text-xs tracking-[0.2em] uppercase rounded-full shadow-lg shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-deep-green/30 transition-all"
                                                    type="submit">
                                                    {{ __('messages.submit_volunteer') }}
                                                    <span
                                                        class="material-symbols-outlined text-base {{ $is_rtl ? 'group-hover:-translate-x-1' : 'group-hover:translate-x-1' }} transition-transform">arrow_right_alt</span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="involve-tab-panel hidden" data-panel="donate">
                                            <div class="flex items-start gap-5 py-2">
                                                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gold-accent/15 flex items-center justify-center mt-1">
                                                    <span class="material-symbols-outlined text-gold-accent">favorite</span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-serif text-xl md:text-2xl text-deep-green font-bold">
                                                        {{ __('messages.donate_panel_title') }}</p>
                                                    <p class="text-charcoal/70 mt-2 leading-relaxed">
                                                        {{ __('messages.donate_panel_text') }}</p>
                                                    <a href="#donation-section"
                                                        class="group inline-flex items-center gap-2 mt-6 px-7 py-3.5 bg-gold-accent text-white font-extrabold text-xs tracking-widest uppercase rounded-full shadow-lg shadow-gold-accent/25 hover:bg-gold-accent/90 hover:-translate-y-0.5 transition-all">
                                                        {{ __('messages.go_to_donation_section') }}
                                                        <span class="material-symbols-outlined text-base group-hover:translate-y-0.5 transition-transform">south</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="involve-tab-panel hidden" data-panel="sponsor">
                                            @if ($errors->involvement->has('type') && old('type') === 'sponsor')
                                                <div
                                                    class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-red-800">
                                                    <ul class="text-sm list-disc list-inside space-y-1">
                                                        @foreach ($errors->involvement->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            <form action="{{ route('involvement.store') }}" method="POST"
                                                class="space-y-6">
                                                @csrf
                                                <input type="hidden" name="type" value="sponsor" />
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.your_name') }}</label>
                                                        <input
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.your_name') }}" type="text"
                                                            name="name"
                                                            value="{{ old('type') === 'sponsor' ? old('name') : '' }}"
                                                            required />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.email_address') }}</label>
                                                        <input
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.email_address') }}"
                                                            type="email" name="email"
                                                            value="{{ old('type') === 'sponsor' ? old('email') : '' }}"
                                                            required />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.phone_number') }}</label>
                                                    <input
                                                        class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                        placeholder="{{ __('messages.phone_placeholder') }}"
                                                        type="text" name="phone"
                                                        value="{{ old('type') === 'sponsor' ? old('phone') : '' }}"
                                                        required />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.message') }}</label>
                                                    <textarea
                                                        class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light resize-none"
                                                        placeholder="{{ __('messages.sponsor_message_placeholder') }}" rows="5" name="message" required>{{ old('type') === 'sponsor' ? old('message') : '' }}</textarea>
                                                </div>
                                                <button
                                                    class="group inline-flex items-center gap-2 px-8 py-4 bg-deep-green text-white font-extrabold text-xs tracking-[0.2em] uppercase rounded-full shadow-lg shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-deep-green/30 transition-all"
                                                    type="submit">
                                                    {{ __('messages.submit_sponsor') }}
                                                    <span
                                                        class="material-symbols-outlined text-base {{ $is_rtl ? 'group-hover:-translate-x-1' : 'group-hover:translate-x-1' }} transition-transform">arrow_right_alt</span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="involve-tab-panel hidden" data-panel="collaborate">
                                            @if ($errors->involvement->has('type') && old('type') === 'collaborate')
                                                <div
                                                    class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-red-800">
                                                    <ul class="text-sm list-disc list-inside space-y-1">
                                                        @foreach ($errors->involvement->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            <form action="{{ route('involvement.store') }}" method="POST"
                                                class="space-y-6">
                                                @csrf
                                                <input type="hidden" name="type" value="collaborate" />
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.your_name') }}</label>
                                                        <input
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.your_name') }}" type="text"
                                                            name="name"
                                                            value="{{ old('type') === 'collaborate' ? old('name') : '' }}"
                                                            required />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.email_address') }}</label>
                                                        <input
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.email_address') }}"
                                                            type="email" name="email"
                                                            value="{{ old('type') === 'collaborate' ? old('email') : '' }}"
                                                            required />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.phone_number') }}</label>
                                                    <input
                                                        class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                        placeholder="{{ __('messages.phone_placeholder') }}"
                                                        type="text" name="phone"
                                                        value="{{ old('type') === 'collaborate' ? old('phone') : '' }}"
                                                        required />
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">{{ __('messages.message') }}</label>
                                                    <textarea
                                                        class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light resize-none"
                                                        placeholder="{{ __('messages.collaborate_message_placeholder') }}" rows="5" name="message" required>{{ old('type') === 'collaborate' ? old('message') : '' }}</textarea>
                                                </div>
                                                <button
                                                    class="group inline-flex items-center gap-2 px-8 py-4 bg-deep-green text-white font-extrabold text-xs tracking-[0.2em] uppercase rounded-full shadow-lg shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-deep-green/30 transition-all"
                                                    type="submit">
                                                    {{ __('messages.submit_collaborate') }}
                                                    <span
                                                        class="material-symbols-outlined text-base {{ $is_rtl ? 'group-hover:-translate-x-1' : 'group-hover:translate-x-1' }} transition-transform">arrow_right_alt</span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="involve-tab-panel hidden" data-panel="tree">
                                            <form action="{{ route('tree-request.submit') }}" method="POST"
                                                enctype="multipart/form-data" class="space-y-6">
                                                @csrf
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <div class="md:col-span-2">
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">
                                                            {{ __('messages.tree_request_location') }}
                                                        </label>
                                                        <input name="location" value="{{ old('location') }}" required
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.tree_request_location_placeholder') }}"
                                                            type="text" />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">
                                                            {{ __('messages.tree_request_number_of_trees') }}
                                                        </label>
                                                        <input name="number_of_trees"
                                                            value="{{ old('number_of_trees') }}" required
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.tree_request_number_of_trees_placeholder') }}"
                                                            type="number" min="1" />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">
                                                            {{ __('messages.tree_request_water_source') }}
                                                        </label>
                                                        <input name="water_source" value="{{ old('water_source') }}"
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.tree_request_water_source_placeholder') }}"
                                                            type="text" />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">
                                                            {{ __('messages.tree_request_responsible_person') }}
                                                        </label>
                                                        <input name="responsible_person"
                                                            value="{{ old('responsible_person') }}" required
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.tree_request_responsible_person_placeholder') }}"
                                                            type="text" />
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">
                                                            {{ __('messages.tree_request_phone_whatsapp') }}
                                                        </label>
                                                        <input name="phone_whatsapp" value="{{ old('phone_whatsapp') }}"
                                                            required
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.tree_request_phone_whatsapp_placeholder') }}"
                                                            type="text" />
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[11px] font-bold uppercase tracking-widest text-charcoal/60 mb-2">
                                                        {{ __('messages.tree_request_photos_videos') }}
                                                    </label>
                                                    <div class="space-y-3" id="treeMediaInputs">
                                                        <input name="media[]"
                                                            class="block w-full text-sm text-charcoal/70 file:mr-4 file:py-3 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-widest file:bg-deep-green file:text-white hover:file:bg-deep-green/90"
                                                            type="file" accept="image/*,video/*" />
                                                    </div>
                                                    <button type="button" id="addTreeMediaInput"
                                                        class="mt-4 inline-flex items-center gap-2 rounded-xl border border-deep-green/20 bg-white px-5 py-3 text-xs font-bold uppercase tracking-widest text-deep-green hover:bg-deep-green/5 transition-all">
                                                        <span class="material-symbols-outlined text-base">add</span>
                                                        Add another file
                                                    </button>
                                                </div>
                                                <button type="submit"
                                                    class="group inline-flex items-center gap-2 px-8 py-4 bg-deep-green text-white font-extrabold text-xs tracking-[0.2em] uppercase rounded-full shadow-lg shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-deep-green/30 transition-all">
                                                    {{ __('messages.tree_request_submit') }}
                                                    <span
                                                        class="material-symbols-outlined text-base {{ $is_rtl ? 'group-hover:-translate-x-1' : 'group-hover:translate-x-1' }} transition-transform">arrow_right_alt</span>
                                                </button>
                                            </form>
                                        </div>

                                        <div class="involve-tab-panel hidden" data-panel="message">
                                            <form action="{{ route('contact.submit') }}" method="POST"
                                                class="space-y-6">
                                                @csrf
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <div>
                                                        <input
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.your_name') }}" type="text"
                                                            name="name" value="{{ old('name') }}" required />
                                                    </div>
                                                    <div>
                                                        <input
                                                            class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                            placeholder="{{ __('messages.email_address') }}"
                                                            type="email" name="email" value="{{ old('email') }}"
                                                            required />
                                                    </div>
                                                </div>
                                                <input
                                                    class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light"
                                                    placeholder="{{ __('messages.subject') }}" type="text"
                                                    name="subject" value="{{ old('subject') }}" required />
                                                <textarea
                                                    class="w-full bg-transparent border-0 border-b-2 border-charcoal/15 rounded-none px-0 py-3 focus:border-deep-green focus:ring-0 focus:outline-none transition-colors text-charcoal placeholder:text-charcoal/40 placeholder:font-light resize-none"
                                                    placeholder="{{ __('messages.how_can_we_help') }}" rows="5" name="message" required>{{ old('message') }}</textarea>
                                                <button
                                                    class="group inline-flex items-center gap-2 px-8 py-4 bg-deep-green text-white font-extrabold text-xs tracking-[0.2em] uppercase rounded-full shadow-lg shadow-deep-green/25 hover:bg-deep-green/90 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-deep-green/30 transition-all"
                                                    type="submit">
                                                    {{ __('messages.send_message') }}
                                                    <span
                                                        class="material-symbols-outlined text-base {{ $is_rtl ? 'group-hover:-translate-x-1' : 'group-hover:translate-x-1' }} transition-transform">arrow_right_alt</span>
                                                </button>
                                            </form>
                                        </div>
                        </div>
                    </div>
                </div>

                <script>
                    (function() {
                        const buttons = Array.from(document.querySelectorAll('#involveQuickLinks .involve-tab-btn'));
                        const panels = Array.from(document.querySelectorAll('#get-involved .involve-tab-panel'));

                        const sidebarTitle = document.getElementById('involveSidebarTitle');
                        const sidebarText = document.getElementById('involveSidebarText');
                        const sidebarList = document.getElementById('involveSidebarList');

                        const copy = {
                            volunteer: {
                                title: @json(__('messages.sidebar_volunteer_title')),
                                text: @json(__('messages.sidebar_volunteer_text')),
                                items: [
                                    @json(__('messages.sidebar_volunteer_item_1')),
                                    @json(__('messages.sidebar_volunteer_item_2')),
                                    @json(__('messages.sidebar_volunteer_item_3')),
                                ],
                            },
                            donate: {
                                title: @json(__('messages.sidebar_donate_title')),
                                text: @json(__('messages.sidebar_donate_text')),
                                items: [
                                    @json(__('messages.sidebar_donate_item_1')),
                                    @json(__('messages.sidebar_donate_item_2')),
                                    @json(__('messages.sidebar_donate_item_3')),
                                ],
                            },
                            sponsor: {
                                title: @json(__('messages.sidebar_sponsor_title')),
                                text: @json(__('messages.sidebar_sponsor_text')),
                                items: [
                                    @json(__('messages.sidebar_sponsor_item_1')),
                                    @json(__('messages.sidebar_sponsor_item_2')),
                                    @json(__('messages.sidebar_sponsor_item_3')),
                                ],
                            },
                            collaborate: {
                                title: @json(__('messages.sidebar_collaborate_title')),
                                text: @json(__('messages.sidebar_collaborate_text')),
                                items: [
                                    @json(__('messages.sidebar_collaborate_item_1')),
                                    @json(__('messages.sidebar_collaborate_item_2')),
                                    @json(__('messages.sidebar_collaborate_item_3')),
                                ],
                            },
                            tree: {
                                title: @json(__('messages.sidebar_tree_title')),
                                text: @json(__('messages.sidebar_tree_text')),
                                items: [
                                    @json(__('messages.sidebar_tree_item_1')),
                                    @json(__('messages.sidebar_tree_item_2')),
                                    @json(__('messages.sidebar_tree_item_3')),
                                ],
                            },
                            message: {
                                title: @json(__('messages.sidebar_message_title')),
                                text: @json(__('messages.sidebar_message_text')),
                                items: [
                                    @json(__('messages.sidebar_message_item_1')),
                                    @json(__('messages.sidebar_message_item_2')),
                                    @json(__('messages.sidebar_message_item_3')),
                                ],
                            },
                        };

                        function setTab(tab) {
                            buttons.forEach(btn => {
                                const active = btn.getAttribute('data-tab') === tab;
                                btn.dataset.active = active ? 'true' : 'false';
                            });

                            panels.forEach(panel => {
                                panel.classList.toggle('hidden', panel.getAttribute('data-panel') !== tab);
                            });

                            if (copy[tab]) {
                                sidebarTitle.textContent = copy[tab].title;
                                sidebarText.textContent = copy[tab].text;
                                sidebarList.innerHTML = copy[tab].items
                                    .map(item =>
                                        `<div class="flex items-start gap-3"><span class="material-symbols-outlined text-gold-accent">check_circle</span><p>${item}</p></div>`
                                    )
                                    .join('');
                            }
                        }

                        buttons.forEach(btn => {
                            btn.addEventListener('click', function() {
                                const tab = this.getAttribute('data-tab');
                                setTab(tab);
                                history.replaceState(null, '', '#get-involved');
                                // Clear any stored tab when user manually clicks
                                sessionStorage.removeItem('openInvolvementTab');
                            });
                        });

                        // Check for stored tab from home page
                        const storedTab = sessionStorage.getItem('openInvolvementTab');
                        const validTabs = ['volunteer', 'sponsor', 'collaborate', 'tree', 'donate', 'message'];
                        if (storedTab && validTabs.includes(storedTab)) {
                            setTab(storedTab);
                            sessionStorage.removeItem('openInvolvementTab'); // Clear after use
                        } else {
                            setTab('volunteer');
                        }

                        // Tree request media inputs (add one-by-one)
                        const addMediaBtn = document.getElementById('addTreeMediaInput');
                        const mediaInputsWrap = document.getElementById('treeMediaInputs');
                        if (addMediaBtn && mediaInputsWrap) {
                            addMediaBtn.addEventListener('click', function() {
                                const currentCount = mediaInputsWrap.querySelectorAll('input[type="file"]').length;
                                if (currentCount >= 10) {
                                    return;
                                }

                                const input = document.createElement('input');
                                input.name = 'media[]';
                                input.type = 'file';
                                input.accept = 'image/*,video/*';
                                input.className =
                                    'block w-full text-sm text-charcoal/70 file:mr-4 file:py-3 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-widest file:bg-deep-green file:text-white hover:file:bg-deep-green/90';
                                mediaInputsWrap.appendChild(input);
                            });
                        }
                    })();
                </script>
            </section>

        </main>
    </div>
    <section class="relative py-12 md:py-16 overflow-hidden bg-background-light" id="donation-section">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full bg-cover bg-center"
                style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCp83JCfKCRYgHZuZA8dzBJ3Ih5yeidFw9eQGrfMH89CUBjfUnGXPlxXvfb0DmVToP0iwQwPaK0W5XabUIrIKFxbSwRZd0LqiyyCermKdbgrB2uPKlC48Z7W_on-lUkUOnlJd62ok3dkmH7JXdQxf63fiy8lTJ7mxggLO5Vjo_p7uKwbi43Dl3GWm8aiiPqcYvOfRpQgKV513yPQakVJ-1w2d86ue27OPY3p8N9H5sr5Mg5oew18KgTO2Ehhj7cSGj-2a6ht7DOZOs');">
            </div>
            <div class="absolute inset-0 bg-white/95"></div>
        </div>
        <div class="relative max-w-4xl mx-auto text-center mb-20">
            <h4 class="text-sage-green font-bold tracking-[0.4em] text-xs uppercase mb-4">
                {{ __('messages.support_the_canopy') }}</h4>
            <h2 class="text-5xl md:text-7xl font-serif text-deep-green leading-tight">
                {{ __('messages.your_contribution') }} <br />
                <span class="font-bold italic">{{ __('messages.roots_our_change') }}</span>
            </h2>
            <div class="w-24 h-[1px] bg-gold-accent mx-auto mt-8 opacity-40"></div>
        </div>

        <div class="relative grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <span
                class="material-symbols-outlined absolute -top-12 -left-12 text-sage-green/20 text-7xl floating parallax-leaf">eco</span>
            <span
                class="material-symbols-outlined absolute -bottom-12 -right-12 text-gold-accent/20 text-8xl floating parallax-leaf"
                style="animation-delay: 2s;">spa</span>
            <span
                class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-primary/5 text-[300px] parallax-leaf">potted_plant</span>

            <div
                class="glass-panel rounded-[2rem] p-10 flex flex-col h-full relative group hover:-translate-y-2 transition-transform duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 p-6 opacity-10">
                    <span class="material-symbols-outlined text-6xl text-gold-accent">location_on</span>
                </div>
                <div class="mb-8">
                    <span class="material-symbols-outlined text-gold-accent text-3xl mb-4">distance</span>
                    <h3 class="font-serif text-2xl font-bold text-deep-green mb-2">{{ __('messages.local_handover') }}
                    </h3>
                    <p class="text-sage-green text-xs font-bold uppercase tracking-widest">
                        {{ __('messages.kabul_afghanistan') }}</p>
                </div>
                <div class="gold-line-art mb-8"></div>
                <div class="space-y-6 flex-grow">
                    <div>
                        <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                            {{ __('messages.primary_location') }}</p>
                        <p class="font-serif text-lg text-charcoal/80">Haidari Market</p>
                        <p class="text-sm text-charcoal/60 italic">District 4, Kabul</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                            {{ __('messages.exchange_office') }}</p>
                        <p class="font-serif text-lg text-charcoal/80">Saray Shazada</p>
                        <p class="text-sm text-charcoal/60">Main Financial Hub</p>
                    </div>
                </div>
                <div class="mt-10 pt-6 border-t border-charcoal/5">
                    <button
                        class="w-full py-3 rounded-full border border-gold-accent/30 text-gold-accent text-sm font-bold hover:bg-gold-accent hover:text-white transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">map</span>
                        {{ __('messages.get_directions') }}
                    </button>
                </div>
            </div>

            <div
                class="glass-panel rounded-[2rem] p-10 flex flex-col h-full relative group hover:-translate-y-2 transition-transform duration-500 overflow-hidden border-sage-green/20">
                <div class="absolute top-0 right-0 p-6 opacity-10">
                    <span class="material-symbols-outlined text-6xl text-sage-green">account_balance_wallet</span>
                </div>
                <div class="mb-8">
                    <span class="material-symbols-outlined text-sage-green text-3xl mb-4">payments</span>
                    <h3 class="font-serif text-2xl font-bold text-deep-green mb-2">{{ __('messages.moneygram_wu') }}</h3>
                    <p class="text-sage-green text-xs font-bold uppercase tracking-widest">
                        {{ __('messages.global_remittance') }}</p>
                </div>
                <div class="gold-line-art mb-8"></div>
                <div class="space-y-8 flex-grow">
                    <div>
                        <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-3">
                            {{ __('messages.receiver_name') }}</p>
                        <p class="font-serif text-3xl font-bold text-deep-green leading-tight">
                            {{ __('messages.mohammad_iqbal_alimyar') }}</p>
                        <p class="text-xs text-sage-green mt-2 font-medium">{{ __('messages.verify_spelling') }}</p>
                    </div>
                    <div class="bg-sage-green/5 p-4 rounded-xl border border-sage-green/10">
                        <p class="text-[10px] text-sage-green font-bold uppercase tracking-widest mb-2">
                            {{ __('messages.instructions') }}
                        </p>
                        <p class="text-xs text-charcoal/60 leading-relaxed">{{ __('messages.share_mtcn') }}</p>
                    </div>
                </div>
                <div class="mt-10 pt-6">
                    <button
                        class="w-full py-4 rounded-full bg-deep-green text-white text-sm font-bold hover:bg-deep-green/90 transition-all shadow-lg shadow-deep-green/20 flex items-center justify-center gap-2 js-copy-trigger"
                        data-copy-text="Mohammad Iqbal Alimyar">
                        <span class="material-symbols-outlined text-lg">content_copy</span>
                        {{ __('messages.copy_full_name') }}
                    </button>
                </div>
            </div>

            <div
                class="glass-panel rounded-[2rem] p-10 flex flex-col h-full relative group hover:-translate-y-2 transition-transform duration-500 overflow-hidden">
                <div class="absolute top-0 right-0 p-6 opacity-10">
                    <span class="material-symbols-outlined text-6xl text-charcoal">account_balance</span>
                </div>
                <div class="mb-8">
                    <span class="material-symbols-outlined text-charcoal/70 text-3xl mb-4">assured_workload</span>
                    <h3 class="font-serif text-2xl font-bold text-deep-green mb-2">{{ __('messages.bank_transfer') }}
                    </h3>
                    <p class="text-sage-green text-xs font-bold uppercase tracking-widest">
                        {{ __('messages.international_ziraat_bank') }}</p>
                </div>
                <div class="gold-line-art mb-8"></div>
                <div class="space-y-5 flex-grow">
                    <div class="relative">
                        <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                            {{ __('messages.iban_number') }}</p>
                        <div class="flex items-center justify-between bg-white/40 p-3 rounded-lg border border-charcoal/5">
                            <p class="font-mono text-xs text-charcoal font-bold">TR76 0001 0021 2193 4812 5001</p>
                            <span
                                class="material-symbols-outlined text-lg text-sage-green cursor-pointer hover:scale-110 transition-transform js-copy-trigger"
                                data-copy-text="TR76 0001 0021 2193 4812 5001">content_copy</span>
                        </div>
                    </div>
                    <div class="relative">
                        <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                            {{ __('messages.swift_bic_code') }}</p>
                        <div class="flex items-center justify-between bg-white/40 p-3 rounded-lg border border-charcoal/5">
                            <p class="font-mono text-xs text-charcoal font-bold">TCZRTRA2</p>
                            <span
                                class="material-symbols-outlined text-lg text-sage-green cursor-pointer hover:scale-110 transition-transform js-copy-trigger"
                                data-copy-text="TCZRTRA2">content_copy</span>
                        </div>
                    </div>
                    <div class="relative">
                        <p class="text-[10px] text-charcoal/40 font-bold uppercase tracking-widest mb-1">
                            {{ __('messages.account_holder') }}</p>
                        <p class="font-serif text-sm text-charcoal/80">EverGreen Conservation Fund</p>
                    </div>
                </div>
                <div class="mt-10 pt-6">
                    <button
                        class="w-full py-3 rounded-full bg-white border border-charcoal/10 text-charcoal text-sm font-bold hover:bg-charcoal/5 transition-all flex items-center justify-center gap-2 js-copy-all-bank"
                        type="button">
                        <span class="material-symbols-outlined text-lg">content_copy</span>
                        {{ __('messages.copy_all_bank_details') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-20 flex flex-col items-center gap-4 text-center">
            <div class="flex -space-x-2">
                <div
                    class="w-10 h-10 rounded-full border-2 border-white bg-sage-green flex items-center justify-center text-white text-[10px] font-bold">
                    99+
                </div>
                <div
                    class="w-10 h-10 rounded-full border-2 border-white bg-gold-accent flex items-center justify-center text-white text-xs">
                    <span class="material-symbols-outlined text-sm">shield_with_heart</span>
                </div>
            </div>
            <p class="text-charcoal/40 text-[10px] font-bold uppercase tracking-[0.3em]">
                {{ __('messages.fully_encrypted_secure_transfers') }}</p>
        </div>
    </section>

    <section class="w-full py-16 bg-white border-y border-charcoal/5">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-[10px] font-bold text-sage-green uppercase tracking-[0.3em] mb-10">
                {{ __('messages.follow_the_journey') }}</p>
            <div class="flex justify-center items-center gap-10 md:gap-16">
                <a class="group flex flex-col items-center gap-3"
                    href="https://www.facebook.com/share/1AZo3YKok2/?mibextid=wwXIfr">
                    <div
                        class="w-12 h-12 flex items-center justify-center rounded-full bg-off-white shadow-sm group-hover:bg-deep-green group-hover:text-white transition-all text-sage-green">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-bold text-charcoal/40 uppercase group-hover:text-deep-green">Facebook</span>
                </a>
                {{-- <a class="group flex flex-col items-center gap-3" href="#">
                    <div
                        class="w-12 h-12 flex items-center justify-center rounded-full bg-off-white shadow-sm group-hover:bg-deep-green group-hover:text-white transition-all text-sage-green">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M12.525.023C13.29 0 14.056 0 14.822.023c.067.85-.24 1.699-.867 2.302.751.421 1.581.65 2.442.677.03-.783-.242-1.556-.767-2.14.75.199 1.467.507 2.115.91-.137 2.133-1.406 3.993-3.322 4.792.052 1.854.197 3.693.435 5.522.083.642.04 1.294-.127 1.917-.738 2.39-3.006 4.145-5.503 4.264-2.174.103-4.32-.774-5.832-2.327-.14-.143-.243-.314-.303-.502-.173-.54-.26-1.103-.26-1.67 0-2.35 1.906-4.256 4.256-4.256.43 0 .85.065 1.246.186-.16-1.127-.235-2.261-.223-3.396-.922.451-1.936.685-2.964.685-1.934 0-3.665-1.11-4.463-2.85-.304-.663-.456-1.38-.445-2.106.01-1.458.835-2.77 2.132-3.396 1.05-.506 2.233-.506 3.282 0 1.297.626 2.122 1.938 2.132 3.396.011.726-.14 1.443-.445 2.106-.21.458-.514.863-.895 1.192.156 1.134.316 2.268.475 3.402.305-.112.63-.168.96-.168 1.428 0 2.585 1.157 2.585 2.585 0 .33-.062.65-.183.948.118.04.24.072.363.093 1.267.22 2.528-.48 3.033-1.688.24-.572.336-1.193.282-1.812-.224-1.748-.36-3.5-.407-5.257z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-bold text-charcoal/40 uppercase group-hover:text-deep-green">TikTok</span>
                </a> --}}
                <a class="group flex flex-col items-center gap-3" href="http://www.youtube.com/@EveryTreeForAHope">
                    <div
                        class="w-12 h-12 flex items-center justify-center rounded-full bg-off-white shadow-sm group-hover:bg-deep-green group-hover:text-white transition-all text-sage-green">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-bold text-charcoal/40 uppercase group-hover:text-deep-green">YouTube</span>
                </a>
                <a class="group flex flex-col items-center gap-3"
                    href="https://www.instagram.com/every_treeforahope?igsh=MXNhbjFuY3NlYWJldw==">
                    <div
                        class="w-12 h-12 flex items-center justify-center rounded-full bg-off-white shadow-sm group-hover:bg-deep-green group-hover:text-white transition-all text-sage-green">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-[10px] font-bold text-charcoal/40 uppercase group-hover:text-deep-green">Instagram</span>
                </a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                setTimeout(function() {
                    successMessage.style.transition = 'opacity 0.5s ease-out';
                    successMessage.style.opacity = '0';
                    setTimeout(function() {
                        successMessage.remove();
                    }, 500);
                }, 3000);
            }
        });
    </script>
@endpush
