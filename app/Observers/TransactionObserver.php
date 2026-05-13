<?php

namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

/**
 * TransactionObserver
 *
 * Menangani otomatisasi di level model.
 * Sesuai architecture.md: Observer bertugas menangani side effects
 * agar service dan controller tetap clean.
 */
class TransactionObserver
{
    /**
     * Log saat transaksi baru dibuat.
     */
    public function created(Transaction $transaction): void
    {
        Log::info('[Transaction] Tagihan baru dibuat', [
            'code'          => $transaction->code,
            'user_id'       => $transaction->user_id,
            'department_id' => $transaction->department_id,
        ]);
    }

    /**
     * Saat status berubah — log perubahan untuk audit.
     */
    public function updated(Transaction $transaction): void
    {
        // Hanya log jika payment_status berubah
        if ($transaction->wasChanged('payment_status')) {
            Log::info('[Transaction] Status berubah', [
                'code'   => $transaction->code,
                'dari'   => $transaction->getOriginal('payment_status'),
                'menjadi'=> $transaction->payment_status->value,
            ]);
        }
    }

    /**
     * Log saat transaksi dihapus (soft delete).
     */
    public function deleted(Transaction $transaction): void
    {
        Log::warning('[Transaction] Transaksi dihapus', [
            'code'    => $transaction->code,
            'user_id' => $transaction->user_id,
        ]);
    }
}
