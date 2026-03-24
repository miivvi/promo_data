<?php

use App\Models\ReportProcess;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/process_control', \App\Http\Controllers\ReportProcessController::class)->name('process_control');
Route::get('/download_file/{id}', \App\Http\Controllers\FileController::class)->name('download_file');
Route::get('/download/stream/{token}', \App\Http\Controllers\FileDownloadStreamController::class)->name('download_stream');
