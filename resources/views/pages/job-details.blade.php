@extends('layouts.layout')
@section('title', $job->title)
@section('content')

    @php
        $responsibilities = $job->lines('responsibilities');
        $requirements = $job->lines('requirements');
        $benefits = $job->lines('benefits');
    @endphp

    <main class="flex-grow bg-stone-50">
        <!-- Header -->
        <section class="bg-deep-green relative overflow-hidden">
            <div class="absolute inset-0 opacity-20"
                style="background-image:radial-gradient(circle at 85% 15%, rgba(170,221,90,.5) 0, transparent 40%);"></div>
            <div class="relative max-w-5xl mx-auto px-6 pt-12 pb-14">
                <a href="{{ route('careers') }}"
                    class="inline-flex items-center gap-1 text-white/70 hover:text-white text-sm font-semibold mb-6 transition-colors">
                    <span class="material-symbols-outlined text-base rtl:rotate-180">arrow_back</span>
                    {{ __('messages.jobs_back_to_all') }}
                </a>

                <div class="flex items-center gap-2 flex-wrap mb-4">
                    @if ($job->category)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-white text-[11px] font-bold uppercase tracking-wider">
                            @if ($job->category->icon)
                                <span class="material-symbols-outlined text-xs">{{ $job->category->icon }}</span>
                            @endif
                            {{ $job->category->name }}
                        </span>
                    @endif
                    @if (!$job->is_open)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-500/20 border border-red-300/30 text-red-100 text-[11px] font-bold uppercase tracking-wider">
                            <span class="material-symbols-outlined text-xs">lock</span>
                            {{ __('messages.jobs_closed_title') }}
                        </span>
                    @endif
                </div>

                {{-- Hiring company --}}
                @if ($job->company)
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-12 w-12 rounded-xl bg-white p-1 shrink-0 flex items-center justify-center overflow-hidden">
                            @if ($job->company->logo_url)
                                <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}" class="h-full w-full object-contain rounded-lg">
                            @else
                                <span class="font-bold text-deep-green">{{ $job->company->initials }}</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-white font-bold flex items-center gap-1.5">
                                {{ $job->company->name }}
                                @if ($job->company->is_verified)
                                    <span class="material-symbols-outlined text-base text-vibrant-lime" title="{{ __('messages.jobs_verified') }}">verified</span>
                                @endif
                            </p>
                            @if ($job->company->industry)
                                <p class="text-white/60 text-xs">{{ $job->company->industry }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <h1 class="text-3xl md:text-4xl lg:text-5xl font-serif text-white leading-tight mb-5">
                    {{ $job->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-white/80">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-base text-vibrant-lime">schedule</span>
                        {{ __('messages.jobs_type_' . $job->employment_type) }}
                    </span>
                    @if ($job->location)
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-vibrant-lime">location_on</span>
                            {{ $job->location }}{{ $job->province ? ', ' . $job->province : '' }}
                        </span>
                    @endif
                    @if ($job->is_remote)
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-vibrant-lime">home_work</span>
                            {{ __('messages.jobs_remote') }}
                        </span>
                    @endif
                    @if ($job->experience_level)
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-vibrant-lime">trending_up</span>
                            {{ __('messages.jobs_exp_' . $job->experience_level) }}
                        </span>
                    @endif
                </div>

                @if ($job->is_open)
                    <button type="button" data-apply-open
                        class="inline-flex items-center gap-2 mt-7 px-7 py-3.5 bg-gold-accent text-deep-green text-sm font-bold rounded-full shadow-lg hover:bg-gold-accent/90 hover:-translate-y-0.5 transition-all">
                        <span class="material-symbols-outlined text-base">send</span>
                        {{ __('messages.jobs_apply_now') }}
                    </button>
                @endif
            </div>
        </section>

        <section class="max-w-5xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main content -->
                <div class="lg:col-span-2 space-y-10">
                    <div>
                        <h2 class="text-2xl font-serif text-deep-green mb-3">{{ __('messages.jobs_section_about') }}</h2>
                        <div class="prose-job text-charcoal/80 leading-relaxed whitespace-pre-line">{{ $job->description }}</div>
                    </div>

                    @if (count($responsibilities))
                        <div>
                            <h2 class="text-2xl font-serif text-deep-green mb-4">{{ __('messages.jobs_section_responsibilities') }}</h2>
                            <ul class="space-y-2.5">
                                @foreach ($responsibilities as $item)
                                    <li class="flex items-start gap-3 text-charcoal/80">
                                        <span class="material-symbols-outlined text-vibrant-lime text-lg shrink-0 mt-0.5">check_circle</span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (count($requirements))
                        <div>
                            <h2 class="text-2xl font-serif text-deep-green mb-4">{{ __('messages.jobs_section_requirements') }}</h2>
                            <ul class="space-y-2.5">
                                @foreach ($requirements as $item)
                                    <li class="flex items-start gap-3 text-charcoal/80">
                                        <span class="material-symbols-outlined text-deep-green/50 text-lg shrink-0 mt-0.5">task_alt</span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (count($benefits))
                        <div>
                            <h2 class="text-2xl font-serif text-deep-green mb-4">{{ __('messages.jobs_section_benefits') }}</h2>
                            <ul class="space-y-2.5">
                                @foreach ($benefits as $item)
                                    <li class="flex items-start gap-3 text-charcoal/80">
                                        <span class="material-symbols-outlined text-gold-accent text-lg shrink-0 mt-0.5">favorite</span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($job->company && ($job->company->about || $job->company->website))
                        <div class="bg-white rounded-2xl border border-deep-green/10 p-6">
                            <h2 class="text-2xl font-serif text-deep-green mb-4">{{ __('messages.jobs_section_company') }}</h2>
                            <div class="flex items-center gap-4 mb-4">
                                <div class="h-14 w-14 rounded-xl bg-stone-50 border border-deep-green/10 p-1.5 shrink-0 flex items-center justify-center overflow-hidden">
                                    @if ($job->company->logo_url)
                                        <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}" class="h-full w-full object-contain">
                                    @else
                                        <span class="font-bold text-deep-green text-lg">{{ $job->company->initials }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-deep-green flex items-center gap-1.5">
                                        {{ $job->company->name }}
                                        @if ($job->company->is_verified)
                                            <span class="material-symbols-outlined text-base text-deep-green" title="{{ __('messages.jobs_verified') }}">verified</span>
                                        @endif
                                    </p>
                                    @if ($job->company->industry || $job->company->location)
                                        <p class="text-charcoal/55 text-sm">{{ collect([$job->company->industry, $job->company->location])->filter()->implode(' · ') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if ($job->company->about)
                                <p class="text-charcoal/75 leading-relaxed whitespace-pre-line">{{ $job->company->about }}</p>
                            @endif
                            @if ($job->company->website)
                                <a href="{{ $job->company->website }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1.5 mt-4 text-sm font-bold text-deep-green hover:text-gold-accent transition-colors">
                                    <span class="material-symbols-outlined text-base">language</span>
                                    {{ __('messages.jobs_visit_website') }}
                                    <span class="material-symbols-outlined text-sm rtl:rotate-180">north_east</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-deep-green/10 shadow-sm p-6 lg:sticky lg:top-24">
                        <h3 class="font-serif text-lg text-deep-green mb-4">{{ __('messages.jobs_overview') }}</h3>
                        <dl class="space-y-3.5 text-sm">
                            @php
                                $overview = [
                                    ['business_center', __('messages.jobs_field_company'), $job->company?->name],
                                    ['category', __('messages.jobs_field_category'), $job->category?->name],
                                    ['work', __('messages.jobs_field_type'), __('messages.jobs_type_' . $job->employment_type)],
                                    ['location_on', __('messages.jobs_field_location'), $job->location ? ($job->location . ($job->province ? ', ' . $job->province : '')) : ($job->province ?: null)],
                                    ['trending_up', __('messages.jobs_field_experience'), $job->experience_level ? __('messages.jobs_exp_' . $job->experience_level) : null],
                                    ['payments', __('messages.jobs_field_salary'), $job->salary_range],
                                    ['groups', __('messages.jobs_field_openings'), $job->openings > 1 ? $job->openings : null],
                                    ['event', __('messages.jobs_field_deadline'), $job->application_deadline?->translatedFormat('M j, Y')],
                                ];
                            @endphp
                            @foreach ($overview as [$icon, $label, $value])
                                @if ($value)
                                    <div class="flex items-start gap-3">
                                        <span class="material-symbols-outlined text-deep-green/40 text-lg shrink-0">{{ $icon }}</span>
                                        <div class="min-w-0">
                                            <dt class="text-charcoal/45 text-xs uppercase tracking-wide">{{ $label }}</dt>
                                            <dd class="text-deep-green font-semibold">{{ $value }}</dd>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </dl>

                        @if ($job->is_open)
                            <button type="button" data-apply-open
                                class="mt-6 w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-deep-green text-white text-sm font-bold rounded-xl hover:bg-deep-green/90 transition-colors">
                                <span class="material-symbols-outlined text-base">send</span>
                                {{ __('messages.jobs_apply_now') }}
                            </button>
                        @endif

                        <div class="mt-5 pt-5 border-t border-deep-green/5">
                            <p class="text-xs text-charcoal/45 mb-2">{{ __('messages.jobs_share') }}</p>
                            <div class="flex items-center gap-2">
                                <a target="_blank" rel="noopener"
                                    href="https://wa.me/?text={{ urlencode($job->title . ' — ' . route('careers.show', $job)) }}"
                                    class="h-9 w-9 inline-flex items-center justify-center rounded-full bg-stone-100 text-deep-green hover:bg-deep-green hover:text-white transition-colors" aria-label="WhatsApp">
                                    <span class="material-symbols-outlined text-lg">chat</span>
                                </a>
                                <a target="_blank" rel="noopener"
                                    href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('careers.show', $job)) }}"
                                    class="h-9 w-9 inline-flex items-center justify-center rounded-full bg-stone-100 text-deep-green hover:bg-deep-green hover:text-white transition-colors" aria-label="LinkedIn">
                                    <span class="material-symbols-outlined text-lg">share</span>
                                </a>
                                <button type="button" data-copy-link data-url="{{ route('careers.show', $job) }}"
                                    class="h-9 w-9 inline-flex items-center justify-center rounded-full bg-stone-100 text-deep-green hover:bg-deep-green hover:text-white transition-colors" aria-label="Copy link">
                                    <span class="material-symbols-outlined text-lg">link</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- Application form -->
            <div id="apply" class="scroll-mt-24 mt-12 max-w-3xl mx-auto">
                @if (session('application_success'))
                    <div class="bg-white border border-vibrant-lime/40 rounded-2xl shadow-sm p-8 text-center">
                        <div class="mx-auto h-16 w-16 rounded-full bg-vibrant-lime/15 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-vibrant-lime text-4xl">task_alt</span>
                        </div>
                        <h2 class="text-2xl font-serif text-deep-green mb-2">{{ __('messages.jobs_apply_success_title') }}</h2>
                        <p class="text-charcoal/70 max-w-md mx-auto">{{ session('success') }}</p>
                        <a href="{{ route('careers') }}"
                            class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-deep-green text-white text-sm font-bold rounded-full hover:bg-deep-green/90 transition-colors">
                            <span class="material-symbols-outlined text-base">work</span>
                            {{ __('messages.jobs_back_to_all') }}
                        </a>
                    </div>
                @elseif ($job->is_open)
                    {{-- Form / external link stays hidden until the user opens the apply modal --}}
                   
                @else
                    <div class="bg-white border border-deep-green/10 rounded-2xl shadow-sm p-8 text-center">
                        <span class="material-symbols-outlined text-deep-green/25 text-5xl mb-3">lock_clock</span>
                        <h2 class="text-xl font-serif text-deep-green mb-2">{{ __('messages.jobs_closed_title') }}</h2>
                        <p class="text-charcoal/60 max-w-md mx-auto">{{ __('messages.jobs_closed_desc') }}</p>
                    </div>
                @endif
            </div>

            {{-- ===== Apply modal (hidden until "Apply now" is clicked) ===== --}}
            @if ($job->is_open)
                <div id="apply-modal" class="fixed inset-0 z-[70] hidden">
                    <div data-apply-close class="absolute inset-0 bg-deep-green/60 backdrop-blur-sm"></div>
                    <div class="absolute inset-0 overflow-y-auto p-4 sm:p-6">
                        <div class="relative mx-auto my-2 sm:my-6 w-full max-w-2xl bg-white rounded-2xl shadow-2xl">
                            {{-- Modal header --}}
                            <div class="sticky top-0 z-10 flex items-center justify-between gap-3 px-6 py-4 border-b border-deep-green/10 bg-white rounded-t-2xl">
                                <h2 class="text-lg font-serif text-deep-green">{{ __('messages.jobs_apply_heading') }}</h2>
                                <button type="button" data-apply-close aria-label="{{ __('messages.jobs_close') }}"
                                    class="p-2 -me-2 rounded-full text-charcoal/50 hover:bg-stone-100 transition-colors">
                                    <span class="material-symbols-outlined">close</span>
                                </button>
                            </div>

                            <div class="p-6 sm:p-8">
                                @if ($job->application_url)
                                    {{-- External application link --}}
                                    <div class="text-center">
                                        <div class="mx-auto h-16 w-16 rounded-full bg-vibrant-lime/15 flex items-center justify-center mb-4">
                                            <span class="material-symbols-outlined text-vibrant-lime text-4xl">captive_portal</span>
                                        </div>
                                        <h3 class="text-xl font-serif text-deep-green mb-2">{{ __('messages.jobs_apply_external_title') }}</h3>
                                        <p class="text-charcoal/65 text-sm max-w-md mx-auto mb-6">{{ __('messages.jobs_apply_external_desc') }}</p>
                                        <a href="{{ $job->application_url }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-gold-accent text-deep-green text-sm font-bold rounded-full shadow-md hover:bg-gold-accent/90 hover:-translate-y-0.5 transition-all">
                                            <span class="material-symbols-outlined text-base">open_in_new</span>
                                            {{ __('messages.jobs_apply_external_btn') }}
                                        </a>
                                        <p class="mt-4 text-xs text-charcoal/40 break-all" dir="ltr">{{ $job->application_url }}</p>
                                    </div>
                                @else
                                    <p class="text-charcoal/60 text-sm mb-6">{{ __('messages.jobs_apply_sub') }}</p>

                                    @if ($errors->any())
                                        <div class="mb-6 flex items-start gap-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                                            <span class="material-symbols-outlined text-base">error</span>
                                            <span>{{ __('messages.jobs_form_error') }}</span>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('careers.apply', $job) }}" enctype="multipart/form-data"
                                        class="space-y-5">
                                        @csrf

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                            <div>
                                                <label for="full_name" class="block text-sm font-semibold text-deep-green mb-1.5">{{ __('messages.jobs_form_name') }} <span class="text-red-500">*</span></label>
                                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                                                    class="w-full rounded-xl border border-deep-green/15 px-4 py-2.5 text-sm focus:border-deep-green focus:ring-0 focus:outline-none @error('full_name') border-red-400 @enderror">
                                                @error('full_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                            </div>
                                            <div>
                                                <label for="email" class="block text-sm font-semibold text-deep-green mb-1.5">{{ __('messages.jobs_form_email') }} <span class="text-red-500">*</span></label>
                                                <input type="email" id="email" name="email" value="{{ old('email') }}" required dir="ltr"
                                                    class="w-full rounded-xl border border-deep-green/15 px-4 py-2.5 text-sm focus:border-deep-green focus:ring-0 focus:outline-none @error('email') border-red-400 @enderror">
                                                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                            </div>
                                            <div>
                                                <label for="phone" class="block text-sm font-semibold text-deep-green mb-1.5">{{ __('messages.jobs_form_phone') }} <span class="text-red-500">*</span></label>
                                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required dir="ltr"
                                                    class="w-full rounded-xl border border-deep-green/15 px-4 py-2.5 text-sm focus:border-deep-green focus:ring-0 focus:outline-none @error('phone') border-red-400 @enderror">
                                                @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                            </div>
                                            <div>
                                                <label for="location" class="block text-sm font-semibold text-deep-green mb-1.5">{{ __('messages.jobs_form_location') }}</label>
                                                <input type="text" id="location" name="location" value="{{ old('location') }}"
                                                    class="w-full rounded-xl border border-deep-green/15 px-4 py-2.5 text-sm focus:border-deep-green focus:ring-0 focus:outline-none">
                                            </div>
                                            <div>
                                                <label for="years_experience" class="block text-sm font-semibold text-deep-green mb-1.5">{{ __('messages.jobs_form_experience') }}</label>
                                                <input type="number" id="years_experience" name="years_experience" value="{{ old('years_experience') }}" min="0" max="80"
                                                    class="w-full rounded-xl border border-deep-green/15 px-4 py-2.5 text-sm focus:border-deep-green focus:ring-0 focus:outline-none">
                                            </div>
                                            <div>
                                                <label for="linkedin_url" class="block text-sm font-semibold text-deep-green mb-1.5">{{ __('messages.jobs_form_linkedin') }}</label>
                                                <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url') }}" dir="ltr" placeholder="https://"
                                                    class="w-full rounded-xl border border-deep-green/15 px-4 py-2.5 text-sm focus:border-deep-green focus:ring-0 focus:outline-none">
                                                @error('linkedin_url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                            </div>
                                        </div>

                                        <div>
                                            <label for="portfolio_url" class="block text-sm font-semibold text-deep-green mb-1.5">{{ __('messages.jobs_form_portfolio') }}</label>
                                            <input type="url" id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url') }}" dir="ltr" placeholder="https://"
                                                class="w-full rounded-xl border border-deep-green/15 px-4 py-2.5 text-sm focus:border-deep-green focus:ring-0 focus:outline-none">
                                            @error('portfolio_url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                        </div>

                                        <div>
                                            <label for="cover_letter" class="block text-sm font-semibold text-deep-green mb-1.5">{{ __('messages.jobs_form_cover') }}</label>
                                            <textarea id="cover_letter" name="cover_letter" rows="5" placeholder="{{ __('messages.jobs_form_cover_ph') }}"
                                                class="w-full rounded-xl border border-deep-green/15 px-4 py-2.5 text-sm focus:border-deep-green focus:ring-0 focus:outline-none">{{ old('cover_letter') }}</textarea>
                                        </div>

                                        {{-- Résumé upload --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-deep-green mb-1.5">{{ __('messages.jobs_form_resume') }} <span class="text-red-500">*</span></label>
                                            <label for="resume" data-resume-drop
                                                class="flex flex-col items-center justify-center gap-2 w-full rounded-xl border-2 border-dashed border-deep-green/20 bg-stone-50 px-4 py-7 text-center cursor-pointer hover:border-deep-green/40 hover:bg-stone-100 transition-colors @error('resume') border-red-400 @enderror">
                                                <span class="material-symbols-outlined text-3xl text-deep-green/40">upload_file</span>
                                                <span data-resume-label class="text-sm text-charcoal/60">{{ __('messages.jobs_form_resume_cta') }}</span>
                                                <span class="text-xs text-charcoal/40">{{ __('messages.jobs_form_resume_hint') }}</span>
                                                <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required class="hidden">
                                            </label>
                                            @error('resume')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                                        </div>

                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-gold-accent text-deep-green text-sm font-bold rounded-full shadow-md hover:bg-gold-accent/90 hover:-translate-y-0.5 transition-all">
                                            <span class="material-symbols-outlined text-base">send</span>
                                            {{ __('messages.jobs_form_submit') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($relatedJobs->isNotEmpty())
                <div class="mt-16">
                    <h2 class="text-2xl font-serif text-deep-green mb-6 text-center">{{ __('messages.jobs_related') }}</h2>
                    <div class="space-y-4 max-w-3xl mx-auto">
                        @include('partials.jobs-list', ['jobs' => $relatedJobs])
                    </div>
                </div>
            @endif
        </section>
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Show chosen file name in the upload box
                const input = document.getElementById('resume');
                const label = document.querySelector('[data-resume-label]');
                if (input && label) {
                    const original = label.textContent;
                    input.addEventListener('change', function() {
                        label.textContent = input.files && input.files.length
                            ? input.files[0].name
                            : original;
                    });
                }

                // Copy link button
                const copyBtn = document.querySelector('[data-copy-link]');
                if (copyBtn) {
                    copyBtn.addEventListener('click', function() {
                        const url = copyBtn.getAttribute('data-url');
                        if (navigator.clipboard) {
                            navigator.clipboard.writeText(url).then(function() {
                                const icon = copyBtn.querySelector('.material-symbols-outlined');
                                if (icon) {
                                    const prev = icon.textContent;
                                    icon.textContent = 'check';
                                    setTimeout(function() { icon.textContent = prev; }, 1500);
                                }
                            });
                        }
                    });
                }

                // Apply modal: open / close, scroll lock, Escape, backdrop click
                const applyModal = document.getElementById('apply-modal');
                if (applyModal) {
                    const openApply = function() {
                        applyModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    };
                    const closeApply = function() {
                        applyModal.classList.add('hidden');
                        document.body.style.overflow = '';
                    };

                    document.querySelectorAll('[data-apply-open]').forEach(function(btn) {
                        btn.addEventListener('click', openApply);
                    });
                    applyModal.querySelectorAll('[data-apply-close]').forEach(function(el) {
                        el.addEventListener('click', closeApply);
                    });
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape' && !applyModal.classList.contains('hidden')) closeApply();
                    });

                    // Re-open the modal automatically if the form returned with errors
                    @if ($errors->any())
                        openApply();
                    @endif
                }
            });
        </script>
    @endpush
@endsection
