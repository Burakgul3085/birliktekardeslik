<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/bagis-yap', [HomeController::class, 'donations'])->name('donations');
Route::get('/iletisim', [HomeController::class, 'contact'])->name('contact');
Route::post('/iletisim', [HomeController::class, 'submitContact'])->name('contact.submit');
Route::get('/gonullu-ol', [HomeController::class, 'volunteer'])->name('volunteer');
Route::post('/gonullu-ol', [HomeController::class, 'submitVolunteer'])->name('volunteer.submit');
Route::post('/ebulten/kayit', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:30,1')
    ->name('newsletter.subscribe');
Route::get('/ebulten/iptal/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/sayfa/{slug}', [HomeController::class, 'page'])->name('pages.show');
