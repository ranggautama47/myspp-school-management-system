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
     * Admin dan Super Admin bisa buat tagihan.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole('super-admin');
    }

    /**
     * Student bisa bayar transaksi miliknya yang masih pending.
     * Admin/Super Admin tidak perlu — pakai approve.
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
     * Approve manual payment — Admin dan Super Admin.
     */
    public function approve(User $user, Transaction $transaction): bool
    {
        return ($user->isAdmin() || $user->hasRole('super-admin')) && $transaction->isPending();
    }

    /**
     * Delete — Admin dan Super Admin.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $user->hasRole('super-admin');
    }
}
