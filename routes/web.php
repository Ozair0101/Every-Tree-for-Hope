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
