<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\PaymentLog;
use App\Models\Transaction;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized  = config('services.midtrans.is_sanitized', true);
        Config::$is3ds        = config('services.midtrans.is_3ds', true);
    }

    // =========================================
    // CREATE SNAP TOKEN
    //
    // FIX: order_id tidak boleh sama dua kali di Midtrans.
    // Solusi: tambahkan suffix timestamp agar selalu unik.
    // Contoh: TRX-20250511-DEMO2-1716192000
    //
    // snap_token yang sudah ada di DB diabaikan karena
    // token lama sudah expired di sisi Midtrans.
    // =========================================

    public function createSnapToken(Transaction $transaction): string
    {
        $transaction->loadMissing(['user', 'department']);

        // Generate order_id unik — tambah timestamp suffix
        // agar tidak bentrok meski kode transaksi sama
        $orderId = $transaction->code . '-' . now()->timestamp;

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $transaction->amount,
            ],
            'customer_details' => [
                'first_name' => $transaction->user?->name ?? 'Student',
                'email'      => $transaction->user?->email ?? 'student@myspp.com',
                'phone'      => $transaction->user?->phone ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id'       => (string) ($transaction->department?->id ?? 1),
                    'price'    => (int) $transaction->amount,
                    'quantity' => 1,
                    'name'     => 'SPP ' . ($transaction->department?->name ?? 'Biaya Pendidikan'),
                ],
            ],

            // ─────────────────────────────────────────────────────────
            // FIX SANDBOX: Hanya tampilkan metode yang ada simulatornya
            // QRIS dan ShopeePay tidak bisa di-simulate → dibuang
            // GoPay biasa → ada tombol "Simulate Payment" di sandbox
            // ─────────────────────────────────────────────────────────
            'enabled_payments' => [
                'gopay',
                'bank_transfer',
                'credit_card',
            ],

            'expiry' => [
                'unit'     => 'hour',
                'duration' => 1,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        // Update transaksi dengan snap token baru
        // Simpan juga order_id yang dipakai ke Midtrans
        // untuk keperluan matching saat webhook masuk
        $transaction->update([
            'snap_token' => $snapToken,
            // Simpan midtrans_order_id agar webhook bisa match
            // ke transaction yang benar meskipun order_id berbeda
        ]);

        // Simpan mapping order_id → transaction.code di PaymentLog
        // sehingga saat webhook masuk dengan order_id "TRX-xxx-timestamp",
        // kita masih bisa temukan transaksinya
        PaymentLog::create([
            'transaction_id'    => $transaction->id,
            'status'            => 'snap_created',
            'raw_response'      => ['order_id' => $orderId, 'snap_token' => $snapToken],
            'midtrans_order_id' => $orderId,
        ]);

        return $snapToken;
    }

    // =========================================
    // HANDLE WEBHOOK
    // =========================================

    public function handleWebhook(array $payload): void
    {
        if (! $this->verifySignature($payload)) {
            throw new \Exception('Invalid Midtrans signature key.');
        }

        $orderId = $payload['order_id'] ?? null;
        $status  = $payload['transaction_status'] ?? null;

        if (! $orderId || ! $status) {
            return;
        }

        // Cari transaksi — coba exact match dulu (kode asli)
        $transaction = Transaction::where('code', $orderId)->first();

        // Kalau tidak ketemu, cari via PaymentLog
        // (karena order_id pakai format "TRX-xxx-timestamp")
        if (! $transaction) {
            $log = PaymentLog::where('midtrans_order_id', $orderId)->first();
            if ($log) {
                $transaction = $log->transaction;
            }
        }

        if (! $transaction) {
            \Illuminate\Support\Facades\Log::warning('Midtrans webhook: transaction not found', [
                'order_id' => $orderId,
            ]);
            return;
        }

        // Catat ke PaymentLog
        PaymentLog::record($transaction, $payload);

        // Update status
        match ($status) {
            'settlement', 'capture' => $transaction->markAsPaid(
                $payload['payment_type'] ?? 'midtrans'
            ),
            'expire'         => $transaction->markAsExpired(),
            'deny', 'cancel' => $transaction->update([
                'payment_status' => TransactionStatus::Cancelled,
            ]),
            default => null,
        };
    }

    // =========================================
    // VERIFY SIGNATURE
    // SHA512(order_id + status_code + gross_amount + server_key)
    // =========================================

    public function verifySignature(array $payload): bool
    {
        $orderId     = $payload['order_id'] ?? '';
        $statusCode  = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey   = config('services.midtrans.server_key');

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $payload['signature_key'] ?? '');
    }
}
