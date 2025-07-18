<?php
use App\Http\Controllers\StemController;
use App\Http\Controllers\StemmingController;
use App\Http\Controllers\AboutController;

Route::get('/', [StemController::class, 'index'])->name('stem.form');
Route::post('/', [StemController::class, 'process'])->name('stem.process');


Route::get('/bulk-stemming', [StemmingController::class, 'showBulkForm'])->name('bulk.form');
Route::post('/bulk-stemming', [StemmingController::class, 'processBulk'])->name('bulk.process');
Route::get('/bulk-stemming/export', [StemmingController::class, 'exportCSV'])->name('bulk.export');

Route::get('/about', [AboutController::class, 'index'])->name('about');


