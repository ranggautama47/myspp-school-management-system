<?php

namespace App\Filament\Widgets;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactionsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Recent Transactions';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn() => Transaction::query()
                    ->with(['user', 'department'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                // Avatar + Nama Siswa
                Tables\Columns\ImageColumn::make('user.image')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(
                        fn($record) =>
                        'https://ui-avatars.com/api/?name=' . urlencode($record->user?->name ?? 'U') . '&background=1D9E75&color=fff'
                    )
                    ->size(36),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student Name')
                    ->description(fn($record) => 'STU-' . str_pad($record->user_id, 4, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount') // Pastikan konsisten menggunakan 'amount'
                    ->label('Amount (IDR)')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')) // 0 desimal agar sama dengan dashboard
                    ->color('emerald'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->color(
                        fn($record) =>
                        $record->payment_status === TransactionStatus::Expired ? 'danger' : null
                    )
                    ->sortable(),

                // Badge status dengan warna dari Enum
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => $state->color()),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->iconButton()
                    ->url(fn($record) => route('filament.admin.resources.transactions.index')),
            ])
            ->paginated(false)
            ->striped();
    }
}
