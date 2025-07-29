<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FormController;


Route::get('/', [HomeController::class, 'index']);
Route::get('/hasil', [HomeController::class, 'hasil']);

Route::post('/',[FormController::class, 'prosesForm'])->name('proses.form');
Route::get('/tes-kirim-wa', [FormController::class, 'kirimWa'])->name('kirim.wa');
