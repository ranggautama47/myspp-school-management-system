<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PaymentOverviewWidget;
use App\Filament\Widgets\RecentTransactionsWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int    $navigationSort  = -1;

    protected static string $routePath = '/';
    protected static ?string $title    = 'Dashboard';

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            \Filament\Forms\Components\DatePicker::make('startDate')
                ->label('From')
                ->default(now()->startOfMonth()),

            \Filament\Forms\Components\DatePicker::make('endDate')
                ->label('To')
                ->default(now()->endOfMonth()),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            PaymentOverviewWidget::class,
            RecentTransactionsWidget::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 4,
        ];
    }

    /**
     * Heading + subheading sesuai blueprint
     */
    public function getHeading(): string
    {
        return 'Dashboard';
    }

    public function getSubheading(): ?string
    {
        return 'Welcome back, Admin! Here\'s what\'s happening at your school.';
    }
}
