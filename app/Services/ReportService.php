<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * ReportService
 * Menyediakan data untuk dashboard analytics dan laporan.
 * Sesuai architecture.md: business logic di service, bukan controller.
 */
class ReportService
{
    /**
     * Summary untuk Filament dashboard widgets.
     */
    public function monthlySummary(): array
    {
        return [
            'total_income'  => $this->totalIncomeThisMonth(),
            'total_paid'    => Transaction::paid()->thisMonth()->count(),
            'total_pending' => Transaction::pending()->count(),
            'total_student' => User::students()->count(),
        ];
    }

    /**
     * Data chart pemasukan per bulan (tahun berjalan).
     * Untuk Filament chart widget.
     */
    public function monthlyIncomeChart(): Collection
    {
        return Transaction::paid()
            ->join('departments', 'transactions.department_id', '=', 'departments.id')
            ->selectRaw('MONTH(paid_at) as month, YEAR(paid_at) as year')
            ->selectRaw('SUM(departments.cost) as total, COUNT(*) as count')
            ->whereYear('paid_at', now()->year)
            ->groupByRaw('YEAR(paid_at), MONTH(paid_at)')
            ->orderByRaw('YEAR(paid_at), MONTH(paid_at)')
            ->get();
    }

    /**
     * Daftar transaksi pending untuk widget notifikasi admin.
     */
    public function pendingTransactions(int $limit = 10): Collection
    {
        return Transaction::pending()
            ->with(['user', 'department'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function totalIncomeThisMonth(): float
    {
        return (float) Transaction::query()
            ->where('transactions.payment_status', 'paid')
            ->whereMonth('transactions.created_at', now()->month)
            ->whereYear('transactions.created_at', now()->year)
            ->join('departments', 'transactions.department_id', '=', 'departments.id')
            ->sum('departments.cost');
    }
}
