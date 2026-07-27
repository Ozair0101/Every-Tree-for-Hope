{{--
    Analytics.

    Ordered by what a coordinator asks first: is anything stuck (pending
    reviews), is the programme keeping up (monthly), who needs help (performers),
    and when/where does work happen (heat maps).
--}}
@php
    $monthly = $data['monthly'];
    $time = $data['completion_time'];
    $quality = $data['review_quality'];
    $pending = $data['pending_reviews'];
    $trees = $data['trees'];
    $heat = $data['heat_map'];

    $peakMonth = max(1, $monthly->max('completed'), $monthly->max('created'));
    $peakTree = max(1, collect($trees['monthly'])->max('count'));
@endphp

<x-filament-panels::page>

    {{-- ══════════════ Headline figures ══════════════ --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $tiles = [
                [
                    'label' => 'Completed this month',
                    'value' => number_format($monthly->last()['completed'] ?? 0),
                    'note' => 'of ' . number_format($monthly->last()['created'] ?? 0) . ' created',
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Average completion',
                    'value' => $this->readableHours($time['average_hours']),
                    // The median is shown beside the mean because one abandoned
                    // task finished months late drags an average badly.
                    'note' => $time['sample'] > 0
                        ? 'median ' . $this->readableHours($time['median_hours']) . ' · ' . $time['sample'] . ' tasks'
                        : 'no completed tasks yet',
                    'tone' => 'info',
                ],
                [
                    'label' => 'Average review score',
                    'value' => $quality['average_score'] !== null ? $quality['average_score'] . '/100' : '—',
                    'note' => $quality['first_time_approval'] !== null
                        ? $quality['first_time_approval'] . '% approved first time'
                        : 'no reviews yet',
                    'tone' => 'success',
                ],
                [
                    'label' => 'Pending reviews',
                    'value' => number_format($pending['count']),
                    'note' => $pending['over_a_week'] > 0
                        ? $pending['over_a_week'] . ' waiting over a week'
                        : ($pending['oldest_days'] !== null ? 'oldest ' . $pending['oldest_days'] . ' days' : 'queue is clear'),
                    // Amber only when the queue is actually stale. A count alone
                    // hides the difference between five waiting an hour and five
                    // waiting three weeks.
                    'tone' => $pending['over_a_week'] > 0 ? 'warning' : 'gray',
                ],
            ];
        @endphp

        @foreach ($tiles as $tile)
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-gray-900">
                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $tile['label'] }}</p>
                <p @class([
                    'mt-1 text-3xl font-bold',
                    'text-primary-600 dark:text-primary-400' => $tile['tone'] === 'primary',
                    'text-info-600 dark:text-info-400' => $tile['tone'] === 'info',
                    'text-success-600 dark:text-success-400' => $tile['tone'] === 'success',
                    'text-warning-600 dark:text-warning-400' => $tile['tone'] === 'warning',
                    'text-gray-950 dark:text-white' => $tile['tone'] === 'gray',
                ])>{{ $tile['value'] }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $tile['note'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ══════════════ Monthly throughput ══════════════ --}}
    <x-filament::section heading="Monthly completed tasks" icon="heroicon-o-chart-bar">
        {{-- CSS bars rather than a charting library: two series over twelve
             months needs no canvas, and this renders in print and in dark mode
             without a second implementation. --}}
        <div class="flex items-end gap-2 overflow-x-auto pb-2" style="height: 200px">
            @foreach ($monthly as $month)
                <div class="flex min-w-[44px] flex-1 flex-col items-center justify-end gap-1">
                    <div class="flex w-full items-end justify-center gap-1" style="height: 150px">
                        <div
                            class="w-1/2 rounded-t bg-gray-300 dark:bg-white/20"
                            style="height: {{ max(2, ($month['created'] / $peakMonth) * 100) }}%"
                            title="{{ $month['created'] }} created"
                        ></div>
                        <div
                            class="w-1/2 rounded-t bg-primary-500"
                            style="height: {{ max(2, ($month['completed'] / $peakMonth) * 100) }}%"
                            title="{{ $month['completed'] }} completed"
                        ></div>
                    </div>
                    <span class="text-[10px] font-medium text-gray-700 dark:text-gray-300">{{ $month['completed'] }}</span>
                    <span class="whitespace-nowrap text-[10px] text-gray-500 dark:text-gray-400">{{ $month['label'] }}</span>
                </div>
            @endforeach
        </div>

        <div class="mt-2 flex gap-4 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-2 w-2 rounded bg-gray-300 dark:bg-white/20"></span> Created
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-2 w-2 rounded bg-primary-500"></span> Completed
            </span>
        </div>
    </x-filament::section>

    {{-- ══════════════ Performers ══════════════ --}}
    <div class="grid gap-6 lg:grid-cols-2">
        @foreach ([
            ['title' => 'Top performing volunteers', 'icon' => 'heroicon-o-trophy', 'rows' => $data['top_performers'], 'good' => true],
            ['title' => 'Needs support', 'icon' => 'heroicon-o-lifebuoy', 'rows' => $data['lowest_performers'], 'good' => false],
        ] as $panel)
            <x-filament::section :heading="$panel['title']" :icon="$panel['icon']">
                @if ($panel['rows']->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Not enough completed work yet to rank anyone.
                    </p>
                @else
                    {{-- "Needs support", not "worst" — this is a list of people to
                         help, and a coordinator reads it that way or not at all. --}}
                    <ul class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($panel['rows'] as $index => $person)
                            <li class="flex items-center gap-3 py-2.5">
                                <span class="w-5 shrink-0 text-xs font-medium text-gray-400">{{ $index + 1 }}</span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-gray-950 dark:text-white">
                                        {{ $person['name'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $person['completed'] }}/{{ $person['assigned'] }} completed
                                        @if ($person['punctuality'] !== null) · {{ $person['punctuality'] }}% on time @endif
                                        @if ($person['average_score'] !== null) · {{ $person['average_score'] }}/100 @endif
                                    </p>
                                </div>

                                <span @class([
                                    'shrink-0 rounded-full px-2 py-1 text-xs font-semibold',
                                    'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-300' => $person['score'] >= 70,
                                    'bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-300' => $person['score'] >= 40 && $person['score'] < 70,
                                    'bg-danger-100 text-danger-700 dark:bg-danger-500/20 dark:text-danger-300' => $person['score'] < 40,
                                ])>{{ $person['score'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-filament::section>
        @endforeach
    </div>

    {{-- ══════════════ Review quality ══════════════ --}}
    <x-filament::section heading="Review outcomes" icon="heroicon-o-clipboard-document-check">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <p class="mb-2 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Score distribution</p>
                @foreach ($quality['distribution'] as $bucket => $count)
                    @php($share = $quality['total'] > 0 ? ($count / max(1, array_sum($quality['distribution']))) * 100 : 0)
                    <div class="mb-1.5 flex items-center gap-2">
                        <span class="w-16 shrink-0 text-xs text-gray-500 dark:text-gray-400">{{ $bucket }}</span>
                        <div class="h-3 flex-1 overflow-hidden rounded bg-gray-100 dark:bg-white/10">
                            <div class="h-full rounded bg-primary-500" style="width: {{ $share }}%"></div>
                        </div>
                        <span class="w-8 shrink-0 text-right text-xs text-gray-600 dark:text-gray-300">{{ $count }}</span>
                    </div>
                @endforeach
            </div>

            <div class="space-y-2 text-sm">
                @foreach ([
                    ['Approved', $quality['approved'], 'text-success-600 dark:text-success-400'],
                    ['Needs revision', $quality['needs_revision'], 'text-warning-600 dark:text-warning-400'],
                    ['Rejected', $quality['rejected'], 'text-danger-600 dark:text-danger-400'],
                ] as [$label, $count, $class])
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">{{ $label }}</span>
                        <span class="font-semibold {{ $class }}">{{ number_format($count) }}</span>
                    </div>
                @endforeach

                @if ($quality['average_rating'])
                    <div class="flex justify-between border-t border-gray-100 pt-2 dark:border-white/5">
                        <span class="text-gray-600 dark:text-gray-400">Average rating</span>
                        <span class="font-semibold text-gray-950 dark:text-white">
                            {{ $quality['average_rating'] }} / 5
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </x-filament::section>

    {{-- ══════════════ Trees ══════════════ --}}
    <x-filament::section heading="Tree plantation" icon="heroicon-o-sparkles">
        <div class="mb-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach ([
                ['Approved trees', number_format($trees['approved'])],
                ['Awaiting review', number_format($trees['pending'])],
                ['With follow-up', number_format($trees['with_follow_up'])],
                // The honest measure of a planting programme: trees in the
                // ground is an input, trees alive months later is the outcome.
                ['Follow-up rate', $trees['follow_up_rate'] !== null ? $trees['follow_up_rate'] . '%' : '—'],
            ] as [$label, $value])
                <div>
                    <p class="text-2xl font-bold text-gray-950 dark:text-white">{{ $value }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="flex items-end gap-1.5 overflow-x-auto" style="height: 110px">
            @foreach ($trees['monthly'] as $month)
                <div class="flex min-w-[38px] flex-1 flex-col items-center justify-end gap-1">
                    <div
                        class="w-full rounded-t bg-success-500"
                        style="height: {{ max(2, ($month['count'] / $peakTree) * 70) }}px"
                        title="{{ $month['count'] }} trees"
                    ></div>
                    <span class="whitespace-nowrap text-[10px] text-gray-500 dark:text-gray-400">{{ $month['label'] }}</span>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    {{-- ══════════════ Heat maps ══════════════ --}}
    <div class="grid gap-6 lg:grid-cols-2">

        <x-filament::section heading="When work happens" icon="heroicon-o-clock">
            <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">
                Submissions over the last 90 days, by day and hour. A programme whose
                work all happens before 10:00 should not be sending reminders at 14:00.
            </p>

            <div class="overflow-x-auto">
                <table class="border-separate" style="border-spacing: 2px">
                    <tbody>
                        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayIndex => $dayLabel)
                            <tr>
                                <td class="pe-2 text-right text-[10px] text-gray-500 dark:text-gray-400">{{ $dayLabel }}</td>
                                @foreach (range(0, 23) as $hour)
                                    @php($count = $heat['grid'][$dayIndex][$hour] ?? 0)
                                    @php($level = $this->intensity($count, $heat['peak']))
                                    <td
                                        @class([
                                            'h-4 w-4 rounded-sm',
                                            'bg-gray-100 dark:bg-white/5' => $level === 0,
                                            'bg-primary-200 dark:bg-primary-500/30' => $level === 1,
                                            'bg-primary-300 dark:bg-primary-500/50' => $level === 2,
                                            'bg-primary-500 dark:bg-primary-500/70' => $level === 3,
                                            'bg-primary-700 dark:bg-primary-400' => $level === 4,
                                        ])
                                        title="{{ $dayLabel }} {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00 — {{ $count }} submissions"
                                    ></td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            @foreach (range(0, 23) as $hour)
                                <td class="text-center text-[8px] text-gray-400">
                                    {{ $hour % 6 === 0 ? $hour : '' }}
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section heading="Where work happens" icon="heroicon-o-map-pin">
            @if ($data['hotspots']->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No tasks carry a named location yet.</p>
            @else
                {{-- Ranked places rather than a density layer: the panel has no
                     mapping key, and a place a coordinator can name is more
                     actionable than a cluster centroid anyway. --}}
                <ul class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach ($data['hotspots'] as $spot)
                        <li class="py-2">
                            <div class="flex items-center justify-between gap-3">
                                <span class="truncate text-sm text-gray-950 dark:text-white">{{ $spot['location'] }}</span>
                                <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $spot['tasks'] }} tasks · {{ $spot['completion_rate'] }}% done
                                </span>
                            </div>
                            <div class="mt-1 h-2 overflow-hidden rounded bg-gray-100 dark:bg-white/10">
                                <div
                                    class="h-full rounded bg-primary-500"
                                    style="width: {{ ($spot['tasks'] / max(1, $data['hotspots']->max('tasks'))) * 100 }}%"
                                ></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-filament::section>
    </div>

</x-filament-panels::page>
