{{--
    Month grid of task deadlines.

    Plain Blade + Tailwind, no calendar library: the requirement is "which days
    are heavy and what is late", and that needs a grid, not a scheduling engine.
    Dark-mode classes are paired throughout because the panel ships both themes.
--}}
<x-filament-panels::page>

    {{-- ── Controls ─────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <x-filament::button wire:click="previousMonth" color="gray" icon="heroicon-m-chevron-left" size="sm">
                Prev
            </x-filament::button>

            <x-filament::button wire:click="today" color="gray" size="sm">Today</x-filament::button>

            <x-filament::button wire:click="nextMonth" color="gray" icon="heroicon-m-chevron-right" size="sm">
                Next
            </x-filament::button>

            <h2 class="ms-2 text-lg font-semibold text-gray-950 dark:text-white">
                {{ $this->current()->format('F Y') }}
            </h2>
        </div>

        <select
            wire:model.live="statusFilter"
            class="rounded-lg border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        >
            @foreach ($this->statusOptions() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── Month summary ────────────────────────────────────────────────── --}}
    @php($summary = $this->monthSummary())
    <div class="grid grid-cols-3 gap-3">
        @foreach ([
            ['label' => 'Due this month', 'value' => $summary['total'], 'class' => 'text-gray-950 dark:text-white'],
            ['label' => 'Completed', 'value' => $summary['completed'], 'class' => 'text-success-600 dark:text-success-400'],
            ['label' => 'Overdue', 'value' => $summary['overdue'], 'class' => 'text-danger-600 dark:text-danger-400'],
        ] as $card)
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
                <div class="text-2xl font-bold {{ $card['class'] }}">{{ number_format($card['value']) }}</div>
                <div class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Grid ─────────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900">

        <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/5">
            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                <div class="p-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        @foreach ($this->weeks() as $week)
            <div class="grid grid-cols-7 border-b border-gray-100 last:border-b-0 dark:border-white/5">
                @foreach ($week as $day)
                    <div @class([
                        'min-h-28 border-e border-gray-100 p-1.5 last:border-e-0 dark:border-white/5',
                        'bg-gray-50/60 dark:bg-white/[0.02]' => ! $day['in_month'],
                        'bg-primary-50/50 dark:bg-primary-500/10' => $day['is_today'],
                    ])>
                        <div @class([
                            'mb-1 text-xs font-medium',
                            'text-gray-400 dark:text-gray-600' => ! $day['in_month'],
                            'text-gray-700 dark:text-gray-300' => $day['in_month'] && ! $day['is_today'],
                            'text-primary-600 dark:text-primary-400 font-bold' => $day['is_today'],
                        ])>
                            {{ $day['date']->day }}
                        </div>

                        {{-- Three shown, the rest counted. A day with twelve
                             deadlines must not stretch the row to a screenful. --}}
                        @foreach ($day['tasks']->take(3) as $task)
                            <a
                                href="{{ route('filament.admin.resources.tasks.edit', ['record' => $task->id]) }}"
                                @class([
                                    'mb-1 block truncate rounded px-1.5 py-1 text-[11px] leading-tight transition hover:opacity-80',
                                    'bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-300' => $task->is_overdue,
                                    'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-300' => ! $task->is_overdue && $task->status === \App\Enums\TaskStatus::APPROVED,
                                    'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300' => ! $task->is_overdue && $task->status !== \App\Enums\TaskStatus::APPROVED,
                                ])
                                title="{{ $task->reference }} — {{ $task->title }} ({{ $task->status->label() }}, {{ $task->priority->label() }})"
                            >
                                <span class="inline-block h-1.5 w-1.5 rounded-full align-middle" style="background: {{ $task->priority->color() }}"></span>
                                {{ $task->title }}
                            </a>
                        @endforeach

                        @if ($day['tasks']->count() > 3)
                            <div class="px-1.5 text-[11px] text-gray-500 dark:text-gray-400">
                                +{{ $day['tasks']->count() - 3 }} more
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    {{-- ── Legend ───────────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
        <span class="font-medium">Priority:</span>
        @foreach (\App\Enums\TaskPriority::cases() as $priority)
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-2 w-2 rounded-full" style="background: {{ $priority->color() }}"></span>
                {{ $priority->label() }}
            </span>
        @endforeach

        <span class="ms-4 font-medium">State:</span>
        <span class="rounded bg-danger-100 px-1.5 py-0.5 text-danger-700 dark:bg-danger-500/20 dark:text-danger-300">Overdue</span>
        <span class="rounded bg-success-100 px-1.5 py-0.5 text-success-700 dark:bg-success-500/20 dark:text-success-300">Completed</span>
    </div>

</x-filament-panels::page>
