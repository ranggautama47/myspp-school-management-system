<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\PaymentLog;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private string $serverKey;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key');

        \Midtrans\Config::$serverKey    = $this->serverKey;
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;
    }

    /**
     * Step 3 dari payment-flow.md:
     * Server kirim detail transaksi ke Midtrans → dapat snap_token.
     */
    public function createSnapToken(Transaction $transaction): array
    {
        try {
            $params = [
                'transaction_details' => [
                    'order_id'     => $transaction->code,
                    'gross_amount' => (int) $transaction->department->cost,
                ],
                'customer_details' => [
                    'first_name' => $transaction->user->name,
                    'email'      => $transaction->user->email,
                    'phone'      => $transaction->user->phone ?? '',
                ],
            ];

            $token       = \Midtrans\Snap::getSnapToken($params);
            $redirectUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

            return [
                'token'        => $token,
                'redirect_url' => $redirectUrl,
            ];
        } catch (\Exception $e) {
            Log::error('[MidtransService] createSnapToken gagal', [
                'transaction_code' => $transaction->code,
                'error'            => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Step 6-9 dari payment-flow.md:
     * Midtrans kirim webhook → validasi signature → update status → catat log.
     *
     * Idempotent: transaksi yang sudah paid tidak akan diproses ulang.
     */
    public function handleWebhook(array $payload): void
    {
        // Step 7: Verifikasi signature key — security dari payment-flow.md section 6
        $this->verifySignature($payload);

        $transaction = Transaction::where('code', $payload['order_id'])->firstOrFail();

        // Step 9: Catat log webhook untuk audit trail
        PaymentLog::record($transaction, $payload);

        $status      = $payload['transaction_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? 'unknown';

        // Pemetaan status sesuai tabel payment-flow.md section 4
        match($status) {
            'settlement', 'capture' => $transaction->markAsPaid($paymentType),
            'expire'                => $transaction->markAsExpired(),
            'cancel', 'deny'        => $transaction->update(['payment_status' => TransactionStatus::Cancelled]),
            default                 => null, // 'pending' — tidak perlu diproses
        };
    }

    /**
     * Validasi SHA512 signature dari Midtrans.
     * Mencegah spoofing dari pihak luar.
     */
    private function verifySignature(array $payload): void
    {
        $expected = hash('sha512',
            ($payload['order_id'] ?? '') .
            ($payload['status_code'] ?? '') .
            ($payload['gross_amount'] ?? '') .
            $this->serverKey
        );

        if ($expected !== ($payload['signature_key'] ?? '')) {
            Log::warning('[MidtransService] Signature tidak valid', [
                'order_id' => $payload['order_id'] ?? 'unknown',
            ]);
            throw new \Exception('Invalid Midtrans signature key.');
        }
    }
}
