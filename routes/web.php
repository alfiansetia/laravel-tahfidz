<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SurahController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PredictionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except(['edit']);
    Route::resource('siswa', SiswaController::class)->except(['edit']);
    Route::resource('surah', SurahController::class)->except(['edit']);

    // Route Setoran
    Route::get('/setoran/history', [SetoranController::class, 'history'])->name('setoran.history');
    Route::get('/setoran/data', [SetoranController::class, 'data'])->name('setoran.data');
    Route::get('/setoran/siswa', [SetoranController::class, 'getSiswaByKelas'])->name('setoran.siswa');
    Route::resource('setoran', SetoranController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // AI Analysis
    Route::get('/prediction', [PredictionController::class, 'index'])->name('prediction.index');
});
