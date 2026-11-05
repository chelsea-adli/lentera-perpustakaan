<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Halaman utama
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Login dan registrasi
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.authenticate');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Halaman setelah login
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Data buku
    Route::resource('books', BookController::class);

    // Kategori buku
    Route::resource('categories', CategoryController::class)
        ->except(['show']);

    // Peminjaman buku
    Route::get('/loans', [LoanController::class, 'index'])
        ->name('loans.index');

    Route::post('/loans', [LoanController::class, 'store'])
        ->name('loans.store');

    Route::patch('/loans/{loan}/approve', [LoanController::class, 'approve'])
        ->name('loans.approve');

    Route::patch('/loans/{loan}/reject', [LoanController::class, 'reject'])
        ->name('loans.reject');

    Route::patch('/loans/{loan}/return', [LoanController::class, 'returnBook'])
        ->name('loans.return');

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');
});
