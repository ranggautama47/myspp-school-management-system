<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class PaymentOverviewWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Payment Overview';

    // Fix: dihapus karena sering menyebabkan resize loop (kedutan) pada doughnut chart

    protected int|string|array $columnSpan = 1;

    protected static ?string $pollingInterval = null;

    public function getDescription(): string|Htmlable|null
    {
        $total = Transaction::count();
        return 'Total ' . number_format($total, 0, ',', '.') . ' transactions';
    }

    protected function getData(): array
    {
        $paid = Transaction::paid()->count();
        $pending = Transaction::pending()->count();
        $failed = Transaction::query()
            ->whereIn('payment_status', [
                TransactionStatus::Failed,
                TransactionStatus::Expired,
            ])
            ->count();

        $total = max($paid + $pending + $failed, 1);

        $paidPct = round($paid / $total * 100, 1);
        $pendingPct = round($pending / $total * 100, 1);
        $failedPct = round(100 - $paidPct - $pendingPct, 1);

        return [
            'datasets' => [
                [
                    'data' => [$paid, $pending, $failed],
                    'backgroundColor' => ['#10B981', '#F59E0B', '#F43F5E'],
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                    'hoverBorderWidth' => 2,
                    'hoverBorderColor' => '#1E293B',
                ],
            ],
            'labels' => [
                "Paid      {$paid} ({$paidPct}%)",
                "Pending   {$pending} ({$pendingPct}%)",
                "Failed    {$failed} ({$failedPct}%)",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'align' => 'center',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'padding' => 16,
                        'font' => ['size' => 12],
                        'boxWidth' => 8,
                        'boxHeight' => 8,
                    ],
                ],
                'tooltip' => ['enabled' => true],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'cutout' => '65%',
            'maintainAspectRatio' => false,
            'responsive' => true,

            // Layout padding — beri ruang ekstra bawah untuk legend
            'layout' => [
                'padding' => [
                    'bottom' => 8,
                ],
            ],
        ];
    }
}