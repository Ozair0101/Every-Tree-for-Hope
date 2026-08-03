<?php

namespace App\Http\Controllers\Api;

use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Financial transparency — the data behind the site's /report page, which
 * is a static Blade view with no controller of its own.
 *
 * GET  /api/report — expenses, paginated, with totals per type
 * POST /api/report — record a new expense (create_expense permission)
 */
class ReportController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $expenses = Expense::ordered()->paginate($this->perPage($request, 20));

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $expenses->getCollection()->map(fn (Expense $expense) => $this->present($expense))->all(),
            'summary' => [
                'total_spent' => (float) Expense::sum('total_cost'),
                'by_type' => Expense::query()
                    ->selectRaw('expense_type, SUM(total_cost) as total')
                    ->groupBy('expense_type')
                    ->pluck('total', 'expense_type')
                    ->map(fn ($total) => (float) $total)
                    ->all(),
            ],
            'meta' => $this->paginationMeta($expenses),
        ]);
    }

    /**
     * Record a new expense from the mobile app.
     *
     * Guarded by the same `create_expense` permission the admin panel enforces —
     * resolved through {@see \App\Policies\ExpensePolicy} so a role configured in
     * Filament grants (or denies) this automatically.
     *
     * `total_cost` is the amount that lands on the transparency page. When it is
     * omitted but a numeric quantity and unit price are given, it is derived so a
     * quick entry ("5 saplings @ 120") still totals correctly.
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('create', Expense::class), 403);

        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'expense_type' => 'nullable|string|max:120',
            'quantity' => 'nullable|string|max:120',
            'unit_price' => 'nullable|numeric|min:0|max:9999999999',
            'total_cost' => 'nullable|numeric|min:0|max:9999999999',
            'who_paid' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
        ]);

        $unitPrice = $validated['unit_price'] ?? 0;
        $totalCost = $validated['total_cost']
            ?? (is_numeric($validated['quantity'] ?? null)
                ? (float) $validated['quantity'] * (float) $unitPrice
                : $unitPrice);

        $expense = Expense::create([
            'date' => $validated['date'],
            'description' => $validated['description'],
            'expense_type' => $validated['expense_type'] ?? null,
            'quantity' => $validated['quantity'] ?? null,
            'unit_price' => $unitPrice,
            'total_cost' => $totalCost,
            'who_paid' => $validated['who_paid'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return $this->created(['expense' => $this->present($expense)], __('Expense recorded.'));
    }

    /** The public shape of a single expense row. */
    private function present(Expense $expense): array
    {
        return [
            'id' => $expense->id,
            'date' => $expense->date?->toDateString(),
            'description' => $expense->description,
            'expense_type' => $expense->expense_type,
            'quantity' => $expense->quantity,
            'unit_price' => (float) $expense->unit_price,
            'total_cost' => (float) $expense->total_cost,
            'who_paid' => $expense->who_paid,
            'notes' => $expense->notes,
        ];
    }
}
