<?php

namespace App\Filament\Pages;

use App\Enums\ExpenseCategory;
use App\Enums\InvoiceStatus;
use App\Enums\TransactionStatus;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
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

    public string $period = 'this_month';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->endOfMonth()->toDateString();
    }

    // =========================================
    // HELPER — query paid transactions
    // FIX: pakai COALESCE(paid_at, updated_at) agar transaksi
    // yang approved manual (paid_at NULL) tetap masuk hitungan.
    // Kalau paid_at ada → pakai paid_at
    // Kalau paid_at NULL → fallback ke updated_at (saat status diubah)
    // =========================================

    private function paidTransactionsInPeriod(string $from, string $to)
    {
        return Transaction::paid()
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('paid_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                    ->orWhere(function ($q) use ($from, $to) {
                        $q->whereNull('paid_at')
                            ->whereBetween('updated_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                    });
            });
    }

    // =========================================
    // HEADER ACTIONS
    // =========================================

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->label('Filter Period')
                ->icon('heroicon-o-funnel')
                ->color('primary')
                ->form([
                    Select::make('period')
                        ->label('Period')
                        ->options([
                            'this_month' => 'Bulan Ini',
                            'this_year' => 'Tahun Ini',
                            'custom' => 'Custom Range',
                        ])
                        ->default($this->period)
                        ->live()
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

            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action('exportCsv'),
        ];
    }

    // =========================================
    // STATS — semua pakai helper paidTransactionsInPeriod()
    // =========================================

    public function getStats(): array
    {
        $from = $this->dateFrom;
        $to = $this->dateTo;

        $totalRevenue = $this->paidTransactionsInPeriod($from, $to)->sum('amount');
        $totalExpense = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $totalPaid = $this->paidTransactionsInPeriod($from, $to)->count();
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

    // =========================================
    // CHART — 6 bulan terakhir
    // FIX: pakai helper yang sama agar konsisten
    // =========================================

    public function getMonthlyRevenue(): array
    {
        return collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $from = $date->copy()->startOfMonth()->toDateString();
            $to = $date->copy()->endOfMonth()->toDateString();
            $label = $date->format('M Y');

            $revenue = $this->paidTransactionsInPeriod($from, $to)->sum('amount');

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

    // =========================================
    // EXPENSE BY CATEGORY
    // =========================================

    public function getExpenseByCategory(): array
    {
        $from = $this->dateFrom;
        $to = $this->dateTo;

        return Expense::whereBetween('expense_date', [$from, $to])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get()
            ->map(fn($row) => [
                'category' => ExpenseCategory::from($row->category)->label(),
                'total' => (float) $row->total,
            ])
            ->toArray();
    }

    // =========================================
    // RECENT TRANSACTIONS — pakai helper
    // =========================================

    public function getRecentTransactions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->paidTransactionsInPeriod($this->dateFrom, $this->dateTo)
            ->with(['user', 'department'])
            ->latest('updated_at')
            ->limit(10)
            ->get();
    }

    // =========================================
    // EXPORT CSV
    // FIX: pakai helper paidTransactionsInPeriod()
    //      agar data yang paid_at NULL tetap ikut ter-export
    //      Tambah kolom Status + Semester untuk kelengkapan laporan
    // =========================================

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $from = $this->dateFrom;
        $to = $this->dateTo;

        $transactions = $this->paidTransactionsInPeriod($from, $to)
            ->with(['user', 'department'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $filename = 'finance-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($transactions, $from, $to) {
            // BOM UTF-8 agar Excel baca karakter Indonesia dengan benar
            echo "\xEF\xBB\xBF";

            $handle = fopen('php://output', 'w');

            // Info periode di baris pertama
            fputcsv($handle, ['Finance Report — MySPP']);
            fputcsv($handle, ['Period', $from . ' s/d ' . $to]);
            fputcsv($handle, ['Generated At', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, []); // baris kosong pemisah

            // Header kolom
            fputcsv($handle, [
                'No',
                'Transaction Code',
                'Student Name',
                'Department',
                'Semester',
                'Amount (IDR)',
                'Payment Method',
                'Status',
                'Paid At',
                'Created At',
            ]);

            $no = 1;
            foreach ($transactions as $trx) {
                fputcsv($handle, [
                    $no++,
                    $trx->code,
                    $trx->user?->name ?? '-',
                    $trx->department?->name ?? '-',
                    $trx->department?->semester ? 'Semester ' . $trx->department->semester : '-',
                    (int) $trx->amount,
                    match ($trx->payment_method) {
                        'bank_transfer' => 'Bank Transfer',
                        'e_wallet' => 'E-Wallet',
                        'manual' => 'Manual',
                        default => $trx->payment_method ?? '-',
                    },
                    $trx->payment_status->label(),
                    $trx->paid_at?->format('Y-m-d H:i') ?? $trx->updated_at?->format('Y-m-d H:i') . ' (approved)',
                    $trx->created_at?->format('Y-m-d H:i') ?? '-',
                ]);
            }

            // Baris kosong + summary total di akhir
            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', 'TOTAL', $transactions->sum('amount'), '', '', '', '']);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}