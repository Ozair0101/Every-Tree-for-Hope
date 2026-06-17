@extends('layouts.layout')
@section('title', __('messages.voices_share_title') . ' — ' . __('messages.voices_page_title'))
@section('content')

    {{-- Full dedicated page for sharing a new voice (was previously a modal). --}}
    <main class="flex-grow bg-[#f7f6f1] dark:bg-background-dark">

        {{-- compact hero --}}
        <section class="relative overflow-hidden bg-deep-green">
            <div class="absolute inset-0 opacity-25"
                style="background-image:radial-gradient(circle at 15% 20%, rgba(132,204,22,.45) 0, transparent 38%),radial-gradient(circle at 85% 10%, rgba(255,255,255,.35) 0, transparent 40%);">
            </div>
            <div class="relative max-w-3xl mx-auto px-6 pt-16 pb-20 text-center">
                <a href="{{ route('voices.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-bold text-white/70 hover:text-white transition-colors mb-6">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    {{ __('messages.voices_back') }}
                </a>
                <h1 class="text-3xl md:text-4xl font-serif text-white leading-tight mb-3">{{ __('messages.voices_share_title') }}</h1>
                <p class="text-white/80 max-w-xl mx-auto">
                    {{ __('messages.voices_share_subtitle') }}
                </p>
            </div>
            <div class="absolute -bottom-px left-0 right-0 h-6 bg-[#f7f6f1] dark:bg-background-dark"
                style="clip-path:polygon(0 60%,4% 40%,8% 60%,12% 35%,16% 60%,20% 45%,24% 60%,28% 38%,32% 60%,36% 42%,40% 60%,44% 36%,48% 60%,52% 44%,56% 60%,60% 38%,64% 60%,68% 42%,72% 60%,76% 36%,80% 60%,84% 44%,88% 60%,92% 38%,96% 60%,100% 42%,100% 100%,0 100%);">
            </div>
        </section>

        <section class="max-w-2xl mx-auto px-6 py-12">
            <div class="bg-white dark:bg-white/5 rounded-3xl shadow-xl shadow-black/5 border border-black/5 overflow-hidden">
                @if ($errors->any())
                    <div class="mx-6 mt-6 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('voices.store') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-5">
                    @csrf
                    {{-- honeypot --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-charcoal mb-1.5">{{ __('messages.voices_form_name') }} <span class="text-rose-500">*</span></label>
                            <input type="text" name="author_name" value="{{ old('author_name') }}" required maxlength="120"
                                class="w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-deep-green/30 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-charcoal mb-1.5">{{ __('messages.voices_form_country') }}</label>
                            <input type="text" name="country" value="{{ old('country') }}" maxlength="120" placeholder="{{ __('messages.voices_form_country_ph') }}"
                                class="w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-deep-green/30 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-charcoal mb-1.5">{{ __('messages.voices_form_email') }} <span class="font-normal text-charcoal/40">{{ __('messages.voices_form_email_note') }}</span></label>
                        <input type="email" name="author_email" value="{{ old('author_email') }}" maxlength="255"
                            class="w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-deep-green/30 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-charcoal mb-1.5">{{ __('messages.voices_form_type') }} <span class="text-rose-500">*</span></label>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($categories as $key => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="category" value="{{ $key }}" class="peer sr-only" {{ old('category', 'experience') === $key ? 'checked' : '' }}>
                                    <span class="inline-block px-4 py-2 rounded-full text-sm font-bold border border-black/10 text-charcoal peer-checked:bg-deep-green peer-checked:text-white peer-checked:border-deep-green transition-colors">{{ __('messages.voices_cat_' . $key) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-charcoal mb-1.5">{{ __('messages.voices_form_title') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required maxlength="160" placeholder="{{ __('messages.voices_form_title_ph') }}"
                            class="w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-deep-green/30 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-charcoal mb-1.5">{{ __('messages.voices_form_story') }} <span class="text-rose-500">*</span></label>
                        <textarea name="body" required rows="8" maxlength="8000" placeholder="{{ __('messages.voices_form_story_ph') }}"
                            class="w-full rounded-xl border border-black/10 px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-deep-green/30 focus:outline-none">{{ old('body') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-charcoal mb-1.5">{{ __('messages.voices_form_photo') }} <span class="font-normal text-charcoal/40">{{ __('messages.voices_form_photo_note') }}</span></label>
                        <input type="file" name="image" accept="image/*"
                            class="w-full text-sm text-charcoal/70 file:mr-3 file:rounded-full file:border-0 file:bg-deep-green/10 file:px-4 file:py-2 file:text-sm file:font-bold file:text-deep-green hover:file:bg-deep-green/20">
                    </div>

                    <div class="flex items-center gap-2 rounded-xl bg-amber-50 border border-amber-200 px-3.5 py-2.5 text-[12px] text-amber-800">
                        <span class="material-symbols-outlined text-[18px]">shield</span>
                        {{ __('messages.voices_form_moderation') }}
                    </div>

                    <div class="flex items-center gap-3 pt-1">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-deep-green text-white text-sm font-extrabold rounded-full hover:bg-deep-green/90 transition-colors">
                            <span class="material-symbols-outlined text-lg">send</span>
                            {{ __('messages.voices_form_submit') }}
                        </button>
                        <a href="{{ route('voices.index') }}" class="text-sm font-bold text-charcoal/60 hover:text-charcoal">{{ __('messages.voices_form_cancel') }}</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
@endsection
