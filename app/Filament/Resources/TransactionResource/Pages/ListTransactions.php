<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Resources\Components\Tab::make('All Transactions')
                ->modifyQueryUsing(fn($query) => $query->with(['department', 'user'])),

            'pending' => \Filament\Resources\Components\Tab::make('Pending')
                ->modifyQueryUsing(fn($query) => $query->with(['department', 'user'])
                    ->where('payment_status', \App\Enums\TransactionStatus::Pending))
                ->badge(\App\Models\Transaction::where('payment_status', \App\Enums\TransactionStatus::Pending)->count())
                ->badgeColor('warning'),

            'paid' => \Filament\Resources\Components\Tab::make('Paid')
                ->modifyQueryUsing(fn($query) => $query->with(['department', 'user'])
                    ->where('payment_status', \App\Enums\TransactionStatus::Paid))
                ->badge(\App\Models\Transaction::where('payment_status', \App\Enums\TransactionStatus::Paid)->count())
                ->badgeColor('success'),

            'expired' => \Filament\Resources\Components\Tab::make('Expired')
                ->modifyQueryUsing(fn($query) => $query->with(['department', 'user'])
                    ->where('payment_status', \App\Enums\TransactionStatus::Expired))
                ->badgeColor('danger'),
        ];
    }
}
