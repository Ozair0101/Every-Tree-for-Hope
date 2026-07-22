<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Base class for every API controller.
 *
 * Keeps the JSON envelope identical across the whole API so the mobile
 * client can rely on one shape:
 *
 *   { "success": bool, "message": string|null, "data": mixed }
 *
 * Paginated resource collections keep their own `data` / `meta` / `links`
 * keys, so `respondWithResource()` is used for those instead.
 */
abstract class ApiController extends Controller
{
    /**
     * A successful response carrying a payload.
     */
    protected function ok(mixed $data = null, ?string $message = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * A successful "resource was created" response.
     */
    protected function created(mixed $data = null, ?string $message = null): JsonResponse
    {
        return $this->ok($data, $message, 201);
    }

    /**
     * A failed response. Validation errors are handled by Laravel itself
     * (422) — this is for business-rule failures.
     */
    protected function fail(string $message, mixed $errors = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * A paginated list in the standard envelope, with a flat `meta` block
     * that is easier to consume from React Native than Laravel's default
     * nested links/meta payload.
     */
    protected function paginated(
        LengthAwarePaginator $paginator,
        string $resourceClass,
        array $extra = []
    ): JsonResponse {
        return response()->json(array_merge([
            'success' => true,
            'message' => null,
            'data' => $resourceClass::collection($paginator)->resolve(),
        ], $extra, [
            'meta' => $this->paginationMeta($paginator),
        ]));
    }

    /**
     * Let the client choose a page size, clamped so a bad value can't ask
     * the database for everything.
     */
    protected function perPage(Request $request, int $default, int $max = 50): int
    {
        return min(max((int) $request->query('per_page', $default), 1), $max);
    }

    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'has_more' => $paginator->hasMorePages(),
        ];
    }
}
