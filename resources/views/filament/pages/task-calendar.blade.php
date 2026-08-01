{{--
    Month grid of task deadlines.

    Styling note — why this file carries its own CSS instead of Tailwind classes
    ---------------------------------------------------------------------------
    Filament ships a *precompiled* stylesheet containing only the utilities its
    own components use. This panel registers no custom theme (no ->viteTheme()),
    so nothing rebuilds Tailwind against these views: `grid-cols-7`, `min-h-28`
    and friends do not exist in public/css/filament/filament/app.css, and a page
    written in them renders as a bare stack of divs — which is exactly how this
    one looked.

    Rather than add a theme build step — which would also need `npm run build`
    and a working public/build manifest, neither of which this install currently
    has — the calendar carries scoped CSS of its own. Every colour comes from
    Filament's published custom properties (--primary-*, --gray-*, --danger-*,
    --success-*), so the grid follows the panel's amber primary and both light
    and dark themes without hard-coding either.
--}}
<x-filament-panels::page>

    <style>
        /* Everything is scoped to .tc- so none of it can leak into Filament's
           own chrome. */

        .tc-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .tc-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tc-month {
            margin-inline-start: 0.25rem;
            font-size: 1.125rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: var(--gray-950);
        }

        .dark .tc-month { color: #fff; }

        .tc-select {
            appearance: none;
            border: 1px solid var(--gray-300);
            background-color: #fff;
            color: var(--gray-950);
            border-radius: 0.5rem;
            padding: 0.45rem 2rem 0.45rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.05);
            /* The chevron is drawn in, because the browser default is invisible
               against a dark background. */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.25em;
        }

        .dark .tc-select {
            border-color: var(--gray-700);
            background-color: var(--gray-900);
            color: #fff;
        }

        /* ── Summary ──────────────────────────────────────────────────── */

        .tc-summary {
            display: grid;
            /* auto-fit rather than a fixed three columns: on a narrow window the
               cards wrap instead of squeezing into unreadable slivers. */
            grid-template-columns: repeat(auto-fit, minmax(11rem, 1fr));
            gap: 0.75rem;
        }

        .tc-card {
            border: 1px solid var(--gray-200);
            background-color: #fff;
            border-radius: 0.75rem;
            padding: 1rem;
        }

        .dark .tc-card {
            border-color: rgb(255 255 255 / 0.1);
            background-color: var(--gray-900);
        }

        .tc-card-value {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1.1;
            color: var(--gray-950);
        }

        .dark .tc-card-value { color: #fff; }

        .tc-card-value--success { color: var(--success-600); }
        .dark .tc-card-value--success { color: var(--success-400); }
        .tc-card-value--danger { color: var(--danger-600); }
        .dark .tc-card-value--danger { color: var(--danger-400); }

        .tc-card-label {
            margin-top: 0.15rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-500);
        }

        .dark .tc-card-label { color: var(--gray-400); }

        /* ── Grid ─────────────────────────────────────────────────────── */

        .tc-grid {
            overflow: hidden;
            border: 1px solid var(--gray-200);
            background-color: #fff;
            border-radius: 0.75rem;
        }

        .dark .tc-grid {
            border-color: rgb(255 255 255 / 0.1);
            background-color: var(--gray-900);
        }

        .tc-week {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .tc-head {
            border-bottom: 1px solid var(--gray-200);
            background-color: var(--gray-50);
        }

        .dark .tc-head {
            border-color: rgb(255 255 255 / 0.1);
            background-color: rgb(255 255 255 / 0.05);
        }

        .tc-head-cell {
            padding: 0.55rem 0.25rem;
            text-align: center;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gray-500);
        }

        .dark .tc-head-cell { color: var(--gray-400); }

        .tc-body + .tc-body { border-top: 1px solid var(--gray-100); }
        .dark .tc-body + .tc-body { border-color: rgb(255 255 255 / 0.05); }

        .tc-day {
            /* A floor, not a fixed height: a day with three chips is taller than
               an empty one, and grid makes every cell in the row match it. */
            min-height: 7rem;
            padding: 0.375rem;
            border-inline-end: 1px solid var(--gray-100);
        }

        .tc-day:last-child { border-inline-end: 0; }
        .dark .tc-day { border-color: rgb(255 255 255 / 0.05); }

        .tc-day--outside { background-color: rgb(249 250 251 / 0.6); }
        .dark .tc-day--outside { background-color: rgb(255 255 255 / 0.02); }

        .tc-day--today { background-color: color-mix(in srgb, var(--primary-500) 10%, transparent); }

        .tc-date {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.5rem;
            height: 1.5rem;
            margin-bottom: 0.25rem;
            padding: 0 0.35rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .dark .tc-date { color: var(--gray-300); }

        .tc-date--outside { color: var(--gray-400); font-weight: 500; }
        .dark .tc-date--outside { color: var(--gray-600); }

        /* Today reads as a filled pill — the one thing the eye should find
           first on opening the page. */
        .tc-date--today {
            background-color: var(--primary-600);
            color: #fff;
            font-weight: 700;
        }

        /* ── Task chips ───────────────────────────────────────────────── */

        .tc-task {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            margin-bottom: 0.2rem;
            padding: 0.2rem 0.35rem;
            border-radius: 0.375rem;
            font-size: 0.7rem;
            line-height: 1.3;
            text-decoration: none;
            transition: opacity 0.15s ease, transform 0.15s ease;
        }

        .tc-task:hover { opacity: 0.85; transform: translateY(-1px); }

        .tc-task-title {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tc-dot {
            flex: none;
            width: 0.4rem;
            height: 0.4rem;
            border-radius: 999px;
        }

        .tc-task--overdue {
            background-color: var(--danger-100);
            color: var(--danger-700);
        }

        .dark .tc-task--overdue {
            background-color: color-mix(in srgb, var(--danger-500) 20%, transparent);
            color: var(--danger-300);
        }

        .tc-task--done {
            background-color: var(--success-100);
            color: var(--success-700);
        }

        .dark .tc-task--done {
            background-color: color-mix(in srgb, var(--success-500) 20%, transparent);
            color: var(--success-300);
        }

        .tc-task--open {
            background-color: var(--gray-100);
            color: var(--gray-700);
        }

        .dark .tc-task--open {
            background-color: rgb(255 255 255 / 0.1);
            color: var(--gray-300);
        }

        .tc-more {
            padding-inline-start: 0.35rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--gray-500);
        }

        .dark .tc-more { color: var(--gray-400); }

        /* ── Legend ───────────────────────────────────────────────────── */

        .tc-legend {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem 1rem;
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .dark .tc-legend { color: var(--gray-400); }

        .tc-legend-title { font-weight: 700; }
        .tc-legend-item { display: flex; align-items: center; gap: 0.35rem; }

        .tc-legend-swatch {
            display: inline-block;
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 999px;
        }

        .tc-legend-chip {
            border-radius: 0.375rem;
            padding: 0.1rem 0.4rem;
            font-weight: 600;
        }

        /* On a phone seven columns cannot each hold a chip, so the grid scrolls
           sideways as one piece rather than crushing every cell. */
        @media (max-width: 48rem) {
            .tc-scroll { overflow-x: auto; }
            .tc-grid { min-width: 44rem; }
        }
    </style>

    {{-- ── Controls ─────────────────────────────────────────────────────── --}}
    <div class="tc-toolbar">
        <div class="tc-nav">
            <x-filament::button wire:click="previousMonth" color="gray" icon="heroicon-m-chevron-left" size="sm">
                Prev
            </x-filament::button>

            <x-filament::button wire:click="today" color="gray" size="sm">Today</x-filament::button>

            <x-filament::button wire:click="nextMonth" color="gray" icon="heroicon-m-chevron-right" size="sm">
                Next
            </x-filament::button>

            <h2 class="tc-month">{{ $this->current()->format('F Y') }}</h2>
        </div>

        <select wire:model.live="statusFilter" class="tc-select">
            @foreach ($this->statusOptions() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── Month summary ────────────────────────────────────────────────── --}}
    @php($summary = $this->monthSummary())
    <div class="tc-summary">
        @foreach ([
            ['label' => 'Due this month', 'value' => $summary['total'], 'modifier' => ''],
            ['label' => 'Completed', 'value' => $summary['completed'], 'modifier' => ' tc-card-value--success'],
            ['label' => 'Overdue', 'value' => $summary['overdue'], 'modifier' => ' tc-card-value--danger'],
        ] as $card)
            <div class="tc-card">
                <div class="tc-card-value{{ $card['modifier'] }}">{{ number_format($card['value']) }}</div>
                <div class="tc-card-label">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Grid ─────────────────────────────────────────────────────────── --}}
    <div class="tc-scroll">
        <div class="tc-grid">

            <div class="tc-week tc-head">
                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <div class="tc-head-cell">{{ $day }}</div>
                @endforeach
            </div>

            @foreach ($this->weeks() as $week)
                <div class="tc-week tc-body">
                    @foreach ($week as $day)
                        <div @class([
                            'tc-day',
                            'tc-day--outside' => ! $day['in_month'],
                            'tc-day--today' => $day['is_today'],
                        ])>
                            <div @class([
                                'tc-date',
                                'tc-date--outside' => ! $day['in_month'],
                                'tc-date--today' => $day['is_today'],
                            ])>
                                {{ $day['date']->day }}
                            </div>

                            {{-- Three shown, the rest counted. A day with twelve
                                 deadlines must not stretch the row to a screenful. --}}
                            @foreach ($day['tasks']->take(3) as $task)
                                <a
                                    href="{{ route('filament.admin.resources.tasks.edit', ['record' => $task->id]) }}"
                                    @class([
                                        'tc-task',
                                        'tc-task--overdue' => $task->is_overdue,
                                        'tc-task--done' => ! $task->is_overdue && $task->status === \App\Enums\TaskStatus::APPROVED,
                                        'tc-task--open' => ! $task->is_overdue && $task->status !== \App\Enums\TaskStatus::APPROVED,
                                    ])
                                    title="{{ $task->reference }} — {{ $task->title }} ({{ $task->status->label() }}, {{ $task->priority->label() }})"
                                >
                                    <span class="tc-dot" style="background: {{ $task->priority->color() }}"></span>
                                    <span class="tc-task-title">{{ $task->title }}</span>
                                </a>
                            @endforeach

                            @if ($day['tasks']->count() > 3)
                                <div class="tc-more">+{{ $day['tasks']->count() - 3 }} more</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Legend ───────────────────────────────────────────────────────── --}}
    <div class="tc-legend">
        <span class="tc-legend-title">Priority:</span>
        @foreach (\App\Enums\TaskPriority::cases() as $priority)
            <span class="tc-legend-item">
                <span class="tc-legend-swatch" style="background: {{ $priority->color() }}"></span>
                {{ $priority->label() }}
            </span>
        @endforeach

        <span class="tc-legend-title" style="margin-inline-start: 0.5rem;">State:</span>
        <span class="tc-legend-chip tc-task--overdue">Overdue</span>
        <span class="tc-legend-chip tc-task--done">Completed</span>
        <span class="tc-legend-chip tc-task--open">Open</span>
    </div>

</x-filament-panels::page>
