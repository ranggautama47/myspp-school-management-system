<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private readonly MidtransService $midtrans,
    ) {}

    /**
     * Admin membuat tagihan SPP untuk siswa.
     * Dipanggil dari TransactionController@store.
     */
    public function createBill(User $student, int $departmentId): Transaction
    {
        return DB::transaction(function () use ($student, $departmentId) {
            return Transaction::create([
                'user_id'        => $student->id,
                'department_id'  => $departmentId,
                'payment_status' => TransactionStatus::Pending,
            ]);
        });
    }

    /**
     * Siswa memulai pembayaran — generate Midtrans snap token.
     * Dipanggil dari PaymentController@initiate.
     */
    public function initiateMidtransPayment(Transaction $transaction): array
    {
        throw_unless(
            $transaction->canBePaid(),
            \Exception::class,
            'Transaksi ini tidak bisa dibayar.'
        );

        $snapData = $this->midtrans->createSnapToken($transaction);

        $transaction->update([
            'snap_token'   => $snapData['token'],
            'midtrans_url' => $snapData['redirect_url'],
        ]);

        return $snapData;
    }
    /**
     * Siswa upload bukti bayar manual (tanpa Midtrans).
     * Status tetap pending — tunggu approve admin.
     */
    public function submitManualPayment(Transaction $transaction, string $proofPath): void
    {
        throw_unless(
            $transaction->canBePaid(),
            \Exception::class,
            'Transaksi ini tidak bisa dibayar.'
        );

        $transaction->update([
            'proof_of_payment' => $proofPath,
        ]);
    }

    /**
     * Admin approve pembayaran manual.
     * Dipanggil dari TransactionController@approve (Filament action).
     */
    public function approveManualPayment(Transaction $transaction): void
    {
        throw_unless(
            $transaction->isPending(),
            \Exception::class,
            'Hanya transaksi pending yang bisa di-approve.'
        );

        $transaction->markAsPaid('manual');
    }

    /**
     * Summary untuk Filament dashboard widget.
     */
    public function getDashboardSummary(): array
    {
        return [
            'total_income'       => $this->getTotalIncomeThisMonth(),
            'total_paid'         => Transaction::paid()->thisMonth()->count(),
            'total_pending'      => Transaction::pending()->count(),
        ];
    }

    private function getTotalIncomeThisMonth(): float
    {
        return (float) Transaction::paid()
            ->thisMonth()
            ->join('departments', 'transactions.department_id', '=', 'departments.id')
            ->sum('departments.cost');
    }
}
