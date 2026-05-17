<?php

namespace App\Filament\Pages;

use App\Enums\InvoiceStatus;
use App\Enums\TransactionStatus;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\DB;

class FinanceReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.finance-report';

    // =========================================
    // FILTER STATE
    // =========================================

    public string $period = 'this_month'; // this_month | this_year | custom
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->endOfMonth()->toDateString();
    }

    // =========================================
    // HEADER ACTIONS — filter period
    // =========================================

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label('Filter Period')
                ->icon('heroicon-o-funnel')
                ->form([
                    Select::make('period')
                        ->label('Period')
                        ->options([
                            'this_month' => 'Bulan Ini',
                            'this_year' => 'Tahun Ini',
                            'custom' => 'Custom Range',
                        ])
                        ->default($this->period)
                        ->reactive()
                        ->native(false),

                    DatePicker::make('date_from')
                        ->label('From')
                        ->default($this->dateFrom)
                        ->visible(fn($get) => $get('period') === 'custom'),

                    DatePicker::make('date_to')
                        ->label('Until')
                        ->default($this->dateTo)
                        ->visible(fn($get) => $get('period') === 'custom'),
                ])
                ->action(function (array $data) {
                    $this->period = $data['period'];

                    match ($data['period']) {
                        'this_month' => [
                            $this->dateFrom = now()->startOfMonth()->toDateString(),
                            $this->dateTo = now()->endOfMonth()->toDateString(),
                        ],
                        'this_year' => [
                            $this->dateFrom = now()->startOfYear()->toDateString(),
                            $this->dateTo = now()->endOfYear()->toDateString(),
                        ],
                        'custom' => [
                            $this->dateFrom = $data['date_from'],
                            $this->dateTo = $data['date_to'],
                        ],
                    };
                }),

            // Export CSV action
            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action('exportCsv'),
        ];
    }

    // =========================================
    // DATA GETTERS — dipanggil dari Blade view
    // =========================================

    public function getStats(): array
    {
        $from = $this->dateFrom;
        $to = $this->dateTo;

        $totalRevenue = Transaction::paid()
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        $totalExpense = Expense::whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $totalPaid = Transaction::paid()
            ->whereBetween('paid_at', [$from, $to])
            ->count();

        $totalPending = Transaction::pending()->count();

        $totalInvoiceUnpaid = Invoice::unpaid()->count() + Invoice::overdue()->count();

        $netIncome = $totalRevenue - $totalExpense;

        return [
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_income' => $netIncome,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'total_invoice_unpaid' => $totalInvoiceUnpaid,
        ];
    }

    public function getMonthlyRevenue(): array
    {
        // 6 bulan terakhir — untuk line chart
        return collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $label = $date->format('M Y');

            $revenue = Transaction::paid()
                ->whereMonth('paid_at', $date->month)
                ->whereYear('paid_at', $date->year)
                ->sum('amount');

            $expense = Expense::whereMonth('expense_date', $date->month)
                ->whereYear('expense_date', $date->year)
                ->sum('amount');

            return [
                'month' => $label,
                'revenue' => (float) $revenue,
                'expense' => (float) $expense,
            ];
        })->toArray();
    }

    public function getExpenseByCategory(): array
    {
        $from = $this->dateFrom;
        $to = $this->dateTo;

        return Expense::whereBetween('expense_date', [$from, $to])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get()
            ->map(fn($row) => [
                'category' => \App\Enums\ExpenseCategory::from($row->category)->label(),
                'total' => (float) $row->total,
            ])
            ->toArray();
    }

    public function getRecentTransactions(): \Illuminate\Database\Eloquent\Collection
    {
        return Transaction::with(['user', 'department'])
            ->paid()
            ->whereBetween('paid_at', [$this->dateFrom, $this->dateTo])
            ->latest('paid_at')
            ->limit(10)
            ->get();
    }

    // =========================================
    // EXPORT CSV
    // =========================================

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $from = $this->dateFrom;
        $to = $this->dateTo;

        $transactions = Transaction::with(['user', 'department'])
            ->paid()
            ->whereBetween('paid_at', [$from, $to])
            ->get();

        $filename = 'finance-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($transactions) {
            $handle = fopen('php://output', 'w');

            // Header CSV
            fputcsv($handle, [
                'Transaction Code',
                'Student Name',
                'Department',
                'Amount (IDR)',
                'Payment Method',
                'Paid At',
            ]);

            foreach ($transactions as $trx) {
                fputcsv($handle, [
                    $trx->code,
                    $trx->user?->name ?? '-',
                    $trx->department?->name ?? '-',
                    (int) $trx->amount,
                    $trx->payment_method ?? '-',
                    $trx->paid_at?->format('Y-m-d H:i') ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}