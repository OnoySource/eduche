<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FormController;


Route::get('/', [HomeController::class, 'index']);
Route::get('/hasil', [HomeController::class, 'hasil']);

Route::post('/',[FormController::class, 'prosesForm'])->name('proses.form');

Route::get('/cek-drive', function () {
    try {
        // List isi Google Drive
        $files = Storage::disk('google')->listContents('/', false);

        return response()->json([
            'status' => 'berhasil',
            'jumlah_item' => $files->count(),
            'files' => $files
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'gagal',
            'pesan' => $e->getMessage()
        ]);
    }
});


