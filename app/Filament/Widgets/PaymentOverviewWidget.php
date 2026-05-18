<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class PaymentOverviewWidget extends ChartWidget
{
    // ISOLASI TOTAL: Prevent widget from any Livewire events that could trigger refresh
    protected $listeners = [];

    protected static ?int $sort = 3;
    protected static ?string $heading = 'Payment Overview';
    protected static ?string $maxHeight = '380px';
    protected int|string|array $columnSpan = 1;

    // FIX: Override polling to ensure no automatic refresh
    protected static ?string $pollingInterval = null;

    // Ensure widget only renders once and stays stable
    protected function shouldRender(): bool
    {
        return true;
    }

    public static function canView(): bool
    {
        return true;
    }

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
            'maintainAspectRatio' => true,
            'responsive' => true,
            'layout' => ['padding' => ['bottom' => 8]],
            // FIX: Disable animations to prevent re-rendering loops
            'animation' => false,
            'animations' => false,
        ];
    }

}
