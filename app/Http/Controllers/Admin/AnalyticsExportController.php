<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\AnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The analytics report, in the two formats an NGO actually needs.
 *
 * Excel goes to whoever will re-cut the numbers — a donor's finance team, a
 * grant application. The printable report goes to whoever needs it on paper.
 *
 * Both read from {@see AnalyticsService}, the same source as the dashboard, so
 * a figure quoted in a grant report and the same figure on screen cannot drift
 * apart.
 */
class AnalyticsExportController extends Controller
{
    public function excel(Request $request, AnalyticsService $analytics): StreamedResponse
    {
        abort_unless($request->user()?->can('export_task'), 403);

        $data = $analytics->overview();

        return response()->streamDownload(function () use ($data) {
            $spreadsheet = new Spreadsheet;

            // One sheet per question, rather than one wide sheet: a reader
            // opening this wants "the performance table", not to hunt for it
            // among four unrelated blocks.
            $this->summarySheet($spreadsheet->getActiveSheet(), $data);
            $this->monthlySheet($spreadsheet->createSheet(), $data);
            $this->performanceSheet($spreadsheet->createSheet(), $data);
            $this->locationSheet($spreadsheet->createSheet(), $data);

            $spreadsheet->setActiveSheetIndex(0);

            (new Xlsx($spreadsheet))->save('php://output');

            // PhpSpreadsheet holds tens of MB of objects; without this two
            // concurrent exports can push a worker over its memory limit.
            $spreadsheet->disconnectWorksheets();
        }, 'analytics-'.now()->format('Y-m-d').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * The printable report.
     *
     * HTML that opens the browser's print dialogue, for the same reason as the
     * task report: this project carries no PDF library, and adding one should
     * be a deliberate decision rather than a side effect of an export button.
     * "Save as PDF" in that dialogue produces a real PDF.
     */
    public function pdf(Request $request, AnalyticsService $analytics): View
    {
        abort_unless($request->user()?->can('export_task'), 403);

        return view('exports.analytics-pdf', [
            'data' => $analytics->overview(),
            'generated_at' => now(),
        ]);
    }

    /* ══════════════ Sheets ══════════════ */

    private function summarySheet($sheet, array $data): void
    {
        $sheet->setTitle('Summary');

        $rows = [
            ['Metric', 'Value'],
            ['Tasks completed this month', $data['monthly']->last()['completed'] ?? 0],
            ['Tasks created this month', $data['monthly']->last()['created'] ?? 0],
            ['Average completion time (hours)', $data['completion_time']['average_hours'] ?? '—'],
            ['Median completion time (hours)', $data['completion_time']['median_hours'] ?? '—'],
            ['Average review score', $data['review_quality']['average_score'] ?? '—'],
            ['Average rating (of 5)', $data['review_quality']['average_rating'] ?? '—'],
            ['First-time approval rate (%)', $data['review_quality']['first_time_approval'] ?? '—'],
            ['Pending reviews', $data['pending_reviews']['count']],
            ['Pending over a week', $data['pending_reviews']['over_a_week']],
            ['Trees approved', $data['trees']['approved']],
            ['Trees with follow-up photo', $data['trees']['with_follow_up']],
            ['Follow-up rate (%)', $data['trees']['follow_up_rate'] ?? '—'],
        ];

        $sheet->fromArray($rows, null, 'A1');
        $this->styleHeader($sheet, 'A1:B1');
        $sheet->getColumnDimension('A')->setWidth(38);
        $sheet->getColumnDimension('B')->setWidth(18);
    }

    private function monthlySheet($sheet, array $data): void
    {
        $sheet->setTitle('Monthly');

        $rows = [['Month', 'Created', 'Completed', 'Trees planted']];
        $trees = collect($data['trees']['monthly'])->keyBy('label');

        foreach ($data['monthly'] as $month) {
            $rows[] = [
                $month['label'],
                $month['created'],
                $month['completed'],
                $trees[$month['label']]['count'] ?? 0,
            ];
        }

        $sheet->fromArray($rows, null, 'A1');
        $this->styleHeader($sheet, 'A1:D1');

        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function performanceSheet($sheet, array $data): void
    {
        $sheet->setTitle('Volunteers');

        $rows = [['Volunteer', 'Assigned', 'Completed', 'Declined', 'Completion %', 'On time %', 'Avg score', 'Avg rating', 'Performance']];

        // Both ends of the table in one sheet, deduplicated: with few
        // volunteers the top and bottom lists overlap, and printing someone
        // twice makes the report look wrong.
        $seen = [];

        foreach ([$data['top_performers'], $data['lowest_performers']] as $group) {
            foreach ($group as $person) {
                if (isset($seen[$person['id']])) {
                    continue;
                }

                $seen[$person['id']] = true;

                $rows[] = [
                    $person['name'],
                    $person['assigned'],
                    $person['completed'],
                    $person['declined'],
                    $person['completion_rate'],
                    $person['punctuality'] ?? '—',
                    $person['average_score'] ?? '—',
                    $person['average_rating'] ?? '—',
                    $person['score'],
                ];
            }
        }

        $sheet->fromArray($rows, null, 'A1');
        $this->styleHeader($sheet, 'A1:I1');

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function locationSheet($sheet, array $data): void
    {
        $sheet->setTitle('Locations');

        $rows = [['Location', 'Tasks', 'Completed', 'Completion %', 'Latitude', 'Longitude']];

        foreach ($data['hotspots'] as $spot) {
            $rows[] = [
                $spot['location'],
                $spot['tasks'],
                $spot['completed'],
                $spot['completion_rate'],
                $spot['latitude'] ?? '—',
                $spot['longitude'] ?? '—',
            ];
        }

        $sheet->fromArray($rows, null, 'A1');
        $this->styleHeader($sheet, 'A1:F1');

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    private function styleHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '064E3B']],
        ]);

        $sheet->freezePane('A2');
    }
}
