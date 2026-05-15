<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class PaymentTrendsWidget extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Payment Trends';
    protected static ?string $maxHeight = '320px';
    protected int|string|array $columnSpan = 2;
    protected static ?string $pollingInterval = null;

    // Default filter adalah 6 bulan
    public ?string $filter = '6';

    // 1. Menambahkan Menu Dropdown Filter
    protected function getFilters(): ?array
    {
        return [
            '3' => 'Last 3 Months',
            '6' => 'Last 6 Months',
            '12' => 'Last 12 Months',
        ];
    }

    // 2. Menampilkan Info Ringkasan (Icon 'i' & Deskripsi Dinamis)
    public function getDescription(): string|Htmlable|null
    {
        $monthsCount = (int) ($this->filter ?? 6);

        $startDate = now()->subMonths($monthsCount - 1)->startOfMonth();
        $endDate = now()->endOfMonth();

        // Hitung total pendapatan sesuai range filter
        $totalRevenue = (float) Transaction::paid()
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->join('departments', 'transactions.department_id', '=', 'departments.id')
            ->sum('departments.cost');

        $formattedTotal = number_format($totalRevenue, 0, ',', '.');
        $rangeText = $startDate->format('M Y') . ' - ' . $endDate->format('M Y');

        return "Data periode {$rangeText}. Total Pendapatan SPP: IDR {$formattedTotal}";
    }

    // 3. Mengambil Data Grafik Secara Dinamis
    protected function getData(): array
    {
        $monthsCount = (int) ($this->filter ?? 6);

        // Loop data berdasarkan jumlah bulan yang dipilih di filter
        $data = collect(range($monthsCount - 1, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);

            $total = (float) Transaction::paid()
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->join('departments', 'transactions.department_id', '=', 'departments.id')
                ->sum('departments.cost');

            return [
                'label' => $date->format('M Y'),
                'total' => $total,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (IDR)',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => 'var(--mypp-chart-line)',
                    'backgroundColor' => 'var(--mypp-chart-fill)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4, // Membuat garis grafik melengkung halus (smooth)
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}