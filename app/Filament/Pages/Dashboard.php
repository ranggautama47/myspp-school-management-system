<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PaymentOverviewWidget;
use App\Filament\Widgets\PaymentTrendsWidget;
use App\Filament\Widgets\RecentTransactionsWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = -1;

    protected static string $routePath = '/';
    protected static ?string $title = 'Dashboard';

    // =========================================
    // SUBHEADING — nama dinamis dari user login
    // =========================================

    public function getHeading(): string
    {
        return 'Dashboard';
    }

    public function getSubheading(): ?string
    {
        $name = Auth::user()?->name ?? 'Admin';
        $firstName = explode(' ', $name)[0];

        return "Welcome back, {$firstName}! Here's what's happening at your school.";
    }

    // =========================================
    // HEADER ACTIONS — date range picker kompak
    // di pojok KANAN ATAS, sejajar heading
    // Tampil: 📅 May 1 – May 31, 2026 ▼
    // =========================================

    protected function getHeaderActions(): array
    {
        return [
            Action::make('dateRange')
                ->label($this->getDateRangeLabel())
                ->icon('heroicon-o-calendar-days')
                ->color('gray')
                ->outlined()
                ->form([
                    Grid::make(2)->schema([
                        DatePicker::make('startDate')
                            ->label('From')
                            ->default(now()->startOfMonth())
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->maxDate(now())
                            ->closeOnDateSelection(),

                        DatePicker::make('endDate')
                            ->label('To')
                            ->default(now()->endOfMonth())
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->maxDate(now()->addYears(5))
                            ->closeOnDateSelection(),
                    ]),
                ])
                ->fillForm([
                    'startDate' => session('dashboard_start', now()->startOfMonth()->format('Y-m-d')),
                    'endDate' => session('dashboard_end', now()->endOfMonth()->format('Y-m-d')),
                ])
                ->action(function (array $data) {
                    session([
                        'dashboard_start' => $data['startDate'],
                        'dashboard_end' => $data['endDate'],
                    ]);
                    // Only refresh the widgets that depend on date range
                    $this->dispatch('dateRangeUpdated');
                })
                ->modalHeading('Select Date Range')
                ->modalSubmitActionLabel('Apply')
                ->modalWidth('md'),
        ];
    }

    // Label tombol: "May 1 – May 31, 2026"
    private function getDateRangeLabel(): string
    {
        $start = session('dashboard_start', now()->startOfMonth()->format('Y-m-d'));
        $end = session('dashboard_end', now()->endOfMonth()->format('Y-m-d'));

        return \Carbon\Carbon::parse($start)->format('M j') . ' – ' . \Carbon\Carbon::parse($end)->format('M j, Y');
    }

    // =========================================
    // FILTER FORM — tetap ada tapi tersembunyi
    // (dibutuhkan trait HasFiltersForm)
    // =========================================

    public function filtersForm(Form $form): Form
    {
        return $form->schema([]);
    }


    // =========================================
    // WIDGETS — urutan sesuai blueprint:
    // Row 1: Stats (4 cards full width)
    // Row 2: Payment Trends (kiri 2/3) + Payment Overview (kanan 1/3)
    // Row 3: Recent Transactions (full width)
    // =========================================

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            PaymentTrendsWidget::class,
            PaymentOverviewWidget::class,
            RecentTransactionsWidget::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }
}
