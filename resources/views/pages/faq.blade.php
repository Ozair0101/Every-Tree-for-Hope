@extends('layouts.layout')
@section('title', __('messages.faq_page_title'))
@section('content')

    <!-- Hero -->
    <header class="relative py-28 md:py-36 overflow-hidden bg-deep-green">
        <div class="absolute inset-0 opacity-[0.04]"
            style="background-image: radial-gradient(rgba(255,255,255,0.4) 1px, transparent 1px); background-size: 22px 22px;"></div>
        <span class="material-symbols-outlined absolute top-10 right-[6%] text-white/[0.04] text-[220px] select-none pointer-events-none">help</span>

        <div class="relative z-10 max-w-4xl mx-auto px-6 text-center">
            <div class="inline-flex items-center gap-2 bg-white/10 border border-white/15 px-4 py-2 rounded-full backdrop-blur-sm mb-6">
                <span class="material-symbols-outlined text-vibrant-lime text-sm">quiz</span>
                <span class="text-white/90 text-[10px] font-bold uppercase tracking-[0.25em]">{{ __('messages.faq_badge') }}</span>
            </div>
            <h1 class="text-white text-5xl md:text-6xl lg:text-7xl font-serif font-bold leading-tight mb-4">
                {{ __('messages.faq_hero_title') }}
            </h1>
            <p class="text-white/60 text-lg max-w-2xl mx-auto">
                {{ __('messages.faq_hero_desc') }}
            </p>
            <div class="mt-8 flex items-center justify-center gap-6 text-white/40 text-xs font-bold">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-vibrant-lime text-base">check_circle</span>
                    <span>{{ $faqCount }} {{ __('messages.faq_answered') }}</span>
                </div>
                <div class="w-1 h-1 rounded-full bg-white/20"></div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-vibrant-lime text-base">category</span>
                    <span>{{ $faqs->count() }} {{ __('messages.faq_categories') }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Category Jump Links -->
    @if($faqs->count() > 0)
    <nav class="sticky top-[65px] z-40 bg-white/90 backdrop-blur-md border-b border-gray-200/70 py-3">
        <div class="max-w-5xl mx-auto px-4 overflow-x-auto no-scrollbar">
            <div class="flex items-center gap-2 min-w-max">
                @php
                    $categoryIcons = [
                        'Introduction & Background' => 'history_edu',
                        'Goals & Mission' => 'flag',
                        'Working Methods' => 'construction',
                        'Team & Structure' => 'groups',
                        'Impact & Community' => 'volunteer_activism',
                        'Future & Vision' => 'rocket_launch',
                        'Challenges' => 'warning',
                        'General' => 'help',
                    ];
                @endphp
                @foreach($faqs as $category => $items)
                    <a href="#cat-{{ Str::slug($category) }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold border border-gray-200 hover:border-deep-green hover:bg-deep-green hover:text-white text-charcoal transition-all whitespace-nowrap">
                        <span class="material-symbols-outlined text-sm">{{ $categoryIcons[$category] ?? 'help' }}</span>
                        {{ $category }}
                        <span class="bg-gray-100 text-charcoal/60 rounded-full px-1.5 py-0.5 text-[9px] font-extrabold">{{ $items->count() }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </nav>
    @endif

    <!-- FAQ Content -->
    <main class="py-16 md:py-24 px-4 sm:px-6">
        <div class="max-w-4xl mx-auto">
            @foreach($faqs as $category => $items)
                <section id="cat-{{ Str::slug($category) }}" class="mb-16 last:mb-0 scroll-mt-32">
                    <!-- Category header -->
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-deep-green/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-deep-green text-xl">{{ $categoryIcons[$category] ?? 'help' }}</span>
                        </div>
                        <div>
                            <h2 class="text-2xl md:text-3xl font-serif font-bold text-deep-green">{{ $category }}</h2>
                            <p class="text-charcoal/40 text-xs font-bold">{{ $items->count() }} {{ __('messages.faq_questions_label') }}</p>
                        </div>
                    </div>

                    <!-- Accordion items -->
                    <div class="space-y-3">
                        @foreach($items as $faq)
                            <div class="faq-item group rounded-xl border border-gray-200/80 bg-white hover:border-deep-green/20 transition-colors overflow-hidden"
                                data-faq-id="{{ $faq->id }}">
                                <!-- Question (toggle) -->
                                <button type="button"
                                    class="faq-toggle w-full flex items-start gap-4 px-5 py-4 text-left focus:outline-none"
                                    onclick="toggleFaq(this)">
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full bg-vibrant-lime/10 flex items-center justify-center mt-0.5 group-hover:bg-vibrant-lime/20 transition-colors">
                                        <span class="material-symbols-outlined text-deep-green text-base faq-icon transition-transform duration-300">add</span>
                                    </div>
                                    <h3 class="flex-1 text-deep-green text-sm md:text-base font-bold leading-relaxed">
                                        {{ $faq->question }}
                                    </h3>
                                </button>
                                <!-- Answer (hidden by default) -->
                                <div class="faq-answer hidden px-5 pb-5">
                                    <div class="ml-11 pl-4 border-l-2 border-vibrant-lime/30">
                                        <p class="text-charcoal/70 text-sm leading-[1.8]">
                                            {{ $faq->answer }}
                                        </p>
                                        @if($faq->asked_by_name)
                                            <p class="mt-3 text-charcoal/40 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                                                <span class="material-symbols-outlined text-xs">person</span>
                                                {{ __('messages.faq_answered_by') }} {{ $faq->asked_by_name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </main>

    <!-- Ask a Question Section -->
    <section class="py-20 md:py-28 px-4 sm:px-6 bg-gradient-to-b from-white to-background-light" id="ask-question">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-10">
                <span class="material-symbols-outlined text-vibrant-lime text-4xl mb-3">edit_note</span>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-deep-green mb-3">
                    {{ __('messages.faq_ask_title') }}
                </h2>
                <p class="text-charcoal/60 text-sm max-w-lg mx-auto">
                    {{ __('messages.faq_ask_desc') }}
                </p>
            </div>

            @if(session('success'))
                <div class="mb-8 p-4 rounded-xl bg-primary/10 border border-primary/20 text-deep-green text-sm font-medium flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-xl mt-0.5">check_circle</span>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('faq.submit') }}" method="POST"
                class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 md:p-8 space-y-5">
                @csrf

                <!-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-charcoal/60 text-[10px] font-bold uppercase tracking-widest mb-2 block">
                            {{ __('messages.faq_your_name') }} <span class="text-charcoal/30">({{ __('messages.faq_optional') }})</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full h-11 px-4 text-sm border border-gray-200 rounded-xl focus:border-deep-green focus:ring-1 focus:ring-deep-green/20 outline-none transition-colors"
                            placeholder="{{ __('messages.faq_name_placeholder') }}">
                    </div>
                    <div>
                        <label class="text-charcoal/60 text-[10px] font-bold uppercase tracking-widest mb-2 block">
                            {{ __('messages.faq_your_email') }} <span class="text-charcoal/30">({{ __('messages.faq_optional') }})</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full h-11 px-4 text-sm border border-gray-200 rounded-xl focus:border-deep-green focus:ring-1 focus:ring-deep-green/20 outline-none transition-colors"
                            placeholder="{{ __('messages.faq_email_placeholder') }}">
                    </div>
                </div> -->

                <div>
                    <label class="text-charcoal/60 text-[10px] font-bold uppercase tracking-widest mb-2 block">
                        {{ __('messages.faq_your_question') }} <span class="text-red-400">*</span>
                    </label>
                    <textarea name="question" rows="4" required
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:border-deep-green focus:ring-1 focus:ring-deep-green/20 outline-none transition-colors resize-none"
                        placeholder="{{ __('messages.faq_question_placeholder') }}">{{ old('question') }}</textarea>
                    @error('question')
                        <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full h-12 bg-deep-green text-white font-bold rounded-xl hover:bg-deep-green/90 transition-all flex items-center justify-center gap-2 shadow-lg shadow-deep-green/10">
                    <span class="material-symbols-outlined text-vibrant-lime text-lg">send</span>
                    {{ __('messages.faq_submit_btn') }}
                </button>

                <p class="text-charcoal/40 text-[10px] text-center">
                    {{ __('messages.faq_submit_note') }}
                </p>
            </form>
        </div>
    </section>

    @push('scripts')
    <script>
        function toggleFaq(btn) {
            const item = btn.closest('.faq-item');
            const answer = item.querySelector('.faq-answer');
            const icon = item.querySelector('.faq-icon');
            const isOpen = !answer.classList.contains('hidden');

            // Close all other open items
            document.querySelectorAll('.faq-answer:not(.hidden)').forEach(a => {
                if (a !== answer) {
                    a.classList.add('hidden');
                    a.closest('.faq-item').querySelector('.faq-icon').textContent = 'add';
                    a.closest('.faq-item').querySelector('.faq-icon').style.transform = '';
                    a.closest('.faq-item').classList.remove('border-deep-green/30', 'shadow-md');
                }
            });

            if (isOpen) {
                answer.classList.add('hidden');
                icon.textContent = 'add';
                icon.style.transform = '';
                item.classList.remove('border-deep-green/30', 'shadow-md');
            } else {
                answer.classList.remove('hidden');
                icon.textContent = 'remove';
                icon.style.transform = 'rotate(180deg)';
                item.classList.add('border-deep-green/30', 'shadow-md');
            }
        }

        // Smooth scroll for category links
        document.querySelectorAll('a[href^="#cat-"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href'))?.scrollIntoView({
                    behavior: 'smooth', block: 'start'
                });
            });
        });
    </script>
    @endpush
@endsection
