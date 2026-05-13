<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\User;
use App\Services\ReportService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    // Refresh otomatis setiap 30 detik
    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $summary = app(ReportService::class)->monthlySummary();

        $lastMonthIncome = (float) Transaction::paid()
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->join('departments', 'transactions.department_id', '=', 'departments.id')
            ->sum('departments.cost');

        $incomeGrowth = $lastMonthIncome > 0
            ? round((($summary['total_income'] - $lastMonthIncome) / $lastMonthIncome) * 100, 1)
            : 0;

        return [
            // Stat 1: Total Revenue
            Stat::make('Total Revenue (SPP)', 'Rp ' . number_format($summary['total_income'], 0, ',', '.'))
                ->description($incomeGrowth >= 0
                    ? "↑ {$incomeGrowth}% vs bulan lalu"
                    : "↓ " . abs($incomeGrowth) . "% vs bulan lalu")
                ->descriptionIcon($incomeGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($incomeGrowth >= 0 ? 'success' : 'danger')
                ->chart($this->getIncomeSparkline()),

            // Stat 2: Active Students
            Stat::make('Active Students', number_format($summary['total_student']))
                ->description('Siswa terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            // Stat 3: Pending Payments
            Stat::make('Pending Payments', number_format($summary['total_pending']))
                ->description('Menunggu pembayaran')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            // Stat 4: Paid This Month
            Stat::make('Paid This Month', number_format($summary['total_paid']))
                ->description('Pembayaran bulan ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }

    /**
     * Data sparkline 7 hari terakhir untuk chart kecil di stat card
     */
    private function getIncomeSparkline(): array
    {
        return Transaction::paid()
            ->selectRaw('DATE(paid_at) as date, COUNT(*) as count')
            ->where('paid_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();
    }
}
