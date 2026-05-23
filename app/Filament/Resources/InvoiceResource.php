<?php

namespace App\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Enums\TransactionStatus;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationLabel = 'Invoices';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'number';

    public static function getGloballySearchableAttributes(): array
    {
        return ['number', 'student.user.name'];
    }

    // =========================================
    // FORM
    // =========================================

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Invoice Information')
                ->schema([
                    Forms\Components\TextInput::make('number')
                        ->label('Invoice Number')
                        ->disabled()
                        ->placeholder('Generated automatically')
                        ->columnSpanFull(),

                    // Pilih siswa — tampil nama + NIS
                    Forms\Components\Select::make('student_id')
                        ->label('Student')
                        ->options(
                            Student::with('user')
                                ->get()
                                ->mapWithKeys(fn($s) => [
                                    $s->id => "{$s->user->name} ({$s->nis})",
                                ])
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $student = Student::with('department')->find($state);
                            if ($student && $student->department) {
                                $set('department_id', $student->department_id);
                                $cost = $student->department->cost;
                                // Use raw numeric amount so the Filament money/mask displays formatted value
                                $set('amount', (int) $cost);
                            }
                        }),

                    // Department — auto-filled dari siswa, bisa override
                    Forms\Components\Select::make('department_id')
                        ->label('Department')
                        ->relationship('department', 'name')
                        ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} — Semester {$record->semester}")
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $dept = \App\Models\Department::find($state);
                                if ($dept) {
                                    $set('amount', (int) $dept->cost);
                                }
                            }
                        }),

                    // Nominal — auto dari department, read-only agar konsisten
                    Forms\Components\TextInput::make('amount')
                        ->label('Amount (IDR)')
                        ->prefix('Rp')
                        ->required()
                        ->extraInputAttributes([
                            'x-mask:dynamic' => '$money($input, ".")',
                            'inputmode' => 'numeric',
                        ])
                        ->dehydrateStateUsing(fn($state) => (int) preg_replace('/[^0-9]/', '', (string) $state)),

                    // Due date
                    Forms\Components\DatePicker::make('due_date')
                        ->label('Due Date')
                        ->required()
                        ->minDate(now())
                        ->default(now()->addDays(30)),

                    // Status
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(InvoiceStatus::options())
                        ->default(InvoiceStatus::Unpaid->value)
                        ->required()
                        ->native(false),

                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(2)
                        ->maxLength(500)
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
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['student.user', 'department']))
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Invoice No.')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Medium)
                    ->copyable(),

                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Student')
                    ->description(fn($record) => $record->student?->nis ?? '-')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->description(fn($record) => 'Semester ' . ($record->department?->semester ?? '-'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount (IDR)')
                    ->money('IDR', locale: 'id')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn($record) => $record->isOverdue() ? 'danger' : null),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state->label())
                    ->color(fn($state) => $state->color()),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(InvoiceStatus::options()),

                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Only')
                    ->query(
                        fn(Builder $query) => $query
                            ->where('status', InvoiceStatus::Unpaid->value)
                            ->where('due_date', '<', now())
                    ),

                Tables\Filters\Filter::make('due_this_month')
                    ->label('Due This Month')
                    ->query(fn(Builder $query) => $query->dueThisMonth()),
            ])
            ->actions([
                // Approve = tandai lunas + buat Transaction otomatis
                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Invoice as Paid')
                    ->modalDescription('Tandai invoice ini sebagai lunas? Transaksi akan dibuat otomatis.')
                    ->visible(fn(Invoice $record) => $record->canBePaid())
                    ->action(function (Invoice $record) {
                        $transaction = null;

                        if ($record->transaction_id) {
                            $existingTx = Transaction::find($record->transaction_id);
                            if ($existingTx && !$existingTx->isPaid()) {
                                $existingTx->update([
                                    'payment_method' => 'manual',
                                    'payment_status' => TransactionStatus::Paid,
                                    'paid_at' => now(),
                                ]);
                                $transaction = $existingTx;
                            }
                        }

                        if (!$transaction) {
                            // Buat Transaction otomatis untuk audit trail jika belum ada
                            $transaction = Transaction::create([
                                'user_id' => $record->student->user_id,
                                'department_id' => $record->department_id,
                                'amount' => $record->amount,
                                'payment_method' => 'manual',
                                'payment_status' => TransactionStatus::Paid,
                                'paid_at' => now(),
                            ]);
                        }

                        $record->markAsPaid($transaction);

                        Notification::make()
                            ->title('Invoice marked as paid')
                            ->body("Invoice {$record->number} berhasil ditandai lunas.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->hidden(fn(Invoice $record) => $record->isPaid()),

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
    // QUERY — eager loading
    // =========================================

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with(['student.user', 'department']);
    }

    // =========================================
    // PAGES
    // =========================================

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    // =========================================
    // NAV BADGE — unpaid count
    // =========================================

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::unpaid()->count() + static::getModel()::overdue()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
