<?php
use App\Http\Controllers\StemController;

Route::get('/', [StemController::class, 'index'])->name('stem.form');
Route::post('/', [StemController::class, 'process'])->name('stem.process');
