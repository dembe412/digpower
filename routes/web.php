<?php

use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
})->name('home');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/google{token}.html', [SeoController::class, 'googleVerification'])
    ->where('token', '[A-Za-z0-9_-]+')
    ->name('seo.google-verification');
