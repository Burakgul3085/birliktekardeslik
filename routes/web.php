<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/bagis-yap', [HomeController::class, 'donations'])->name('donations');
Route::get('/iletisim', [HomeController::class, 'contact'])->name('contact');
Route::post('/iletisim', [HomeController::class, 'submitContact'])->name('contact.submit');
Route::get('/gonullu-ol', [HomeController::class, 'volunteer'])->name('volunteer');
Route::post('/gonullu-ol', [HomeController::class, 'submitVolunteer'])->name('volunteer.submit');
Route::get('/sayfa/{slug}', [HomeController::class, 'page'])->name('pages.show');
