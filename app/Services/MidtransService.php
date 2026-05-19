<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\PaymentLog;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        // Konfigurasi Midtrans dari config/services.php
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    /**
     * Generate Snap token untuk payment popup.
     * Dipanggil dari TransactionController atau StudentController.
     */
    public function createSnapToken(Transaction $transaction): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $transaction->code,
                'gross_amount' => (int) $transaction->amount,
            ],
            'customer_details' => [
                'first_name' => $transaction->user?->name ?? 'Student',
                'email' => $transaction->user?->email ?? 'student@myspp.com',
                'phone' => $transaction->user?->phone ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => $transaction->department?->id ?? 1,
                    'price' => (int) $transaction->amount,
                    'quantity' => 1,
                    'name' => 'SPP ' . ($transaction->department?->name ?? 'Biaya Pendidikan'),
                ],
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Simpan snap token ke transaction
        $transaction->update([
            'snap_token' => $snapToken,
            'midtrans_url' => Snap::getSnapUrl($params),
        ]);

        return $snapToken;
    }

    /**
     * Handle webhook notification dari Midtrans.
     * Dipanggil dari MidtransController@webhook.
     */
    public function handleWebhook(array $payload): void
    {
        // Verifikasi signature dulu
        if (!$this->verifySignature($payload)) {
            throw new \Exception('Invalid Midtrans signature key.');
        }

        $orderId = $payload['order_id'] ?? null;
        $status = $payload['transaction_status'] ?? null;

        if (!$orderId || !$status) {
            return;
        }

        $transaction = Transaction::where('code', $orderId)->first();

        if (!$transaction) {
            return; // Order tidak ditemukan, abaikan
        }

        // Catat ke PaymentLog untuk audit trail
        PaymentLog::record($transaction, $payload);

        // Update status transaksi berdasarkan notifikasi Midtrans
        match ($status) {
            'settlement', 'capture' => $transaction->markAsPaid(
                $payload['payment_type'] ?? 'midtrans'
            ),
            'expire' => $transaction->markAsExpired(),
            'deny', 'cancel' => $transaction->update([
                'payment_status' => \App\Enums\TransactionStatus::Cancelled,
            ]),
            default => null, // pending, dll — tidak ada aksi
        };
    }

    /**
     * Verifikasi signature key dari webhook Midtrans.
     * Formula: SHA512(order_id + status_code + gross_amount + server_key)
     */
    public function verifySignature(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey = config('services.midtrans.server_key');

        $expectedSignature = hash(
            'sha512',
            $orderId . $statusCode . $grossAmount . $serverKey
        );

        return ($payload['signature_key'] ?? '') === $expectedSignature;
    }
}