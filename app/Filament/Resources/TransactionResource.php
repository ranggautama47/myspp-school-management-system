<?php

namespace App\Filament\Resources;

use App\Enums\TransactionStatus;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Transaction Code')
                            ->disabled()
                            ->columnSpan('full'),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'bank_transfer' => 'Bank Transfer',
                                'e_wallet' => 'E-Wallet',
                                'manual' => 'Manual Payment',
                            ])
                            ->required(),

                        Forms\Components\Select::make('payment_status')
                            ->options([
                                TransactionStatus::Pending->value => TransactionStatus::Pending->label(),
                                TransactionStatus::Paid->value => TransactionStatus::Paid->label(),
                                TransactionStatus::Expired->value => TransactionStatus::Expired->label(),
                            ])
                            ->required(),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Paid At'),

                        Forms\Components\FileUpload::make('proof_of_payment')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->directory('proofs'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.cost')
                    ->label('Amount (IDR)')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format((float) $state, 2, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => $state->color()),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        TransactionStatus::Pending->value => TransactionStatus::Pending->label(),
                        TransactionStatus::Paid->value => TransactionStatus::Paid->label(),
                        TransactionStatus::Expired->value => TransactionStatus::Expired->label(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\TransactionResource\Pages\ListTransactions::route('/'),
            'create' => \App\Filament\Resources\TransactionResource\Pages\CreateTransaction::route('/create'),
            'edit' => \App\Filament\Resources\TransactionResource\Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
