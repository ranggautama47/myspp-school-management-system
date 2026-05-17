<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Enums\TransactionStatus;
use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Payment')
                ->icon('heroicon-o-plus'),
        ];
    }

    // =========================================
    // TABS — All / Pending / Paid / Expired
    // Fix: badge pakai closure fn() => ... bukan static value
    // Supaya badge count selalu fresh setiap render, bukan snapshot saat boot
    // =========================================

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Transactions')
                ->badge(fn() => Transaction::count()),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where('payment_status', TransactionStatus::Pending->value)
                )
                ->badge(fn() => Transaction::pending()->count())
                ->badgeColor('warning'),

            'paid' => Tab::make('Paid')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where('payment_status', TransactionStatus::Paid->value)
                )
                ->badge(fn() => Transaction::paid()->count())
                ->badgeColor('success'),

            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->where('payment_status', TransactionStatus::Expired->value)
                )
                ->badge(fn() => Transaction::expired()->count())
                ->badgeColor('gray'),
        ];
    }
}