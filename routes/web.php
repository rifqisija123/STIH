<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PemetaanController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect('/login');
});

// Pemetaan Routes (Protected with Auth)
Route::prefix('pemetaan')->name('pemetaan.')->middleware('auth')->group(function () {
    Route::get('/form', [PemetaanController::class, 'form'])->name('form');
    Route::get('/form/tabel', [PemetaanController::class, 'tabel'])->name('form.tabel');
    Route::get('/form/{id}', [PemetaanController::class, 'edit'])->name('form.edit');
    Route::post('/form', [PemetaanController::class, 'store'])->name('store');
    Route::put('/form/{id}', [PemetaanController::class, 'update'])->name('form.update');
    Route::delete('/form/{id}', [PemetaanController::class, 'destroy'])->name('form.destroy');
    Route::get('/schools', [PemetaanController::class, 'getSchools'])->name('schools.get');


    Route::get('/import', [ImportController::class, 'index'])->name('import');
    Route::post('/import/process', [ImportController::class, 'process'])->name('import.process');
    Route::post('/import/preview', [ImportController::class, 'preview'])->name('import.preview');
});

// API Routes for Pemetaan
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/cities', [PemetaanController::class, 'getCities'])->name('api.cities');
});

// Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');