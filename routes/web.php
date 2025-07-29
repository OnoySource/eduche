<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FormController;


Route::get('/', [HomeController::class, 'index']);
Route::get('/hasil', [HomeController::class, 'hasil']);

Route::post('/',[FormController::class, 'prosesForm'])->name('proses.form');

Route::get('/cek-drive', function () {
    try {
        // Coba akses isi folder Google Drive
        $files = Storage::disk('google')->listContents('/', false);

        // Jika berhasil, tampilkan list file/folder
        return response()->json([
            'status' => 'berhasil',
            'jumlah_item' => count($files),
            'files' => $files
        ]);
    } catch (\Exception $e) {
        // Jika gagal, tampilkan pesan error
        return response()->json([
            'status' => 'gagal',
            'pesan' => $e->getMessage()
        ]);
    }
});

