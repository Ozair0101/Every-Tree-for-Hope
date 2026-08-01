<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\Tasks\Export\TaskExporter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Printable task report for the admin panel.
 *
 * A web route rather than a Filament action because it has to open in a new tab
 * — the browser's print dialogue is what produces the PDF, and a Livewire action
 * cannot navigate the user somewhere printable.
 *
 * The filters arrive as query parameters and are re-applied here rather than
 * shared with the Livewire component's state. That is a small duplication, but
 * it makes the report a plain linkable URL: a coordinator can bookmark
 * "overdue, critical" and get the same report every week.
 */
class TaskExportController extends Controller
{
    public function pdf(Request $request, TaskExporter $exporter): View
    {
        abort_unless($request->user()?->can('export_task'), 403);

        $query = $this->filtered($request);

        return view('exports.tasks-pdf', $exporter->toPdfData($query) + [
            'max_rows' => $exporter->maxRows(),
            'filter_summary' => $this->describe($request),
        ]);
    }

    private function filtered(Request $request): Builder
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'array'],
            'status.*' => ['string'],
            'priority' => ['sometimes', 'array'],
            'priority.*' => ['string'],
            'overdue' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'string', 'max:120'],
            'from' => ['sometimes', 'date'],
            'until' => ['sometimes', 'date'],
        ]);

        return Task::query()
            // Only values that exist as enum cases reach the query — the same
            // rule the API repository applies, for the same reason.
            ->when($validated['status'] ?? null, fn (Builder $q, array $statuses) => $q->whereIn(
                'status',
                array_filter($statuses, fn ($s) => TaskStatus::tryFrom($s) !== null),
            ))
            ->when($validated['priority'] ?? null, fn (Builder $q, array $priorities) => $q->whereIn(
                'priority',
                array_filter($priorities, fn ($p) => TaskPriority::tryFrom($p) !== null),
            ))
            ->when($request->boolean('overdue'), fn (Builder $q) => $q->overdue())
            ->when($validated['from'] ?? null, fn (Builder $q, $d) => $q->whereDate('due_date', '>=', $d))
            ->when($validated['until'] ?? null, fn (Builder $q, $d) => $q->whereDate('due_date', '<=', $d))
            ->when($validated['search'] ?? null, function (Builder $q, string $term) {
                $escaped = str_replace(['%', '_'], ['\%', '\_'], $term);

                $q->where(fn (Builder $inner) => $inner
                    ->where('reference', $escaped)
                    ->orWhere('title', 'like', "%{$escaped}%")
                    ->orWhere('location_name', 'like', "%{$escaped}%"));
            })
            ->orderBy('due_date');
    }

    /** A human sentence describing the filters, printed in the report header. */
    private function describe(Request $request): ?string
    {
        $parts = [];

        if ($statuses = $request->query('status')) {
            $parts[] = 'status '.implode('/', (array) $statuses);
        }

        if ($priorities = $request->query('priority')) {
            $parts[] = 'priority '.implode('/', (array) $priorities);
        }

        if ($request->boolean('overdue')) {
            $parts[] = 'overdue only';
        }

        if ($search = $request->query('search')) {
            $parts[] = "search \"{$search}\"";
        }

        return $parts === [] ? null : implode(', ', $parts);
    }
}
