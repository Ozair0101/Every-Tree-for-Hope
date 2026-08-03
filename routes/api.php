<?php

use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DonatorController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MaintenanceVisitController;
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
use App\Http\Controllers\Api\V1\PublicProfileController;
use App\Http\Controllers\Api\V1\Tasks\NotificationController as TaskNotificationController;
use App\Http\Controllers\Api\V1\Tasks\TaskAssignmentController;
use App\Http\Controllers\Api\V1\Tasks\TaskController;
use App\Http\Controllers\Api\V1\Tasks\TaskProgressController;
use App\Http\Controllers\Api\V1\Tasks\TaskReviewController;
use App\Http\Controllers\Api\V1\TreeController;
use App\Http\Controllers\Api\V1\TreeImageController;
use App\Http\Controllers\Api\V1\TreeSocialController;
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
    /*
    |----------------------------------------------------------------------
    | Public member profiles
    |----------------------------------------------------------------------
    | Reached by tapping a name on a post or a comment. Open to anyone, because
    | the wall itself is — but the payload is narrow, and the task block inside
    | it only appears for a caller holding `view_any_task`.
    */
    Route::get('/users/{user}/profile', [PublicProfileController::class, 'show'])
        ->name('users.profile');

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/stats', [HomeController::class, 'stats'])->name('stats');
    Route::get('/report', [ReportController::class, 'index'])->name('report');
    // Record an expense — gated by the create_expense permission in the controller.
    Route::post('/report', [ReportController::class, 'store'])
        ->middleware(['auth:sanctum', 'throttle:20,1'])
        ->name('report.store');

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

        // Module B — maintenance visits logged against a planting event. The
        // log is public (approved visits only for anonymous viewers, resolved
        // in the controller); recording one needs a session.
        Route::get('/{event}/maintenance-visits', [MaintenanceVisitController::class, 'index'])
            ->name('maintenance-visits.index');
        Route::post('/{event}/maintenance-visits', [MaintenanceVisitController::class, 'store'])
            ->middleware(['auth:sanctum', 'throttle:20,1'])
            ->name('maintenance-visits.store');
    });

    /*
    |----------------------------------------------------------------------
    | Maintenance visit moderation — approve/reject a logged visit
    |----------------------------------------------------------------------
    */
    Route::prefix('maintenance-visits')->name('maintenance-visits.')
        ->middleware('auth:sanctum')
        ->group(function () {
            Route::post('/{visit}/approve', [MaintenanceVisitController::class, 'approve'])->name('approve');
            Route::post('/{visit}/reject', [MaintenanceVisitController::class, 'reject'])->name('reject');
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
        // Declared before '/{voice}' so the wildcard does not read "mine" as a slug.
        Route::get('/mine', [VoiceController::class, 'mine'])
            ->middleware('auth:sanctum')
            ->name('mine');

        // Keyed on the comment, not the finding it sits on — a comment id is
        // unique on its own. Declared before the '/{voice}' wildcard so
        // "comments" is not read as a slug.
        Route::delete('/comments/{comment}', [VoiceController::class, 'destroyComment'])
            ->middleware('auth:sanctum')
            ->name('comments.destroy');

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

        // The community wall. Reading is open — a plantation programme's work
        // is public — so this sits outside the auth group alongside the map.
        Route::get('/feed', [TreeSocialController::class, 'feed'])->name('feed');

        // Deleting a comment is keyed on the comment, not the tree it sits on:
        // the author may remove their own from anywhere. Declared before the
        // '/{tree}' wildcard so "comments" is not read as a tree id.
        Route::delete('/comments/{comment}', [TreeSocialController::class, 'destroyComment'])
            ->middleware('auth:sanctum')
            ->name('comments.destroy');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/mine', [TreeController::class, 'mine'])->name('mine');

            // Moderation, mirroring the approve/reject actions on the Filament
            // resource so a reviewer can clear the queue from the phone.
            // Declared before '/{tree}' so "pending" is not read as an id.
            Route::get('/pending', [TreeController::class, 'pending'])->name('pending');

            // The caller's own saved posts. Declared before '/{tree}' so the
            // wildcard does not read "favourites" as an id.
            Route::get('/favourites', [TreeSocialController::class, 'favourites'])->name('favourites');
            Route::post('/{tree}/approve', [TreeController::class, 'approve'])->name('approve');
            Route::post('/{tree}/reject', [TreeController::class, 'reject'])->name('reject');

            Route::post('/', [TreeController::class, 'store'])
                ->middleware('throttle:20,1')
                ->name('store');

            // The planter correcting their own record. Coordinates are not
            // editable — see the controller for why.
            Route::match(['put', 'patch'], '/{tree}', [TreeController::class, 'update'])
                ->middleware('throttle:30,1')
                ->name('update');
            Route::post('/{tree}/updates', [TreeController::class, 'storeUpdate'])
                ->middleware('throttle:30,1')
                ->name('updates.store');

            // The "after" photograph, added to the same record as the planting
            // photo so the pair is unambiguous. Superseded by the multi-image
            // endpoint below; kept so an app build that has not updated yet
            // keeps working.
            Route::post('/{tree}/after-image', [TreeController::class, 'storeAfterImage'])
                ->middleware('throttle:20,1')
                ->name('after-image');

            /*
            |------------------------------------------------------------------
            | Galleries — several photographs per phase, one of them the cover
            |------------------------------------------------------------------
            */
            Route::post('/{tree}/images', [TreeImageController::class, 'storeBefore'])
                ->middleware('throttle:20,1')
                ->name('images.store');
            Route::post('/{tree}/after-images', [TreeImageController::class, 'storeAfter'])
                ->middleware('throttle:20,1')
                ->name('after-images.store');
            Route::post('/{tree}/images/{image}/cover', [TreeImageController::class, 'setCover'])
                ->name('images.cover');
            Route::delete('/{tree}/images/{image}', [TreeImageController::class, 'destroy'])
                ->name('images.destroy');

            /*
            |------------------------------------------------------------------
            | Reacting — signed in only
            |------------------------------------------------------------------
            | Reading the wall is public; liking, commenting and sharing are not.
            */
            // Private save. Toggling, like the like above.
            Route::post('/{tree}/favourite', [TreeSocialController::class, 'toggleFavourite'])
                ->name('favourite');

            // Removing a post you planted. Moderators may also remove one.
            Route::delete('/{tree}', [TreeController::class, 'destroy'])->name('destroy');

            Route::post('/{tree}/like', [TreeSocialController::class, 'toggleLike'])
                ->middleware('throttle:60,1')
                ->name('like');
            Route::post('/{tree}/share', [TreeSocialController::class, 'share'])
                ->middleware('throttle:60,1')
                ->name('share');
            Route::post('/{tree}/comments', [TreeSocialController::class, 'storeComment'])
                ->middleware('throttle:30,1')
                ->name('comments.store');
        });

        // Public reads that carry a tree id. Declared before the bare '/{tree}'
        // so the wildcard does not swallow the sub-paths.
        Route::get('/{tree}/images', [TreeImageController::class, 'index'])->name('images.index');
        Route::get('/{tree}/comments', [TreeSocialController::class, 'comments'])->name('comments.index');

        // Declared last so the wildcard does not match "map", "mine" or "feed".
        Route::get('/{tree}', [TreeController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Task management — field operations
    |----------------------------------------------------------------------
    |
    | Every route requires a Sanctum token: there is no public view of who is
    | doing what and where. Authorisation beyond that is the policy's job —
    | staff see the whole board, volunteers see their own work.
    |
    | {task} binds on the public UUID (see TaskServiceProvider::boot), so an
    | integer id in a URL is a 404 rather than someone else's task.
    |
    | Ordering matters: every static segment (mine, map, notifications,
    | reviews) is declared BEFORE '/{task}', or the wildcard swallows it.
    |
    | Write endpoints are rate limited. Submissions and progress reports carry
    | photos over slow connections, so their limits are lower than reads but
    | high enough for a volunteer working through a checklist in the field.
    */
    Route::prefix('tasks')->name('tasks.')->middleware('auth:sanctum')->group(function () {

        // ── Task inbox (task_notifications, not Laravel's notifications) ──
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [TaskNotificationController::class, 'index'])->name('index');
            Route::get('/unread-count', [TaskNotificationController::class, 'unreadCount'])->name('unread-count');
            Route::post('/read-all', [TaskNotificationController::class, 'markAllRead'])->name('read-all');
            Route::get('/preferences', [TaskNotificationController::class, 'preferences'])->name('preferences');
            Route::put('/preferences', [TaskNotificationController::class, 'updatePreferences'])->name('preferences.update');
            Route::post('/{id}/read', [TaskNotificationController::class, 'markRead'])->whereNumber('id')->name('read');
            Route::post('/{id}/unread', [TaskNotificationController::class, 'markUnread'])->whereNumber('id')->name('unread');
            Route::delete('/{id}', [TaskNotificationController::class, 'destroy'])->whereNumber('id')->name('destroy');
        });

        // ── The reviewer's queue, across every task ──
        Route::get('/reviews/queue', [TaskReviewController::class, 'queue'])->name('reviews.queue');

        // Who a coordinator may assign to. Static, so declared before '/{task}'.
        Route::get('/assignable-users', [TaskAssignmentController::class, 'assignableUsers'])
            ->name('assignable-users');

        // ── Lists ──
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/mine', [TaskController::class, 'mine'])->name('mine');
        Route::get('/map', [TaskController::class, 'map'])->name('map');
        Route::post('/', [TaskController::class, 'store'])->middleware('throttle:60,1')->name('store');

        // ── One task ──
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::match(['put', 'patch'], '/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        Route::post('/{task}/publish', [TaskController::class, 'publish'])->name('publish');
        Route::post('/{task}/cancel', [TaskController::class, 'cancel'])->name('cancel');
        Route::get('/{task}/progress', [TaskProgressController::class, 'index'])->name('progress.index');

        // ── Assignments ──
        Route::prefix('/{task}/assignments')->name('assignments.')->group(function () {
            Route::get('/', [TaskAssignmentController::class, 'index'])->name('index');
            Route::post('/', [TaskAssignmentController::class, 'store'])->name('store');

            Route::prefix('/{assignment}')->group(function () {
                Route::post('/accept', [TaskAssignmentController::class, 'accept'])->name('accept');
                Route::post('/decline', [TaskAssignmentController::class, 'decline'])->name('decline');
                Route::post('/start', [TaskAssignmentController::class, 'start'])->name('start');
                Route::post('/reassign', [TaskAssignmentController::class, 'reassign'])->name('reassign');

                Route::post('/submit', [TaskAssignmentController::class, 'submit'])
                    ->middleware('throttle:20,1')->name('submit');

                Route::get('/checklist', [TaskAssignmentController::class, 'checklist'])->name('checklist');
                Route::post('/checklist/{item}', [TaskAssignmentController::class, 'tickChecklistItem'])
                    ->whereNumber('item')->middleware('throttle:120,1')->name('checklist.tick');
                Route::delete('/checklist/{item}', [TaskAssignmentController::class, 'untickChecklistItem'])
                    ->whereNumber('item')->middleware('throttle:120,1')->name('checklist.untick');

                Route::get('/progress', [TaskProgressController::class, 'forAssignment'])->name('progress.index');
                Route::post('/progress', [TaskProgressController::class, 'store'])
                    ->middleware('throttle:60,1')->name('progress.store');

                Route::get('/reviews', [TaskReviewController::class, 'index'])->name('reviews.index');
                Route::post('/reviews', [TaskReviewController::class, 'store'])
                    ->middleware('throttle:60,1')->name('reviews.store');
            });
        });
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

        // Expo push device registration. Declared before '/{id}/read' so the
        // wildcard does not swallow them.
        Route::post('/devices', [NotificationController::class, 'registerDevice'])->name('devices.register');
        Route::delete('/devices', [NotificationController::class, 'unregisterDevice'])->name('devices.unregister');

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
