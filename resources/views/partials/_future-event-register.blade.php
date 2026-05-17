{{-- Reusable: CTA row (Register + Share) + inline registration form. Expects $event. --}}
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
