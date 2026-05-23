<?php

namespace App\Filament\Resources;

use App\Enums\TransactionStatus;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon   = 'heroicon-o-document-text';
    protected static ?string $navigationGroup  = 'Finance';
    protected static ?string $navigationLabel  = 'Payments';
    protected static ?string $modelLabel       = 'Payment';
    protected static ?string $pluralModelLabel = 'Payments';
    protected static ?int    $navigationSort   = 1;

    protected static ?string $recordTitleAttribute = 'code';

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'user.name'];
    }

    // =========================================
    // FORM
    // =========================================

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Transaction Information')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Transaction Code')
                        ->disabled()
                        ->placeholder('Generated automatically')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('user_id')
                        ->label('Student')
                        ->relationship(
                            'user',
                            'name',
                            modifyQueryUsing: fn($query) => $query->whereHas('student')
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $student = \App\Models\Student::where('user_id', $state)->first();
                            if ($student) {
                                $set('department_id', $student->department_id);
                                $cost = \App\Models\Department::find($student->department_id)?->cost;
                                // Set raw numeric amount so the frontend mask formats it correctly
                                $set('amount', $cost ? (int) $cost : null);
                            }
                        }),

                    Forms\Components\Select::make('department_id')
                        ->label('Department')
                        ->relationship('department', 'name')
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} — Semester {$record->semester}")
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('amount')
                        ->label('Amount (IDR)')
                        ->prefix('Rp')
                        ->required()
                        ->extraInputAttributes([
                            'x-mask:dynamic' => '$money($input, ".")',
                            'inputmode'      => 'numeric',
                        ])
                        ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', (string) $state)),

                    Forms\Components\Select::make('payment_method')
                        ->label('Payment Method')
                        ->options([
                            'bank_transfer' => 'Bank Transfer',
                            'e_wallet'      => 'E-Wallet',
                            'manual'        => 'Manual Payment',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('payment_status')
                        ->label('Status')
                        ->options(TransactionStatus::options())
                        ->required()
                        ->native(false),

                    Forms\Components\DateTimePicker::make('paid_at')
                        ->label('Paid At'),

                    Forms\Components\FileUpload::make('proof_of_payment')
                        ->label('Proof of Payment')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                        ->directory('proofs')
                        ->disk('public')
                        ->downloadable()
                        ->openable()
                        ->previewable(true)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    // =========================================
    // TABLE
    // =========================================

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['department', 'user']))
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->description(fn($record) => 'Semester ' . ($record->department?->semester ?? '-')),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount (IDR)')
                    ->money('IDR', locale: 'id')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('success')
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
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('Status')
                    ->options(TransactionStatus::options()),
            ])
            ->actions([

                // ── TEST PAY ──────────────────────────────────────────
                // Muncul hanya di baris Pending
                // Generate snap token → redirect ke TestPay page
                Tables\Actions\Action::make('test_pay')
                    ->label('Test Pay')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(
                        fn(Transaction $record) =>
                        $record->payment_status === TransactionStatus::Pending
                    )
                    ->action(function (Transaction $record) {
                        try {
                            $service   = app(\App\Services\MidtransService::class);
                            $snapToken = $service->createSnapToken($record);

                            return redirect()->route('filament.admin.pages.test-pay', [
                                'token'          => $snapToken,
                                'transaction_id' => $record->id,
                                'code'           => $record->code,
                                'amount'         => (int) $record->amount,
                            ]);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal generate Snap Token')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // ── APPROVE (manual) ──────────────────────────────────
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payment')
                    ->modalDescription('Tandai transaksi ini sebagai lunas secara manual? Aksi ini tidak bisa dibatalkan.')
                    ->visible(
                        fn(Transaction $record) =>
                        $record->payment_status === TransactionStatus::Pending
                    )
                    ->action(function (Transaction $record) {
                        $record->markAsPaid($record->payment_method ?? 'manual');

                        Notification::make()
                            ->title('Payment approved')
                            ->body("Transaksi {$record->code} berhasil diapprove.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    // =========================================
    // QUERY
    // =========================================

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['user', 'department']);
    }

    // =========================================
    // PAGES — semua 3 page harus ada filenya
    // =========================================

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit'   => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    // =========================================
    // NAV BADGE — pending count
    // =========================================

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::pending()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
