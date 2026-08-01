{{--
    Analytics.

    Ordered by what a coordinator asks first: is anything stuck (pending
    reviews), is the programme keeping up (monthly), who needs help (performers),
    and when/where does work happen (heat maps).

    Styling note: this page ships its own scoped stylesheet (the `.pd` block
    below) rather than relying on Tailwind utilities. Filament's compiled CSS
    only contains the utilities Filament itself uses, so arbitrary classes like
    `lg:grid-cols-4` or `min-w-[44px]` in a custom view never generate — which is
    why the page previously rendered as an unstyled stack. Plain scoped CSS
    always renders, in light and dark, in print, and without a theme build.
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
            'tone' => $pending['over_a_week'] > 0 ? 'warning' : 'gray',
        ],
    ];
@endphp

<x-filament-panels::page>
    <div class="pd">

        {{-- ══════════════ Headline figures ══════════════ --}}
        <div class="pd-kpis">
            @foreach ($tiles as $tile)
                <div class="pd-kpi pd-tone-{{ $tile['tone'] }}">
                    <span class="pd-kpi-accent"></span>
                    <p class="pd-kpi-label">{{ $tile['label'] }}</p>
                    <p class="pd-kpi-value">{{ $tile['value'] }}</p>
                    <p class="pd-kpi-note">{{ $tile['note'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ══════════════ Monthly throughput ══════════════ --}}
        <section class="pd-card">
            <header class="pd-card-head">
                <x-filament::icon icon="heroicon-o-chart-bar" class="pd-head-icon" />
                <h3 class="pd-card-title">Monthly completed tasks</h3>
                <div class="pd-legend">
                    <span><i class="pd-swatch pd-swatch-muted"></i> Created</span>
                    <span><i class="pd-swatch pd-swatch-primary"></i> Completed</span>
                </div>
            </header>

            <div class="pd-bars">
                @foreach ($monthly as $month)
                    <div class="pd-bar-col">
                        <div class="pd-bar-pair">
                            <div class="pd-bar pd-bar-muted"
                                 style="height: {{ max(2, ($month['created'] / $peakMonth) * 100) }}%"
                                 title="{{ $month['created'] }} created"></div>
                            <div class="pd-bar pd-bar-primary"
                                 style="height: {{ max(2, ($month['completed'] / $peakMonth) * 100) }}%"
                                 title="{{ $month['completed'] }} completed"></div>
                        </div>
                        <span class="pd-bar-value">{{ $month['completed'] }}</span>
                        <span class="pd-bar-label">{{ $month['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ══════════════ Performers ══════════════ --}}
        <div class="pd-grid-2">
            @foreach ([
                ['title' => 'Top performing volunteers', 'icon' => 'heroicon-o-trophy', 'rows' => $data['top_performers']],
                ['title' => 'Needs support', 'icon' => 'heroicon-o-lifebuoy', 'rows' => $data['lowest_performers']],
            ] as $panel)
                <section class="pd-card">
                    <header class="pd-card-head">
                        <x-filament::icon :icon="$panel['icon']" class="pd-head-icon" />
                        <h3 class="pd-card-title">{{ $panel['title'] }}</h3>
                    </header>

                    @if ($panel['rows']->isEmpty())
                        <p class="pd-empty">Not enough completed work yet to rank anyone.</p>
                    @else
                        <ul class="pd-list">
                            @foreach ($panel['rows'] as $index => $person)
                                <li class="pd-person">
                                    <span class="pd-rank">{{ $index + 1 }}</span>
                                    <div class="pd-person-body">
                                        <p class="pd-person-name">{{ $person['name'] }}</p>
                                        <p class="pd-person-meta">
                                            {{ $person['completed'] }}/{{ $person['assigned'] }} completed
                                            @if ($person['punctuality'] !== null) · {{ $person['punctuality'] }}% on time @endif
                                            @if ($person['average_score'] !== null) · {{ $person['average_score'] }}/100 @endif
                                        </p>
                                    </div>
                                    <span @class([
                                        'pd-score',
                                        'pd-score-good' => $person['score'] >= 70,
                                        'pd-score-mid' => $person['score'] >= 40 && $person['score'] < 70,
                                        'pd-score-low' => $person['score'] < 40,
                                    ])>{{ $person['score'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach
        </div>

        {{-- ══════════════ Review quality ══════════════ --}}
        <section class="pd-card">
            <header class="pd-card-head">
                <x-filament::icon icon="heroicon-o-clipboard-document-check" class="pd-head-icon" />
                <h3 class="pd-card-title">Review outcomes</h3>
            </header>

            <div class="pd-grid-2 pd-tight">
                <div>
                    <p class="pd-sub">Score distribution</p>
                    @foreach ($quality['distribution'] as $bucket => $count)
                        @php($share = $quality['total'] > 0 ? ($count / max(1, array_sum($quality['distribution']))) * 100 : 0)
                        <div class="pd-dist-row">
                            <span class="pd-dist-label">{{ $bucket }}</span>
                            <div class="pd-track"><div class="pd-track-fill pd-fill-primary" style="width: {{ $share }}%"></div></div>
                            <span class="pd-dist-count">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="pd-outcomes">
                    @foreach ([
                        ['Approved', $quality['approved'], 'good'],
                        ['Needs revision', $quality['needs_revision'], 'mid'],
                        ['Rejected', $quality['rejected'], 'low'],
                    ] as [$label, $count, $tone])
                        <div class="pd-outcome-row">
                            <span class="pd-outcome-label">{{ $label }}</span>
                            <span class="pd-outcome-value pd-text-{{ $tone }}">{{ number_format($count) }}</span>
                        </div>
                    @endforeach

                    @if ($quality['average_rating'])
                        <div class="pd-outcome-row pd-outcome-total">
                            <span class="pd-outcome-label">Average rating</span>
                            <span class="pd-outcome-value">{{ $quality['average_rating'] }} / 5</span>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- ══════════════ Trees ══════════════ --}}
        <section class="pd-card">
            <header class="pd-card-head">
                <x-filament::icon icon="heroicon-o-sparkles" class="pd-head-icon" />
                <h3 class="pd-card-title">Tree plantation</h3>
            </header>

            <div class="pd-mini-stats">
                @foreach ([
                    ['Approved trees', number_format($trees['approved'])],
                    ['Awaiting review', number_format($trees['pending'])],
                    ['With follow-up', number_format($trees['with_follow_up'])],
                    ['Follow-up rate', $trees['follow_up_rate'] !== null ? $trees['follow_up_rate'] . '%' : '—'],
                ] as [$label, $value])
                    <div class="pd-mini">
                        <p class="pd-mini-value">{{ $value }}</p>
                        <p class="pd-mini-label">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <div class="pd-bars pd-bars-sm">
                @foreach ($trees['monthly'] as $month)
                    <div class="pd-bar-col">
                        <div class="pd-bar-pair">
                            <div class="pd-bar pd-bar-success"
                                 style="height: {{ max(2, ($month['count'] / $peakTree) * 100) }}%"
                                 title="{{ $month['count'] }} trees"></div>
                        </div>
                        <span class="pd-bar-label">{{ $month['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ══════════════ Heat maps ══════════════ --}}
        <div class="pd-grid-2">

            <section class="pd-card">
                <header class="pd-card-head">
                    <x-filament::icon icon="heroicon-o-clock" class="pd-head-icon" />
                    <h3 class="pd-card-title">When work happens</h3>
                </header>
                <p class="pd-note">
                    Submissions over the last 90 days, by day and hour. A programme whose
                    work all happens before 10:00 should not be sending reminders at 14:00.
                </p>

                <div class="pd-scroll">
                    <table class="pd-heat">
                        <tbody>
                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayIndex => $dayLabel)
                                <tr>
                                    <td class="pd-heat-day">{{ $dayLabel }}</td>
                                    @foreach (range(0, 23) as $hour)
                                        @php($count = $heat['grid'][$dayIndex][$hour] ?? 0)
                                        @php($level = $this->intensity($count, $heat['peak']))
                                        <td class="pd-heat-cell pd-heat-{{ $level }}"
                                            title="{{ $dayLabel }} {{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00 — {{ $count }} submissions"></td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                @foreach (range(0, 23) as $hour)
                                    <td class="pd-heat-hour">{{ $hour % 6 === 0 ? $hour : '' }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pd-heat-legend">
                    <span>Less</span>
                    <i class="pd-heat-cell pd-heat-0"></i>
                    <i class="pd-heat-cell pd-heat-1"></i>
                    <i class="pd-heat-cell pd-heat-2"></i>
                    <i class="pd-heat-cell pd-heat-3"></i>
                    <i class="pd-heat-cell pd-heat-4"></i>
                    <span>More</span>
                </div>
            </section>

            <section class="pd-card">
                <header class="pd-card-head">
                    <x-filament::icon icon="heroicon-o-map-pin" class="pd-head-icon" />
                    <h3 class="pd-card-title">Where work happens</h3>
                </header>

                @if ($data['hotspots']->isEmpty())
                    <p class="pd-empty">No tasks carry a named location yet.</p>
                @else
                    <ul class="pd-list">
                        @foreach ($data['hotspots'] as $spot)
                            <li class="pd-spot">
                                <div class="pd-spot-head">
                                    <span class="pd-spot-name">{{ $spot['location'] }}</span>
                                    <span class="pd-spot-meta">{{ $spot['tasks'] }} tasks · {{ $spot['completion_rate'] }}% done</span>
                                </div>
                                <div class="pd-track">
                                    <div class="pd-track-fill pd-fill-primary"
                                         style="width: {{ ($spot['tasks'] / max(1, $data['hotspots']->max('tasks'))) * 100 }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

    </div>

    {{-- Scoped design system for this page. Uses the brand greens so it reads
         as part of Every Tree for Hope, and adapts to Filament's dark mode via
         the `.dark` class Filament sets on <html>. --}}
    <style>
        .pd {
            --pd-surface: #ffffff;
            --pd-muted: #f4f7f5;
            --pd-border: #e6ece8;
            --pd-ink: #0f1f17;
            --pd-ink-soft: #5a6b62;
            --pd-faint: #8aa093;
            --pd-primary: #16a34a;
            --pd-primary-deep: #0b3a22;
            --pd-info: #0891b2;
            --pd-success: #16a34a;
            --pd-warning: #d97706;
            --pd-danger: #dc2626;
            --pd-gold: #b7791f;

            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .dark .pd {
            --pd-surface: #16211b;
            --pd-muted: #101a15;
            --pd-border: rgba(255,255,255,0.08);
            --pd-ink: #f1f5f2;
            --pd-ink-soft: #a9bcb1;
            --pd-faint: #7d938a;
        }

        /* ── KPI tiles ── */
        .pd-kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
        }
        .pd-kpi {
            position: relative;
            overflow: hidden;
            background: var(--pd-surface);
            border: 1px solid var(--pd-border);
            border-radius: 1rem;
            padding: 1.15rem 1.15rem 1.05rem;
            box-shadow: 0 1px 2px rgba(11,58,34,0.04);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .pd-kpi:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(11,58,34,0.10); }
        .pd-kpi-accent {
            position: absolute; inset: 0 auto 0 0; width: 4px;
            background: var(--pd-primary);
        }
        .pd-tone-primary .pd-kpi-accent { background: var(--pd-primary); }
        .pd-tone-info    .pd-kpi-accent { background: var(--pd-info); }
        .pd-tone-success .pd-kpi-accent { background: var(--pd-success); }
        .pd-tone-warning .pd-kpi-accent { background: var(--pd-warning); }
        .pd-tone-gray    .pd-kpi-accent { background: var(--pd-faint); }
        .pd-kpi-label {
            font-size: .7rem; font-weight: 600; letter-spacing: .06em;
            text-transform: uppercase; color: var(--pd-faint); margin: 0 0 .35rem;
        }
        .pd-kpi-value { font-size: 2rem; font-weight: 800; line-height: 1.05; margin: 0; color: var(--pd-ink); }
        .pd-tone-primary .pd-kpi-value { color: var(--pd-primary); }
        .pd-tone-info    .pd-kpi-value { color: var(--pd-info); }
        .pd-tone-success .pd-kpi-value { color: var(--pd-success); }
        .pd-tone-warning .pd-kpi-value { color: var(--pd-warning); }
        .pd-kpi-note { font-size: .78rem; color: var(--pd-ink-soft); margin: .4rem 0 0; }

        /* ── Cards ── */
        .pd-card {
            background: var(--pd-surface);
            border: 1px solid var(--pd-border);
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 1px 2px rgba(11,58,34,0.04);
        }
        .pd-card-head { display: flex; align-items: center; gap: .6rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .pd-head-icon { width: 1.25rem; height: 1.25rem; color: var(--pd-primary); }
        .pd-card-title { font-size: 1rem; font-weight: 700; color: var(--pd-ink); margin: 0; }
        .pd-legend { margin-left: auto; display: flex; gap: 1rem; font-size: .72rem; color: var(--pd-ink-soft); }
        .pd-legend span { display: inline-flex; align-items: center; gap: .35rem; }
        .pd-swatch { width: .55rem; height: .55rem; border-radius: 2px; display: inline-block; }
        .pd-swatch-primary { background: var(--pd-primary); }
        .pd-swatch-muted { background: var(--pd-faint); opacity: .5; }

        .pd-grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem; }
        .pd-tight { gap: 1.5rem; }

        /* ── Bar chart ── */
        .pd-bars { display: flex; align-items: flex-end; gap: .5rem; height: 210px; overflow-x: auto; padding-bottom: .25rem; }
        .pd-bars-sm { height: 130px; }
        .pd-bar-col { flex: 1; min-width: 42px; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: .3rem; }
        .pd-bar-pair { display: flex; align-items: flex-end; justify-content: center; gap: .25rem; width: 100%; height: 100%; }
        .pd-bar { width: 46%; border-radius: 5px 5px 0 0; min-height: 3px; transition: height .3s ease; }
        .pd-bars-sm .pd-bar { width: 68%; }
        .pd-bar-primary { background: linear-gradient(180deg, #22c55e, #16a34a); }
        .pd-bar-success { background: linear-gradient(180deg, #34d399, #16a34a); }
        .pd-bar-muted { background: var(--pd-faint); opacity: .35; }
        .pd-bar-value { font-size: .68rem; font-weight: 700; color: var(--pd-ink); }
        .pd-bar-label { font-size: .62rem; color: var(--pd-faint); white-space: nowrap; }

        /* ── Lists / performers ── */
        .pd-list { list-style: none; margin: 0; padding: 0; }
        .pd-person { display: flex; align-items: center; gap: .75rem; padding: .7rem 0; border-top: 1px solid var(--pd-border); }
        .pd-person:first-child { border-top: 0; }
        .pd-rank { width: 1.6rem; height: 1.6rem; flex-shrink: 0; display: grid; place-items: center; border-radius: 999px; background: var(--pd-muted); font-size: .72rem; font-weight: 700; color: var(--pd-faint); }
        .pd-person:nth-child(1) .pd-rank { background: rgba(183,121,31,0.15); color: var(--pd-gold); }
        .pd-person-body { min-width: 0; flex: 1; }
        .pd-person-name { font-size: .85rem; font-weight: 600; color: var(--pd-ink); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pd-person-meta { font-size: .72rem; color: var(--pd-ink-soft); margin: .15rem 0 0; }
        .pd-score { flex-shrink: 0; border-radius: 999px; padding: .25rem .6rem; font-size: .72rem; font-weight: 700; }
        .pd-score-good { background: rgba(22,163,74,0.13); color: #15803d; }
        .pd-score-mid  { background: rgba(217,119,6,0.14); color: #b45309; }
        .pd-score-low  { background: rgba(220,38,38,0.13); color: #b91c1c; }
        .dark .pd-score-good { background: rgba(34,197,94,0.2); color: #86efac; }
        .dark .pd-score-mid  { background: rgba(245,158,11,0.2); color: #fcd34d; }
        .dark .pd-score-low  { background: rgba(248,113,113,0.2); color: #fca5a5; }
        .pd-empty { font-size: .85rem; color: var(--pd-ink-soft); margin: 0; }

        /* ── Distribution / outcomes ── */
        .pd-sub { font-size: .7rem; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--pd-faint); margin: 0 0 .6rem; }
        .pd-dist-row { display: flex; align-items: center; gap: .5rem; margin-bottom: .45rem; }
        .pd-dist-label { width: 4rem; flex-shrink: 0; font-size: .72rem; color: var(--pd-ink-soft); }
        .pd-dist-count { width: 2rem; flex-shrink: 0; text-align: right; font-size: .72rem; color: var(--pd-ink-soft); }
        .pd-track { flex: 1; height: .55rem; border-radius: 999px; background: var(--pd-muted); overflow: hidden; }
        .pd-track-fill { height: 100%; border-radius: 999px; }
        .pd-fill-primary { background: linear-gradient(90deg, #22c55e, #16a34a); }
        .pd-outcomes { display: flex; flex-direction: column; gap: .5rem; }
        .pd-outcome-row { display: flex; justify-content: space-between; align-items: center; font-size: .85rem; }
        .pd-outcome-label { color: var(--pd-ink-soft); }
        .pd-outcome-value { font-weight: 700; color: var(--pd-ink); }
        .pd-text-good { color: var(--pd-success); }
        .pd-text-mid { color: var(--pd-warning); }
        .pd-text-low { color: var(--pd-danger); }
        .pd-outcome-total { border-top: 1px solid var(--pd-border); padding-top: .5rem; margin-top: .1rem; }

        /* ── Mini stats ── */
        .pd-mini-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; margin-bottom: 1.15rem; }
        .pd-mini { padding: .5rem .25rem; }
        .pd-mini-value { font-size: 1.5rem; font-weight: 800; color: var(--pd-ink); margin: 0; }
        .pd-mini-label { font-size: .72rem; color: var(--pd-ink-soft); margin: .2rem 0 0; }

        /* ── Heat map ── */
        .pd-note { font-size: .74rem; color: var(--pd-ink-soft); margin: 0 0 .9rem; line-height: 1.5; }
        .pd-scroll { overflow-x: auto; }
        .pd-heat { border-collapse: separate; border-spacing: 3px; }
        .pd-heat-day { padding-right: .5rem; text-align: right; font-size: .62rem; color: var(--pd-faint); }
        .pd-heat-hour { text-align: center; font-size: .55rem; color: var(--pd-faint); }
        .pd-heat-cell { width: 15px; height: 15px; border-radius: 3px; display: inline-block; }
        td.pd-heat-cell { display: table-cell; }
        .pd-heat-0 { background: var(--pd-muted); }
        .pd-heat-1 { background: #bbf7d0; }
        .pd-heat-2 { background: #6ee7a8; }
        .pd-heat-3 { background: #22c55e; }
        .pd-heat-4 { background: #15803d; }
        .dark .pd-heat-1 { background: rgba(34,197,94,0.3); }
        .dark .pd-heat-2 { background: rgba(34,197,94,0.5); }
        .dark .pd-heat-3 { background: rgba(34,197,94,0.75); }
        .dark .pd-heat-4 { background: #22c55e; }
        .pd-heat-legend { display: flex; align-items: center; gap: .3rem; margin-top: .85rem; font-size: .65rem; color: var(--pd-faint); }
        .pd-heat-legend .pd-heat-cell { width: 13px; height: 13px; }

        /* ── Hotspots ── */
        .pd-spot { padding: .65rem 0; border-top: 1px solid var(--pd-border); }
        .pd-spot:first-child { border-top: 0; }
        .pd-spot-head { display: flex; justify-content: space-between; align-items: center; gap: .75rem; margin-bottom: .4rem; }
        .pd-spot-name { font-size: .85rem; color: var(--pd-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pd-spot-meta { flex-shrink: 0; font-size: .72rem; color: var(--pd-ink-soft); }
    </style>
</x-filament-panels::page>
