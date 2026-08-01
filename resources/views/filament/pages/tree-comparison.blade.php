{{--
    Before and after, side by side.

    Two photographs per card, equal width, sharing a caption bar. Equal width
    matters: an eye judges growth by comparing the sapling against the frame,
    and unequal panes make a tree look bigger simply because its photo is.
--}}
@php($counts = $this->counts())

<x-filament-panels::page>

    {{-- ── Filter ───────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex gap-2">
            @foreach ([
                'compared' => 'With follow-up',
                'awaiting' => 'Awaiting follow-up',
            ] as $key => $label)
                <button
                    type="button"
                    wire:click="$set('filter', '{{ $key }}')"
                    @class([
                        'rounded-lg px-3 py-2 text-sm font-medium transition',
                        'bg-primary-600 text-white' => $filter === $key,
                        'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-white/10 dark:text-gray-300 dark:hover:bg-white/20' => $filter !== $key,
                    ])
                >
                    {{ $label }}
                    <span class="ms-1 opacity-75">{{ number_format($counts[$key]) }}</span>
                </button>
            @endforeach
        </div>

        <input
            type="search"
            wire:model.live.debounce.400ms="search"
            placeholder="Species, place or planter"
            class="w-full rounded-lg border-gray-300 text-sm shadow-sm sm:w-72 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        >
    </div>

    {{-- ── Cards ────────────────────────────────────────────────────── --}}
    @php($trees = $this->trees())

    @if ($trees->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-300 p-10 text-center dark:border-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $filter === 'compared'
                    ? 'No tree has a follow-up photograph yet.'
                    : 'Every approved tree already has its follow-up.' }}
            </p>
        </div>
    @else
        <div class="grid gap-5 lg:grid-cols-2">
            @foreach ($trees as $tree)
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">

                    {{-- Heading --}}
                    <div class="flex items-start justify-between gap-3 border-b border-gray-100 px-4 py-3 dark:border-white/5">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-gray-950 dark:text-white">{{ $tree->species }}</p>
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ $tree->user?->name ?? 'Unknown planter' }}
                                @if ($tree->location_name) · {{ $tree->location_name }} @endif
                            </p>
                        </div>

                        @if ($tree->hasComparison() && $tree->growthDays() !== null)
                            <span class="shrink-0 rounded-full bg-success-100 px-2 py-1 text-xs font-medium text-success-700 dark:bg-success-500/20 dark:text-success-300">
                                {{-- Months read better than days once past a season. --}}
                                {{ $tree->growthDays() >= 60
                                    ? round($tree->growthDays() / 30) . ' months of growth'
                                    : $tree->growthDays() . ' days of growth' }}
                            </span>
                        @endif
                    </div>

                    {{-- The pair --}}
                    <div class="grid grid-cols-2">
                        @foreach ([
                            ['label' => 'Before', 'url' => $tree->image_url, 'thumb' => $tree->image_thumbnail_path, 'date' => $tree->planted_on],
                            ['label' => 'After', 'url' => $tree->after_image_url, 'thumb' => $tree->after_image_thumbnail_path, 'date' => $tree->after_image_taken_at],
                        ] as $side)
                            <div class="relative border-e border-gray-100 last:border-e-0 dark:border-white/5">
                                @if ($side['url'])
                                    {{-- Full image on click; the grid itself uses the
                                         compressed copy, which is what makes a page of
                                         twelve photographs load at all. --}}
                                    <a href="{{ $side['url'] }}" target="_blank" rel="noopener">
                                        <img
                                            src="{{ $side['url'] }}"
                                            alt="{{ $side['label'] }} — {{ $tree->species }}"
                                            loading="lazy"
                                            class="aspect-square w-full object-cover"
                                        >
                                    </a>
                                @else
                                    <div class="flex aspect-square w-full items-center justify-center bg-gray-50 dark:bg-white/5">
                                        <span class="text-xs text-gray-400 dark:text-gray-600">
                                            No {{ strtolower($side['label']) }} photo
                                        </span>
                                    </div>
                                @endif

                                <span class="absolute left-2 top-2 rounded bg-black/60 px-2 py-0.5 text-xs font-medium text-white">
                                    {{ $side['label'] }}
                                </span>

                                @if ($side['date'])
                                    <span class="absolute bottom-2 left-2 rounded bg-black/60 px-2 py-0.5 text-[11px] text-white">
                                        {{ $side['date']->format('M Y') }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Verification strip --}}
                    <div class="space-y-1 px-4 py-3 text-xs">
                        @if ($tree->after_image_note)
                            <p class="text-gray-700 dark:text-gray-300">{{ $tree->after_image_note }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-gray-500 dark:text-gray-400">
                            @if ($label = $this->distanceLabel($tree))
                                {{-- The one check that matters on a comparison: a
                                     follow-up shot somewhere else entirely. Flagged,
                                     not accused — a photographer steps back, and GPS
                                     drifts under a canopy. --}}
                                <span @class([
                                    'font-medium text-warning-700 dark:text-warning-400' => $this->looksDisplaced($tree),
                                ])>
                                    @if ($this->looksDisplaced($tree)) ⚠ @endif
                                    {{ $label }}
                                </span>
                            @endif

                            @if ($tree->after_image_device)
                                <span>{{ $tree->after_image_device }}</span>
                            @endif

                            @if ($tree->after_image_taken_at)
                                <span>Taken {{ $tree->after_image_taken_at->format('d M Y, H:i') }}</span>
                            @endif
                        </div>

                        @if ($filter === 'awaiting')
                            <p class="text-gray-500 dark:text-gray-400">
                                Planted {{ $tree->planted_on?->diffForHumans() }} — no follow-up yet.
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5">
            {{ $trees->links() }}
        </div>
    @endif

</x-filament-panels::page>
