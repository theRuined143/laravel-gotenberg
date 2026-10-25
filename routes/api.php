<?php

use Illuminate\Support\Facades\Route;
use Nonamechrs\GotenbergScreenshots\Http\Controllers\ScreenshotController;

Route::post('/', [ScreenshotController::class, 'store'])->name('screenshots.store');
Route::get('{id}', [ScreenshotController::class, 'show'])->where('id', '[0-9A-Za-z]{26}')->name('screenshots.show');
