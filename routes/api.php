<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — MySPP School Management System
|--------------------------------------------------------------------------
|
| Base URL  : /api
| Auth      : Laravel Sanctum (Bearer Token)
|
| Public    → tidak perlu token
| Protected → wajib header: Authorization: Bearer {token}
|
*/

// =========================================
// PUBLIC — tidak perlu login
// =========================================

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Midtrans webhook — WAJIB public
// Midtrans server yang kirim POST ke sini, bukan user login
// JANGAN masuk ke dalam auth middleware
Route::post('/midtrans/webhook', [MidtransController::class, 'webhook'])
    ->name('midtrans.webhook');

// =========================================
// PROTECTED — wajib login (Sanctum Bearer Token)
// =========================================

Route::middleware('auth:sanctum')->group(function () {

    // ── Auth ──────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // ── Transactions ──────────────────────────────────────
    // Admin → semua transaksi
    // Student → hanya milik sendiri (difilter di controller via Policy)
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::post('/', [TransactionController::class, 'store']);
        Route::get('/{transaction}', [TransactionController::class, 'show']);
        Route::post('/{transaction}/pay', [TransactionController::class, 'pay']);
        Route::post('/{transaction}/approve', [TransactionController::class, 'approve']);
        Route::post('/{transaction}/upload-proof', [TransactionController::class, 'uploadProof']);
    });

    // ── Midtrans Snap Token ────────────────────────────────
    // Student request snap token untuk buka Snap payment popup
    Route::post('/midtrans/snap-token', [MidtransController::class, 'snapToken'])
        ->name('midtrans.snap-token');

});