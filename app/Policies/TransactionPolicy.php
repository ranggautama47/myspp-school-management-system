<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

/**
 * TransactionPolicy
 *
 * Sesuai architecture.md: Policy-Based Authorization.
 * Student hanya bisa akses transaksi miliknya sendiri.
 * Admin punya full access.
 */
class TransactionPolicy
{
    /**
     * Admin lihat semua. Student lihat semua (difilter lewat scope di controller).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin bebas. Student hanya boleh lihat transaksi miliknya.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $transaction->user_id === $user->id;
    }

    /**
     * Hanya admin yang bisa buat tagihan.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Student bisa bayar transaksi miliknya yang masih pending.
     * Admin tidak perlu — admin pakai approve.
     */
    public function pay(User $user, Transaction $transaction): bool
    {
        return $transaction->user_id === $user->id
            && $transaction->canBePaid();
    }

    /**
     * Upload bukti bayar — hanya pemilik transaksi.
     */
    public function uploadProof(User $user, Transaction $transaction): bool
    {
        return $transaction->user_id === $user->id
            && $transaction->isPending();
    }

    /**
     * Approve manual payment — hanya admin.
     */
    public function approve(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() && $transaction->isPending();
    }

    /**
     * Delete — hanya admin.
     */
    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}
