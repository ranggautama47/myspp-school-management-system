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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FinanceReport extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int    $navigationSort  = 4;

    protected static string $view = 'filament.pages.finance-report';

    // =========================================
    // FILTER STATE
    // =========================================

    public string  $period     = 'this_month';
    public ?string $dateFrom   = null;
    public ?string $dateTo     = null;
    public int     $chartMonths = 6;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo   = now()->endOfMonth()->toDateString();
    }

    // =========================================
    // HELPER — paid transactions fallback paid_at NULL
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
                            'this_year'  => 'Tahun Ini',
                            'custom'     => 'Custom Range',
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
                            $this->dateTo   = now()->endOfMonth()->toDateString(),
                        ],
                        'this_year' => [
                            $this->dateFrom = now()->startOfYear()->toDateString(),
                            $this->dateTo   = now()->endOfYear()->toDateString(),
                        ],
                        'custom' => [
                            $this->dateFrom = $data['date_from'],
                            $this->dateTo   = $data['date_to'],
                        ],
                    };
                }),

            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('exportExcel'),
        ];
    }

    // =========================================
    // STATS
    // =========================================

    public function getStats(): array
    {
        $from = $this->dateFrom;
        $to   = $this->dateTo;

        $totalRevenue       = $this->paidTransactionsInPeriod($from, $to)->sum('amount');
        $totalExpense       = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');
        $totalPaid          = $this->paidTransactionsInPeriod($from, $to)->count();
        $totalPending       = Transaction::pending()->count();
        $totalInvoiceUnpaid = Invoice::unpaid()->count() + Invoice::overdue()->count();
        $netIncome          = $totalRevenue - $totalExpense;

        return [
            'total_revenue'        => $totalRevenue,
            'total_expense'        => $totalExpense,
            'net_income'           => $netIncome,
            'total_paid'           => $totalPaid,
            'total_pending'        => $totalPending,
            'total_invoice_unpaid' => $totalInvoiceUnpaid,
        ];
    }

    public function updatedChartMonths()
    {
        $this->dispatch('refresh-chart');
    }

    // =========================================
    // CHART DATA
    // =========================================

    public function getMonthlyRevenue(): array
    {
        $data = [];
        $limit = $this->chartMonths; // ambil dari dropdown (3, 6, atau 12)

        // Mulai dari 0 sampai limit-1 (misal limit=3 → 0,1,2 = 3 bulan)
        for ($i = 0; $i < $limit; $i++) {
            $month = now()->subMonths($limit - 1 - $i);
            $monthName = $month->translatedFormat('M Y');

            $revenue = Transaction::where('payment_status', TransactionStatus::Paid)
                ->whereMonth('paid_at', $month->month)
                ->whereYear('paid_at', $month->year)
                ->sum('amount');

            $expense = Expense::whereMonth('expense_date', $month->month)
                ->whereYear('expense_date', $month->year)
                ->sum('amount');

            $data[] = [
                'month' => $monthName,
                'revenue' => (float) $revenue,
                'expense' => (float) $expense,
            ];
        }

        return $data;
    }

    public function getChartTotals(): array
    {
        $monthly = $this->getMonthlyRevenue();

        return [
            'revenue' => collect($monthly)->sum('revenue'),
            'expense' => collect($monthly)->sum('expense'),
        ];
    }

    public function getExpenseByCategory(): array
    {
        return Expense::query()
            // Ganti angka 6 menjadi dinamis ($this->chartMonths)
            ->where('expense_date', '>=', now()->subMonths($this->chartMonths))
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'category' => $item->category->label(),
                'total' => (float) $item->total,
            ])
            ->toArray();
    }

    public function getRecentTransactions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->paidTransactionsInPeriod($this->dateFrom, $this->dateTo)
            ->with(['user', 'department'])
            ->latest('updated_at')
            ->limit(10)
            ->get();
    }

    // =========================================
    // EXPORT EXCEL — PhpSpreadsheet
    // Install dulu: composer require phpoffice/phpspreadsheet
    // =========================================

    public function exportExcel(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $from = $this->dateFrom;
        $to   = $this->dateTo;

        $transactions = $this->paidTransactionsInPeriod($from, $to)
            ->with(['user', 'department'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $expenses = Expense::with('recorder')
            ->whereBetween('expense_date', [$from, $to])
            ->orderBy('expense_date', 'desc')
            ->get();

        $stats = $this->getStats();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Finance Report MySPP')
            ->setSubject('Laporan Keuangan')
            ->setCreator('MySPP System');

        // ── SHEET 1: PAYMENTS ─────────────────────────────────────────
        $ws = $spreadsheet->getActiveSheet();
        $ws->setTitle('Payments');
        $ws->setShowGridlines(false);
        $ws->freezePane('A11');
        $ws->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToPage(true)
            ->setFitToWidth(1);

        $s = fn(string $range, array $style) => $ws->getStyle($range)->applyFromArray($style);

        // Helper style arrays
        $borderThin = ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCBD5E1']]];

        // Row 1 — Judul
        $ws->mergeCells('A1:J1');
        $ws->setCellValue('A1', 'LAPORAN KEUANGAN  -  MySPP School Management System');
        $ws->getRowDimension(1)->setRowHeight(48);
        $s('A1:J1', [
            'font'      => ['bold' => true, 'size' => 18, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Row 2 — Subtitle
        $ws->mergeCells('A2:J2');
        $ws->setCellValue('A2', 'Finance Report  -  Rekap Pembayaran SPP');
        $ws->getRowDimension(2)->setRowHeight(20);
        $s('A2:J2', [
            'font'      => ['size' => 10, 'italic' => true, 'color' => ['argb' => 'FF94A3B8'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Row 3 — Info periode
        $ws->mergeCells('A3:E3');
        $ws->setCellValue('A3', 'Periode: ' . $from . '  s/d  ' . $to);
        $ws->mergeCells('F3:J3');
        $ws->setCellValue('F3', 'Generated: ' . now()->format('Y-m-d H:i:s'));
        $ws->getRowDimension(3)->setRowHeight(22);
        $s('A3:J3', [
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF059669']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1);
        $ws->getStyle('F3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setIndent(1);

        // Row 4 — Spacer
        $ws->mergeCells('A4:J4');
        $ws->getRowDimension(4)->setRowHeight(14);
        $s('A4:J4', ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']]]);

        // Row 5-7 — Summary cards
        $ws->getRowDimension(5)->setRowHeight(18);
        $ws->getRowDimension(6)->setRowHeight(34);
        $ws->getRowDimension(7)->setRowHeight(20);

        // Card Revenue (A-C)
        foreach (['A5:C5', 'A6:C6', 'A7:C7'] as $r) {
            $ws->mergeCells($r);
            $s($r, ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF064E3B']]]);
        }
        $ws->setCellValue('A5', 'TOTAL REVENUE');
        $ws->setCellValue('A6', (float) $stats['total_revenue']);
        $ws->getStyle('A6')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $ws->setCellValue('A7', $stats['total_paid'] . ' transaksi lunas dalam periode ini');
        $s('A5', [
            'font' => ['bold' => true, 'size' => 9,  'color' => ['argb' => 'FF6EE7B7'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1]
        ]);
        $s('A6', [
            'font' => ['bold' => true, 'size' => 20, 'color' => ['argb' => 'FF10B981'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $s('A7', [
            'font' => ['size' => 9, 'italic' => true, 'color' => ['argb' => 'FF6EE7B7'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);

        // Card Expenses (D-F)
        foreach (['D5:F5', 'D6:F6', 'D7:F7'] as $r) {
            $ws->mergeCells($r);
            $s($r, ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4C0519']]]);
        }
        $ws->setCellValue('D5', 'TOTAL EXPENSES');
        $ws->setCellValue('D6', (float) $stats['total_expense']);
        $ws->getStyle('D6')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $ws->setCellValue('D7', $expenses->count() . ' pengeluaran dicatat');
        $s('D5', [
            'font' => ['bold' => true, 'size' => 9,  'color' => ['argb' => 'FFFCA5A5'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1]
        ]);
        $s('D6', [
            'font' => ['bold' => true, 'size' => 20, 'color' => ['argb' => 'FFF43F5E'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $s('D7', [
            'font' => ['size' => 9, 'italic' => true, 'color' => ['argb' => 'FFFCA5A5'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);

        // Card Net Income (G-J)
        foreach (['G5:J5', 'G6:J6', 'G7:J7'] as $r) {
            $ws->mergeCells($r);
            $s($r, ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0C4A6E']]]);
        }
        $netColor = ((float) $stats['net_income'] >= 0) ? 'FF38BDF8' : 'FFF43F5E';
        $ws->setCellValue('G5', 'NET INCOME');
        $ws->setCellValue('G6', (float) $stats['net_income']);
        $ws->getStyle('G6')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $ws->setCellValue('G7', 'Revenue - Expenses');
        $s('G5', [
            'font' => ['bold' => true, 'size' => 9,  'color' => ['argb' => 'FFBAE6FD'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1]
        ]);
        $s('G6', [
            'font' => ['bold' => true, 'size' => 20, 'color' => ['argb' => $netColor], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $s('G7', [
            'font' => ['size' => 9, 'italic' => true, 'color' => ['argb' => 'FFBAE6FD'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);

        // Row 8-9 Spacer
        foreach ([8, 9] as $r) {
            $ws->mergeCells("A{$r}:J{$r}");
            $ws->getRowDimension($r)->setRowHeight(12);
            $s("A{$r}:J{$r}", ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']]]);
        }

        // Row 10 — Table header
        $ws->getRowDimension(10)->setRowHeight(28);
        $cols10 = [
            'A' => 'No',
            'B' => 'Kode Transaksi',
            'C' => 'Nama Siswa',
            'D' => 'Jurusan',
            'E' => 'Semester',
            'F' => 'Nominal (IDR)',
            'G' => 'Metode Bayar',
            'H' => 'Status',
            'I' => 'Tanggal Bayar',
            'J' => 'Dibuat'
        ];
        foreach ($cols10 as $col => $label) {
            $ws->setCellValue("{$col}10", $label);
        }
        $s('A10:J10', [
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF334155']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => $borderThin,
        ]);

        // Rows 11+ — Data
        $dataRow = 11;
        foreach ($transactions as $i => $trx) {
            $ws->getRowDimension($dataRow)->setRowHeight(22);
            $bg = ($i % 2 === 0) ? 'FFFFFFFF' : 'FFF1F5F9';

            $paidAt = $trx->paid_at
                ? $trx->paid_at->format('Y-m-d H:i')
                : ($trx->updated_at ? $trx->updated_at->format('Y-m-d H:i') . ' *' : '-');

            $rowData = [
                'A' => $i + 1,
                'B' => $trx->code,
                'C' => $trx->user?->name ?? '-',
                'D' => $trx->department?->name ?? '-',
                'E' => $trx->department?->semester ? 'Semester ' . $trx->department->semester : '-',
                'F' => (float) $trx->amount,
                'G' => match ($trx->payment_method) {
                    'bank_transfer' => 'Bank Transfer',
                    'e_wallet'      => 'E-Wallet',
                    'manual'        => 'Manual',
                    default         => $trx->payment_method ?? '-',
                },
                'H' => $trx->payment_status->label(),
                'I' => $paidAt,
                'J' => $trx->created_at?->format('Y-m-d') ?? '-',
            ];

            foreach ($rowData as $col => $val) {
                $ws->setCellValue("{$col}{$dataRow}", $val);
                $isCenter = in_array($col, ['A', 'E', 'G', 'H', 'I', 'J']);
                $s("{$col}{$dataRow}", [
                    'font'      => ['size' => 10, 'name' => 'Calibri', 'color' => ['argb' => 'FF1E293B']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . ltrim($bg, 'FF')]],
                    'alignment' => [
                        'horizontal' => $isCenter ? Alignment::HORIZONTAL_CENTER : Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'indent'     => in_array($col, ['B', 'C', 'D']) ? 1 : 0,
                    ],
                    'borders' => $borderThin,
                ]);
            }

            // Amount — hijau, right align, format rupiah
            $ws->getStyle("F{$dataRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $s("F{$dataRow}", [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FF059669']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ]);

            // Status badge — background hijau muda
            $s("H{$dataRow}", [
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => 'FF065F46']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1FAE5']],
            ]);

            $dataRow++;
        }

        // Total row
        $ws->getRowDimension($dataRow)->setRowHeight(28);
        $ws->mergeCells("A{$dataRow}:E{$dataRow}");
        $ws->setCellValue("A{$dataRow}", 'TOTAL PEMBAYARAN');
        $ws->setCellValue("F{$dataRow}", '=SUM(F11:F' . ($dataRow - 1) . ')');
        $ws->getStyle("F{$dataRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $s("A{$dataRow}:J{$dataRow}", [
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF334155']],
            'borders'   => $borderThin,
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws->getStyle("A{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $s("F{$dataRow}", [
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);

        // Catatan kaki
        $noteRow = $dataRow + 2;
        $ws->mergeCells("A{$noteRow}:J{$noteRow}");
        $ws->setCellValue("A{$noteRow}", '* Tanggal bertanda (*) = paid_at tidak tercatat, referensi dari waktu approval. Digenerate otomatis oleh MySPP.');
        $s("A{$noteRow}:J{$noteRow}", [
            'font'      => ['size' => 9, 'italic' => true, 'color' => ['argb' => 'FF64748B'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);

        // Column widths Sheet 1
        foreach (
            [
                'A' => 5,
                'B' => 22,
                'C' => 22,
                'D' => 22,
                'E' => 12,
                'F' => 18,
                'G' => 15,
                'H' => 14,
                'I' => 20,
                'J' => 14
            ] as $col => $w
        ) {
            $ws->getColumnDimension($col)->setWidth($w);
        }

        // ── SHEET 2: EXPENSES ─────────────────────────────────────────
        $ws2 = $spreadsheet->createSheet();
        $ws2->setTitle('Expenses');
        $ws2->setShowGridlines(false);
        $ws2->freezePane('A5');
        $ws2->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToPage(true)
            ->setFitToWidth(1);

        $s2 = fn(string $range, array $style) => $ws2->getStyle($range)->applyFromArray($style);

        // Row 1 — Judul
        $ws2->mergeCells('A1:G1');
        $ws2->setCellValue('A1', 'LAPORAN PENGELUARAN  -  MySPP');
        $ws2->getRowDimension(1)->setRowHeight(40);
        $s2('A1:G1', [
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Row 2 — Periode
        $ws2->mergeCells('A2:G2');
        $ws2->setCellValue('A2', 'Periode: ' . $from . '  s/d  ' . $to);
        $ws2->getRowDimension(2)->setRowHeight(20);
        $s2('A2:G2', [
            'font'      => ['size' => 10, 'italic' => true, 'color' => ['argb' => 'FF94A3B8'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Row 3 — Spacer
        $ws2->mergeCells('A3:G3');
        $ws2->getRowDimension(3)->setRowHeight(12);
        $s2('A3:G3', ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']]]);

        // Row 4 — Header tabel
        $ws2->getRowDimension(4)->setRowHeight(26);
        foreach (
            [
                'A' => 'No',
                'B' => 'Nama Pengeluaran',
                'C' => 'Kategori',
                'D' => 'Nominal (IDR)',
                'E' => 'Tanggal',
                'F' => 'Dicatat Oleh',
                'G' => 'Keterangan'
            ] as $col => $label
        ) {
            $ws2->setCellValue("{$col}4", $label);
        }
        $s2('A4:G4', [
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4C0519']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => $borderThin,
        ]);

        // Warna badge per kategori
        $catColors = [
            'operational' => ['FFEFF6FF', 'FF1D4ED8'],
            'utilities'   => ['FFF0FDF4', 'FF166534'],
            'maintenance' => ['FFFFFBEB', 'FF92400E'],
            'equipment'   => ['FFFAF5FF', 'FF6B21A8'],
            'salary'      => ['FFFFF1F2', 'FF9F1239'],
            'event'       => ['FFFEF3C7', 'FF92400E'],
            'other'       => ['FFF8FAFC', 'FF334155'],
        ];

        $expRow = 5;
        foreach ($expenses as $i => $exp) {
            $ws2->getRowDimension($expRow)->setRowHeight(22);
            $bg2    = ($i % 2 === 0) ? 'FFFFFFFF' : 'FFF1F5F9';
            $catKey = $exp->category instanceof ExpenseCategory ? $exp->category->value : $exp->category;
            [$catBg, $catFg] = $catColors[$catKey] ?? ['FFF8FAFC', 'FF334155'];

            $catLabel = $exp->category instanceof ExpenseCategory ? $exp->category->label() : $exp->category;

            $expData = [
                'A' => $i + 1,
                'B' => $exp->name,
                'C' => $catLabel,
                'D' => (float) $exp->amount,
                'E' => $exp->expense_date?->format('Y-m-d') ?? '-',
                'F' => $exp->recorder?->name ?? '-',
                'G' => $exp->notes ?? '-',
            ];

            foreach ($expData as $col => $val) {
                $ws2->setCellValue("{$col}{$expRow}", $val);
                $isCenter2 = in_array($col, ['A', 'C', 'E', 'F']);
                $s2("{$col}{$expRow}", [
                    'font'      => ['size' => 10, 'name' => 'Calibri', 'color' => ['argb' => 'FF1E293B']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg2]],
                    'alignment' => [
                        'horizontal' => $isCenter2 ? Alignment::HORIZONTAL_CENTER : Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'indent'     => in_array($col, ['B', 'G']) ? 1 : 0,
                    ],
                    'borders' => $borderThin,
                ]);
            }

            // Nominal — merah + rupiah
            $ws2->getStyle("D{$expRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $s2("D{$expRow}", [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFDC2626']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ]);

            // Kategori — badge warna
            $s2("C{$expRow}", [
                'font' => ['bold' => true, 'size' => 9, 'color' => ['argb' => $catFg]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $catBg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $expRow++;
        }

        // Total expenses row
        $ws2->getRowDimension($expRow)->setRowHeight(28);
        $ws2->mergeCells("A{$expRow}:C{$expRow}");
        $ws2->setCellValue("A{$expRow}", 'TOTAL PENGELUARAN');
        $ws2->setCellValue("D{$expRow}", '=SUM(D5:D' . ($expRow - 1) . ')');
        $ws2->getStyle("D{$expRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $s2("A{$expRow}:G{$expRow}", [
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4C0519']],
            'borders'   => $borderThin,
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $ws2->getStyle("A{$expRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $s2("D{$expRow}", [
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFF43F5E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);

        // Column widths Sheet 2
        foreach (['A' => 5, 'B' => 32, 'C' => 16, 'D' => 18, 'E' => 14, 'F' => 16, 'G' => 32] as $col => $w) {
            $ws2->getColumnDimension($col)->setWidth($w);
        }

        // ── STREAM ────────────────────────────────────────────────────
        $spreadsheet->setActiveSheetIndex(0);
        $filename = 'laporan-keuangan-myspp-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
