<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile API — version 1
|--------------------------------------------------------------------------
|
| Consumed by the Every Tree for Hope React Native application. Every route is
| prefixed with /api/v1 so that a future breaking change can ship as v2 while
| older installed apps continue to work against v1 — app-store updates are not
| instantaneous and cannot be forced.
|
| Authorisation is NOT re-implemented here. Protected routes resolve the user
| via Sanctum and then defer to the same policy classes the admin panel uses,
| so the mobile client inherits the existing RBAC rules automatically.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Public ──────────────────────────────────────────────────────────
    // Login is throttled hard: 5 attempts per minute per IP. Credential
    // stuffing is the most likely attack against this surface.
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('auth.login');

    // ── Authenticated ───────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
    });
});
