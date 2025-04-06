<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->name('v1.')->group(function () {
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/{username}', [ProfileController::class, 'show'])->name('show');
        Route::post('/{username}', [ProfileController::class, 'scrape'])->name('scrape');
        Route::get('/{username}/status', [ProfileController::class, 'status'])->name('status');
    });
});
