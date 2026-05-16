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

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $modelLabel = 'Payment';

    protected static ?string $pluralModelLabel = 'Payments';

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
                            ->placeholder('Generated automatically')
                            ->columnSpan('full'),

                        Forms\Components\Select::make('user_id')
                            ->relationship(
                                'user',
                                'name',
                                // FILTER: Hanya ambil user yang memiliki data di tabel students
                                modifyQueryUsing: fn($query) => $query->whereHas('student')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $student = \App\Models\Student::where('user_id', $state)->first();
                                if ($student) {
                                    // 1. Isi otomatis Department
                                    $set('department_id', $student->department_id);

                                    // 2. Ambil harga dari model Department
                                    $cost = \App\Models\Department::find($student->department_id)?->cost;

                                    // 3. Langsung beri titik (format) sebelum dikirim ke kotak Amount
                                    $formattedCost = $cost ? number_format($cost, 0, ',', '.') : null;
                                    $set('amount', $formattedCost);
                                }
                            }),

                        Forms\Components\Select::make('department_id')
                            ->relationship('department', 'name')
                            // INI AGAR MUNCUL: "Nama Jurusan - Semester X"
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - Semester {$record->semester}")
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount (IDR)')
                            ->prefix('Rp')
                            ->readOnly()
                            ->required()
                            // Bersihkan semua titik/huruf sebelum disimpan ke database (kembali murni jadi angka)
                            ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', (string) $state)),

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
                            ->directory('proofs')
                            ->disk('public')
                            ->downloadable()
                            ->openable()
                            ->previewable(true)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['department', 'user']))
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
                    ->description(fn($record) => "Semester " . $record->department->semester),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount (IDR)')
                    ->money('IDR', locale: 'id') // Otomatis mengubah angka (misal 500000) menjadi format Rp 500.000
                    ->weight(\Filament\Support\Enums\FontWeight::Bold) // Membuat teks menjadi tebal
                    ->color('success') // Warna hijau emerald (di Filament v3 menggunakan 'success')
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
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payment')
                    ->modalDescription('Are you sure you want to approve this payment? This action cannot be undone.')
                    ->visible(fn(Transaction $record) => $record->payment_status === TransactionStatus::Pending)
                    ->action(function (Transaction $record) {
                        $record->update([
                            'payment_status' => TransactionStatus::Paid,
                            'paid_at' => now(),
                        ]);
                    }),
                Tables\Actions\ViewAction::make(),
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
