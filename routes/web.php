<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\PageDisplayController::class, 'home'])->name('frontend.home');
Route::get('{slug}', [\App\Http\Controllers\PageDisplayController::class, 'show'])->name('frontend.page');
