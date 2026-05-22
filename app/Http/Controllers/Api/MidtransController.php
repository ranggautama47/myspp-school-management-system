<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MidtransController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtransService
    ) {}

    // =========================================
    // SNAP TOKEN
    // POST /api/midtrans/snap-token
    // Header: Authorization: Bearer {token}
    // Body: { transaction_id }
    //
    // Student request snap token untuk buka popup Midtrans.
    // Hanya boleh request untuk transaksi milik sendiri.
    // =========================================

    public function snapToken(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id' => ['required', 'integer', 'exists:transactions,id'],
        ]);

        $transaction = Transaction::with(['user', 'department'])
            ->findOrFail($request->transaction_id);

        // Guard: student hanya bisa request token untuk transaksinya sendiri
        if (
            ! $request->user()->hasRole('admin') &&
            ! $request->user()->hasRole('super admin')
        ) {
            if ($transaction->user_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized. Transaksi ini bukan milik kamu.',
                ], 403);
            }
        }

        // Guard: hanya transaksi pending yang bisa dibayar
        if (! $transaction->canBePaid()) {
            return response()->json([
                'message' => 'Transaksi ini tidak bisa dibayar. Status: ' . $transaction->payment_status->label(),
            ], 422);
        }

        try {
            $snapToken = $this->midtransService->createSnapToken($transaction);

            return response()->json([
                'snap_token'   => $snapToken,
                'client_key'   => config('services.midtrans.client_key'),
                'is_production' => config('services.midtrans.is_production'),
                'transaction'  => [
                    'code'   => $transaction->code,
                    'amount' => (int) $transaction->amount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal generate snap token: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================
    // WEBHOOK
    // POST /api/midtrans/webhook
    // Tidak pakai auth — Midtrans server yang kirim
    //
    // Midtrans akan kirim notifikasi ke sini saat:
    // - Payment settlement (lunas)
    // - Payment pending
    // - Payment expired / deny / cancel
    // =========================================

    public function webhook(Request $request): Response
    {
        $payload = $request->all();

        // Log raw payload untuk debugging (hapus di production jika tidak perlu)
        \Illuminate\Support\Facades\Log::info('Midtrans webhook received', [
            'order_id' => $payload['order_id'] ?? 'unknown',
            'status'   => $payload['transaction_status'] ?? 'unknown',
        ]);

        try {
            $this->midtransService->handleWebhook($payload);

            // Midtrans butuh HTTP 200 untuk tahu webhook berhasil diterima
            return response('OK', 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans webhook error', [
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);

            // Tetap return 200 ke Midtrans agar tidak retry terus-menerus
            // Error sudah dicatat di log
            return response('Error handled', 200);
        }
    }
}
