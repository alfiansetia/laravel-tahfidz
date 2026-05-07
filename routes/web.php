<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SurahController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PredictionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class)->except(['edit']);
    Route::resource('siswa', SiswaController::class)->except(['edit']);
    Route::resource('surah', SurahController::class)->except(['edit']);

    // Setoran Routes
    Route::get('setoran', [SetoranController::class, 'index'])->name('setoran.index');
    Route::get('setoran/data', [SetoranController::class, 'data'])->name('setoran.data');
    Route::get('setoran/siswa', [SetoranController::class, 'getSiswaByKelas'])->name('setoran.siswa');
    Route::post('setoran', [SetoranController::class, 'store'])->name('setoran.store');
    Route::get('setoran/history/{siswa}', [SetoranController::class, 'history'])->name('setoran.history');
    Route::delete('setoran/{setoran}', [SetoranController::class, 'destroy'])->name('setoran.destroy');

    // Profile Routes
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Analisis Prediksi
    Route::get('analisis', [PredictionController::class, 'index'])->name('prediction.index');
});
