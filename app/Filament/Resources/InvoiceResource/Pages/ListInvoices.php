<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\InvoiceStatus;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Invoice')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Invoices')
                ->badge(fn() => \App\Models\Invoice::count()),

            'unpaid' => Tab::make('Unpaid')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', InvoiceStatus::Unpaid->value))
                ->badge(fn() => \App\Models\Invoice::unpaid()->count())
                ->badgeColor('warning'),

            'overdue' => Tab::make('Overdue')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', InvoiceStatus::Overdue->value))
                ->badge(fn() => \App\Models\Invoice::overdue()->count())
                ->badgeColor('danger'),

            'paid' => Tab::make('Paid')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', InvoiceStatus::Paid->value))
                ->badge(fn() => \App\Models\Invoice::paid()->count())
                ->badgeColor('success'),
        ];
    }
}