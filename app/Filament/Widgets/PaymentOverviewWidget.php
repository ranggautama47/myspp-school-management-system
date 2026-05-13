<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PaymentOverviewWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Payment Overview';

    protected static ?string $maxHeight = '300px';

    protected int|string|array $columnSpan = 1;

    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $paid = Transaction::paid()->count();
        $pending = Transaction::pending()->count();
        $failed = Transaction::query()
            ->whereIn('payment_status', [TransactionStatus::Failed, TransactionStatus::Expired])
            ->count();

        $total = $paid + $pending + $failed;

        return [
            'datasets' => [
                [
                    'data' => [$paid, $pending, $failed],
                    'backgroundColor' => ['#10B981', '#F59E0B', '#F43F5E'],
                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => [
                "Paid ({$paid})",
                "Pending ({$pending})",
                "Failed / Expired ({$failed})",
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
                    'position' => 'right',
                    'labels' => ['usePointStyle' => true, 'padding' => 16],
                ],
            ],
            'cutout' => '70%',
        ];
    }
}
