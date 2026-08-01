{{--
    Printable analytics report.

    Portrait A4, styled for paper. Reads top-down as a summary someone can hand
    to a donor: headline figures, then the month-by-month table, then the people.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>Analytics — {{ $generated_at->format('Y-m-d') }}</title>
    <style>
        @page { size: A4 portrait; margin: 14mm; }
        * { box-sizing: border-box; }
        body { font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif; font-size: 11px; color: #111827; margin: 0; }
        header { border-bottom: 2px solid #064e3b; padding-bottom: 10px; margin-bottom: 16px; }
        h1 { font-size: 18px; margin: 0 0 4px; color: #064e3b; }
        h2 { font-size: 13px; color: #064e3b; margin: 18px 0 8px; }
        .meta { color: #6b7280; font-size: 10px; }
        .tiles { display: flex; flex-wrap: wrap; gap: 8px; }
        .tile { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 12px; min-width: 120px; }
        .tile .v { font-size: 18px; font-weight: 700; color: #064e3b; }
        .tile .l { font-size: 9px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        th { background: #064e3b; color: #fff; text-align: left; padding: 5px; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px; border-bottom: 1px solid #e5e7eb; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        footer { margin-top: 18px; color: #9ca3af; font-size: 9px; text-align: center; }
        .no-print { margin-bottom: 14px; }
        .no-print button { background: #064e3b; color: #fff; border: 0; border-radius: 6px; padding: 8px 16px; font-size: 12px; cursor: pointer; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    {{-- On screen only, so the report can still be printed if the automatic
         dialogue is blocked by the browser. --}}
    <div class="no-print"><button onclick="window.print()">Save as PDF / Print</button></div>

    <header>
        <h1>{{ config('app.name') }} — Analytics Report</h1>
        <div class="meta">Generated {{ $generated_at->format('d M Y, H:i') }}</div>
    </header>

    <div class="tiles">
        <div class="tile">
            <div class="v">{{ number_format($data['monthly']->sum('completed')) }}</div>
            <div class="l">Completed (12 mo)</div>
        </div>
        <div class="tile">
            <div class="v">{{ $data['completion_time']['average_hours'] !== null ? round($data['completion_time']['average_hours'], 1) . 'h' : '—' }}</div>
            <div class="l">Avg completion</div>
        </div>
        <div class="tile">
            <div class="v">{{ $data['review_quality']['average_score'] ?? '—' }}</div>
            <div class="l">Avg review score</div>
        </div>
        <div class="tile">
            <div class="v">{{ number_format($data['pending_reviews']['count']) }}</div>
            <div class="l">Pending reviews</div>
        </div>
        <div class="tile">
            <div class="v">{{ number_format($data['trees']['approved']) }}</div>
            <div class="l">Trees approved</div>
        </div>
        <div class="tile">
            <div class="v">{{ $data['trees']['follow_up_rate'] !== null ? $data['trees']['follow_up_rate'] . '%' : '—' }}</div>
            <div class="l">Follow-up rate</div>
        </div>
    </div>

    <h2>Month by month</h2>
    <table>
        <thead>
            <tr><th>Month</th><th>Created</th><th>Completed</th><th>Trees planted</th></tr>
        </thead>
        <tbody>
            @php($trees = collect($data['trees']['monthly'])->keyBy('label'))
            @foreach ($data['monthly'] as $month)
                <tr>
                    <td>{{ $month['label'] }}</td>
                    <td>{{ $month['created'] }}</td>
                    <td>{{ $month['completed'] }}</td>
                    <td>{{ $trees[$month['label']]['count'] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Top performing volunteers</h2>
    <table>
        <thead>
            <tr><th>Volunteer</th><th>Completed</th><th>Assigned</th><th>On time</th><th>Avg score</th><th>Performance</th></tr>
        </thead>
        <tbody>
            @forelse ($data['top_performers'] as $person)
                <tr>
                    <td>{{ $person['name'] }}</td>
                    <td>{{ $person['completed'] }}</td>
                    <td>{{ $person['assigned'] }}</td>
                    <td>{{ $person['punctuality'] !== null ? $person['punctuality'] . '%' : '—' }}</td>
                    <td>{{ $person['average_score'] ?? '—' }}</td>
                    <td>{{ $person['score'] }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:14px; color:#6b7280;">Not enough completed work to rank anyone yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Busiest locations</h2>
    <table>
        <thead>
            <tr><th>Location</th><th>Tasks</th><th>Completed</th><th>Completion</th></tr>
        </thead>
        <tbody>
            @forelse ($data['hotspots'] as $spot)
                <tr>
                    <td>{{ $spot['location'] }}</td>
                    <td>{{ $spot['tasks'] }}</td>
                    <td>{{ $spot['completed'] }}</td>
                    <td>{{ $spot['completion_rate'] }}%</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; padding:14px; color:#6b7280;">No tasks carry a named location yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <footer>{{ config('app.name') }} · Analytics report</footer>

    <script>
        window.addEventListener('load', () => setTimeout(() => window.print(), 300));
    </script>
</body>
</html>
