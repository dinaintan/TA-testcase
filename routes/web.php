<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/upload');
});

// Halaman upload file
// Cukup definisikan satu rute GET untuk /upload yang mengarah ke controller
Route::get('/upload', [UploadController::class, 'index'])->name('upload.form');

// Proses parsing dan menampilkan tabel ADT
Route::post('/upload', [UploadController::class, 'proses'])->name('upload');
Route::get('/tableadt', [UploadController::class, 'tampiltabel'])->name('tableadt');
Route::get('/tableadg', [UploadController::class, 'tampilgraph'])->name('tableadg');


require __DIR__.'/auth.php';
