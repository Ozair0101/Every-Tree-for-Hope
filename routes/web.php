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

Route::get('/gallery', function () {
    return view('pages.gallery');
})->name('gallery');

Route::get('/donators', function () {
    return view('pages.donators');
})->name('donators');

Route::get('/report', function () {
    return view('pages.report');
})->name('report');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');
