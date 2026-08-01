<?php

namespace App\Services\Tasks\Export;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Turns a filtered task query into a spreadsheet or a printable report.
 *
 * Takes a Builder rather than a collection, so the admin table's own filters,
 * search and sort flow straight through — an export that silently ignored the
 * filters on screen would be worse than no export, because it looks right.
 *
 * Both formats share one column definition ({@see self::rows()}), so the Excel
 * file and the PDF can never disagree about what a task record contains.
 */
class TaskExporter
{
    /**
     * Hard ceiling on an export.
     *
     * A spreadsheet is built entirely in memory; without a cap, one click on a
     * table with no filters would try to hydrate every task ever created and
     * exhaust the request's memory limit. 5,000 rows is far more than anyone
     * reads and comfortably inside PHP's default 128M.
     */
    private const MAX_ROWS = 5000;

    /** @var array<string, string> column key => heading */
    private const COLUMNS = [
        'reference' => 'Reference',
        'title' => 'Title',
        'category' => 'Category',
        'status' => 'Status',
        'priority' => 'Priority',
        'assignees' => 'Assignees',
        'progress' => 'Progress %',
        'start_date' => 'Start date',
        'due_date' => 'Due date',
        'overdue' => 'Overdue',
        'estimated_hours' => 'Est. hours',
        'actual_hours' => 'Actual hours',
        'location' => 'Location',
        'created_by' => 'Created by',
        'completed_at' => 'Completed at',
    ];

    /**
     * Flatten the query into export rows.
     *
     * `chunk` rather than `get`: the cap bounds the total, but loading five
     * thousand tasks with their assignees in one hydration is still a spike
     * worth avoiding on a shared host.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function rows(Builder $query): Collection
    {
        $rows = collect();

        $query->with(['category:id,name', 'assignees.user:id,name,lastname', 'creator:id,name,lastname'])
            ->limit(self::MAX_ROWS)
            ->chunk(500, function ($tasks) use ($rows) {
                foreach ($tasks as $task) {
                    $rows->push($this->row($task));
                }
            });

        return $rows;
    }

    /** @return array<string, mixed> */
    private function row(Task $task): array
    {
        return [
            'reference' => $task->reference,
            'title' => $task->title,
            'category' => $task->category?->name ?? '—',
            'status' => $task->status->label(),
            'priority' => $task->priority->label(),
            'assignees' => $task->assignees
                ->map(fn ($a) => trim($a->user?->name.' '.($a->user?->lastname ?? '')))
                ->filter()
                ->implode(', ') ?: '—',
            'progress' => (int) $task->progress,
            'start_date' => $task->start_date?->format('Y-m-d H:i') ?? '—',
            'due_date' => $task->due_date?->format('Y-m-d H:i') ?? '—',
            'overdue' => $task->is_overdue ? 'Yes' : 'No',
            'estimated_hours' => $task->estimated_hours !== null ? (float) $task->estimated_hours : '—',
            'actual_hours' => $task->actual_hours !== null ? (float) $task->actual_hours : '—',
            'location' => $task->location_name
                ?: ($task->latitude !== null ? round((float) $task->latitude, 5).', '.round((float) $task->longitude, 5) : '—'),
            'created_by' => trim(($task->creator?->name ?? '').' '.($task->creator?->lastname ?? '')) ?: '—',
            'completed_at' => $task->completed_at?->format('Y-m-d H:i') ?? '—',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Excel
    |--------------------------------------------------------------------------
    */

    /**
     * Stream an .xlsx file.
     *
     * Streamed rather than written to disk: the file is wanted once, and a
     * temp file that nothing cleans up is a slow storage leak.
     */
    public function toExcel(Builder $query, string $filename = 'tasks'): StreamedResponse
    {
        $rows = $this->rows($query);

        return response()->streamDownload(function () use ($rows) {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Tasks');

            $headings = array_values(self::COLUMNS);
            $sheet->fromArray($headings, null, 'A1');

            $lastColumn = chr(64 + count($headings));

            // A header that stays visible and filterable is the difference
            // between a spreadsheet someone uses and one they re-sort by hand.
            $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '064E3B']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->freezePane('A2');
            $sheet->setAutoFilter("A1:{$lastColumn}".max(1, $rows->count() + 1));

            $rowNumber = 2;

            foreach ($rows as $row) {
                $sheet->fromArray(array_values($row), null, "A{$rowNumber}");

                // Overdue rows tinted red: the single most useful thing to spot
                // when scanning an exported board.
                if (($row['overdue'] ?? 'No') === 'Yes') {
                    $sheet->getStyle("A{$rowNumber}:{$lastColumn}{$rowNumber}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
                    ]);
                }

                $rowNumber++;
            }

            $sheet->getStyle("A1:{$lastColumn}".($rowNumber - 1))
                ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            foreach (range('A', $lastColumn) as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            (new Xlsx($spreadsheet))->save('php://output');

            // Frees the ~50MB of objects PhpSpreadsheet holds; without it a
            // couple of concurrent exports can push the worker over its limit.
            $spreadsheet->disconnectWorksheets();
        }, "{$filename}-".now()->format('Y-m-d').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    /**
     * Render a print-ready report.
     *
     * This returns styled HTML that opens the browser's print dialogue, not a
     * server-generated PDF — the project has no PDF library installed, and
     * adding one is a composer change that should be a deliberate decision
     * rather than a side effect of an export button.
     *
     * The result is a genuine PDF via "Save as PDF", which is what the browser
     * print dialogue produces, and it costs no dependency and no server memory.
     * To switch to true server-side rendering, install barryvdh/laravel-dompdf
     * and replace the body of this method with
     * `Pdf::loadView('exports.tasks-pdf', $data)->download()` — the view and the
     * column definitions above are already shared, so nothing else changes.
     *
     * @return array{rows: Collection, summary: array<string, int>, generated_at: \Carbon\CarbonInterface}
     */
    public function toPdfData(Builder $query): array
    {
        $rows = $this->rows($query);

        return [
            'columns' => self::COLUMNS,
            'rows' => $rows,
            'summary' => [
                'total' => $rows->count(),
                'overdue' => $rows->where('overdue', 'Yes')->count(),
                'completed' => $rows->where('status', 'Approved')->count(),
            ],
            'generated_at' => now(),
            'truncated' => $rows->count() >= self::MAX_ROWS,
        ];
    }

    public function maxRows(): int
    {
        return self::MAX_ROWS;
    }
}
