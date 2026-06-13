<div class="space-y-4">
    @forelse ($jobs as $job)
        <a href="{{ route('careers.show', $job) }}"
            class="group block bg-white rounded-2xl border border-deep-green/10 p-5 sm:p-6 shadow-sm hover:shadow-xl hover:border-deep-green/30 hover:-translate-y-0.5 transition-all">
            <div class="flex items-start gap-4">
                <div class="hidden sm:flex h-12 w-12 rounded-xl bg-stone-50 border border-deep-green/10 overflow-hidden items-center justify-center shrink-0">
                    @if ($job->company && $job->company->logo_url)
                        <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}" class="h-full w-full object-contain">
                    @elseif ($job->company)
                        <span class="font-bold text-deep-green text-sm">{{ $job->company->initials }}</span>
                    @else
                        <span class="material-symbols-outlined text-deep-green">work</span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1.5">
                        @if ($job->is_featured)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-gold-accent/15 text-gold-accent text-[11px] font-bold uppercase tracking-wider">
                                <span class="material-symbols-outlined text-xs">star</span>
                                {{ __('messages.jobs_featured') }}
                            </span>
                        @endif
                        @if ($job->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-vibrant-lime/15 text-deep-green text-[11px] font-bold uppercase tracking-wider">
                                {{ $job->category->name }}
                            </span>
                        @endif
                    </div>

                    <h3 class="text-lg sm:text-xl font-bold text-deep-green group-hover:text-gold-accent transition-colors">
                        {{ $job->title }}
                    </h3>

                    @if ($job->company)
                        <p class="text-sm text-charcoal/70 mt-0.5 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-deep-green/50">business_center</span>
                            {{ $job->company->name }}
                            @if ($job->company->is_verified)
                                <span class="material-symbols-outlined text-sm text-deep-green" title="{{ __('messages.jobs_verified') }}">verified</span>
                            @endif
                        </p>
                    @endif

                    @if ($job->summary)
                        <p class="text-charcoal/60 text-sm mt-1 line-clamp-2">{{ $job->summary }}</p>
                    @endif

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-3 text-xs text-charcoal/60">
                        <span class="inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm text-deep-green/50">schedule</span>
                            {{ __('messages.jobs_type_' . $job->employment_type) }}
                        </span>
                        @if ($job->location)
                            <span class="inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm text-deep-green/50">location_on</span>
                                {{ $job->location }}
                            </span>
                        @endif
                        @if ($job->is_remote)
                            <span class="inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm text-deep-green/50">home_work</span>
                                {{ __('messages.jobs_remote') }}
                            </span>
                        @endif
                        @if ($job->experience_level)
                            <span class="inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm text-deep-green/50">trending_up</span>
                                {{ __('messages.jobs_exp_' . $job->experience_level) }}
                            </span>
                        @endif
                        @if ($job->salary_range)
                            <span class="inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm text-deep-green/50">payments</span>
                                {{ $job->salary_range }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="hidden sm:flex flex-col items-end gap-3 shrink-0 text-end">
                    <span class="text-xs text-charcoal/40 whitespace-nowrap">{{ $job->created_at->diffForHumans() }}</span>
                    <span class="inline-flex items-center gap-1 text-sm font-bold text-deep-green group-hover:gap-2 transition-all whitespace-nowrap">
                        {{ __('messages.jobs_view_apply') }}
                        <span class="material-symbols-outlined text-base rtl:rotate-180">arrow_forward</span>
                    </span>
                </div>
            </div>

            @if ($job->application_deadline)
                <div class="mt-4 pt-3 border-t border-deep-green/5 flex items-center gap-1.5 text-xs text-charcoal/50">
                    <span class="material-symbols-outlined text-sm">event</span>
                    {{ __('messages.jobs_deadline') }}: {{ $job->application_deadline->translatedFormat('M j, Y') }}
                </div>
            @endif
        </a>
    @empty
        <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-deep-green/15">
            <span class="material-symbols-outlined text-deep-green/20 text-6xl mb-3">work_off</span>
            <h3 class="text-xl font-serif text-deep-green mb-2">{{ __('messages.jobs_no_results_title') }}</h3>
            <p class="text-charcoal/60 max-w-md mx-auto mb-5">{{ __('messages.jobs_no_results_desc') }}</p>
            <a href="{{ route('careers') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-deep-green text-white text-sm font-bold hover:bg-deep-green/90 transition-colors">
                <span class="material-symbols-outlined text-base">restart_alt</span>
                {{ __('messages.jobs_clear_filters') }}
            </a>
        </div>
    @endforelse
</div>

@if ($jobs->hasPages())
    <div class="mt-10 flex justify-center" id="pagination-container">
        {{ $jobs->links() }}
    </div>
@endif
