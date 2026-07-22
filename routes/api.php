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

<?php

use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DonatorController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\InvolvementController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SponsorPackageController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TreeRequestController;
use App\Http\Controllers\Api\UpcomingEventController;
use App\Http\Controllers\Api\VoiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Every Tree for Hope
|--------------------------------------------------------------------------
|
| The JSON counterpart of routes/web.php, for the React Native app. The web
| routes are untouched; these live alongside them under the /api URL prefix.
|
| Conventions
| -----------
| • Every group is a resource prefix: /api/events, /api/voices, ...
| • Within a group: index → '/', show → '/{id}', writes → POST.
| • Route names are all prefixed `api.` (the whole file is wrapped in one
|   name group below) so they never clash with the web route names — the
|   Blade views call route('voices.index'), the app calls api.voices.index.
| • Static "meta" routes (/filters, /categories) are declared BEFORE the
|   '/{param}' route so the wildcard does not swallow them.
| • Locale: send `X-Locale: en|fa|ps` (or ?lang=). See SetApiLocale.
| • Device identity for likes: send `X-Device-Id: <uuid>`. See VoiceController.
| • Write endpoints are rate limited; file uploads use multipart/form-data.
|
*/

Route::name('api.')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Home & impact
    |----------------------------------------------------------------------
    */
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/stats', [HomeController::class, 'stats'])->name('stats');
    Route::get('/report', [ReportController::class, 'index'])->name('report');

    /*
    |----------------------------------------------------------------------
    | Events — past planting events (web: /event)
    |----------------------------------------------------------------------
    */
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Upcoming events — what people can still register for
    |----------------------------------------------------------------------
    */
    Route::prefix('upcoming-events')->name('upcoming-events.')->group(function () {
        Route::get('/', [UpcomingEventController::class, 'index'])->name('index');
        Route::get('/{upcomingEvent}', [UpcomingEventController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Careers — job board (web: /careers)
    |----------------------------------------------------------------------
    */
    Route::prefix('careers')->name('careers.')->group(function () {
        Route::get('/', [CareerController::class, 'index'])->name('index');
        // Declared before '/{job}', which would otherwise match "filters".
        Route::get('/filters', [CareerController::class, 'filters'])->name('filters');
        Route::get('/{job}', [CareerController::class, 'show'])->name('show');

        Route::post('/{job}/apply', [CareerController::class, 'apply'])
            ->middleware('throttle:10,1')
            ->name('apply');
    });

    /*
    |----------------------------------------------------------------------
    | Voices of Nature — community wall (web: /voices)
    |----------------------------------------------------------------------
    */
    Route::prefix('voices')->name('voices.')->group(function () {
        Route::get('/', [VoiceController::class, 'index'])->name('index');
        Route::get('/categories', [VoiceController::class, 'categories'])->name('categories');
        Route::get('/{voice}', [VoiceController::class, 'show'])->name('show');

        Route::middleware('throttle:20,1')->group(function () {
            Route::post('/', [VoiceController::class, 'store'])->name('store');
            Route::post('/{voice}/like', [VoiceController::class, 'like'])->name('like');
            Route::post('/{voice}/comment', [VoiceController::class, 'comment'])->name('comment');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Supporters — donators & partners (web: /donators, /partners, /advisors)
    |----------------------------------------------------------------------
    */
    Route::prefix('donators')->name('donators.')->group(function () {
        Route::get('/', [DonatorController::class, 'index'])->name('index');
        // {code} is a sponsor code (e.g. ETH-ABDULKARIM-01), not an id.
        Route::get('/{code}', [DonatorController::class, 'show'])->name('show');
    });

    Route::prefix('partners')->name('partners.')->group(function () {
        Route::get('/', [PartnerController::class, 'index'])->name('index');
        Route::get('/advisors', [PartnerController::class, 'advisors'])->name('advisors');
        Route::get('/{code}', [PartnerController::class, 'show'])->name('show');
    });

    Route::prefix('sponsor-packages')->name('sponsor-packages.')->group(function () {
        Route::get('/', [SponsorPackageController::class, 'index'])->name('index');
        Route::get('/{sponsorPackage}', [SponsorPackageController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Team
    |----------------------------------------------------------------------
    */
    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/{team}', [TeamController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Media — video library
    |----------------------------------------------------------------------
    */
    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');

        /*
         * Admin CRUD, mirroring the web MediaController. Declared before
         * '/{media}' so "manage" is not read as an id.
         *
         * Left open for now because the web routes are too — add
         * ->middleware('auth:sanctum') or 'role:Admin' to this one group
         * once the app has admin login.
         */
        Route::prefix('manage')->name('manage.')->group(function () {
            Route::post('/', [MediaController::class, 'store'])->name('store');
            Route::put('/{media}', [MediaController::class, 'update'])->name('update');
            Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
        });

        Route::get('/{media}', [MediaController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | FAQ (web: /faq)
    |----------------------------------------------------------------------
    */
    Route::prefix('faqs')->name('faqs.')->group(function () {
        Route::get('/', [FaqController::class, 'index'])->name('index');
        Route::post('/', [FaqController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('store');
    });

    /*
    |----------------------------------------------------------------------
    | Public form submissions
    |----------------------------------------------------------------------
    | Rate limited, since these are unauthenticated writes.
    */
    Route::middleware('throttle:10,1')->group(function () {
        // web: POST /contact
        Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

        // web: POST /tree-request
        Route::post('/tree-requests', [TreeRequestController::class, 'store'])->name('tree-requests.store');

        // web: POST /get-involved
        Route::post('/involvement', [InvolvementController::class, 'store'])->name('involvement.store');
    });
});
