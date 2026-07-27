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
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\TreeController;
use App\Http\Controllers\Api\VoiceController;
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
| This is the JSON counterpart of routes/web.php. The web routes are untouched;
| these live alongside them and reuse the same models, queries and validation.
|
| Authorisation is NOT re-implemented here. Protected routes resolve the user
| via Sanctum and then defer to the same policy classes the admin panel uses,
| so the mobile client inherits the existing RBAC rules automatically.
|
| Conventions
| -----------
| • Every group is a resource prefix: /api/v1/events, /api/v1/voices, ...
| • Within a group: index → '/', show → '/{id}', writes → POST.
| • Route names are all prefixed `api.v1.` so they never clash with the web
|   route names — the Blade views call route('voices.index'), the app calls
|   route('api.v1.voices.index').
| • Static "meta" routes (/filters, /categories) are declared BEFORE the
|   '/{param}' route so the wildcard does not swallow them.
| • Locale: send `X-Locale: en|fa|ps` (or ?lang=). See SetApiLocale.
| • Device identity for likes: send `X-Device-Id: <uuid>`. See VoiceController.
| • Write endpoints are rate limited; file uploads use multipart/form-data.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Authentication
    |----------------------------------------------------------------------
    */

    // Login is throttled hard: 5 attempts per minute per IP. Credential
    // stuffing is the most likely attack against this surface.
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('auth.login');

    // Sign-up is throttled too, though less aggressively than login: it is a
    // slower, deliberate action, but still a write that must not be scriptable
    // into thousands of junk accounts.
    Route::post('/auth/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('auth.register');

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

        // POST, not PUT: PHP does not populate an uploaded file on PUT/PATCH, and
        // the avatar arrives as multipart. The client spoofs the method if it
        // wants REST semantics; the server just needs the file.
        Route::post('/auth/profile', [AuthController::class, 'updateProfile'])->name('auth.profile');
    });

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
        // Create — gated by the create_event permission inside the controller.
        Route::post('/', [EventController::class, 'store'])
            ->middleware(['auth:sanctum', 'throttle:20,1'])
            ->name('store');
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Upcoming events — what people can still register for
    |----------------------------------------------------------------------
    */
    Route::prefix('upcoming-events')->name('upcoming-events.')->group(function () {
        Route::get('/', [UpcomingEventController::class, 'index'])->name('index');
        Route::post('/', [UpcomingEventController::class, 'store'])
            ->middleware(['auth:sanctum', 'throttle:20,1'])
            ->name('store');
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
    | Planted trees — user field capture with GPS, tracking and moderation
    |----------------------------------------------------------------------
    | Static segments (map, mine) are declared before the '/{tree}' wildcard
    | so it does not swallow them. Writes require a Sanctum token.
    */
    Route::prefix('trees')->name('trees.')->group(function () {
        Route::get('/', [TreeController::class, 'index'])->name('index');
        Route::get('/map', [TreeController::class, 'map'])->name('map');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/mine', [TreeController::class, 'mine'])->name('mine');
            Route::post('/', [TreeController::class, 'store'])
                ->middleware('throttle:20,1')
                ->name('store');
            Route::post('/{tree}/updates', [TreeController::class, 'storeUpdate'])
                ->middleware('throttle:30,1')
                ->name('updates.store');
        });

        // Declared last so the wildcard does not match "map" or "mine".
        Route::get('/{tree}', [TreeController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Notifications — the in-app notification centre (all authenticated)
    |----------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
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
         * Now that Sanctum is available, this is guarded by a token. Swap in
         * 'role:Admin' if a specific role should be required.
         */
        Route::prefix('manage')->name('manage.')->middleware('auth:sanctum')->group(function () {
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
