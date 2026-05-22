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
            // Label "Manual Payment" lebih jelas dari "New Payment"
            // Menjelaskan ke admin: ini untuk bayar tunai/transfer manual
            // bukan via Midtrans Snap
            Actions\CreateAction::make()
                ->label('Manual Payment')
                ->icon('heroicon-o-banknotes')
                ->color('gray'),
        ];
    }

    // =========================================
    // TABS — All / Pending / Paid / Expired
    // Badge pakai closure fn() agar selalu fresh
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
