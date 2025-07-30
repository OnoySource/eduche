<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\WhatsAppController;



Route::get('/', [HomeController::class, 'index']);
Route::get('/hasil', [HomeController::class, 'hasil']);

Route::post('/',[FormController::class, 'prosesForm'])->name('proses.form');
Route::get('/tes-kirim-wa', [FormController::class, 'kirimWa'])->name('kirim.wa');

Route::get('/send-wa', [WhatsAppController::class, 'send']);
