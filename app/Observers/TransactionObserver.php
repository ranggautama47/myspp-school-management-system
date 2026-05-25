<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Mail\PaymentSuccessMail;
use App\Enums\TransactionStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
                'menjadi' => $transaction->payment_status->value,
            ]);
            // EKSEKUSI EMAIL JIKA STATUS MENJADI PAID (LUNAS)
            if ($transaction->payment_status === TransactionStatus::Paid) {
                // Eager load user relationship untuk menghindari N+1 queries
                $transaction->loadMissing('user');

                // Pastikan user dan emailnya ada (student email = user email)
                if ($transaction->user && $transaction->user->email) {
                    Mail::to($transaction->user->email)->queue(new PaymentSuccessMail($transaction));
                    Log::info('[Email] PaymentSuccessMail masuk antrean untuk: ' . $transaction->user->email, [
                        'transaction_code' => $transaction->code,
                        'user_id' => $transaction->user->id,
                    ]);
                } else {
                    Log::warning('[Email] Gagal mengirim PaymentSuccessMail: Data email user tidak ditemukan.', [
                        'transaction_code' => $transaction->code,
                        'user_id' => $transaction->user_id,
                    ]);
                }
            }
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
