{{--
    Print-ready task report.

    Styled for paper rather than screen: landscape, repeating table header on
    every page, and no interactive chrome. `window.print()` fires on load so the
    browser's own "Save as PDF" is one keystroke away — which is what produces
    the actual PDF, since the project carries no PDF library.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>Task report — {{ $generated_at->format('Y-m-d') }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }

        * { box-sizing: border-box; }

        body {
            font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 10px;
            color: #111827;
            margin: 0;
        }

        header { border-bottom: 2px solid #064e3b; padding-bottom: 10px; margin-bottom: 14px; }
        h1 { font-size: 17px; margin: 0 0 4px; color: #064e3b; }
        .meta { color: #6b7280; font-size: 10px; }

        .summary { display: flex; gap: 10px; margin: 12px 0 16px; }
        .card {
            border: 1px solid #e5e7eb; border-radius: 6px;
            padding: 8px 14px; min-width: 110px;
        }
        .card .value { font-size: 18px; font-weight: 700; color: #064e3b; }
        .card .label { font-size: 9px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }

        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }   /* repeat the header on every printed page */
        tr { page-break-inside: avoid; }
        th {
            background: #064e3b; color: #fff; text-align: left;
            padding: 6px 5px; font-size: 9px; text-transform: uppercase; letter-spacing: .03em;
        }
        td { padding: 5px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tr.overdue { background: #fef2f2 !important; }
        tr.overdue td:first-child { border-left: 3px solid #dc2626; }

        .notice {
            margin-top: 12px; padding: 8px 10px; border-radius: 6px;
            background: #fffbeb; border: 1px solid #fde68a; color: #92400e; font-size: 10px;
        }

        footer { margin-top: 16px; color: #9ca3af; font-size: 9px; text-align: center; }

        .no-print { margin-bottom: 14px; }
        .no-print button {
            background: #064e3b; color: #fff; border: 0; border-radius: 6px;
            padding: 8px 16px; font-size: 12px; cursor: pointer;
        }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    {{-- Visible on screen only, so the report can still be printed manually if
         the automatic dialogue is blocked by the browser. --}}
    <div class="no-print">
        <button onclick="window.print()">Save as PDF / Print</button>
    </div>

    <header>
        <h1>{{ config('app.name') }} — Task Report</h1>
        <div class="meta">
            Generated {{ $generated_at->format('d M Y, H:i') }}
            @if($filter_summary) · Filters: {{ $filter_summary }} @endif
        </div>
    </header>

    <div class="summary">
        <div class="card">
            <div class="value">{{ number_format($summary['total']) }}</div>
            <div class="label">Tasks</div>
        </div>
        <div class="card">
            <div class="value">{{ number_format($summary['completed']) }}</div>
            <div class="label">Completed</div>
        </div>
        <div class="card">
            <div class="value">{{ number_format($summary['overdue']) }}</div>
            <div class="label">Overdue</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($columns as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr class="{{ $row['overdue'] === 'Yes' ? 'overdue' : '' }}">
                    @foreach($columns as $key => $heading)
                        <td>{{ $row[$key] }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" style="text-align:center; padding:20px; color:#6b7280;">
                        No tasks matched these filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($truncated)
        <div class="notice">
            This report was capped at {{ number_format($max_rows) }} rows. Narrow the filters to export the rest.
        </div>
    @endif

    <footer>{{ config('app.name') }} · Task management report · Page <span class="page"></span></footer>

    <script>
        // Deferred a tick so the table has laid out before the dialogue opens —
        // printing mid-layout produces a first page with a clipped header.
        window.addEventListener('load', () => setTimeout(() => window.print(), 300));
    </script>
</body>
</html>
