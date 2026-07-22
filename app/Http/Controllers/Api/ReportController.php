<?php

namespace App\Http\Controllers\Api;

use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Financial transparency — the data behind the site's /report page, which
 * is a static Blade view with no controller of its own.
 *
 * GET /api/report — expenses, paginated, with totals per type
 */
class ReportController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $expenses = Expense::ordered()->paginate($this->perPage($request, 20));

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $expenses->getCollection()->map(fn (Expense $expense) => [
                'id' => $expense->id,
                'date' => $expense->date?->toDateString(),
                'description' => $expense->description,
                'expense_type' => $expense->expense_type,
                'quantity' => $expense->quantity,
                'unit_price' => (float) $expense->unit_price,
                'total_cost' => (float) $expense->total_cost,
                'who_paid' => $expense->who_paid,
                'notes' => $expense->notes,
            ])->all(),
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
}
