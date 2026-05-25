<?php

use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\ProfileController;
use Illuminate\Support\Facades\Route;

// ── GUEST ────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── STUDENT PORTAL — wajib login + role student ───────────────────
Route::middleware(['auth', 'student.only'])->group(function () {

    Route::get('/', fn() => redirect()->route('student.dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('student.dashboard');

    // Transactions
    Route::prefix('transactions')->name('student.transactions')->group(function () {
        Route::get('/',                    [PaymentController::class, 'index'])->name('');
        Route::get('/{transaction}/download', [PaymentController::class, 'download'])->name('.download');
        Route::get('/{transaction}',       [PaymentController::class, 'show'])->name('.show');
        Route::post('/{transaction}/snap-token',    [PaymentController::class, 'getSnapToken'])->name('.snap-token');
        Route::post('/{transaction}/upload-proof',  [PaymentController::class, 'uploadProof'])->name('.upload-proof');
    });

    // Invoices
    Route::post('/invoices/{invoice}/checkout', [PaymentController::class, 'checkoutInvoice'])->name('student.invoices.checkout');

    // Profile
    Route::get('/profile',           [ProfileController::class, 'edit'])->name('student.profile');
    Route::put('/profile',           [ProfileController::class, 'update'])->name('student.profile.update');
    Route::put('/profile/password',  [ProfileController::class, 'updatePassword'])->name('student.profile.password');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
