<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/works', function () {
    return view('pages.works');
})->name('works');

Route::get('/event/{event}', [App\Http\Controllers\EventController::class, 'show'])->name('events.show');

Route::get('/event', [App\Http\Controllers\EventController::class, 'index'])->name('gallery');

// Keep old /gallery links working (bookmarks, shared URLs, search engines).
Route::redirect('/gallery', '/event');
Route::redirect('/gallery/{event}', '/event/{event}');

// Upcoming (future) events
Route::get('/upcoming-events', [App\Http\Controllers\UpcomingEventController::class, 'index'])
    ->name('upcoming-events.index');
Route::get('/upcoming-event/{upcomingEvent}', [App\Http\Controllers\UpcomingEventController::class, 'show'])
    ->name('upcoming-events.show');

// Careers / Jobs
Route::get('/careers', [App\Http\Controllers\CareerController::class, 'index'])->name('careers');
Route::get('/careers/{job}', [App\Http\Controllers\CareerController::class, 'show'])->name('careers.show');
Route::post('/careers/{job}/apply', [App\Http\Controllers\CareerController::class, 'apply'])->name('careers.apply');

Route::get('/donators', [App\Http\Controllers\DonatorController::class, 'index'])->name('donators');

Route::get('/partners', [App\Http\Controllers\PartnerController::class, 'partners'])->name('partners');
Route::get('/advisors', [App\Http\Controllers\PartnerController::class, 'advisors'])->name('advisors');

Route::get('/report', function () {
    return view('pages.report');
})->name('report');

Route::get('/funding-model', function () {
    return view('pages.funding');
})->name('funding');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

// Route::get('/donate', function () {
//     return view('pages.aidos-campaign');
// })->name('donate');

Route::get('/history', function () {
    return view('pages.history');
})->name('history');

Route::get('/children-awareness', function () {
    return view('pages.children-awareness');
})->name('awareness');

Route::post('/contact', [App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

Route::post('/tree-request', [App\Http\Controllers\TreeRequestController::class, 'submit'])->name('tree-request.submit');

Route::post('/get-involved', [App\Http\Controllers\InvolvementController::class, 'store'])->name('involvement.store');

Route::get('/faq', [App\Http\Controllers\FaqController::class, 'index'])->name('faq');
Route::post('/faq', [App\Http\Controllers\FaqController::class, 'submit'])->name('faq.submit');

// Voices of Nature — community wall (ideas, findings, experiences)
Route::get('/voices', [App\Http\Controllers\VoiceController::class, 'index'])->name('voices.index');
Route::get('/voices/share', [App\Http\Controllers\VoiceController::class, 'create'])->name('voices.create');
Route::post('/voices', [App\Http\Controllers\VoiceController::class, 'store'])->name('voices.store');
Route::get('/voices/{voice}', [App\Http\Controllers\VoiceController::class, 'show'])->name('voices.show');
Route::post('/voices/{voice}/like', [App\Http\Controllers\VoiceController::class, 'like'])->name('voices.like');
Route::post('/voices/{voice}/comment', [App\Http\Controllers\VoiceController::class, 'comment'])->name('voices.comment');

/*
|--------------------------------------------------------------------------
| Admin — printable task report
|--------------------------------------------------------------------------
|
| A web route rather than a Filament action, because the report has to open in
| a new tab: the browser's own print dialogue is what turns it into a PDF, and
| a Livewire action cannot navigate there. Guarded by the panel's auth plus an
| explicit `export_task` check inside the controller.
*/
Route::middleware(['auth'])
    ->prefix('admin/tasks')
    ->name('admin.tasks.')
    ->group(function () {
        Route::get('/export/pdf', [App\Http\Controllers\Admin\TaskExportController::class, 'pdf'])
            ->name('export.pdf');
    });

/*
|--------------------------------------------------------------------------
| Admin — analytics reports
|--------------------------------------------------------------------------
|
| Same reasoning as above, twice over: the Excel report streams a file download
| and the printable report needs its own tab, and a Livewire action can do
| neither. Both re-check `export_task` inside the controller.
*/
Route::middleware(['auth'])
    ->prefix('admin/analytics')
    ->name('admin.analytics.')
    ->group(function () {
        Route::get('/export/excel', [App\Http\Controllers\Admin\AnalyticsExportController::class, 'excel'])
            ->name('export.excel');
        Route::get('/export/pdf', [App\Http\Controllers\Admin\AnalyticsExportController::class, 'pdf'])
            ->name('export.pdf');
    });

/*
|--------------------------------------------------------------------------
| Cached image thumbnails
|--------------------------------------------------------------------------
|
| Serves small, cached copies of stored photographs for the mobile app's list
| cards. Deliberately outside the API's rate limiter — a scrolling feed loads
| many images at once, and static images should not count against it.
*/
Route::get('/thumb/{width}/{path}', [App\Http\Controllers\ThumbnailController::class, 'show'])
    ->where('width', '[0-9]+')
    ->where('path', '.*')
    ->name('thumb');
